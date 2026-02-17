/**
 * user-navigation.js (Sin autenticación)
 * Manejo de navegación para usuarios no registrados
 */

(function(){
    // Variables globales
    const navbar = document.getElementById('navegacion');
    const navbarIndicator = document.getElementById('barra_indicador');
    const mainElement = document.querySelector('.main');
    const compactMedia = window.matchMedia('(max-width: 992px)');
    let timeout;

    function isCompactView() {
        return compactMedia.matches;
    }
    
    // Mostrar la barra de navegación
    function showNavbar() {
        if (navbar) navbar.style.transform = 'translateY(0)';
        if (navbarIndicator) navbarIndicator.style.display = 'none';
        if (mainElement) mainElement.classList.remove('nav-hidden');
    }
    
    // Ocultar la barra de navegación
    function hideNavbar() {
        if (navbar) navbar.style.transform = 'translateY(-100%)';
        if (navbarIndicator) navbarIndicator.style.display = 'block';
        if (mainElement) mainElement.classList.add('nav-hidden');
    }
    
    function initScrollBehavior() {
        if (isCompactView()) {
            showNavbar();
            return;
        }

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
            
            // Si el cursor se aleja de la parte superior, la ocultamos después de 1 segundo
            timeout = setTimeout(function() {
                hideNavbar();
            }, 1000);
        });
        
        // Mostrar la barra de navegación cuando se haga clic en el indicador
        navbarIndicator.addEventListener('click', showNavbar);
    }

    // Mostrar solo una sección específica
    function showOnlySection(className) {
        document.querySelectorAll('main.content section').forEach(s => {
            s.style.display = s.classList.contains(className) ? '' : 'none';
        });
    }

    // Mostrar múltiples secciones
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
            ph.innerHTML = '<h4 id="placeholderTitle"></h4><p id="placeholderText">Content coming soon...</p>';
            const mainContent = document.querySelector('main.content');
            if (mainContent) mainContent.prepend(ph);
        }
        
        const titleEl = document.getElementById('placeholderTitle');
        const textEl = document.getElementById('placeholderText');
        
        if (titleEl) {
            titleEl.innerText = action === 'ayuda' ? 'Help' : 
                               action === 'configuracion' ? 'Settings' : 
                               action;
        }
        
        if (textEl) {
            if (action === 'ayuda') {
                textEl.innerHTML = 'For help and support, please contact us at: <a href="mailto:support@storybook.com">support@storybook.com</a>';
            } else if (action === 'configuracion') {
                textEl.innerHTML = 'Please <a href="/login.html">login</a> to access settings.';
            } else {
                textEl.innerText = 'Content for "' + action + '" section.';
            }
        }
        
        // ocultar otras secciones y mostrar el placeholder
        document.querySelectorAll('main.content section').forEach(s => s.style.display = 'none');
        ph.style.display = '';
    }

    // Event listeners para los botones de navegación
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar comportamiento de scroll
        initScrollBehavior();

        compactMedia.addEventListener('change', function() {
            showNavbar();
            if (navbar) navbar.classList.remove('show');
            if (mainElement) mainElement.classList.remove('nav-hidden');
        });
        
        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                const action = this.dataset.action;

                if (isCompactView() && navbar) {
                    navbar.classList.remove('show');
                }
                
                if (action === 'servicios') {
                    if (typeof loadPublicServices === 'function') {
                        await loadPublicServices();
                    }
                    showOnlySection('services-section');
                    
                } else if (action === 'inicio') {
                    if (typeof loadPublicServices === 'function') {
                        await loadPublicServices();
                    }
                    if (typeof loadPublicPosts === 'function') {
                        await loadPublicPosts();
                    }
                    showMultipleSections(['services-section', 'posts-section']);
                    
                } else if (action === 'publicaciones') {
                    if (typeof loadPublicPosts === 'function') {
                        await loadPublicPosts();
                    }
                    showOnlySection('posts-section');
                    
                } else {
                    showPlaceholder(action);
                }
            });
        });

        // Toggle navegación en móvil
        const navToggleBtn = document.getElementById('navHamburgerBtn') || document.getElementById('navToggleBtn');
        const topNav = document.getElementById('navegacion');
        
        if (navToggleBtn && topNav) {
            navToggleBtn.addEventListener('click', function() {
                topNav.classList.toggle('show');
                navToggleBtn.setAttribute('aria-expanded', topNav.classList.contains('show') ? 'true' : 'false');
            });
        }
    });
})();
