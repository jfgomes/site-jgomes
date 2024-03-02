// Login page
let login_page =  '/login';

// Home page
let home_page =  '/home';

// Get token from local
let access_token = localStorage.getItem('access_token');

// Ajax requests module definition
let serverLessRequests = (function($)
{
    // Login function
    function doLogin() {
        let formData = $('#login-form').serialize();
        $.ajax({
            type: 'POST',
            url: '/api/v1/login',
            data: formData,
            success: function(response)
            {
                // Store the access_token at localStorage
                localStorage.setItem('access_token', response.access_token);

                // Handle successful login - redirect to home
                window.location.href = `${home_page}`;
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON.error || 'Login failed. Please try again.';
                $('#response-message').html('<div style="color: red;">' + errorMessage + '</div>');
            }
        });
    }

    // Bypass login function
    function checkAuthAndByPassLogin()
    {
        $.ajax({
            type: 'GET',
            url: '/api/v1/check',
            headers: {
                'Authorization': `Bearer ${access_token}`
            },
            success: function(response) {
                if(response === true){
                    window.location.href = `${home_page}`;
                }
                return false;
            }
        });
    }

    // Logout function
    function doLogout()
    {
        $.ajax({
            type: 'POST',
            url: '/api/v1/logout',
            headers: {
                'Authorization': `Bearer ${access_token}`
            },
            success: function() {
                localStorage.removeItem('access_token');
                window.location.href = `${login_page}`;
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
    }

    // Function to check if client is authenticated before use the api
    function checkAuthAndGetData(url)
    {
        $.ajax({
            type: 'GET',
            url: '/api/v1/check',
            headers: {
                'Authorization': `Bearer ${access_token}`
            },
            success: function(response) {
                if(response === true){
                    return getData(url);
                }
                return false;
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    // Case not authenticated go to login page
                    window.location.href = `${login_page}`;
                } else {
                    console.log('Error: ' + xhr.status);
                }
            }
        });
    }

    // Function ( private ) to get data from backend throw api
    function getData(url)
    {
        // Get data from backend
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'Authorization': `Bearer ${access_token}`
            },
            success: function(response)
            {
                console.log(response);
            },
            error: function(xhr)
            {
                window.location.href = `${login_page}`;
            }
        });
    }

    // Init the module..
    // Assigning events to HTML elements, initializing other functions, etc...
    function init()
    {
        // Login btn
        $('#login-btn').on('click', doLogin);

        // Logout btn
        $('#logout-btn').on('click', doLogout);

        // Bypass login page case user has a valid access_token
        if (window.location.pathname === `${login_page}`) {
            checkAuthAndByPassLogin();
        }
    }

    // Return init
    return {
        init: init,
        checkAuthAndGetData: checkAuthAndGetData
    };

})(jQuery);

// Initialize the module when the DOM is ready.
$(document).ready(function() {
    serverLessRequests.init();
});
