//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
    $(".delete_article").click(function(){
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

    $('.open_modal').click(function(){
      console.log('#modal_article_' + $(this).attr('id'));
      $('#modal_article_' + $(this).attr('id')).modal('show');
    });

    $('.close_modal').click(function(){
      $('#modal_article_' + $(this).attr('id')).modal('hide');
    });

    $(".add-auto-task").click(function(e){
      e.preventDefault();
      var base_auto = $("#new-task-auto");
      var nb_tasks = parseInt($("#nb_auto_tasks").val());
      base_auto.children()[1].lastElementChild.name = base_auto.children()[1].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";      
      base_auto.children()[2].firstElementChild.name = base_auto.children()[2].firstElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      base_auto.children()[3].lastElementChild.lastElementChild.name = base_auto.children()[3].lastElementChild.lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      
      base_auto[0].id = "new_auto_" + + nb_tasks.toString();
      $("#tasks").append(base_auto.clone());
      base_auto[0].id = "new-task-auto";
      base_auto.children()[1].lastElementChild.name = "";      
      base_auto.children()[2].firstElementChild.name = "";
      base_auto.children()[3].lastElementChild.lastElementChild.name = "";
      
      $("#nb_auto_tasks").val(nb_tasks+1)
      $("#tasks").children().show();
    });

    $(".add-manual-task").click(function(e){
      e.preventDefault();
      var base_auto = $("#new-task-man");
      var nb_tasks = parseInt($("#nb_man_tasks").val());
      base_auto.children()[1].lastElementChild.lastElementChild.name = base_auto.children()[1].lastElementChild.lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      base_auto[0].id = "new_man_" + nb_tasks.toString();
      $("#tasks").append(base_auto.clone());
      base_auto[0].id = "new-task-man";
      base_auto.children()[1].lastElementChild.lastElementChild.name = "";
      $("#nb_man_tasks").val(nb_tasks+1);
      $("#tasks").children().show();
    });

    $(".del-saved-task").click(function(e){
      e.preventDefault();
      var link = $(this).attr('data-link');
      var id = $(this).attr('data-id');
      
      var id_article = $(this).attr('data-idarticle');
      var nb_saved_tasks = parseInt($('h4#saved-tasks-'+id_article).attr("data-nb"));
      $.ajax({
        url : link,
        type : 'GET',
        dataType : 'html',
        success: function (data_rep) {
          if (data_rep != "Success"){
            console.log(data_rep);
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {    
            if (nb_saved_tasks > 1){
              $('#task-' + id).hide();
            }else {
              $('#task-' + id).html("<p><em>Aucune tâche n'est enregistrée pour le moment.</em></p>");
            }
            $('h4#saved-tasks-'+id_article).attr("data-nb", nb_saved_tasks-1);
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
    });

    $(".add-auto-task-modif").click(function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      var base_auto = $("#new-task-auto");
      var nb_tasks = parseInt($("#nb_auto_tasks-modif-" + id).val());
      base_auto.children()[1].lastElementChild.name = base_auto.children()[1].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";      
      base_auto.children()[2].firstElementChild.name = base_auto.children()[2].firstElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      base_auto.children()[3].lastElementChild.lastElementChild.name = base_auto.children()[3].lastElementChild.lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      
      base_auto[0].id = "new_auto_" + + nb_tasks.toString();
      $("#tasks-modif-" + id).append(base_auto.clone());
      base_auto[0].id = "new-task-auto";
      base_auto.children()[1].lastElementChild.name = "";      
      base_auto.children()[2].firstElementChild.name = "";
      base_auto.children()[3].lastElementChild.lastElementChild.name = "";
      
      $("#nb_auto_tasks-modif-" + id).val(nb_tasks+1)
      $("#tasks-modif-" + id).children().show();
    });

    $(".add-manual-task-modif").click(function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      var base_auto = $("#new-task-man");
      var nb_tasks = parseInt($("#nb_man_tasks-modif-"  +id).val());
      base_auto.children()[1].lastElementChild.lastElementChild.name = base_auto.children()[1].lastElementChild.lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      base_auto[0].id = "new_man_" + nb_tasks.toString();
      $("#tasks-modif-" + id).append(base_auto.clone());
      base_auto[0].id = "new-task-man";
      base_auto.children()[1].lastElementChild.lastElementChild.name = "";
      $("#nb_man_tasks-modif-"+ id).val(nb_tasks+1);
      $("#tasks-modif-" + id).children().show();
    });

    $(".save_article_modifs").click(function(){
      var id = $(this).attr('id');
      var link = $(this).attr("data");
      var data = $("#mod-form-" + id).serializeArray();
      console.log($("#mod-form-" + id).serializeArray());
      $.ajax({
        url : link,
        type : 'POST',
        data : data,
        dataType : 'html',
        success: function (data_rep) {
            console.log(data_rep);
          if (data_rep != "Success"){
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {
              $("#modal_article_"+id).modal('hide');
          }
        },
        error: function() {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
      });
    });
});