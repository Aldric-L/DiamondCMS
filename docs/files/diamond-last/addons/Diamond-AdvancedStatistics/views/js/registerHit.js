$(document).ready(() => {
    var arguments = {
        internal_path: document.URL.replace($("#diamondhead").attr("data-baselink"), ""),
        HTTP_REFERER: Document.referrer,
        HTTP_USER_AGENT: navigator.userAgent
    }
    $.ajax({
        url : $("#diamondhead").attr("data-baselink")+ "api/das_statistics/get/registerhit/",
        type : 'POST',
        data: arguments,
        dataType : 'html',
        success: function (res) {},
        error: function() {
            console.log("Erreur dans l'enregistrement statistique du hit. (Diamond-AdvancedStatistics)")
        }
    });
});
