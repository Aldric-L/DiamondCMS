<?php global $tickets, $ntickets; ?>

<div id="fh5co-page-title">
  <div class="overlay"></div>
  <div class="text">
    <h1>Support <?php if ($_SESSION['user']->getRoleLevel($controleur_def->bddConnexion()) >= 2){?>- Version <?php echo $_SESSION['user']->getRoleName($controleur_def->bddConnexion()); }?></h1>
  </div>
</div>
<div id="create_ticket" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title text-center">Créer un nouveau ticket</h4>
        </div>
        <div class="modal-body">
          <h2 class="text-center">Création d'un ticket :</h2>
          <div id="champs_newticket" style="display: none;">
                <h3 class="text-center text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Merci de remplir tous les champs ! <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h3><br />
          </div>
          <form method="post" id="newticket_form">
            <div class="form-group">
              <label for="title" class="col-form-label">Titre du ticket :</label>
              <input class="form-control" type="text" name="title" id="title">
              <small id="titleHelp" class="form-text text-muted">Il doit être claire et rapide pour résumer votre problème.</small>
            </div>
            <br />
            <div class="form-group">
              <label for="content" class="col-form-label">Contenu de votre demande :</label>
              <textarea class="form-control" rows="10" type="text-area" id="content" name="content"></textarea>
              <small id="contentHelp" class="form-text text-muted">Elle doit être détaillée, la plus soignée possible, et ne pourra être modifiée.</small>
            </div>
            <br />
            <h4 class="text-center text-danger">Attention, une fois votre ticket envoyé,<br/> il sera impossible à supprimer.</h4><br/>
            <button type="submit" class="btn btn-success align-right center-block acc">Créer le ticket</button>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">FERMER</button>
        </div>
      </div>
  </div>
