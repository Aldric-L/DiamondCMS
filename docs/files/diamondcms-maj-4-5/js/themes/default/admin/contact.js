//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    $(".supp_contact").click(function(){
      var id = $(this).attr('id');
      var link = $(this).attr('data');
      console.log(link);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(id);
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
            }else {    
              $('#contact_modal_' + id).modal('hide');
              $('#contact_line_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });


    $('.contact_modal_link').click(function(){
      $('#contact_modal_' + $(this).attr('data')).modal('show');
    });
});