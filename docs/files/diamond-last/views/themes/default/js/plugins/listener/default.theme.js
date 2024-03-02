
function successfunc(result, api_link, showreturn, should_reload, thisbtn=undefined, oldhtml=undefined, private_callback=undefined, th=undefined){
    if (typeof(result) == "undefined" || result == null || result == "")
        return;
    try {
        var r = JSON.parse(result);
    }catch (e){
        alert("Erreur fatale code 112 - Impossible d'interpréter le retour du serveur (Parse error). Contacter un administrateur ou le support DiamondCMS.");
        return;
    }
    //console.log(r);
    if (r['State'] == 1 && (r['Errors'] == null || typeof(r['Errors']) == "undefined")){
        if (showreturn == true && r['Return'] != undefined && r['Return'] != null){
            alert("Réponse du serveur : " + r['Return'])
        }
        if (should_reload)
            location.reload(true);
            
        if (typeof(thisbtn) != "undefined")
            thisbtn.html(oldhtml);
            
        if (typeof(private_callback) != "undefined" && typeof(th) != "undefined" )
            window[private_callback](r, th);
        else if (typeof(private_callback) != "undefined" )
            window[private_callback](r);
        
    }else {
        $.ajax({
            url : api_link + "tools/GET/errorcontent",
            type : 'POST',
            data : 'code=' + r['Errors'][0],
            dataType : 'html',
            success: function (err_res) {
                var e_res = JSON.parse(err_res);
                if (e_res['State'] == 1 && e_res['Errors'] == null){
                    var str_err = "Erreur : code " + r['Errors'][0] + " - " + e_res['Return'] + " ";
                    if (showreturn == true && r['Return'] != undefined && r['Return'] != null){
                        str_err = str_err + "- Réponse originelle du serveur : " + r['Return'];
                    }
                    else if (showreturn == true && r['Errors'][1] != undefined && r['Errors'] != null){
                        str_err = str_err + "- Réponse originelle du serveur : " + r['Errors'][1];
                    }
                    alert(str_err);
                }else{
                    alert("Erreur fatale code 121 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
                }
                console.log("Réponse du serveur : " + r['Errors'][1]);
                if (typeof(thisbtn) != "undefined")
                    thisbtn.html(oldhtml);
                if (typeof(private_callback) != "undefined")
                    window[private_callback](r);
            },
            error: function() {
                if (typeof(thisbtn) != "undefined")
                    thisbtn.html(oldhtml);
                alert("Erreur fatale code 121 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
            }
        });
        
    }
}

function serialinter(target, targetform){
    let vals = {};
    for (i = 0; i < target.length; i++) {
        e = target[i];
        if (e.type == "checkbox"){
            vals[e.name] = target[i].checked;
        }else if (e.type == "text" ||e.type == "select" ||e.type == "password" ||e.type == "email" ||e.type == "number" ||e.type == "hidden" || e.localName == "select"){
            vals[e.name] = target[i].value;
        }else if (e.type == "textarea" || e.localName == "textarea"){
            if (typeof tinymce === 'undefined') {
                vals[e.name] = target[i].value;
            }else {
                for (var ed of tinyMCE.editors){
                    if (ed.formElement == $(targetform)[0]){
                        vals[e.name] = ed.getContent();
                        break;
                    }
                }
                //vals[e.name] = tinyMCE.activeEditor.getContent();
            }
        }else if (e.type == "file"){
            if (target[i].files.length === 0){
                vals[e.name] = undefined;
            }else if (target[i].files.length >= 1){
                if (target[i].files.length > 1)
                    alert("Un seul fichier ne peut être envoyé à la fois. Seul le premier fichier est considéré.");
                vals[e.name] = target[i].files[0];
            }
        }else if (e.type == "button" && e.id == "dic_launcher"){
            console.log(e, DIC.get_contentSelected());
            dic_content = DIC.get_contentSelected();
            if (dic_content != undefined){
                for(var pair of dic_content) {
                    vals[pair[0]] = pair[1];
                }
            }   
        }
    }
    return vals;
}
function DiamondSerializeArray(form){
    $i = 0;
    while ($(form)[$i] == undefined && $i < 5){ $i++; }
    return serialinter($(form)[$i].elements, form);
}

function DiamondSerializeFormData(form){
    $i = 0;
    while ($(form)[$i] == undefined && $i < 5){ $i++; }
    return getFormData($(form)[$i].elements, form);
}

function getFormData(target, targetform){
    var fdata = new FormData();
    for (i = 0; i < target.length; i++) {
        e = target[i];
        if (e.type == "checkbox"){
            fdata.append( e.name, target[i].checked );
        }else if (e.type == "text" ||e.type == "select" ||e.type == "password" ||e.type == "email" ||e.type == "number" ||e.type == "hidden" || e.localName == "select"){
            fdata.append( e.name, target[i].value );
        }else if (e.type == "textarea" || e.localName == "textarea"){
            if (typeof tinymce === 'undefined') {
                fdata.append( e.name, target[i].value );
            }else {
                for (var ed of tinyMCE.editors){
                    if (ed.formElement == $(targetform)[0]){
                        fdata.append( e.name,ed.getContent() );
                        break;
                    }
                }
                //fdata.append( e.name,tinyMCE.activeEditor.getContent() );
            }
        }else if (e.type == "file"){
            if (target[i].files.length === 0){
                fdata.append( e.name, undefined );
            }else if (target[i].files.length >= 1){
                if (target[i].files.length > 1)
                    alert("Un seul fichier ne peut être envoyé à la fois. Seul le premier fichier est considéré.");
                
                fdata.append( e.name, target[i].files[0] );
            }
        }else if (e.type == "button" && e.id == "dic_launcher"){
            console.log(e, DIC.get_contentSelected());
            dic_content = DIC.get_contentSelected();
            if (dic_content != undefined){
                for(var pair of dic_content) {
                    fdata.append( pair[0], pair[1] );
                }
            }   
        }
    }
    return fdata;
}

function checkFields(form){
    $i = 0;
    while ($(form)[$i] == undefined && $i < 5){ $i++; }
    var target = $(form)[$i].elements;
    for (i = 0; i < target.length; i++) {
        e = target[i];
        if (e.type == "text" ||e.type == "select" ||e.type == "password" ||e.type == "email" ||e.type == "number" ||e.type == "hidden" || e.localName == "select"){
            if (!e.classList.contains("dic_input") && typeof(e.dataset.neededforvalidation) != "undefined" && e.dataset.neededforvalidation == "true" && (target[i].value == "" || target[i].value == " " || target[i].value == undefined || typeof(target[i].value) == "undefined"))
                return false;
        }else if (e.type == "textarea" || e.localName == "textarea"){
            if (typeof tinymce === 'undefined') {
                if (!e.classList.contains("dic_input") && typeof(e.dataset.neededforvalidation) != "undefined" && e.dataset.neededforvalidation == "true" && (target[i].value == "" || target[i].value == " " || target[i].value == undefined || typeof(target[i].value) == "undefined"))
                    return false;
            }else {
                if (!e.classList.contains("dic_input") && typeof(e.dataset.neededforvalidation) != "undefined" && e.dataset.neededforvalidation == "true" && (tinyMCE.activeEditor.getContent() == "" || tinyMCE.activeEditor.getContent() == " " || tinyMCE.activeEditor.getContent() == undefined || typeof(tinyMCE.activeEditor.getContent()) == "undefined"))
                    return false;
            }
        }else if (e.type == "file"){
            if (!e.classList.contains("dic_input") && typeof(e.dataset.neededforvalidation) && e.dataset.neededforvalidation == "true" && target[i].files.length === 0){
                return false;
            }
        }
        else if (e.type == "button" && e.id == "dic_launcher"){
            if (!(DIC.get_contentSelected() != undefined))
                return false;
        }
    }
    return true;
}

    /**
     * AJAX Simple Send - Fonction de simplification des requêtes XHR vers l'API DiamondCMS interne
     * Cette fonction est à privilégier car elle est propre, elle évite la redondance et surtout elle permet d'afficher la bonne erreur à l'utilisateur en cas de problème.
     * 
     * @author Aldric L.
     * @copyright 2022
     * 
     * Usage : Pour contacter l'API : api/admin/set/addonstate avec la requête POST par exemple :
     * <button 
            data-api="<?= LINK; ?>api/" data-module="admin/" data-verbe="set" data-func="addonstate" data-tosend="addon=<?php echo $addon[0]; ?>" data-reload="true"
            class="ajax-simpleSend ">...
        </button>
     * 
     */
    function processSimpleSend(e, th){
        e.preventDefault();
        e.stopPropagation();
        var api_link = (typeof(th.attr("data-api")) != "undefined" && th.attr("data-api") !== "") ? th.attr("data-api") : $("#diamondhead").attr("data-baselink")+ "api/";
        var api_module = th.attr("data-module");
        var verbe = th.attr("data-verbe");
        var func = th.attr("data-func");
        var should_reload = (th.attr("data-reload") === "true") ? true : false;
        var tosend = th.attr("data-tosend");
        var isform = (th.attr("data-useform") === "true") ? true : false;
        var showreturn = (th.attr("data-showreturn") === "true") ? true : false;
        var private_callback = th.attr("data-callback");
        var noloading = (th.attr("data-noloading") === "true") ? true : false;
        var needAll = (th.attr("data-needAll") === "true") ? true : false;

        var thisbtn = th;
        var oldhtml = thisbtn.html();
        if (noloading != "true")
            thisbtn.html("Chargement...");

        if (typeof(th.attr("data-toggle")) != "undefined" && th.attr("data-toggle") == "modal" && typeof(th.attr("data-target")) != "undefined"){
            $(th.attr("data-target")).modal("show");
        }

        if (typeof(th.attr("data-dismiss")) != "undefined" && th.attr("data-dismiss") == "modal"){
            th.parents(".modal").modal("hide");
        }

        // POST or not POST ? That is the question...
        if (tosend != null && tosend != undefined){
            //Form or not Form ? That is the question back...
            if (isform && isform){
                var fdta = DiamondSerializeFormData(tosend);

                //Prevent default for the form
                $(tosend).on('submit', (e)=>{ e.preventDefault(); })

                if (!needAll){
                    var checkIfAllFilled = true;
                    checkIfAllFilled = checkFields(tosend);
                    if (checkIfAllFilled == false){
                        alert("Erreur ! Le formulaire n'a pas été entièrement complété. Veuillez terminer votre saisie avant de l'envoyer.");
                        if (noloading != "true")
                            thisbtn.html(oldhtml);
                        return;
                    }
                    $.ajax({
                        url : api_link  + api_module + verbe + "/" + func,
                        type : 'POST',
                        data: fdta,
                        processData : false,
                        contentType : false,
                        success: function (result) { successfunc(result, api_link, showreturn, should_reload, thisbtn, oldhtml, private_callback, th); },
                        error: function() {
                            alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
                        }
                    });
                }else {
                    var tosendArray = DiamondSerializeArray(tosend);
                    var emptyness = false;
                    for (let key in tosendArray) {
                        if (tosendArray[key] == null || tosendArray[key] == undefined || typeof(tosendArray[key]) == undefined || (typeof(tosendArray[key]) == "string" && tosendArray[key].length == 0)){
                            emptyness = true; 
                        }
                    }
                    if (needAll == true && emptyness == true){
                        alert("Erreur ! Le formulaire n'a pas été entièrement complété. Veuillez terminer votre saisie avant de l'envoyer.");
                        if (noloading != "true")
                            thisbtn.html(oldhtml);
                        return;
                    }
                    $.ajax({
                        url : api_link  + api_module + verbe + "/" + func,
                        type : 'POST',
                        data: fdta,
                        processData : false,
                        contentType : false,
                        dataType : false,
                        success: function (result) { successfunc(result, api_link, showreturn, should_reload, thisbtn, oldhtml, private_callback, th); },
                        error: function() {
                            alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
                        }
                    });
                }
            }else {
                $.ajax({
                    url : api_link  + api_module + verbe + "/" + func,
                    type : 'POST',
                    data : tosend,
                    dataType : 'html',
                    success: function (result) { successfunc(result, api_link, showreturn, should_reload, thisbtn, oldhtml, private_callback, th); },
                    error: function() {
                        alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
                    }
                });
            }
        }else {
            $.ajax({
                url : api_link  + api_module + verbe + "/" + func,
                type : 'GET',
                dataType : 'html',
                success: function (result) { successfunc(result, api_link, showreturn, should_reload, thisbtn, oldhtml, private_callback, th); },
                error: function() {
                    alert("Erreur fatale code 111 - Impossible de trouver l'erreur. Contacter un administrateur ou le support DiamondCMS.");
                }
            });
        }
    }

        $(".ajax-simpleSend").click(function(e){
            processSimpleSend(e, $(this));
            e.preventDefault();
            e.stopPropagation();
        });


$(document).ready(function(e){
    $('.cp').colorpicker({}); 
})