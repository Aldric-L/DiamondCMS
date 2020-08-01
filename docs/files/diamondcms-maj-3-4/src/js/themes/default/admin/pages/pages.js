//On utilise cette ligne pour verifier qu'aucun autre code n'ait changé la valeur de $
jQuery(function ($){
    $(".delete").click(function(){
      var link = $(this).attr('data-link');
      var redirect = $(this).attr('data-redirect');
      console.log(link);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
                document.location.href=redirect;
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $("#name").change(function(){
        var value = $("#name").val();
        value = value.toLowerCase();
        value = value.replaceAll('é', 'e');
        value = value.replaceAll('è', 'e');
        value = value.replaceAll('ê', 'e');
        value = value.replaceAll('@', 'a');
        value = value.replaceAll(' ', '_');
        value = value.replaceAll('#', '');
        value = value.replaceAll('[', '');
        value = value.replaceAll(']', '');
        value = value.replaceAll('(', '');
        value = value.replaceAll(')', '');
        value = value.replaceAll('{', '');
        value = value.replaceAll('}', '');
        value = value.replaceAll("'", '');
        value = value.replaceAll('"', '');
        value = value.replaceAll('~', '');
        value = value.replaceAll('^', '');
        value = value.replaceAll('$', '');
        value = value.replaceAll('£', '');
        value = value.replaceAll('¤', '');
        value = value.replaceAll('*', '');
        value = value.replaceAll('µ', '');
        value = value.replaceAll('¨', '');
        value = value.replaceAll('ù', '');
        value = value.replaceAll('§', '');
        value = value.replaceAll('!', '');
        value = value.replaceAll(';', '');
        value = value.replaceAll(',', '');
        value = value.replaceAll('?', '');
        value = value.replaceAll('.', '');
        value = value.replaceAll('=', '');
        value = value.replaceAll('+', '');
        value = value.replaceAll('°', '');
        value = value.replaceAll('à', 'a');
        value = value.replaceAll('ä', 'a');
        value = value.replaceAll('ö', 'o');
        value = value.replaceAll('ô', 'o');
        value = value.replaceAll('ü', 'u');
        value = value.replaceAll('û', 'u');
        value = value.replaceAll('ë', 'e');
        value = value.replaceAll('ç', 'c');
        value = value.replaceAll("\ ", '');
        value = value.replaceAll("/", '');
        value = value.replaceAll('`', '');
        value = value.replaceAll(':', '');
        value = value.replaceAll('|', '');
        value = value.replaceAll('&', '');
        value = value.replaceAll('²', '');
        $("#name_raw").val(value);
        value = "";
    });

    //Cette fonction, particulièrement qualitative avouons-le, permet de déplacer un lien dans le footer ET dans le header 
    $(document).on('click', '.arraw-moove', function(){
      var parent_list_link = $(this).parent().parent().parent();
      
      var total = parent_list_link.attr('data-nb');

      var dir = $(this).attr('data-dir');
      var id = $(this).attr('data-id');
      var pos = $(this).parent().parent().attr('data-pos');
      //console.log("total = "+ + total + " dir= " + dir  +" id= " + id + " pos = " + pos);
      var out_html = "";

      var items = "";
      items = parent_list_link.children();

      //Détermination de la nouvelle position
      if (dir == "left"){
        var new_pos = parseInt(pos, 10) - 1;
      }else {
        var new_pos = parseInt(pos, 10) + 1;
      }

      if (parseInt(new_pos) >= 0 && parseInt(new_pos) <= total){
        var old = items[parseInt(new_pos)];
        items[parseInt(new_pos)] = items[parseInt(pos)];
        items[parseInt(pos)] = old;

        for (var i = 0; i < items.length; i++){
          out_html = out_html + items[i].outerHTML;
        }

        parent_list_link.html(out_html);

        var link = parent_list_link.attr('data-link');

        new_list = parent_list_link.children();
        for (var i = 0; i < new_list.length; i++){
          if (new_list[i].className != "no_pages" && new_list[i].dataset.id != undefined){
            $.ajax({
              url : link + new_list[i].dataset.id,
              type : 'POST',
              data : {pos: i, type: parent_list_link.attr('data-type')},
              dataType : 'html',
              success: function (data_rep) {
                if (data_rep != "Success"){
                  alert("Erreur, Code 112, Merci de contacter les administrateurs du site (Impossible de sauvegarder le nouvel ordre).");
                }
              },
              error: function() {
                alert("Erreur, Code 111, Merci de contacter les administrateurs du site (Impossible de sauvegarder le nouvel ordre).");
              }
            });
            new_list[i].dataset.pos = i;
          }
          
        }

      }
    });

    $(".arraw-moove-back").click(function(e){
      e.preventDefault();
      var link = $(this).attr('data-link');
      var redirect_link = $(this).attr('data-redirect-link');
      console.log(link);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
                document.location.href=redirect_link;
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    //Fonction fonctionnant pour le header et le footer
    $(".delete_link").click(function(){
      var link = $(this).attr('data-link');
      console.log(link);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
                document.location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
    
    // --------------Header
    $(".newmd").click(function(){
      var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
                document.location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".delete_md").click(function(){
      var link = $(this).attr('data-link');
      var id = $(this).attr('data-id');
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              $("#md_" + id).hide();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".rename_md").click(function(){
      var id = $(this).attr('data-id');
      var url = $(this).attr('data-link');
      var originalval = $("#name_md_"+id).html();
      $(this).attr('disabled', true);

      $("#name_md_"+id).html('<input type="text" class="form-control" id="renamer" data-orignialval="' + $("#name_md_"+id).html() + '" data-link="' + url + '" data-id="' + id + '" value="' + $("#name_md_"+id).html() + '"/>');
      
      $("#renamer").focusout(function(){
        if (originalval == $(this).val()){
          var id = $(this).attr('data-id');
          $("#name_md_"+id).html($(this).val());
          $('.rename_md[data-id="'+id + '"]').attr('disabled', false);
        }
      });
  
      $("#renamer").change(function(){
        var value = $(this).val();
        var originalval = $(this).attr('data-originalval');
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-link');
        if (value != " " && value != ""){
          $.ajax({
            url : url,
            type : 'POST',
            data : {val: value},
            dataType : 'html',
            success: function (data_rep) {
              if (data_rep != "Success"){
                alert("Erreur code 112 : Impossible de renommer l'élement demandé. Contacter un administrateur.");
                console.log(data_rep);
              }else {    
                $("#name_md_"+id).html(value);
                $('.rename_md[data-id="'+id + '"]').attr('disabled', false);
              }
            },
            error: function() {
              alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
            }
          });
        }else {
          $("#name_md_"+id).html(originalval);
          $('.rename_md[data-id="'+id + '"]').attr('disabled', false);
        }
      });
    });

    $(".hlink").click(function(){
      $(this).hide();
      $("#links_available").show();

      $("#links_available").change(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'POST',
          data : {titre: $('#links_available option:selected').val(), link: $('#links_available option:selected').attr('data-val')},
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              alert("Erreur code 112 : Impossible d'ajouter l'élement demandé. Contacter le support.");
              console.log(data_rep);
            }else {    
              document.location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
        
      })
    });

});