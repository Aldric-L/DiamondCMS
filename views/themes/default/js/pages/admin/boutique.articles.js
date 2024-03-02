//On utilise cette ligne pour verifier qu'aucun autre code n'ai changé la valeur de $
jQuery(function ($){
  const elemInForm = [".new-task-serverSelect", ".new-task-mustbeconnectedCheckbox", ".new-task-cmdImput", ".new-task-manImput"];
    /*$(".delete_article").click(function(){
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
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
            }else {    
              $('#line_' + id).remove();
            }
          },
          error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }
        });
    });
**/

$(".add-task").click(function(e){
  e.preventDefault();
  var is_manual = false;
  var is_mod = false;
  var base, nb_tasks, finalID, oldID;

  if ($(this).attr("data-ismod") == "true"){
    var id_mod = $(this).attr('data-id_mod');
    is_mod = true;
  }

  if ($(this).attr("data-ismanual") == "true"){
    is_manual = true;
    base = $("#new-task-man");
    if (is_mod){
      nb_tasks = parseInt($("#nb_man_tasks-modif-" + id_mod).val());
      $("#nb_man_tasks-modif-" + id_mod).val(nb_tasks+1)
    }else {
      nb_tasks = parseInt($("#nb_man_tasks").val());
      $("#nb_man_tasks").val(nb_tasks+1)
    }
    finalID = "new_man_" + nb_tasks.toString();
    oldID = "new-task-man";
  }else {
    base = $("#new-task-auto");
    if (is_mod){
      nb_tasks = parseInt($("#nb_auto_tasks-modif-" + id_mod).val());
      $("#nb_auto_tasks-modif-" + id_mod).val(nb_tasks+1)
    }else {
      nb_tasks = parseInt($("#nb_auto_tasks").val());
      $("#nb_auto_tasks").val(nb_tasks+1)
    }
    finalID = "new_auto_" + nb_tasks.toString();
    oldID = "new-task-auto";
  }

  if (base.find(".arrow-delete")['length'] >= 1){
    base.find(".arrow-delete")[0].dataset.id_task = (nb_tasks).toString();
    base.find(".arrow-delete")[0].dataset.is_manual = is_manual;
    base.find(".arrow-delete")[0].dataset.modif = is_mod;
    if (is_mod)
      base.find(".arrow-delete")[0].dataset.id_article = id_mod;

  }

  elemInForm.forEach(element => {
    if (base.find(element)['length'] >= 1)
      base.find(element)[0].name = base.find(element)[0].dataset.originalname + "[" + (nb_tasks).toString() + "]";
  });

  base[0].id = finalID;
  base[0].dataset.id_task = (nb_tasks).toString();

  if (is_mod){
    $("#tasks-modif-" + id_mod).append(base.clone());
    $("#tasks-modif-" + id_mod).children().show();
  }else{
    $("#tasks").append(base.clone());
    $("#tasks").children().show();
  }

  elemInForm.forEach(element => {
    if (base.find(element)['length'] >= 1)
      base.find(element)[0].dataset.name = "";
  });
  base[0].id = oldID;
  base[0].dataset.id_task = "";
  base[0].dataset.is_manual = is_manual;

  $(".arrow-delete").unbind('click').bind('click', function(e){
    e.preventDefault();
    var is_manual = ($(this).attr("data-is_manual") == "true") ? true : false;
    var is_mod = ($(this).attr("data-modif") == "true") ? true : false;
    var id_task = parseInt($(this).attr("data-id_task"));
  
    if (is_mod){
      var id_article = $(this).attr("data-id_article");
      if (is_manual){
        $("#nb_man_tasks-modif-" + id_article).val(parseInt($("#nb_man_tasks").val())-1)
        var target_task = $("#tasks-modif-" + id_article).find(".new-task-man#new_man_"+id_task);
        target_task.remove();
        var tasklist = $("#tasks-modif-" + id_article).find(".new-task-man");
      }else{
        $("#nb_auto_tasks-modif-" + id_article).val(parseInt($("#nb_auto_tasks").val())-1)
        var target_task = $("#tasks-modif-" + id_article).find(".new-task-auto#new_auto_"+id_task);
        target_task.remove();
        var tasklist = $("#tasks-modif-" + id_article).find(".new-task-auto");
      }
    }else {
      if (is_manual){
        $("#nb_man_tasks").val(parseInt($("#nb_man_tasks").val())-1)
        var target_task = $("#tasks").find(".new-task-man#new_man_"+id_task);
        target_task.remove();
        var tasklist = $("#tasks").find(".new-task-man");
      }else{
        $("#nb_auto_tasks").val(parseInt($("#nb_auto_tasks").val())-1)
        var target_task = $("#tasks").find(".new-task-auto#new_auto_"+id_task);
        target_task.remove();
        var tasklist = $("#tasks").find(".new-task-auto");
      }
    }

    tasklist.each(function (i){
      idt = parseInt($(this).attr("data-id_task"));
      if (idt >= id_task){
        if ($(this).find(".arrow-delete")['length'] >= 1)
          $(this).find(".arrow-delete")[0].dataset.id_task = (idt-1).toString();
      
        elemInForm.forEach(element => {
          if ($(this).find(element)['length'] >= 1)
            $(this).find(element)[0].name = $(this).find(element)[0].dataset.originalname + "[" + (idt-1).toString() + "]";
        });
        if ($(this).attr("data-is_manual") == "true")
          $(this).attr('id', "new_man_" + (idt-1).toString());
        else 
          $(this).attr('id', "new_auto_" + (idt-1).toString());
          
        $(this).attr('data-id_task', (idt-1).toString());
      }
    });
  
  });
})




    $(".add-auto-task").click(function(e){
      e.preventDefault();
      var base_auto = $("#new-task-auto");
      var nb_tasks = parseInt($("#nb_auto_tasks").val());
      if (base_auto.find(".auto-delete")['length'] >= 1){
        base_auto.find(".auto-delete")[0].dataset.id =  (nb_tasks).toString();
      }
      base_auto.children()[1].lastElementChild.name = base_auto.children()[1].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";      
      base_auto.children()[2].firstElementChild.name = base_auto.children()[2].firstElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      base_auto.children()[3].lastElementChild.name = base_auto.children()[3].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      
      base_auto[0].id = "new_auto_" + + nb_tasks.toString();
      $("#tasks").append(base_auto.clone());
      base_auto[0].id = "new-task-auto";
      base_auto.children()[1].lastElementChild.name = "";      
      base_auto.children()[2].firstElementChild.name = "";
      base_auto.children()[3].lastElementChild.name = "";
      if (base_auto.find(".auto-delete")['length'] >= 1){
        base_auto.find(".auto-delete")[0].dataset.id =  "";
      }
      
      $("#nb_auto_tasks").val(nb_tasks+1)
      $("#tasks").children().show();
    });

    $(".add-manual-task").click(function(e){
      e.preventDefault();
      var base_auto = $("#new-task-man"); 
      var nb_tasks = parseInt($("#nb_man_tasks").val());
      if (base_auto.find(".man-delete")['length'] >= 1){
        base_auto.find(".man-delete")[0].dataset.id =  (nb_tasks).toString();
      }
      base_auto.children()[1].lastElementChild.name = base_auto.children()[1].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      base_auto[0].id = "new_man_" + nb_tasks.toString();
      $("#tasks").append(base_auto.clone());
      base_auto[0].id = "new-task-man";
      base_auto.children()[1].lastElementChild.name = "";
      $("#nb_man_tasks").val(nb_tasks+1);
      if (base_auto.find(".man-delete")['length'] >= 1){
        base_auto.find(".man-delete")[0].dataset.id = "";
      }
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
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
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
      base_auto.children()[3].lastElementChild.name = base_auto.children()[3].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      if (base_auto.find(".auto-delete")['length'] >= 1){
        base_auto.find(".auto-delete")[0].dataset.id =  (nb_tasks).toString();
        base_auto.find(".auto-delete")[0].dataset.modif =  true;
      }
      
      base_auto[0].id = "new_auto_" + + nb_tasks.toString();
      $("#tasks-modif-" + id).append(base_auto.clone());
      base_auto[0].id = "new-task-auto";
      base_auto.children()[1].lastElementChild.name = "";      
      base_auto.children()[2].firstElementChild.name = "";
      base_auto.children()[3].lastElementChild.name = "";
      if (base_auto.find(".auto-delete")['length'] >= 1){
        base_auto.find(".auto-delete")[0].dataset.id = "";
      }
      
      $("#nb_auto_tasks-modif-" + id).val(nb_tasks+1)
      $("#tasks-modif-" + id).children().show();
    });

    $(".add-manual-task-modif").click(function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      var base_auto = $("#new-task-man");
      var nb_tasks = parseInt($("#nb_man_tasks-modif-"  +id).val());
      base_auto.children()[1].lastElementChild.name = base_auto.children()[1].lastElementChild.dataset.originalname + "[" + (nb_tasks).toString() + "]";
      if (base_auto.find(".man-delete")['length'] >= 1){
        base_auto.find(".man-delete")[0].dataset.id =  (nb_tasks).toString();
        base_auto.find(".man-delete")[0].dataset.modif =  true;
      }
      base_auto[0].id = "new_man_" + nb_tasks.toString();
      $("#tasks-modif-" + id).append(base_auto.clone());
      base_auto[0].id = "new-task-man";
      base_auto.children()[1].lastElementChild.name = "";
      if (base_auto.find(".man-delete")['length'] >= 1){
        base_auto.find(".man-delete")[0].dataset.id = "";
      }
      $("#nb_man_tasks-modif-"+ id).val(nb_tasks+1);
      $("#tasks-modif-" + id).children().show();
    });

    $(".man-delete").click(function(e){
      if ($(this).attr("data-modif") == "true"){

      }else {
        console.log($("#tasks").children())
      }
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
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site. Réponse du serveur: " + data_rep);
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