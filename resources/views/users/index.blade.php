<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <!--- Basic Page Needs ========================================= -->
        <title>🙋🏼‍♂️ Users</title>
        @include('partials.meta')

        <!-- Favicons ================================================== -->
        <link rel="shortcut icon" href="favicon.png">

        <!-- JS + CSS ================================================== -->
        @include('partials.css_js')

        <!-- USERS WITH DATATABLES JS================================================== -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

        <!-- USERS CUSTOM CSS ================================================== -->
        <link rel="stylesheet" href="css/local/private/users.css">
        <link rel="stylesheet" href="css/styles.css">

    </head>
    <body>
        <!-- Overlay to block the page during the loading ============== -->
        @include('partials.overlay')

        <!-- Header ==================================================== -->
        <header>
            <div class="header-content">
                <h1 class="header-title">🙋🏼‍♂️ Users ( Elasticsearch + Datatables )</h1>
                <div class="clear-both"></div>
                <div class="button-container">
                    <a href="/home">
                        <button class="adminBtn">
                            🏠 Home
                        </button>
                    </a>
                    <a href="/lang">
                        <button class="adminBtn">
                            📝 Translations
                        </button>
                    </a>
                    <a href="/locations">
                        <button class="adminBtn">
                            🗺️ Locations
                        </button>
                    </a>
                    @include('partials.logout')
                </div>
            </div>
        </header> <!-- Header End -->

        <div class="search-add-user-container">
            <div class="float-right">
                <button class="open-modal-btn" onclick="openModal()">➕ Add User</button>
            </div>
            <div class="float-right">
                <label for="query"></label>
                <input type="text" id="query" placeholder="🔎 Search...">
            </div>
            <div class="clear-both"></div>
        </div>

        <!-- List ====================================================== -->
        <section class="user-list-section">
            <table id="user-table" class="display user-table">
                <!-- Table headers -->
                <thead>
                <tr class="table-header">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th></th>
                </tr>
                </thead>
                <!-- Table body for users -->
                <tbody></tbody>
            </table>
        </section>

        <!--  Footer ================================================== -->
        <footer>
            @include('partials.cookies')
        </footer> <!-- Footer End-->

        <!-- Get users data ============================== -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

        <!-- add modal -->
        <div id="userModal" class="modal">
            <!-- modal content -->
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <p class="modal-title">➕ Add user</p>
                <form class="user-form" id="add-user-form">
                    <label for="new-user-name">Name:</label>
                    <input class="user-name" type="text" id="new-user-name" name="name" required>
                    <label for="new-user-email">Email:</label>
                    <input class="user-email" type="email" id="new-user-email" name="email" required>
                    <div class="setPassEditInput">
                        <label for="new-user-password">Password:</label>
                        <input class="user-password" type="password" id="new-user-password" name="password" required>
                    </div>
                    <label for="new-user-role">Role:</label> <br>
                    <select id="new-user-role" name="role">
                        <option value="admin">Admin</option>
                        <option value="default">Default</option>
                    </select>
                    <br><br>
                    <button type="button" onclick="validate('add')">Confirm</button>
                </form>
            </div>
        </div>

        <!-- update modal -->
        <div id="userModalUpdate" class="modal">
            <!-- modal content -->
            <div class="modal-content">
                <span class="close" onclick="closeModalUpdate()">&times;</span>
                <p class="modal-title">📝 Edit user</p>
                <form class="user-form-update" id="update-user-form">
                    <input type="hidden" id="update-user-id" name="id" required>
                    <label for="update-user-name">Name:</label>
                    <input class="user-name-update" type="text" id="update-user-name" name="name" required>
                    <label for="update-user-email">Email:</label>
                    <input class="user-email-update" type="email" id="update-user-email" name="email" required>
                    <br>
                    <label for="update-user-role">Role:</label> <br>
                    <select id="update-user-role" name="role">
                        <option value="admin">Admin</option>
                        <option value="default">Default</option>
                    </select>
                    <br>
                    <button type="button" onclick="showPassInput()">New password</button>
                    <div class="setPassEditInput-update">
                        <label for="update-user-password">Password:</label>
                        <input class="user-password-update" type="password" id="update-user-password" name="password">
                    </div>
                    <button class="confirm-button" type="button" onclick="validate('update')">Confirm</button>
                </form>
            </div>
        </div>

        <!-- delete modal -->
        <div id="userModalDelete" class="modal">
            <!-- modal content -->
            <div class="modal-content">
                <span class="close" onclick="closeModalDelete()">&times;</span>
                <p class="modal-title">❌ Delete</p>
                <p class="modal-warning">( If you confirm, the user will be deleted forever )</p>
                <form id="delete-user-form">
                    <input type="hidden" id="delete-user-id" name="id" required>
                    <button class="delete-button" type="button" onclick="deleteUser()">Confirm</button>
                </form>
            </div>
        </div>
    </body>
</html>
