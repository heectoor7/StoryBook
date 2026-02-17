/**
 * company-navigation.js
 * Manejo de navegación y eventos de la interfaz de empresa
 */

(function(){
    // Variables globales
    const sidebar = document.getElementById('sidebar');
    const compactMedia = window.matchMedia('(max-width: 992px)');

    function isCompactView() {
        return compactMedia.matches;
    }

    function closeSidebarOnCompact() {
        if (isCompactView() && sidebar) {
            sidebar.classList.remove('open');
            const toggle = document.getElementById('companyMenuToggle');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        }
    }
    
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
        const menuToggle = document.getElementById('companyMenuToggle');
        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function() {
                const willOpen = !sidebar.classList.contains('open');
                sidebar.classList.toggle('open');
                menuToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        compactMedia.addEventListener('change', function() {
            if (!isCompactView() && sidebar) {
                sidebar.classList.remove('open');
            }
            if (menuToggle) {
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('click', function(e) {
            if (!isCompactView() || !sidebar || !menuToggle) return;
            const clickedInsideSidebar = sidebar.contains(e.target);
            const clickedToggle = menuToggle.contains(e.target);
            if (!clickedInsideSidebar && !clickedToggle) {
                sidebar.classList.remove('open');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        
        // Menú: Publicaciones
        const menuPosts = document.getElementById('menuPosts');
        if (menuPosts) {
            menuPosts.addEventListener('click', async function() {
                closeSidebarOnCompact();
                showOnlySection('postsSection');
                updatePageTitle('My Posts');
                if (typeof loadCompanyPosts === 'function') {
                    await loadCompanyPosts();
                }
            });
        }

        // Menú: Gestión de servicios
        const menuServices = document.getElementById('menuServices');
        if (menuServices) {
            menuServices.addEventListener('click', async function() {
                closeSidebarOnCompact();
                showOnlySection('servicesSection');
                updatePageTitle('My Services');
                if (typeof loadCompanyServices === 'function') {
                    await loadCompanyServices();
                }
            });
        }

        // Menú: Agenda de reservas
        const menuBookings = document.getElementById('menuBookings');
        if (menuBookings) {
            menuBookings.addEventListener('click', async function() {
                closeSidebarOnCompact();
                showOnlySection('bookingsSection');
                updatePageTitle('Reservations Schedule');
                if (typeof loadCompanyBookings === 'function') {
                    await loadCompanyBookings();
                }
            });
        }
        const menuFollowers = document.getElementById('menuFollowers');
        if (menuFollowers) {
            menuFollowers.addEventListener('click', async function() {
                closeSidebarOnCompact();
                showOnlySection('followersSection');
                updatePageTitle('My Followers');
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
        updatePageTitle('My Posts');
        if (typeof loadCompanyPosts === 'function') {
            loadCompanyPosts();
        }

        // Botón My Profile -> cargar perfil dentro de <main> vía AJAX
        const btnProfile = document.getElementById('btnProfile');
        if (btnProfile) {
            btnProfile.addEventListener('click', async function() {
                await loadCompanyProfile();
            });
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
                sidebarTitle.textContent = user.company.name || 'My Company';
            }
        } catch (err) {
            console.error('[debug] loadCompanyInfo exception', err);
        }
    }

    // Cargar y renderizar perfil completo dentro del main (posts, servicios, reservas, datos)
    async function loadCompanyProfile() {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        const main = document.getElementById('mainContent');
        if (!main) return;

        // Mostrar estado de carga inmediato
        main.innerHTML = `<section class="section-header"><h5>Loading profile...</h5></section>`;

        try {
            const res = await fetch('/api/company/profile', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });

            if (!res.ok) {
                main.innerHTML = `<p style="color: var(--danger);">Profile could not be loaded.</p>`;
                return;
            }

            const data = await res.json();
            const company = data.company || data;

            // Render principal: info + secciones reutilizando los ids existentes
            main.innerHTML = `
                <section id="profileOverview">
                    <div class="section-header">
                        <h5>Company Profile</h5>
                        <div>
                            <button id="btnEditProfile" class="btn btn-outline-primary me-2">Edit Profile</button>
                        </div>
                    </div>
                    <div class="card" style="max-width:900px;">
                        <div class="card-body d-flex gap-4">
                            <div>
                                ${company.logo ? `<img src="${company.logo}" alt="Logo" style="max-width:160px; border-radius:8px;">` : ''}
                            </div>
                            <div>
                                <h4 style="margin:0 0 6px 0;">${company.name || 'No name'}</h4>
                                <p style="margin:0; color:var(--text-secondary);">${company.description || ''}</p>
                                <div style="margin-top:8px;">
                                    <span class="badge bg-primary">${company.city || ''}</span>
                                    <span class="badge" style="background:var(--bg-card); color:var(--text-secondary);">${company.address || ''}</span>
                                </div>
                                <div style="margin-top:8px; color:var(--text-secondary);">Email: ${data.email || company.email || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="postsSectionProfile" style="margin-top:20px;">
                    <div class="section-header"><h5>Posts</h5></div>
                    <div class="row" id="companyPostsContainer">
                        <p style="color: var(--text-secondary);">Loading posts...</p>
                    </div>
                </section>

                <section id="servicesSectionProfile" style="margin-top:20px;">
                    <div class="section-header"><h5>Services</h5></div>
                    <div class="row" id="companyServicesContainer">
                        <p style="color: var(--text-secondary);">Loading services...</p>
                    </div>
                </section>

                <section id="bookingsSectionProfile" style="margin-top:20px;">
                    <div class="section-header"><h5>Bookings</h5></div>
                    <div id="companyBookingsContainer">
                        <p style="color: var(--text-secondary);">Loading bookings...</p>
                    </div>
                </section>
            `;

            // Llamar a los loaders si están disponibles (reutilizan los mismos contenedores)
            if (typeof loadCompanyPosts === 'function') {
                await loadCompanyPosts();
            }
            if (typeof loadCompanyServices === 'function') {
                await loadCompanyServices();
            }
            if (typeof loadCompanyBookings === 'function') {
                await loadCompanyBookings();
            }

            // Agregar listener al botón Editar Perfil
            const btnEditProfile = document.getElementById('btnEditProfile');
            if (btnEditProfile) {
                btnEditProfile.addEventListener('click', async function() {
                    await loadEditProfileForm(company, data.email);
                });
            }

        } catch (e) {
            console.error('Error loading profile via AJAX', e);
            main.innerHTML = `<p style="color: var(--danger);">Error loading profile.</p>`;
        }
    }

    // Cargar formulario de edición de perfil
    async function loadEditProfileForm(company, email) {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        const main = document.getElementById('mainContent');
        if (!main) return;

        main.innerHTML = `
            <section id="editProfileSection">
                <div class="section-header">
                    <h5>Edit Company Profile</h5>
                </div>
                <form id="editProfileForm" class="edit-form" enctype="multipart/form-data" style="max-width:600px;">
                    <div class="form-group mb-3">
                        <label for="editLogo" class="form-label">Profile Photo:</label>
                        <div class="profile-image-container mb-3">
                            <img id="editProfileImagePreview" class="profile-image-preview" src="${company.logo || ''}" alt="Photo preview" style="max-width: 150px; max-height: 150px; border-radius: 8px; display: ${company.logo ? 'block' : 'none'};">
                        </div>
                        <input type="file" id="editLogo" name="logo" accept="image/*" class="form-control">
                        <small class="form-text" style="color: var(--text-secondary);">Formats: JPG, PNG, GIF (max. 5MB)</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="editName" class="form-label">Company Name:</label>
                        <input type="text" id="editName" name="name" required class="form-control" value="${company.name || ''}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="editEmail" class="form-label">Email:</label>
                        <input type="email" id="editEmail" name="email" required class="form-control" value="${email || ''}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="editPhone" class="form-label">Phone:</label>
                        <input type="tel" id="editPhone" name="phone" class="form-control" value="${company.phone || ''}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="editDescription" class="form-label">Description:</label>
                        <textarea id="editDescription" name="description" rows="4" class="form-control">${company.description || ''}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="editAddress" class="form-label">Address:</label>
                        <input type="text" id="editAddress" name="address" class="form-control" value="${company.address || ''}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="editCity" class="form-label">City:</label>
                        <input type="text" id="editCity" name="city" class="form-control" value="${company.city || ''}">
                    </div>

                    <div class="form-actions d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" id="cancelEditBtn" class="btn btn-secondary">Cancel</button>
                    </div>

                    <div id="editMessage" class="message mt-3"></div>
                </form>
            </section>
        `;

        // Preview de imagen
        const editLogo = document.getElementById('editLogo');
        if (editLogo) {
            editLogo.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        showEditMessage('Image must not exceed 5MB', 'error');
                        this.value = '';
                        return;
                    }
                    if (!file.type.startsWith('image/')) {
                        showEditMessage('Please select a valid image file', 'error');
                        this.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('editProfileImagePreview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Submit del formulario
        const editForm = document.getElementById('editProfileForm');
        if (editForm) {
            editForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('name', document.getElementById('editName').value);
                formData.append('email', document.getElementById('editEmail').value);
                formData.append('phone', document.getElementById('editPhone').value);
                formData.append('description', document.getElementById('editDescription').value);
                formData.append('address', document.getElementById('editAddress').value);
                formData.append('city', document.getElementById('editCity').value);
                
                const fileInput = document.getElementById('editLogo');
                if (fileInput.files.length > 0) {
                    formData.append('logo', fileInput.files[0]);
                }

                try {
                    const res = await fetch('/api/company/profile', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                        body: formData
                    });

                    if (!res.ok) {
                        const errorData = await res.json();
                        throw new Error(errorData.message || 'Error saving');
                    }

                    showEditMessage('Profile updated successfully', 'success');
                    setTimeout(async () => {
                        await loadCompanyProfile();
                    }, 1500);
                } catch (e) {
                    console.error(e);
                    showEditMessage('Error saving changes: ' + e.message, 'error');
                }
            });
        }

        // Botón Cancelar
        const cancelBtn = document.getElementById('cancelEditBtn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', async function() {
                await loadCompanyProfile();
            });
        }
    }

    function showEditMessage(text, type) {
        const messageDiv = document.getElementById('editMessage');
        if (messageDiv) {
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type;
        }
    }
})();
