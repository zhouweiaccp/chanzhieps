$(document).ready(function() 
{
    setRequiredFields();
    setAdminLeftMenu();

    $('img.adaptive').each(function()
    {
      $(this).resizeImage()
    });
    $.setAjaxModal();
    $.setAjaxForm('#ajaxForm');
    $.setAjaxDeleter('.deleter');

    /* Ping for keep login every six minute. */
    if(needPing) setInterval('setPing()', 1000 * 60);

    $("#topNav li").hover(function () 
    {
        $(this).toggleClass('hover');
    });
});
