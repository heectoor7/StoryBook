/**
 * user-auth.js
 * Maneja la autenticación y verificación del usuario
 */

// Comprobar autenticación y mostrar información del usuario
document.addEventListener('DOMContentLoaded', async function() {
    const h3 = document.getElementById('userTitle');
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        if (h3) h3.innerText += ' (no autenticado)';
        return;
    }
    
    try {
        const res = await fetch('/api/me', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            if (h3) h3.innerText += ' (no autenticado)';
            return;
        }
        
        const user = await res.json();
        if (h3) {
            h3.innerText += user && user.id ? ' ' + user.id : ' (sin id)';
        }
    } catch (e) {
        console.error(e);
        if (h3) h3.innerText += ' (error)';
    }
});
