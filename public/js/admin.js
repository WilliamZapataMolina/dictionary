window.addEventListener("load", () => {
    const formAdd = document.getElementById("formAddWord");
    const tableWords = document.getElementById("tableWords");
    const categorySelect = formAdd.querySelector("select[name='category_id']");

    cargarNavbarYFooter();
    cargarCategorias();
    cargarPalabras();

    // INICIALIZAR SELECTOR DE IMÁGENES PARA FORMULARIO DE AGREGAR
    cargarSelectorDeImagenes({
        selector: document.getElementById("imageSelectorAdd"),
        preview: document.getElementById("imagePreviewAdd"),
        hiddenInput: formAdd.querySelector("input[name='image_url_path']")
    });

    // ENVÍO DE FORMULARIO DE AGREGAR
    formAdd.addEventListener("submit", async e => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const action = "addWord";

        const rawPath = form.querySelector("input[name='image_url_path']")?.value;
        const path = rawPath?.trim();

        if (path && path !== "undefined" && path !== "") {
            formData.append("file_path", path);
        }

        try {
            const res = await fetch(`../server/controller/Controller.php?action=${action}`, {
                method: "POST",
                body: formData
            });
            const data = await res.json();
            alert(data.message);
            if (data.success) {
                form.reset();
                cargarPalabras();
                cargarSelectorDeImagenes({
                    selector: document.getElementById("imageSelectorAdd"),
                    preview: document.getElementById("imagePreviewAdd"),
                    hiddenInput: formAdd.querySelector("input[name='image_url_path']")
                });
            }
        } catch (err) {
            console.error("Error en envío:", err);
        }
    });

    // FUNCIONES PARA CARGAR CONTENIDOS

    function cargarNavbarYFooter() {
        fetch("parts/navbar.html").then(r => r.text()).then(html => document.getElementById("navbar").innerHTML = html);
        fetch("parts/footer.html").then(r => r.text()).then(html => document.getElementById("footer").innerHTML = html);
    }

    async function cargarCategorias(selectedId = "") {
        categorySelect.innerHTML = `<option value="">Seleccionar categoría</option>`;

        try {
            const res = await fetch("../server/controller/Controller.php?action=getCategories");
            const data = await res.json();

            data.forEach(cat => {
                const opt = document.createElement("option");
                opt.value = cat.id;
                opt.textContent = cat.name;
                categorySelect.appendChild(opt);
            });

            if (selectedId) {
                categorySelect.value = selectedId;
            }
        } catch (e) {
            console.error("Error cargando categorías:", e);
            categorySelect.innerHTML = `<option value="">(Error)</option>`;
        }
    }

    function cargarPalabras() {
        fetch("../server/controller/Controller.php?action=getWord")
            .then(r => r.json())
            .then(data => {
                if (Array.isArray(data)) renderizarTabla(data);
                else tableWords.innerHTML = "<p>Error al cargar palabras.</p>";
            });
    }

    function renderizarTabla(words) {
        console.log("FILE_IDS en renderizarTabla:", words.map(w => w.file_id));
        if (!words.length) {
            tableWords.innerHTML = "<p>No hay palabras.</p>";
            return;
        }

        let html = `
        <table class="table table-bordered">
          <thead><tr>
            <th>Inglés</th><th>Español</th><th>Categoría</th><th>Imagen</th><th>Acciones</th>
          </tr></thead><tbody>`;

        words.forEach(w => {
            html += `
            <tr data-id="${w.id}"
                data-word_in="${w.word_in}"
                data-meaning="${w.meaning}"
                data-category_id="${w.category_id}"
                data-file-id="${w.file_id !== null && w.file_id !== undefined ? w.file_id : ''}"

                data-image_path="${w.file_path || ''}">
              <td>${w.word_in}</td>
              <td>${w.meaning}</td>
              <td>${w.category}</td>
              <td>${w.file_path ? `<img src="${w.file_path}" style="width:60px;">` : 'Sin imagen'}</td>
              <td>
                <button class="btn btn-sm btn-warning btn-edit">Editar</button>
                <button class="btn btn-sm btn-danger btn-delete">Eliminar</button>
              </td>
            </tr>`;
        });

        html += "</tbody></table>";
        tableWords.innerHTML = html;

        // SETUP botones que no tienen relación con el modal
        setupDeleteButtons();
        // setupEditButtons se pasa a modal.js porque es para abrir modal
        document.dispatchEvent(new Event("wordsTableRendered"));
    }

    function setupDeleteButtons() {
        document.querySelectorAll(".btn-delete").forEach(btn => {
            btn.onclick = async () => {
                const row = btn.closest("tr");
                if (!confirm("¿Eliminar esta palabra?")) return;

                const fd = new FormData();
                fd.append("id", row.dataset.id);
                const res = await fetch("../server/controller/Controller.php?action=deleteWord", {
                    method: "POST", body: fd
                });
                const data = await res.json();
                alert(data.message);
                cargarPalabras();
            };
        });
    }

    async function cargarSelectorDeImagenes({ selector, preview, hiddenInput, selectedPath = "" }) {
        selector.innerHTML = `<option value="">Seleccionar imagen...</option>`;
        preview.style.display = selectedPath ? "block" : "none";
        preview.src = selectedPath || "";
        hiddenInput.value = selectedPath;

        try {
            const r = await fetch("../server/controller/Controller.php?action=getImages");
            const list = await r.json();

            list.forEach(img => {
                const opt = document.createElement("option");
                opt.value = img.path;
                opt.textContent = img.name;
                selector.appendChild(opt);
            });

            if (selectedPath) selector.value = selectedPath;

            selector.onchange = () => {
                const path = selector.value;
                hiddenInput.value = path;
                preview.src = path;
                preview.style.display = path ? "block" : "none";
            };
        } catch (e) {
            console.error("Error al cargar imágenes:", e);
        }
    }

    // Exporto las funciones necesarias para modal.js
    window.cargarCategorias = cargarCategorias;
    window.cargarSelectorDeImagenes = cargarSelectorDeImagenes;
    window.cargarPalabras = cargarPalabras;
});
