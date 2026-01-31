/**
 * user-navigation.js
 * Manejo de navegación y eventos de la interfaz de usuario
 */

(function(){
    // Variables globales
    let lastScrollTop = 0;
    let ticking = false;
    let topNav = null;
    const scrollThreshold = 100; // píxeles antes de activar el hide
    
    function handleScroll() {
        if (!topNav) return;
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Solo ocultar si hacemos scroll hacia abajo y hemos pasado el threshold
        if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
            // Scrolling down - ocultar nav
            if (!topNav.classList.contains('nav-hidden')) {
                topNav.classList.add('nav-hidden');
            }
        } else if (scrollTop < lastScrollTop) {
            // Scrolling up - mostrar nav
            if (topNav.classList.contains('nav-hidden')) {
                topNav.classList.remove('nav-hidden');
            }
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        ticking = false;
    }
    
    function initScrollBehavior() {
        topNav = document.getElementById('topNav');
        
        if (!topNav) {
            console.error('❌ topNav no encontrado!');
            return;
        }
        
        // Optimizar con requestAnimationFrame
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(handleScroll);
                ticking = true;
            }
        }, { passive: true });
        
        // Detectar mouse cerca de la parte superior
        document.addEventListener('mousemove', function(e) {
            // Si el mouse está en los primeros 150px desde arriba, mostrar nav
            if (e.clientY < 150) {
                if (topNav && topNav.classList.contains('nav-hidden')) {
                    topNav.classList.remove('nav-hidden');
                }
            }
        }, { passive: true });
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