</div>
<?php if ($_SESSION['user']->getRoleLevel($controleur_def->bddConnexion()) >= 2){?>
<div class="content-container container">
  <h1 class="text-center">Derniers tickets créés</h1>
  <br />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Pseudo</th>
        <th>Titre</th>
        <th>Date</th>
        <th>Status</th>
        <th>Réponses</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $key => $ticket) {
        echo "<tr>";
        echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $tickets[$key]['pseudo'] . '&s=26"> ' . $tickets[$key]['pseudo'] . "</td>";
        echo "<td>" . $tickets[$key]['titre_ticket'] . "</td>";
        echo "<td>" . $tickets[$key]['date_ticket'] . "</td>";
        echo "<td>" . $tickets[$key]['status_l'] . "</td>";
        echo '<td id="n_'. $ticket['id'] . '">' . sizeof($tickets[$key]['rep']) . "</td>";
        echo '<td class="td_view_del_'. $tickets[$key]['id'] . '"><a class="v_'. $tickets[$key]['id'] . ' bold" href="#"><i class="fa fa-eye" aria-hidden="true"></i> Voir </a>';
        if ($tickets[$key]['status'] != 2 ){
          echo '| <span class="d_' . $tickets[$key]['id']. '_text"><a class="d_'. $tickets[$key]['id'] . ' bold" style="color: red;" href="#"><i class="fa fa-times" aria-hidden="true"></i>  Fermer le ticket</a></span></td>';
        }else {
          echo '| <i class="fa fa-times" aria-hidden="true"></i>  Le ticket est déjà fermé</td>';
        }
        echo "</tr>";
     } //End Foreach?>
    </tbody>
  </table>
  <?php foreach ($tickets as $key => $ticket) {?>
  <div id="modal_ticket_<?php echo $tickets[$key]['id']; ?>" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">#<?php echo $tickets[$key]['id']; ?> - <?php echo $tickets[$key]['titre_ticket']; ?> par <?php echo $tickets[$key]['pseudo']; ?></h4>
              </div>
              <div class="modal-body">
                <h3><?php echo $tickets[$key]['titre_ticket'] . ' par ' . '<img width=28 height=28 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $tickets[$key]['pseudo'] . '&s=28"> ' . $tickets[$key]['pseudo'] . ' le '. $tickets[$key]['date_ticket']; ?></h3>
                <hr style="margin-left: 2.3%;width: 95%;margin-top: 5px;margin-bottom: 10px;">
                <div class="content_ticket">
                <p>
                  <?php echo $tickets[$key]['contenu_ticket'];?>
                </p>
                </div>
                <div class="reponses_bdd">
                <?php if (!empty($tickets[$key]['rep'])){ ?>
                  <hr><h4 class="text-center bold" style="font-size: 21px;">Réponses</h4><hr>
                  <?php foreach($tickets[$key]['rep'] as $rep){ ?>
                    <div id="r_c_<?php echo $rep['id'];?>">
                      <h4 style="margin-left: 2.3%;width: 92,5%;margin-top: 5px;margin-bottom: 10px;"><?php if ($rep['role'] != 0){?><span class="bold"><?php echo $controleur_def->getRoleNameById($controleur_def->bddConnexion(), $rep['role']);?> : </span><?php }?>
                      <?php echo $rep['pseudo'];?>, le <?php echo $rep['date_reponse'];?></h4>
                      <hr style="margin-left: 5%;width: 92,5%;margin-top: 5px;margin-bottom: 10px;">
                      <span style="margin-left: 5%;width: 92,5%;margin-top: 5px;"><?php echo $rep['contenu_reponse'];?></span><br/><br />
                      <?php 
                      if ($_SESSION['user']->getRoleLevel($controleur_def->bddConnexion()) >= $controleur_def->getRoleLevel($controleur_def->bddConnexion(), $rep['role'])){?> 
                        <p class="text-right bold" style="margin-left: 5%;width: 92,5%;margin-top: 5px;margin-bottom: 10px;"><em><a href="#" id="d_r_<?php echo $rep['id'];?>" class="bold">Supprimer cette réponse.</a></em></p>
                      <?php } ?>
                    </div>
                  <?php } //END foreach 2 (reps) ?>
                <?php } ?>
                </div>
                <div id="r_<?php echo $tickets[$key]['id']; ?>" class="response" <?php if ($tickets[$key]['status'] == 2){?>style="display: none;"<?php } ?>>
                  <hr style="margin-top: 20px">
                    <a href="#" class="bold rep_<?php echo $tickets[$key]['id']; ?>"><h4 class="text-center bold">Répondre... <i class="fa fa-arrow-down" aria-hidden="true"></i></h4></a>
                  <hr style="margin-bottom: 5px;">
                  <div class="center" id="rep_div_<?php echo $tickets[$key]['id']; ?>" style="display: none;">
                      <br />
                      <form method="post" action="" class="form_reponse">
                      <label for="form-control col-sm-2">Votre réponse :</label>
                      <textarea class="form-control t_r" cols="25" rows="6" name="content_post"></textarea><br />
                      <input type="hidden" class="hidden_id" value="<?php echo $tickets[$key]['id']; ?>"/>
                      <button type="submit" class="btn pull-right btn-danger sub acc">Valider</button>
                    </form>
                    <br /><br />
                  </div>
                </div>
                <div class="response_none" <?php if ($tickets[$key]['status'] != 2){?>style="display: none;"<?php } ?>>
                  <hr style="margin-top: 20px">
                    <h4 class="text-center bold"><em>Vous ne pouvez plus répondre car le ticket est fermé.</em></h4>
                  <hr style="margin-bottom: 5px;">
                </div>
              </div>
              <div class="modal-footer">
                <?php if ($tickets[$key]['status'] != 2 ){
                        echo '<button type="button" class="d_' . $tickets[$key]['id']. ' btn btn-danger btn-default" data-dismiss="modal"><span class="d_' . $tickets[$key]['id']. '_text">Fermer le sujet</span></button>';
                      }else {
                        echo '<button type="button" class="btn btn-default">Le ticket est déjà fermé</button>';
                      }?>
                  <button type="button" class="btn btn-default" data-dismiss="modal">FERMER</button>
              </div>
          </div>
      </div>
  </div>
  <script>
    //MODAL View ticket
    $(".v_<?php echo $tickets[$key]['id']; ?>").click(function(){
      $("#modal_ticket_<?php echo $tickets[$key]['id']; ?>").modal('show');
    });
    //END modal
    //REPONSE DANS MODAL TICKET
    $(".rep_<?php echo $tickets[$key]['id']; ?>").click(function(){
      if ($('#rep_div_<?php echo $tickets[$key]['id']; ?>').is(":hidden")){
        $('.rep_<?php echo $tickets[$key]['id']; ?>').html('<h4 class="text-center">Répondre... <i class="fa fa-arrow-up" aria-hidden="true"></i></h4>');
        $('#rep_div_<?php echo $tickets[$key]['id']; ?>').show();
      }else {
        $('.rep_<?php echo $tickets[$key]['id']; ?>').html('<h4 class="text-center">Répondre... <i class="fa fa-arrow-down" aria-hidden="true"></i></h4>');
        $('#rep_div_<?php echo $tickets[$key]['id']; ?>').hide();
      }
    });
    //END REPONSE
  </script>

  <?php 
  //Systeme de fermeture de tickets
    if ((isset($_SESSION['admin']) && $_SESSION['admin']) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $tickets[$key]['pseudo'])) {?>
          <script>
          $(".d_<?php echo $tickets[$key]['id']; ?>").click(function(){
              $.ajax({
                 url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/d_t/<?php echo $tickets[$key]['id']; ?>/',
                 type : 'GET',
                 success: function (data_rep) {
                    if (data_rep != "Success"){
                      alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                    }else {                   
                      $(".d_<?php echo $tickets[$key]['id'];?>_text").html('<i class="fa fa-times" aria-hidden="true"></i>  Le ticket est déjà fermé');
                      $("#r_<?php echo $tickets[$key]['id']; ?>").hide();
                    }
                 },
                 error: function() {
                    alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                 }
              });
          });
              </script>
    <?php }
    //Systeme de suppression des reponses par ticket
    foreach($tickets[$key]['rep'] as $rep){  
      if ((isset($_SESSION['admin']) && $_SESSION['admin']) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $rep['pseudo'])) {?>
            <script>
            $("#d_r_<?php echo $rep['id']; ?>").click(function(){
                $.ajax({
                  url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/d_r/<?php echo $rep['id']; ?>/',
                  type : 'GET',
                  success: function (data_rep) {
                      if (data_rep != "Success"){
                        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                      }else {
                        $('#r_c_<?php echo $rep['id'];?>').hide();
                        var old_v = $('#n_<?php echo $ticket['id'];?>').html();
                        $('#n_<?php echo $ticket['id'];?>').html(old_v-1);
                      }
                  },
                  error: function() {
                      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                  }
                });
            });
                </script>
      <?php } 
    } //End Foreach (reponses)
   } //End Foreach (modal)
   //Systeme de pages (max tickets par pages)
   if (isset($param[1]) && sizeof($ntickets)-($param[1]*10) >= 10){
    if (!isset($param[1])){?>
    <span class="text-right bold" style="float: right;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/1">Page suivante <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></span>
  <?php }else { ?>
    <span class="text-right bold" style="float: right;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/<?php echo $param[1]+1;?>">Page suivante <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></span>
  <?php }}
    if (isset($param[1]) && $param[1] == 1){?>
      <span class="text-left bold" style="float: left;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/"><i class="fa fa-angle-double-left" aria-hidden="true"></i>Page précedente</a></span>
    <?php }else if (isset($param[1]) && $param[1] >= 2){?>
      <span class="text-left bold" style="float: left;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/<?php echo $param[1]-1;?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i>Page précedente</a></span>
    <?php }
  //END Systeme de pages ?> 
