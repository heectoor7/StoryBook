/**
 * company-followers.js
 * Funciones para gestión de seguidores
 */

// Cargar seguidores de la empresa
async function loadCompanyFollowers() {
    const token = localStorage.getItem('auth_token');
    const followersContainer = document.getElementById('companyFollowersContainer');
    
    if (!followersContainer) {
        console.error('[Followers] Container not found');
        return;
    }
    
    followersContainer.innerHTML = '<p style="color: var(--text-secondary);">Loading followers...</p>';
    
    if (!token) {
        followersContainer.innerHTML = '<p style="color: var(--text-secondary);">Not authenticated</p>';
        return;
    }

    try {
        console.log('[Followers] Loading followers...');
        const res = await fetch('/api/company/followers', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        console.log('[Followers] Response status:', res.status);

        if (!res.ok) {
            const errorData = await res.text();
            console.error('[Followers] Error response:', errorData);
            throw new Error('Error loading followers: ' + res.status);
        }

        const followers = await res.json();
        console.log('[Followers] Data received:', followers);
        
        if (!Array.isArray(followers) || followers.length === 0) {
            followersContainer.innerHTML = `
                <div class="alert alert-info">
                    <p class="mb-0">You don't have followers yet. Share your profile to get more!</p>
                </div>
            `;
            return;
        }

        followersContainer.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Total followers: <strong>${followers.length}</strong></h6>
                    <div class="list-group">
                        ${followers.map(follower => `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                                            ${follower.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">${follower.name}</h6>
                                            <small style="color: var(--text-secondary);">${follower.email}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">Follower</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error('[Followers] Error loading:', err);
        followersContainer.innerHTML = '<p class="text-danger">Error loading followers</p>';
    }
}
