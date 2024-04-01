// Access token name
let access_token_str  = 'access_token';

// Access token ttl
let access_token_ttl_minutes = 15;

// Login page
let login_page           = '/login';

// Home page
let home_page            = '/home';

// Forbidden page
let forbidden_page       = '/403';

// Too many requests page
let many_requests_page   = '/429';

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
            data: $('#loginForm').serialize(),
            success: function(response)
            {
                // Store the access_token at localStorage
                setTokenWithExpiry(access_token_str, response.access_token, access_token_ttl_minutes);

                // Handle successful login - redirect to home
                window.location.href = `${home_page}`;
            },
            error: function(xhr)
            {
                // Remove the overlay
                $("#overlay").hide();

                // Inform client about 429 ( Too many requests )
                if (xhr.status !== 429)
                {
                    // Show error to client
                    showFlashMessage(
                        'error',
                        'Invalid login'
                    );
                    return;
                }

                // Show error to client
                showFlashMessage(
                    'error',
                    'Too many request at this moment'
                );
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
                        window.location.href = `${login_page}?` + btoa('b64=true&success=Logout done with success');
                    }
                });
            })
            .catch(() => {
                // Here we can handle the error appropriately (e.g., redirect to an error page)
            });
    }

    // Get data if authorized
    function checkAuthAndGetData(url) {
        return new Promise((resolve, reject) => {
            // Had the overlay
            $("#overlay").show();

            // Wait for the promise to be resolved
            getToken(access_token_str)
                .then(token => getData(url, token))
                .then(response => {
                    // Handle successful data retrieval
                    if(response.result.user.role === 'admin')
                    {
                        $(".adminLink").show();
                    }

                    // Resolve the promise with the response
                    resolve(response);
                })
                .catch(error => {
                    // Case forbidden (403) errors go to forbidden page
                    if (error === 403)
                    {
                        window.location.href = forbidden_page;

                        // Case too many requests (429) errors go to many requests page
                    } else if (error === 429)
                    {
                        window.location.href = many_requests_page;

                    } else {
                        // Case other errors redirect to login page
                        window.location.href = `${login_page}?` + btoa(`b64=true&error=${error}`);
                    }
                    reject(error); // Reject the promise
                })
                .finally(() => {
                    // Hide the overlay regardless of success or failure
                    // $("#overlay").hide();
                });
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
                    // Case Forbidden go back
                    if (xhr.status === 403)
                    {
                        reject(403);
                        return;
                    }

                    // Inform client about 429 ( Too many requests )
                    if (xhr.status === 429)
                    {
                        // Reject the promise for too many requests
                        reject(429);
                        return;
                    }

                    // xhr.statusText || 'Cannot load data. Please try again.'
                    reject('Cannot load data');
                }
            });
        });
    }

    // Function to create and show errors
    function showFlashMessage(type, message) {
        $('<div>', {
            class: 'flashMessage ' + type,
            text: message
        }).prependTo('#loginMsg').delay(3000).fadeOut(1000, function() {
            $(this).remove();
        });
    }

    // Function to set the token in localStorage with an expiration time
    function setTokenWithExpiry(key, value, ttlInMinutes)
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
                    setTokenWithExpiry(access_token_str, response.access_token, access_token_ttl_minutes);

                    // Return the new token
                    resolve(response.access_token);
                },
                error: function (xhr) {

                    // Inform client about 429 ( Too many requests )
                    if (xhr.status !== 429) {

                        // Reject the promise as the token refresh failed
                        reject('Authentication needed');
                        return;
                    }

                    reject('Too many requests');
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

            // If localStorage is empty, reject the promise as the no token was not found
            if (!accessToken)
            {
                reject('Authentication needed');
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
                    success: function() {
                        resolve(token);
                    },
                    error: function(xhr) {
                        // Remove the token fom localStorage if it is invalid.
                        // Allow to keep 429 ( Too many requests )
                        if (xhr.status !== 429) {
                            // Remove the token from localStorage if it is invalid.
                            localStorage.removeItem(key);

                            // Reject the promise as the refresh token is invalid
                            reject('Authentication needed');
                            return;
                        }

                        // Reject the promise for too many requests
                        reject('Too many requests');
                    }
                });
            }
        });
    }

    // Init the module...
    // Assigning events to HTML elements, initializing other functions, etc...
    function init()
    {
        // Login btn via click
        $('#loginBtn').on('click', doLogin);

        // Login btn via btn enter
        $(document).on('keypress', function(event) {
            if (event.which === 13) { // Enter btn code
                event.preventDefault();
                doLogin(); // Call the func doLogin
            }
        });

        // Bypass login page case user has a valid access_token
        if (window.location.pathname === `${login_page}`)
        {
            checkAuthAndByPassLogin();
        }

        // Set time to remove flash messages
        $('.flashMessage').delay(3000).fadeOut(1000);
    }

    // Return init
    return {
        init: init,
        checkAuthAndGetData: checkAuthAndGetData,
        doLogout: doLogout
    };

})(jQuery);

