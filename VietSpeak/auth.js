// API Base URL - Laravel backend
const API_URL = (typeof CONFIG !== 'undefined' && CONFIG.API_URL) ? CONFIG.API_URL : '/api';
const LMS_URL = API_URL.replace('/api', '/login');

document.addEventListener('DOMContentLoaded', () => {
    const lmsLink = document.getElementById('lmsLoginLink');
    if (lmsLink) {
        lmsLink.href = LMS_URL;
    }
});

// Handle login form submission
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('errorMessage');
    const loginBtn = document.getElementById('loginBtn');
    
    errorDiv.textContent = '';
    loginBtn.disabled = true;
    loginBtn.textContent = 'Đang đăng nhập...';
    
    try {
        const response = await fetch(`${API_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });
        
        console.log('Response status:', response.status);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success && data.token) {
            // Save token and user info
            localStorage.setItem('vietspeak_token', data.token);
            localStorage.setItem('vietspeak_user', JSON.stringify(data.user));
            
            console.log('Token saved:', data.token);
            console.log('User saved:', data.user);
            
            // Small delay before redirect
            setTimeout(() => {
                window.location.href = 'account.html';
            }, 100);
        } else {
            errorDiv.textContent = data.message || 'Đăng nhập thất bại';
            loginBtn.disabled = false;
            loginBtn.textContent = 'Đăng nhập';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorDiv.textContent = 'Lỗi kết nối đến server. Vui lòng kiểm tra lại.';
        loginBtn.disabled = false;
        loginBtn.textContent = 'Đăng nhập';
    }
    
    
    return false;
}

// Handle registration form submission
async function handleRegister(event) {
    event.preventDefault();
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;
    const errorDiv = document.getElementById('errorMessage');
    const registerBtn = document.getElementById('registerBtn');
    
    // Basic frontend validation
    if (password !== password_confirmation) {
        errorDiv.textContent = 'Mật khẩu xác nhận không khớp';
        return false;
    }
    
    errorDiv.textContent = '';
    registerBtn.disabled = true;
    registerBtn.textContent = 'Đang xử lý...';
    
    try {
        const response = await fetch(`${API_URL}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ 
                name, 
                email, 
                password,
                password_confirmation
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.token) {
            // Save token and user info
            localStorage.setItem('vietspeak_token', data.token);
            localStorage.setItem('vietspeak_user', JSON.stringify(data.user));
            
            // Redirect to account page
            window.location.href = 'account.html';
        } else {
            errorDiv.textContent = data.message || 'Validation Error: ' + JSON.stringify(data.errors);
            registerBtn.disabled = false;
            registerBtn.textContent = 'Đăng Ký Ngay';
        }
    } catch (error) {
        console.error('Register error:', error);
        errorDiv.textContent = 'Lỗi kết nối server';
        registerBtn.disabled = false;
        registerBtn.textContent = 'Đăng Ký Ngay';
    }
    
    return false;
}

// Check if user is logged in
function isLoggedIn() {
    return localStorage.getItem('vietspeak_token') !== null;
}

// Get current user
function getCurrentUser() {
    const userStr = localStorage.getItem('vietspeak_user');
    return userStr ? JSON.parse(userStr) : null;
}

