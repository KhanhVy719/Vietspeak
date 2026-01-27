const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const puppeteer = require('puppeteer');

const app = express();
const port = 3001;

app.use(cors());
app.use(bodyParser.json());

// Browser instance
let validCookies = null;
let lastLogin = 0;

async function getHistoryWithPuppeteer(username, password, accountNo) {
    const browser = await puppeteer.launch({
        headless: "new", // or false to debug
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();
    
    try {
        // 1. Go to Login Page
        console.log('[Puppeteer] Navigating to MBBank...');
        await page.goto('https://online.mbbank.com.vn/pl/login', { waitUntil: 'networkidle0' });

        // 2. Login
        console.log('[Puppeteer] Entering credentials...');
        await page.type('#user-id', username);
        await page.type('#password', password);
        
        // Click login and wait for navigation
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('#login-btn') // Adjust selector if needed
        ]);
        
        // Check for captcha or errors
        // (If captcha appears, Puppeteer might struggle unless we use a solver, 
        // but often the browser environment itself bypasses the "Crypto Challenge" blocking simple requests).
        
        // 3. Navigate to History (or call API directly using page context)
        console.log('[Puppeteer] Login successful (assumed). Fetching history...');
        
        // We can execute fetch inside the page context to get history
        // This reuses the browser's credentials/cookies/headers
        const history = await page.evaluate(async (acct) => {
             const toDate = new Date();
             const fromDate = new Date();
             fromDate.setDate(toDate.getDate() - 30);
             
             const formatDate = (date) => {
                 return ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth() + 1)).slice(-2) + "/" + date.getFullYear();
             };
             
             const url = 'https://online.mbbank.com.vn/api/retail-transactionms/transactionms/get-account-transaction-history';
             const body = {
                  "accountNo": acct,
                  "fromDate": formatDate(fromDate),
                  "toDate": formatDate(toDate)
             };
             
             const response = await fetch(url, {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'Deviceid': localStorage.getItem('deviceIdCommon') || '',
                     'Refno': username + '-' + Date.now()
                     // Authorization header is usually cookie-based or auto-handled by browser
                 },
                 body: JSON.stringify(body)
             });
             
             return await response.json();
        }, accountNo);
        
        return history;

    } catch (e) {
        console.error('[Puppeteer] Error:', e);
        throw e;
    } finally {
        await browser.close();
    }
}

app.post('/api/history', async (req, res) => {
    try {
        const { username, password, accountNo } = req.body;
        console.log(`[Puppeteer-Server] Request for ${username}`);
        
        // Since Puppeteer is slow, we might want to keep the browser open, but for now open-close is safer
        const data = await getHistoryWithPuppeteer(username, password, accountNo);
        
        if (data && data.transactionHistoryList) {
             return res.json({ success: true, data: { transactionHistoryList: data.transactionHistoryList } });
        } else {
             return res.json({ success: false, message: 'No history found or structure changed', data });
        }

    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});

app.listen(port, () => {
    console.log(`Puppeteer API Server running at http://localhost:${port}`);
});
