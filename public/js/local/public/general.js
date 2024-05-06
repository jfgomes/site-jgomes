function downloadCV(urlDocument)
{
    let link  = document.createElement('a');
    link.href     = urlDocument;
    link.download = urlDocument;
    link.click();
}

$(document).ready(function()
{
    $('.custom-select').each(function()
    {
        let $select = $(this);
        let $trigger = $select.find('.custom-select-trigger');
        let $options = $select.find('.custom-options');
        let $optionsList = $options.find('.custom-option');

        $trigger.click(function(e) {
            e.stopPropagation(); // Avoid the default propagation
            $('.custom-options').not($options).removeClass('active'); // Close the other dropdowns
            $options.toggleClass('active');
        });

        $optionsList.click(function() {
            window.location.href = $(this).attr('data-value');
        });
    });

    $(document).click(function(e) {
        if (!$('.custom-select').is(e.target) && $('.custom-select').has(e.target).length === 0) {
            $('.custom-options').removeClass('active');
        }
    });
});
