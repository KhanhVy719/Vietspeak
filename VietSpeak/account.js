// Check authentication on page load
document.addEventListener('DOMContentLoaded', async function() {
    logger.log('Account page loaded');
    logger.log('Token:', localStorage.getItem('vietspeak_token'));
    
    if (!isLoggedIn()) {
        logger.log('Not logged in, redirecting to login...');
        window.location.href = '/login';
        return;
    }

    logger.log('User is logged in, loading data...');
    
    // Load data concurrently to improve speed
    await Promise.allSettled([
        loadProfile(),
        loadProgress(),
        loadClasses(),
        loadCourses(),
        loadAssignments(),
        loadGrades()
    ]);
});

// Load progress statistics
async function loadProgress() {
    const data = await fetchApi('/student/progress');
    
    if (data && data.success && document.getElementById('totalClasses')) {
        const progress = data.data;
        
        // Update stats in sidebar
        // Update stats
        const totalClassesEl = document.getElementById('totalClasses');
        if (totalClassesEl) totalClassesEl.textContent = progress.total_classes || 0;

        const averageGradeEl = document.getElementById('averageGrade');
        if (averageGradeEl) {
            const avgScore = progress.average_score || 0;
            averageGradeEl.textContent = avgScore > 0 ? `${avgScore}/10` : '--';
        }

        const submittedStatsEl = document.getElementById('submittedStats');
        if (submittedStatsEl) {
            const completed = progress.completed_assignments || 0;
            // Note: API returns total_assignments as total pending + completed? Or total in DB?
            // Assuming total_assignments is total count.
            const total = progress.total_assignments || 0; 
            submittedStatsEl.textContent = `${completed}/${total}`;
        }
        
    } else {
        console.error('Failed to load progress:', data);
        if (document.getElementById('submittedStats')) document.getElementById('submittedStats').textContent = 'Error';
    }
}

