const { MB } = require('./dist/index.js');
const moment = require('moment');

const username = process.env.MB_USERNAME || '';
const password = process.env.MB_PASSWORD || '';
const accountNumber = process.env.MB_ACCOUNT || '';
const searchText = process.argv[2] || ''; // Cụm từ cần tìm (ví dụ: LVN3368)

if (!username || !password) {
    console.log(JSON.stringify({ 
        error: 'Missing MB_USERNAME or MB_PASSWORD',
        message: 'Vui lòng cấu hình MB_USERNAME và MB_PASSWORD trong biến môi trường'
    }));
    process.exit(1);
}

if (!accountNumber) {
    console.log(JSON.stringify({ 
        error: 'Missing MB_ACCOUNT',
        message: 'Vui lòng cấu hình MB_ACCOUNT (số tài khoản MB Bank để kiểm tra)'
    }));
    process.exit(1);
}

(async () => {
    try {
        const mb = new MB({
            username,
            password,
            preferredOCRMethod: "default",
            saveWasm: true,
        });

        // Đăng nhập
        await mb.login();
        
        // Lấy giao dịch trong 30 ngày gần nhất (giống Test.ts)
        const toDate = moment().format("DD/MM/YYYY");
        const fromDate = moment().subtract(30, "days").format("DD/MM/YYYY");
        
        const transactions = await mb.getTransactionsHistory({
            accountNumber,
            fromDate,
            toDate
        });

        // Tìm giao dịch có chứa cụm từ trong nội dung chuyển khoản
        let found = null;
        if (searchText && searchText.length > 0) {
            // Tìm giao dịch có chứa cụm từ trong transactionDesc (không phân biệt hoa thường)
            const searchLower = searchText.toLowerCase();
            found = transactions.find(tx => {
                const desc = (tx.transactionDesc || '').toLowerCase();
                return desc.includes(searchLower);
            });
        }

        const result = {
            found: !!found,
            transaction: found || null,
            total_transactions: transactions.length,
            search_text: searchText
        };
        
        // Nếu không có searchText, trả về tất cả giao dịch
        if (!searchText || searchText.length === 0) {
            result.transactions = transactions.slice(0, 20);
        }

        console.log(JSON.stringify(result));
    } catch (e) {
        console.log(JSON.stringify({ 
            error: e.message,
            stack: e.stack 
        }));
        process.exit(1);
    }
})();

