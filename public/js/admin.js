document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formAddWord");
    const tableWords = document.getElementById("tableWords");

    loadWords();

    // Crear o editar
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const action = form.dataset.editing === "true" ? "editWord" : "addWord";
        if (action === "editWord") {
            formData.append("id", form.dataset.wordId);
        }

        fetch(`../server/controller/Controller.php?action=${action}`, {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    form.reset();
                    form.dataset.editing = "false";
                    loadWords();
                } else {
                    alert("Error: " + data.message);
                }
            });
    });

    // Leer palabras
    function loadWords() {
        fetch("../server/controller/Controller.php?action=getWords")
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    renderWords(data);
                } else {
                    tableWords.innerHTML = "<p>No se pudieron cargar las palabras.</p>";
                }
            });
    }

    // Renderizar tabla
    function renderWords(words) {
        if (words.length === 0) {
            tableWords.innerHTML = "<p>No hay palabras registradas.</p>";
            return;
        }

        let html = `<table class="table table-bordered">
            <thead>
                <tr>
                    <th>Inglés</th>
                    <th>Español</th>
                    <th>Categoría</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>`;

        words.forEach(word => {
            html += `<tr>
                <td>${word.english}</td>
                <td>${word.spanish}</td>
                <td>${word.category}</td>
                <td><img src="${word.image_url}" style="width:60px;"></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editWord(${encodeURIComponent(JSON.stringify(word))})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteWord(${word.id})">Eliminar</button>
                </td>
            </tr>`;
        });

        html += "</tbody></table>";
        tableWords.innerHTML = html;
    }

    // Eliminar palabra
    window.deleteWord = function (id) {
        if (!confirm("¿Seguro que deseas eliminar esta palabra?")) return;

        const formData = new FormData();
        formData.append("id", id);

        fetch("../server/controller/Controller.php?action=deleteWord", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadWords();
            });
    };

    // Editar palabra (rellena el formulario)
    window.editWord = function (wordJSON) {
        const word = JSON.parse(wordJSON);
        document.getElementById("english").value = word.english;
        document.getElementById("spanish").value = word.spanish;
        document.getElementById("category").value = word.category;
        document.getElementById("image_url").value = word.image_url;

        form.dataset.editing = "true";
        form.dataset.wordId = word.id;
    };
});
