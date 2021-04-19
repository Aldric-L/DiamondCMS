//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    $(".save_modifs").click(function(){
        if ($('#en_forum_externe').prop('checked')){
            var en = "true";
        }else {
            var en = "false";
        }
        console.log(en);
        $.ajax({
          url : $(this).attr('data'),
          type : 'POST',
          data : { link: $('#link').val(), en_fe: en},
          dataType : 'html',
          success: function (data_rep) {
              console.log(data_rep);
            if (data_rep != "Success"){
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".enable").click(function(){
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
            }else {    
              document.location.href = document.location.href;
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".delete_cat").click(function(){
      var id = $(this).attr('id');
      var link = $(this).attr('data');
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
              $('#line_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
    $(".delete_scat").click(function(){
      var id = $(this).attr('id');
      var link = $(this).attr('data');
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
              $('#line_scat_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".moove_scat").click(function(){
      $(this).hide();
      $("#cats_available").show();

      $("#cats_available").change(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'POST',
          data : {id: $('#cats_available option:selected').val()},
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              alert("Erreur code 112 : Impossible d'ajouter l'élement demandé. Contacter le support. Réponse du serveur: " + data_rep);
              console.log(data_rep);
            }else {    
              document.location.href = document.location.href;
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
        
      })
    });
});