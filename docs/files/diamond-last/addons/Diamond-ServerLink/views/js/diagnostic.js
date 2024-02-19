function interprete_diags(res, id, link){
    if (res["Return"] == "Disabled"){
        $("#disabled_" + id).show();
        return;
    }
    else if (res["Return"] == "Connection archived"){
        $("#success_" + id).show();
        return;
    }
    else {
        if (res['Return'] == "Query Error : Failed to read from socket."){
            $("#failure-help_" + id).html("<em><strong>Quickfix : La première chose à faire est de vérifier que votre serveur est bien allumé et connecté. N'hésitez pas à le redémarrer.</strong> Vérifiez ensuite que le port précisé pour la Query est le bon, puis que celui-ci est bien ouvert dans le cas où les deux serveurs (WEB et de jeu) ne sont pas sur le même réseau.</em>");
            $("#failure-help_" + id).show();
        }else if (res['Return'] == "Query Error : Could not create socket: L’adresse demandée n’est pas valide dans son contexte" 
        || res['Return'] == "Query Error : Could not create socket: php_network_getaddresses: getaddrinfo failed: H?te inconnu. " 
        || res['Return'] == "Query Error : Could not create socket: php_network_getaddresses: getaddrinfo failed: Hôte inconnu. "){
            $("#failure-help_" + id).html("<em><strong>Quickfix :</strong> Vérifiez l'adresse IP du serveur de jeu, elle est incorrecte.</em>");
            $("#failure-help_" + id).show();
        }else if (res['Return'] == "RCon Error : RCON authorization failed."){
            $("#failure-help_" + id).html("<em><strong>Quickfix :</strong> Vérifiez que le mot de passe RCon est correct et que le RCon est bien autorisé dans votre configuration.</em>");
            $("#failure-help_" + id).show();
        }else if (res['Return'] == "RCon Error : Failed to read from socket." 
        || res['Return'] == "RCon Error : Rcon read: Failed to read any data from socket" 
        || res['Return'] == "RCon Error : Can't connect to RCON server: Une tentative de connexion a échoué car le parti connecté n’a pas répondu convenablement au-delà d’une certaine durée ou une connexion établie a échoué car l’hôte de connexion n’a pas répondu"){
            $("#failure-help_" + id).html("<em><strong>Quickfix :</strong> Vérifiez que le port précisé pour le RCon est le bon, puis que celui-ci est bien ouvert dans le cas où les deux serveurs (WEB et de jeu) ne sont pas sur le même réseau.</em>");
            $("#failure-help_" + id).show();
        }else if (res['Return'] ==  "JSONAPI Error : Unable to reach Minecraft Server, using JSONAPI (Throwable fund)"){
            $("#failure-help_" + id).html("<em><strong>Quickfix :</strong> Vérifiez que le port et l'adresse du serveur sont corrects, et correspondent à votre configuration JSONAPI.</em>");
            $("#failure-help_" + id).show();
        }
        $("#failure-error_" + id).html("<strong> Erreur : </strong>" + res["Return"]);
        $("#failure_" + id).show();
        return;
    }
}


$(document).ready(function(e) {
    var servers = $(".server");
    Object.entries(servers).forEach(([key, value]) => {
        if (typeof(value.attributes) != "undefined"){
            var id = value.attributes['data-id'].nodeValue;
            var link = value.attributes['data-link'].nodeValue;
            $.ajax({
                url : link,
                type : 'POST',
                data : "id=" + id,
                success: function (res) {
                    $("#loader_" + id).hide();
                    try {
                        res = JSON.parse(res);
                        interprete_diags(res, id, link);
                    }catch (e){
                        $("#failure-error_" + id).html("Une erreur imprévue est survenue. Les données reçues sont inexploitables. Le serveur est probablement mal configuré.");
                        $("#failure_" + id).show();
                        return;
                    }
                },
                error: function() {
                    $("#loader_" + id).hide();
                    $("#failure_" + id).show();
                    alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
                }
            });
        }
    })
});