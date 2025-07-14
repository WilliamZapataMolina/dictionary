document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos del DOM
    const searchBtn = document.getElementById('search-button');
    const searchInput = document.getElementById('search-input');
    const container = document.getElementById('dictionary-container');
    const categoriesContainer = document.getElementById('categories-container');

    // Cargar cantidad de palabras con imagen y categorías al iniciar
    loadImageCount();
    loadCategories();

    /**
     * Muestra la cantidad total de palabras que tienen imagen asociada.
     * Se consulta al backend y se inserta el valor en el DOM.
     */
    function loadImageCount() {
        fetch('/dictionary/server/controller/Controller.php?action=countWordsWithImages')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const countContainer = document.getElementById('image-count-container');
                    countContainer.innerHTML = `<strong>Actualmente hay ${data.count} palabras con imágenes disponibles.</strong>`;
                } else {
                    console.error('Error al obtener conteo:', data.message);
                }
            })
            .catch(err => {
                console.error('Error en la petición del conteo:', err);
            });
    }

    /**
    * Carga y muestra las categorías visuales con sus respectivas miniaturas.
    * Las imágenes son agrupadas por categoría, en un layout de tipo tarjeta.
    */
    function loadCategories() {
        categoriesContainer.innerHTML = "<p class='text-muted'>Cargando categorías...</p>";

        fetch("/dictionary/server/controller/Controller.php?action=getCategoriesWithImages")
            .then(res => res.json())
            .then(data => {
                categoriesContainer.innerHTML = '';
                if (Object.keys(data).length === 0) {
                    categoriesContainer.innerHTML = "<p class='text-muted'>No hay categorías para mostrar.</p>";
                    return;
                }
                // Recorre cada categoría y genera una tarjeta con sus imágenes
                for (const [category, images] of Object.entries(data)) {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4 mb-4';

                    const imageHTML = images.map(img =>
                        `<img src="${img}" class="img-thumbnail me-2 mb-2" style="width:70px; height:70px; object-fit:cover;">`
                    ).join('');

                    col.innerHTML = `
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">${category}</h5>
                               <div class="category-images">${imageHTML}</div>
                            </div>
                        </div>
                    `;

                    categoriesContainer.appendChild(col);
                }
            })
            .catch(err => {
                console.error("Error al cargar categorías visuales:", err);
                categoriesContainer.innerHTML = "<p class='text-danger'>Error al cargar categorías.</p>";
            });
    }

    /**
      * Evento de clic en el botón de búsqueda.
      * Envía la palabra ingresada al backend y muestra la tarjeta con imagen, significado, categoría y botón de pronunciación.
      */
    searchBtn.addEventListener('click', () => {
        const query = searchInput.value.trim();
        if (!query) {
            alert('Por favor ingresa una palabra.');
            return;
        }

        // Ocultar categorías al buscar y mostrar mensaje de carga
        categoriesContainer.innerHTML = "";
        container.innerHTML = '<p class="text-muted">Buscando...</p>';

        // Enviar la búsqueda al backend
        fetch('/dictionary/server/controller/Controller.php?action=searchWord', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `search=${encodeURIComponent(query)}`
        })
            .then(response => response.json())
            .then(data => {
                container.innerHTML = ''; // Limpiar resultados anteriores

                if (data.length === 0) {
                    container.innerHTML = '<p class="text-muted">No se encontraron resultados.</p>';
                    return;
                }

                // Mostrar cada palabra encontrada en una tarjeta
                data.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-4';

                    card.innerHTML = `
                        <div class="card h-100 shadow-sm">
                            ${item.image_url ? `<img src="${item.image_url}" class="card-img-top" alt="Imagen de ${item.word_in}">` : ''}
                            <div class="card-body">
                                <h5 class="card-title d-flex align-items-center justify-content-between">
                                 <span>${item.word_in}</span>
                                   <button class="btn btn-sm btn-outline-secondary" onclick="pronunciarPalabra('${item.word_in}')" title="Escuchar pronunciación">
                                        <i class="bi bi-volume-up-fill"></i>
                                       </button>
                                 </h5>
                                <p class="card-text"><strong>Significado:</strong> ${item.meaning}</p>
                                <p class="card-text"><strong>Categoría:</strong> ${item.category}</p>
                            </div>
                        </div>
                    `;

                    container.appendChild(card);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<p class="text-danger">Ocurrió un error al buscar.</p>';
            });
    });

});
/**
 * Reproduce en voz alta la palabra recibida como texto.
 * Si incluye notación fonética (ej: "house /haʊs/"), se elimina para que no afecte la pronunciación.
 * @param {string} texto - Palabra completa que puede incluir notación fonética.
 */
function pronunciarPalabra(texto) {
    const palabra = texto.split('/')[0].trim(); // Extrae solo la palabra, sin fonética
    const mensaje = new SpeechSynthesisUtterance(palabra);
    mensaje.lang = "en-GB";//Acento británico
    window.speechSynthesis.speak(mensaje);
}