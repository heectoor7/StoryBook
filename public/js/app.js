/* ===============================================
    REGISTER
================================================ */
document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formUsuario");
    const resultado = document.getElementById("resultado");
    const modalEl = document.getElementById("registerModal");
    const bootstrapModal = new bootstrap.Modal(modalEl);

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        resultado.textContent = "Creando usuario...";

        try {
            const formData = new FormData(form);
            const res = await fetch('../api/users/registrar_usuario.php', {
                method: 'POST',
                body: formData
            });

            if (!res.ok) throw new Error("Error en la petición");

            const data = await res.json();

            if (data.success) {
                resultado.textContent = "Usuario creado ✅";
                form.reset();

                // Espera un segundo, cierra modal y recarga la página
                setTimeout(() => {
                    bootstrapModal.hide();
                    window.location.reload(); // recarga para mostrar header con sesión
                }, 1000);

            } else {
                resultado.textContent = "Error: " + (data.error || "Algo salió mal");
            }

        } catch (err) {
            resultado.textContent = "Error de red: " + err.message;
            console.error(err);
        }
    });
});

/* ===============================================
    LOGIN
================================================ */
document.addEventListener("DOMContentLoaded", () => {

    const formLogin = document.getElementById("formLogin");
    const loginResultado = document.getElementById("loginResultado");

    if (!formLogin) return;

    const modalLoginEl = document.getElementById("loginModal");
    const modalLogin = new bootstrap.Modal(modalLoginEl);

    formLogin.addEventListener("submit", async (e) => {
        e.preventDefault();
        loginResultado.textContent = "Iniciando sesión...";

        try {
            const formData = new FormData(formLogin);

            const res = await fetch("/storybook/src/api/users/login_usuario.php", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                loginResultado.textContent = "Login correcto ✅";

                setTimeout(() => {
                    modalLogin.hide();
                    window.location.reload();
                }, 800);

            } else {
                loginResultado.textContent = data.error;
            }

        } catch (err) {
            console.error(err);
            loginResultado.textContent = "Error de conexión";
        }
    });

});

