$("#next_button").click(function(){
    $.ajax({
      url : $(this).attr('data'),
      type : 'GET',
      dataType : 'html',
      success: function (data_rep) {
        if (data_rep != "Success"){
          console.log(data_rep);
          alert("Erreur, Code 112, Réintallez le CMS");
        }else {    
          location.reload();
        }
      },
      error: function() {
        alert("Erreur, Code 112, Réintallez le CMS");
      }
    });
});