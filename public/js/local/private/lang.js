let langModule = (function($)
{
    function init()
    {
        alert(1);
    }

    function getTranslations(url)
    {
        alert(12);
        return 1;
    }

    function updateTranslations(url)
    {
        alert(123);
    }

    // Return init
    return {
        init: init,
        getTranslations: getTranslations,
        updateTranslations: updateTranslations,
    };

})(jQuery);

