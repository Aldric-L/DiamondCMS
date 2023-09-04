/*
  Fichier javascript inclus dans chacque page permetant d'inclure le code d'implémentations que certains plugins nécessites.
  Ce fichier est inclus au début de la page ! Il n'est pas destiner aux codes qui attendent un évenement (onclik, ...).
  Pour ceux là utiliser l'autre fichier "plugins_listener".
  Pour une meilleure lisibilité, le code chaque plugin doit etre entouré de commentaires indiquant à quel plugin correspond le code.
  Cette indication doit etre présentée de la manière suivante (sensible au caractère) :
    //nomduplugin.js
    code...
    //END nomduplugin.js
*/
//tinymce.js
if (typeof(tinymce) !== "undefined"){
  tinymce.init({
    selector: 'textarea',
    theme: 'silver',
    height: 300,
    menubar: '',
    plugins: [
      'advlist autolink lists link image charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'media table paste code emoticons'
    ],
    toolbar: [
      'undo redo | bold italic underline Strikethrough | alignleft aligncenter alignright alignfull | numlist bullist outdent indent code | link image | styleselect fontsizeselect forecolor backcolor | emoticons '
    ],
    setup: function(ed) {
      if ($('#'+ed.id).prop('readonly')) {
          ed.settings.readonly = true;
      }
    }
    //bbcode_dialect: "punbb"
  });
}
//END tinymce.js
