    function loadingError(){
        $("#DSL-iframe-loader").hide();
        $("#DSL-iframe-requestdepend").show();
        var r = $("#DSL-iframe-requestdepend").html();
        r += "<p><strong>Erreur Diamond-ServerLink :</strong> Imposible de traiter correctement les données reçues pour afficher l'état des serveurs.</p>";
        $("#DSL-iframe-requestdepend").html(r);
    }

    $("#DSL-iframe-requestdepend").hide();
    $.ajax({
      url : $("#infos-servers").attr('data-link'),
      type : 'GET',
      dataType : 'html',
      success: function (res) {
        $("#DSL-iframe-loader").hide();
        try {
            res = JSON.parse(res);
        }catch (e){
            return loadingError();
        }
        if (typeof(res) == "undefined" || typeof(res['State']) == "undefined" && res['State'] === 1)
            return loadingError();
        
        if (Object.keys(res['Return']).length == 0){
            var r = $("#DSL-iframe-requestdepend").html();
            r += '<p class="text-center">Aucun serveur de jeu n\'est pour le moment initialisé avec Diamond-ServerLink. <br><em>N\'hésitez pas à en ajouter un dans l\'Assistant de configuration.</em></p>';
            $("#DSL-iframe-requestdepend").html(r);
            $("#DSL-iframe-requestdepend").show();
        }
        for (var i = 1; Object.keys(res['Return']).length >= i; i++){
            var r = $("#DSL-iframe-requestdepend").html();
            if (res['Return'][i]['results'] == false && res['Return'][i]['enabled'] == false){
                r += '<p><strong>Serveur ' + i + ' : ' + res['Return'][i]['name'] + '</strong> - Etat du serveur : <span style="color: red;">Désactivé</span></p>'
            }else if(res['Return'][i]['results'] == false && res['Return'][i]['enabled'] == true){
                r += '<p><strong>Serveur ' + i + ' : ' + res['Return'][i]['name'] + '</strong> - Etat du serveur : <span style="color: red;">Déconnecté</span></p>'
            }else if(typeof(res['Return'][i]['results']) == "Object" || typeof(res['Return'][i]['results']) == "object") {
                r += '<p><strong>Serveur ' + i + ' : ' + res['Return'][i]['name'] + '</strong> - Etat du serveur : <span style="color: green;">Connecté</span></p>'
                r += '<div class="cmd" style="margin-bottom: 1em;">';
                r += '  <form id="sendserv_'+ i +'">';
                r += '      <div class="row">';
                r += '          <div class="col-sm-9 ">';
                r += '               <input type="hidden" name="id" value="' + i + '">';
                r += '               <input type="text" class="form-control" name="cmd" placeholder="Commande à exécuter">';
                r += '           </div>';
                r += '          <div class="col-sm-3">';
                r += '              <button type="button" class="btn btn-success ajax-simpleSend" style="width: 100%;"';
                r += '                      data-api="' + $("#infos-servers").attr('data-baselink') + 'api/" data-module="serveurs/" data-verbe="set" data-func="execOnServer"';
                r += '                      data-tosend="#sendserv_' + i + '" data-useform="true" data-reload="false" data-showreturn="true" data-needAll="true">Envoyer</button>';
                r += '          </div>';
                r += '       </div>';
                r += '  </form>';
                r += '</div>'
            }else {
                return loadingError();
            }
            $("#DSL-iframe-requestdepend").html(r);
            $("#DSL-iframe-requestdepend").show();
            /*$(".ajax-simpleSend").click(function(e){
                processSimpleSend(e, $(this));
                e.stopPropagation();
            });*/
        }
        $("#DSL-iframe-requestdepend > .ajax-simpleSend").click(function(e){ processSimpleSend(e, $(this)); });
      },
      error: function() {
        return loadingError();
      }
    });