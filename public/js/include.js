// Escucha el evento cuando el DOM esté completamente cargado
// Al cargar, inserta dinámicamente el navbar y el footer desde archivos HTML externos
window.addEventListener("DOMContentLoaded", () => {
    includeHTML("parts/navbar.html", "#navbar", updateNavbarAuth);// Navbar con verificación de autenticación
    includeHTML("parts/footer.html", "#footer");// Footer sin lógica adicional
});

/**
 * Carga contenido HTML externo y lo inserta en un elemento del DOM.
 * @param {string} file - Ruta al archivo HTML que se va a incluir.
 * @param {string} selector - Selector CSS del elemento donde se insertará el contenido.
 * @param {function|null} callback - Función opcional que se ejecuta después de insertar el contenido.
 */
function includeHTML(file, selector, callback = null) {
    fetch(file)
        .then(response => {
            if (!response.ok) throw new Error("Error cargando " + file);
            return response.text();// Convierte la respuesta a texto HTML
        })
        .then(data => {
            const target = document.querySelector(selector);// Elemento destino
            if (target) {
                target.innerHTML = data;// Inserta el HTML cargado
                if (typeof callback === "function") {
                    callback();// Ejecuta la función si se ha proporcionado
                }
            } else {
                console.warn("Elemento no encontrado: " + selector);// El selector no existe en el DOM
            }
        })
        .catch(error => {
            console.error(error);// Error al cargar o insertar el HTML
        });
}

/**
 * Verifica si el usuario actual es administrador y modifica el navbar dinámicamente.
 * Muestra botones según el estado de autenticación:
 * - Si es admin: muestra opciones para diccionario, volver al panel, y logout.
 * - Si no es admin: muestra botón de login.
 */
function updateNavbarAuth() {
    fetch("../server/controller/authStatus.php")
        .then(res => res.json())// Convierte la respuesta a objeto JSON
        .then(data => {
            const authButtons = document.getElementById("authButtons");// Contenedor de botones en el navbar
            if (!authButtons) return;// Si no existe el contenedor, se detiene

            authButtons.innerHTML = ""; // Limpia cualquier botón anterior

            if (data.is_admin) {
                // Usuario autenticado como admin

                // Botón: Ver Diccionario
                const btnDiccionario = document.createElement("a");
                btnDiccionario.href = "/dictionary/public/dictionary.html";
                btnDiccionario.className = "btn btn-outline-info me-2";
                btnDiccionario.textContent = "Ver Diccionario";
                authButtons.appendChild(btnDiccionario);

                // Botón: Volver al Panel (solo si no estamos ya en él)
                const currentPage = window.location.pathname;
                if (!currentPage.includes("adminPanel.php")) {
                    const btnPanel = document.createElement("a");
                    btnPanel.href = "/dictionary/public/adminPanel.php";
                    btnPanel.className = "btn btn-outline-warning me-2";
                    btnPanel.textContent = "Volver al Panel";
                    authButtons.appendChild(btnPanel);
                }

                // Botón logout Admin
                const logoutBtn = document.createElement("a");
                logoutBtn.href = "/dictionary/server/controller/Controller.php?action=logout";
                logoutBtn.className = "btn btn-outline-light";
                logoutBtn.textContent = "Logout Admin";
                authButtons.appendChild(logoutBtn);
            } else {
                // Usuario no autenticado como admin

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
