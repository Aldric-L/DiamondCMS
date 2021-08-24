//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    //modal d'execution de la tache
    $(".task").click(function(){
        var id = $(this).attr('data');
        $("#task_" + id).modal('show');
    });
    $(".close_mod").click(function(){
        if (!$(this).hasClass('reloader')){
          var id = $(this).attr('data');
          $("#task_" + id).modal('hide');
        }else {
          location.reload(true);
        }  
    });
    //end modal d'execution de la tache

    //Validation du pseudo
    $(".validate_psd").click(function(){
        var id = $(this).attr('data');
        var psd = $("#psd_" + id).val();
        $(".test#" + id).prop('disabled', false);
        $(".test#" + id).attr('data', $(".test#" + id).attr('data-origin') + psd);
        $("#psd_" + id).prop('readonly', true);
        $(this).prop('disabled', true);
    });
    //end Validation du pseudo

    $(".reloader").click(function(){
        location.reload(true);
    });

    //Requette de test de connexion
    $(".test").click(function(){
        $(this).html("Chargement...");
        var id = $(this).attr('id');
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            console.log(data_rep);
            if (data_rep.substring(0, 9) == "Test OK: "){
                $('#disp_success_' + id).show();
                $('#success_' + id).html(data_rep.substring(9));
                $('.test#' + id).html("Succès !");
                $('.test#' + id).prop('disabled', true);
                $('.get#getbtn_' + id).prop('disabled', false);
                $('.get#getbtn_' + id).attr('data', $('.test#' + id).attr('data').replace('test', 'get'));
            }else {
                $('#disp_error_' + id).show();
                $('#error_' + id).html(data_rep);
                $("#psd_" + id).prop('readonly', false);
                $(".validate_psd#psdbtn_" + id).prop('disabled', false);
                $('.test#' + id).html("Réessayer !");
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site. (Ne vous inquiétez pas, aucun montant ne vous a été facturé, mais une erreur technique nous empêche de poursuivre la transaction. Merci de réessayer plus tard.)");
          }
        });
    });

    //Requette de test de connexion
    $(".get").click(function(){
        $(this).html("Chargement...");
        var id = $(this).attr('id');
        var id_real = $(this).attr('data-id');
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            console.log(data_rep);
            if (data_rep != "Success !"){
                alert('ERREUR :' + data_rep); 
                $('.get#' + id).html("Erreur ! Rechargez la page.");
                $('.get#' + id).prop('disabled', true);
                $('.close_mod#close_mod_' + id_real).addClass('reloader');
                $('.close_mod#close_mod_' + id_real).removeClass('close_mod');
            }else {
                $('.get#' + id).html("Succès !");
                $('.get#' + id).prop('disabled', true);
                $('.close_mod#close_mod_' + id_real).addClass('reloader');
                $('.close_mod#close_mod_' + id_real).removeClass('close_mod');
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site. (Ne vous inquiétez pas, aucun montant ne vous a été facturé, mais une erreur technique nous empêche de poursuivre la transaction. Merci de réessayer plus tard.)");
          }
        });
    });
});