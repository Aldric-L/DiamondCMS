<?php global $tickets, $ntickets; ?>
<!--Titre de la page -->
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay" ></div>
  <div class="text">
    <h1>Support <?php if ($_SESSION['user']->getLevel() >= 2){?>- Version <?php echo $_SESSION['user']->getRName($controleur_def->bddConnexion()); }?></h1>
  </div>
</div>

<?php if ($_SESSION['user']->getLevel() < 2){?>
<!-- Modal de création de Tickets -->
<div id="create_ticket" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title text-center">Créer un nouveau ticket</h4>
        </div>
        <div class="modal-body">
          <h2 class="text-center">Création d'un ticket :</h2>
          <form method="post" id="newticket_form">
            <div class="form-group">
              <label for="titre_ticket" class="col-form-label">Titre du ticket :</label>
              <input class="form-control" type="text" name="titre_ticket" id="titre_ticket">
              <small id="titleHelp" class="form-text text-muted">Il doit être clair et bref pour résumer votre problème.</small>
            </div>
            <br />
            <div class="form-group">
              <label for="contenu_ticket" class="col-form-label">Contenu de votre demande :</label>
              <textarea class="form-control" rows="10" type="text-area" id="contenu_ticket" name="contenu_ticket"></textarea>
              <small id="contentHelp" class="form-text text-muted">Elle doit être détaillée, la plus soignée possible, et ne pourra être modifiée.</small>
            </div>
            <br />
            <h5 class="text-center text-danger">Attention, une fois votre ticket envoyé,<br/> il sera impossible à supprimer.</h5><br/>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
          <button type="submit" class="btn btn-custom ajax-simpleSend"
            data-module="support/" data-verbe="get" data-func="createTicket" data-tosend="#newticket_form" data-useform="true" data-reload="true"
            >Créer le ticket</button>
        </div>
      </div>
  </div>
</div>
<!-- FIN Modal de création de Tickets -->
<?php }?>

<!-- Explication du Support -->
<div id="explic">
  <h2><?php echo $Serveur_Config['Serveur_name']; ?> met à votre disposition un support.</h2>
  <p class="explicp">Ce support vous permet de contacter gratuitement notre équipe. Les tickets ne sont lisibles que par vous et nous. 
    Nous vous répondons dans les délais les plus courts possibles. N'hésitez pas à verifier sur le forum si votre question n'a pas été déjà traitée.
  </p>
</div>
<!-- FIN Explication du Support -->
<?php if ($_SESSION['user']->getLevel() < 2){?>
<h4 class="text-center">
<button class="btn btn-custom addTicket" data-toggle="modal" data-target="#create_ticket" >Créer un nouveau ticket</button> <a class="btn btn-custom" href="<?= LINK; ?>forum" >Retourner sur le forum</a>
</h4>
<?php }else { ?>
<h3 class="text-center">
Derniers tickets créés par les utilisateurs
</h3>
<?php } ?> 
<br />
<div class="content-container container">
  <!-- Affichage des tickets -->
  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
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
        
        echo '<td><img width="30px" height="30px" src="' . LINK . 'getprofileimg/'. $tickets[$key]['pseudo'] . '/120"></td><td> ' . $tickets[$key]['pseudo'] . "</td>";
        
        
        echo "<td>" . $tickets[$key]['titre_ticket'] . "</td>";
        echo "<td>" . $tickets[$key]['date_t'] . "</td>";
        echo "<td>" . $tickets[$key]['status_l'] . "</td>";
        echo '<td id="n_'. $ticket['id'] . '">' . sizeof($tickets[$key]['rep']) . "</td>";
        echo '<td><a class="bold" data-toggle="modal" data-target="#modal_ticket_' . $tickets[$key]['id'] . '" data="'. $tickets[$key]['id'] . '" href="#"><i class="fa fa-eye" aria-hidden="true"></i> Voir </a>';
        if ($tickets[$key]['status'] != 2 ){
          echo '| <a class="ajax-simpleSend bold" data-module="support/" data-verbe="set" data-func="closeTicket" data-reload="true" data-tosend="id='. $tickets[$key]['id'] . '" style="color: red;" href="#"><i class="fa fa-times" aria-hidden="true"></i>  Fermer le ticket</a></td>';
        }else {
          echo '| <i class="fa fa-times" aria-hidden="true"></i>  Le ticket est déjà fermé</td>';
        }
        echo "</tr>";
     } //End Foreach?>
    </tbody>
  </table>
  <!-- FIN Affichage des tickets -->
  
  <?php 
   //Systeme de pages (max tickets par pages)
   if (($ntickets > 10 && (!isset($param[1]) && empty($param[1]))) || (isset($param[1]) && is_numeric($param[1]) && ($ntickets-($param[1]*10)) >= 10)){
    if (!isset($param[1])){?>
    <span class="text-right bold" style="float: right;"><a class="bold" href="<?= LINK; ?>support/1">Page suivante <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></span>
  <?php }else { ?>
    <span class="text-right bold" style="float: right;"><a class="bold" href="<?= LINK; ?>support/<?php echo intval($param[1])+1;?>">Page suivante <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></span>
  <?php }}
    if (isset($param[1]) && is_numeric($param[1]) && intval($param[1]) == 1){?>
      <span class="text-left bold" style="float: left;"><a class="bold" href="<?= LINK; ?>support/"><i class="fa fa-angle-double-left" aria-hidden="true"></i>Page précedente</a></span>
    <?php }else if (isset($param[1]) && $param[1] >= 2){?>
      <span class="text-left bold" style="float: left;"><a class="bold" href="<?= LINK; ?>support/<?php echo intval($param[1])-1;?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i>Page précedente</a></span>
    <?php }
  //END Systeme de pages ?> 
