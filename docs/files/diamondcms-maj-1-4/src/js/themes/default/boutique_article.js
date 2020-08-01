//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    //modal d'erreur
    $(".cant_buy").click(function(){
        $("#error_modal").modal('show');
    });
    $(".close_error_mod").click(function(){
        $("#error_modal").modal('hide');
    });
    //end modal d'erreur

    //modal de payement
    $(".can_buy").click(function(){
        $("#buy_modal").modal('show');
    });
    $(".close_buy_mod").click(function(){
        $("#buy_modal").modal('hide');
    });
    //end modal de payement

    $(".buy").click(function(){
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            console.log(data_rep);
            if (data_rep == '0' || data_rep == '2' || data_rep == '3'){
              alert("Erreur interne grave : Une erreur est survenue lors de la finalisation du payement. Normallement, vous n'avez pas été facturé de cette transaction, vous pouvez donc réessayer sans frais. Dans tous les cas, il est conseillé de contacter notre support au plus vite.");
            }else if (data_rep.substring(0, 4) == "url:"){
              document.location.href=data_rep.substring(4);              
            }else if (data_rep == '6'){
              alert("Erreur interne grave : Une erreur est survenue lors de la finalisation du payement (erreur de connexion). Normallement, vous n'avez pas été facturé de cette transaction, vous pouvez donc réessayer sans frais. Dans tous les cas, il est conseillé de contacter notre support au plus vite.");
            }else if (data_rep == '1'){
              alert("Erreur interne grave : Une erreur est survenue lors de la finalisation du payement. Il semblerait que vous ne disposiez pas d'assez d'argent pour poursuivre la transaction");
            }else {
              alert('Une erreur interne grave est surevenue lors de la réalisation du payement. Malheureusement, il semblerait que la facturation ait quand même été réalisée. Contactez donc au plus vite notre support pour obtenir de l\'aide.');
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site. (Ne vous inquiétez pas, aucun montant ne vous a été facturé, mais une erreur technique nous empêche de poursuivre la transaction. Merci de réessayer plus tard.)");
          }
        });
    });
});