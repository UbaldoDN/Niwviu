<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear y Administrar Prestamos de Libros</title>

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
    <h1>Crear Prestamo</h1>
    <form id="userBookForm">
        <div class="form-group">
            <label for="users">Usuarios:</label>
            <select id="users" name="users">
                <!-- Opciones de usuarios se cargarán dinámicamente -->
            </select>
        </div>    
        <div class="form-group">
            <label for="books">Libros:</label>
            <select id="books" name="books">
                <!-- Opciones de libros se cargarán dinámicamente -->
            </select>
        </div>
        <button type="submit">Autorizar préstamo</button>
    </form>
    <div id="message"></div>

    <h1>Lista de Prestamos</h1>
    <ul id="userBookList"></ul>
</div>

<!-- Modal para editar préstamo -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Préstamo</h2>
        <form id="editUserBookForm">
            <input type="hidden" id="editUserId" name="id">
            <div class="form-group">
                <label for="editUsers">Usuarios:</label>
                <select id="editUsers" name="users">
                    <!-- Opciones de usuarios se cargarán dinámicamente -->
                </select>
            </div>
            <input type="hidden" id="editBookId" name="id">
            <div class="form-group">
                <label for="editBooks">Libros:</label>
                <select id="editBooks" name="books">
                    <!-- Opciones de libros se cargarán dinámicamente -->
                </select>
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>
<input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo base_url(); ?>">

<script>
    const API_URL = document.getElementById('baseUrl').value + 'api/v1';
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('userBookForm');
        const messageDiv = document.getElementById('message');
        const userBookList = document.getElementById('userBookList');
        const usersSelect = document.getElementById('users');
        const booksSelect = document.getElementById('books');
        
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editUserBookForm');
        const editUserIdInput = document.getElementById('editUserId');
        const editBookIdInput = document.getElementById('editBookId');
        const editUsersSelect = document.getElementById('editUsers');
        const editBooksSelect = document.getElementById('editBooks');
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
        const loadUsersBooks = async () => {
            try {
                const response = await fetch(`${API_URL}/users/`);
                const users = await response.json();

                usersSelect.innerHTML = '';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    usersSelect.appendChild(option);
                });

                // Cargar libros disponibles en el menú desplegable
                const books = await fetchBooks();

                booksSelect.innerHTML = '';
                books.forEach(book => {
                    const option = document.createElement('option');
                    option.value = book.id;
                    option.textContent = `${book.name} (${book.author})`;
                    booksSelect.appendChild(option);
                });

                userBookList.innerHTML = '';
                users.forEach(user => {
                    const li = document.createElement('li');
                    if (user.books.length > 0) {
                        let booksName = '';
                        user.books.forEach(book => {
                            booksName += `${book.name} (${book.author}), `;
                        });
                        booksName = booksName.slice(0, -2);
                        li.innerHTML = `
                            <span><strong>${user.name}</strong> - ${user.email} Libros: <strong>${booksName}</strong></span>
                            <button class="getBack" data-id="${user.id}">Regresar préstamo</button>
                        `;
                        userBookList.appendChild(li);
                    }
                });
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar los libros</p>';
            }
        };

        // Cargar libros al cargar la página
        loadUsersBooks();

        // Event listener para el formulario de creación de libros
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const userId = Array.from(document.getElementById('users').selectedOptions).map(option => option.value);
            const bookId = parseInt(Array.from(document.getElementById('books').selectedOptions).map(option => option.value));

            try {
                const response = await fetch(`${API_URL}/users/${userId}/borrowedBook`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ bookId })
                });

                if (!response.ok) {
                    throw response;
                }

                // Limpiar los campos del formulario después de una creación exitosa
                document.getElementById('users').selectedIndex = -1;
                document.getElementById('books').selectedIndex = -1;

                // Recargar la lista de libros
                loadUsersBooks();

                // Mostrar mensaje de éxito
                messageDiv.innerHTML = '<p class="success">Préstamo creado exitosamente</p>';
            } catch (error) {
                error.json()
                    .then(msg => {
                        let errorMessage = '';
                        const bookIdError = JSON.stringify(msg.message.bookId, null, 2);
                        if (bookIdError != undefined) {
                            errorMessage += `<p class="error">${bookIdError}</p>`;
                        }
                        console.error('Error book:', bookIdError);
                        messageDiv.innerHTML = `<p class="error">Hubo un error al actualizar el usuario</p>${errorMessage}`;
	                });
            }
        });

        // Event listener para abrir el modal de edición al hacer clic en el botón "Actualizar"
        userBookList.addEventListener('click', async (e) => {
            if (e.target.classList.contains('getBack')) {
                const userId = e.target.dataset.id;
                const user = await fetchUser(userId);
                if (user) {
                    editUserIdInput.value = user.id;
                    // Cargar usuarios disponible en el menú desplegable
                    editUsersSelect.innerHTML = '';
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    editUsersSelect.appendChild(option);

                    // Cargar libros disponibles en el menú desplegable
                    editBooksSelect.innerHTML = '';
                    user.books.forEach(book => {
                        const option = document.createElement('option');
                        option.value = book.id;
                        option.textContent = `${book.name} (${book.author})`;
                        editBooksSelect.appendChild(option);
                    });

                    openEditModal();
                }
            }
        });

        // Función para cargar un libro específico
        const fetchUser = async (id) => {
            try {
                const response = await fetch(`${API_URL}/users/${id}`);
                const user = await response.json();
                return user;
            } catch (error) {
                console.error('Error al obtener un libro:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar el libro</p>';
                return null;
            }
        };

        // Función para cargar los usuarios
        const fetchUsers = async () => {
            try {
                const response = await fetch(`${API_URL}/users`);
                const users = await response.json();
                return users;
            } catch (error) {
                console.error('Error al cargar los usuarios:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar los usuarios</p>';
                return [];
            }
        };

        // Función para cargar los libros disponibles
        const fetchBooks = async () => {
            try {
                const response = await fetch(`${API_URL}/books`);
                const books = await response.json();
                return books;
            } catch (error) {
                console.error('Error al cargar los libros:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al cargar los libros</p>';
                return [];
            }
        };

        // Event listener para el formulario de edición de libros
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const userId = Array.from(editUsersSelect.selectedOptions).map(option => option.value);
            const bookId = parseInt(Array.from(editBooksSelect.selectedOptions).map(option => option.value));
            try {
                const response = await fetch(`${API_URL}/users/${userId}/getBackBorrowedBook`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ bookId })
                });

                if (!response.ok) {
                    throw response;
                }

                // Cerrar el modal de edición
                closeEditModal();

                // Recargar la lista de prestamos
                loadUsersBooks();

                // Mostrar mensaje de éxito
                messageDiv.innerHTML = '<p class="success">Préstamo actualizado exitosamente</p>';
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = '<p class="error">Hubo un error al actualizar el préstamo</p>';
            }
        });
    });

</script>
</body>
</html>
