<?php
global $infos, $lastactions, $pseudo, $not_user, $can_ban, $not_found, $Serveur_Config, $commandes;
 
if (empty($infos) || $not_found){?>
    <div id="emptyServer">
    <br />
    <h1>Erreur !</h1>
    <h2>Le compte demandé n'existe pas !</h2>
    <br /><br /><br />
  </div><br /><br /><br />
<?php }else {
 ?>
 <div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
   <div class="overlay"></div>
   <div class="text">
   <?php if (!$not_user){ ?>
     <h1>Mon Compte</h1>
   <?php }else { ?>
    <h1>Le compte de <?php echo $pseudo; ?></h1>
   <?php } ?>
   </div>
 </div>
<?php if (!$infos[0]['is_ban']){ ?>
<div class="content-container container content-page">
  <h1 class="text-center">Informations sur le compte de <?php echo $pseudo; ?></h1>
  <br /><br />
  <div class="row">
    <div class="col-sm-4 col-lg-4 col-sm-offset-2 col-lg-offset-2"><!--
        <img class="img-responsive img-rounded" src="<?php echo $Serveur_Config['api_url']; ?>skin.php?id=<?php echo $Serveur_Config['id_cms']; ?>&u=<?php echo $pseudo; ?>&s=500">
        -->
        <p style="text-align: right;"><br><img class="" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>getprofileimg/<?php echo $pseudo; ?>/200"></p>
    </div>
    <div class="col-sm-4 col-lg-4">
        <h2>Infos : </h2>
        <p><strong>Pseudo :</strong> <?php echo $infos[0]['pseudo'];?><br/>
        <?php if (!$not_user){ ?>
           <strong>Mot de passe :</strong> Vous seul le connaissez !<br/>
          <?php } ?>
          <?php if (!$not_user){ ?>
           <strong>Email :</strong> <?php echo $infos[0]['email'];?><br/>
          <?php }else { ?>
           <strong>Email :</strong> Nous ne divulguons pas les emails de nos membres.<br/>
          <?php } ?>
           <strong>Nombre de votes :</strong> <?php echo $infos[0]['votes'];?><br/>
           <strong>Argent en ligne :</strong> <?php echo $infos[0]['money'];?> <?php echo $Serveur_Config['Serveur_money'];?>(s)<br/>
           <strong>Pseudo :</strong> <?php echo $infos[0]['pseudo'];?><br/>
           <strong>Inscrit le :</strong> <?php echo $infos[0]['date_inscription'];?><br/>
           <strong>Rang :</strong> <?php echo $infos[0]['grade']; ?><br/>
           <?php if (!$not_user){ ?>
              <strong><a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>compte/deconnexion/" class="bold">Se déconnecter...</a></strong><br/>
           <?php } ?>
        </p>
    </div>
  </div>
  <?php if ($can_ban){?>
    <hr>
    <p class="text-center"><a class="ban" href="#" style="text-decoration: none; color: black;"><i class="fa fa-ban text-danger" aria-hidden="true"></i> Bannir cet utilisateur</a> - 
    <a class="supp" data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/supp/<?php echo $infos[0]['pseudo']; ?>/" href="#" style="text-decoration: none; color: black;"><i class="fa fa-trash-o fa-lg"></i> Supprimer toutes ses interventions</a></p>
    <?php if (!$not_user){?>
      <p class="text-center text-danger">Se bannir soi-même est une idée comme une autre.</p>
    <?php } ?>
  <?php }else {?>
    <p class="text-center text-danger">Vous n'avez pas l'autorisation de bannir ce compte.</p>
  <?php } ?>
  <hr>
  <?php 
  //Affichage de l'interface pour modifier le compte
  if (!$not_user){ ?>
    <div class="container">
      <div class="rows">
            <h3 class="text-center">Modifier votre profil :</h1><br>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
              <form method="POST" action="" enctype="multipart/form-data" class="" id="modify_profil">
                <div class="form-group row">
                  <label for="pseudo" class="col-sm-2 col-form-label">Pseudo</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="pseudo" id="pseudo" value="<?= $infos[0]['pseudo']; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="email" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="email" name="email" value="<?= $infos[0]['email']; ?>">
                  </div>
                </div>
                <?php if (!defined("DMcProfileImg") || DMcProfileImg == false){ ?>
                  <div class="form-group row">
                    <label for="img" class="col-sm-2 col-form-label">Photo de profil</label>
                    <div class="col-sm-10">
                      <input type="file" class="form-control-file" placeholder="file" name="img" id="img">
                      <small id="imgHelpBlock" class="form-text text-muted">
                        Vous pouvez renvoyer ce formulaire sans impacter votre photo de profil en laissant ce champ vide.<br>
                        <span style="color: red;">Attention ! Votre photo de profil doit <strong>impérativement être un carré</strong> et être un png ou un jpeg</span>
                      </small>
                    </div>
                  </div>
                  <p style="text-align: right;"><button type="submit" id="submit-all" class="btn btn-success btn-md">Envoyer</button></P>
                <?php } ?>
              </form>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div><!-- ./container -->
    <hr>
  <?php } ?>
  <?php 
  //Affichage de l'interface pour modifier le compte
  if (!$not_user){ ?>
    <div class="container">
      <div class="rows">
            <h3 class="text-center">Vos dernières notifications :</h1>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
                <?php
                if (empty($controleur_def->getNotifyLog($infos[0]['id']))){ ?>
                    <p style="text-align: center;">Aucune notification à afficher</p>
                <?php } ?>
                <ul>
                <?php foreach ($controleur_def->getNotifyLog($infos[0]['id']) as $n) {  ?>
                <li>
                  <strong><?php echo $n['title']; ?></strong> - 
                 <?php echo $n['content']; ?> - <a href="<?= $n['link']; ?>">En savoir plus...</a>
                </li>
              <?php  }   ?>
              </ul>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div><!-- ./container -->
    <hr>
  <?php } ?>
  <?php 
  //Affichage de l'interface pour modifier le compte
  if (!$not_user){ ?>
    <div class="container">
      <div class="rows">
            <h3 class="text-center">Vos dernières commandes :</h1>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
                <?php
                if (empty($commandes)){ ?>
                    <p style="text-align: center;">Vous n'avez pour le moment rien acheté sur notre boutique !</p>
                <?php } ?>
                <ul>
                <?php foreach ($commandes as $c) {  ?>
                <li>
                  <strong>Achat de l'article "<?php echo $c['article']['name']; ?>" pour <?php echo $c['price']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong> - 
                 Numéro de commande : <?php echo $c['uuid']; ?> (passée le <?= $c['date']; ?>)
                 <?php if ($c['success']){ ?>
                    - <span style="color: green;"><strong>Terminée avec succès</strong></span>
                 <?php }else { ?>
                    - <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>boutique/getback/<?=$c['uuid']; ?>">Finir de réceptionner la commande</a>
                 <?php } ?>
                </li>
              <?php  }   ?>
              </ul>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div><!-- ./container -->
    <hr>
  <?php } ?>
  <?php if (!$not_user){ ?>
    <h2 class="text-center">Vos dernières interventions :</h1>
  <?php }else { ?>
    <h2 class="text-center">Ses dernières interventions :</h1>
  <?php } 
    if (!empty($lastactions)){
      echo '<br /><br /><div class="last">';
      foreach ($lastactions as $key => $lastaction) {
        echo '<p><strong class="bold">Le ' . $lastactions[$key]['date_com'] . ' sur le sujet "' . $lastactions[$key]['id_post']['titre_post'] . '" par ' . $lastactions[$key]['id_post']['user'] . "</strong><br />";
        echo $lastactions[$key]['content_com'] . "</p>";
        echo '<p class="text-right"><a href="'  .$Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum/com/'. $lastactions[$key]['id_post']['id'] .'/">Retourner sur le sujet...</a></p><br />';
      }
      echo "</div>";
    }else {
      if (!$not_user){
          echo '<h4 class="text-center text-warning">Vous n\'avez pas encore participé à un sujet !</h4>';
        }else {
          echo '<h4 class="text-center text-warning">'. $pseudo . ' n\'a pas encore participé à un sujet !</h4>';
        }    
      }
 ?>
</div>
<style>
.last {
  width: 80%;
  margin: auto;
}
</style>
<?php if ($can_ban && $not_user){ ?>
<!-- Ban modal -->
<div id="ban_modal" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Validation requise !</h4>
        </div>
        <div class="modal-body">
          <h5 class="text-danger">Attention ! Vous vous apretez a éffectuer une action irrévertible !</h5>
          <p>Êtes vous sûr de vouloir bannir définitivement <?php echo $infos[0]['pseudo'];?> ?</p>
          <br />
          <p>Veuillez indiquer une raison au bannissement : <input type="text" id="reason"></input></p>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default">Fermer</button>
          <button type="button" class="btn btn-danger" data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/ban/<?php echo $infos[0]['pseudo']; ?>/" id="ban_button">Valider</button>
        </div>
      </div>
  </div>
</div>
<!-- END Ban modal -->
<?php } // $can_ban && not_user ?>

<?php if ($can_ban && $not_user) { ?>
  <div id="emptyServer" style="display: none;">
      <br />
      <h1>Compte banni !</h1>
      <h2>Le compte demandé a été banni.</h2>
      <br /><br /><br />
  </div><br /><br /><br />
<?php } ?>
<?php }else { /*!$is_ban*/ ?>
  <div id="emptyServer" style="">
    <br />
    <h1>Compte banni !</h1>
    <h2>Le compte demandé a été banni.</h2>
    <br /><br /><br />
</div><br /><br /><br />
<?php } 
 } // !empty($infos) ?>