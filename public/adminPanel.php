<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel de Administración | Willi-Vocabulary</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="thirdparty/bootstrap-5.3.3-dist/css/bootstrap.min.css" />

    <!-- Estilos propios -->
    <link rel="stylesheet" href="css/styles.css" />

    <!-- include.js (para navbar y footer) -->
    <script src="js/include.js" defer></script>
</head>

<body>

    <!-- Navbar dinámico -->
    <div id="navbar"></div>

    <main class="container py-4">
        <h1 class="mb-4 text-center">Panel de Administración</h1>

        <!-- Formulario para agregar nueva palabra -->
        <section id="add-word-form" class="mb-5">
            <h2 class="mb-3">Agregar nueva palabra</h2>
            <form id="formAddWord" enctype="multipart/form-data" class="row g-3" autocomplete="off">
                <div class="col-md-4">
                    <input type="text" name="word_in" id="addWordIn" class="form-control" placeholder="Palabra en inglés" required />
                </div>
                <div class="col-md-4">
                    <input type="text" name="meaning" id="addMeaning" class="form-control" placeholder="Traducción al español" required />
                </div>
                <div class="col-md-4">
                    <select name="category_id" id="addCategory" class="form-select" required>
                        <option value="">Seleccionar categoría</option>
                        <!-- JS rellenará estas opciones -->
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="imageSelectorAdd" class="form-label">Seleccionar imagen</label>
                    <select id="imageSelectorAdd" name="image_selector" class="form-select" required>
                        <input type="hidden" name="file_id" id="addFileId" />
                        <option value="">Seleccionar imagen...</option>
                        <!-- JS rellenará estas opciones -->
                    </select>
                </div>

                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary w-100">Agregar palabra</button>
                </div>

                <!-- Vista previa de la imagen seleccionada -->
                <div class="col-12 text-center">
                    <img id="imagePreviewAdd" src="" alt="Vista previa" style="max-height: 200px; display: none;" class="img-thumbnail" />
                </div>

                <!-- Campos ocultos para manejar datos de imagen -->
                <input type="hidden" name="image_url_path" id="addImageUrlPath" />
                <input type="hidden" name="file_id" id="addFileId" />
            </form>
        </section>

        <!-- Tabla de palabras registradas -->
        <section id="word-list">
            <h2 class="mb-3">Lista de palabras registradas</h2>
            <div id="tableWords" class="table-responsive">
                <!-- JS inyectará esta tabla -->
            </div>
        </section>
    </main>

    <!-- Footer dinámico -->
    <div id="footer"></div>

    <!--------------------------------------------------------------- Modal para editar palabra ----------------------------------------->

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditWord" class="modal-body" enctype="multipart/form-data" autocomplete="off">
                    <h5 class="modal-title mb-3">Editar palabra</h5>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" name="word_in" id="editWordIn" class="form-control" placeholder="Palabra en inglés" required />
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="meaning" id="editMeaning" class="form-control" placeholder="Traducción al español" required />
                        </div>
                        <div class="col-md-4">
                            <select name="category_id" id="editCategory" class="form-select" required>
                                <option value="">Seleccionar categoría</option>
                                <!-- JS rellenará estas opciones -->
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imageSelectorEdit" class="form-label">Seleccionar imagen</label>
                        <select id="imageSelectorEdit" name="image_selector" class="form-select">
                            <input type="hidden" name="file_id" id="editFileId" value="0" />
                            <!-- JS rellenará estas opciones -->
                        </select>
                    </div>

                    <div class="mb-3 text-center">
                        <img id="imagePreviewEdit" src="" alt="Vista previa" style="max-height: 200px; display: none;" class="img-thumbnail" />
                    </div>

                    <!-- Campos ocultos para imagen y para identificar la palabra a editar -->
                    <input type="hidden" name="image_url_path" id="editImageUrlPath" />

                    <input type="hidden" name="word_id" id="editWordId" />

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script principal (lógica del panel) -->
    <script src="js/admin.js" defer></script>
    <script src="js/modal.js"></script>

</body>

</html>