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
            <form id="formAddWord" enctype="multipart/form-data" class="row g-3" autocomplete="on">
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

                <div class="col-md-6 d-flex flex-column justify-content-end">
                    <label for="imageSelectorAdd" class="form-label">Seleccionar imagen</label>
                    <select id="imageSelectorAdd" name="image_selector" class="form-select" required></select>
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Agregar palabra</button>
                </div>

                <!-- Vista previa de la imagen seleccionada -->
                <div class="col-12 text-center">
                    <img id="imagePreviewAdd" src="" alt="Vista previa" style="max-height: 200px; display: none;" class="img-thumbnail" />
                </div>

                <!-- Campos ocultos para manejar datos de imagen -->
                <input type="hidden" name="image_url_path" id="addImageUrlPath" />
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

    <!-- Incluir modal de edición -->
    <?php include 'modal.php'; ?>

    <!-- Scripts al final del body -->
    <!-- Bootstrap JS (Popper incluido) -->
    <script src="thirdparty/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js" defer></script>
    <!-- Lógica principal del panel (agregar, listar, borrar) -->
    <script src="js/admin.js" defer></script>
    <!-- Lógica del modal de edición -->
    <script src="js/modal.js" defer></script>

</body>

</html>