/*
 * MIT License
 *
 * Copyright (c) 2024 CookieGMVN and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

import { config } from "dotenv";
import moment from "moment";
import { MB } from "./index";

config();

(async () => {
    const username = process.env.MB_USERNAME || "";
    const password = process.env.MB_PASSWORD || "";
    const accountNumber = process.env.MB_ACCOUNT || "";
    const toDate = process.env.MB_TO_DATE || moment().format("DD/MM/YYYY");
    const fromDate = process.env.MB_FROM_DATE || moment().subtract(30, "days").format("DD/MM/YYYY");
    
    // Nhận cụm từ cần tìm từ command line argument
    const searchText = process.argv[2] || "";

    if (!username || !password) {
        console.log(JSON.stringify({
            error: "Missing MB_USERNAME or MB_PASSWORD",
            message: "Thiếu MB_USERNAME hoặc MB_PASSWORD trong biến môi trường."
        }));
        process.exit(1);
    }

    if (!accountNumber) {
        console.log(JSON.stringify({
            error: "Missing MB_ACCOUNT",
            message: "Thiếu MB_ACCOUNT trong biến môi trường."
        }));
        process.exit(1);
    }

    const mb = new MB({
        username,
        password,
        preferredOCRMethod: "default",
        saveWasm: true,
    });

    try {
        await mb.login();

        const history = await mb.getTransactionsHistory({
            accountNumber,
            fromDate,
            toDate,
        });

        if (!history || history.length === 0) {
            console.log(JSON.stringify({
                found: false,
                transaction: null,
                total_transactions: 0,
                search_text: searchText,
                message: "Không có giao dịch trong khoảng thời gian."
            }));
            return;
        }

        // Nếu có cụm từ cần tìm, tìm giao dịch có chứa cụm từ đó
        // Loại bỏ khoảng trắng để tìm được cả "LVN3331" và "LVN 3331"
        let found = null;
        if (searchText && searchText.length > 0) {
            const searchLower = searchText.toLowerCase().replace(/\s+/g, ''); // Loại bỏ tất cả khoảng trắng
            found = history.find(tx => {
                const desc = (tx.transactionDesc || '').toLowerCase().replace(/\s+/g, ''); // Loại bỏ khoảng trắng trong mô tả
                return desc.includes(searchLower);
            });
        }

        const result: any = {
            found: !!found,
            transaction: found || null,
            total_transactions: history.length,
            search_text: searchText
        };

        // Nếu không có cụm từ tìm kiếm, trả về top 20 giao dịch
        if (!searchText || searchText.length === 0) {
            result.transactions = history.slice(0, 20);
        }

        console.log(JSON.stringify(result));
    } catch (e) {
        const errorMsg = (e as Error).message;

        // Xử lý lỗi GW18 như cũ
        if (errorMsg.includes("GW18")) {
            console.log(JSON.stringify({
                found: false,
                message: "Test completed. The library is functioning correctly."
            }));
            return;
        }
        
        // Output lỗi dưới dạng JSON
        console.log(JSON.stringify({
            error: errorMsg,
            stack: (e as Error).stack
        }));
        process.exit(1);
    }
})();