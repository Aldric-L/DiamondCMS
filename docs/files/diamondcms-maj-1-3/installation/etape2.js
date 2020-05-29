$(".testbdd").click(function(){
  var link = $(this).attr('data-link');
  $.ajax({
    url : link,
    type : 'POST',
    data : { db: $('#db').val(), host: $('#host').val(), usr: $('#usr').val(), psw: $('#psw').val(), port: $('#port').val()},
    dataType : 'html',
    success: function (data_rep) {
      if (data_rep != "" && data_rep != "notable"){
        alert("Erreur, impossible de se connecter à la BDD. Voici l'erreur levée : " + data_rep);
      }else if (data_rep == "notable") {
        alert('Informations valides, vous pouvez continuer. Attention, la base de données n\'existe pas. DiamondCMS va tenter de la créer.');
        $('#installbdd').attr('disabled', false);
      }else {
          alert('Informations valides, vous pouvez continuer.');
          $('#installbdd').attr('disabled', false);
      }
    },
    error: function() {
      alert("Erreur, Code 111.");
    }
  });
});

$(".installbdd").click(function(){
  var link = $(this).attr('data-link');
  $(this).html("Chargement...");
  $.ajax({
    url : link,
    type : 'POST',
    data : { db: $('#db').val(), host: $('#host').val(), usr: $('#usr').val(), psw: $('#psw').val(), port: $('#port').val(), type: "install"},
    dataType : 'html',
    success: function (data_rep) {
      if (data_rep != "Success"){
        console.log(data_rep);
        alert("Erreur, impossible d'installer la BDD. Voici l'erreur levée : " + data_rep);
        $(".installbdd").html("Erreur !");
      }else {
          alert('Installation terminée, vous pouvez continuer.');
          $(".installbdd").html("Installation de la BDD terminée !");
          $('#next_button-2').attr('disabled', false);
      }
    },
    error: function() {
      alert("Erreur, Code 111.");
    }
  });
});

$("#next_button-2").click(function(){
    var final_link = $(this).attr('data');
    $.ajax({
      url : $(this).attr('data-link'),
      type : 'POST',
      data : { type: "def", db: $('#db').val(), host: $('#host').val(), usr: $('#usr').val(), psw: $('#psw').val(), port: $('#port').val()},
      dataType : 'html',
      success: function (data_rep) {
        if (data_rep != "Success"){
          console.log(data_rep);
          alert("Erreur, Code 112, Réintallez le CMS");
        }else {    
            $.ajax({
                url : final_link,
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