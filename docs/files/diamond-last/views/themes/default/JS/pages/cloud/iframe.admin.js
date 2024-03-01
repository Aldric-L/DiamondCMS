$("iframe").on('load', function(){
    $("#DiamondCloudUserIframe").contents().find("body").css("background-color","var(--admin-bg-color)");
    $("#DiamondCloudUserIframe").contents().find("body").css("color","var(--admin-text-color)");
});