async function loadProfile() {
    const data = await fetchApi('/student/profile');
    
    if (data && data.success) {
        const user = data.data;
        
        // Update profile info
        document.getElementById('userName').textContent = user.name;
        document.getElementById('userEmail').textContent = user.email;

        // Sync with Local Storage for Header
        localStorage.setItem('vietspeak_user', JSON.stringify(user));
        if (typeof updateNavigation === 'function') {
            updateNavigation();
        }
        
        // Update balance
        if (document.getElementById('userBalance')) {
            const formatter = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            });
            document.getElementById('userBalance').textContent = formatter.format(user.balance || 0);
        }

        // Update AI Credits
        if (document.getElementById('userCredits')) {
            document.getElementById('userCredits').textContent = user.ai_credits || 0;
        }

        // Update notification
        if (document.getElementById('welcomeMessage')) {
            document.getElementById('welcomeMessage').textContent = `Chào mừng trở lại, ${user.name.split(' ')[0]}!`;
        }
        
        // Update avatar
        const avatarImg = document.getElementById('avatarImg');
        if (avatarImg) {
            if (user.avatar_url) {
                avatarImg.src = user.avatar_url;
            } else {
                // Fallback to UI Avatars with user's name
                avatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=1a3a5f&color=fff`;
            }
        }
    } else {
        console.error('Failed to load profile:', data);
    }
}

async function loadClasses() {
    const data = await fetchApi('/student/classes');
    const classesDiv = document.getElementById('classesList');
    
    if (data && data.success) {
        const classes = data.data;
        
        // Don't update stats here - let loadProgress handle it
        
        if (classes.length === 0) {
            classesDiv.innerHTML = '<div class="empty-state">Bạn chưa được thêm vào lớp nào</div>';
        } else {
            classesDiv.innerHTML = classes.map(cls => `
                <div class="class-item">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h4>📘 ${cls.name}</h4>
                            <p style="margin-top: 5px;">${cls.description || 'Không có mô tả'}</p>
                        </div>
                    </div>
                    
                    ${cls.teachers && cls.teachers.length > 0 ? 
                        `<p style="margin-top: 8px; color: var(--accent); font-weight: 600; font-size: 0.9rem;">
                            👨‍🏫 Giáo viên: ${cls.teachers.join(', ')}
                        </p>` 
                        : ''}
                    
                    ${cls.my_group ? `
                        <style>
                            .member-item { position: relative; cursor: pointer; transition: all 0.2s; }
                            .member-item:hover { background: #dce4e6 !important; }
                            .member-tooltip {
                                visibility: hidden;
                                opacity: 0;
                                position: absolute;
                                bottom: 125%;
                                left: 50%;
                                transform: translateX(-50%);
                                background: rgba(44, 62, 80, 0.95);
                                color: white;
                                padding: 8px 12px;
                                border-radius: 6px;
                                font-size: 0.8rem;
                                white-space: nowrap;
                                z-index: 100;
                                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                                transition: opacity 0.2s;
                                pointer-events: none;
                                text-align: left;
                                min-width: 120px;
                            }
                            .member-tooltip::after {
                                content: "";
                                position: absolute;
                                top: 100%;
                                left: 50%;
                                margin-left: -5px;
                                border-width: 5px;
                                border-style: solid;
                                border-color: rgba(44, 62, 80, 0.95) transparent transparent transparent;
                            }
                            .member-item:hover .member-tooltip {
                                visibility: visible;
                                opacity: 1;
                            }
                        </style>
                        <div style="margin-top: 15px; padding: 12px; background: #fdfefe; border: 1px solid #e1e8ed; border-radius: 8px;">
                            <div style="font-weight: 600; color: #2c3e50; margin-bottom: 8px; display: flex; align-items: center;">
                                <span style="background: #3498db; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; margin-right: 8px;">
                                    ${cls.my_group.name}
                                </span>
                                <span style="font-size: 0.85rem; color: #7f8c8d;">Thành viên nhóm:</span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                ${cls.my_group.members.map(m => `
                                    <div class="member-item" style="display: flex; align-items: center; background: #ecf0f1; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; color: #34495e;">
                                        <span style="width: 20px; height: 20px; background: #bdc3c7; border-radius: 50%; display: inline-block; text-align: center; line-height: 20px; margin-right: 6px; font-size: 0.7rem; color: white; font-weight: bold;">
                                            ${m.name.charAt(0).toUpperCase()}
                                        </span>
                                        ${m.name}
                                        
                                        <!-- Tooltip Stats -->
                                        <div class="member-tooltip">
                                            <div style="border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 4px; margin-bottom: 4px; font-weight: bold; color: #f1c40f;">${m.name}</div>
                                            <div>⭐ Điểm TB: <b>${m.stats ? m.stats.avg_score : '-'}</b></div>
                                            <div>📝 Đã nộp: <b>${m.stats ? m.stats.completed_assignments : 0}</b> bài</div>
                                            <div>📚 Khóa học: <b>${m.stats ? m.stats.courses_count : 0}</b></div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : '<div style="margin-top: 10px; font-style: italic; color: #bdc3c7; font-size: 0.85rem;">(Bạn chưa được phân vào tổ nào)</div>'}
                </div>
            `).join('');
        }
    } else {
        console.error('Failed to load classes:', data);
        classesDiv.innerHTML = '<div class="empty-state">Không thể tải danh sách lớp học</div>';
    }
}

async function loadCourses() {
    const data = await fetchApi('/student/courses');
    const coursesDiv = document.getElementById('coursesList');
    
    if (data && data.success) {
        const courses = data.data;
        
        if (courses.length > 0) {
            coursesDiv.innerHTML = courses.map(course => `
                <div class="class-item" style="border-left-color: var(--accent);">
                    <h4>📚 ${course.name}</h4>
                    <p style="margin-top: 5px; color: #666;">${course.description || 'Không có mô tả'}</p>
                    <div style="margin-top: 10px; display: flex; gap: 15px; flex-wrap: wrap; font-size: 0.9rem;">
                        ${course.instructor ? `<span>👨‍🏫 ${course.instructor}</span>` : ''}
                        ${course.duration ? `<span>⏱️ ${course.duration}</span>` : ''}
                        <span>📊 Tiến độ: ${course.progress}%</span>
                        <span style="color: #999;">📅 Ghi danh: ${course.enrolled_at}</span>
                    </div>
                </div>
            `).join('');
        } else {
            coursesDiv.innerHTML = '<div class="empty-state">Bạn chưa đăng ký khóa học nào</div>';
        }
    } else {
        console.error('Failed to load courses:', data);
        if (coursesDiv) coursesDiv.innerHTML = '<div class="empty-state">Không thể tải danh sách khóa học</div>';
    }
}

async function loadAssignments() {
    const data = await fetchApi('/student/assignments');
    // const assignmentsDiv = document.getElementById('assignmentsList'); // Removed from UI
    // const submittedDiv = document.getElementById('submittedAssignmentsList'); // Removed from UI
    
    if (data && data.success) {
        /*
        const assignments = data.data;

        // Filter assignments
        // Pending logic skipped as section is removed
        // const pendingAssignments = assignments.filter(a => a.status === 'pending' || a.status === 'overdue');
        
        // Submitted = submitted or graded
        const submittedAssignments = assignments.filter(a => a.status === 'submitted' || a.status === 'graded');

        // Render Pending (Removed)
        
        // Render Submitted (Replaces Courses section) -- REMOVED AS REQUESTED
        if (submittedDiv) {
            if (submittedAssignments.length === 0) {
                submittedDiv.innerHTML = '<div class="empty-state">Chưa có bài tập nào đã hoàn thành</div>';
            } else {
                submittedDiv.innerHTML = submittedAssignments.map(renderAssignmentItem).join('');
            }
        }
        */
        
    } else {
        console.error('Failed to load assignments:', data);
        // if (assignmentsDiv) assignmentsDiv.innerHTML = '<div class="empty-state">Không thể tải danh sách bài tập</div>';
        // if (submittedDiv) submittedDiv.innerHTML = '<div class="empty-state">Không thể tải lịch sử nộp bài</div>';
    }
}

function renderAssignmentItem(assign) {
    let statusIcon = '';
    let statusText = '';
    let statusColor = '';
    
    switch(assign.status) {
        case 'graded':
            statusIcon = '✅';
            statusText = `Đã chấm: ${assign.grade.score}/10`;
            statusColor = '#27ae60';
            break;
        case 'submitted':
            statusIcon = '📝';
            statusText = `Đã nộp (${assign.submitted_at})`;
            statusColor = '#f39c12';
            break;
        case 'overdue':
            statusIcon = '⚠️';
            statusText = 'Quá hạn';
            statusColor = '#c0392b';
            break;
        default: // pending
            statusIcon = '❌';
            statusText = 'Chưa nộp';
            statusColor = '#e74c3c';
    }
    
    return `
        <div class="assignment-item" style="border-left-color: ${statusColor}">
            <h4>${assign.title}</h4>
            <p style="margin-top: 5px; color: #666;">
                📚 ${assign.classroom.name} • 📅 Hạn nộp: ${assign.due_date}
            </p>
            <p style="margin-top: 10px; color: ${statusColor}; font-weight: 600; font-size: 0.95rem;">
                ${statusIcon} ${statusText}
            </p>
            ${assign.status === 'graded' && assign.grade && assign.grade.comment ? 
                `<p style="margin-top: 8px; padding: 10px; background: #f8f9fa; border-radius: 8px; font-size: 0.85rem; color: #555;">
                    💬 <strong>Nhận xét:</strong> ${assign.grade.comment}
                </p>` 
                : ''}
        </div>
    `;
}

async function loadGrades() {
    const data = await fetchApi('/student/grades');
    
    if (data && data.success) {
        const grades = data.data;
        
        // Don't update stats here - loadProgress() handles all stats
        /*
        if (grades.length > 0) {
            // Calculate average score
            const avgScore = (grades.reduce((sum, g) => sum + g.score, 0) / grades.length).toFixed(1);
            document.getElementById('averageGrade').textContent = `${avgScore}/10`;
        } else {
            document.getElementById('averageGrade').textContent = '-';
        }
        */
    } else {
        console.error('Failed to load grades:', data);
    }
}

// Load Assignments and update notification badge
async function loadAssignments() {
    try {
        const data = await fetchApi('/student/assignments');
        
        if (data && data.success && data.data) {
            const assignments = data.data;
            
            // Count pending assignments (status === 'pending')
            const pendingCount = assignments.filter(a => a.status === 'pending').length;
            
            // Show/hide notification badge
            const notification = document.getElementById('pending-notification');
            if (notification) {
                notification.style.display = pendingCount > 0 ? 'inline-block' : 'none';
            }
            
            logger.log(`Loaded ${assignments.length} assignments, ${pendingCount} pending`);
        } else {
            console.error('Failed to load assignments:', data);
        }
    } catch (error) {
        console.error('Error loading assignments:', error);
    }
}

