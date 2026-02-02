/**
 * company-content.js
 * Funciones para gestión de contenido de empresa: posts, stories, servicios y reservas
 */

// ============================================
// GESTIÓN DE POSTS Y STORIES
// ============================================

// Cargar posts de la empresa
async function loadCompanyPosts() {
    const token = localStorage.getItem('auth_token');
    const postsContainer = document.getElementById('companyPostsContainer');
    
    if (!postsContainer) return;
    
    postsContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando publicaciones...</p>';
    
    if (!token) {
        postsContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/company/posts', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar posts');
        }

        const posts = await res.json();
        
        if (!Array.isArray(posts) || posts.length === 0) {
            postsContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay publicaciones aún</p>';
            return;
        }

        postsContainer.innerHTML = posts.map(post => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    ${post.image ? `<img src="${post.image}" class="card-img-top" alt="Post" style="max-height:200px;object-fit:cover;">` : ''}
                    <div class="card-body">
                        <span class="badge ${post.is_story ? 'bg-info' : 'bg-primary'} mb-2">
                            ${post.is_story ? 'Story' : 'Post'}
                        </span>
                        <p class="card-text">${post.content || ''}</p>
                        <small style="color: var(--text-secondary);">
                            ${new Date(post.created_at).toLocaleDateString()}
                        </small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-danger" onclick="deletePost(${post.id})">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error('[debug] loadCompanyPosts exception', err);
        postsContainer.innerHTML = '<p class="text-danger">Error al cargar publicaciones</p>';
    }
}

// Crear nuevo post o story
async function createPost(isStory = false) {
    const content = document.getElementById('newPostContent').value.trim();
    const imageInput = document.getElementById('newPostImage');
    const token = localStorage.getItem('auth_token');

    if (!content && !imageInput.files.length) {
        alert('Debes agregar contenido o imagen');
        return;
    }

    if (!token) {
        alert('No autenticado');
        return;
    }

    const formData = new FormData();
    formData.append('content', content);
    formData.append('is_story', isStory ? '1' : '0');
    
    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    try {
        const res = await fetch('/api/company/posts', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!res.ok) {
            throw new Error('Error al crear publicación');
        }

        // Limpiar formulario
        document.getElementById('newPostContent').value = '';
        document.getElementById('newPostImage').value = '';

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('newPostModal'));
        if (modal) modal.hide();

        // Recargar posts
        await loadCompanyPosts();
        
        alert(isStory ? 'Story creada exitosamente' : 'Post creado exitosamente');
    } catch (err) {
        console.error('[debug] createPost exception', err);
        alert('Error al crear publicación');
    }
}

