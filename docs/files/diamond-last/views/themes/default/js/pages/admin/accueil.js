//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
function getRandomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}

jQuery(function ($){

  //Selon la loi des grands nombres, on peut affirmer que le CMS testera une fois toutes les 10 connexions s'il est à jour.
  //Cette fonction a été rajoutée afin d'améliorer l'empreinte carbone de DiamondCMS en minimisant les requettes inutiles
  var random_for_maj = getRandomInt(5);
  if (random_for_maj == 2){
    $.ajax({
      url : $('#maj').attr("data-link"),
      type : 'GET',
      dataType : 'html',
      success: function (data_rep) {
        r = JSON.parse(data_rep);
        if (r['State'] === 1)
          $('#maj').html(r['Return']);
        else 
          $('#maj').html("Impossible de vérifier la mise à jour.");
      }
    });
  }
  var itemsProcessed = 0;

  function callback(){
    $(".addon-iframe").children(".ajax-simpleSend").click(function(e){
        processSimpleSend(e, $(this));
        e.stopPropagation();
    });
  }

  Object.entries($(".addon-iframe")).forEach(([key, value]) => {
    var src, id;
    if (typeof(value) == "object" && value.className == "addon-iframe"){
      src = value.dataset.src;
      id = value.id;
      $("#" + id).load(value.dataset.src, function( response, status, xhr ) {
        if ( status == "error" ) {
          DiamondThrowError(138)
          $("#" + id).html($("#iframe-error").html());
          $("#" + id).show();
          $("#" + id).find(".addon-error-name").html(value.dataset.name);
        }
      });
    }
    itemsProcessed++;
    if(itemsProcessed === $(".addon-iframe").length) {
      callback();
    }
  })

  //Selon la loi des grands nombres, on peut affirmer que le CMS testera une fois toutes les 5 connexions si des messages sont à afficher.
  //Cette fonction a été rajoutée afin d'améliorer l'empreinte carbone de DiamondCMS en minimisant les requettes inutiles
  var random_for_maj = getRandomInt(10);
  if (random_for_maj == 2){
    $.ajax({
      url : "https://aldric-l.github.io/DiamondCMS/broadcast.json",
      type : 'GET',
      dataType : 'html',
      success: function (data_rep) {
        var r  = JSON.parse(data_rep);
        var div = "";
        if (r.all != null){
          div = '<div class="alert alert-' + r.all.type + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + r.all.msg + '</div>';
        }
        if (r[$('#broadcaster').attr('data-version')] != null){
          div = div + '<div class="alert alert-' + r[$('#broadcaster').attr('data-version')].type + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + r[$('#broadcaster').attr('data-version')].msg + '</div>';
        }
        $('#broadcaster').html(div);
      }
    });
  }

  
});