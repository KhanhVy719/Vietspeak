// Global assignments data
let allAssignments = [];
let currentFilter = 'pending'; // 'pending' or 'submitted'

document.addEventListener('DOMContentLoaded', async function() {
    // Login check handled by account.js/auth.js
    // if (!isLoggedIn()) { window.location.href = 'login.html'; return; }

    // Init data
    // await loadAssignments(); // Can be called by switchView or keep here
});

async function loadAssignments() {
    const listContainer = document.getElementById('full-assignments-list');
    
    if (!listContainer) return; // Prevent error on other pages
    
    try {
        const data = await fetchApi('/student/assignments');
        
        if (data && data.success) {
            allAssignments = data.data;
            updateCounts();
            renderAssignments();
        } else {
            const errMsg = data && data.error ? data.error : 'Không thể tải dữ liệu';
            listContainer.innerHTML = `<div class="empty-state" style="color: #c0392b">⚠️ ${errMsg}</div>`;
        }
    } catch (error) {
        console.error('Error loading assignments:', error);
        listContainer.innerHTML = `<div class="empty-state" style="color: #c0392b">❌ Lỗi kết nối server: ${error.message}</div>`;
    }
}

function updateCounts() {
    const pendingCount = allAssignments.filter(a => a.status === 'pending' || a.status === 'overdue').length;
    const badge = document.getElementById('count-pending');
    if (badge) {
        badge.textContent = pendingCount > 0 ? `(${pendingCount})` : '';
        badge.style.color = pendingCount > 0 ? '#c0392b' : '#666';
    }
}

function filterAssignments(status) {
    currentFilter = status;
    
    // Update tabs UI
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    
    if (status === 'pending') {
        document.getElementById('tab-pending').classList.add('active');
    } else {
        document.getElementById('tab-submitted').classList.add('active');
    }

    renderAssignments();
}

