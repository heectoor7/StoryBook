<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/stylePrueba.css">
    <!-- Head: Título y estilos de la página -->
    <title>Inicio User</title>
    <!-- Styles para stories y UI relacionada -->
    <style>
    /* Stories styles: circular small story UI (tipo Instagram) */
    .stories-section { border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 18px; }
    .stories-list { display:flex; gap:16px; overflow-x:auto; padding:8px 4px; align-items:center; }

    .story-pill { width:86px; flex:0 0 auto; text-align:center; }
    .story-circle { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; padding:4px; box-sizing:content-box; }
    /* ring / gradient border like stories */
    .story-circle { background: linear-gradient(135deg,#f6d365,#fda085); }
    .story-inner { width:100%; height:100%; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#052023; color:#fff; font-weight:700; font-size:1.1rem; }

    .story-label { display:block; margin-top:6px; font-size:0.8rem; color:#fff; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    /* small responsive tweaks */
    @media (max-width: 480px) {
        .story-pill { width:70px; }
        .story-circle { width:60px; height:60px; }
        .story-inner { font-size:1rem; }
    }

    /* Services horizontal scroll */
    #servicesContainer {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        gap: 16px;
        padding: 12px 0;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    #servicesContainer::-webkit-scrollbar {
        height: 8px;
    }

    #servicesContainer::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
        border-radius: 4px;
    }

    #servicesContainer::-webkit-scrollbar-thumb {
        background: rgba(16,185,129,0.5);
        border-radius: 4px;
    }

    #servicesContainer::-webkit-scrollbar-thumb:hover {
        background: rgba(16,185,129,0.7);
    }

    #servicesContainer .col-md-6,
    #servicesContainer .col-lg-4 {
        flex: 0 0 320px !important;
        max-width: 320px !important;
        width: 320px !important;
    }

    #servicesContainer .card {
        height: 100%;
        min-height: 200px;
    }
    </style>
</head>
<body>
<!-- Left sidebar removed — layout uses primary header and top-nav instead --> 

    <!-- Main: Contenedor principal (header + contenido dinámico) -->
    <div class="main">

        <!-- Primary header: logo / search / profile -->
        <header class="primary-header d-flex align-items-center">
            <div class="d-flex align-items-center">
                <img src="assets/img/storybookLOGO.png" alt="Logo" style="height:160px;">
            </div>

            <div class="d-flex align-items-center">
                <input type="text" class="form-control search-input me-2" placeholder="Search service...">
            </div>

            <div>
                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#loginModal">My Profile</button>
                <button id="logoutBtn" class="btn btn-logout">Log out</button>
            </div>
        </header>

        <!-- Top navigation (ocultable) -->
        <nav class="top-nav" id="topNav">
            <button class="nav-btn me-3" data-action="inicio">Home</button>
            <button class="nav-btn me-3" data-action="publicaciones">Publications</button>
            <button class="nav-btn me-3" data-action="servicios">Services</button>
            <button class="nav-btn me-3" data-action="ayuda">Help</button>
            <button class="nav-btn" data-action="configuracion">Settings</button>
        </nav>

        <main class="content p-3">
            <!-- Section: Stories (carrusel tipo Instagram) -->
            <section class="stories-section">
                <div id="storiesContainer" class="stories-list">
                    <p class="text-muted">Loading stories...</p>
                </div>
            </section>

            <!-- Section: Services (en Inicio muestra servicios de empresas que sigues) -->
            <section class="services-section">
                <h4 class="mb-3">Services from companies you follow</h4>
                <div id="servicesContainer" class="row">
                    <p class="text-muted">Loading services...</p>
                </div>
            </section>

            <!-- Section: Reservas (mis reservas del usuario) -->
            <section class="reservas-section">
                <h4 class="mb-3">My Reservations</h4>
                <div id="reservasContainer" class="row">
                    <p class="text-muted">Loading reservations...</p>
                </div>
            </section>

            <!-- Section: Posts (muestra posts de empresas que sigues) -->
            <section class="posts-section">
                <h4 class="mb-4">Posts from companies you follow</h4>
                <div id="postsContainer" class="row">
                    <p class="text-muted">Loading posts...</p>
                </div>
            </section>

            <!-- CONTENIDO TEMPORAL PARA PRUEBAS DE SCROLL -->
            <section style="padding: 50px 0;">
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 1</h5><p>Haz scroll hacia abajo para ver el efecto de auto-hide en el nav superior.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 2</h5><p>Cuando hagas scroll hacia abajo, el nav se ocultará automáticamente.</p><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 3</h5><p>Cuando hagas scroll hacia arriba, el nav reaparecerá.</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 4</h5><p>También puedes mover el ratón a la parte superior de la pantalla para mostrar el nav.</p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 5</h5><p>Este contenido asegura que la página sea lo suficientemente larga.</p><p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 6</h5><p>Más contenido para hacer scroll...</p><p>Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 7</h5><p>Continúa haciendo scroll...</p><p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 8</h5><p>Ya casi...</p><p>Sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 9</h5><p>Un poco más...</p><p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido de prueba 10</h5><p>¡Final del contenido de prueba!</p><p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido extra 11</h5><p>Asegurando scroll suficiente...</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></div></div>
                <div class="card mb-4" style="min-height: 200px;"><div class="card-body"><h5>Contenido extra 12</h5><p>Más contenido para garantizar scroll...</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse.</p></div></div>
            </section>
        </main>
    </div>

<!-- Bootstrap bundle (asegurar disponible para modal) -->
<script src="js/bootstrap.bundle.js"></script>

<!-- Stories modal (visualizador de stories) -->
<div class="modal fade" id="storiesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content bg-dark text-white">
      <div class="modal-body text-center">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <small id="storyCompany" class="fw-bold"></small>
          <small><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></small>
        </div>
        <div id="storyContent" style="max-height:70vh; overflow:auto; text-align:left;">
          <!-- El contenido de la story se insertará aquí -->
        </div>
        <div class="mt-2 text-start"><small id="storyMeta" class="text-muted"></small></div>
        <div class="progress mt-2" style="height:4px;">
          <div id="storyProgress" class="progress-bar" role="progressbar" style="width:0%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="site-footer">
  <div class="container">
    <img src="assets/img/storybookLOGO.png" alt="Logo" style="height:160px;">
    <div class="footer-links">
      <a href="#" data-action="inicio">Home</a>
      <a href="#" data-action="publicaciones">Publications</a>
      <a href="#" data-action="servicios">Services</a>
      <a href="#" data-action="ayuda">Help</a>
      <a href="#" data-action="configuracion">Settings</a>
    </div>
    <small>© 2026 StoryBook. All rights reserved.</small>
  </div>
</footer>

<!-- Scripts externos modulares -->
<script src="js/user/user-auth.js"></script>
<script src="js/user/user-content.js"></script>
<script src="js/user/stories-modal.js"></script>
<script src="js/user/user-navigation.js"></script>

</body>
</html>