// team.js - Load team members dynamically
const API_BASE_URL = 'https://adminvietspeakai.tranhungdaocfs.site';

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`${API_BASE_URL}/api/public/team`);
        const data = await response.json();
        
        if (data.success && data.data) {
            renderTeamMembers(data.data);
        } else {
            showTeamError();
        }
    } catch (error) {
        console.error('Error loading team members:', error);
        showTeamError();
    }
});

function renderTeamMembers(members) {
    const container = document.getElementById('team-container');
    if (!container) return;
    
    container.innerHTML = members.map(member => {
        // Use uploaded avatar if available, otherwise use initials badge
        const avatarHTML = member.avatar_url 
            ? `<img src="${member.avatar_url}" alt="${member.name}" class="profile-img">`
            : `<img src="https://ui-avatars.com/api/?name=${encodeURIComponent(member.initials)}&background=${member.avatar_color.substring(1)}&color=fff&size=256" alt="${member.name}" class="profile-img">`;
        
        return `
            <div class="profile-card">
                <div class="profile-img-wrap">
                    ${avatarHTML}
                </div>
                <div class="profile-info">
                    <h3>${member.name}</h3>
                    <span class="role">${member.title}</span>
                    <p>${member.description}</p>
                </div>
            </div>
        `;
    }).join('');
}

function showTeamError() {
    const container = document.getElementById('team-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="text-center" style="grid-column: 1 / -1; padding: 40px;">
            <p style="color: #999;">Không thể tải thông tin đội ngũ. Vui lòng thử lại sau.</p>
        </div>
    `;
}
