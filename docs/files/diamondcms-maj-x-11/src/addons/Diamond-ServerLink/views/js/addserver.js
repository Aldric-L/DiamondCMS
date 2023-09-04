var loader = $("#loader").html();
var api_link = $("#config").attr("data-apiLink");
function step3(){
    // On récupère la config du nouveau serveur
    var config = DiamondSerializeArray("#config");
    console.log(config);
    $(".queryEchec").hide();
    $(".rconEchec").hide();
    $("#rconTest").html("En attente d'un résultat favorable du test Query.");

    $.ajax({
        url : api_link + "/GET/testQueryRaw",
        type : 'POST',
        data : config,
        success: function (res) {
            try {
                res = JSON.parse(res)
            }catch (e){
                var res_to_send = '<span style="color:red;">Erreur :</span> Les données reçues ne sont pas exploitables.';
                res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que les champs sont correctement remplis (l'IP en particulier, pas d'espaces à la fin des saisies).</em>"
                $("#queryTest").html(res_to_send);
                $(".queryEchec").show();
                $("#disbtn").attr("disabled", true);
                return;
            }
            console.log(res);
            if (!typeof(res) !== "Object" && !typeof(res) !== "Array"){
                var res_to_send = '<span style="color:red;">Erreur :</span> Les données reçues ne sont pas exploitables.';
                res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que les champs sont correctement complétés (l'IP en particulier, pas d'espaces à la fin des saisies).</em>"
                $("#queryTest").html(res_to_send);
                $("#disbtn").attr("disabled", true);
            }
        
            if (res['Errors'] != undefined && res['Errors'][0] != undefined && res['Errors'][1] != undefined)
                $("#queryTest").html('<strong><span style="color:red;">Error :</span> ' + res['Errors'][0] + ' - ' + res['Errors'][1] + '</strong>');
                $(".queryEchec").show();
                $("#disbtn").attr("disabled", true);
            if (res['Return'] != undefined){
                if (res['Return'] == "Connection archived"){
                    $(".queryEchec").hide();
                    $("#queryTest").html('<strong><span class="text-custom">Succès !</span> Connexion initialisée avec succès.</strong>');
                    $("#rconTest").html(loader);
                    checkRcon();
                }else {
                    if (res['Return'] == "Failed to read from socket."){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que le port précisé pour la Query est le bon, puis que celui-ci est bien ouvert dans le cas où les deux serveurs (WEB et de jeu) ne sont pas sur le même réseau.</em>"
                        $("#queryTest").html(res_to_send);
                        $("#disbtn").attr("disabled", true);
                    }else if (res['Return'] == "Could not create socket: L’adresse demandée n’est pas valide dans son contexte"){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez l'adresse IP du serveur de jeu, elle est incorrecte.</em>"
                        $("#queryTest").html(res_to_send);
                        $("#disbtn").attr("disabled", true);
                    }else if (res['Return'] ==  "Unable to reach Minecraft Server, using JSONAPI (Throwable fund)"){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que le port et l'adresse du serveur sont corrects, et correspondent à votre configuration JSONAPI.</em>"
                        $("#queryTest").html(res_to_send);
                        $("#disbtn").attr("disabled", true);
                    }else if (res['Return'] ==  "JSONAPI Error : Invalid username, password or salt."){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que le nom d'utilisateur 'Diamond-ServerLink' a bien été ajouté dans votre configuration JSONAPI, que le mot de passe est correct, ainsi que le salt 'DiamondSALT'. Nous vous rappelons que le salt et le nom d'utilisateur ne sont pas modifiable avec Diamond-ServerLink.</em>"
                        $("#queryTest").html(res_to_send);
                        $("#disbtn").attr("disabled", true);
                    }else {
                        $("#queryTest").html('<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>");
                        $("#disbtn").attr("disabled", true);
                    }
                    $(".queryEchec").show();
                }
            }


        },
        error: function() {
            alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
        }
    });
}

function checkRcon(){
    // On récupère la config du nouveau serveur
    var config = DiamondSerializeArray("#config");
    $(".queryEchec").hide();
    $(".rconEchec").hide();

    $.ajax({
        url : api_link + "/GET/testRconRaw",
        type : 'POST',
        data : config,
        success: function (res) {
            try {
                res = JSON.parse(res)
            }catch (e){
                var res_to_send = '<span style="color:red;">Erreur :</span> Les données reçues ne sont pas exploitables.';
                res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que les champs sont correctement remplis (l'IP en particulier, pas d'espaces à la fin des saisies).</em>"
                $("#rconTest").html(res_to_send);
                $(".rconEchec").show();
                $("#disbtn").attr("disabled", true);
                return;
            }
            console.log(res);
            if (!typeof(res) !== "Object" && !typeof(res) !== "Array"){
                var res_to_send = '<span style="color:red;">Erreur :</span> Les données reçues ne sont pas exploitables.';
                res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que les champs sont correctement complétés (l'IP en particulier, pas d'espaces à la fin des saisies).</em>"
                $("#rconTest").html(res_to_send);
                $("#disbtn").attr("disabled", true);
            }
        
            if (res['Errors'] != undefined && res['Errors'][0] != undefined && res['Errors'][1] != undefined)
                $("#rconTest").html('<strong><span style="color:red;">Error :</span> ' + res['Errors'][0] + ' - ' + res['Errors'][1] + '</strong>');
                $(".rconEchec").show();
            if (res['Return'] != undefined){
                if (res['Return'] == "Connection archived"){
                    $(".rconEchec").hide();
                    $("#rconTest").html('<strong><span class="text-custom">Succès !</span> Connexion initialisée avec succès.</strong>');

                    if ($("#queryTest").html() == '<strong><span class="text-custom">Succès !</span> Connexion initialisée avec succès.</strong>'){
                        $("#disbtn").attr("disabled", false);
                    }

                }else {
                    if (res['Return'] == "RCON authorization failed."){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que le mot de passe RCon est correct et que le RCon est bien autorisé dans votre configuration.</em>"
                        $("#rconTest").html(res_to_send);
                    }else if (res['Return'] == "Failed to read from socket."
                    || res['Return'] == "Rcon read: Failed to read any data from socket"  || res['Return'] == "Can't connect to RCON server: Une tentative de connexion a échoué car le parti connecté n’a pas répondu convenablement au-delà d’une certaine durée ou une connexion établie a échoué car l’hôte de connexion n’a pas répondu"){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que le port précisé pour le RCon est le bon, puis que celui-ci est bien ouvert dans le cas où les deux serveurs (WEB et de jeu) ne sont pas sur le même réseau.</em>"
                        $("#rconTest").html(res_to_send);
                    }else if (res['Return'] ==  "Unable to reach Minecraft Server, using JSONAPI (Throwable fund)"){
                        var res_to_send = '<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>";
                        res_to_send += "<br>" + "<em><strong>Quickfix :</strong> Vérifiez que le port et l'adresse du serveur sont corrects, et correspondent à votre configuration JSONAPI.</em>"
                        $("#rconTest").html(res_to_send);
                    }else {
                        $("#rconTest").html('<strong><span style="color:red;">Error :</span> ' + res['Return'] + "</strong>");
                    }
                    $(".rconEchec").show();
                    $("#disbtn").attr("disabled", true);
                }
            }


        },
        error: function() {
            alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
        }
    });
}

$(".queryRetry").click(function(e){
    e.preventDefault();
    $("#queryTest").html(loader);
    step3();
});

$(".rconRetry").click(function(e){
    e.preventDefault();
    step3();
});


$(".next").click(function(e){
    e.preventDefault();
    stepto = $(this).attr("data-to");
    need = $(this).attr("data-need");
    if (typeof(need) != "undefined"){
        needArray = need.split(", ");
        var err = false;
        needArray.forEach(element => {
            if ($("#" + element).val() == "" || $("#" + element).val() == " "){
                err = true;
            }
        });
        if (err == true){
            alert("Vous devez absolument remplir tous les champs demandés !");
            return;
        }
    }
    $("#etape" + (stepto-1)).hide();
    $("#etape" + stepto).show();

    if (stepto == 3)
        step3();
});

$(".back").click(function(e){
    e.preventDefault();
    stepto = $(this).attr("data-to");
    console.log(stepto, stepto+1);
    $("#etape" + (parseInt(stepto)+1)).hide();
    $("#etape" + (parseInt(stepto)+1)).removeClass("wow bounceInUp");
    $("#etape" + (parseInt(stepto)+1)).css("animation-name", "animation-name");
    $("#etape" + stepto).removeClass("wow bounceInUp");
    $("#etape" + stepto).css("animation-name", "animation-name");
    $("#etape" + stepto).show();

    if (stepto == 3)
        step3();
});

$("#game").on("change", (e) =>{
    switch ($("#game").val()) {
        case "Minecraft-Java":
            $("#queryport").val("25565");
            $("#rconport").val("25575");
            $(".JSON_hide").hide();
            $("#querybloc").show();
            break;
        
        case "Minecraft JSONAPI":
            $("#queryport").val("20059");
            $("#rconport").val("20059");
            $(".JSON_hide").show();
            $("#querybloc").hide();
            break;

        default:
            $("#queryport").val("27015");
            $("#rconport").val("27015");
            $(".JSON_hide").hide();
            $("#querybloc").show();
            break;
    
    }
})

$(window).keydown(function(event){
    // On désactive le enter
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
});

function allowend(){
    $("#end").attr("disabled", false)
}

function stopend(){
    $("#end").attr("disabled", true)
}