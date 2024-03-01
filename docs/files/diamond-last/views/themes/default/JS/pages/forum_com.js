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
      $(this).removeClass("btn-custom");
      $(this).addClass("btn-default");
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
    plugins: [
      'advlist autolink lists link image charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'media table contextmenu paste code emoticons'
    ],
    toolbar: [
      'undo redo | bold italic underline Strikethrough | alignleft aligncenter alignright alignfull | numlist bullist outdent indent code | link image | styleselect fontsizeselect forecolor backcolor | emoticons '
    ],
    setup : function (editor) {
      editor.on('blur', function(e){
        var apilink = editor.getBody().dataset.apilink;
        var id_com = editor.getBody().dataset.id;
        var content = editor.getContent();
        $.ajax({
            url : apilink + "forum/set/editComment/",
            type : 'POST',
            data : {content: content, id:id_com},
            dataType : 'html',
            success: function (data_rep) {
                successfunc(data_rep, apilink, false, false);
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
    plugins: [
      'advlist autolink lists link image charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'media table contextmenu paste code emoticons'
    ],
    toolbar: [
      'undo redo | bold italic underline Strikethrough | alignleft aligncenter alignright alignfull | numlist bullist outdent indent code | link image | styleselect fontsizeselect forecolor backcolor | emoticons '
    ],
    setup : function (editor) {
      editor.on('blur', function(e){
        /*console.log("SAVED !");
        console.log(editor.getContent(), editor.getBody());*/
        var id_com = editor.getBody().dataset.id;
        var link = editor.getBody().dataset.link;
        var apilink = editor.getBody().dataset.apilink;
        var content = editor.getContent();
        //console.log(id_com, link);
        $.ajax({
            url : apilink + "forum/set/editPost/",
            type : 'POST',
            data : {content: content, id:id_com},
            dataType : 'html',
            success: function (data_rep) {
              successfunc(data_rep, apilink, false, false);
              /*if (data_rep != "Success"){
                console.log(id_com);
                console.log(data_rep);
                alert("Erreur, Code 112, Merci de contacter le support. Réponse du serveur: " + data_rep);
              }else {    
                //On ne fait rien, TinyMCE a affiché la modif, nous on se contente de l'enregistrer.
                //location.reload();
              }*/
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
    var apilink = $(this).attr("data-apilink");
    $(this).hide();
    $(".br_hidden").show();
    $("#cats_available").show();
    
    $("#cats_available").change(function(){
        var link = $(this).attr('data-link');
        $.ajax({
          url : apilink + "forum/set/moovePost/",
          type : 'POST',
          data : {new_cat: $('#cats_available option:selected').val(), id:id},
          dataType : 'html',
          success: function (data_rep) {
            successfunc(data_rep, apilink, false, true);

          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
        
    })
});