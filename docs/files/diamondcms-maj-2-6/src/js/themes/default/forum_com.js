var nb_element_de_class;
  nb_element_de_class=$(".table-forum-com").length;
  for (var i = 1;nb_element_de_class>=i;i++){
    if (i >= 11){
      $("." + i).hide();
    }
  }
  var hide = true;
  $(".hider").click(function(){
    if (hide){
      for (var i = 1;nb_element_de_class>=i;i++){
        if (i >= 11){
          $("." + i).show();
        }
      }
      hide = false;
      $(this).html("Cacher les dernières réponses");
    }else {
      for (var i = 1;nb_element_de_class>=i;i++){
        if (i >= 11){
          $("." + i).hide();
        }
      }
      hide = true;
      $(this).html("Voir les réponses cachées");
    }
  });

tinymce.init({
    selector: ".content-com-span",
    inline: true,
    menubar: false,
    setup : function (editor) {
      editor.on('blur', function(e){
        console.log("SAVED !");
        console.log(editor.getContent(), editor.getBody());
        var id_com = editor.getBody().dataset.id;
        var link = editor.getBody().dataset.link;
        var content = editor.getContent();
        console.log(id_com, link);
        $.ajax({
            url : link,
            type : 'POST',
            data : {content: content},
            dataType : 'html',
            success: function (data_rep) {
              if (data_rep != "Success"){
                console.log(id_com);
                console.log(data_rep);
                alert("Erreur, Code 112, Merci de contacter le support. Réponse du serveur: " + data_rep);
              }else {    
                //On ne fait rien, TinyMCE a affiché la modif, nous on se contente de l'enregistrer.
                //location.reload();
              }
            },
            error: function() {
              alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
            }
        });
      });
    }
});

tinymce.init({
    selector: ".content-post-span",
    inline: true,
    menubar: false,
    setup : function (editor) {
      editor.on('blur', function(e){
        console.log("SAVED !");
        console.log(editor.getContent(), editor.getBody());
        var id_com = editor.getBody().dataset.id;
        var link = editor.getBody().dataset.link;
        var content = editor.getContent();
        console.log(id_com, link);
        $.ajax({
            url : link,
            type : 'POST',
            data : {content: content},
            dataType : 'html',
            success: function (data_rep) {
              if (data_rep != "Success"){
                console.log(id_com);
                console.log(data_rep);
                alert("Erreur, Code 112, Merci de contacter le support. Réponse du serveur: " + data_rep);
              }else {    
                //On ne fait rien, TinyMCE a affiché la modif, nous on se contente de l'enregistrer.
                //location.reload();
              }
            },
            error: function() {
              alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
            }
        });
      });
    }
});

$(".moove").click(function(e){
    e.preventDefault();
    var parent = $(this).parent();
    var id = $(this).attr("data-id");
    var link = $(this).attr("data-link");
    $(this).hide();
    $(".br_hidden").show();
    $("#cats_available").show();
    
    $("#cats_available").change(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : link,
          type : 'POST',
          data : {scat: $('#cats_available option:selected').val()},
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              alert("Erreur code 112 : Impossible de déplacer l'élement demandé. Contacter le support. Réponse du serveur: " + data_rep);
              console.log(data_rep);
            }else {    
                document.location.href = document.location.href;
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
        
    })
});