/**
 * company-navigation.js
 * Manejo de navegación y eventos de la interfaz de empresa
 */

(function(){
    // Variables globales
    const sidebar = document.getElementById('sidebar');
    
    // Función para mostrar solo una sección
    function showOnlySection(sectionId) {
        document.querySelectorAll('main.content section').forEach(s => {
            s.style.display = s.id === sectionId ? '' : 'none';
        });
    }

    // Función para actualizar el título de la página
    function updatePageTitle(title) {
        const pageTitle = document.getElementById('pageTitle');
        if (pageTitle) {
            pageTitle.textContent = title;
        }
    }

    // Event listeners para los botones de navegación del sidebar
    document.addEventListener('DOMContentLoaded', function() {
        
        // Menú: Publicaciones
        const menuPosts = document.getElementById('menuPosts');
        if (menuPosts) {
            menuPosts.addEventListener('click', async function() {
                showOnlySection('postsSection');
                updatePageTitle('Mis Publicaciones');
                if (typeof loadCompanyPosts === 'function') {
                    await loadCompanyPosts();
                }
            });
        }

        // Menú: Gestión de servicios
        const menuServices = document.getElementById('menuServices');
        if (menuServices) {
            menuServices.addEventListener('click', async function() {
                showOnlySection('servicesSection');
                updatePageTitle('Mis Servicios');
                if (typeof loadCompanyServices === 'function') {
                    await loadCompanyServices();
                }
            });
        }

        // Menú: Agenda de reservas
        const menuBookings = document.getElementById('menuBookings');
        if (menuBookings) {
            menuBookings.addEventListener('click', async function() {
                showOnlySection('bookingsSection');
                updatePageTitle('Agenda de Reservas');
                if (typeof loadCompanyBookings === 'function') {
                    await loadCompanyBookings();
                }
            });
        }

        // Menú: Seguidores
        const menuFollowers = document.getElementById('menuFollowers');
        if (menuFollowers) {
            menuFollowers.addEventListener('click', async function() {
                showOnlySection('followersSection');
                updatePageTitle('Mis Seguidores');
                if (typeof loadCompanyFollowers === 'function') {
                    await loadCompanyFollowers();
                }
            });
        }

        // Botón: Crear nueva publicación
        const btnNewPost = document.getElementById('btnNewPost');
        if (btnNewPost) {
            btnNewPost.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('newPostModal'));
                modal.show();
            });
        }

        // Botón: Crear post normal
        const btnCreatePost = document.getElementById('btnCreatePost');
        if (btnCreatePost) {
            btnCreatePost.addEventListener('click', async function() {
                if (typeof createPost === 'function') {
                    await createPost(false);
                }
            });
        }

        // Botón: Crear story
        const btnCreateStory = document.getElementById('btnCreateStory');
        if (btnCreateStory) {
            btnCreateStory.addEventListener('click', async function() {
                if (typeof createPost === 'function') {
                    await createPost(true);
                }
            });
        }

        // Botón: Crear nuevo servicio
        const btnNewService = document.getElementById('btnNewService');
        if (btnNewService) {
            btnNewService.addEventListener('click', function() {
                if (typeof openNewServiceModal === 'function') {
                    openNewServiceModal();
                }
            });
        }

        // Botón: Guardar servicio
        const btnSaveService = document.getElementById('btnSaveService');
        if (btnSaveService) {
            btnSaveService.addEventListener('click', async function() {
                if (typeof saveService === 'function') {
                    await saveService();
                }
            });
        }

        // Logout handler
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function() {
                const token = localStorage.getItem('auth_token');
                if (token) {
                    try {
                        await fetch('/api/logout', {
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer ' + token,
                                'Accept': 'application/json'
                            }
                        });
                    } catch (e) {
                        console.warn('Logout API call failed', e);
                    }
                }
                localStorage.removeItem('auth_token');
                window.location.href = '/index.html';
            });
        }

        // Verificar autenticación y cargar datos iniciales
        const token = localStorage.getItem('auth_token');
        if (!token) {
            console.warn('No hay token de autenticación');
            window.location.href = '/login.html';
            return;
        }

        // Cargar información de la empresa
        loadCompanyInfo();

        // Mostrar sección de publicaciones por defecto
        showOnlySection('postsSection');
        updatePageTitle('Mis Publicaciones');
        if (typeof loadCompanyPosts === 'function') {
            loadCompanyPosts();
        }
    });

    // Cargar información de la empresa
    async function loadCompanyInfo() {
        const token = localStorage.getItem('auth_token');
        
        if (!token) return;

        try {
            const res = await fetch('/api/me', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                throw new Error('Error al obtener información del usuario');
            }

            const user = await res.json();
            
            // Actualizar nombre de la empresa en el sidebar
            const sidebarTitle = document.getElementById('sidebarTitle');
            if (sidebarTitle && user.company) {
                sidebarTitle.textContent = user.company.name || 'Mi Empresa';
            }
        } catch (err) {
            console.error('[debug] loadCompanyInfo exception', err);
        }
    }
})();
