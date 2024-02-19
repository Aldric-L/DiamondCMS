$(document).ready(function(e) {
    $(".request_depend").hide();
    var lien_base = $("#ServerState").attr("data-baselink");
    $.ajax({
      url: $("#ServerState").attr("data-link"),
      type : 'POST',
      data : {width: "400"},
      dataType : 'html',
    }).done(function( res ) {
      try {
        res = JSON.parse(res);
        if (typeof(res["Return"]) != "undefined" && typeof(res["State"]) != "undefined" && res["State"] == 1)
          json_result = res["Return"];
        else
          return;
      }catch (e){
        alert('Something goes wrong... Call DiamondCMS\' support.');
        return;
      }
      var torender = "";
      var base;

      if (Object.keys(res['Return']).length == 0){
        torender += '<p class="text-center">Aucun serveur de jeu n\'est pour le moment initialisé avec Diamond-ServerLink. <br><em>N\'hésitez pas à en ajouter un dans l\'Assistant de configuration.</em></p>';
      }else {
        for(let i in json_result){
          if (i%2==0)
            base = $("#serverState-right-server");
          else
            base = $("#serverState-left-server");
          base.children().find(".desc_serveur").html(json_result[i]['desc']);
          base.children().find(".name_serveur").html(json_result[i]['name']);
          if (json_result[i]['img_customlink'].substring(0, 4) == "http")
            base.children().find(".img_serveur").attr('src', json_result[i]['img_customlink']);
          else
            base.children().find(".img_serveur").attr('src', lien_base + json_result[i]['img_customlink']);
    
          if (json_result[i]['results'] == false){
            if (json_result[i]['enabled'] == true){
              base.children().find(".slots_serveur").html('Slots : <span style="color: red;">Déconnecté</span>');
              base.children().find(".etat_serveur").html('Etat du serveur : <span style="color: red;">Déconnecté</span>');
              base.children().find(".link_serveur").attr('disabled', "");
            }else {
              base.children().find(".slots_serveur").html('Slots : <span style="color: red;">Désactivé</span>');
              base.children().find(".etat_serveur").html('Etat du serveur : <span style="color: red;">Désactivé</span>');
              base.children().find(".link_serveur").attr('disabled', "");
            }
          }else {
            base.children().find(".slots_serveur").html('Slots : ' + json_result[i]['results']['Players'] + " / " + json_result[i]['results']['MaxPlayers']);
            base.children().find(".etat_serveur").html('Etat du serveur : <span style="color: green;">Connecté</span>');
            base.children().find(".link_serveur").attr('href', lien_base+"Diamond-ServerLink/serveurs/" + i);
            base.children().find(".link_serveur").attr('disabled', false);
          }
          torender += base.html();
        }
      }
      
      
      $("#serverStateRenderBlock").html(torender);
      $("#loader").hide();
      $(".request_depend").show();
      $(".no_show").hide();
    })
  });