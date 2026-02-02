/**
 * company-followers.js
 * Funciones para gestión de seguidores
 */

// Cargar seguidores de la empresa
async function loadCompanyFollowers() {
    const token = localStorage.getItem('auth_token');
    const followersContainer = document.getElementById('companyFollowersContainer');
    
    if (!followersContainer) {
        console.error('[Followers] Container no encontrado');
        return;
    }
    
    followersContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando seguidores...</p>';
    
    if (!token) {
        followersContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        return;
    }

    try {
        console.log('[Followers] Cargando seguidores...');
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
            throw new Error('Error al cargar seguidores: ' + res.status);
        }

        const followers = await res.json();
        console.log('[Followers] Datos recibidos:', followers);
        
        if (!Array.isArray(followers) || followers.length === 0) {
            followersContainer.innerHTML = `
                <div class="alert alert-info">
                    <p class="mb-0">Aún no tienes seguidores. ¡Comparte tu perfil para conseguir más!</p>
                </div>
            `;
            return;
        }

        followersContainer.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Total de seguidores: <strong>${followers.length}</strong></h6>
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
                                    <span class="badge bg-success">Seguidor</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error('[Followers] Error al cargar:', err);
        followersContainer.innerHTML = '<p class="text-danger">Error al cargar seguidores</p>';
    }
}
