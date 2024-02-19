//On utilise cette ligne pour verifier qu'aucun autre code n'ait changé la valeur de $
jQuery(function ($){
    if (typeof(tinyMCE) != "undefined"){
        tinyMCE.each(tinyMCE.editors, (ev,t) => {
            ev.on('change', function(e){
                //console.log("SAVED !", e.target.getContent(), e.target.getBody().dataset.id);
                if (e.target.getBody().dataset.id == "accueilpopupcontent"){
                    $("#accueilpopup_content").html(e.target.getContent());
                    $(":input[name=text_popup_accueil]").val(e.target.getContent());
                }else {
                    $("#modal_jouer_content").html(e.target.getContent());
                    $(":input[name=text_jouer_menu]").val(e.target.getContent());
                }
                
            });
        });
    }else {
        $("#jouercontent").on("change", (e)=>{
            $("#modal_jouer_content").html($("#jouercontent").val());
            $(":input[name=text_jouer_menu]").val($("#jouercontent").val());
        });
        $("#accueilpopupcontent").on("change", (e)=>{
            $("#accueilpopup_content").html($("#accueilpopup").val());
            $(":input[name=text_popup_accueil]").val($("#accueilpopup").val());
        });
    }
    


});



