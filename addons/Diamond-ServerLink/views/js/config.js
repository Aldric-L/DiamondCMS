function launchdiags(){
    var servers = $(".server");
    Object.entries(servers).forEach(([key, value]) => {
        if (typeof(value.attributes) != "undefined"){
            var id = value.attributes['data-id'].nodeValue;
            var link = value.attributes['data-link'].nodeValue;
            $("#loader_" + id).show();
            $("#failure_" + id).hide();
            $("#success_" + id).hide();
            $("#disabled_" + id).hide();
            $.ajax({
                url : link,
                type : 'POST',
                data: DiamondSerializeFormData("#serveur_config"),
                processData : false,
                contentType : false,
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
}

function en_save(){
    var inputs = $(".in_req");
    for(let [name, val] of Object.entries(inputs)) {
        value = val.value;
        console.log(value)
        if (isNaN(val) && (val.tagName == "input" || val.tagName == "INPUT" || val.tagName == "select" || val.tagName == "SELECT") && (typeof value == "undefined" || value == "" || value == " " || value == "undefined" || (typeof value == "string" && value.length === 0))){
            $("#save").attr("disabled", true);
            return;
        }   
    }

    if (!DIC.is_termined()){
        $("#save").attr("disabled", true);
        return;
    }

    $("#save").attr("disabled", false)
}

function off_save(){
    $("#save").attr("disabled", true)
}

function returnlobby(){
    window.location.replace(window.location.href.slice(0, -1));
}

$(".rediag").click(e =>{
    launchdiags();
});

$("input").on('change', e =>{
    en_save();
    launchdiags();
});