const express = require('express');
const bodyParser = require('body-parser');
const { MB } = require('./dist/index.js');
const cors = require('cors');

const app = express();
const port = 3001;

app.use(cors());
app.use(bodyParser.json());

// Main check endpoint
app.post('/api/history', async (req, res) => {
    try {
        const { username, password, accountNo, deviceId } = req.body;

        if (!username || !password || !accountNo) {
            return res.status(400).json({ success: false, message: 'Missing credentials' });
        }

        console.log(`[MBBank-Local] Attempting login for user: ${username}`);
        
        // Initialize MBBank using the local configuration style
        const mb = new MB({
            username,
            password,
            deviceId: deviceId || undefined,
            preferredOCRMethod: "default", // Use the local assets (wasm/onnx)
            saveWasm: true
        });
        
        await mb.login();

        // Calculate dates
        const toDate = new Date();
        const fromDate = new Date();
        fromDate.setDate(toDate.getDate() - 30); // Last 30 days
        
        // Format DD/MM/YYYY
        const formatDate = (date) => {
             return ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth() + 1)).slice(-2) + "/" + date.getFullYear();
        }
        
        const fFrom = formatDate(fromDate);
        const fTo = formatDate(toDate);

        console.log(`[MBBank-Local] Fetching history from ${fFrom} to ${fTo}`);

        // Get history
        const history = await mb.getTransactionsHistory({
            accountNumber: accountNo,
            fromDate: fFrom,
            toDate: fTo
        });
        
        console.log(`[MBBank-Local] Success. Found ${Array.isArray(history) ? history.length : 0} transactions.`);
        
        return res.json({
            success: true,
            data: { transactionHistoryList: history || [] }
        });

    } catch (error) {
        console.error('[MBBank-Local] Error:', error.message);
        console.error(error);
        
        return res.json({
            success: false, // Don't crash the checking UI
            message: error.message,
            details: error.toString()
        });
    }
});

app.listen(port, () => {
    console.log(`MBBank Local API running at http://localhost:${port}`);
});
