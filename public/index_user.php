<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3 id="userTitle">Página del usuario</h3>

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