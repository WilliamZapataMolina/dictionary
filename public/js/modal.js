window.addEventListener("load", () => {
    const formEdit = document.getElementById("formEditWord");
    const modalElement = document.getElementById("editModal");

    // Escuchar cuando se recarga la tabla de palabras para reconfigurar los botones editar
    document.addEventListener("wordsTableRendered", setupEditButtons);

    // ENVÍO DE FORMULARIO DE EDICIÓN (Modal)
    formEdit.addEventListener("submit", async e => {
        e.preventDefault();
        const form = e.target;
        form.querySelector("input[name='file_id']").value = form.querySelector("select[name='image_selector']").value;
        console.log("file_id enviado:", form.querySelector("input[name='file_id']").value);
        const formData = new FormData(form);
        const action = "updateWord";

        formData.append("id", form.dataset.wordId);

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
                delete form.dataset.editing;
                delete form.dataset.wordId;

                // Recargar tabla y cerrar modal
                window.cargarPalabras();
                bootstrap.Modal.getInstance(modalElement).hide();
            }
        } catch (err) {
            console.error("Error en envío (edición):", err);
        }
    });
    document.getElementById("imageSelectorEdit").addEventListener("change", function () {
        document.getElementById("editFileId").value = this.value;
    });

    // Configurar los botones Editar para que abran el modal con datos cargados
    async function setupEditButtons() {
        document.querySelectorAll(".btn-edit").forEach(btn => {
            btn.onclick = async () => {
                const row = btn.closest("tr");

                formEdit.dataset.editing = "true";
                formEdit.dataset.wordId = row.dataset.id;

                // Cargar los campos de texto
                formEdit.querySelector("[name='word_in']").value = row.dataset.word_in;
                formEdit.querySelector("[name='meaning']").value = row.dataset.meaning;

                // Sincronizar file_id oculto
                formEdit.querySelector("[name='file_id']").value = row.dataset.file_id || "0";

                // Seleccionar imagen en el select
                const imageSelectEdit = formEdit.querySelector("select[name='image_selector']");
                if (imageSelectEdit) {
                    imageSelectEdit.value = row.dataset.file_id || "";
                }

                // Vista previa si hay imagen
                const imagePreview = document.getElementById("imagePreviewEdit");
                const imagePath = row.dataset.image_path;
                if (imagePath) {
                    imagePreview.src = imagePath;
                    imagePreview.style.display = "block";
                } else {
                    imagePreview.style.display = "none";
                }

                // Cargar categorías
                const categorySelectEdit = formEdit.querySelector("select[name='category_id']");
                categorySelectEdit.innerHTML = `<option value="">Cargando categorías...</option>`;

                try {
                    const data = await fetch("../server/controller/Controller.php?action=getCategories").then(r => r.json());
                    categorySelectEdit.innerHTML = `<option value="">Seleccionar categoría</option>`;

                    data.forEach(cat => {
                        const opt = document.createElement("option");
                        opt.value = cat.id;
                        opt.textContent = cat.name;
                        categorySelectEdit.appendChild(opt);
                    });

                    categorySelectEdit.value = row.dataset.category_id;
                } catch (e) {
                    console.error("Error cargando categorías:", e);
                    categorySelectEdit.innerHTML = `<option value="">(Error)</option>`;
                }

                // Mostrar modal
                new bootstrap.Modal(modalElement).show();
            };
        });


    }

    // Solo se ejecuta una vez al mostrar el modal
    modalElement.addEventListener('shown.bs.modal', async () => {
        const wordId = formEdit.dataset.wordId;
        const row = document.querySelector(`tr[data-id='${wordId}']`);
        if (!row) return;

        const selector = document.getElementById("imageSelectorEdit");
        const preview = document.getElementById("imagePreviewEdit");
        const hiddenInput = document.getElementById("editImageUrlPath");
        const fileIdInput = document.getElementById("editFileId");

        // Esperamos que cargarSelectorDeImagenes termine (debe devolver una promesa)
        await cargarSelectorDeImagenes({
            selector,
            preview,
            hiddenInput,
            fileIdInput,
            selectedFileId: row.dataset.file_id || ""
        });


        // REASIGNAMOS valor previamente seleccionado
        selector.value = row.dataset.file_id || "";
        fileIdInput.value = row.dataset.file_id || "";

        if (row.dataset.image_path) {
            preview.src = row.dataset.image_path;
            preview.style.display = "block";
        } else {
            preview.style.display = "none";
        }
    });

});
