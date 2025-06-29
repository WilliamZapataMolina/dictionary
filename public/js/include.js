window.addEventListener("DOMContentLoaded", () => {
    includeHTML("parts/navbar.html", "#navbar");
    includeHTML("parts/footer.html", "#footer");
});

function includeHTML(file, selector) {
    fetch(file)
        .then(response => {
            if (!response.ok) throw new Error("Error cargando " + file);
            return response.text();
        })
        .then(data => {
            const target = document.querySelector(selector);
            if (target) {
                target.innerHTML = data;
            } else {
                console.warn("Elemento no encontrado: " + selector);
            }
        })
        .catch(error => {
            console.error(error);
        });
}

