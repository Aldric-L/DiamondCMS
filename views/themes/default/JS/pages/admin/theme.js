function success_save(r){ $("#save_beforeleaving").css("display", "none"); }

jQuery(function ($){
  $("#custom_button").click(function(){
      $("#custom-depend").show();
      if ($("#save_beforeleaving").css("display") === "none"){
          $("#save_beforeleaving").css("display", "block");
      }
  });

  $(".color-picker").on("change", (e) => {
      if ($("#save_beforeleaving").css("display") === "none"){
          $("#save_beforeleaving").css("display", "block");
      }
      document.getElementById("useriframer").contentDocument.documentElement.style.setProperty("--" + e.target.name, e.target.value);
      document.getElementById("adminiframer").contentDocument.documentElement.style.setProperty("--" + e.target.name, e.target.value);
  });
});