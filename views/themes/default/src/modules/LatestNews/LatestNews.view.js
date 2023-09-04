$(document).ready(function(e) {
    var lien_base = $("#LatestNews").attr("data-baselink");
    $.ajax({
      url: $("#LatestNews").attr("data-link"),
      type : 'GET',
      dataType : 'html',
    }).done(function( res ) {
      try {
        res = JSON.parse(res);
        if (typeof(res["Return"]) != "undefined" && typeof(res["State"]) != "undefined" && res["State"] == 1)
          json_result = res["Return"];
        else
          return;
      }catch (e){
        alert('Something goes wrong... Call DiamondCMS\' support.');
        return;
      }
  
      var torender = "";
      
    if (json_result.length == 0)
        torender += '<p style="text-align: center;">Aucune news n\'est à afficher.</p>';
    for(let i in json_result){
        torender += '<div class="col-sm-4">';
        if (json_result[i]['final_img_link'].substring(0, 4) == "http")
          torender += '<p class="text-center"><center><img style="width: 400px" class="img-rounded img-responsive" src="' + json_result[i]['final_img_link'] + '" alt="' + json_result[i]['name'] + '"></center></p>';
        else
          torender += '<p class="text-center"><center><img style="width: 400px" class="img-rounded img-responsive" src="' + lien_base + json_result[i]['final_img_link'] + '" alt="' + json_result[i]['name'] + '"></center></p>';
        torender += '<h2 class="text-center">' + json_result[i]['name'] +'</h2>';
        torender += '<p class="text-center bold">Le ' + json_result[i]['date'] + ' par ' + json_result[i]['user'] + '</p>';
        torender += '<p class="text-center bree-serif"><a href="' + lien_base +  'news/' + json_result[i]['id'] + '"><button type="button" class="btn btn-custom">En savoir plus...</button></a></p>';
        torender += '</div>';
    }        
    $("#LatestNewsRenderer").html(torender);
    $("#loaderLN").hide();
    })
  });