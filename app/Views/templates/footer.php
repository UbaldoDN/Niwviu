<footer>
    <div class="environment">

        <p>Page rendered in {elapsed_time} seconds time</p>

        <p>Environment: <?= ENVIRONMENT ?></p>

    </div>

    <div class="copyrights">

        <p>&copy; <?= date('Y') ?> CodeIgniter Foundation. CodeIgniter is open source project released under the MIT
            open source licence.</p>

    </div>

</footer>
<input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo base_url(); ?>">

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById("menuToggle").addEventListener('click', toggleMenu);
        function toggleMenu() {
            var menuItems = document.getElementsByClassName('menu-item');
            for (var i = 0; i < menuItems.length; i++) {
                var menuItem = menuItems[i];
                menuItem.classList.toggle("hidden");
            }
        }
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const url = document.getElementById('baseUrl').value;
        const API_URL = `${url}api/v1`;
        let hrefIndex = document.getElementById('hrefIndex');
        let books = document.getElementById('hrefBooks');
        let categories = document.getElementById('hrefCategories');
        let users = document.getElementById('hrefUsers');
        let borrowedBooks = document.getElementById('hrefBorrowedBooks');
        hrefIndex.href = `${url}books`;
        books.href = `${url}books`;
        categories.href = `${url}categories`;
        users.href = `${url}users`;
        borrowedBooks.href = `${url}borrowedBook`;

        <?= $this->renderSection('scripts'); ?>
    });
</script>
</body>
</html>
