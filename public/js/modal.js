window.addEventListener("load", () => {
    const formEdit = document.getElementById("formEditWord");
    const modalElement = document.getElementById("editModal");

    async function cargarSelectorDeImagenes({ selector, preview, hiddenInput, selectedFileId }) { // Asegúrate de que recibes selectedFileId
        selector.innerHTML = `<option value="">Seleccionar imagen...</option>`;
        preview.style.display = "none"; // Ocultar inicialmente
        preview.src = ""; // Limpiar src

        try {
            // *** Se accede a las imágenes desde la base de datos ***
            const res = await fetch("../server/controller/Controller.php?action=getImagesFromDatabase");

            const images = await res.json(); // Aquí se intenta parsear la respuesta

            // *** Verificación de la respuesta (recomendado) ***
            if (!Array.isArray(images)) {
                console.error("🚨 La respuesta del servidor no es un array:", images);
                selector.innerHTML = '<option value="">Error al cargar imágenes</option>';
                return; // Detener la ejecución si no es un array
            }
            console.log(`✅ ${images.length} imágenes obtenidas del servidor.`);

            // Poblar el selector con las imágenes
            images.forEach(img => {
                const opt = document.createElement("option");
                opt.value = img.id; // ¡Usar el ID de la base de datos como valor!
                opt.textContent = img.name; // Mostrar el nombre de la imagen
                opt.dataset.path = img.path; // Guardar la ruta completa en un data attribute
                selector.appendChild(opt);
            });

            // Precargar la imagen seleccionada si hay un selectedFileId
            if (selectedFileId) {
                const optionToSelect = selector.querySelector(`option[value="${selectedFileId}"]`);
                if (optionToSelect) {
                    selector.value = selectedFileId; // Seleccionar la opción en el <select>
                    preview.src = optionToSelect.dataset.path; // Establecer la URL de la vista previa
                    preview.style.display = 'block'; // Mostrar la vista previa
                    hiddenInput.value = optionToSelect.dataset.path; // Actualizar el input oculto si lo necesitas para la URL completa
                    console.log(`🖼️ Imagen precargada correctamente: ${optionToSelect.dataset.path} (ID: ${selectedFileId})`);
                } else {
                    console.warn(`⚠️ selectedFileId (${selectedFileId}) no encontrado en las opciones del selector.`);
                    selector.value = ""; // Asegurarse de que el selector esté en la opción por defecto
                    preview.style.display = 'none'; // Ocultar vista previa si no se encontró
                    hiddenInput.value = ''; // Limpiar el input oculto
                }
            } else {
                console.log("🚫 No hay selectedFileId inicial. Selector e imagen de vista previa limpios.");
                selector.value = ""; // Asegurar que el selector esté en la opción por defecto
                preview.style.display = 'none'; // Ocultar vista previa si no hay imagen
                hiddenInput.value = ''; // Limpiar el input oculto
            }
            // LISTENER PARA EL CAMBIO DEL SELECTOR! ---
            selector.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const newImagePath = selectedOption.dataset.path; // Obtener la ruta del data-path

                if (newImagePath) {
                    preview.src = newImagePath;      // Actualizar la vista previa
                    preview.style.display = 'block'; // Mostrar la vista previa
                    hiddenInput.value = selectedOption.value; // Guardar el ID de la imagen seleccionada
                } else {
                    // Si se selecciona la opción "Seleccionar imagen..." o una sin path
                    preview.src = "";
                    preview.style.display = 'none'; // Ocultar vista previa
                    hiddenInput.value = ""; // Limpiar el ID
                }
            });
            // ----------------------------------------------------------------
        } catch (error) {
            console.error("🚨 Error cargando imágenes para el selector:", error);
            selector.innerHTML = '<option value="">Error al cargar imágenes</option>';
            preview.style.display = 'none';
        }
    }

    async function setupEditButtons() {
        document.querySelectorAll(".btn-edit").forEach(btn => {
            btn.onclick = async () => {
                const row = btn.closest("tr");


                formEdit.dataset.wordId = row.dataset.id;

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

        console.log("🐛 Valor RAW de fileIdSelect.value al inicio del submit:", fileIdSelect.value);
        const wordId = form.dataset.wordId;
        const originalFileId = form.dataset.originalFile;

        // 1. Obtener el valor actual del select. Convertir 'undefined' o null a cadena vacía.
        // Esto es CLAVE para evitar que la cadena "undefined" llegue más allá.
        let finalFileIdToSend = fileIdSelect.value ?? "";
        // 👆👆👆 ¡CAMBIO AQUÍ! Añadimos ?? "" 👆👆👆

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