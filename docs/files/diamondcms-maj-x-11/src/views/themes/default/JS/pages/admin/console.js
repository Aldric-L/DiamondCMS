jQuery(function ($){
    var api_link = $("#command").attr("data-api");
    var txt_buffer;
    $("#scriptLoader").change(function() {
        $("#import_status").html("Chargement du fichier...");
        var fileReader = new FileReader();
        //console.log(this.files[0], this.files[0].name.substr(-7), this.files[0].type);
        if ((this.files[0].type != "text/plain" && this.files[0].type != undefined && this.files[0].type != "") || this.files[0].name.substr(-7) != ".dshell"){
            $("#import_status").html('<span style="color: red;"><strong>Erreur :</strong> Le fichier chargé n\'est pas de la bonne extension ou du bon type MIME.</span>');
            $("#exec").attr("disabled", true);
            return;
        }
        fileReader.readAsText(this.files[0]);
        fileReader.onload = function(fileEvent) {
            txt_buffer = fileReader.result.split("\r\n");
            $("#import_status").html('<span class="text-custom"><Strong>Succès : </strong>Fichier chargé.</span>');
            $("#exec").attr("disabled", false);
        }
    });

    $("#exec").on("click", ()=>{
        $("#exec").html("Exécution en cours... Patientez !");
        if (txt_buffer != null && txt_buffer != "" && txt_buffer != undefined){
            txt_buffer.forEach(element => cmd_send(element));
        }
        $("#exec").html("Terminé !");
    });
    var cmd_stocked = [];
    var iterator = 0;

    function scrollDown(){
        var objDiv = document.getElementById("console");
        objDiv.scrollTop = objDiv.scrollHeight;
    }

    function resultParser(result){
        try {
            res = JSON.parse(result)
        }catch (e){
            $("#console").append("<p>Error : Parsing error.</p>");
            return;
        }
        if (res['State'] == undefined)
            $("#console").append("<p>Error : Parsing error.</p>");
        var res_to_send = "";
        if (res['State'] != 1)
            res_to_send += '<p><strong>State :</strong> <span style="color: red">Error</span> <br>';
        else 
            res_to_send += '<p><strong>State :</strong> <span style="color: green">Valid</span> <br>';
       
        
        if (res['Errors'] != undefined && res['Errors'][0] != undefined && res['Errors'][1] != undefined)
            res_to_send += '<strong>Error :</strong> ' + res['Errors'][0] + ' - ' + res['Errors'][1];
        console.log(res['Return'], typeof(res['Return']));
        if (res['Return'] != undefined && typeof(res['Return']) == "string"){
            res_to_send += '<strong>Return :</strong> ' + res['Return'];
        }else if (res['Return'] != undefined && typeof(res['Return']) == "object"){
            res_to_send += '<strong>Return :</strong><br> ';
            res_to_send += JSON.stringify(res['Return']);
        }
            
        $("#console").append(res_to_send + "</p>");
    }

    function cmd_send(cmd_brut){
        if (cmd_brut.substr(0,1) == "#" || cmd_brut.substr(0,2) == "//"){
            $("#console").append("<p>> <em>" + cmd_brut + "</em></p>");
            return;
        }

        var request = [];
        var arguments = {};
        var word_buffer = "";
        var arg_buffer = "";
        var val_buffer = "";
        var choice_buffer = "arg_buffer";
        var ignore = false;
        var bis_ignore = false;
        for (var i=0; i < cmd_brut.length; i++){
            if (request.length < 3){
                if (cmd_brut[i] != ' '){
                    word_buffer += cmd_brut[i];
                }else if (word_buffer != "") {
                    request.push(word_buffer);
                    word_buffer = "";
                }
            }else {
                if (!ignore && !bis_ignore && (cmd_brut[i] == '=' || cmd_brut[i] == ' ')){
                    if (choice_buffer == "arg_buffer" && arg_buffer != ""){
                      choice_buffer = "val_buffer";
                    }
                    else {
                      choice_buffer = "arg_buffer";
                      arguments[arg_buffer] = val_buffer;
                      arg_buffer = val_buffer = "";
                    }
                }
                else if (cmd_brut[i] == '"'){
                    if (bis_ignore){
                      bis_ignore = false;
                      if (choice_buffer == "arg_buffer")
                        arg_buffer += cmd_brut[i];
                      else 
                        val_buffer += cmd_brut[i];
                    }else{
                      ignore = !ignore;
                    }
                }
                else if (cmd_brut[i] == "#"){
                  bis_ignore = true;
                }
                else if (cmd_brut[i] != '=') {
                    bis_ignore = false;
                    if (choice_buffer == "arg_buffer")
                        arg_buffer += cmd_brut[i];
                    else 
                        val_buffer += cmd_brut[i];
                }
                
            } 
        }
        if (request.length < 3 && word_buffer != "")
            request.push(word_buffer);
        else if (arg_buffer != "" && val_buffer != "")
            arguments[arg_buffer] = val_buffer;

        console.log(request, arguments, cmd_brut);

        if (request.length < 3){
            $("#console").append("<p>> " + cmd_brut + "</p>");
            $("#console").append("<p><strong>Error : </strong>Bad parameters (Not enough words)</p>");
            scrollDown();
            return false;
        }

        $("#console").append("<p>> " + cmd_brut + "</p>");
        cmd_stocked.push(cmd_brut);
        iterator = cmd_stocked.length;
        if (request[1] == "GET" || request[1] == "get" || request[1] == "Get" || request[1] == "SET" || request[1] == "set" || request[1] == "Set"){
          $("#command").val("");
          $.ajax({
              url : api_link + request[0] + "/" + request[1].toLowerCase()  + "/" + request[2],
              type : 'POST',
              async: false,
              data : arguments,
              dataType : 'html',
              success: function (result) { 
                  resultParser(result);
                  scrollDown();
              },
              error: function() {
                  $("#console").append("<p>Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.</p>");
              }
          });
        }else {
          $("#console").append("<p><strong>Error : </strong>Request verb unknown.</p>");
          scrollDown();
          return false;
        }
    }

    $(window).keydown(function(event){
        if(event.keyCode == 40){
            if (iterator < cmd_stocked.length)
                iterator++;
            $("#command").val(cmd_stocked[iterator]);
        }else if (event.keyCode == 38){
            if (iterator > 0)
                iterator--;
            $("#command").val(cmd_stocked[iterator]);
            
        }
        if(event.keyCode == 13 && $("#command").val() != "" && $("#command").val() != " " ) {
          event.preventDefault();
          
          cmd_send($("#command").val());
            
          return false;
        }
    });


    $("#getlogmod").click((e) => {
        var api_link = $("#command").attr("data-api");
        var target = $("#lastApiCalls");
        var todisp = "";
        $.ajax({
            url : api_link + "admin/get/lastApiCalls/",
            type : 'GET',
            dataType : 'html',
            success: function (result) { 
                try {
                    var res = JSON.parse(result);
                }catch (e){
                    target.html('<p class="text-danger">Erreur : impossible de charger la ressource demandée.</p>');
                    return;
                }
                if (typeof(res['Return']) == "undefined" || typeof(res['State']) == "undefined" || res['State'] == "0"){
                    target.html('<p class="text-danger">Erreur : impossible de charger la ressource demandée.</p>');
                    return;
                }
                for(let i in res['Return']){
                    var obj = res['Return'];
                    todisp += "<h3><strong>Requête du " + obj[i]['date'] +"</strong></h3>";
                    todisp += "<p><strong>Commande : </strong>";
                    for(let j in obj[i]['parameters']){
                        var o = obj[i]['parameters'];
                        if (o[j] != "api"){
                            todisp += o[j] + " ";
                        }
                    }
                    todisp += "<br />";
                    todisp += "<strong>Utilisateur : </strong>" + obj[i]['user'] +"<br />";
                    todisp += "<strong>Niveau d'autorisation accordé : </strong>" + obj[i]['level'] +" / 5 (Elevation de privilèges : " + ((obj[i]['privilege_elevation'] == true) ? "<strong>OUI</strong>" : "non") + ")<br />";
                    if (obj[i]['privilege_elevation'] == true && typeof(obj[i]['traceback'][0]['file']) != "undefined")
                    todisp += "Autorisation accordée par : <em>" + ((obj[i]['traceback'][0]['file'].includes("boutique.getback.php") ? "Boutique (Achat d'un article - Controleur Getback)" : obj[i]['traceback'][0]['file'])) +"</em><br />";

                    todisp += "<strong>Arguments fournis : </strong>";
                    if (((typeof(obj[i]['args']) ==  "Object" || typeof(obj[i]['args']) ==  "object") && Object.keys(obj[i]['args']).length === 0) || (typeof(obj[i]['args']) ==  "Array" && obj[i]['args'].length === 0)){
                        todisp +="Aucun <br />";
                    }else if (typeof(obj[i]['args']) ==  "Object" || typeof(obj[i]['args']) ==  "object"){
                        for(let j in obj[i]['args']){
                            var o = obj[i]['args'];
                            if(typeof(o[j]) != "string" && typeof(o[j]) != "int" && typeof(o[j]) != "bool" && typeof(o[j]) != "number"  ){
                                todisp += j + "=donnée non supportée"  + " ";
                            }else {
                                todisp += j + "=" + o[j] + " ";
                            }
                        }
                        todisp += "<br />";
                    }
                    todisp += "<strong>Réponse du serveur : </strong>";
                    if ((typeof(obj[i]['result']) != "Object" && typeof(obj[i]['result']) != "object") || typeof(obj[i]['result']['State']) == "undefined" || typeof(obj[i]['result']['Errors']) == "undefined"){
                        todisp +="Donnée inexploitable <br />";
                    }else if ((typeof(obj[i]['result']) == "Object" || typeof(obj[i]['result']) == "object")){
                        if (obj[i]['result']['State'] == '1'){
                            todisp +="Succès <br />";
                        }else{
                            if (typeof(obj[i]['result']['Errors']) == "object" && obj[i]['result']['Errors'].length == 2)
                                todisp +="Erreur - " + obj[i]['result']['Errors'][1] + " (code " + obj[i]['result']['Errors'][0] +") <br />";
                            else 
                                todisp +="Erreur <br />";
                        }
                    }
                    
                    todisp += "</p><hr>";

                    console.log(obj[i]);
                }
                target.html(todisp);
            },
            error: function() {
                $("#console").append("<p>Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.</p>");
            }
        });
    })
});