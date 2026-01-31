/**
 * user-content.js
 * Funciones para cargar contenido: posts, servicios y reservas
 */

// Cargar posts de empresas seguidas
async function loadFollowedPosts(token) {
    const t = typeof token !== 'undefined' ? token : localStorage.getItem('auth_token');
    const storiesContainer = document.getElementById('storiesContainer');
    const postsContainer = document.getElementById('postsContainer');
    
    if (!t) {
        if (storiesContainer) storiesContainer.innerHTML = '<p class="text-muted">No autenticado</p>';
        if (postsContainer) postsContainer.innerHTML = '<p class="text-muted">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/user/followed-companies-posts', {
            headers: {
                'Authorization': 'Bearer ' + t,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            if (storiesContainer) storiesContainer.innerHTML = '<p class="text-muted">Error al cargar stories</p>';
            if (postsContainer) postsContainer.innerHTML = '<p class="text-muted">Error al cargar posts</p>';
            return;
        }

        const posts = await res.json();
        const stories = posts.filter(p => p.is_story);
        const regular = posts.filter(p => !p.is_story);

        // Render stories (small, horizontal)
        if (storiesContainer) {
            if (stories.length === 0) {
                storiesContainer.innerHTML = '<p class="text-muted">No hay stories</p>';
                window.storiesData = [];
            } else {
                window.storiesData = stories;
                storiesContainer.innerHTML = stories.map((s, i) => `
                    <div class="story-pill">
                        <div class="story-circle" role="button" onclick="openStoriesModal(${i})" title="${s.company_name}">
                            <div class="story-inner">${(s.company_name||'').charAt(0).toUpperCase()}</div>
                        </div>
                        <small class="story-label">${s.company_name}</small>
                    </div>
                `).join('');
            }
        }

        // Render regular posts (grid)
        if (postsContainer) {
            if (regular.length === 0) {
                postsContainer.innerHTML = '<p class="text-muted">No hay posts disponibles</p>';
            } else {
                postsContainer.innerHTML = regular.map(post => `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">${post.company_name}</h5>
                                </div>
                                <p class="card-text flex-grow-1">${post.content}</p>
                                <div class="mt-2">
                                    <small class="text-muted d-block">Publicado: ${new Date(post.created_at).toLocaleDateString()}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

    } catch (e) {
        console.error(e);
        if (storiesContainer) storiesContainer.innerHTML = '<p class="text-danger">Error al cargar stories</p>';
        if (postsContainer) postsContainer.innerHTML = '<p class="text-danger">Error al cargar posts</p>';
    }
}

// Cargar servicios de las empresas que sigues (usado en Inicio)
async function loadFollowedServices(token) {
    const t = typeof token !== 'undefined' ? token : localStorage.getItem('auth_token');
    const servicesContainer = document.getElementById('servicesContainer');
    
    if (servicesContainer) {
        servicesContainer.innerHTML = '<p class="text-muted">Cargando servicios...</p>';
    }
    
    console.debug('[debug] loadFollowedServices token=', !!t);
    
    try {
        const res = await fetch('/api/user/followed-companies-services', {
            headers: t ? {
                'Authorization': 'Bearer ' + t,
                'Accept': 'application/json'
            } : {
                'Accept': 'application/json'
            }
        });
        
        console.debug('[debug] services response', res.status, res.ok);
        
        if (!res.ok) {
            const text = await res.text().catch(() => 'no body');
            console.warn('[debug] services error body:', text);
            if (servicesContainer) {
                servicesContainer.innerHTML = '<p class="text-muted">Error al cargar servicios (ver consola)</p>';
            }
            return;
        }
        
        const services = await res.json();
        console.debug('[debug] services json length=', Array.isArray(services) ? services.length : 'not array', services);
        
        if (!servicesContainer) return;
        
        if (!Array.isArray(services) || services.length === 0) {
            servicesContainer.innerHTML = '<p class="text-muted">No hay servicios de las empresas que sigues. Si crees que esto es un error, comprueba que sigues a empresas o revisa la consola para detalles.</p>';
            return;
        }
        
        servicesContainer.innerHTML = services.map(s => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title mb-0">${s.name}</h5>
                            <small class="text-muted">${s.category ?? ''}</small>
                        </div>
                        <p class="card-text text-truncate">${s.description ?? ''}</p>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">${s.company_name ?? ''}</small>
                            <strong>${s.price ? '$' + parseFloat(s.price).toFixed(2) : ''}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error('[debug] loadFollowedServices exception', err);
        if (servicesContainer) {
            servicesContainer.innerHTML = '<p class="text-danger">Error al cargar servicios (ver consola)</p>';
        }
    }
}

// Cargar reservas del usuario autenticado
async function loadUserBookings(token) {
    const t = typeof token !== 'undefined' ? token : localStorage.getItem('auth_token');
    const reservasContainer = document.getElementById('reservasContainer');
    
    if (!reservasContainer) return;
    
    reservasContainer.innerHTML = '<p class="text-muted">Cargando reservas...</p>';
    
    if (!t) {
        reservasContainer.innerHTML = '<p class="text-muted">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/user/bookings', {
            headers: {
                'Authorization': 'Bearer ' + t,
                'Accept': 'application/json'
            }
        });
        
        console.debug('[debug] bookings response', res.status, res.ok);
        
        if (!res.ok) {
            reservasContainer.innerHTML = '<p class="text-muted">Error al cargar reservas</p>';
            return;
        }
        
        const bookings = await res.json();
        console.debug('[debug] bookings json length=', Array.isArray(bookings) ? bookings.length : 'not array', bookings);
        
        if (!Array.isArray(bookings) || bookings.length === 0) {
            reservasContainer.innerHTML = '<p class="text-muted">No tienes reservas</p>';
            return;
        }

        reservasContainer.innerHTML = bookings.map(b => `
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div><strong>${b.service_name ?? 'Servicio'}</strong> <small class="text-muted">- ${b.company_name ?? ''}</small></div>
                            <div><small class="text-muted">Fecha: ${b.date} &nbsp; Hora: ${b.time}</small></div>
                        </div>
                        <div><span class="badge bg-secondary">${b.status}</span></div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error('[debug] loadUserBookings exception', err);
        reservasContainer.innerHTML = '<p class="text-danger">Error al cargar reservas (ver consola)</p>';
    }
}

// Cargar todos los servicios (usado cuando el usuario pulsa el botón "Servicios")
async function loadServices() {
    const token = localStorage.getItem('auth_token');
    const servicesContainer = document.getElementById('servicesContainer');
    
    if (!servicesContainer) return;
    
    servicesContainer.innerHTML = '<p class="text-muted">Cargando servicios...</p>';
    
    try {
        const res = await fetch('/api/services', {
            headers: token ? {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            } : {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            servicesContainer.innerHTML = '<p class="text-muted">Error al cargar servicios</p>';
            return;
        }
        
        const services = await res.json();
        
        if (!services.length) {
            servicesContainer.innerHTML = '<p class="text-muted">No hay servicios</p>';
            return;
        }
        
        servicesContainer.innerHTML = services.map(s => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title mb-0">${s.name}</h5>
                            <small class="text-muted">${s.category ?? ''}</small>
                        </div>
                        <p class="card-text text-truncate">${s.description ?? ''}</p>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">${s.company_name ?? ''}</small>
                            <strong>${s.price ? '$' + parseFloat(s.price).toFixed(2) : ''}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error(err);
        servicesContainer.innerHTML = '<p class="text-danger">Error al cargar servicios</p>';
    }
}

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');

    // Mostrar la vista de Inicio por defecto al cargar la página
    document.querySelectorAll('main.content section').forEach(s => {
        s.style.display = (s.classList.contains('stories-section') || 
                          s.classList.contains('services-section') ||
                          s.classList.contains('reservas-section')) ? '' : 'none';
    });
    
    const userTitle = document.getElementById('userTitle');
    if (userTitle) userTitle.innerText = 'Página del usuario';

    if (!token) return;

    await loadFollowedPosts(token);
    await loadFollowedServices(token);
    await loadUserBookings(token);
});
