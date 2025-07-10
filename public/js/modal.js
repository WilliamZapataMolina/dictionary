window.addEventListener("load", () => {
    const formEdit = document.getElementById("formEditWord");
    const modalElement = document.getElementById("editModal");

    async function cargarSelectorDeImagenes({ selector, preview, hiddenInput, selectedFileId }) {
        // 1. Mostrar un mensaje de carga inicial
        selector.innerHTML = '<option value="">Cargando imágenes…</option>';
        console.log("🟡 Iniciando carga de imágenes para selector.");

        try {
            // 2. Obtener las imágenes del servidor
            const res = await fetch("../server/controller/Controller.php?action=getImagesFromDataBase");
            const images = await res.json();
            console.log(`✅ ${images.length} imágenes obtenidas del servidor.`);

            // 3. Reconstruir el selector con todas las opciones de imagen
            let optionsHtml = '<option value="">Seleccionar imagen</option>';
            images.forEach(img => {
                optionsHtml += `<option value="${img.id}" data-path="${img.path}">${img.name}</option>`;
            });
            selector.innerHTML = optionsHtml; // Asigna todas las opciones de una vez

            // 4. Intentar seleccionar la imagen original o la previamente seleccionada
            if (selectedFileId) {
                // Asegurarse de que la opción del selectedFileId exista en el selector
                const optionToSelect = selector.querySelector(`option[value="${selectedFileId}"]`);
                if (optionToSelect) {
                    selector.value = selectedFileId; // Establece el valor del select
                    preview.src = optionToSelect.dataset.path; // Usa el data-path de la opción seleccionada
                    preview.style.display = 'block';
                    hiddenInput.value = optionToSelect.dataset.path;
                    console.log(`🖼️ Imagen precargada correctamente: ${optionToSelect.dataset.path} (ID: ${selectedFileId})`);
                } else {
                    // Si el ID de la imagen original no se encuentra en las opciones cargadas
                    selector.value = ""; // Asegura que "Seleccionar imagen" esté seleccionada
                    preview.style.display = 'none';
                    hiddenInput.value = '';
                    console.warn(`⚠️ selectedFileId (${selectedFileId}) no encontrado en las opciones del selector. Se ha limpiado la vista previa.`);
                }
            } else {
                // Si no hay un selectedFileId (palabra sin imagen o nueva)
                selector.value = ""; // Asegura que "Seleccionar imagen" esté seleccionado
                preview.style.display = 'none';
                hiddenInput.value = '';
                console.log("🚫 No hay selectedFileId inicial. Selector e imagen de vista previa limpios.");
            }

            // 5. Configurar el evento 'change' para cuando el usuario manipule el selector
            selector.addEventListener("change", () => {
                const sel = selector.options[selector.selectedIndex];
                if (sel && sel.value) { // Si hay una opción seleccionada con valor
                    preview.src = sel.dataset.path;
                    preview.style.display = 'block';
                    hiddenInput.value = sel.dataset.path;
                    console.log("🔄 Imagen cambiada por el usuario. Nuevo file_id:", sel.value);
                } else { // Si se selecciona la opción "Seleccionar imagen" (vacía)
                    preview.style.display = 'none';
                    hiddenInput.value = '';
                    console.log("🔄 Imagen desvinculada por el usuario (file_id limpio).");
                }
            });

        } catch (err) {
            // Manejo de errores en caso de que la petición falle
            console.error("🚨 Error cargando imágenes para el selector:", err);
            selector.innerHTML = '<option value="">Error al cargar imágenes</option>';
            preview.style.display = 'none';
        }
    }

    async function setupEditButtons() {
        document.querySelectorAll(".btn-edit").forEach(btn => {
            btn.onclick = async () => {
                const row = btn.closest("tr");

                // 👇👇👇 PON TU BREAKPOINT AQUÍ 👇👇👇
                formEdit.dataset.wordId = row.dataset.id;
                // 👆👆👆👆👆👆👆👆👆👆👆👆👆👆👆
                formEdit.dataset.originalFile = row.dataset.fileId || "";

                formEdit.word_in.value = row.dataset.word_in;
                formEdit.meaning.value = row.dataset.meaning;
                formEdit.querySelector("input[name='id']").value = row.dataset.id;


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

                new bootstrap.Modal(modalElement).show();

                await cargarSelectorDeImagenes({
                    selector: formEdit.querySelector("select[name='file_id']"),
                    preview: document.getElementById("imagePreviewEdit"),
                    hiddenInput: formEdit.image_url_path,
                    selectedFileId: row.dataset.fileId
                });
            };
        });
    }

    formEdit.addEventListener("submit", async (e) => {
        e.preventDefault();
        const form = e.target;

        const fileIdSelect = form.querySelector("select[name='file_id']");
        // <-- PON UN BREAKPOINT JUSTO EN LA LÍNEA SIGUIENTE
        console.log("🐛 Valor RAW de fileIdSelect.value al inicio del submit:", fileIdSelect.value);
        const wordId = form.dataset.wordId;
        const originalFileId = form.dataset.originalFile;

        // 1. Obtener el valor actual del select. Convertir 'undefined' o null a cadena vacía.
        // Esto es CLAVE para evitar que la cadena "undefined" llegue más allá.
        let finalFileIdToSend = fileIdSelect.value ?? "";
        // 👆👆👆 ¡CAMBIO AQUÍ! Añadimos ?? "" 👆👆👆

        // Puedes quitar el 'debugger;' si ya no lo necesitas, o dejarlo para verificar este paso.
        // debugger; 

        // 2. Lógica para determinar el file_id final a enviar:
        // Si el valor actual del select es "" (opción "Seleccionar imagen") Y la palabra tenía una imagen original,
        // significa que el usuario no cambió la imagen (o no la quitó explícitamente), entonces restauramos la original.
        if (finalFileIdToSend === "" && originalFileId) {
            finalFileIdToSend = originalFileId;
            console.log("🔁 Restaurando file_id original:", originalFileId);
        } else if (finalFileIdToSend === "") {
            // Si el usuario explícitamente eligió la opción vacía, o la palabra nunca tuvo imagen y el select está vacío,
            // enviamos una cadena vacía. PHP la interpretará correctamente como NULL.
            console.log("🔄 Intentando desvincular la imagen (enviando cadena vacía).");
        }
        // Si finalFileIdToSend ya tiene un ID numérico (porque se seleccionó una nueva imagen), se usa ese valor.

        const formData = new FormData(form);
        formData.set("id", wordId);
        // ¡Esta es la línea CRÍTICA! Sobreescribe lo que FormData haya recogido para file_id
        // con el valor que hemos determinado (`finalFileIdToSend`).
        formData.set("file_id", finalFileIdToSend);

        console.log("📦 Datos enviados en FormData:");
        for (const [k, v] of formData.entries()) {
            console.log(`${k}: ${v}`);
        }
        console.log("🚀 file_id FINAL enviado:", finalFileIdToSend); // Este log debe mostrar un número o ""

        try {
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
            console.error("🚨 Error durante el envío:", err);
        }
    });

    document.addEventListener("wordsTableRendered", setupEditButtons);
});