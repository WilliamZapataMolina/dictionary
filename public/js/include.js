window.addEventListener("DOMContentLoaded", () => {
    includeHTML("parts/navbar.html", "#navbar", updateNavbarAuth);
    includeHTML("parts/footer.html", "#footer");
});

function includeHTML(file, selector, callback = null) {
    fetch(file)
        .then(response => {
            if (!response.ok) throw new Error("Error cargando " + file);
            return response.text();
        })
        .then(data => {
            const target = document.querySelector(selector);
            if (target) {
                target.innerHTML = data;
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

// ✅ Actualiza el navbar dependiendo si el usuario es admin
function updateNavbarAuth() {
    fetch("../server/controller/authStatus.php")
        .then(res => res.json())
        .then(data => {
            const authButtons = document.getElementById("authButtons");
            if (!authButtons) return;

            authButtons.innerHTML = ""; // limpiar contenido anterior

            if (data.is_admin) {
                // Botón para ir al diccionario
                const btnDiccionario = document.createElement("a");
                btnDiccionario.href = "/dictionary/public/dictionary.html";
                btnDiccionario.className = "btn btn-outline-info me-2";
                btnDiccionario.textContent = "Ver Diccionario";
                authButtons.appendChild(btnDiccionario);

                // Detectar si no estamos en el panel
                const currentPage = window.location.pathname;
                if (!currentPage.includes("adminPanel.php")) {
                    const btnPanel = document.createElement("a");
                    btnPanel.href = "/dictionary/public/adminPanel.php";
                    btnPanel.className = "btn btn-outline-warning me-2";
                    btnPanel.textContent = "Volver al Panel";
                    authButtons.appendChild(btnPanel);
                }

                // Botón logout
                const logoutBtn = document.createElement("a");
                logoutBtn.href = "/dictionary/server/controller/Controller.php?action=logout";
                logoutBtn.className = "btn btn-outline-light";
                logoutBtn.textContent = "Logout Admin";
                authButtons.appendChild(logoutBtn);
            } else {
                // Mostrar botón de login
                const loginBtn = document.createElement("a");
                loginBtn.href = "/dictionary/public/login.php";
                loginBtn.className = "btn btn-outline-light";
                loginBtn.textContent = "Login Admin";
                authButtons.appendChild(loginBtn);
            }
        })
        .catch(error => {
            console.error("Error obteniendo el estado de autenticación:", error);
        });
}