// Eliminar post
async function deletePost(postId) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta publicación?')) {
        return;
    }

    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('No autenticado');
        return;
    }

    try {
        const res = await fetch(`/api/company/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al eliminar post');
        }

        await loadCompanyPosts();
        alert('Publicación eliminada exitosamente');
    } catch (err) {
        console.error('[debug] deletePost exception', err);
        alert('Error al eliminar publicación');
    }
}

// ============================================
// GESTIÓN DE SERVICIOS
// ============================================

// Cargar servicios de la empresa
async function loadCompanyServices() {
    const token = localStorage.getItem('auth_token');
    const servicesContainer = document.getElementById('companyServicesContainer');
    
    if (!servicesContainer) return;
    
    servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando servicios...</p>';
    
    if (!token) {
        servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/company/services', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar servicios');
        }

        const services = await res.json();
        
        if (!Array.isArray(services) || services.length === 0) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay servicios aún. <button class="btn btn-sm btn-primary" onclick="openNewServiceModal()">Crear servicio</button></p>';
            return;
        }

        servicesContainer.innerHTML = services.map(service => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    ${service.image ? `<img src="${service.image}" class="card-img-top" alt="${service.name}" style="max-height:180px;object-fit:cover;">` : ''}
                    <div class="card-body">
                        <h5 class="card-title">${service.name}</h5>
                        <p class="card-text text-truncate">${service.description || ''}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small style="color: var(--text-secondary);">${service.category || 'Sin categoría'}</small>
                            <strong>$${parseFloat(service.price || 0).toFixed(2)}</strong>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="editService(${service.id})">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteService(${service.id})">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error('[debug] loadCompanyServices exception', err);
        servicesContainer.innerHTML = '<p class="text-danger">Error al cargar servicios</p>';
    }
}

// Variables para el modal de servicio
let editingServiceId = null;

// Abrir modal para nuevo servicio
function openNewServiceModal() {
    editingServiceId = null;
    document.getElementById('serviceModalTitle').textContent = 'Crear nuevo servicio';
    document.getElementById('serviceName').value = '';
    document.getElementById('serviceDescription').value = '';
    document.getElementById('servicePrice').value = '';
    document.getElementById('serviceCategory').value = '';
    document.getElementById('serviceImage').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    modal.show();
}

// Editar servicio existente
async function editService(serviceId) {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('No autenticado');
        return;
    }

    try {
        const res = await fetch(`/api/company/services/${serviceId}`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar servicio');
        }

        const service = await res.json();
        
        editingServiceId = serviceId;
        document.getElementById('serviceModalTitle').textContent = 'Editar servicio';
        document.getElementById('serviceName').value = service.name || '';
        document.getElementById('serviceDescription').value = service.description || '';
        document.getElementById('servicePrice').value = service.price || '';
        document.getElementById('serviceCategory').value = service.category || '';
        
        const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
        modal.show();
    } catch (err) {
        console.error('[debug] editService exception', err);
        alert('Error al cargar servicio');
    }
}

// Guardar servicio (crear o actualizar)
async function saveService() {
    const name = document.getElementById('serviceName').value.trim();
    const description = document.getElementById('serviceDescription').value.trim();
    const price = document.getElementById('servicePrice').value.trim();
    const category = document.getElementById('serviceCategory').value.trim();
    const imageInput = document.getElementById('serviceImage');
    const token = localStorage.getItem('auth_token');

    if (!name || !price) {
        alert('Nombre y precio son obligatorios');
        return;
    }

    if (!token) {
        alert('No autenticado');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('price', price);
    formData.append('category', category);
    
    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    try {
        let res;
        if (editingServiceId) {
            // Actualizar servicio existente
            formData.append('_method', 'PUT');
            res = await fetch(`/api/company/services/${editingServiceId}`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData
            });
        } else {
            // Crear nuevo servicio
            res = await fetch('/api/company/services', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData
            });
        }

        if (!res.ok) {
            throw new Error('Error al guardar servicio');
        }

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('serviceModal'));
        if (modal) modal.hide();

        // Recargar servicios
        await loadCompanyServices();
        
        alert(editingServiceId ? 'Servicio actualizado exitosamente' : 'Servicio creado exitosamente');
        editingServiceId = null;
    } catch (err) {
        console.error('[debug] saveService exception', err);
        alert('Error al guardar servicio');
    }
}

// Eliminar servicio
async function deleteService(serviceId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este servicio?')) {
        return;
    }

    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('No autenticado');
        return;
    }

    try {
        const res = await fetch(`/api/company/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al eliminar servicio');
        }

        await loadCompanyServices();
        alert('Servicio eliminado exitosamente');
    } catch (err) {
        console.error('[debug] deleteService exception', err);
        alert('Error al eliminar servicio');
    }
}

// ============================================
// GESTIÓN DE RESERVAS (AGENDA)
// ============================================

