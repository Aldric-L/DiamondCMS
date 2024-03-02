//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
function connectCallback(r){
  console.log(r);
  if (typeof(r['Return']) != "undefined" && typeof(r['Return']['isAccount']) != "undefined"){
      if (r['Return']['isAccount']){
        if (r['Return']['banned'] && typeof(r['Return']['r_ban']) != "undefined"){
          $("#connectReturn").html('<span style="color:red;">Erreur : </span> Votre compte a été banni pour le motif "' + r['Return']['r_ban'] + '".');
        }else if (r['Return']['banned']){
          $("#connectReturn").html('<span style="color:red;">Erreur : </span> Votre compte a été banni.');
        }else {
          $("#connectReturn").html('<span style="color:green;">Succès : </span> Connexion en cours de finalisation.');
          location.reload();
        }
      }else {
        $("#connectReturn").html('<span style="color:red;">Erreur : </span> Aucun compte ne correspond.');
      }
  }
}

function inscriptionCallback(r){
  console.log(r);
  if (typeof(r['Return']) != "undefined" && typeof(r['Return']['Valid']) != "undefined"){
      if (!r['Return']['Valid']){
        if (typeof(r['Return']['error']) != "undefined"){
          $("#inscriptionReturn").html('<span style="color:red;">Erreur : </span>' + r['Return']['error']);
        }else {
          $("#inscriptionReturn").html('<span style="color:red;">Erreur : </span> Une erreur inconnue est survenue.');
        }
      }else {
        $("#inscriptionReturn").html('<span style="color:green;">Succès : </span> Inscription en cours de finalisation.');
        location.reload();
      }
  }
}

function reinitCallback(r){
  console.log(r);
  if (typeof(r['Return']) != "undefined"){
      if (typeof(r['Return']['Valid']) != "undefined" && !r['Return']['Valid']){
        if (typeof(r['Return']['error']) != "undefined"){
          $("#reinitReturn").html('<span style="color:red;">Erreur : </span>' + r['Return']['error']);
        }else {
          $("#reinitReturn").html('<span style="color:red;">Erreur : </span> Une erreur inconnue est survenue.');
        }
      }else {
        $("#reinitReturn").html('<span style="color:green;">Réponse : </span> ' + r['Return']);
      }
  }
}

function endreinitCallback(r){
  console.log(r);
  if (typeof(r['Return']) != "undefined"){
      if (typeof(r['Return']['Valid']) != "undefined" && !r['Return']['Valid']){
        if (typeof(r['Return']['error']) != "undefined"){
          $("#endreinitReturn").html('<span style="color:red;">Erreur : </span>' + r['Return']['error']);
        }else {
          $("#endreinitReturn").html('<span style="color:red;">Erreur : </span> Une erreur inconnue est survenue.');
        }
      }else {
        $("#endreinitReturn").html('<span style="color:green;">Réponse : </span> ' + r['Return']);
        window.location = $("#diamondhead").attr("data-baselink");
      }
  }
}

function voteCallback(r){
  if (typeof(r["State"]) != "undefined" && typeof(r["Return"]["vote_link"]) != "undefined" && r["State"] == 1)
    window.location = r["Return"]["vote_link"];
}


jQuery(function ($){
  /* MODALS (header) */
  $(".serveur").click(function(){
    $("#serveur_box").show();
  });
  $(".inscription").click(function(){
    $("#inscription_modal").modal('show');
  });
  $(".connexion").click(function(){
    $("#connexion_modal").modal('show');
  });
  $(".pswd_reinit").click(function(e){
    e.preventDefault();
    $("#connexion_modal").modal('hide');
    $("#reinit_modal").modal('show');
  });
  //END MODALS

  //Formulaires Inscription/connexion
  $("#connexion_form").submit(function(e){
    if ($("#pseudo_connexion").val() == "" || $("#mp_connexion").val() == ""){
      e.preventDefault();
      $("#champs_co").css("display", "block");
    }
  });
  $("#inscription_form").submit(function(e){
    if ($("#pseudo_inscription").val() == "" || $("#mp_inscription").val() == "" || $("#mp2_inscription").val() == "" || $("#email_inscription").val() == ""){
      e.preventDefault();
      $("#champs_inscription").css("display", "block");
    }
  });
  //END FORMS

  //Ce code est obligatoire, sa suppression, même par un modeur pour créer un nouveau thème expose à la fois l'utilisateur du thème et le modeur à des poursuites judciaires. DiamondCMS est un projet gratuit, opensources, et ne vous demande que de laisser un lien vers son site en échange de sa gratuité.
  if ($("#msg_dcms").html() == undefined || $("#msg_dcms").html() == "" || $("#msg_dcms").html() == " " || $("#msg_dcms") == undefined || $("#msg_dcms")[0].innerText == undefined || $("#msg_dcms")[0].innerText == "" || $("#msg_dcms")[0].innerText == " " || $("#msg_dcms").children().length == 0 ){
    $(".footer-company-about").html($(".footer-company-about").html() + '<br>Nous utilisons la version gratuite de DiamondCMS beta pour notre site internet : <a href="https://aldric-l.github.io/DiamondCMS/">Découvrez-la !</a>');
  }
});
