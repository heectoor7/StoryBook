<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- User CSS files -->
    <link rel="stylesheet" href="css/user/base.css">
    <link rel="stylesheet" href="css/user/header.css">
    <link rel="stylesheet" href="css/user/layout.css">
    <link rel="stylesheet" href="css/user/sections.css">
    <link rel="stylesheet" href="css/user/stories.css">
    <link rel="stylesheet" href="css/user/components.css">
    <link rel="stylesheet" href="css/user/footer.css">
    <link rel="stylesheet" href="css/user/responsive.css">
    <title>StoryBook</title>
</head>
<body>
<!-- Left sidebar removed — layout uses primary header and top-nav instead --> 

    <!-- Main: Contenedor principal (header + contenido dinámico) -->
    <div class="main">

        <!-- Primary header: logo / search / profile -->
        <header class="primary-header d-flex align-items-center">
            <div class="d-flex align-items-center">
                <img src="assets/img/storybookLOGO.png" alt="Logo">
            </div>

            <div class="d-flex align-items-center">
                <input type="text" id="searchInput" class="form-control search-input me-2" placeholder="Search service...">
            </div>

            <div>
                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#loginModal">My Profile</button>
                <button id="logoutBtn" class="btn btn-logout">Log out</button>
            </div>
        </header>

        <!-- Top navigation (ocultable) -->
        <nav class="top-nav" id="navegacion">
            <button class="nav-btn me-3" data-action="inicio">Home</button>
            <button class="nav-btn me-3" data-action="publicaciones">Publications</button>
            <button class="nav-btn me-3" data-action="servicios">Services</button>
            <button class="nav-btn me-3" data-action="ayuda">Help</button>
            <button class="nav-btn" data-action="configuracion">Settings</button>
        </nav>
        
        <!-- Indicador de barra oculta -->
        <div id="barra_indicador"></div>

        <main class="content p-3">
            <!-- Section: Stories -->
            <section class="stories-section">
                <div class="stories-carousel-container">
                    <button id="storiesScrollLeft" class="stories-scroll-btn stories-scroll-left">
                        ‹
                    </button>
                    <div id="storiesContainer" class="stories-list">
                        <p style="color: var(--text-secondary);">Loading stories...</p>
                    </div>
                    <button id="storiesScrollRight" class="stories-scroll-btn stories-scroll-right">
                        ›
                    </button>
                </div>
            </section>

            <!-- Section: Reservas (mis reservas del usuario) -->
            <section class="reservas-section">
                <h4 class="mb-3">My Reservations</h4>
                <div id="reservasContainer" class="row">
                    <p style="color: var(--text-secondary);">Loading reservations...</p>
                </div>
            </section>

            <!-- Section: Services (en Inicio muestra servicios de empresas que sigues) -->
            <section class="services-section">
                <h4 class="mb-3">Services from companies you follow</h4>
                <div id="servicesContainer" class="row">
                    <p style="color: var(--text-secondary);">Loading services...</p>
                </div>
            </section>

            <!-- Section: Posts (muestra posts de empresas que sigues) -->
            <section class="posts-section">
                <h4 class="mb-4">Posts from companies you follow</h4>
                <div id="postsContainer" class="row">
                    <p style="color: var(--text-secondary);">Loading posts...</p>
                </div>
            </section>
        </main>
    </div>

<!-- Bootstrap bundle (asegurar disponible para modal) -->
<script src="js/bootstrap.bundle.js"></script>

<!-- Stories modal (visualizador de stories) -->
<div class="modal fade" id="storiesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 10rem;">
    <div class="modal-content bg-dark text-white">
      <div class="modal-body text-center">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <small id="storyCompany" class="fw-bold"></small>
          <small><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></small>
        </div>
        <div id="storyContent">
          <!-- El contenido de la story se insertará aquí -->
        </div>
        <div class="mt-2 text-start"><small id="storyMeta" style="color: var(--text-secondary);"></small></div>
        <div class="progress mt-2 story-progress">
          <div id="storyProgress" class="progress-bar" role="progressbar"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de gestión de reserva -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 10rem;">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">Manage Reservation</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="bookingModalContent">
          <div class="mb-3">
            <label class="form-label"><strong>Service:</strong></label>
            <p id="modalServiceName" style="color: var(--text-secondary);"></p>
          </div>
          <div class="mb-3">
            <label class="form-label"><strong>Company:</strong></label>
            <p id="modalCompanyName" style="color: var(--text-secondary);"></p>
          </div>
          <div class="mb-3">
            <label for="modalDateInput" class="form-label"><strong>Date:</strong></label>
            <input type="date" class="form-control" id="modalDateInput">
          </div>
          <div class="mb-3">
            <label for="modalTimeInput" class="form-label"><strong>Time:</strong></label>
            <input type="time" class="form-control" id="modalTimeInput">
          </div>
          <div class="mb-3">
            <label class="form-label"><strong>Status:</strong></label>
            <p><span id="modalStatus" class="badge bg-secondary"></span></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="deleteBookingBtn">Delete</button>
        <button type="button" class="btn btn-primary" id="updateBookingBtn">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para nueva reserva -->
