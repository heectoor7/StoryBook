<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Inicio User</title>
    <style>
    /* Stories styles: horizontal small cards */
    .stories-section { border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 18px; }
    .stories-list { display:flex; gap:12px; overflow-x:auto; padding:8px 0; }
    .story-card { min-width:140px; max-width:160px; height:120px; flex:0 0 auto; }
    .story-card .card-body { padding:8px; }
    .story-card .card-title { font-size:0.95rem; }
    .story-card .card-text { font-size:0.85rem; max-height:48px; overflow:hidden; }
    </style>
</head>
<body>
<!-- Sidebar -->
    <aside class="sidebar">
        <img src="assets/img/storybookLOGO.png" alt="Logo" style="width: 200px;">
        <h3>Made in</h3>
        <ul>
            <li>Héctor</li>
            <li>Julián</li>
            <li>Hugo</li>
        </ul>
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
                    storiesContainer.innerHTML = stories.map(s => `
                        <div class="card story-card">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="card-title mb-0">${s.company_name}</h6>
                                </div>
                                <p class="card-text text-truncate">${s.content}</p>
                                ${s.expires_at ? '<small class="text-muted">Expira: ' + new Date(s.expires_at).toLocaleString() + '</small>' : ''}
                            </div>
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
            } catch (e) {
                console.error(e);
                document.getElementById('postsContainer').innerHTML = '<p class="text-danger">Error al cargar posts</p>';
            }
            });
        </script>
</body>
</html>