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
            followersContainer.innerHTML = '<p style="color: var(--text-secondary);">You don\'t have followers yet. Share your profile to get more!</p>';
            return;
        }

        followersContainer.innerHTML = followers.map(follower => `
            <div class="col-12 mb-2">
                <div class="card company-card-clickable">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div style="width:50px;height:50px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">
                                ${follower.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div><strong>${follower.name}</strong></div>
                                <div><small style="color: var(--text-secondary);">${follower.email}</small></div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-danger remove-follower-btn" data-user-id="${follower.id}">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        const removeButtons = followersContainer.querySelectorAll('.remove-follower-btn');
        removeButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const userId = this.dataset.userId;
                await removeCompanyFollower(userId, this);
            });
        });
    } catch (err) {
        console.error('[Followers] Error loading:', err);
        followersContainer.innerHTML = '<p class="text-danger">Error loading followers</p>';
    }
}

async function removeCompanyFollower(userId, buttonElement) {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        alert('Not authenticated');
        return;
    }

    buttonElement.disabled = true;
    const originalText = buttonElement.innerText;
    buttonElement.innerText = 'Removing...';

    try {
        const res = await fetch(`/api/company/followers/${userId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || 'Error removing follower');
        }

        await loadCompanyFollowers();
    } catch (err) {
        console.error('[Followers] Remove error:', err);
        alert(err.message || 'Error removing follower');
        buttonElement.disabled = false;
        buttonElement.innerText = originalText;
    }
}
