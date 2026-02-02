<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoryBook - Panel de Empresa</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-primary, #f8f9fa);
        }

        .sidebar {
            width: 250px;
            background-color: var(--bg-secondary, #ffffff);
            border-right: 1px solid var(--border-color, #dee2e6);
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar img {
            display: block;
            margin: 0 auto 20px;
        }

        .sidebar h3 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color, #0d6efd);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text-primary, #212529);
        }

        .sidebar ul li:hover {
            background-color: var(--primary-light, #e7f1ff);
            color: var(--primary-color, #0d6efd);
        }

        .main-container {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: var(--bg-secondary, #ffffff);
            border-bottom: 1px solid var(--border-color, #dee2e6);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 30px;
            flex: 1;
        }

        .bookings-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-container {
                margin-left: 200px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <img id="sidebarLogo" src="assets/img/storybookLOGO.png" alt="Logo" style="width: 180px;">
        <h3 id="sidebarTitle">Mi Empresa</h3>
        <ul id="sidebarMenu">
            <li id="menuStats">📊 Estadísticas</li>
            <li id="menuPosts">📝 Publicaciones</li>
            <li id="menuServices">🛠️ Servicios</li>
            <li id="menuBookings">📅 Agenda</li>
            <li id="menuFollowers">👥 Seguidores</li>
            <li id="menuSettings">⚙️ Configuración</li>
        </ul>
    </aside>

    <!-- Main Container -->
    <div class="main-container" id="mainContainer">

        <!-- Header -->
        <header class="header" id="mainHeader">
            <h4 class="m-0" id="pageTitle">Estadísticas</h4>

            <div class="d-flex align-items-center gap-2" id="headerActions">
                <button class="btn btn-outline-danger" id="logoutBtn">
                    Cerrar Sesión
                </button>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="content" id="mainContent">

            <!-- Sección: Estadísticas -->
            <section id="statsSection">
                <div class="section-header">
                    <h5>Dashboard</h5>
                </div>
                <div id="companyStatsContainer">
                    <p style="color: var(--text-secondary);">Cargando estadísticas...</p>
                </div>
            </section>

            <!-- Sección: Publicaciones -->
            <section id="postsSection" style="display: none;">
                <div class="section-header">
                    <h5>Mis Publicaciones</h5>
                    <button class="btn btn-primary" id="btnNewPost">
                        + Nueva Publicación
                    </button>
                </div>
                <div class="row" id="companyPostsContainer">
                    <p style="color: var(--text-secondary);">Cargando publicaciones...</p>
                </div>
            </section>

            <!-- Sección: Servicios -->
            <section id="servicesSection" style="display: none;">
                <div class="section-header">
                    <h5>Mis Servicios</h5>
                    <button class="btn btn-primary" id="btnNewService">
                        + Nuevo Servicio
                    </button>
                </div>
                <div class="row" id="companyServicesContainer">
                    <p style="color: var(--text-secondary);">Cargando servicios...</p>
                </div>
            </section>

            <!-- Sección: Agenda de Reservas -->
            <section id="bookingsSection" style="display: none;">
                <div class="section-header">
                    <h5>Agenda de Reservas</h5>
                </div>
                <div id="companyBookingsContainer">
                    <p style="color: var(--text-secondary);">Cargando reservas...</p>
                </div>
            </section>

            <!-- Sección: Seguidores -->
            <section id="followersSection" style="display: none;">
                <div class="section-header">
                    <h5>Mis Seguidores</h5>
                </div>
                <div id="companyFollowersContainer">
                    <p style="color: var(--text-secondary);">Esta funcionalidad estará disponible próximamente.</p>
                </div>
            </section>

            <!-- Sección: Configuración -->
            <section id="settingsSection" style="display: none;">
                <div class="section-header">
                    <h5>Configuración</h5>
                </div>
                <div id="companySettingsContainer">
                    <p style="color: var(--text-secondary);">Panel de configuración en desarrollo.</p>
                </div>
            </section>

        </main>

    </div>

    <!-- Modal: Nueva Publicación -->
    <div class="modal fade" id="newPostModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Publicación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newPostContent" class="form-label">Contenido</label>
                        <textarea class="form-control" id="newPostContent" rows="4" placeholder="Escribe algo..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="newPostImage" class="form-label">Imagen (opcional)</label>
                        <input type="file" class="form-control" id="newPostImage" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" id="btnCreateStory">Publicar como Story</button>
                    <button type="button" class="btn btn-primary" id="btnCreatePost">Publicar como Post</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Servicio -->
    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalTitle">Crear Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="serviceName" class="form-label">Nombre del servicio *</label>
                        <input type="text" class="form-control" id="serviceName" required>
                    </div>
                    <div class="mb-3">
                        <label for="serviceDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="serviceDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="servicePrice" class="form-label">Precio *</label>
                        <input type="number" step="0.01" class="form-control" id="servicePrice" required>
                    </div>
                    <div class="mb-3">
                        <label for="serviceCategory" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="serviceCategory">
                    </div>
                    <div class="mb-3">
                        <label for="serviceImage" class="form-label">Imagen (opcional)</label>
                        <input type="file" class="form-control" id="serviceImage" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSaveService">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/bootstrap.bundle.js"></script>
    <script src="js/company/company-content.js"></script>
    <script src="js/company/company-navigation.js"></script>
</body>

</html>
