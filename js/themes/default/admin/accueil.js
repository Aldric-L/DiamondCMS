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

        //$("#img_serveur_".concat(i)).attr('src', lien_base+"views/uploads/img/" + json_result[i]['img']);
        if (json_result[i]['results'] == false){
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Déconnecté</span>');
        }else {
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: green;">Connecté</span>');
        }
        console.log(i);
      }
    })
  });
