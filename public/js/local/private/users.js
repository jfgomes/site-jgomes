$(document).ready(function() {
    let table = $('#user-table').DataTable({
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

// Function to delete a row
function deleteRow(id) {
    var table = $('#user-table').DataTable();
    table.rows(function(idx, data, node) {
        return data.id === id;
    }).remove().draw();
}

// Function to open the modal
function openModal() {
    document.getElementById("userModal").style.display = "block";
}

// Function to close the modal
function closeModal() {
    document.getElementById("userModal").style.display = "none";
    resetFormField();
}

// Function to add a user (simple example)
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

    // Close the modal
    closeModal();
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById("userModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function resetFormField(){
    $(".user-name").css("border", "").val("");
    $(".user-email").css("border", "").val("");
    $(".user-password").css("border", "").val("");
}

function validate(action) {
    // Check if required fields are filled and email is valid
    var isValid = true;

    // Validate email format using a regular expression
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(action === 'add') {
        $(".user-form [required]").each(function() {
            if ($.trim($(this).val()) === "") {
                isValid = false;
                $(this).css("border", "2px solid red"); // Highlight empty field
            } else {
                $(this).css("border", ""); // Remove highlight if filled
            }
        });

        var userEmail = $.trim($(".user-email").val());
        if (!emailPattern.test(userEmail)) {
            isValid = false;
            $(".user-email").css("border", "2px solid red"); // Highlight invalid email field
        } else {
            $(".user-email").css("border", ""); // Remove highlight if valid
        }

        // Check if password field is visible and filled
        if ($(".setPassEditInput").is(":visible") && $.trim($(".user-password").val()) === "") {
            isValid = false;
            $(".user-password").css("border", "2px solid red"); // Highlight empty password field
        } else {
            $(".user-password").css("border", ""); // Remove highlight if filled
        }
    }

    if(action === 'update') {
        $(".user-form-update [required]").each(function() {
            if ($.trim($(this).val()) === "") {
                isValid = false;
                $(this).css("border", "2px solid red"); // Highlight empty field
            } else {
                $(this).css("border", ""); // Remove highlight if filled
            }
        });

        var userEmailUpdate = $.trim($(".user-email-update").val());
        if (!emailPattern.test(userEmailUpdate)) {
            isValid = false;
            $(".user-email-update").css("border", "2px solid red"); // Highlight invalid email field
        } else {
            $(".user-email-update").css("border", ""); // Remove highlight if valid
        }

        // Check if password field is visible and filled
        if ($(".setPassEditInput-update").is(":visible") && $.trim($(".user-password-update").val()) === "") {
            isValid = false;
            $(".user-password-update").css("border", "2px solid red"); // Highlight empty password field
        } else {
            $(".user-password-update").css("border", ""); // Remove highlight if filled
        }
    }

    // If all fields are filled and email is valid, proceed with user update
    if (isValid) {
        if(action === 'add') {
            addUser();
        } else {
            UpdateUser();
        }
    } else {
        alert("Please fill in all required fields correctly.");
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

// Function to open the update modal
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

// Function to close the update modal
function closeModalUpdate() {
    document.getElementById("userModalUpdate").style.display = "none";
    resetFormField();
}

// Function to update a user (simple example)
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

    // Close the modal
    closeModalUpdate();
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById("userModalUpdate");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Function to open the delete modal
function openModalDelete(id) {
    $("#delete-user-id").val(id);
    document.getElementById("userModalDelete").style.display = "block";
}

// Function to close the delete modal
function closeModalDelete() {
    document.getElementById("userModalDelete").style.display = "none";
}

// Function to delete a user (simple example)
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

        // Example action: Just show an alert with the user's data
        alert("User deleted!");

        // Hide overlay
        $("#overlay").hide();
    });

    // Close the modal
    closeModalDelete();
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById("userModalDelete");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
