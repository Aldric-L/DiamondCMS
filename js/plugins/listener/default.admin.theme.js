//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
   
    //Utilisé dans la partie news du cms
    $(".news_link_modal").click(function(){
        $("#modal_news_" + $(this).attr("data")).modal('show');
    });

    $(".supp_new").click(function(){
        var link = $(this).attr("data");
        var id = $(this).attr("id");
        $(".btn_news_del_"+ id).text("Chargement en cours");
        $.ajax({
            url : link,
            type : 'GET',
            dataType : 'html',
            success: function () {
                $("#modal_news_" + id).modal('hide');
                $("#news_link_modal_" + id).hide();
                console.log(id);
            },
            error: function() {
                alert("Erreur, impossible de fermer la fenetre. (Code D'erreur 111, Merci de contacter les administrateurs du site.)");
            }
        });
    });

    // Fin news

    // permissions
    $(".supp_role").click(function(){
        var link = $(this).attr("data");
        var id = $(this).attr("id");
        $(".btn_role_del_"+ id).text("Chargement en cours");
        $.ajax({
            url : link,
            type : 'GET',
            dataType : 'html',
            success: function () {
                $("#line_" + id).hide();
                console.log(id);
            },
            error: function() {
                alert("Erreur, impossible de supprimer l'élement (Code D'erreur 111, Merci de contacter les administrateurs du site.)");
            }
        });
    });
    // Fin permissions

    $(".err_controleur_end").click(function(){
        $.ajax({
        url : $(this).attr("id"),
        type : 'GET',
        dataType : 'html',
        success: function () {
            $("#c_def_error").modal('hide');
        },
        error: function() {
            alert("Erreur, impossible de fermer la fenetre. (Code D'erreur 111, Merci de contacter les administrateurs du site.)");
        }
        });
    });
});