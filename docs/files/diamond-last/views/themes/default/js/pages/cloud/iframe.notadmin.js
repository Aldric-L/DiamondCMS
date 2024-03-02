$("iframe").on('load', function(){
    $("#DiamondCloudUserIframe").contents().find("body").css("background-color","var(--main-bg-color)");
    $("#DiamondCloudUserIframe").contents().find("body").css("color","var(--main-text-color)");
});