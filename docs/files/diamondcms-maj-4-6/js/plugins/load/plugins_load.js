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
tinymce.init({
  selector: 'textarea',
  theme: 'modern',
  height: 300,
  menubar: '',
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'media table contextmenu paste code'
  ],
  toolbar: [
    'undo redo | bold italic underline Strikethrough | link image | alignleft aligncenter alignright alignjustify | bullist numlist code'
  ],
  //bbcode_dialect: "punbb"
});
//END tinymce.js
//wow.js
new WOW().init();
//END wow.js