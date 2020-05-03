$("#last_button").click(function(){
    var final_link = $(this).attr('data');
    $.ajax({
        url : $(this).attr('data-first'),
        type : 'POST',
        data: $("#conf").serializeArray(),
        success: function (data_rep) {
          if (data_rep != "Success"){
            console.log(data_rep);
            alert("Erreur, Code 112, Réintallez le CMS : " + data_rep);
          }else {    
            $.ajax({
                url : final_link,
                type : 'GET',
                dataType : 'html',
                success: function (data_rep) {
                  if (data_rep != "Success"){
                    console.log(data_rep);
                    alert("Erreur, Code 112, Réintallez le CMS :" + data_rep);
                  }else {    
                    location.reload();
                  }
                },
                error: function() {
                  alert("Erreur, Code 111, Réintallez le CMS");
                }
            });
          }
        },
        error: function() {
          alert("Erreur, Code 111, Réintallez le CMS");
        }
      });
});