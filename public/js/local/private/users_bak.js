let usersModule = (function($)
{
    function init()
    {
        // ===========================================================
        // Set municipality list according the selected district
        $('#').on('change', function()
        {
            // Request to get the municipalities of the selected district
            serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/locations', districtParams))
                .then(response => {


                })
                .catch(error => {
                    console.error(
                        'Error:',
                        error
                    );
                }).finally(() => {

            });
        });
    }

    // Return init
    return {
        init: init
    };

})(jQuery);

