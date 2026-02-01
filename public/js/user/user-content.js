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
        if (storiesContainer) storiesContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        if (postsContainer) postsContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
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
            if (storiesContainer) storiesContainer.innerHTML = '<p style="color: var(--text-secondary);">Error al cargar stories</p>';
            if (postsContainer) postsContainer.innerHTML = '<p style="color: var(--text-secondary);">Error al cargar posts</p>';
            return;
        }

        const posts = await res.json();
        const stories = posts.filter(p => p.is_story);
        const regular = posts.filter(p => !p.is_story);

        // Render stories (small, horizontal)
        if (storiesContainer) {
            if (stories.length === 0) {
                storiesContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay stories</p>';
                window.storiesData = [];
            } else {
                window.storiesData = stories;
                storiesContainer.innerHTML = stories.map((s, i) => {
                    const logoUrl = s.company_logo;
                    const initial = (s.company_name||'').charAt(0).toUpperCase();
                    
                    return `
                    <div class="story-pill">
                        <div class="story-circle" role="button" onclick="openStoriesModal(${i})" title="${s.company_name}">
                            ${logoUrl ? 
                                `<img src="${logoUrl}" alt="${s.company_name}" class="story-inner" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">` : 
                                `<div class="story-inner">${initial}</div>`
                            }
                        </div>
                        <small class="story-label">${s.company_name}</small>
                    </div>
                `}).join('');
                
                // Inicializar flechas de navegación del carrusel
                initStoriesCarouselNavigation();
            }
        }

        // Render regular posts (carousel by company)
        if (postsContainer) {
            if (regular.length === 0) {
                postsContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay posts disponibles</p>';
            } else {
                // Agrupar posts por empresa
                const postsByCompany = {};
                regular.forEach(post => {
                    const companyId = post.company_id;
                    if (!postsByCompany[companyId]) {
                        postsByCompany[companyId] = {
                            company_name: post.company_name,
                            company_logo: post.company_logo,
                            posts: []
                        };
                    }
                    postsByCompany[companyId].posts.push(post);
                });

                // Generar HTML por empresa
                let html = '';
                Object.keys(postsByCompany).forEach(companyId => {
                    const companyData = postsByCompany[companyId];
                    const carouselId = `carousel-company-${companyId}`;
                    
                    html += `
                        <div class="col-12 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                ${companyData.company_logo ? 
                                    `<img src="${companyData.company_logo}" alt="${companyData.company_name}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:12px;">` : 
                                    `<div style="width:40px;height:40px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;margin-right:12px;color:#fff;"><strong>${companyData.company_name.charAt(0)}</strong></div>`
                                }
                                <h5 class="mb-0">${companyData.company_name}</h5>
                            </div>
                            
                            <div id="${carouselId}" class="carousel slide" data-bs-ride="false">
                                <div class="carousel-inner">
                    `;
                    
                    companyData.posts.forEach((post, index) => {
                        html += `
                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <div class="card post-card" style="cursor:pointer;" onclick="openPostModal(${JSON.stringify(post).replace(/"/g, '&quot;')})">
                                    ${post.image ? `<img src="${post.image}" class="card-img-top" alt="${companyData.company_name}" style="max-height:300px;object-fit:cover;">` : ''}
                                    <div class="card-body">
                                        <p class="card-text">${post.content}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small style="color: var(--text-secondary);">Publicado: ${new Date(post.created_at).toLocaleDateString()}</small>
                                            <small style="color: var(--text-secondary);">${post.comments.length} comentarios</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `
                                </div>
                                ${companyData.posts.length > 1 ? `
                                    <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                
                postsContainer.innerHTML = html;
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
        servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando servicios...</p>';
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
                servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Error al cargar servicios (ver consola)</p>';
            }
            return;
        }
        
        const services = await res.json();
        console.debug('[debug] services json length=', Array.isArray(services) ? services.length : 'not array', services);
        
        if (!servicesContainer) return;
        
        if (!Array.isArray(services) || services.length === 0) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay servicios de las empresas que sigues. Si crees que esto es un error, comprueba que sigues a empresas o revisa la consola para detalles.</p>';
            return;
        }
        
        servicesContainer.innerHTML = services.map(s => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 service-card" style="cursor:pointer;" onclick="openServiceModal(${JSON.stringify(s).replace(/"/g, '&quot;')})">
                    ${s.image ? `<img src="${s.image}" class="card-img-top" alt="${s.name}" style="max-height:180px;object-fit:cover;">` : ''}
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title mb-0">${s.name}</h5>
                            <small style="color: var(--text-secondary);">${s.category ?? ''}</small>
                        </div>
                        <p class="card-text text-truncate">${s.description ?? ''}</p>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small style="color: var(--text-secondary);">${s.company_name ?? ''}</small>
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
    
    reservasContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando reservas...</p>';
    
    if (!t) {
        reservasContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
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
            reservasContainer.innerHTML = '<p style="color: var(--text-secondary);">Error al cargar reservas</p>';
            return;
        }
        
        const bookings = await res.json();
        console.debug('[debug] bookings json length=', Array.isArray(bookings) ? bookings.length : 'not array', bookings);
        
        if (!Array.isArray(bookings) || bookings.length === 0) {
            reservasContainer.innerHTML = '<p style="color: var(--text-secondary);">No tienes reservas</p>';
            return;
        }

        reservasContainer.innerHTML = bookings.map(b => `
            <div class="col-12 mb-2">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div><strong>${b.service_name ?? 'Servicio'}</strong> <small style="color: var(--text-secondary);">- ${b.company_name ?? ''}</small></div>
                    <div><small style="color: var(--text-secondary);">Date: ${b.date} &nbsp; Time: ${b.time ? b.time.substring(0, 5) : ''}</small></div>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="openBookingModal(${b.id}, '${b.service_name}', '${b.company_name}', '${b.date}', '${b.time ? b.time.substring(0, 5) : ''}', '${b.status}')">
                    Edit
                    </button>
                </div>
                </div>
            </div>
            </div>
        `).join('');
        
        // Inicializar modal de gestión de reservas
        initBookingModal();
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
    
    servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando servicios...</p>';
    
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
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Error al cargar servicios</p>';
            return;
        }
        
        const services = await res.json();
        
        if (!services.length) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay servicios</p>';
            return;
        }
        
        // Agrupar servicios por categoría
        const servicesByCategory = {};
        services.forEach(s => {
            const category = s.category || 'Other';
            if (!servicesByCategory[category]) {
                servicesByCategory[category] = [];
            }
            servicesByCategory[category].push(s);
        });
        
        // Generar HTML agrupado por categoría
        let html = '';
        Object.keys(servicesByCategory).sort().forEach(category => {
            html += `
                <div class="col-12 mb-4">
                    <h4 class="mb-3 text-primary">${category}</h4>
                    <div class="row">
            `;
            
            servicesByCategory[category].forEach(s => {
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 service-card" style="cursor:pointer;" onclick="openServiceModal(${JSON.stringify(s).replace(/"/g, '&quot;')})">
                            ${s.image ? `<img src="${s.image}" class="card-img-top" alt="${s.name}" style="max-height:180px;object-fit:cover;">` : ''}
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title mb-0">${s.name}</h5>
                                </div>
                                <p class="card-text text-truncate">${s.description ?? ''}</p>
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <small style="color: var(--text-secondary);">${s.company_name ?? ''}</small>
                                    <strong>${s.price ? '$' + parseFloat(s.price).toFixed(2) : ''}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        servicesContainer.innerHTML = html;
        
        // Ya no necesitamos event listeners para botones de reserva
        // porque las tarjetas ahora abren el modal directamente
    } catch (err) {
        console.error(err);
        servicesContainer.innerHTML = '<p class="text-danger">Error al cargar servicios</p>';
    }
}

// Abrir modal de publicación con comentarios
let currentPostData = null;

function openPostModal(postData) {
    currentPostData = postData;
    const modal = new bootstrap.Modal(document.getElementById('postModal'));
    
    // Llenar datos del post
    document.getElementById('postModalCompany').textContent = postData.company_name;
    
    const postImage = document.getElementById('postModalImage');
    const postImageContainer = document.getElementById('postModalImageContainer');
    if (postData.image) {
        postImage.src = postData.image;
        postImageContainer.style.display = 'block';
    } else {
        postImageContainer.style.display = 'none';
    }
    
    document.getElementById('postModalContent').textContent = postData.content;
    document.getElementById('postModalDate').textContent = new Date(postData.created_at).toLocaleDateString();
    
    // Llenar comentarios
    const commentsList = document.getElementById('postCommentsList');
    if (postData.comments.length === 0) {
        commentsList.innerHTML = '<p style="color: var(--text-secondary);">No hay comentarios aún</p>';
    } else {
        commentsList.innerHTML = postData.comments.map(c => `
            <div class="comment-item mb-3 p-2" style="background:var(--bg-hover);border-radius:8px;">
                <div class="d-flex justify-content-between align-items-start">
                    <strong style="font-size:0.9rem;">${c.user_name}</strong>
                    <small style="color: var(--text-secondary); font-size:0.75rem;">${new Date(c.created_at).toLocaleString()}</small>
                </div>
                <p class="mb-0 mt-1" style="font-size:0.85rem;">${c.content}</p>
            </div>
        `).join('');
    }
    
    modal.show();
}

// Agregar comentario
async function addCommentToPost() {
    const commentInput = document.getElementById('postCommentInput');
    const content = commentInput.value.trim();
    
    if (!content || !currentPostData) return;
    
    const token = localStorage.getItem('auth_token');
    
    try {
        const res = await fetch(`/api/posts/${currentPostData.id}/comments`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content })
        });
        
        if (!res.ok) {
            alert('Error al agregar comentario');
            return;
        }
        
        const data = await res.json();
        
        // Agregar el nuevo comentario a la lista
        currentPostData.comments.push(data.comment);
        
        // Actualizar la vista del modal
        const commentsList = document.getElementById('postCommentsList');
        if (currentPostData.comments.length === 1) {
            commentsList.innerHTML = '';
        }
        
        commentsList.innerHTML += `
            <div class="comment-item mb-3 p-2" style="background:var(--bg-hover);border-radius:8px;">
                <div class="d-flex justify-content-between align-items-start">
                    <strong style="font-size:0.9rem;">${data.comment.user_name}</strong>
                    <small style="color: var(--text-secondary); font-size:0.75rem;">${new Date(data.comment.created_at).toLocaleString()}</small>
                </div>
                <p class="mb-0 mt-1" style="font-size:0.85rem;">${data.comment.content}</p>
            </div>
        `;
        
        commentInput.value = '';
        
        // Recargar posts para actualizar el contador
        await loadFollowedPosts(token);
    } catch (err) {
        console.error('Error adding comment:', err);
        alert('Error al agregar comentario');
    }
}

// Variables globales para el modal de reservas
let currentBookingId = null;
let bookingModal = null;

// Inicializar modal de gestión de reservas
function initBookingModal() {
    const bookingModalEl = document.getElementById('bookingModal');
    if (bookingModalEl && window.bootstrap && !bookingModal) {
        bookingModal = new bootstrap.Modal(bookingModalEl);
        
        // Configurar botón de eliminar
        const deleteBtn = document.getElementById('deleteBookingBtn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', deleteBooking);
        }
        
        // Configurar botón de actualizar
        const updateBtn = document.getElementById('updateBookingBtn');
        if (updateBtn) {
            updateBtn.addEventListener('click', updateBooking);
        }
    }
}

// Abrir modal de gestión de reserva
window.openBookingModal = function(id, serviceName, companyName, date, time, status) {
    currentBookingId = id;
    
    document.getElementById('modalServiceName').innerText = serviceName;
    document.getElementById('modalCompanyName').innerText = companyName;
    document.getElementById('modalDateInput').value = date;
    document.getElementById('modalTimeInput').value = time;
    document.getElementById('modalStatus').innerText = status;
    
    if (!bookingModal) {
        initBookingModal();
    }
    
    if (bookingModal) {
        bookingModal.show();
    }
}

// Actualizar reserva
async function updateBooking() {
    if (!currentBookingId) return;
    
    const newDate = document.getElementById('modalDateInput').value;
    const newTime = document.getElementById('modalTimeInput').value;
    
    if (!newDate || !newTime) {
        alert('Por favor completa fecha y hora');
        return;
    }
    
    const token = localStorage.getItem('auth_token');
    if (!token) {
        alert('No autenticado');
        return;
    }
    
    try {
        const res = await fetch(`/api/user/bookings/${currentBookingId}`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                date: newDate,
                time: newTime + ':00'
            })
        });
        
        if (res.ok) {
            alert('Reserva actualizada correctamente');
            if (bookingModal) bookingModal.hide();
            // Recargar reservas
            await loadUserBookings(token);
        } else {
            const error = await res.json().catch(() => ({ message: 'Error desconocido' }));
            alert('Error al actualizar la reserva: ' + (error.message || 'Error desconocido'));
        }
    } catch (err) {
        console.error('Error updating booking:', err);
        alert('Error al actualizar la reserva');
    }
}

