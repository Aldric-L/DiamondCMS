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

    var myDropzone = new Dropzone("div#dz", { url: "/file/post"});
    Dropzone.options.dz = {
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        accept: function(file, done) {
          if (file.name == "justinbieber.jpg") {
            done("Naha, you don't.");
          }
          else { done(); }
        }
      };
      

    // Fin news

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