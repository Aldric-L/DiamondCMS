//On utilise cette ligne pour verifier qu'aucun autre code n'ait changé la valeur de $
jQuery(function ($){
    $(".mainconf").click(function(){
        $.ajax({
            url : $(".mainconf").attr('data-link'),
            type : 'POST',
            data : 'Serveur_name=' + $('#Serveur_name').val() + '&protocol=' + $('#protocol').val() + '&desc=' + $('#desc').val() + '&about_footer=' + $('#about_footer').val() + '&support_en=' + $('#support_en').prop('checked') + '&vote_en=' + $('#vote_en').prop('checked') + '&lien_vote=' + $('#lien_vote').val() + '&socialgl=' + $('#socialgl').val() + '&socialtw=' + $('#socialtw').val() + '&socialyt=' + $('#socialyt').val() + '&socialdiscord=' + $('#socialdiscord').val() + '&socialfb=' + $('#socialfb').val() + '&logo=' + $('#logo').val() + '&favicon=' + $('#favicon').val(),
            dataType : 'html',
            success: function (data_rep) {
                console.log(data_rep);
            if (data_rep != "Success"){
                alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }
            },
            error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
            }
        });
    });

    $(".bddconf").click(function(){
        $.ajax({
          url : $(".bddconf").attr('data-link'),
          type : 'POST',
          data : 'host=' + $('#host').val() + '&db=' + $('#db').val() + '&usr=' + $('#usr').val() + '&pwd=' + $('#pwd').val(),
          dataType : 'html',
          success: function (data_rep) {
              console.log(data_rep);
            if (data_rep != "Success"){
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".saveserver").click(function(){
        var link = $(this).attr("data");
        var id = $(this).attr("data-id");
        $.ajax({
          url : link,
          type : 'POST',
          data : 'name=' + $('#name_'+id).val() + '&desc=' + $('#desc_' + id).val() + '&host=' + $('#host_'+id).val() + '&queryport=' + $('#queryport_'+id).val() + '&rconport=' + $('#rconport_'+id).val() + '&password=' + $('#password_'+id).val()+ '&version=' + $('#version_'+id).val() + '&enabled=' + $('#en_' +id).prop('checked') + '&game=' + $('#game_' + id + ' option:selected').val() + '&img=' + $('#img_' + id + ' option:selected').val(),
          dataType : 'html',
          success: function (data_rep) {
              console.log(data_rep);
            if (data_rep != "Success"){
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
    
    $(".suppserver").click(function(){
        var link = $(this).attr("data");
        var id = $(this).attr("data-id");
        $.ajax({
        url : link,
        type : 'POST',
        data : 'id=' + id,
        dataType : 'html',
        success: function (data_rep) {
            console.log(data_rep);
            if (data_rep != "Success"){
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {
            location.reload();
            }
        },
        error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
        });
    });

    $(".save_ns_server").click(function(){
        var link = $(this).attr('data');
        if ($('#name_ns').val() != "" && $('#desc_ns').val() != "" && $('#host_ns').val() != "" && $('#queryport_ns').val() != "" && $('#rconport_ns').val() != "" && $('#password_ns').val() != "" && $('#version_ns').val() != ""){
            $.ajax({
                url : link,
                type : 'POST',
                data : 'name=' + $('#name_ns').val() + '&desc=' + $('#desc_ns').val() + '&host=' + $('#host_ns').val() + '&queryport=' + $('#queryport_ns').val() + '&rconport=' + $('#rconport_ns').val() + '&password=' + $('#password_ns').val()+ '&version=' + $('#version_ns').val() + '&enabled=' + $('#en_ns').prop('checked') + '&game=' + $('#game_ns option:selected').val() + '&img=' + $('#img_ns option:selected').val(),
                dataType : 'html',
                success: function (data_rep) {
                    console.log(data_rep);
                    if (data_rep != "Success"){
                        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                    }else {
                        location.reload();
                    }
                },
                error: function() {
                alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                }
            });
        }else {
            alert("Formulaire incomplet : Merci de le compléter entièrement avant de sauvegarder la nouvelle configuration.");
        }
      
    });
});