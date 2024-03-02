/*
  Fichier javascript inclu dans chaque page permetant d'inclure le code d'implémentation que certains plugins nécessitent
  Pour une meilleure lisibilité, le code chaque plugin doit etre entouré de commentaires indiquant à quel plugin correspond le code.
  Cette indication doit etre présentée de la manière suivante (sensible au caractère) :
    //nomduplugin.js
    code...
    //END nomduplugin.js
*/
//dic.js
$(document).ready(function(e) {
  if ($("#dic_launcher").length && typeof $("#dic_launcher").attr('data-wherearefiles') != "undefined" && typeof $("#dic_launcher").attr('data-whereisdic') != "undefined"){
      var height = "676";
      var width = "1200";
      var callback = null;
      var resetcallback = null;
      var imgdefault = null;
      var linkdefault = null;
      var imgformat = null;
      var en = [true, true, true];
      
      if (typeof $("#dic_launcher").attr('data-imgWidth') != "undefined" && $("#dic_launcher").attr('data-imgWidth') != "")
        width = $("#dic_launcher").attr('data-imgWidth');
      
      if (typeof $("#dic_launcher").attr('data-imgHeight') != "undefined" && $("#dic_launcher").attr('data-imgHeight') != "")
        height = $("#dic_launcher").attr('data-imgHeight');

      if (typeof $("#dic_launcher").attr('data-callback') != "undefined" && $("#dic_launcher").attr('data-callback') != "")
        callback = $("#dic_launcher").attr('data-callback');
      
      if (typeof $("#dic_launcher").attr('data-resetcallback') != "undefined" && $("#dic_launcher").attr('data-resetcallback') != "")
        resetcallback = $("#dic_launcher").attr('data-resetcallback');

      if (typeof $("#dic_launcher").attr('data-imgdefault') != "undefined" && $("#dic_launcher").attr('data-imgdefault') != "")
        imgdefault = $("#dic_launcher").attr('data-imgdefault');

      if (typeof $("#dic_launcher").attr('data-linkdefault') != "undefined" && $("#dic_launcher").attr('data-linkdefault') != "")
        linkdefault = $("#dic_launcher").attr('data-linkdefault');
      
      if (typeof $("#dic_launcher").attr('data-imgformat') != "undefined" && $("#dic_launcher").attr('data-imgformat') != "")
        imgformat = $("#dic_launcher").attr('data-imgformat');

      if (typeof $("#dic_launcher").attr('data-enPreChargedImgs') != "undefined" && $("#dic_launcher").attr('data-enPreChargedImgs') != "")
        en[0] = ($("#dic_launcher").attr('data-enPreChargedImgs') === "false") ? false : true;
      
      if (typeof $("#dic_launcher").attr('data-enNewImgLink') != "undefined" && $("#dic_launcher").attr('data-enNewImgLink') != "")
        en[2] = ($("#dic_launcher").attr('data-enNewImgLink') === "false") ? false : true;

      if (typeof $("#dic_launcher").attr('data-enUploadImg') != "undefined" && $("#dic_launcher").attr('data-enUploadImg') != "")
        en[1] = ($("#dic_launcher").attr('data-enUploadImg') === "false") ? false : true;

      $.ajax({
          url : $("#dic_launcher").attr('data-wherearefiles'),
          type : 'POST',
          data: "height=" + height + "&width=" + width,
          dataType : 'html',
          success: function (res) {
              try {
                  res = JSON.parse(res)
              }catch (e){
                  alert('Something goes wrong... Call DiamondCMS\' support (Unable to parse JSON from DIC image list.');
                  return;
              }
              DIC = new DiamondImgChooser({
                  en_1: en[0],
                  img_1 : res["Return"],
                  en_2: en[1],
                  en_3: en[2],
                  adapt_launcher: true,
                  where_is_dic: $("#dic_launcher").attr('data-whereisdic'),
                  callback : callback,
                  resetcallback : resetcallback,
                  preselected_img: imgdefault,
                  preselected_link: linkdefault,
                  imgformat: imgformat
              });
          },
          error: function() {
              alert('Something goes wrong... Call DiamondCMS\' support.');
          }
      });
  }
});
//END dic.js

//Chart JS
if (typeof(Chart) !== "undefined"){
  Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
}
