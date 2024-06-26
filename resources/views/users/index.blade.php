<!DOCTYPE html>
    <html class="no-js" lang="en">
    <head>
        <!--- Basic Page Needs ========================================= -->
        <title>🙋🏼‍♂️ Users</title>
        @include('partials.meta')

        <!-- Favicons ================================================== -->
        <link rel="shortcut icon" href="favicon.png" >

        <!-- JS + CSS ================================================== -->
        @include('partials.css_js')

        <!-- USERS WITH DATATABLES JS================================================== -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

        <!-- USERS CUSTOM CSS ================================================== -->
        <link rel="stylesheet" href="css/local/private/users.css">
    </head>
    <body>
    <!-- Overlay to block the page during the loading ============== -->
    @include('partials.overlay')

    <!-- Header ==================================================== -->
    <header>
        <div class="header-content">
            <h1 class="header-title">🙋🏼‍♂️ Users ( Elasticsearch + Datatables ) </h1>
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
<script>

    $(document).ready(function() {
        var table = $('#user-table').DataTable({
            dom: 'lrtip', // Customize the layout to hide the search input
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings) {
                $("#overlay").show();
                var queryValue = $('#query').val();
                var url = `/api/v1/users-es?page=${data.start / data.length + 1}&limit=${data.length}&sortField=${data.columns[data.order[0].column].data}&sortOrder=${data.order[0].dir}&query=${queryValue}`;

                serverLessRequests.checkAuthAndGetData(url)
                    .then(response => {
                        callback({
                            draw: data.draw,
                            recordsTotal: response.recordsTotal,
                            recordsFiltered: response.recordsFiltered,
                            data: response.data
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                    })
                    .finally(() => {
                        $("#overlay").hide();
                    });
            },
            columns: [
                { data: 'id', orderable: false, className: 'dt-center column-even', width: '5%' },
                { data: 'name', className: 'dt-center column-odd', width: '15%' },
                { data: 'email', className: 'dt-center column-even', width: '20%' },
                { data: 'role', className: 'dt-center column-odd', width: '10%' },
                { data: 'created_at', className: 'dt-center column-even', width: '20%' },
                { data: 'updated_at', className: 'dt-center column-odd', width: '20%' },
                {
                    data: null,
                    className: 'dt-center column-even',
                    orderable: false,
                    width: '15%',
                    render: function(data, type, row) {
                        return `<button style="background-color: lightslategrey" onclick="openModalUpdate(${row.id})">📝 Edit user</button>
                            <button style="background-color: lightcoral" onclick="openModalDelete(${row.id})">❌ Delete</button>`;
                    }
                }
            ],
            autoWidth: false,
            responsive: true,
            pageLength: 4,
            lengthMenu: [4, 8, 20, 50, 100],
            order: [[5, 'desc']]
        });

        $('#query').on('keyup', function() {
            table.draw();
        });
    });


        // Função para deletar uma linha
        function deleteRow(id) {
            var table = $('#user-table').DataTable();
            table.rows(function(idx, data, node) {
                return data.id === id;
            }).remove().draw();
        }



</script>

<!-- O modal -->
<div style="z-index: 9999999;" id="userModal" class="modal">
    <!-- Conteúdo do modal -->
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <p style="font-size:23px; color: grey;margin-bottom: 30px">➕ Add user</p>
        <form class="user-form" id="add-user-form" style="color: black;">
            <label for="new-user-name">Name:</label>
            <input class="user-name" style="margin: 10px 0px;width: 100% !important;padding-left: 12px;" type="text" id="new-user-name" name="name" required>
            <label for="new-user-email">Email:</label>
            <input class="user-email" style="margin: 10px 0px;width: 100% !important;padding-left: 12px;" type="email" id="new-user-email" name="email" required>
            <div class="setPassEditInput" >
                <label for="new-user-password">Password:</label>
                <input class="user-password" style="margin: 10px 0px;width: 100% !important;padding-left: 12px;" type="password" id="new-user-password" name="password" required>
            </div>

            <label for="new-user-role">Role:</label> <br>
            <select style="margin: 10px 0px;
  width: 100%;
  height: 47px;
  padding-left: 10px;" id="new-user-role" name="role">
                <option value="admin">Admin</option>
                <option value="default">Default</option>
            </select>
            <br><br>
            <button style="height: 80px" type="button" onclick="validate('add')">Confirm</button>
        </form>
    </div>
</div>

<script>
    // Função para abrir o modal
    function openModal()
    {
        document.getElementById("userModal").style.display = "block";
    }

    // Função para fechar o modal
    function closeModal() {
        document.getElementById("userModal").style.display = "none";
        resetFormField();
    }

    // Função para adicionar um usuário (exemplo simples)
    function addUser() {

        $("#overlay").show();

        // Post data updated
        serverLessRequests.checkAuthAndPostData(

            // Set endpoint
            '/api/v1/users',

            // Set data
            $("#add-user-form").serialize()

        ).then(response => {

            alert("New user added with success!");

            // Refresh page
            location.reload();

            // Hide overlay
            //$("#overlay").hide();
        });

        // Fechar o modal
        closeModal();
    }

    // Fechar o modal se o usuário clicar fora dele
    window.onclick = function(event) {
        var modal = document.getElementById("userModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>



<!-- 1 modal -->
<div style="z-index: 9999999;" id="userModalUpdate" class="modal">
    <!-- Conteúdo do modal -->
    <div class="modal-content">
        <span class="close" onclick="closeModalUpdate()">&times;</span>
        <p style="font-size:23px; color: grey;margin-bottom: 30px">📝 Edit user</p>
        <form class="user-form-update" id="update-user-form" style="color: black;">
            <input type="hidden" id="update-user-id" name="id" required>
            <label for="update-user-name">Name:</label>
            <input class="user-name-update" style="margin: 10px 0px;width: 100% !important;padding-left: 12px;" type="text" id="update-user-name" name="name" required>
            <label for="update-user-email">Email:</label>
            <input class="user-email-update" style="margin: 10px 0px;width: 100% !important;padding-left: 12px;" type="email" id="update-user-email" name="email" required>
            <br>
            <label for="update-user-role">Role:</label> <br>
            <select style="margin: 10px 0px;
  width: 100%;
  height: 47px;
  padding-left: 10px;" id="update-user-role" name="role">
                <option value="admin">Admin</option>
                <option value="default">Default</option>
            </select>
            <br>
            <button style="margin-bottom: 15px" type="button" onclick="showPassInput()">New password</button>
            <div class="setPassEditInput-update" style="display: none">
                <label for="update-user-password">Password:</label>
                <input class="user-password-update" style="margin: 10px 0px;width: 100% !important;padding-left: 12px;" type="password" id="update-user-password" name="password">
            </div>
            <button style="background-color: lightslategrey;height: 80px" type="button" onclick="validate('update')">Confirm</button>
        </form>
    </div>
</div>

<script>

    function resetFormField(){
        $(".user-name").css("border", "").val("");
        $(".user-email").css("border", "").val("");
        $(".user-password").css("border", "").val("");
    }
    function validate(action) {
        // Verifica se os campos obrigatórios estão preenchidos e se o e-mail é válido
        var isValid = true;

        // Valida o formato do e-mail usando uma expressão regular
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if(action === 'add')
        {
            $(".user-form [required]").each(function() {
                if ($.trim($(this).val()) === "") {
                    isValid = false;
                    $(this).css("border", "2px solid red"); // Destaca o campo vazio
                } else {
                    $(this).css("border", ""); // Remove o destaque se estiver preenchido
                }
            });

            var userEmail = $.trim($(".user-email").val());
            if (!emailPattern.test(userEmail)) {
                isValid = false;
                $(".user-email").css("border", "2px solid red"); // Destaca o campo de e-mail inválido
            } else {
                $(".user-email").css("border", ""); // Remove o destaque se o e-mail for válido
            }

            // Verifica se o campo de senha está visível e, se estiver, verifica se está preenchido
            if ($(".setPassEditInput").is(":visible") && $.trim($(".user-password").val()) === "") {
                isValid = false;
                $(".user-password").css("border", "2px solid red"); // Destaca o campo vazio
            } else {
                $(".user-password").css("border", ""); // Remove o destaque se estiver preenchido
            }
        }

        if(action === 'update')
        {
            $(".user-form-update [required]").each(function() {
                if ($.trim($(this).val()) === "") {
                    isValid = false;
                    $(this).css("border", "2px solid red"); // Destaca o campo vazio
                } else {
                    $(this).css("border", ""); // Remove o destaque se estiver preenchido
                }
            });

            var userEmailUpdate = $.trim($(".user-email-update").val());
            if (!emailPattern.test(userEmailUpdate)) {
                isValid = false;
                $(".user-email-update").css("border", "2px solid red"); // Destaca o campo de e-mail inválido
            } else {
                $(".user-email-update").css("border", ""); // Remove o destaque se o e-mail for válido
            }

            // Verifica se o campo de senha está visível e, se estiver, verifica se está preenchido
            if ($(".setPassEditInput-update").is(":visible") && $.trim($(".user-password-update").val()) === "") {
                isValid = false;
                $(".user-password-update").css("border", "2px solid red"); // Destaca o campo vazio
            } else {
                $(".user-password-update").css("border", ""); // Remove o destaque se estiver preenchido
            }
        }


        // Se todos os campos estão preenchidos e o e-mail é válido, prossiga com a atualização do usuário
        if (isValid) {
            if(action === 'add')
            {
                addUser();
            }
            else
            {
                UpdateUser();
            }

        } else {
            alert("Por favor, preencha todos os campos obrigatórios corretamente.");
        }
    }


    function showPassInput(){
        if ($(".setPassEditInput-update").is(":visible")) {
            $(".setPassEditInput-update").hide();
        } else {
            $(".setPassEditInput-update").show();
        }

    }

    function getRowData(id) {
        var table = $('#user-table').DataTable();
        var rowData = table.rows().data().toArray();
        var foundRow = rowData.find(row => String(row.id) === String(id));
        if (foundRow) {
            console.log(foundRow);
            return (JSON.stringify(foundRow));
        } else {
            console.error('No row found with ID:', id);

        }
    }


    // Função para abrir o modal
    function openModalUpdate(id) {
        $("#update-user-id").val(id);

        document.getElementById("userModalUpdate").style.display = "block";

        let data = getRowData(id);
        let obj  = $.parseJSON(data);

        $("#update-user-name").val(obj.name);
        $("#update-user-email").val(obj.email);
        $("#update-user-role").val(obj.role);
        $("#update-user-password").val(obj.password);

    }

    // Função para fechar o modal
    function closeModalUpdate() {
        document.getElementById("userModalUpdate").style.display = "none";
        resetFormField();
    }

    // Função para adicionar um usuário (exemplo simples)
    function UpdateUser() {
        $("#overlay").show();


        // Post data updated
        serverLessRequests.checkAuthAndPutData(

            // Set endpoint
            '/api/v1/users',

            // Row id
            $("#update-user-id").val(),

            // Set data
            $("#update-user-form").serialize()

        ).then(response => {

            alert("User updated with success!");

            // Refresh page
            location.reload();

            // Hide overlay
            //$("#overlay").hide();
        });

        // Fechar o modal
        closeModalUpdate();
    }

    // Fechar o modal se o usuário clicar fora dele
    window.onclick = function(event) {
        var modal = document.getElementById("userModalUpdate");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<!-- 2 modal -->
<div style="z-index: 9999999;" id="userModalDelete" class="modal">
    <!-- Conteúdo do modal -->
    <div class="modal-content">
        <span class="close" onclick="closeModalDelete()">&times;</span>
        <p style="font-size:23px; color: grey;margin-bottom: 30px">❌ Delete</p>
        <p style="font-size:15px; color: #aaaaaa; text-align: center; margin-bottom: 20px">( If you confirm, the user will be deleted forever )</p>
        <form id="delete-user-form" style="color: black;">
            <input type="hidden" id="delete-user-id" name="id" required>
            <button style="background-color: lightcoral; height: 80px" type="button" onclick="deleteUser()">Confirm</button>
        </form>
    </div>
</div>

<script>
    // Função para abrir o modal
    function openModalDelete(id) {

        $("#delete-user-id").val(id);
        document.getElementById("userModalDelete").style.display = "block";
    }

    // Função para fechar o modal
    function closeModalDelete() {
        document.getElementById("userModalDelete").style.display = "none";
    }

    // Função para adicionar um usuário (exemplo simples)
    function deleteUser() {

        $("#overlay").show();

        // Post data updated
        serverLessRequests.checkAuthAndDeleteData(

            // Set endpoint
            '/api/v1/users',

            // Id
            $("#delete-user-id").val()

        ).then(response => {

           // reloadTable();

            deleteRow($("#delete-user-id").val());

            // Exemplo de ação: Apenas exibir um alerta com os dados do usuário
            alert("User deleted!");

            // Hide overlay
            $("#overlay").hide();
        });

        // Fechar o modal
        closeModalDelete();
    }

    // Fechar o modal se o usuário clicar fora dele
    window.onclick = function(event) {
        var modal = document.getElementById("userModalDelete");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
