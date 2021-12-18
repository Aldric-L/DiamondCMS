//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
function getRandomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}

jQuery(function ($){

  //Selon la loi des grands nombres, on peut affirmer que le CMS testera une fois toutes les 10 connexions s'il est à jour.
  //Cette fonction a été rajoutée afin d'améliorer l'empreinte carbone de DiamondCMS en minimisant les requettes inutiles
  var random_for_maj = getRandomInt(5);
  if (random_for_maj == 2){
    $.ajax({
      url : $('#maj').attr("data-link"),
      type : 'GET',
      dataType : 'html',
      success: function (data_rep) {
        $('#maj').html(data_rep);
      }
    });
  }

  //Selon la loi des grands nombres, on peut affirmer que le CMS testera une fois toutes les 5 connexions si des messages sont à afficher.
  //Cette fonction a été rajoutée afin d'améliorer l'empreinte carbone de DiamondCMS en minimisant les requettes inutiles
  var random_for_maj = getRandomInt(10);
  if (random_for_maj == 2){
    $.ajax({
      url : "https://aldric-l.github.io/DiamondCMS/broadcast.json",
      type : 'GET',
      dataType : 'html',
      success: function (data_rep) {
        var r  = JSON.parse(data_rep);
        var div = "";
        if (r.all != null){
          div = '<div class="alert alert-' + r.all.type + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + r.all.msg + '</div>';
        }
        if (r[$('#broadcaster').attr('data-version')] != null){
          div = div + '<div class="alert alert-' + r[$('#broadcaster').attr('data-version')].type + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + r[$('#broadcaster').attr('data-version')].msg + '</div>';
        }
        $('#broadcaster').html(div);
      }
    });
  }

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
        $("#serveur_name_".concat(i)).html('<strong> Serveur ' + i + ' :</strong> ' + json_result[i]['name']);
        console.log("#etat_serveur_".concat(i));

        if (json_result[i]['results'] == false){
          if (json_result[i]['enabled'] == "true"){
            $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Déconnecté</span>');
          }else {
            $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Désactivé</span>');
          }
        }else {
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: green;">Connecté</span>');
          $("#cmd_".concat(i)).show();
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
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
          }else {    
            location.reload(true);
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
  });

  $('.serveur_sendcmd').click(function(){
    var id = $(this).attr('data-idserv');
    var link = $(this).attr('data-link');
    var cmd = $('#serveur_cmd_' + id).val();
    $.ajax({
        url : link,
        type : 'POST',
        data : 'cmd=' + JSON.stringify(cmd),
        dataType : 'html',
        success: function (data_rep) {
            console.log(data_rep);
            $('#serveur_answercmd_' + id).show();
            if (data_rep == "" || data_rep == " "){
              data_rep = "...";
            }
            $('#serveur_answercmd_' + id).html('<strong>Réponse :</strong> <span style="font-style:italic">'+ data_rep + '</span>');
        },
        error: function() {
          $('#serveur_answercmd_' + id).show();
          $('#serveur_answercmd_' + id).html('<span style="color: red;">Erreur. Impossible d\'envoyer la commande</span>');
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
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
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
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
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