/**
 * user-content.js (Sin autenticación)
 * Funciones para cargar contenido público: posts y servicios sin autenticación
 */

// Cargar posts públicos aleatorios
async function loadPublicPosts() {
    const postsContainer = document.getElementById('postsContainer');
    
    if (!postsContainer) return;
    
    postsContainer.innerHTML = '<p style="color: var(--text-secondary);">Loading posts...</p>';
    
    try {
        const res = await fetch('/api/posts', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            postsContainer.innerHTML = '<p class="text-danger">Error loading posts</p>';
            return;
        }
        
        const posts = await res.json();
        
        if (!Array.isArray(posts) || posts.length === 0) {
            postsContainer.innerHTML = '<p style="color: var(--text-secondary);">No posts available</p>';
            return;
        }

        // Agrupar posts por empresa
        const postsByCompany = {};
        posts.forEach(p => {
            if (!postsByCompany[p.company_id]) {
                postsByCompany[p.company_id] = {
                    company_name: p.company_name,
                    posts: []
                };
            }
            postsByCompany[p.company_id].posts.push(p);
        });

        // Renderizar posts agrupados por empresa con carrusel horizontal tipo stories
        postsContainer.innerHTML = Object.values(postsByCompany).map((company, companyIndex) => {
            return `
            <div class="company-posts-section mb-4">
                <h5 class="mb-3">${company.company_name}</h5>
                <div class="posts-carousel-horizontal">
                    ${company.posts.map(p => `
                        <div class="post-card-horizontal" onclick='openPostModal(${JSON.stringify(p).replace(/'/g, "&#39;")})'>
                            <div class="card">
                                ${p.image ? `<img src="${p.image}" class="card-img-top" alt="${p.company_name}">` : ''}
                                <div class="card-body">
                                    <p class="card-text">${p.content ? p.content.substring(0, 80) + (p.content.length > 80 ? '...' : '') : ''}</p>
                                    <small style="color: var(--text-secondary);">${new Date(p.created_at).toLocaleDateString()}</small>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            `;
        }).join('');

    } catch (e) {
        console.error(e);
        if (postsContainer) {
            postsContainer.innerHTML = '<p class="text-danger">Error loading posts</p>';
        }
    }
}

// Cargar servicios públicos aleatorios
async function loadPublicServices() {
    const servicesContainer = document.getElementById('servicesContainer');
    
    if (!servicesContainer) return;
    
    servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Loading services...</p>';
    
    try {
        const res = await fetch('/api/services', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            servicesContainer.innerHTML = '<p class="text-danger">Error loading services</p>';
            return;
        }
        
        const services = await res.json();
        
        if (!Array.isArray(services) || services.length === 0) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No services available</p>';
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
        console.error(err);
        if (servicesContainer) {
            servicesContainer.innerHTML = '<p class="text-danger">Error loading services</p>';
        }
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
    
    // Cargar comentarios
    loadPostComments(postData.id);
    
    modal.show();
}

// Cargar comentarios del post
async function loadPostComments(postId) {
    const commentsList = document.getElementById('postCommentsList');
    commentsList.innerHTML = '<p style="color: var(--text-secondary);">Loading comments...</p>';
    
    try {
        const res = await fetch(`/api/posts/${postId}/comments`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            commentsList.innerHTML = '<p class="text-danger">Error loading comments</p>';
            return;
        }
        
        const comments = await res.json();
        
        if (!Array.isArray(comments) || comments.length === 0) {
            commentsList.innerHTML = '<p style="color: var(--text-secondary);">No comments yet</p>';
            return;
        }
        
        commentsList.innerHTML = comments.map(c => `
            <div class="comment-item mb-2">
                <strong>${c.user_name || 'Anonymous'}</strong>
                <p class="mb-1">${c.content}</p>
                <small style="color: var(--text-secondary);">${new Date(c.created_at).toLocaleDateString()}</small>
            </div>
        `).join('');
        
    } catch (err) {
        console.error(err);
        commentsList.innerHTML = '<p class="text-danger">Error loading comments</p>';
    }
}

// Agregar comentario (requiere login)
async function addCommentToPost() {
    alert('Please login to comment on posts');
    window.location.href = '/login.html';
}

// Abrir modal de servicio
let currentServiceData = null;

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
        imageContainer.style.display = 'block';
    } else {
        imageContainer.style.display = 'none';
    }
    
    // Cargar valoraciones
    loadServiceRatings(service.id);
    
    modal.show();
}

// Cargar valoraciones del servicio
async function loadServiceRatings(serviceId) {
    const ratingsList = document.getElementById('serviceRatingsList');
    ratingsList.innerHTML = '<p style="color: var(--text-secondary);">Loading ratings...</p>';
    
    try {
        const res = await fetch(`/api/services/${serviceId}/ratings`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            ratingsList.innerHTML = '<p class="text-danger">Error loading ratings</p>';
            return;
        }
        
        const ratings = await res.json();
        
        if (!Array.isArray(ratings) || ratings.length === 0) {
            ratingsList.innerHTML = '<p style="color: var(--text-secondary);">No ratings yet</p>';
            return;
        }
        
        ratingsList.innerHTML = ratings.map(r => `
            <div class="rating-item mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong>${r.user_name || 'Anonymous'}</strong>
                    <span class="text-warning">${'★'.repeat(r.rating)}${'☆'.repeat(5 - r.rating)}</span>
                </div>
                ${r.comment ? `<p class="mb-1">${r.comment}</p>` : ''}
                <small style="color: var(--text-secondary);">${new Date(r.created_at).toLocaleDateString()}</small>
            </div>
        `).join('');
        
    } catch (err) {
        console.error(err);
        ratingsList.innerHTML = '<p class="text-danger">Error loading ratings</p>';
    }
}

// Función de búsqueda
async function searchServices(searchTerm) {
    const servicesContainer = document.getElementById('servicesContainer');
    
    if (!servicesContainer) return;
    
    servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Searching...</p>';
    
    try {
        const res = await fetch(`/api/services/search?q=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            servicesContainer.innerHTML = '<p class="text-danger">Error searching services</p>';
            return;
        }
        
        const services = await res.json();
        
        if (!Array.isArray(services) || services.length === 0) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No services found</p>';
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
        console.error(err);
        if (servicesContainer) {
            servicesContainer.innerHTML = '<p class="text-danger">Error searching services</p>';
        }
    }
}

// Event listener para la barra de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length === 0) {
                // Si el campo está vacío, cargar servicios normales
                searchTimeout = setTimeout(() => {
                    loadPublicServices();
                }, 300);
            } else if (searchTerm.length >= 2) {
                // Buscar si hay al menos 2 caracteres
                searchTimeout = setTimeout(() => {
                    searchServices(searchTerm);
                }, 300);
            }
        });
    }
});

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', async function() {
    // Cargar contenido público
    await loadPublicServices();
    await loadPublicPosts();
});
