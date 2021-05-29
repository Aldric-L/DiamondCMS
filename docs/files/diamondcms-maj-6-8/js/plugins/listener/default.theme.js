//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
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
