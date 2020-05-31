var n_serveurs = $("#infos-servers").attr("data-nb");
var lien_base = $("#infos-servers").attr("data-link");
console.log(lien_base);
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
      $("#desc_serveur_".concat(i)).html(json_result[i]['desc']);
      $("#serveur_name_".concat(i)).html(json_result[i]['name']);
      var img = json_result[i]['img'];
      if (img.substring(img.length - 4) == ".png"){
        console.log(lien_base+"getimage/png/-/" + img.substring(img.length-4, -4) + "/350/150/");
        $("#img_serveur_".concat(i)).attr('src', lien_base+"getimage/png/-/" + img.substring(img.length-4, -4) + "/"+ (Math.round((9 * 350)/16)).toString() +"/350/");
      }else if (img.substring(img.length - 4) == ".jpg"){
        $("#img_serveur_".concat(i)).attr('src', lien_base+"getimage/jpg/-/" + img.substring(img.length-4, -4) + "/"+ (Math.round((9 * 350)/16)).toString() +"/350/");
      }else if (img.substring(img.length - 4) == "jpeg"){
        $("#img_serveur_".concat(i)).attr('src', lien_base+"getimage/jpeg/-/" + img.substring(img.length-5, -5) + "/" (Math.round((9 * 350)/16)).toString() + "/350/");
      }
      //$("#img_serveur_".concat(i)).attr('src', lien_base+"views/uploads/img/" + json_result[i]['img']);
      if (json_result[i]['results'] == false){
        if (json_result[i]['enabled'] == "true"){
          $("#slots_serveur_".concat(i)).html('Slots : <span style="color: red;">Déconnecté</span>');
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Déconnecté</span>');
          $("#link_serveur_".concat(i)).attr('disabled', "");
        }else {
          $("#slots_serveur_".concat(i)).html('Slots : <span style="color: red;">Désactivé</span>');
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Désactivé</span>');
          $("#link_serveur_".concat(i)).attr('disabled', "");
        }
      }else {
        $("#slots_serveur_".concat(i)).html('Slots : ' + json_result[i]['results']['Players'] + " / " + json_result[i]['results']['MaxPlayers']);
        $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: green;">Connecté</span>');
        $("#link_serveur_".concat(i)).attr('href', lien_base+"serveurs/" + i);
      }
      console.log(i);
    }
  })
});