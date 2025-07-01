document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formAddWord");
    const tableWords = document.getElementById("tableWords");
    const categorySelect = form.querySelector("select[name='category']");

    cargarNavbarYFooter();
    cargarCategorias();
    cargarPalabras();

    form.addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData(form);

        const action = form.dataset.editing === "true" ? "editWord" : "addWord";
        if (action === "editWord") {
            formData.append("id", form.dataset.wordId);
        }

        fetch(`../server/controller/Controller.php?action=${action}`, {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    form.reset();
                    form.dataset.editing = "false";
                    delete form.dataset.wordId;
                    cargarPalabras();
                }
            })
            .catch(err => console.error("Error en envío:", err));
    });

    function cargarNavbarYFooter() {
        fetch("parts/navbar.html")
            .then(res => res.text())
            .then(html => document.getElementById("navbar").innerHTML = html);
        fetch("parts/footer.html")
            .then(res => res.text())
            .then(html => document.getElementById("footer").innerHTML = html);
    }

    function cargarCategorias() {
        fetch("../server/controller/Controller.php?action=getCategories")
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    categorySelect.innerHTML = `<option value="">Seleccionar categoría</option>`;
                    data.forEach(cat => {
                        const option = document.createElement("option");
                        option.value = cat.name;
                        option.textContent = cat.name;
                        categorySelect.appendChild(option);
                    });
                }
            })
            .catch(err => {
                console.error("Error cargando categorías:", err);
                categorySelect.innerHTML = `<option value="">(Error al cargar)</option>`;
            });
    }

    function cargarPalabras() {
        fetch("../server/controller/Controller.php?action=getWords")
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    renderizarTabla(data);
                } else {
                    tableWords.innerHTML = "<p>No se pudieron cargar las palabras.</p>";
                }
            });
    }

    function renderizarTabla(words) {
        if (words.length === 0) {
            tableWords.innerHTML = "<p>No hay palabras registradas.</p>";
            return;
        }

        let html = `<table class="table table-bordered">
            <thead>
                <tr>
                    <th>Inglés</th>
                    <th>Español</th>
                    <th>Categoría</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>`;

        words.forEach(word => {
            html += `<tr data-id="${word.id}"
                         data-english="${word.english}"
                         data-spanish="${word.spanish}"
                         data-category="${word.category}">
                <td>${word.english}</td>
                <td>${word.spanish}</td>
                <td>${word.category}</td>
                <td><img src="${word.image_url}" style="width:60px;"></td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit">Editar</button>
                    <button class="btn btn-sm btn-danger btn-delete">Eliminar</button>
                </td>
            </tr>`;
        });

        html += "</tbody></table>";
        tableWords.innerHTML = html;

        // Asignar eventos
        document.querySelectorAll(".btn-edit").forEach(btn => {
            btn.addEventListener("click", e => {
                const row = e.target.closest("tr");
                form.querySelector("input[name='english']").value = row.dataset.english;
                form.querySelector("input[name='spanish']").value = row.dataset.spanish;
                form.querySelector("select[name='category']").value = row.dataset.category;

                form.dataset.editing = "true";
                form.dataset.wordId = row.dataset.id;
            });
        });

        document.querySelectorAll(".btn-delete").forEach(btn => {
            btn.addEventListener("click", e => {
                const row = e.target.closest("tr");
                const id = row.dataset.id;

                if (!confirm("¿Seguro que deseas eliminar esta palabra?")) return;

                const formData = new FormData();
                formData.append("id", id);

                fetch("../server/controller/Controller.php?action=deleteWord", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        cargarPalabras();
                    })
                    .catch(err => console.error("Error eliminando palabra:", err));
            });
        });
    }
});