</div>
<!-- Création d'un modal par ticket -->
<?php foreach ($tickets as $key => $ticket) {?>
  <div id="modal_ticket_<?php echo $tickets[$key]['id']; ?>" class="modal fade">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">#<?php echo $tickets[$key]['id']; ?> - <?php echo $tickets[$key]['titre_ticket']; ?> par <?php echo $tickets[$key]['pseudo']; ?></h4>
              </div>
              <div class="modal-body">
                <h2><?php echo $tickets[$key]['titre_ticket'] . ' par ' . '<img width="42px" height="42px" src="' . LINK . 'getprofileimg/'. $tickets[$key]['pseudo'] . '/120"> ' . $tickets[$key]['pseudo'] . ' le '. $tickets[$key]['date_t']; ?></h2>
                <div class="content_ticket">
                  <?php echo htmlspecialchars_decode(DiamondShortcuts\utf8_decode($tickets[$key]['contenu_ticket']));?>
                </div>
                <br>
                <div class="reponses">
                <?php if (!empty($tickets[$key]['rep'])){ ?>
                  <?php foreach($tickets[$key]['rep'] as $rep){ ?>
                    <div id="r_c_<?php echo $rep['id'];?>">
                      <h4 style="margin-left: 4%;width: 96%;margin-top: 5px;margin-bottom: 10px;">
                      <?php if ($controleur_def->getRoleLevel($controleur_def->bddConnexion(),$rep['role']) > 1){?><span class="bold"><?php echo $controleur_def->getRoleNameById($controleur_def->bddConnexion(), $rep['role']);?> : </span><?php }?>
                      <img width="35px" height="35px" src="<?php echo LINK . 'getprofileimg/'. $rep['pseudo'] . '/120'; ?> ">
                      <?php echo $rep['pseudo'];?>, le <?php echo $rep['date_rep'];?>
                      <?php if ($_SESSION['user']->getLevel() > 4 || $_SESSION['user']->getLevel() > $controleur_def->getRoleLevel($controleur_def->bddConnexion(), $rep['role'])){?> 
                        <span class="pull-right"><a href="#" class="text-danger ajax-simpleSend" data-module="support/" data-verbe="set" data-func="deleteAnswer" data-reload="true" data-tosend="id=<?php echo $rep['id'];?>"><i class="fa fa-trash" aria-hidden="true"></i></a></span>
                      <?php } ?>
                      </h4>
                      <hr style="margin-left: 4%;width: 96%;margin-top: 5px;margin-bottom: 10px;">
                      <div style="margin-left: 4%;width: 96%;margin-top: 5px;"><?php echo htmlspecialchars_decode(DiamondShortcuts\utf8_decode($rep['contenu_reponse']));?></div><br/><br />
                     
                    </div>
                  <?php } //END foreach 2 (reps) ?>
                <?php }else { ?>
                    <p class="text-center"><em>Aucune réponse n'a pour l'instant été enregistrée.</em></p>
                <?php } ?>
                </div>
                <?php if ($tickets[$key]['status'] != 2){?>
                <hr>
                <div class="response">
                  <div class="center" id="rep_div_<?php echo $tickets[$key]['id']; ?>">
                      <br />
                      <form method="post" id="form_reponse_<?php echo $tickets[$key]['id']; ?>" class="form_reponse">
                      <label for="form-control col-sm-2">Votre réponse :</label>
                      <textarea class="form-control" cols="25" rows="6" name="contenu_reponse""></textarea><br />
                      <input type="hidden" class="hidden_id" name="id_ticket" value="<?php echo $tickets[$key]['id']; ?>"/>
                      <button type="submit" class="btn pull-right btn-custom ajax-simpleSend"
                      data-module="support/" data-verbe="get" data-func="createAnswer" data-reload="true" data-tosend="#form_reponse_<?php echo $tickets[$key]['id']; ?>" data-useform="true">Envoyer cette réponse</button>
                    </form>
                    <br /><br />
                  </div>
                </div>
                <?php }else{ ?>
                  <p class="text-center"><em>Vous ne pouvez plus répondre car le ticket est fermé.</em></p>
                <?php } ?>
              </div>
              <div class="modal-footer">
                <?php if ($tickets[$key]['status'] != 2 ){
                        echo '<button type="button" class="d_' . $tickets[$key]['id']. ' btn btn-danger" data-dismiss="modal"><span class="d_' . $tickets[$key]['id']. '_text">Fermer le sujet</span></button>';
                      }else {
                        echo '<button type="button" class="btn btn-default">Le ticket est déjà fermé</button>';
                      }?>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
              </div>
          </div>
      </div>
  </div>
  <!-- FIN Création d'un modal par ticket -->
  <?php 
  } ?>

