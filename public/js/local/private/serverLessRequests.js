// Access token name
let access_token_str  = 'access_token';

// Access token ttl
let access_token_ttl_minutes = 1;

// Login page
let login_page       =  '/login';

// Home page
let home_page        =  '/home';

// Ajax requests module definition
let serverLessRequests = (function($)
{
    // Login function
    function doLogin()
    {
        // Had the overlay
        $("#overlay").show();

        // Authenticate
        $.ajax({
            type: 'POST',
            url: '/api/v1/login',
            data: $('#login-form').serialize(),
            success: function(response)
            {
                // Store the access_token at localStorage
                setWithExpiry(access_token_str, response.access_token, access_token_ttl_minutes);

                // Handle successful login - redirect to home
                window.location.href = `${home_page}`;
            },
            error: function(xhr)
            {
                // Show error to client
                showFlashMessage(
                    'error',
                    xhr.statusText || 'Login failed. Please try again.'
                );

                // Remove the overlay
                $("#overlay").hide();
            }
        });
    }

    // Bypass login case client has a valid token
    function checkAuthAndByPassLogin()
    {
        // Wait for the promise to be resolved
        getToken(access_token_str)
            .then((token) => {
                // Case no token stored or token is invalid, redirect to home_page
                if (!token) {
                    window.location.href = `${login_page}`;
                } else {
                    // Token is valid, redirect to home_page or perform other actions
                    window.location.href = `${home_page}`;
                }
            })
            .catch(() => {
                // Here we can handle the error appropriately (e.g., redirect to somewhere)
            });
    }

    // Logout function
    function doLogout()
    {
        // Had the overlay
        $("#overlay").show();

        // Wait for the promise to be resolved
        getToken(access_token_str)
            .then((token) => {
                $.ajax({
                    type: 'POST',
                    url: '/api/v1/logout',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    success: function() {
                        // Handle success if needed
                    },
                    error: function(xhr) {
                        // Handle error if needed
                    },
                    complete: function() {

                        // Remove client token stored in the browser
                        localStorage.removeItem(access_token_str);

                        // Redirect to login page
                        window.location.href = `${login_page}?success=Logout`;
                    }
                });
            })
            .catch((error) => {
                // Here we can handle the error appropriately (e.g., redirect to an error page)
                console.error(error);
            });
    }

    // Get data if authorized
    function checkAuthAndGetData(url)
    {
        // Had the overlay
        $("#overlay").show();

        // Wait for the promise to be resolved
        getToken(access_token_str)
            .then(token => getData(url, token))
            .then(response => {
                // Handle successful data retrieval
                console.log(response);
            })
            .catch(error => {
                // Case error redirect to login page
                window.location.href = `${login_page}?error=${error}`;
            })
            .finally(() => {
                // Hide the overlay regardless of success or failure
                $("#overlay").hide();
            });
    }

    // Function to get data from backend with a valid token
    function getData(url, token)
    {
        return new Promise(function(resolve, reject)
        {
            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                success: function(response)
                {
                    resolve(response);
                },
                error: function(xhr)
                {
                    resolve(xhr);
                },
                complete: function(xhr)
                {
                    reject(new Error(xhr.statusText || 'Cannot load data. Please try again.'));
                }
            });
        });
    }

    // Function to create and show flash errors
    function showFlashMessage(type, message)
    {
        $('.flash-message').remove();
        let flashElement = $('<div>').addClass('flash-message ' + type)
            .text(message);
        $('body').prepend(flashElement);
    }

    // Function to set the token in localStorage with an expiration time
    function setWithExpiry(key, value, ttlInMinutes)
    {
        let now = new Date();
        let item = {
            value: value,
            expiry: now.getTime() + ttlInMinutes * 60 * 1000, // Converting minutes to milliseconds
        };

        localStorage.setItem(key, JSON.stringify(item));
    }

    // Function to retrieve a new token from localStorage
    function doRefreshToken(oldToken)
    {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'POST',
                url: '/api/v1/refresh',
                headers: {
                    'Authorization': `Bearer ${oldToken}`
                },
                success: function (response) {

                    // Store the access_token at localStorage
                    setWithExpiry(access_token_str, response.access_token, access_token_ttl_minutes);

                    // Return the new token
                    resolve(response.access_token);
                },
                error: function () {
                    reject(new Error('Token refresh failed'));
                }
            });
        });
    }

    // Function to return the current token from localStorage and case it is expired, create a new one
    function getToken(key)
    {
        return new Promise((resolve, reject) => {

            // Check if token is stored in localStorage
            let accessToken = localStorage.getItem(key);

            // If localStorage is empty, exit the promise
            if (!accessToken)
            {
                reject(new Error('Token not found'));
                return;
            }

            // Extract token and expiration date
            let access_token = JSON.parse(accessToken);

            // Current date
            let now = new Date();

            // Get current token
            let token = access_token.value;

            // Compare current date with the token validation date. If the token expired, delete the current localStorage
            if (now.getTime() > access_token.expiry) {

                // Remove the token if it has expired
                localStorage.removeItem(key);

                // Use the promise returned by doRefreshToken to create a new token and store it in localStorage
                doRefreshToken(token)
                    .then((newToken) => {
                        resolve(newToken);
                    })
                    .catch((error) => {
                        reject(error);
                    })

            } else {

                // Last check... check if the current token is valid
                $.ajax({
                    type: 'GET',
                    url: '/api/v1/check',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    success: function(result) {
                        resolve(token);
                    },
                    error: function(xhr) {
                        // Remove the token fom localStorage if it is invalid
                        localStorage.removeItem(key);

                        // Reject the promise
                        reject(new Error('Invalid Refresh'));
                    }
                });
            }
        });
    }

    // Init the module...
    // Assigning events to HTML elements, initializing other functions, etc...
    function init()
    {
        // Login btn
        $('#login-btn').on('click', doLogin);

        // Bypass login page case user has a valid access_token
        if (window.location.pathname === `${login_page}`)
        {
            checkAuthAndByPassLogin();
        }
    }

    // Return init
    return {
        init: init,
        checkAuthAndGetData: checkAuthAndGetData,
        doLogout: doLogout
    };

})(jQuery);