</div>
<script>
//Ajout d'une réponse
$('.form_reponse').submit(function(e){
  e.preventDefault();
  tinyMCE.triggerSave(true, true);
  if($('textarea', this).val() != "" && $('textarea', this).val() != "<br>"){
    $.ajax({
          url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/a_r/' + $('input[class="hidden_id"]', this).val(),
          type : 'POST',
          data : 'pseudo=<?php echo $_SESSION['pseudo']; ?>&content=' + $('textarea', this).val(),
          dataType : 'html',
          success: function (data_rep){
            if (data_rep != "Success"){
              alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
              debugger;
            }else {
              location.reload(true);
            }
          },
          error: function (data_rep) {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
          }     
      });
  }
});
//END ADD         
</script>
<?php }else { ?>
<div id="explicsupport">
  <h2>Le serveur <?php echo $Serveur_Config['Serveur_name']; ?> met à votre disposition un support.</h2>
  <p class="explicp">Ce support vous permet de contacter gratuitement notre équipe. Les tickets ne sont lisibles que par vous et nous. 
    Nous vous répondons dans les délais les plus courts possibles. N'hésitez pas à verifier sur le forum si votre question n'a pas été déjà traitée.
  </p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  Attention, tout comportement inaproprié pourra entrainer des sonctions graves.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
</div>
<br />
<div class="content-container container">
  <div class="rows">
    <div class="col-lg-3">
    <br />
      <div class="actions">
        <div class="actions_top">
          <h4 class="text-center">Action(s) :</h4>
        </div>
        <!--<hr style="margin-left: 5%; width: 90%; margin-top: 5px;  margin-bottom: 5px;">-->
        <div class="actions_content">
            <h5 class="text-center"><button class="btn btn-success acc addTicket" style="border-color: #197d62;background-color: #197d62;">Créer un nouveau ticket</button></h5>
            <h5 class="text-center"><button class="btn btn-success acc" style="border-color: #197d62;background-color: #197d62;"><a class="no" style="text-decoration: none;color: white;" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>forum">Retourner sur le forum...</a></button></h5>
        </div>
      </div>
    </div>
    <div class="col-lg-9">
      <h3 class="text-center">Vos derniers tickets créés</h3>
      <br />
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Date</th>
            <th>Status</th>
            <th>Réponses</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tickets as $key => $ticket) {
            echo "<tr>";
            //echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $tickets[$key]['pseudo'] . '&s=26"> ' . $tickets[$key]['pseudo'] . "</td>";
            echo "<td>" . $tickets[$key]['titre_ticket'] . "</td>";
            echo "<td>" . $tickets[$key]['date_ticket'] . "</td>";
            echo "<td>" . $tickets[$key]['status_l'] . "</td>";
            echo '<td id="n_'. $ticket['id'] . '">' . sizeof($tickets[$key]['rep']) . "</td>";
            echo '<td class="td_view_del_'. $tickets[$key]['id'] . '"><a class="v_'. $tickets[$key]['id'] . ' bold" href="#"><i class="fa fa-eye" aria-hidden="true"></i> Voir </a>';
            if ($tickets[$key]['status'] != 2 ){
              echo '| <span class="d_' . $tickets[$key]['id']. '_text"><a class="d_'. $tickets[$key]['id'] . ' bold" style="color: red;" href="#"><i class="fa fa-times" aria-hidden="true"></i>  Fermer le ticket</a></span></td>';
            }else {
              echo '| <i class="fa fa-times" aria-hidden="true"></i>  Le ticket est déjà fermé</td>';
            }
            echo "</tr>";
          } //End Foreach ?> 
        </tbody>
      </table>
      <?php 
          if (empty($tickets)){
            echo '<p class="text-center"><em>Vous n\'avez pas encore créé de tickets...</em></p>';
          }
      //Systeme de pages (max tickets par pages)
   if (isset($param[1]) && sizeof($ntickets)-($param[1]*10) >= 10){
    if (!isset($param[1])){?>
    <span class="text-right bold" style="float: right;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/1">Page suivante <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></span>
  <?php }else { ?>
    <span class="text-right bold" style="float: right;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/<?php echo $param[1]+1;?>">Page suivante <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></span>
  <?php }}
    if (isset($param[1]) && $param[1] == 1){?>
      <span class="text-left bold" style="float: left;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/"><i class="fa fa-angle-double-left" aria-hidden="true"></i> Page précedente</a></span>
    <?php }else if (isset($param[1]) && $param[1] >= 2){?>
      <span class="text-left bold" style="float: left;"><a class="bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>support/<?php echo $param[1]-1;?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> Page précedente</a></span>
    <?php } //END Systeme de pages ?> 
    </div>
  </div>
