<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <!--- Basic Page Needs ========================================= -->
    <title>Users</title>
    @include('partials.meta')

    <!-- Favicons ================================================== -->
    <link rel="shortcut icon" href="favicon.png" >

    <!-- JS + CSS ================================================== -->
    @include('partials.css_js')

    <!-- USERS TABLE WITH DATATABLES ================================================== -->
</head>
<body>
<!-- Overlay to block the page during the loading ============== -->
@include('partials.overlay')

<!-- Header ==================================================== -->
<header>
    <div class="header-content">
        <h1>Users</h1>
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

<!-- List ====================================================== -->
<section style="color:white; z-index: 9999999;position: absolute;top:80px;left:90px;">
 <!--   <h2>User List</h2> -->
    <table id="user-table" class="display">
        <!-- Table headers -->
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <!-- Table body for users -->
        <tbody>
        </tbody>
    </table>
</section>

<!-- Form to add new user -->
<section style="color:white; z-index: 9999999;position: absolute;top:500px;left:90px;">
    <h2>Add New User</h2>
    <form id="add-user-form">
        <label for="new-user-name">Name:</label>
        <input type="text" id="new-user-name" name="name" required>
        <label for="new-user-email">Email:</label>
        <input type="email" id="new-user-email" name="email" required>
        <label for="new-user-role">Role:</label>
        <input type="email" id="new-user-role" name="role">
        <label for="new-user-password">Password:</label>
        <input type="email" id="new-user-password" name="password" required>
        <button type="button" onclick="addUser()">Add User</button>
    </form>
</section>

<!-- Footer ================================================== -->
<footer>
    @include('partials.cookies')
</footer> <!-- Footer End-->

<!-- Get users data ============================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>

        $(document).ready(function()
        {
            // Check authentication, get translation data from the server and create the fields
            serverLessRequests.checkAuthAndGetData('/api/v1/users-es').then(response =>
            {
                // Populate DataTable with received data
                $('#user-table').DataTable({
                    data: response.data.users,
                    columns: [
                        { data: 'name' },
                        { data: 'email' },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<button onclick="editUser(' + row.id + ')">Edit</button>' +
                                       '<button onclick="deleteUser(' + row.id + ')">Delete</button>';
                            }
                        }
                    ]
                });
                $("#overlay").hide();
            });
        });

        // Function to handle editing user
        function editUser(userId) {
            // Implement logic to edit user
            console.log('Edit user with ID:', userId);
        }

        // Function to handle deleting user
        function deleteUser(userId) {
            // Implement logic to delete user
            console.log('Delete user with ID:', userId);
        }

        // Function to handle adding user
        function addUser()
        {
            $("#overlay").show();

            // Post data updated
            serverLessRequests.checkAuthAndPostData(
                // Set endpoint
                '/api/v1/users',
                // Set data
                $("#add-user-form").serialize()
            ).then(response => {

                // Output from server
                alert(response.result);

                // Hide overlay
                $("#overlay").hide();
            });
        }

</script>
</body>
</html>
