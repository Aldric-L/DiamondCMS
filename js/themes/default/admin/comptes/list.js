jQuery(function ($){
    var id;
    $(".modify_first_button").click(function(){
        id = $(this).attr("data");
        $("#modify_modal_" + $(this).attr("data")).modal('show');
    });
    $(".close_modify_mod").click(function(){
        id = $(this).attr("data");
        $("#modify_modal_" + $(this).attr("data")).modal('hide');
    });
    $(".mod_button").click(function(e){
        e.preventDefault();
        var link = $(this).attr("data");
        var l =  $(this).attr("data-link");
        if ($('#isban_' + link).val() == "no"){
            $.ajax({
                url : l + link + '/',
                type : 'POST',
                data : 'money=' + $('#money_' + link).val() + "&role=" + $('#role_' + link).val(),
                dataType : 'html',
                success: function (data_rep) {
                    if (data_rep != "Success"){
                        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                    }else {    
                        document.location.href = document.location.href;
                    }
                },
                error: function() {
                    alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                }
            });
        }else if ($('#isban_' + link).prop("checked") == true) {
            $.ajax({
                url : l + link + '/',
                type : 'POST',
                data : 'money=' + $('#money_' + link).val() + "&role=" + $('#role_' + link).val()+ "&isban=" + $('#isban_' + link).val(),
                dataType : 'html',
                success: function (data_rep) {
                    if (data_rep != "Success"){
                        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                    }else {    
                        document.location.href = document.location.href;
                    }
                },
                error: function() {
                    alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                }
            });
        }else {
            $.ajax({
                url : l + link + '/',
                type : 'POST',
                data : 'money=' + $('#money_' + link).val() + "&role=" + $('#role_' + link).val(),
                dataType : 'html',
                success: function (data_rep) {
                    if (data_rep != "Success"){
                        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                    }else {    
                        document.location.href = document.location.href;
                    }
                },
                error: function() {
                    alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                }
            });
        }
    });


    $(".first_ban_button").click(function(){
        id = $(this).attr("data");
        $("#ban_modal_" + $(this).attr("data")).modal('show');
    });
    $(".close_mod").click(function(){
        id = $(this).attr("data");
        $("#ban_modal_" + $(this).attr("data")).modal('hide');
    });
    $(".ban_button").click(function(){
        var link = $(this).attr("data");
        var l =  $(this).attr("data-link");
            $.ajax({
                url : l + link +'/',
                type : 'POST',
                data : 'reason=' + $('#reason_' + link).val(),
                dataType : 'html',
                success: function (data_rep) {
                    console.log(data_rep);
                if (data_rep != "Success"){
                    alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                }else {              
                    $("#is_ban_" + link).html("<span style=\"color: red;\">Banni</span>");
                    $('.first_ban_button').attr('disabled', true);
                    $("#ban_modal_" + link).modal('hide');
                }
                },
                error: function() {
                    alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                }
            });
    });

    $(".supp_profile_img").click(function(){
        var link = $(this).attr("data");
        var l =  $(this).attr("data-link");
            $.ajax({
                url : l + link + '/',
                type : 'GET',
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
});