/**
 * user-navigation.js
 * Manejo de navegación y eventos de la interfaz de usuario
 */

(function(){
    // Variables globales
    const navbar = document.getElementById('navegacion');
    const navbarIndicator = document.getElementById('barra_indicador');
    let timeout;
    
    // Mostrar la barra de navegación
    function showNavbar() {
        if (navbar) navbar.style.transform = 'translateY(0)';
        if (navbarIndicator) navbarIndicator.style.display = 'none';
    }
    
    // Ocultar la barra de navegación
    function hideNavbar() {
        if (navbar) navbar.style.transform = 'translateY(-100%)';
        if (navbarIndicator) navbarIndicator.style.display = 'block';
    }
    
    function initScrollBehavior() {
        if (!navbar || !navbarIndicator) {
            console.error('❌ navbar o navbarIndicator no encontrados!');
            return;
        }
        
        // Detectar el movimiento del mouse
        window.addEventListener('mousemove', function(e) {
            clearTimeout(timeout);
            
            // Si el cursor está cerca de la parte superior de la ventana, mostramos la barra de navegación
            if (e.clientY < 50) {
                showNavbar();
            }
            
            // Si el cursor se aleja de la parte superior, la ocultamos después de 1 segundos
            timeout = setTimeout(function() {
                hideNavbar();
            }, 1000);
        });
        
        // Mostrar la barra de navegación cuando se haga clic en el indicador
        navbarIndicator.addEventListener('click', showNavbar);
    }

    //
    function showOnlySection(className) {
        document.querySelectorAll('main.content section').forEach(s => {
            s.style.display = s.classList.contains(className) ? '' : 'none';
        });
    }

    function showMultipleSections(classes) {
        document.querySelectorAll('main.content section').forEach(s => {
            s.style.display = classes.some(c => s.classList.contains(c)) ? '' : 'none';
        });
    }

    function showPlaceholder(action) {
        // crear sección placeholder si no existe
        let ph = document.getElementById('placeholderSection');
        if (!ph) {
            ph = document.createElement('section');
            ph.id = 'placeholderSection';
            ph.className = 'placeholder-section';
            ph.innerHTML = '<h4 id="placeholderTitle"></h4><p id="placeholderText">Contenido dinámico</p>';
            const mainContent = document.querySelector('main.content');
            if (mainContent) mainContent.prepend(ph);
        }
        
        const titleEl = document.getElementById('placeholderTitle');
        const textEl = document.getElementById('placeholderText');
        
        if (titleEl) {
            titleEl.innerText = action === 'reservas' ? 'Reservas' : 
                               action === 'ayuda' ? 'Ayuda' : 
                               action === 'configuracion' ? 'Configuración' : 
                               action;
        }
        
        if (textEl) {
            textEl.innerText = 'Contenido de la sección "' + action + '".';
        }
        
        // ocultar otras secciones y mostrar el placeholder
        document.querySelectorAll('main.content section').forEach(s => s.style.display = 'none');
        ph.style.display = '';
    }

    // Event listeners para los botones de navegación
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar comportamiento de scroll
        initScrollBehavior();
        
        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                const action = this.dataset.action;
                const userTitle = document.getElementById('userTitle');
                
                if (action === 'servicios') {
                    if (typeof loadServices === 'function') {
                        await loadServices();
                    }
                    showOnlySection('services-section');
                    if (userTitle) userTitle.innerText = 'Todos los servicios';
                    
                } else if (action === 'inicio') {
                    const token = localStorage.getItem('auth_token');
                    if (typeof loadFollowedServices === 'function') {
                        await loadFollowedServices(token);
                    }
                    if (typeof loadUserBookings === 'function') {
                        await loadUserBookings(token);
                    }
                    showMultipleSections(['stories-section','services-section','reservas-section']);
                    if (userTitle) userTitle.innerText = 'Página del usuario';
                    
                } else if (action === 'publicaciones') {
                    const token = localStorage.getItem('auth_token');
                    if (typeof loadFollowedPosts === 'function') {
                        await loadFollowedPosts(token);
                    }
                    showOnlySection('posts-section');
                    if (userTitle) userTitle.innerText = 'Publicaciones';
                    
                } else {
                    showPlaceholder(action);
                    if (userTitle) userTitle.innerText = this.innerText;
                }
            });
        });

        // Toggle navegación en móvil
        const navToggleBtn = document.getElementById('navToggleBtn');
        
        if (navToggleBtn && topNav) {
            navToggleBtn.addEventListener('click', function() {
                topNav.classList.toggle('show');
            });
        }

        // Logout handler: revocar token en servidor y eliminar localmente
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
    });
})();
