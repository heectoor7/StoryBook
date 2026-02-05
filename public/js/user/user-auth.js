/**
 * user-auth.js
 * Maneja la autenticación y verificación del usuario
 */

// Comprobar autenticación y mostrar información del usuario
document.addEventListener('DOMContentLoaded', async function() {
    const h3 = document.getElementById('userTitle');
    const userNameDisplay = document.getElementById('userNameDisplay');
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        if (h3) h3.innerText += ' (no autenticado)';
        if (userNameDisplay) userNameDisplay.innerText = 'Guest';
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
            if (userNameDisplay) userNameDisplay.innerText = 'Guest';
            return;
        }
        
        const user = await res.json();
        if (h3) {
            h3.innerText += user && user.id ? ' ' + user.id : ' (sin id)';
        }
        if (userNameDisplay) {
            userNameDisplay.innerText = user && user.name ? user.name : 'User';
        }
    } catch (e) {
        console.error(e);
        if (h3) h3.innerText += ' (error)';
        if (userNameDisplay) userNameDisplay.innerText = 'Guest';
    }
});
