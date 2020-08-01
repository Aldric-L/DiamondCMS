//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    //Protection du formulaire : impossible de le renvoyer incomplet.
    if ($("#id_pp").val() == " " || $("#id_pp").val() == "" || $("#secret_pp").val() == "" || $("#secret_pp").val() == " "){
      $(".ppconf").attr("disabled", true);
      $("em.explain").show();
    }else {
      $("em.explain").hide();
    }

    $("#id_pp").change(function(){
      if ($("#id_pp").val() == " " || $("#id_pp").val() == "" || $("#secret_pp").val() == "" || $("#secret_pp").val() == " "){
        $(".ppconf").attr("disabled", true);
        $(".ppconf").html('Sauvegarder');
        $("em.explain").show();
      }else {
        $(".ppconf").attr("disabled", false);
        $("em.explain").hide();
      }
    });

    $("#secret_pp").change(function(){
      if ($("#id_pp").val() == " " || $("#id_pp").val() == "" || $("#secret_pp").val() == "" || $("#secret_pp").val() == " "){
        $(".ppconf").attr("disabled", true);
        $(".ppconf").html('Sauvegarder');
        $("em.explain").show();
      }else {
        $(".ppconf").attr("disabled", false);
        $("em.explain").hide();
      }
    });

    $(".ppconf").click(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'POST',
          data: {en_paypal: $('#en_paypal').prop('checked'), sandbox: $('#sandbox').prop('checked'), money: $('#money').val(), id_pp: $('#id_pp').val(), secret_pp: $('#secret_pp').val()},
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".addpp").click(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'POST',
          data: {name: $('#name').val(), prix: $('#prix').val(), nb: $('#nb').val()},
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".del_offre").click(function(){
      var id = $(this).attr('data-id');
      var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              $('#line_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
});