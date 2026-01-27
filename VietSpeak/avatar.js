// ========== AVATAR UPLOAD FUNCTIONALITY ==========

// Update avatar display (both in sidebar and storage)
// Update avatar display (both in sidebar and storage)
function updateAvatarDisplay(avatarUrl) {
    const avatarImg = document.getElementById('avatarImg');
    const deleteBtn = document.getElementById('deleteAvatarBtn'); // May not exist in new UI, check existence
    
    if (avatarImg) {
        if (avatarUrl) {
            avatarImg.src = avatarUrl;
            if (deleteBtn) deleteBtn.style.display = 'block';
        } else {
            // Fallback to UI Avatars with current username if possible, or generic
            const user = getCurrentUser();
            const name = user ? user.name : 'User';
            avatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a3a5f&color=fff`;
            
            if (deleteBtn) deleteBtn.style.display = 'none';
        }
    }
}

// Upload avatar
async function uploadAvatar(file) {
    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const response = await fetch(`${API_URL}/student/avatar/upload`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('vietspeak_token')}`,
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update localStorage
            localStorage.setItem('vietspeak_user', JSON.stringify(data.user));
            
            // Update UI
            updateAvatarDisplay(data.avatar_url);
            
            // Refresh navigation
            if (typeof updateNavigation === 'function') {
                updateNavigation();
            }
            
            alert('✅ Upload ảnh thành công!');
        } else {
            alert('❌ ' + (data.message || 'Upload thất bại'));
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('❌ Lỗi upload ảnh. Vui lòng thử lại.');
    }
}

// Delete avatar
async function deleteAvatar() {
    if (!confirm('Bạn có chắc muốn xóa ảnh đại diện?')) return;

    try {
        const response = await fetch(`${API_URL}/student/avatar/delete`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('vietspeak_token')}`,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update localStorage
            localStorage.setItem('vietspeak_user', JSON.stringify(data.user));
            
            // Update UI
            updateAvatarDisplay(null);
            
            // Refresh navigation
            if (typeof updateNavigation === 'function') {
                updateNavigation();
            }
            
            alert('✅ Đã xóa ảnh đại diện');
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('❌ Lỗi xóa ảnh');
    }
}

// Event listeners
document.getElementById('avatarInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('❌ File quá lớn! Tối đa 10MB');
            this.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('❌ Chỉ chấp nhận file ảnh!');
            this.value = '';
            return;
        }
        
        uploadAvatar(file);
        this.value = ''; // Reset input
    }
});

document.getElementById('deleteAvatarBtn')?.addEventListener('click', deleteAvatar);

// Update avatar on page load
window.addEventListener('load', function() {
    const user = getCurrentUser();
    if (user && user.avatar_url) {
        updateAvatarDisplay(user.avatar_url);
    }
});
