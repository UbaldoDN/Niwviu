<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear y Administrar Libros</title>

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
        <li><a id="hrefCategories">Categorías</a></li>
            <li><a id="hrefBooks">Libros</a></li>
            <li><a id="hrefUsers">Usuarios</a></li>
            <li><a id="hrefBorrowedBooks">Prestamos de Libros</a></li>
        </ul>
    </nav>
</header>
<div class="container">
    <h1>Crear Libro</h1>
    <form id="bookForm">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name">
        </div>
        <div class="form-group">
            <label for="author">Autor:</label>
            <input type="text" id="author" name="author">
        </div>
        <div class="form-group">
            <label for="publishedAt">Fecha de Publicación:</label>
            <input type="date" id="publishedAt" name="publishedAt">
        </div>
        <div class="form-group">
            <label for="categories">Categorías:</label>
            <select id="categories" name="categories" multiple>
                <!-- Opciones de categorías se cargarán dinámicamente -->
            </select>
        </div>
        <button type="submit">Crear</button>
    </form>
    <div id="message"></div>

    <h1>Lista de Libros</h1>
    <ul id="bookList"></ul>
</div>

<!-- Modal para editar libro -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Libro</h2>
        <form id="editBookForm">
            <input type="hidden" id="editBookId" name="id">
            <div class="form-group">
                <label for="editName">Nombre:</label>
                <input type="text" id="editName" name="name">
            </div>
            <div class="form-group">
                <label for="editAuthor">Autor:</label>
                <input type="text" id="editAuthor" name="author">
            </div>
            <div class="form-group">
                <label for="editPublishedAt">Fecha de Publicación:</label>
                <input type="date" id="editPublishedAt" name="editPublishedAt">
            </div>
            <div class="form-group">
                <label for="editCategories">Categorías:</label>
                <select id="editCategories" name="categories" multiple>
                    <!-- Opciones de categorías se cargarán dinámicamente -->
                </select>
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>
<input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo base_url(); ?>">

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const url = document.getElementById('baseUrl').value;
        const API_URL = `${url}api/v1`;

        let books = document.getElementById('hrefBooks');
        let categories = document.getElementById('hrefCategories');
        let users = document.getElementById('hrefUsers');
        let borrowedBooks = document.getElementById('hrefBorrowedBooks');
        books.href = `${url}books`;
        categories.href = `${url}categories`;
        users.href = `${url}users`;
        borrowedBooks.href = `${url}borrowedBook`;

        const form = document.getElementById('bookForm');
        const messageDiv = document.getElementById('message');
        const bookList = document.getElementById('bookList');
        const categoriesSelect = document.getElementById('categories');
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editBookForm');
        const editNameInput = document.getElementById('editName');
        const editAuthorInput = document.getElementById('editAuthor');
        const editBookIdInput = document.getElementById('editBookId');
        const editPublishedAtInput = document.getElementById('editPublishedAt');
        const editCategoriesSelect = document.getElementById('editCategories');
        const closeEditModalBtn = document.querySelector('#editModal .close');

        // Mostrar modal para editar libro
        const openEditModal = () => {
            editModal.style.display = 'block';
        };

        // Cerrar modal para editar libro
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

        // Función para cargar y mostrar la lista de libros
        const loadBooks = async () => {
            try {
                const response = await fetch(`${API_URL}/books`);
                const books = await response.json();

                // Cargar categorías disponibles en el menú desplegable
                const categories = await fetchCategories();
                categoriesSelect.innerHTML = '';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = `${category.name} (${category.description})`;
                    categoriesSelect.appendChild(option);
                });

                bookList.innerHTML = '';
                books.forEach(book => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <span><strong>${book.name}</strong> - ${book.author} (${book.publishedAt})</span>
                        <button class="update" data-id="${book.id}">Actualizar</button>
                        <button class="delete" data-id="${book.id}">Eliminar</button>
                    `;
                    bookList.appendChild(li);

                    // Event listener para botones de eliminar
                    li.querySelector('.delete').addEventListener('click', async () => {
                        try {
                            const response = await fetch(`${API_URL}/books/${book.id}`, {
                                method: 'DELETE'
                            });

                            if (!response.ok) {
                                throw new Error('Error eliminando el libro.');
                            }

                            li.remove();
                            messageDiv.innerHTML = '<p class="success">Libro eliminado exitosamente</p>';
                        } catch (error) {
                            console.error('Error:', error);
                            messageDiv.innerHTML = '<p class="error">Hubo un error al eliminar el libro</p>';
                        }
                    });
                });
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar los libros</p>';
            }
        };

        // Cargar libros al cargar la página
        loadBooks();

        // Event listener para el formulario de creación de libros
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const author = document.getElementById('author').value;
            const publishedAt = document.getElementById('publishedAt').value + ' 00:00:00';
            const categories = Array.from(document.getElementById('categories').selectedOptions).map(option => option.value);
    
            try {
                const response = await fetch(`${API_URL}/books`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, author, publishedAt, categories })
                });

                if (!response.ok) {
                    throw response;
                }

                // Limpiar los campos del formulario después de una creación exitosa
                document.getElementById('name').value = '';
                document.getElementById('author').value = '';
                document.getElementById('publishedAt').value = '';
                document.getElementById('categories').selectedIndex = -1;

                // Recargar la lista de libros
                loadBooks();

                // Mostrar mensaje de éxito
                messageDiv.innerHTML = '<p class="success">Libro creado exitosamente</p>';
            } catch (error) {
                error.json().then(msg => {
                    let errorMessage = '';
                        const categoriesError = JSON.stringify(msg.message.categories, null, 2);
                        if (categoriesError != undefined) {
                            errorMessage += `<p class="error">${categoriesError}</p>`;
                        }
                        console.error('Error categories:', categoriesError);
                        messageDiv.innerHTML = `<p class="error">Hubo un error al crear un libro: </p>${errorMessage}`;
                });
            }
        });

        // Event listener para abrir el modal de edición al hacer clic en el botón "Actualizar"
        bookList.addEventListener('click', async (e) => {
            if (e.target.classList.contains('update')) {
                const bookId = e.target.dataset.id;
                const book = await fetchBook(bookId);

                if (book) {
                    editBookIdInput.value = book.id;
                    editNameInput.value = book.name;
                    editAuthorInput.value = book.author;
                    editPublishedAtInput.value = book.publishedAt;

                    // Cargar categorías disponibles en el menú desplegable
                    const categories = await fetchCategories();
                    editCategoriesSelect.innerHTML = '';
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = `${category.name} (${category.description})`;
                        if (book.categories.find((element) => element.id === category.id)) {
                            option.selected = true;
                        }
                        editCategoriesSelect.appendChild(option);
                    });

                    openEditModal();
                }
            }
        });

        // Función para cargar un libro específico
        const fetchBook = async (id) => {
            try {
                const response = await fetch(`${API_URL}/books/${id}`);
                const book = await response.json();
                return book;
            } catch (error) {
                console.error('Error al obtener un libro:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar el libro</p>';
                return null;
            }
        };

        // Función para cargar las categorías disponibles
        const fetchCategories = async () => {
            try {
                const response = await fetch(`${API_URL}/categories`);
                const categories = await response.json();
                return categories;
            } catch (error) {
                console.error('Error al cargar las categorías:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar las categorías</p>';
                return [];
            }
        };

        // Event listener para el formulario de edición de libros
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = editBookIdInput.value;
            const name = editNameInput.value;
            const author = editAuthorInput.value;
            const publishedAt = editPublishedAtInput.value + ' 00:00:00';
            const categories = Array.from(editCategoriesSelect.selectedOptions).map(option => option.value);

            try {
                const response = await fetch(`${API_URL}/books/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, author, publishedAt, categories })
                });

                if (!response.ok) {
                    throw response;
                }

                // Cerrar el modal de edición
                closeEditModal();

                // Recargar la lista de libros
                loadBooks();

                // Mostrar mensaje de éxito
                messageDiv.innerHTML = '<p class="success">Libro actualizado exitosamente</p>';
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

</script>
</body>
</html>
