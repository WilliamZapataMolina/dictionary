window.addEventListener("load", () => {
    const formEdit = document.getElementById("formEditWord");
    const modalElement = document.getElementById("editModal");

    // Función para cargar imágenes en el selector
    async function cargarSelectorDeImagenes({ selector, preview, hiddenInput, fileIdInput, selectedFileId }) {
        selector.innerHTML = '<option value="">Cargando imágenes...</option>';
        try {
            const res = await fetch("../server/controller/Controller.php?action=getImages");
            const images = await res.json();

            selector.innerHTML = '<option value="">Seleccionar imagen</option>';
            images.forEach(img => {
                const option = document.createElement("option");
                option.value = img.id;
                option.textContent = img.name;
                option.dataset.path = img.path;
                selector.appendChild(option);
            });

            if (selectedFileId) {
                selector.value = selectedFileId;
                const selectedOption = selector.querySelector(`option[value="${selectedFileId}"]`);
                if (selectedOption) {
                    const path = selectedOption.dataset.path;
                    preview.src = path;
                    preview.style.display = path ? "block" : "none";
                    hiddenInput.value = path;
                    fileIdInput.value = selectedFileId;
                }
            }

            // Evento change para actualizar vista previa y campos ocultos
            selector.addEventListener("change", () => {
                const selected = selector.options[selector.selectedIndex];
                if (selected && selected.value) {
                    preview.src = selected.dataset.path || "";
                    preview.style.display = "block";
                    hiddenInput.value = preview.src;
                    fileIdInput.value = selected.value;
                } else {
                    preview.style.display = "none";
                    hiddenInput.value = "";
                    fileIdInput.value = "";
                }
            });

        } catch (err) {
            console.error("Error cargando imágenes:", err);
            selector.innerHTML = '<option value="">(Error al cargar imágenes)</option>';
            preview.style.display = "none";
        }
    }

    // Setup botones para abrir modal con datos precargados
    async function setupEditButtons() {
        document.querySelectorAll(".btn-edit").forEach(btn => {
            btn.onclick = async () => {
                const row = btn.closest("tr");
                formEdit.dataset.editing = "true";
                formEdit.dataset.wordId = row.dataset.id;
                formEdit.dataset.originalFileId = row.dataset.file_id || "";

                formEdit.querySelector("[name='word_in']").value = row.dataset.word_in;
                formEdit.querySelector("[name='meaning']").value = row.dataset.meaning;

                const imageSelector = formEdit.querySelector("select[name='image_selector']");
                const fileIdInput = formEdit.querySelector("input[name='file_id']");
                const imagePathInput = formEdit.querySelector("input[name='image_url_path']");
                const preview = document.getElementById("imagePreviewEdit");

                // Cargar categorías (puedes adaptar si quieres)
                const categorySelect = formEdit.querySelector("select[name='category_id']");
                categorySelect.innerHTML = `<option value="">Cargando categorías...</option>`;
                try {
                    const data = await fetch("../server/controller/Controller.php?action=getCategories").then(r => r.json());
                    categorySelect.innerHTML = `<option value="">Seleccionar categoría</option>`;
                    data.forEach(cat => {
                        const opt = document.createElement("option");
                        opt.value = cat.id;
                        opt.textContent = cat.name;
                        categorySelect.appendChild(opt);
                    });
                    categorySelect.value = row.dataset.category_id;
                } catch (e) {
                    console.error("Error cargando categorías:", e);
                    categorySelect.innerHTML = `<option value="">(Error)</option>`;
                }

                // Mostrar modal y cargar imágenes
                new bootstrap.Modal(modalElement).show();

                await cargarSelectorDeImagenes({
                    selector: imageSelector,
                    preview,
                    hiddenInput: imagePathInput,
                    fileIdInput,
                    selectedFileId: row.dataset.file_id
                });
            };
        });
    }

    // Evento submit para el formulario
    formEdit.addEventListener("submit", async e => {
        e.preventDefault();

        const form = e.target;
        const fileIdInput = form.querySelector("input[name='file_id']");
        const imageSelector = form.querySelector("select[name='image_selector']");
        //fileIdInput.value = imageSelector.value || fileIdInput.value;
        fileIdInput.value = imageSelector.value || form.dataset.originalFileId;
        console.log("🌐 Dataset wordId:", form.dataset.wordId);
        const formData = new FormData(form);
        formData.append("id", form.dataset.wordId);

        console.log("file_id en formData:", formData.get("file_id")); // <-- Aquí

        try {
            const res = await fetch(`../server/controller/Controller.php?action=updateWord`, {
                method: "POST",
                body: formData
            });
            const data = await res.json();
            alert(data.message);
            if (data.success) {
                form.reset();
                delete form.dataset.editing;
                delete form.dataset.wordId;
                window.cargarPalabras();
                bootstrap.Modal.getInstance(modalElement).hide();
            }
        } catch (err) {
            console.error("Error en envío (edición):", err);
        }
    });

    // Inicializar setup de botones
    document.addEventListener("wordsTableRendered", setupEditButtons);
});
