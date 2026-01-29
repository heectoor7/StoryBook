<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Inicio User</title>
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
    </style>
</head>
<body>
<!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand text-center mb-3">
            <img src="assets/img/storybookLOGO.png" alt="Logo" style="max-width:160px;">
        </div>

        <nav class="sidebar-nav">
            <button class="btn sidebar-btn">Inicio</button>
            <div class="sidebar-links mt-3">
                <button class="sidebar-item">Reservas</button>
                <button class="sidebar-item">Servicios</button>
                <button class="sidebar-item">Ayuda</button>
                <button class="sidebar-item">Configuración</button>
            </div>
        </nav>
    </aside> 

    <!-- Main -->
    <div class="main">

        <header class="header d-flex justify-content-between align-items-center p-3">
            <h3 class="m-0">StoryBook</h3><p id="userTitle">Página del usuario</p>

            <div class="d-flex align-items-center">

                <input type="text" class="form-control me-2" placeholder="Buscar...">
                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal"
                    data-bs-target="#loginModal">Mi perfil</button>

            </div>
        </header>

        <main class="content p-3">
            <section class="stories-section">
                <h5 class="mb-2">Stories</h5>
                <div id="storiesContainer" class="stories-list">
                    <p class="text-muted">Cargando stories...</p>
                </div>
            </section>

            <section class="services-section">
                <h4 class="mb-3">Servicios de empresas que sigues</h4>
                <div id="servicesContainer" class="row">
                    <p class="text-muted">Cargando servicios...</p>
                </div>
            </section>

            <section class="posts-section">
                <h4 class="mb-4">Posts de empresas que sigues</h4>
                <div id="postsContainer" class="row">
                    <p class="text-muted">Cargando posts...</p>
                </div>
            </section>
        </main>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', async function() {
        const h3 = document.getElementById('userTitle');
        const token = localStorage.getItem('auth_token');
        if (!token) { h3.innerText += ' (no autenticado)'; return; }
        try {
            const res = await fetch('/api/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) { h3.innerText += ' (no autenticado)'; return; }
            const user = await res.json();
            h3.innerText += user && user.id ? ' ' + user.id : ' (sin id)';
        } catch (e) {
            console.error(e);
            h3.innerText += ' (error)';
        }
    });
    </script>

    <script>
            document.addEventListener('DOMContentLoaded', async function() {
            const token = localStorage.getItem('auth_token');
            if (!token) return;

            try {
                const res = await fetch('/api/user/followed-companies-posts', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Error al cargar posts');
                
                const posts = await res.json();
                const storiesContainer = document.getElementById('storiesContainer');
                const container = document.getElementById('postsContainer');

                const stories = posts.filter(p => p.is_story);
                const regular = posts.filter(p => !p.is_story);

                // Render stories (small, horizontal)
                if (stories.length === 0) {
                    storiesContainer.innerHTML = '<p class="text-muted">No hay stories</p>';
                } else {
                    // Guardar stories globalmente para el modal
                    window.storiesData = stories;
                    storiesContainer.innerHTML = stories.map((s, i) => `
                        <div class="story-pill">
                            <div class="story-circle" role="button" onclick="openStoriesModal(${i})" title="${s.company_name}">
                                <div class="story-inner">${(s.company_name||'').charAt(0).toUpperCase()}</div>
                            </div>
                            <small class="story-label">${s.company_name}</small>
                        </div>
                    `).join('');
                }

                // Render regular posts (grid)
                if (regular.length === 0) {
                    container.innerHTML = '<p class="text-muted">No hay posts disponibles</p>';
                    return;
                }

                container.innerHTML = regular.map(post => `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">${post.company_name}</h5>
                            </div>
                            <p class="card-text flex-grow-1">${post.content}</p>
                            <div class="mt-2">
                                <small class="text-muted d-block">Publicado: ${new Date(post.created_at).toLocaleDateString()}</small>
                            </div>
                        </div>
                    </div>
                </div>
                `).join('');

                // Cargar servicios de las empresas seguidas
                try {
                    const sres = await fetch('/api/user/followed-companies-services', {
                        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                    });
                    const servicesContainer = document.getElementById('servicesContainer');
                    if (!sres.ok) {
                        servicesContainer.innerHTML = '<p class="text-muted">Error al cargar servicios</p>';
                    } else {
                        const services = await sres.json();
                        if (!services.length) {
                            servicesContainer.innerHTML = '<p class="text-muted">No hay servicios</p>';
                        } else {
                            servicesContainer.innerHTML = services.map(s => `
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title mb-0">${s.name}</h5>
                                                <small class="text-muted">${s.category ?? ''}</small>
                                            </div>
                                            <p class="card-text text-truncate">${s.description}</p>
                                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                                <small class="text-muted">${s.company_name ?? ''}</small>
                                                <strong>${s.price ? '$' + parseFloat(s.price).toFixed(2) : ''}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        }
                    }
                } catch (err) {
                    console.error(err);
                    const servicesContainer = document.getElementById('servicesContainer');
                    servicesContainer.innerHTML = '<p class="text-danger">Error al cargar servicios</p>';
                }
            } catch (e) {
                console.error(e);
                document.getElementById('postsContainer').innerHTML = '<p class="text-danger">Error al cargar posts</p>';
            }
            });
        </script>

<!-- Bootstrap bundle (asegurar disponible para modal) -->
<script src="js/bootstrap.bundle.js"></script>

<!-- Stories modal -->
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

<script>
(function(){
  const STORY_DURATION = 5000; // 5 segundos
  let storyModalEl = document.getElementById('storiesModal');
  let storyModal = null;
  if (storyModalEl && window.bootstrap) storyModal = new bootstrap.Modal(storyModalEl);

  let currentStoryIndex = 0;
  let storyTimer = null;
  let progressInterval = null;

  function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
  }

  window.openStoriesModal = function(index) {
    if (!window.storiesData || window.storiesData.length === 0) return;
    currentStoryIndex = index;
    showStory(currentStoryIndex);
    if (storyModal) storyModal.show();
  }

  function showStory(idx) {
    clearTimers();
    const s = window.storiesData[idx];
    const companyEl = document.getElementById('storyCompany');
    const contentEl = document.getElementById('storyContent');
    const metaEl = document.getElementById('storyMeta');
    const progress = document.getElementById('storyProgress');

    companyEl.innerText = s.company_name || '';
    // Insertar contenido formateado, preservando saltos de línea
    const safe = escapeHtml(s.content || '');
    contentEl.innerHTML = '<div style="padding:12px; font-size:1.25rem; line-height:1.4;">' + safe.replace(/\n/g, '<br>') + '</div>';
    metaEl.innerText = s.expires_at ? 'Expira: ' + new Date(s.expires_at).toLocaleString() : '';
    progress.style.width = '0%';

    const start = Date.now();
    progressInterval = setInterval(() => {
      const pct = Math.min(100, (Date.now() - start) / STORY_DURATION * 100);
      progress.style.width = pct + '%';
    }, 100);

    storyTimer = setTimeout(() => {
      nextStory();
    }, STORY_DURATION);
  }

  function nextStory() {
    if (!window.storiesData) return;
    if (currentStoryIndex + 1 >= window.storiesData.length) {
      // cerrar modal al terminar
      if (storyModal) storyModal.hide();
    } else {
      currentStoryIndex++;
      showStory(currentStoryIndex);
    }
  }

  function prevStory() {
    if (!window.storiesData) return;
    if (currentStoryIndex - 1 < 0) return;
    currentStoryIndex--;
    showStory(currentStoryIndex);
  }

  function clearTimers() {
    if (storyTimer) { clearTimeout(storyTimer); storyTimer = null; }
    if (progressInterval) { clearInterval(progressInterval); progressInterval = null; }
  }

  if (storyModalEl) {
    storyModalEl.addEventListener('hidden.bs.modal', clearTimers);
    // avanzar al click en la mitad derecha, retroceder en la mitad izquierda
    storyModalEl.addEventListener('click', function(e) {
      const rect = e.currentTarget.getBoundingClientRect();
      const x = e.clientX - rect.left;
      if (x > rect.width / 2) nextStory(); else prevStory();
    });
  }
})();
</script>
</body>
</html>