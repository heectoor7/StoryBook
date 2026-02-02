/**
 * company-posts.js
 * Funciones para gestión de publicaciones (posts y stories)
 */

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
        console.error('[Posts] Error al cargar:', err);
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
        console.error('[Posts] Error al crear:', err);
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
        console.error('[Posts] Error al eliminar:', err);
        alert('Error al eliminar publicación');
    }
}
