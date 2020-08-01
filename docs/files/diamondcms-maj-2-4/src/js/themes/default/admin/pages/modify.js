//On utilise cette ligne pour verifier qu'aucun autre code n'ait changé la valeur de $
jQuery(function ($){
    $("#delete").click(function(e){
      e.preventDefault();
      var link = $(this).attr('data-link');
      var redirect = $(this).attr('data-redirect');
      console.log(link,redirect);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
                document.location = redirect;
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
});