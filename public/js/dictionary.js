document.addEventListener('DOMContentLoaded', () => {
    const searchBtn = document.getElementById('search-button');
    const searchInput = document.getElementById('search-input');
    const container = document.getElementById('dictionary-container');
    const categoriesContainer = document.getElementById('categories-container');

    loadImageCount();
    loadCategories();

    // Mostrar cantidad de palabras con imagen
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

    // Cargar categorías con imágenes
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
                                <div class="d-flex flex-wrap">${imageHTML}</div>
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

    // Búsqueda de palabra
    searchBtn.addEventListener('click', () => {
        const query = searchInput.value.trim();
        if (!query) {
            alert('Por favor ingresa una palabra.');
            return;
        }

        categoriesContainer.innerHTML = ""; // Ocultar categorías
        container.innerHTML = '<p class="text-muted">Buscando...</p>';

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
                container.innerHTML = ''; // Limpiar antes

                if (data.length === 0) {
                    container.innerHTML = '<p class="text-muted">No se encontraron resultados.</p>';
                    return;
                }

                data.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-4';

                    card.innerHTML = `
                        <div class="card h-100 shadow-sm">
                            ${item.image_url ? `<img src="${item.image_url}" class="card-img-top" alt="Imagen de ${item.word_in}">` : ''}
                            <div class="card-body">
                                <h5 class="card-title">${item.word_in}</h5>
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
