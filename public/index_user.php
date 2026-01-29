<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Inicio User</title>
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

        <!-- Contenido principal -->
        <main class="content p-3">
            <p>Contenido principal de la página</p>
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
</body>
</html>