function renderAssignments() {
    const listContainer = document.getElementById('full-assignments-list');
    if (!listContainer) return;
    
    listContainer.innerHTML = '';

    let filtered = [];
    if (currentFilter === 'pending') {
        filtered = allAssignments.filter(a => a.status === 'pending' || a.status === 'overdue');
    } else {
        filtered = allAssignments.filter(a => a.status === 'submitted' || a.status === 'graded');
    }

    if (filtered.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <i>${currentFilter === 'pending' ? '🎉' : '📭'}</i>
                <p>${currentFilter === 'pending' ? 'Bạn đã hoàn thành hết bài tập!' : 'Chưa có lịch sử nộp bài'}</p>
            </div>
        `;
        return;
    }

    // Sort: Overdue first, then by date
    filtered.sort((a, b) => new Date(a.due_date) - new Date(b.due_date));

    filtered.forEach(assign => {
        const card = document.createElement('div');
        card.className = 'assignment-card';
        
        // Determine status style
        let statusClass = 'status-pending';
        let statusLabel = 'Cần làm';
        let actionBtn = '';

        if (assign.status === 'overdue') {
            statusClass = 'status-overdue';
            statusLabel = 'Quá hạn';
            actionBtn = `<button class="btn-submit" onclick="openModal(${assign.id}, '${assign.title}')">📤 Nộp bù</button>`;
        } else if (assign.status === 'submitted') {
            statusClass = 'status-submitted';
            statusLabel = 'Đã nộp';
            actionBtn = `<span style="color: var(--primary); font-weight: 500;">Đang chờ chấm...</span>`;
        } else if (assign.status === 'graded') {
            statusClass = 'status-graded';
            statusLabel = 'Đã chấm';
            actionBtn = `
                <div class="grade-box">
                    <div class="grade-number">${assign.grade.score}/10</div>
                </div>
            `;
        } else {
            // Pending
            actionBtn = `<button class="btn-submit" onclick="openModal(${assign.id}, '${assign.title}')">📤 Nộp bài</button>`;
        }

        card.innerHTML = `
            <div class="card-content">
                <h3>${assign.title}</h3>
                <div class="card-meta">
                    <span>📘 ${assign.classroom ? assign.classroom.name : 'Lớp học'}</span> &bull; 
                    <span>📅 Hạn nộp: ${formatDate(assign.due_date)}</span>
                </div>
                <div class="status-badge ${statusClass}">
                    ${statusLabel}
                </div>
                ${assign.status === 'graded' && assign.grade.comment ? 
                    `<p style="margin-top: 10px; font-size: 0.9rem; color: #555; background: #f9f9f9; padding: 8px; border-radius: 6px;">
                        💬 <strong>Nhận xét:</strong> ${assign.grade.comment}
                    </p>` : ''}
            </div>
            <div class="card-actions">
                ${actionBtn}
            </div>
        `;
        
        listContainer.appendChild(card);
    });
}

// --- MODAL & SUBMISSION LOGIC ---

function openModal(id, title) {
    document.getElementById('assignmentId').value = id;
    document.getElementById('modalTaskTitle').textContent = `Bài tập: ${title}`;
    document.getElementById('submitModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('submitModal').style.display = 'none';
    document.getElementById('submissionForm').reset();
    document.getElementById('fileNameDisplay').textContent = '📁 Nhấn để chọn file (PDF, PPTX, MP4...)';
}

// 2GB Limit (matches backend validation)
const MAX_UPLOAD_SIZE = 2 * 1024 * 1024 * 1024; 

function updateFileName(input) {
    const errorDiv = document.getElementById('submissionError');
    errorDiv.style.display = 'none'; // Hide previous errors

    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        
        // Show size
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        
        if (file.size > MAX_UPLOAD_SIZE) {
            errorDiv.innerHTML = `⚠️ File <b>${file.name}</b> quá lớn (${sizeMB} MB).<br>Máy chủ hiện tại chỉ cho phép tối đa 2GB.<br>Vui lòng chọn file nhỏ hơn hoặc nén lại.`;
            errorDiv.style.display = 'block';
            input.value = ''; // Clear input
            document.getElementById('fileNameDisplay').textContent = '📁 Nhấn để chọn file (PDF, PPTX, MP4...)';
            return;
        }

        document.getElementById('fileNameDisplay').textContent = `📄 ${file.name} (${sizeMB} MB)`;
    }
}

async function handleSubmission(event) {
    event.preventDefault();
    
    const errorDiv = document.getElementById('submissionError');
    errorDiv.style.display = 'none';

    const submitBtn = event.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Đang gửi...';

    const formData = new FormData(event.target);

    // API Call expects multipart/form-data
    try {
        const token = localStorage.getItem('vietspeak_token');
        const assignmentId = formData.get('assignment_id');
        const response = await fetch(`${API_URL}/student/assignments/${assignmentId}/submit`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // Note: Content-Type is set automatically by browser with FormData
            },
            body: formData
        });

        const result = await response.json();

        if (result.success || response.ok) { // Check both for robustness
            alert('✅ Nộp bài thành công!');
            closeModal();
            loadAssignments(); // Refresh list
        } else {
            const errorDiv = document.getElementById('submissionError');
            errorDiv.innerHTML = '❌ ' + (result.message || 'Lỗi khi nộp bài. Có thể file không hợp lệ.');
            errorDiv.style.display = 'block';
        }

    } catch (error) {
        console.error('Submission error:', error);
        const errorDiv = document.getElementById('submissionError');
        errorDiv.innerHTML = `❌ Lỗi kết nối đến máy chủ: ${error.message}`;
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = '🚀 Gửi Bài Ngay';
    }
}

// Helper: Format date DD/MM/YYYY
function formatDate(dateString) {
    if (!dateString) return 'Không thời hạn';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('submitModal');
    if (event.target == modal) {
        closeModal();
    }
}