</div>
<?php foreach ($tickets as $key => $ticket) { ?>
<div id="modal_ticket_<?php echo $tickets[$key]['id']; ?>" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">#<?php echo $tickets[$key]['id']; ?> - <?php echo $tickets[$key]['titre_ticket']; ?> par <?php echo $tickets[$key]['pseudo']; ?></h4>
              </div>
              <div class="modal-body">
                <h3><?php echo $tickets[$key]['titre_ticket'] . ' par ' . '<img width=28 height=28 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $tickets[$key]['pseudo'] . '&s=28"> ' . $tickets[$key]['pseudo'] . ' le '. $tickets[$key]['date_ticket']; ?></h3>
                <hr style="margin-left: 2.3%;width: 95%;margin-top: 5px;margin-bottom: 10px;">
                <div class="content_ticket">
                <p>
                  <?php echo $tickets[$key]['contenu_ticket'];?>
                  </p>
                </div>
                <div class="reponses_bdd">
                <?php if (!empty($tickets[$key]['rep'])){ ?>
                  <hr><h4 class="text-center bold" style="font-size: 21px;">Réponses</h4><hr>
                  <?php foreach($tickets[$key]['rep'] as $rep){ ?>
                    <div id="r_c_<?php echo $rep['id'];?>">
                      <h4 style="margin-left: 2.3%;width: 92,5%;margin-top: 5px;margin-bottom: 10px;"><?php if ($rep['role'] != 0){?><span class="bold"><?php echo $controleur_def->getRoleNameById($controleur_def->bddConnexion(), $rep['role']);?> : </span><?php }?>
                      <?php echo $rep['pseudo'];?>, le <?php echo $rep['date_reponse'];?></h4>
                      <hr style="margin-left: 5%;width: 92,5%;margin-top: 5px;margin-bottom: 10px;">
                      <span style="margin-left: 5%;width: 92,5%;margin-top: 5px;"><?php echo $rep['contenu_reponse'];?></span><br/><br />
                      <?php 
                      if ($_SESSION['user']->getRoleLevel($controleur_def->bddConnexion()) >= $controleur_def->getRoleLevel($controleur_def->bddConnexion(), $rep['role'])){?> 
                        <p class="text-right bold" style="margin-left: 5%;width: 92,5%;margin-top: 5px;margin-bottom: 10px;"><em><a href="#" id="d_r_<?php echo $rep['id'];?>" class="bold">Supprimer cette réponse.</a></em></p>
                      <?php } ?>
                    </div>
                  <?php } //END foreach 2 (reps) ?>
                <?php } ?>
                </div>
                <div id="r_<?php echo $tickets[$key]['id']; ?>" class="response" <?php if ($tickets[$key]['status'] == 2){?>style="display: none;"<?php } ?>>
                  <hr style="margin-top: 20px">
                    <a href="#" class="bold rep_<?php echo $tickets[$key]['id']; ?>"><h4 class="text-center bold">Répondre... <i class="fa fa-arrow-down" aria-hidden="true"></i></h4></a>
                  <hr style="margin-bottom: 5px;">
                  <div class="center" id="rep_div_<?php echo $tickets[$key]['id']; ?>" style="display: none;">
                      <br />
                      <form method="post" action="" class="form_reponse">
                      <label for="form-control col-sm-2">Votre réponse :</label>
                      <textarea class="form-control t_r" cols="25" rows="6" name="content_post"></textarea><br />
                      <input type="hidden" class="hidden_id" value="<?php echo $tickets[$key]['id']; ?>"/>
                      <button type="submit" class="btn pull-right btn-danger sub acc">Valider</button>
                    </form>
                    <br /><br />
                  </div>
                </div>
                <div class="response_none" <?php if ($tickets[$key]['status'] != 2){?>style="display: none;"<?php } ?>>
                  <hr style="margin-top: 20px">
                    <h4 class="text-center bold"><em>Vous ne pouvez plus répondre car le ticket est fermé.</em></h4>
                  <hr style="margin-bottom: 5px;">
                </div>
              </div>
              <div class="modal-footer">
                <?php if ($tickets[$key]['status'] != 2 ){
                        echo '<button type="button" class="d_' . $tickets[$key]['id']. ' btn btn-danger btn-default" data-dismiss="modal"><span class="d_' . $tickets[$key]['id']. '_text">Fermer le sujet</span></button>';
                      }else {
                        echo '<button type="button" class="btn btn-default">Le ticket est déjà fermé</button>';
                      }?>
                  <button type="button" class="btn btn-default" data-dismiss="modal">FERMER</button>
              </div>
          </div>
      </div>
  </div>
  <script>
    //MODAL View ticket
    $(".v_<?php echo $tickets[$key]['id']; ?>").click(function(){
      $("#modal_ticket_<?php echo $tickets[$key]['id']; ?>").modal('show');
    });
    //END modal
    //REPONSE DANS MODAL TICKET
    $(".rep_<?php echo $tickets[$key]['id']; ?>").click(function(){
      if ($('#rep_div_<?php echo $tickets[$key]['id']; ?>').is(":hidden")){
        $('.rep_<?php echo $tickets[$key]['id']; ?>').html('<h4 class="text-center">Répondre... <i class="fa fa-arrow-up" aria-hidden="true"></i></h4>');
        $('#rep_div_<?php echo $tickets[$key]['id']; ?>').show();
      }else {
        $('.rep_<?php echo $tickets[$key]['id']; ?>').html('<h4 class="text-center">Répondre... <i class="fa fa-arrow-down" aria-hidden="true"></i></h4>');
        $('#rep_div_<?php echo $tickets[$key]['id']; ?>').hide();
      }
    });
    //END REPONSE
  </script>

  <?php 
  //Systeme de fermeture de tickets
    if ((isset($_SESSION['admin']) && $_SESSION['admin']) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $tickets[$key]['pseudo'])) {?>
          <script>
          $(".d_<?php echo $tickets[$key]['id']; ?>").click(function(){
              $.ajax({
                 url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/d_t/<?php echo $tickets[$key]['id']; ?>/',
                 type : 'GET',
                 success: function (data_rep) {
                    if (data_rep != "Success"){
                      alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                    }else {                   
                      $(".d_<?php echo $tickets[$key]['id'];?>_text").html('<i class="fa fa-times" aria-hidden="true"></i>  Le ticket est déjà fermé');
                      $("#r_<?php echo $tickets[$key]['id']; ?>").hide();
                    }
                 },
                 error: function() {
                    alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                 }
              });
          });
              </script>
    <?php }
    //Systeme de suppression des reponses par ticket
    foreach($tickets[$key]['rep'] as $rep){  
      if ((isset($_SESSION['admin']) && $_SESSION['admin']) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $rep['pseudo'])) {?>
            <script>
            $("#d_r_<?php echo $rep['id']; ?>").click(function(){
                $.ajax({
                  url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/d_r/<?php echo $rep['id']; ?>/',
                  type : 'GET',
                  success: function (data_rep) {
                      if (data_rep != "Success"){
                        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                      }else {
                        $('#r_c_<?php echo $rep['id'];?>').hide();
                        var old_v = $('#n_<?php echo $ticket['id'];?>').html();
                        $('#n_<?php echo $ticket['id'];?>').html(old_v-1);
                      }
                  },
                  error: function() {
                      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
                  }
                });
            });
                </script>
      <?php } 
    } //End Foreach (reponses)
   } //End Foreach (modal)?> 
