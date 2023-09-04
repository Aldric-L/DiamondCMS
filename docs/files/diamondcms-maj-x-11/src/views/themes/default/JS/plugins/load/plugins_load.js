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
//wow.js
if (typeof(WOW) !== "undefined"){
  new WOW().init();
}
//END wow.js