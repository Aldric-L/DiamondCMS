//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){

    $( "select#img_1" ).change(function(){ 
        if ($("#img_1").val() == "fa"){
            $("#div-fa-1").show();
        }else {
            $("#div-fa-1").hide();
        }
    });
    $( "select#img_2" ).change(function(){ 
        if ($("#img_2").val() == "fa"){
            $("#div-fa-2").show();
        }else {
            $("#div-fa-2").hide();
        }
    });
    $( "select#img_3" ).change(function(){ 
        if ($("#img_3").val() == "fa"){
            $("#div-fa-3").show();
        }else {
            $("#div-fa-3").hide();
        }
    });

    $(".save_modifs_propa").click(function(){
        $.ajax({
          url : $(this).attr('data-link'),
          type : 'POST',
          data: $("#propa_form").serializeArray(),
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
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
          }
        });
    });

    $(".save_modifs").click(function(){
        $.ajax({
          url : $(this).attr('data-link'),
          type : 'POST',
          data: {en_whois: $('#en_whois').prop('checked'), en_news: $('#en_news').prop('checked'), bg: $('#bg').val()},
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