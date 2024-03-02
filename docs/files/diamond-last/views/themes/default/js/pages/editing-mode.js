tinymce.init({
    selector: ".content-editable",
    inline: true,
    plugins: [
        'link', 'lists', 'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'media table contextmenu paste code'
      ],
      toolbar: [
        'undo redo | bold italic underline Strikethrough | link image | styleselect fontsizeselect forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent code'
      ],
    menubar: false,
    setup : function (editor) {
      editor.on('blur', function(e){
        console.log("SAVED !");
        console.log(editor.getContent(), editor.getBody());
        var apilink = editor.getBody().dataset.apilink;
        var page = editor.getBody().dataset.page;
        var content = editor.getContent();
        $.ajax({
            url : apilink + "editing/set/editPage/",
            type : 'POST',
            data : {content: content, name: page},
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