<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear y Administrar Categorías</title>
<!-- STYLES -->
<style {csp-style-nonce}>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #333;
        color: #fff;
        padding: 10px 0;
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    nav ul li {
        display: inline;
        margin-right: 20px;
    }

    nav ul li a {
        color: #fff;
        text-decoration: none;
        font-size: 18px;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
    }

    h1 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    form {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    select {
        width: 100%;
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
    }

    .update {
        background-color: #008CBA;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .update:hover {
        background-color: #005f7f;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 5px;
        width: 80%;
        max-width: 600px;
        position: relative;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="https://niwviu.mx/categories">Categorías</a></li>
            <li><a href="https://niwviu.mx/books">Libros</a></li>
            <li><a href="https://niwviu.mx/users">Usuarios</a></li>
            <li><a href="https://niwviu.mx/borrowedBook">Prestamos de Libros</a></li>
        </ul>
    </nav>
</header>
<div class="container">
    <h1>Crear Categoría</h1>
    <form id="categoryForm">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name">
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <button type="submit">Crear</button>
    </form>
    <div id="message"></div>

    <h1>Lista de Categorías</h1>
    <ul id="categoryList"></ul>
</div>

<!-- Modal para editar categoría -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Categoría</h2>
        <form id="editCategoryForm">
            <input type="hidden" id="editCategoryId" name="id">
            <div class="form-group">
                <label for="editName">Nombre:</label>
                <input type="text" id="editName" name="name">
            </div>
            <div class="form-group">
                <label for="editDescription">Descripción:</label>
                <textarea id="editDescription" name="description"></textarea>
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
    const API_URL = 'https://niwviu.mx/api/v1';
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('categoryForm');
        const messageDiv = document.getElementById('message');
        const categoryList = document.getElementById('categoryList');
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editCategoryForm');
        const editNameInput = document.getElementById('editName');
        const editDescriptionInput = document.getElementById('editDescription');
        const editCategoryIdInput = document.getElementById('editCategoryId');
        const closeEditModalBtn = document.querySelector('#editModal .close');

        // Mostrar modal para editar categoría
        const openEditModal = () => {
            editModal.style.display = 'block';
        };

        // Cerrar modal para editar categoría
        const closeEditModal = () => {
            editModal.style.display = 'none';
        };
        
        // Event listener para cerrar modal al hacer clic en la 'x'
        closeEditModalBtn.addEventListener('click', closeEditModal);

        // Event listener para cerrar modal al hacer clic fuera del modal
        window.addEventListener('click', (e) => {
            if (e.target == editModal) {
                closeEditModal();
            }
        });

        // Función para cargar y mostrar la lista de categorías
        const loadCategories = async () => {
            try {
                const response = await fetch(`${API_URL}/categories`);
                const categories = await response.json();

                categoryList.innerHTML = '';

                categories.forEach(category => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <span><strong>${category.name}</strong>: ${category.description}</span>
                        <button class="update" data-id="${category.id}">Actualizar</button>
                        <button class="delete" data-id="${category.id}">Eliminar</button>
                    `;
                    categoryList.appendChild(li);

                    // Event listener para botones de actualizar
                    li.querySelector('.update').addEventListener('click', () => {
                        const categoryName = document.getElementById('name');
                        const categoryDescription = document.getElementById('description');

                        categoryName.value = category.name;
                        categoryDescription.value = category.description;
                    });

                    // Event listener para botones de eliminar
                    li.querySelector('.delete').addEventListener('click', async () => {
                        try {
                            const response = await fetch(`${API_URL}/categories/${category.id}`, {
                                method: 'DELETE'
                            });

                            if (!response.ok) {
                                throw response;
                            }

                            li.remove();
                            messageDiv.innerHTML = '<p class="success">Categoría eliminada exitosamente</p>';
                        } catch (error) {
                            error.json().then(msg => {
                                let errorMessage = '';
                                    const categoryError = JSON.stringify(msg.message.category, null, 2);
                                    if (categoryError != undefined) {
                                        errorMessage += `<p class="error">${categoryError}</p>`;
                                    }
                                    console.error('Error category:', categoryError);
                                    messageDiv.innerHTML = `<p class="error">Hubo un error al crear el usuario: </p>${errorMessage}`;
                            });
                        }
                    });
                });
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar las categorías</p>';
            }
        };

        // Cargar categorías al cargar la página
        loadCategories();

        // Event listener para el formulario de creación de categorías
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;

            try {
                const response = await fetch(`${API_URL}/categories`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, description })
                });

                if (!response.ok) {
                    throw response;
                }

                const data = await response.json();
                messageDiv.innerHTML = '<p class="success">Categoría creada exitosamente</p>';

                // Limpiar los campos del formulario después de una creación exitosa
                document.getElementById('name').value = '';
                document.getElementById('description').value = '';

                // Recargar la lista de categorías
                loadCategories();
            } catch (error) {
                error.json()
                    .then(msg => {
                        let errorMessage = '';
                        const nameError = JSON.stringify(msg.message.name, null, 2);
                        const descriptionError = JSON.stringify(msg.message.description, null, 2);
                        if (nameError != undefined) {
                            errorMessage += `<p class="error">${nameError}</p>`;
                        }

                        if (descriptionError != undefined) {
                            errorMessage += `<p class="error">${descriptionError}</p>`;
                        }
                        console.error('Error name:', nameError);
                        console.error('Error descripción:', descriptionError);
                        messageDiv.innerHTML = `<p class="error">Hubo un error al crear la categoría: </p>${errorMessage}`;
	                });
            }
        });
    
        // Event listener para abrir el modal de edición al hacer clic en el botón "Actualizar"
        categoryList.addEventListener('click', async (e) => {
            if (e.target.classList.contains('update')) {
                const categoryId = e.target.dataset.id;
                const span = e.target.parentElement.querySelector('span').textContent.split(':');
                const categoryName =  span[0].trim();
                const categoryDescription = span[1].trim();
                editCategoryIdInput.value = categoryId;
                editNameInput.value = categoryName;
                editDescriptionInput.value = categoryDescription;

                openEditModal();
            }
        });

        // Event listener para el formulario de edición de categorías
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = editCategoryIdInput.value;
            const name = editNameInput.value;
            const description = editDescriptionInput.value;

            try {
                const response = await fetch(`${API_URL}/categories/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, description })
                });

                if (!response.ok) {
                    throw response;
                }

                // Cerrar el modal de edición
                closeEditModal();

                // Recargar la lista de categorías
                loadCategories();

                // Mostrar mensaje de éxito
                messageDiv.innerHTML = '<p class="success">Categoría actualizada exitosamente</p>';
            } catch (error) {
                error.json().
                    then(msg => {
                        let errorMessage = '';
                        const nameError = JSON.stringify(msg.message.name, null, 2);
                        const descriptionError = JSON.stringify(msg.message.description, null, 2);
                        if (nameError != undefined) {
                            errorMessage += `<p class="error">${nameError}</p>`;
                        }

                        if (descriptionError != undefined) {
                            errorMessage += `<p class="error">${descriptionError}</p>`;
                        }
                        console.error('Error name:', nameError);
                        console.error('Error email:', descriptionError);
                        messageDiv.innerHTML = `<p class="error">Hubo un error al actualizar la categoría</p>${errorMessage}`;
	                });
            }
        });
    });
</script>
</body>
</html>