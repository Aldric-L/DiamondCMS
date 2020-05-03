//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){

  $(".saveconf").click(function(){
      var link = $(this).attr('data-link');
      $.ajax({
        url : link,
        type : 'POST',
        data: {money_sym: $('#money_sym').val(), money_name: $('#money_name').val(), money: $('#money').val()},
        dataType : 'html',
        success: function (data_rep) {
          if (data_rep != "Success"){
            console.log(data_rep);
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {    
            location.reload();
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
  });

    $(".enable").click(function(){
        $.ajax({
          url : $(this).attr('data'),
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              location.reload();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });

    $(".delete_cat").click(function(){
      var id = $(this).attr('id');
      var link = $(this).attr('data');
      console.log(link);
        $.ajax({
          url : link,
          type : 'GET',
          dataType : 'html',
          success: function (data_rep) {
            if (data_rep != "Success"){
              console.log(id);
              console.log(data_rep);
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
            }else {    
              $('#line_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
});