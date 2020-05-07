//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
  var n_serveurs = $("#infos-servers").attr('data-nb');
  var lien_base = $("#infos-servers").attr('data-link');
  //console.log(JSON.parse(data));
  $(document).ready(function(e) {
    $(".request_depend").hide();
    $.ajax({
      url: lien_base + "serveurs/json/"
    }).done(function( arg ) {
      console.log("AJAX :");
      console.log(JSON.parse(arg));
      var json_result = JSON.parse(arg);
      console.log(json_result);
      $("#loader").hide();
      $(".request_depend").show();
      
      for (var i = 1; n_serveurs >= i; i++){
        $("#serveur_name_".concat(i)).html(json_result[i]['name']);
        console.log("#etat_serveur_".concat(i));

        if (json_result[i]['results'] == false){
          if (json_result[i]['enabled'] == "true"){
            $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Déconnecté</span>');
          }else {
            $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Désactivé</span>');
          }
        }else {
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: green;">Connecté</span>');
        }
        console.log(i);
      }
    })
  });

  $('#mtnc').click(function(){
    var link = $(this).attr('data');
    console.log(link);
      $.ajax({
        url : link,
        type : 'GET',
        dataType : 'html',
        success: function (data_rep) {
          if (data_rep != "Success"){
            console.log(data_rep);
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {    
            location.reload(true);
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
  });

  $(".modify_theme").click(function(){
    var link = $(this).attr('data');
    console.log(link);
      $.ajax({
        url : link,
        type : 'GET',
        dataType : 'html',
        success: function (data_rep) {
          if (data_rep != "Success"){
            console.log(data_rep);
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {    
            location.reload(true);
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
  });

  $(".modify_addon").click(function(){
    var link = $(this).attr('data');
    console.log(link);
      $.ajax({
        url : link,
        type : 'GET',
        dataType : 'html',
        success: function (data_rep) {
          if (data_rep != "Success"){
            console.log(data_rep);
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {    
            location.reload(true);
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
  });
});