</div>
<script>
//Ajout d'une réponse
$('.form_reponse').submit(function(e){
  e.preventDefault();
  $.ajax({
      url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/a_r/' + $('input[class="hidden_id"]', this).val(),
      type : 'POST',
      data : 'pseudo=<?php echo $_SESSION['pseudo']; ?>&content=' + $('textarea[name="content_post"]', this).val(),
      dataType : 'html',
      success: function (data_rep){
        if (data_rep != "Success"){
          debugger;
          alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
        }else {
          debugger;
          location.reload(true);
        }
      },
      error: function (data_rep) {
        alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
      }     
  });
});
//END ADD         
</script>
<?php } ?>
<script>
  $('.addTicket').click(function(){
    $('#create_ticket').modal('show');
  });
  $('#newticket_form').submit(function(e){
    e.preventDefault();
    if ($("#title").val() == "" || $("#content").val() == ""){
      $("#champs_newticket").css("display", "block");
    }else {
      $.ajax({
        url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/a/a_t/',
        type : 'POST',
        data : 'title=' + $('input[name="title"]', this).val() + '&content=' + $('textarea[name="content"]', this).val(),
        dataType : 'html',
        success: function (data_rep){
          if (data_rep != "Success"){
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
          }else {
            location.reload(true);
          }
        },
        error: function (data_rep) {
          alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }     
    });
    }
  });
</script>