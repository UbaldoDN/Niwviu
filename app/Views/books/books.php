<?= $this->extend('templates/layout'); ?>
<?= $this->section('title'); ?>
Libros
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
    <div class="container">
        <h1>Crear Libro</h1>
        <form id="form">
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
        <ul id="list"></ul>
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
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
    const categoriesSelect = document.getElementById('categories');

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
    list.addEventListener('click', async (e) => {
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

        const name = document.getElementById('editName').value;
        const author = document.getElementById('editAuthor').value;
        const id = document.getElementById('editBookId').value;
        const publishedAt = document.getElementById('editPublishedAt').value + ' 00:00:00';
        const categories = Array.from(document.getElementById('editCategories').selectedOptions).map(option => option.value);

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

            document.getElementById('editName').value = '';
            document.getElementById('editAuthor').value = '';
            document.getElementById('editPublishedAt').value = '';
            document.getElementById('editCategories').selectedIndex = -1;
            
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
<?= $this->endSection(); ?>
