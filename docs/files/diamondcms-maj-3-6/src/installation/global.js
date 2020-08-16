$("#next_button").click(function(){
    $.ajax({
      url : $(this).attr('data'),
      type : 'GET',
      dataType : 'html',
      success: function (data_rep) {
        if (data_rep != "Success"){
          console.log(data_rep);
          alert("Erreur, Code 112, Réintallez le CMS. (Erreur levée : " + data_rep + ")");
        }else {    
          document.location.href = document.location.href;
        }
      },
      error: function() {
        alert("Erreur, Code 111, Réintallez le CMS");
      }
    });
});