// Cargar reservas recibidas
async function loadCompanyBookings() {
    const token = localStorage.getItem('auth_token');
    const bookingsContainer = document.getElementById('companyBookingsContainer');
    
    if (!bookingsContainer) return;
    
    bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando reservas...</p>';
    
    if (!token) {
        bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/company/bookings', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar reservas');
        }

        const bookings = await res.json();
        
        if (!Array.isArray(bookings) || bookings.length === 0) {
            bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay reservas aún</p>';
            return;
        }

        // Agrupar reservas por estado
        const pending = bookings.filter(b => b.status === 'pending');
        const confirmed = bookings.filter(b => b.status === 'confirmed');
        const completed = bookings.filter(b => b.status === 'completed');
        const cancelled = bookings.filter(b => b.status === 'cancelled');

        bookingsContainer.innerHTML = `
            <div class="row">
                <!-- Pendientes -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <h5 class="text-warning">Pendientes (${pending.length})</h5>
                    <div class="bookings-list">
                        ${pending.length === 0 ? '<p class="text-muted">Sin reservas</p>' : pending.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Confirmadas -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <h5 class="text-info">Confirmadas (${confirmed.length})</h5>
                    <div class="bookings-list">
                        ${confirmed.length === 0 ? '<p class="text-muted">Sin reservas</p>' : confirmed.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Completadas -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <h5 class="text-success">Completadas (${completed.length})</h5>
                    <div class="bookings-list">
                        ${completed.length === 0 ? '<p class="text-muted">Sin reservas</p>' : completed.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Canceladas -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <h5 class="text-danger">Canceladas (${cancelled.length})</h5>
                    <div class="bookings-list">
                        ${cancelled.length === 0 ? '<p class="text-muted">Sin reservas</p>' : cancelled.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error('[debug] loadCompanyBookings exception', err);
        bookingsContainer.innerHTML = '<p class="text-danger">Error al cargar reservas</p>';
    }
}

// Renderizar tarjeta de reserva
function renderBookingCard(booking) {
    const statusColors = {
        'pending': 'warning',
        'confirmed': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    };
    
    const statusColor = statusColors[booking.status] || 'secondary';
    
    return `
        <div class="card mb-2">
            <div class="card-body p-2">
                <div><strong>${booking.user_name || 'Usuario'}</strong></div>
                <div><small>${booking.service_name || 'Servicio'}</small></div>
                <div><small class="text-muted">${booking.date} ${booking.time ? booking.time.substring(0, 5) : ''}</small></div>
                <div class="mt-2">
                    ${booking.status === 'pending' ? `
                        <button class="btn btn-sm btn-success me-1" onclick="updateBookingStatus(${booking.id}, 'confirmed')">
                            Confirmar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="updateBookingStatus(${booking.id}, 'cancelled')">
                            Cancelar
                        </button>
                    ` : booking.status === 'confirmed' ? `
                        <button class="btn btn-sm btn-success" onclick="updateBookingStatus(${booking.id}, 'completed')">
                            Completar
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Actualizar estado de reserva
async function updateBookingStatus(bookingId, newStatus) {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('No autenticado');
        return;
    }

    try {
        const res = await fetch(`/api/company/bookings/${bookingId}`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!res.ok) {
            throw new Error('Error al actualizar reserva');
        }

        await loadCompanyBookings();
        alert('Reserva actualizada exitosamente');
    } catch (err) {
        console.error('[debug] updateBookingStatus exception', err);
        alert('Error al actualizar reserva');
    }
}

// ============================================
// ESTADÍSTICAS
// ============================================

// Cargar estadísticas de la empresa
async function loadCompanyStats() {
    const token = localStorage.getItem('auth_token');
    const statsContainer = document.getElementById('companyStatsContainer');
    
    if (!statsContainer) return;
    
    if (!token) {
        statsContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/company/stats', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar estadísticas');
        }

        const stats = await res.json();
        
        statsContainer.innerHTML = `
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">${stats.total_services || 0}</h3>
                            <p class="mb-0">Servicios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">${stats.total_bookings || 0}</h3>
                            <p class="mb-0">Reservas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info">${stats.total_followers || 0}</h3>
                            <p class="mb-0">Seguidores</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning">${stats.average_rating || '0.0'}</h3>
                            <p class="mb-0">Valoración promedio</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error('[debug] loadCompanyStats exception', err);
        statsContainer.innerHTML = '<p class="text-danger">Error al cargar estadísticas</p>';
    }
}

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        console.warn('No hay token de autenticación');
        return;
    }

    // Cargar estadísticas por defecto
    await loadCompanyStats();
});