<div class="modal fade" id="newBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 10rem;">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">New Booking</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="newBookingModalContent">
          <div class="mb-3">
            <label class="form-label"><strong>Service:</strong></label>
            <p id="newModalServiceName" style="color: var(--text-secondary);"></p>
          </div>
          <div class="mb-3">
            <label class="form-label"><strong>Company:</strong></label>
            <p id="newModalCompanyName" style="color: var(--text-secondary);"></p>
          </div>
          <div class="mb-3">
            <label for="newModalDateInput" class="form-label"><strong>Date:</strong></label>
            <input type="date" class="form-control" id="newModalDateInput">
          </div>
          <div class="mb-3">
            <label for="newModalTimeInput" class="form-label"><strong>Time:</strong></label>
            <input type="time" class="form-control" id="newModalTimeInput">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="createBookingBtn">Confirm Booking</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ver publicación completa con comentarios -->
<div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 10rem;">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="postModalCompany"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="postModalImageContainer">
          <img id="postModalImage" class="img-fluid rounded mb-3" alt="Post image" style="max-height:250px;width:100%;object-fit:cover;">
        </div>
        <p id="postModalContent" class="mb-3"></p>
        <small style="color: var(--text-secondary);" class="d-block mb-3">Published: <span id="postModalDate"></span></small>
        
        <hr style="border-color:var(--border);">
        
        <h6 class="mb-3">Comments</h6>
        <div id="postCommentsList" style="max-height:200px;overflow-y:auto;margin-bottom:16px;">
          <p style="color: var(--text-secondary);">Loading comments...</p>
        </div>
        
        <div class="input-group">
          <input type="text" class="form-control" id="postCommentInput" placeholder="Write a comment..." style="background:var(--bg-card);border-color:var(--border);color:var(--text-primary);">
          <button class="btn btn-primary" onclick="addCommentToPost()">Send</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ver servicio completo con valoraciones -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 10rem;">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="serviceModalName"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="serviceModalImageContainer">
          <img id="serviceModalImage" class="img-fluid rounded mb-3" alt="Service image" style="max-height:200px;width:100%;object-fit:cover;">
        </div>
        <div class="mb-3">
          <strong>Company:</strong> <span id="serviceModalCompany" style="color: var(--text-secondary);"></span>
        </div>
        <div class="mb-3">
          <strong>Category:</strong> <span id="serviceModalCategory" style="color: var(--text-secondary);"></span>
        </div>
        <p id="serviceModalDescription" class="mb-3"></p>
        <div class="mb-3">
          <strong>Price:</strong> <span id="serviceModalPrice" class="text-success"></span>
        </div>
        
        <div class="mt-3">
          <button class="btn btn-primary w-100" id="serviceModalBookBtn">Book This Service</button>
        </div>
        
        <hr style="border-color:var(--border);" class="my-4">
        
        <h6 class="mb-3">Ratings & Reviews</h6>
        <div id="serviceRatingsList" style="max-height:200px;overflow-y:auto;margin-bottom:16px;">
          <p style="color: var(--text-secondary);">Loading ratings...</p>
        </div>
        
        <div class="mt-3">
          <h6 class="mb-2">Add Your Rating</h6>
          <div class="mb-3">
            <label class="form-label">Rating (1-5 stars):</label>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-warning btn-sm rating-star" data-rating="1">★</button>
              <button class="btn btn-outline-warning btn-sm rating-star" data-rating="2">★★</button>
              <button class="btn btn-outline-warning btn-sm rating-star" data-rating="3">★★★</button>
              <button class="btn btn-outline-warning btn-sm rating-star" data-rating="4">★★★★</button>
              <button class="btn btn-outline-warning btn-sm rating-star" data-rating="5">★★★★★</button>
            </div>
          </div>
          <div class="mb-3">
            <label for="serviceRatingComment" class="form-label">Comment (optional):</label>
            <textarea class="form-control" id="serviceRatingComment" rows="2" placeholder="Share your experience..." style="background:var(--bg-card);border-color:var(--border);color:var(--text-primary);"></textarea>
          </div>
          <button class="btn btn-success" onclick="addRatingToService()">Submit Rating</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="site-footer">
  <div class="container">
    <img src="assets/img/storybookLOGO.png" alt="Logo">
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