// Logout function
async function logout() {
    const token = localStorage.getItem('vietspeak_token');
    
    if (token) {
        try {
            await fetch(`${API_URL}/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                }
            });
        } catch (error) {
            console.error('Logout error:', error);
        }
    }
    
    // Clear local storage
    localStorage.removeItem('vietspeak_token');
    localStorage.removeItem('vietspeak_user');
    
    // Redirect to login
    window.location.href = 'login.html';
}

// Fetch API with authentication
async function fetchApi(endpoint, options = {}) {
    const token = localStorage.getItem('vietspeak_token');
    
    if (!token) {
        console.log('No token found');
        window.location.href = 'login.html';
        return null;
    }
    
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers
    };
    
    try {
        console.log(`Fetching: ${API_URL}${endpoint}`);
        
        const response = await fetch(`${API_URL}${endpoint}`, {
            ...options,
            headers
        });
        
        console.log(`Response status for ${endpoint}:`, response.status);
        
        if (response.status === 401) {
            // Token expired or invalid - logout
            console.log('Unauthorized, logging out...');
            logout();
            return null;
        }
        
        if (!response.ok) {
            console.error(`API error for ${endpoint}:`, response.status);
            try {
                const errData = await response.json();
                return { success: false, error: errData.message || response.statusText, details: errData };
            } catch (e) {
                return { success: false, error: response.statusText };
            }
        }
        
        const data = await response.json();
        console.log(`Data from ${endpoint}:`, data);
        return data;
        
    } catch (error) {
        console.error(`Fetch error for ${endpoint}:`, error);
        // Don't logout on network errors, just return null
        return { success: false, error: error.message };
    }
}

// Update Navigation Bar based on login state
function updateNavigation() {
    console.log('[updateNavigation] Running...');
    const nav = document.querySelector('nav');
    
    if (!nav) {
        console.log('[updateNavigation] Nav element not found');
        return;
    }

    if (isLoggedIn()) {
        console.log('[updateNavigation] User is logged in');
        const user = getCurrentUser();
        
        if (!user) {
            console.log('[updateNavigation] User data missing from localStorage');
            return;
        }

        // Find the login link by searching for text content "Đăng nhập"
        const links = nav.querySelectorAll('a');
        let loginLink = null;
        
        for (const link of links) {
            const linkText = link.textContent.trim().toLowerCase();
            if (linkText === 'đăng nhập') {
                loginLink = link;
                console.log('[updateNavigation] Found login link:', link.href);
                break;
            }
        }

        if (loginLink && user) {
            console.log('[updateNavigation] Replacing login link with user avatar for:', user.name);
            
            // Create user profile link
            const userLink = document.createElement('a');
            userLink.href = 'account.html';
            userLink.className = 'user-nav-link';
            userLink.style.display = 'flex';
            userLink.style.alignItems = 'center';
            userLink.style.gap = '10px';
            
            // Format Balance
            const balance = user.balance || 0;
            const formattedBalance = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(balance);

            // User Avatar/Name + Balance Badge
            let avatarHtml = '';
            if (user.avatar_url) {
                avatarHtml = `<img src="${user.avatar_url}" alt="${user.name}" class="nav-avatar-img" 
                         style="width: 32px !important; height: 32px !important; max-width: 32px !important; max-height: 32px !important; border-radius: 50%; object-fit: cover; display: inline-block; vertical-align: middle;" />`;
            } else {
                avatarHtml = `<div class="nav-avatar">${user.name.charAt(0).toUpperCase()}</div>`;
            }

            userLink.innerHTML = `
                <div style="background: rgba(39, 174, 96, 0.1); color: #27ae60; padding: 4px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; border: 1px solid rgba(39, 174, 96, 0.2);">
                    ${formattedBalance}
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    ${avatarHtml}
                    <span class="nav-username">${user.name.split(' ')[0]}</span>
                </div>
            `;
            
            // Replace the login link
            nav.replaceChild(userLink, loginLink);
            console.log('[updateNavigation] Successfully replaced login link with user avatar');
        } else {
            console.log('[updateNavigation] Login link not found by text search');
        }
    } else {
        console.log('[updateNavigation] User is NOT logged in');
    }
}

// Run navigation update when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        updateNavigation();
        setupMobileMenu();
    });
} else {
    // DOM is already loaded, run immediately
    updateNavigation();
    setupMobileMenu();
}

function setupMobileMenu() {
    const header = document.querySelector('header');
    if (!header) return;

    // Check if toggle already exists
    if (header.querySelector('.menu-toggle')) return;

    // Create Toggle Button
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'menu-toggle';
    toggleBtn.innerHTML = '☰'; // Hamburger icon
    toggleBtn.setAttribute('aria-label', 'Toggle Menu');
    
    // Add click event
    toggleBtn.addEventListener('click', function() {
        const nav = header.querySelector('nav');
        if (nav) {
            nav.classList.toggle('active');
            toggleBtn.innerHTML = nav.classList.contains('active') ? '✕' : '☰';
        }
    });

    // Insert after Logo (before Nav) implies appending to header since flex order handles visual
    // But DOM order: Logo, Nav. We want Logo, Toggle, Nav (or Logo, Nav, Toggle).
    // Flex row: Logo (flex-grow), Toggle, Nav (break row).
    // So Toggle should be after Logo.
    const logo = header.querySelector('.logo');
    if (logo && logo.nextSibling) {
        header.insertBefore(toggleBtn, logo.nextSibling);
    } else {
        header.appendChild(toggleBtn);
    }
}
