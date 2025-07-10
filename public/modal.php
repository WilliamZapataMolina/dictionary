<?php
// modal.php: solo el markup del modal de edición
?>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditWord" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar palabra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editWordIn" class="form-label">Palabra en inglés</label>
                            <input type="text" name="word_in" id="editWordIn" class="form-control" placeholder="Palabra en inglés" required />
                        </div>
                        <div class="col-md-4">
                            <label for="editMeaning" class="form-label">Traducción al español</label>
                            <input type="text" name="meaning" id="editMeaning" class="form-control" placeholder="Traducción al español" required />
                        </div>
                        <div class="col-md-4">
                            <label for="editCategory" class="form-label">Categoría</label>
                            <select name="category_id" id="editCategory" class="form-select" required>
                                <option value="">Seleccionar categoría</option>
                                <!-- JS rellenará estas opciones -->
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imageSelectorEdit" class="form-label">Seleccionar imagen</label>
                        <select id="imageSelectorEdit" name="file_id" class="form-select">
                            <!-- JS rellenará estas opciones -->
                        </select>
                    </div>

                    <div class="mb-3 text-center">
                        <img id="imagePreviewEdit" src="" alt="Vista previa" class="img-thumbnail" style="max-height:200px; display:none;" />
                    </div>


                    <input type="hidden" name="image_url_path" id="editImageUrlPath" />
                    <input type="hidden" name="id" id="editWordId" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>