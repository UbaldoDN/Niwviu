<?= $this->extend('templates/layout'); ?>
<?= $this->section('title'); ?>
Usuarios
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
    <div class="container">
        <h1>Crear Usuario</h1>
        <form id="userForm">
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <button type="submit">Crear</button>
        </form>
        <div id="message"></div>

        <h1>Lista de Usuarios</h1>
        <ul id="userList"></ul>
    </div>

    <!-- Modal para editar usuario -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Usuario</h2>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="id">
                <div class="form-group">
                    <label for="editName">Nombre:</label>
                    <input type="text" id="editName" name="name">
                </div>
                <div class="form-group">
                    <label for="editEmail">Email:</label>
                    <input type="email" id="editEmail" name="email">
                </div>
                <button type="submit">Guardar Cambios</button>
            </form>
            <div id="messageModal"></div>
        </div>
    </div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
    const form = document.getElementById('userForm');
    const messageDiv = document.getElementById('message');
    const messageModal = document.getElementById('messageModal');
    const userList = document.getElementById('userList');
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editUserForm');
    const editNameInput = document.getElementById('editName');
    const editEmailInput = document.getElementById('editEmail');
    const editUserIdInput = document.getElementById('editUserId');
    const closeEditModalBtn = document.querySelector('#editModal .close');

    // Mostrar modal para editar usuario
    const openEditModal = () => {
        editModal.style.display = 'block';
    };

    // Cerrar modal para editar usuario
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
    const loadUsers = async () => {
        try {
            const response = await fetch(`${API_URL}/users`);
            const users = await response.json();

            userList.innerHTML = '';

            users.forEach(user => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span><strong>${user.name}</strong>: ${user.email}</span>
                    <button class="update" data-id="${user.id}">Actualizar</button>
                    <button class="delete" data-id="${user.id}">Eliminar</button>
                `;
                userList.appendChild(li);

                // Event listener para botones de actualizar
                li.querySelector('.update').addEventListener('click', () => {
                    const userName = document.getElementById('name');
                    const userEmail = document.getElementById('email');

                    userName.value = user.name;
                    userEmail.value = user.email;
                });

                // Event listener para botones de eliminar
                li.querySelector('.delete').addEventListener('click', async () => {
                    try {
                        const response = await fetch(`${API_URL}/users/${user.id}`, {
                            method: 'DELETE'
                        });

                        if (!response.ok) {
                            throw response;
                        }

                        li.remove();
                        messageDiv.innerHTML = '<p class="success">Usuario eliminado exitosamente</p>';
                    } catch (error) {
                        error.json().then(msg => {
                            let errorMessage = '';
                            const idError = JSON.stringify(msg.message.id, null, 2);
                            if (idError != undefined) {
                                errorMessage += `<p class="error">${idError}</p>`;
                            }
                            console.error('Error id:', idError);
                            messageDiv.innerHTML = `<p class="error">Hubo un error al crear el usuario: </p>${errorMessage}`;
                        });
                    }
                });
            });
        } catch (error) {
            console.error('Error:', error);
            messageDiv.innerHTML = '<p class="error">Hubo un error al cargar los usuarios.</p>';
        }
    };

    // Cargar categorías al cargar la página
    loadUsers();

    // Event listener para el formulario de creación de categorías
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;

        try {
            const response = await fetch(`${API_URL}/users`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, email })
            });

            if (!response.ok) {
                throw response;
            }

            const data = await response.json();
            messageDiv.innerHTML = '<p class="success">Usuario creado exitosamente</p>';

            // Limpiar los campos del formulario después de una creación exitosa
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';

            // Recargar la lista de categorías
            loadUsers();
        } catch (error) {
            error = error.json();
            error.json()
                .then(msg => {
                    let errorMessage = '';
                    const nameError = JSON.stringify(msg.message.name, null, 2);
                    const emailError = JSON.stringify(msg.message.email, null, 2);
                    if (nameError != undefined) {
                        errorMessage += `<p class="error">${nameError}</p>`;
                    }

                    if (emailError != undefined) {
                        errorMessage += `<p class="error">${emailError}</p>`;
                    }
                    console.error('Error name:', nameError);
                    console.error('Error descripción:', emailError);
                    messageDiv.innerHTML = `<p class="error">Hubo un error al crear el usuario: </p>${errorMessage}`;
                });
        }
    });

    // Event listener para abrir el modal de edición al hacer clic en el botón "Actualizar"
    userList.addEventListener('click', async (e) => {
        if (e.target.classList.contains('update')) {
            const userId = e.target.dataset.id;
            const span = e.target.parentElement.querySelector('span').textContent.split(':');
            const userName = span[0].trim();
            const userEmail = span[1].trim();
            editUserIdInput.value = userId;
            editNameInput.value = userName;
            editEmailInput.value = userEmail;

            openEditModal();
        }
    });

    // Event listener para el formulario de edición de usuarios
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = editUserIdInput.value;
        const name = editNameInput.value;
        const email = editEmailInput.value;

        try {
            const response = await fetch(`${API_URL}/users/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, email })
            });

            if (!response.ok) {
                throw response;
            }

            // Cerrar el modal de edición
            closeEditModal();

            // Recargar la lista de usuarios
            loadUsers();

            // Mostrar mensaje de éxito
            messageDiv.innerHTML = '<p class="success">Usuario actualizado exitosamente</p>';
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
        } catch (error) {
            error.json()
                .then(msg => {
                    let errorMessage = '';
                    const nameError = JSON.stringify(msg.message.name, null, 2);
                    const emailError = JSON.stringify(msg.message.email, null, 2);
                    if (nameError != undefined) {
                        errorMessage += `<p class="error">${nameError}</p>`;
                    }

                    if (emailError != undefined) {
                        errorMessage += `<p class="error">${emailError}</p>`;
                    }
                    console.error('Error name:', nameError);
                    console.error('Error email:', emailError);
                    messageModal.innerHTML = `<p class="error">Hubo un error al actualizar el usuario</p>${errorMessage}`;
                });
        }
    });
<?= $this->endSection(); ?>
