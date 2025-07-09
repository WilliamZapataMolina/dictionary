window.addEventListener("load", () => {
    const formEdit = document.getElementById("formEditWord");
    const modalElement = document.getElementById("editModal");

    /**
     * Carga las imágenes al selector del modal de edición.
     */
    async function cargarSelectorDeImagenes({ selector, preview, hiddenInput, fileIdInput, selectedFileId }) {
        selector.innerHTML = '<option value="">Cargando imágenes…</option>';
        try {
            const res = await fetch("../server/controller/Controller.php?action=getImages");
            const images = await res.json();

            selector.innerHTML = '<option value="">Seleccionar imagen</option>';
            images.forEach(img => {
                const opt = document.createElement("option");
                opt.value = img.id;
                opt.textContent = img.name;
                opt.dataset.path = img.path;
                selector.appendChild(opt);
            });

            if (selectedFileId) {
                selector.value = selectedFileId;
                const sel = selector.querySelector(`option[value="${selectedFileId}"]`);
                if (sel) {
                    preview.src = sel.dataset.path;
                    preview.style.display = 'block';
                    hiddenInput.value = sel.dataset.path;
                    fileIdInput.value = selectedFileId;
                }
            } else {
                preview.style.display = 'none';
                hiddenInput.value = '';
                fileIdInput.value = '';
            }

            selector.addEventListener("change", () => {
                console.log("🔄 Imagen cambiada. Nuevo file_id:", fileIdInput.value);

                const sel = selector.options[selector.selectedIndex];
                if (sel && sel.value) {
                    preview.src = sel.dataset.path;
                    preview.style.display = 'block';
                    hiddenInput.value = sel.dataset.path;
                    fileIdInput.value = sel.value;
                } else {
                    preview.style.display = 'none';
                    hiddenInput.value = '';
                    fileIdInput.value = '';
                }
            });
        } catch (err) {
            console.error("Error cargando imágenes:", err);
            selector.innerHTML = '<option value="">Error al cargar imágenes</option>';
            preview.style.display = 'none';
        }
    }

    /**
     * Enlaza cada botón "Editar" de la tabla para abrir el modal
     * y precargar los datos correspondientes.
     */
    async function setupEditButtons() {
        document.querySelectorAll(".btn-edit").forEach(btn => {
            btn.onclick = async () => {
                const row = btn.closest("tr");

                // Guardamos los datos en dataset
                formEdit.dataset.wordId = row.dataset.id;
                formEdit.dataset.originalFile = row.dataset.fileId || "";


                // Rellenamos los campos
                formEdit.word_in.value = row.dataset.word_in;
                formEdit.meaning.value = row.dataset.meaning;
                formEdit.querySelector("input[name='id']").value = row.dataset.id;

                // Cargar categorías
                const catSel = formEdit.category_id;
                catSel.innerHTML = '<option>…</option>';
                try {
                    const cats = await (await fetch("../server/controller/Controller.php?action=getCategories")).json();
                    catSel.innerHTML = '<option value="">Seleccionar categoría</option>';
                    cats.forEach(c => {
                        const o = document.createElement("option");
                        o.value = c.id;
                        o.textContent = c.name;
                        catSel.append(o);
                    });
                    catSel.value = row.dataset.category_id;
                } catch (e) {
                    console.error("Error cargando categorías:", e);
                }

                // Mostrar modal
                new bootstrap.Modal(modalElement).show();

                // Cargar imágenes
                await cargarSelectorDeImagenes({
                    selector: formEdit.image_selector,
                    preview: document.getElementById("imagePreviewEdit"),
                    hiddenInput: formEdit.image_url_path,
                    fileIdInput: formEdit.file_id,
                    selectedFileId: row.dataset.file_id
                });
            };
        });
    }

    /**
     * Maneja el envío del formulario de edición (modal).
     * Asegura que se mantenga el file_id original si no se cambia.
     */
    formEdit.addEventListener("submit", async (e) => {
        e.preventDefault();

        const form = e.target;

        const imageSelector = form.querySelector("select[name='image_selector']");
        const fileIdInput = form.querySelector("input[name='file_id']");
        const wordId = form.dataset.wordId;
        const originalFileId = form.dataset.originalFile;

        // 🔍 LOG importante
        console.log("🔍 form.dataset.wordId:", wordId);
        console.log("🔍 form.dataset.originalFile:", originalFileId);

        // ✅ Corregir cómo se establece el file_id
        if (imageSelector && imageSelector.value && imageSelector.value !== "undefined") {
            fileIdInput.value = imageSelector.value;
        } else if (originalFileId && originalFileId !== "undefined") {
            fileIdInput.value = originalFileId;
        } else {
            fileIdInput.value = ""; // si no hay nada
        }


        // ✅ Corregimos el id
        const formData = new FormData(form);
        formData.set("id", wordId); // ← IMPORTANTE, no uses append

        // 🔍 Verificar todos los valores antes de enviar
        console.log("📦 Datos enviados:");
        for (const [k, v] of formData.entries()) {
            console.log(`${k}: ${v}`);
        }

        try {
            console.log("🚀 file_id FINAL enviado:", fileIdInput.value);

            const res = await fetch("../server/controller/Controller.php?action=updateWord", {
                method: "POST",
                body: formData,
            });

            const data = await res.json();
            alert(data.message);

            if (data.success) {
                form.reset();
                delete form.dataset.wordId;
                delete form.dataset.originalFile;
                window.cargarPalabras();
                bootstrap.Modal.getInstance(modalElement).hide();
            }
        } catch (err) {
            console.error("🚨 Error en el envío:", err);
        }
    });

    // Espera a que se cargue la tabla para enlazar botones
    document.addEventListener("wordsTableRendered", setupEditButtons);
});
