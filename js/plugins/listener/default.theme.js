//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){

  /* MODALS (header) */
  $(".serveur").click(function(){
    $("#serveur_box").show();
  });
  $(".jouer").click(function(){
    $("#modalJouer").modal('show');
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
});
