//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    // Bouton de banissement dans le modal
    $("#ban_button").click(function(){
        $.ajax({
          url : $(this).attr('data'),
          type : 'POST',
          data : 'reason=' + $('#reason').val(),
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              $(".content-page").hide();               
              $(".ban_user").show();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
        $("#ban_modal").modal('hide');
        $('#emptyServer').show();
        $('.content-page').hide();
    });

    //bouton de banissement inline
    $(".ban").click(function(){
        $("#ban_modal").modal('show');
    });
    //bouton de suppression inline
    /*$(".supp").click(function(){
        $("#supp_modal").modal('show');
    });*/
    $(".supp").click(function(){
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
                console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              alert("Utilisateur purgé, actualisation de la page...");
              location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
});