window.addEventListener("DOMContentLoaded", () => {
    includeHTML("parts/navbar.html", "#navbar", updateNavbarAuth);
    includeHTML("parts/footer.html", "#footer");
});

function includeHTML(file, selector, callback = null) {
    fetch(file)
        .then(response => {
            // Verificar si la respuesta es exitosa
            if (!response.ok) throw new Error("Error cargando " + file);
            return response.text();
        })
        .then(data => {
            console.log("Contenido cargado de " + file + ":", data);
            const target = document.querySelector(selector);
            if (target) {
                target.innerHTML = data;

                // Si se proporciona un callback, ejecutarlo después de insertar el HTML
                if (typeof callback === "function") {
                    callback();
                }
            } else {
                console.warn("Elemento no encontrado: " + selector);
            }
        })
        .catch(error => {
            console.error(error);
        });
}

// ✅ Esta función actualiza el navbar dependiendo de si el usuario es admin
function updateNavbarAuth() {

    fetch("../server/controller/authStatus.php")
        .then(res => res.json())
        .then(data => {

            const authButtons = document.getElementById("authButtons");
            if (!authButtons) return;

            if (data.is_admin) {
                authButtons.innerHTML = `
      <a href="/dictionary/server/controller/Controller.php?action=logout" class="btn btn-outline-light">Logout Admin</a>
    `;
            } else {
                authButtons.innerHTML = `
      <a href="/dictionary/public/login.php" class="btn btn-outline-light">Login Admin</a>
    `;
            }

        })
        .catch(error => {
            console.error("Error obteniendo el estado de autenticación:", error);
        });
}

