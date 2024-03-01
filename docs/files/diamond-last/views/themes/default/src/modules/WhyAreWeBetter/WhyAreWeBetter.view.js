$(document).ready(function(e) {
    var lien_base = $("#WhyAreWeBetter").attr("data-baselink");

    
    $("#WhyAreWeBetterDirectEdit").html($("#wawb_render").html());
    $("#WhyAreWeBetterDirectEdit").children().children().removeClass("wow");
    $("#WhyAreWeBetterDirectEdit").children().children().css("visibility", "visible");

    $(".wawb_edit_field").on("input propertychange", (e)=> {
      var target = e.currentTarget;
      if (target.dataset.originalname != "icon"){
        $("#WhyAreWeBetterDirectEdit").children().find("#wawb_" + target.dataset.originalname + "_" + target.dataset.colid).html(target.value);
      }else {
        if (target.value.substr(0, 4) == "http"){
          $("#WhyAreWeBetterDirectEdit").children().find("#wawb_parent" + target.dataset.originalname + "_" + target.dataset.colid).html(
            '<img width="220px" src="' + target.value + '" id="#wawb_' + target.dataset.originalname + "_" + target.dataset.colid + '" alt="">'
          );
        }else {
          $("#WhyAreWeBetterDirectEdit").children().find("#wawb_parent" + target.dataset.originalname + "_" + target.dataset.colid).html(
            '<i class="fa-5x ' + target.value + '" id="#wawb_' + target.dataset.originalname + "_" + target.dataset.colid + '" aria-hidden="true"></i>'
          );
        }
      }
    });
    
  });