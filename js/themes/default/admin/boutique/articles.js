//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    $(".save_article_modifs").click(function(){
        var id = $(this).attr('id');
        var link = $(this).attr('data');
        var nb_servers = $("#" + id + "_nb_servers").val();
        if (nb_servers != "false"){
            var form_servers = [];
            for (var i=1; i<=nb_servers; i++ ){
              var under_server = [];
              under_server.push(i);
              under_server.push($('#' + id + '_' + i + '_en_serveur').prop('checked'));
              under_server.push($('#' + id + '_' + i + '_mustbe_connected').prop('checked'));
              under_server.push($('#' + id + '_' + i + '_cmd').val());
              form_servers.push(under_server);
            }
    
            $.ajax({
              url : link,
              type : 'POST',
              data : { id: id, servers: form_servers, name: $('#' + id + '_name').val(), desc: $('#' + id + '_desc').val(), prix: $('#' + id + '_prix').val(), cat: $('#' + id + '_cat option:selected').val()},
              dataType : 'html',
              success: function (data_rep) {
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
            $.ajax({
              url : link,
              type : 'POST',
              data : { id: id, name: $('#' + id + '_name').val(), desc: $('#' + id + '_desc').val(), prix: $('#' + id + '_prix').val(), cat: $('#' + id + '_cat option:selected').val()},
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
        }
        
    });

    $(".enable").click(function(){
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
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

    $(".delete_article").click(function(){
      var id = $(this).attr('id');
      var link = $(this).attr('data');
      console.log(link);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(id);
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              $('#line_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $('.open_modal').click(function(){
      console.log('#modal_article_' + $(this).attr('id'));
      $('#modal_article_' + $(this).attr('id')).modal('show');
    });

    $('.close_modal').click(function(){
      $('#modal_article_' + $(this).attr('id')).modal('hide');
    });
});