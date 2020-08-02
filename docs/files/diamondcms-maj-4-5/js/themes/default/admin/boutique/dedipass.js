//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    //Protection du formulaire : impossible de le renvoyer incomplet.
    if ($("#pub_key").val() == " " || $("#pub_key").val() == "" || $("#priv_key").val() == "" || $("#priv_key").val() == " "){
      $(".ddconf").attr("disabled", true);
      $("em.explain").show();
    }else {
      $("em.explain").hide();
    }

    $("#pub_key").change(function(){
      if ($("#pub_key").val() == " " || $("#pub_key").val() == "" || $("#priv_key").val() == "" || $("#priv_key").val() == " "){
        $(".ddconf").attr("disabled", true);
        $(".ddconf").html('Sauvegarder');
        $("em.explain").show();
      }else {
        $(".ddconf").attr("disabled", false);
        $("em.explain").hide();
      }
    });

    $("#priv_key").change(function(){
      if ($("#pub_key").val() == " " || $("#pub_key").val() == "" || $("#priv_key").val() == "" || $("#priv_key").val() == " "){
        $(".ddconf").attr("disabled", true);
        $(".ddconf").html('Sauvegarder');
        $("em.explain").show();
      }else {
        $(".ddconf").attr("disabled", false);
        $("em.explain").hide();
      }
    });

    $(".ddconf").click(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'POST',
          data: {en_ddp: $('#en_ddp').prop('checked'), pub_key: $('#pub_key').val(), priv_key: $('#priv_key').val()},
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
            }else {    
              location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
});