// Eliminar reserva
async function deleteBooking() {
    if (!currentBookingId) return;
    
    if (!confirm('¿Estás seguro de que quieres eliminar esta reserva?')) {
        return;
    }
    
    const token = localStorage.getItem('auth_token');
    if (!token) {
        alert('No autenticado');
        return;
    }
    
    try {
        const res = await fetch(`/api/user/bookings/${currentBookingId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (res.ok) {
            alert('Reserva eliminada correctamente');
            if (bookingModal) bookingModal.hide();
            // Recargar reservas
            await loadUserBookings(token);
        } else {
            alert('Error al eliminar la reserva');
        }
    } catch (err) {
        console.error('Error deleting booking:', err);
        alert('Error al eliminar la reserva');
    }
}

// Inicializar navegación del carrusel de stories
function initStoriesCarouselNavigation() {
    const container = document.getElementById('storiesContainer');
    const leftBtn = document.getElementById('storiesScrollLeft');
    const rightBtn = document.getElementById('storiesScrollRight');
    
    if (!container || !leftBtn || !rightBtn) return;
    
    // Función para verificar y mostrar/ocultar flechas
    function updateArrows() {
        const scrollLeft = container.scrollLeft;
        const scrollWidth = container.scrollWidth;
        const clientWidth = container.clientWidth;
        
        // Mostrar flecha izquierda si no estamos al inicio
        leftBtn.style.display = scrollLeft > 0 ? 'block' : 'none';
        
        // Mostrar flecha derecha si no estamos al final
        rightBtn.style.display = scrollLeft < (scrollWidth - clientWidth - 5) ? 'block' : 'none';
    }
    
    // Event listeners para las flechas
    leftBtn.addEventListener('click', function() {
        container.scrollBy({ left: -300, behavior: 'smooth' });
        setTimeout(updateArrows, 300);
    });
    
    rightBtn.addEventListener('click', function() {
        container.scrollBy({ left: 300, behavior: 'smooth' });
        setTimeout(updateArrows, 300);
    });
    
    // Actualizar flechas al hacer scroll
    container.addEventListener('scroll', updateArrows);
    
    // Actualizar flechas inicialmente
    setTimeout(updateArrows, 100);
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

// Variables para el modal de nueva reserva
let newBookingModal = null;
let currentServiceId = null;

// Inicializar modal de nueva reserva
function initNewBookingModal() {
    const newBookingModalEl = document.getElementById('newBookingModal');
    if (newBookingModalEl && window.bootstrap && !newBookingModal) {
        newBookingModal = new bootstrap.Modal(newBookingModalEl);
        
        // Configurar botón de crear
        const createBtn = document.getElementById('createBookingBtn');
        if (createBtn) {
            createBtn.addEventListener('click', createBooking);
        }
    }
}

// Abrir modal de nueva reserva
window.openNewBookingModal = function(serviceId, serviceName, companyName) {
    currentServiceId = serviceId;
    
    document.getElementById('newModalServiceName').innerText = serviceName;
    document.getElementById('newModalCompanyName').innerText = companyName;
    
    // Limpiar campos
    document.getElementById('newModalDateInput').value = '';
    document.getElementById('newModalTimeInput').value = '';
    
    if (!newBookingModal) {
        initNewBookingModal();
    }
    
    if (newBookingModal) {
        newBookingModal.show();
    }
}

// Crear nueva reserva
async function createBooking() {
    if (!currentServiceId) return;
    
    const date = document.getElementById('newModalDateInput').value;
    const time = document.getElementById('newModalTimeInput').value;
    
    if (!date || !time) {
        alert('Please complete date and time');
        return;
    }
    
    const token = localStorage.getItem('auth_token');
    if (!token) {
        alert('Not authenticated');
        return;
    }
    
    try {
        const res = await fetch('/api/user/bookings', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                service_id: currentServiceId,
                date: date,
                time: time
            })
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            alert(data.message || 'Error creating booking');
            return;
        }
        
        alert('Booking created successfully!');
        
        if (newBookingModal) {
            newBookingModal.hide();
        }
        
        // Recargar reservas si estamos en la sección de inicio
        await loadUserBookings(token);
        
    } catch (err) {
        console.error('[debug] createBooking error', err);
        alert('Error creating booking');
    }
}

// Función de búsqueda
async function searchServices(searchTerm) {
    const token = localStorage.getItem('auth_token');
    const servicesContainer = document.getElementById('servicesContainer');
    
    if (!servicesContainer) return;
    
    servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Searching...</p>';
    
    try {
        const url = searchTerm ? `/api/services?search=${encodeURIComponent(searchTerm)}` : '/api/services';
        
        const res = await fetch(url, {
            headers: token ? {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            } : {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Error searching services</p>';
            return;
        }
        
        const services = await res.json();
        
        if (!services.length) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No services found</p>';
            return;
        }
        
        // Agrupar servicios por categoría
        const servicesByCategory = {};
        services.forEach(s => {
            const category = s.category || 'Other';
            if (!servicesByCategory[category]) {
                servicesByCategory[category] = [];
            }
            servicesByCategory[category].push(s);
        });
        
        // Generar HTML agrupado por categoría
        let html = '';
        Object.keys(servicesByCategory).sort().forEach(category => {
            html += `
                <div class="col-12 mb-4">
                    <h4 class="mb-3 text-primary">${category}</h4>
                    <div class="row">
            `;
            
            servicesByCategory[category].forEach(s => {
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 service-card" style="cursor:pointer;" onclick="openServiceModal(${JSON.stringify(s).replace(/"/g, '&quot;')})">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title mb-0">${s.name}</h5>
                                </div>
                                <p class="card-text text-truncate">${s.description ?? ''}</p>
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <small style="color: var(--text-secondary);">${s.company_name ?? ''}</small>
                                    <strong>${s.price ? '$' + parseFloat(s.price).toFixed(2) : ''}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        servicesContainer.innerHTML = html;
        
        // Ya no necesitamos event listeners para botones de reserva
        // porque las tarjetas ahora abren el modal directamente
    } catch (err) {
        console.error(err);
        servicesContainer.innerHTML = '<p class="text-danger">Error searching services</p>';
    }
}

// Event listener para la barra de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            
            // Debounce: esperar 500ms después de que el usuario deje de escribir
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (searchTerm.length >= 2 || searchTerm.length === 0) {
                    // Cambiar a la sección de servicios automáticamente
                    document.querySelectorAll('main.content section').forEach(s => {
                        s.style.display = 'none';
                    });
                    const servicesSection = document.querySelector('.services-section');
                    if (servicesSection) {
                        servicesSection.style.display = '';
                    }
                    
                    searchServices(searchTerm);
                }
            }, 500);
        });
        
        // Buscar al presionar Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = e.target.value.trim();
                if (searchTerm.length >= 2 || searchTerm.length === 0) {
                    document.querySelectorAll('main.content section').forEach(s => {
                        s.style.display = 'none';
                    });
                    const servicesSection = document.querySelector('.services-section');
                    if (servicesSection) {
                        servicesSection.style.display = '';
                    }
                    
                    searchServices(searchTerm);
                }
            }
        });
    }
});
// ============================================
// MODAL DE SERVICIO CON VALORACIONES
// ============================================

let currentServiceData = null;
let selectedRating = 0;

// Abrir modal de servicio
function openServiceModal(service) {
    currentServiceData = service;
    
    const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    
    document.getElementById('serviceModalName').textContent = service.name || '';
    document.getElementById('serviceModalCompany').textContent = service.company_name || '';
    document.getElementById('serviceModalCategory').textContent = service.category || '';
    document.getElementById('serviceModalDescription').textContent = service.description || '';
    document.getElementById('serviceModalPrice').textContent = service.price ? '$' + parseFloat(service.price).toFixed(2) : 'N/A';
    
    const imageContainer = document.getElementById('serviceModalImageContainer');
    const image = document.getElementById('serviceModalImage');
    if (service.image) {
        image.src = service.image;
        image.alt = service.name;
        imageContainer.style.display = 'block';
    } else {
        imageContainer.style.display = 'none';
    }
    
    // Configurar botón de reserva
    const bookBtn = document.getElementById('serviceModalBookBtn');
    bookBtn.onclick = function() {
        modal.hide();
        openNewBookingModal(service.id, service.name, service.company_name);
    };
    
    // Cargar valoraciones
    loadServiceRatings(service.id);
    
    // Reset rating selection
    selectedRating = 0;
    document.querySelectorAll('.rating-star').forEach(btn => {
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-outline-warning');
    });
    document.getElementById('serviceRatingComment').value = '';
    
    // Event listeners para los botones de rating
    document.querySelectorAll('.rating-star').forEach(btn => {
        btn.onclick = function() {
            selectedRating = parseInt(this.dataset.rating);
            document.querySelectorAll('.rating-star').forEach(b => {
                const r = parseInt(b.dataset.rating);
                if (r <= selectedRating) {
                    b.classList.remove('btn-outline-warning');
                    b.classList.add('btn-warning');
                } else {
                    b.classList.remove('btn-warning');
                    b.classList.add('btn-outline-warning');
                }
            });
        };
    });
    
    modal.show();
}

// Cargar valoraciones del servicio
async function loadServiceRatings(serviceId) {
    const ratingsList = document.getElementById('serviceRatingsList');
    ratingsList.innerHTML = '<p style="color: var(--text-secondary);">Loading ratings...</p>';
    
    const token = localStorage.getItem('auth_token');
    
    try {
        const res = await fetch(`/api/services/${serviceId}/ratings`, {
            headers: token ? {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            } : {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            ratingsList.innerHTML = '<p style="color: var(--text-secondary);">Error loading ratings</p>';
            return;
        }
        
        const ratings = await res.json();
        
        if (!ratings || ratings.length === 0) {
            ratingsList.innerHTML = '<p style="color: var(--text-secondary);">No ratings yet. Be the first to rate this service!</p>';
            return;
        }
        
        ratingsList.innerHTML = ratings.map(r => `
            <div class="rating-item mb-3 p-3" style="background:var(--bg-hover);border-radius:8px;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong>${r.user_name || 'Anonymous'}</strong>
                        <div class="text-warning">${'★'.repeat(r.rating)}${'☆'.repeat(5 - r.rating)}</div>
                    </div>
                    <small style="color: var(--text-secondary);">${new Date(r.created_at).toLocaleDateString()}</small>
                </div>
                ${r.comment ? `<p class="mb-0" style="color: var(--text-secondary);">${r.comment}</p>` : ''}
            </div>
        `).join('');
        
    } catch (err) {
        console.error('Error loading ratings:', err);
        ratingsList.innerHTML = '<p class="text-danger">Error loading ratings</p>';
    }
}

// Agregar valoración al servicio
async function addRatingToService() {
    if (!currentServiceData) return;
    
    if (selectedRating === 0) {
        alert('Please select a rating (1-5 stars)');
        return;
    }
    
    const comment = document.getElementById('serviceRatingComment').value.trim();
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('You must be logged in to rate a service');
        return;
    }
    
    try {
        const res = await fetch(`/api/services/${currentServiceData.id}/ratings`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rating: selectedRating,
                comment: comment
            })
        });
        
        if (!res.ok) {
            const data = await res.json();
            alert(data.message || 'Error adding rating');
            return;
        }
        
        // Reset form
        selectedRating = 0;
        document.querySelectorAll('.rating-star').forEach(btn => {
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-outline-warning');
        });
        document.getElementById('serviceRatingComment').value = '';
        
        // Reload ratings
        loadServiceRatings(currentServiceData.id);
        
        alert('Rating added successfully!');
        
    } catch (err) {
        console.error('Error adding rating:', err);
        alert('Error adding rating');
    }
}