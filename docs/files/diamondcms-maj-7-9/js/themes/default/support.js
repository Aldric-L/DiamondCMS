//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){

    $('.addTicket').click(function(){
        $('#create_ticket').modal('show');
    });

    $('#newticket_form').submit(function(e){
        e.preventDefault();
        if ($("#title").val() == "" || $("#content").val() == ""){
          $("#champs_newticket").css("display", "block");
        }else {
          $.ajax({
            url : $("#hidden_url_nt").val(),
            type : 'POST',
            data : {title: $('input[name="title"]', this).val(), content: $('textarea[name="content"]', this).val()},
            dataType : 'html',
            success: function (data_rep){
              if (data_rep != "Success"){
                alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
              }else {
                location.reload(true);
              }
            },
            error: function (data_rep) {
              alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
            }     
        });
        }
    });

    //MODAL View ticket
    $(".v").click(function(){
        var id = $(this).attr("data");
        $("#modal_ticket_" + id).modal('show');
    });
    //END modal

    //REPONSE DANS MODAL TICKET
    $(".rep").click(function(){
        var id = $(this).attr("data");
        if ($('#rep_div_' + id).is(":hidden")){
          $('#rep_' + id).html('<h4 class="text-center">Répondre... <i class="fa fa-arrow-up" aria-hidden="true"></i></h4>');
          $('#rep_div_' + id).show();
        }else {
          $('#rep_' + id).html('<h4 class="text-center">Répondre... <i class="fa fa-arrow-down" aria-hidden="true"></i></h4>');
          $('#rep_div_' + id).hide();
        }
      });
      //END REPONSE

    //Ajout d'une réponse
    $('.form_reponse').submit(function(e){
        var id = $('input[class="hidden_id"]', this).val();
        e.preventDefault();
        tinyMCE.triggerSave(true, true);
        if($('textarea', this).val() != "" && $('textarea', this).val() != "<br>"){
        $.ajax({
                url : $('#hidden_url_'+id).val(),
                type : 'POST',
                data : {pseudo: $('#hidden_pseudo_'+id).val(), content: $('textarea#'+id).val() },
                dataType : 'html',
                success: function (data_rep){
                if (data_rep != "Success"){
                    alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
                    console.log(data_rep);
                    debugger;
                }else {
                    location.reload(true);
                }
                },
                error: function (data_rep) {
                alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                }     
            });
        }
    });
  //END ADD         
    
});