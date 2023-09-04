<?php
  global $Serveur_Config, $controleur_def, $erreur_vote, $param, $ban, $notify, $notifyadmin, $conf_mce;
  $css = $controleur_def->css;
  $title = $controleur_def->title;
  $mds = $controleur_def->getHeaderPages();
?>
<!DOCTYPE html>
<html id="diamondhtml">
<?php // NE JAMAIS MODIFIER LES ATTRIBUTS DU HEAD SOUS PEINE DE PERTE DU FONCTIONNEMENT D'AJAX SIMPLESEND ?>
<head data-baselink="<?php echo LINK; ?>" id="diamondhead">
  <meta charset="utf-8">
  <meta lang="fr">
  <meta name="author" content="DiamondCMS, pour <?= $Serveur_Config['Serveur_name']; ?>">
  <link rel="icon" href="<?php echo Manager::makeGetImageLink($Serveur_Config['favicon']); ?>" />

  <!-- Title -->
  <?php if (!empty($title)){?>
  <meta title="<?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?>">
  <title><?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?></title>
  <?php }else{?>
  <title><?= $Serveur_Config['Serveur_name']; ?></title>
  <?php } ?>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/animate.css">
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/colors.css"/>
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/fonts.css"/>
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/sources.css"/>
  <?php if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User && $_SESSION['user']->isAdmin()): ?>
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/editing.css"/>
  <?php endif; ?>
  <?php if (isset($_SESSION['editing_mode']) && $_SESSION['editing_mode']): ?>
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/editing-mode.css"/>
  <?php endif; ?>
  <?php if (!empty($css)){?>
  <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/<?= $css; ?>.css"/>
  <?php } ?>

  <!-- Polices 
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">-->

  <script src="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/js/jquery-3.1.1.js"></script>
  <?php if ($conf_mce['editor']['enable'] == true){ ?>
    <script src='https://cdn.tiny.cloud/1/<?= $conf_mce['editor']['key']; ?>/tinymce/5/tinymce.min.js'></script>
  <?php } ?>

  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
  <?php $controleur_def->pluginloader(true, false); ?>
  
  <!-- JavaScript : Erreurs Modal, et Google analytics -->
    <?php if (!empty($controleur_def->getErrors())){ ?>
    <script type="text/javascript">
      $(function(){
        $("#c_def_error").modal('show');
        return true;
      });
    </script>
    <?php }else {?>
      <script type="text/javascript">
        $(document).ready(function(){
          <?php if (isset($param[0]) && $param[0] == "connexion" && $ban == false){?>
          $("#connexion_modal").modal('show');
          <?php }else if (isset($param[0]) && $param[0] == "inscription") { ?>
          $("#inscription_modal").modal('show');
          <?php }else if (isset($param[0]) && $param[0] == "endReinit") { ?>
          $("#endreinit_modal").modal('show');
          <?php }else if (isset($param[0]) && $param[0] == "unsubscribe") { ?>
          $("#unsubscribe_modal").modal('show');
          <?php }else if ($ban != false) { ?>
          $("#ban_modal").modal('show');
          <?php } ?>
          return true;
        });
      </script>
    <?php } ?>
  <script>
    <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']) && !empty($notify)){ 
      foreach ($notify as $n) { ?>
        $(document).ready(function () {
            Snarl.addNotification({
                title: "<?php echo str_replace('"', "'", $n['title']); ?>",
                text: "<?php echo str_replace('"', "'", $n['content']); ?>",
                <?php if ($n['type'] == 1){ //CONTACT ?>
                  icon: '<i class="fa fa-comment" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 2) { //SUPPORT ?>
                  icon: '<i class="fa fa-question-circle-o" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 3) { //ERROR ?>
                  icon: '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 4) { //FORUM ?>
                  icon: '<i class="fa fa-comments-o" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 5) { //BOUTIQUE ?>
                  icon: '<i class="fa fa-shopping-cart" aria-hidden="true"></i>',
                <?php }else { ?>
                icon: '<i class="fa fa-quote-right" aria-hidden="true"></i>',
                <?php } ?>
                <?php if (!empty($n["link"])){?>
                action: "<?php echo $n["link"]; ?>",
                <?php } ?>
                timeout: 12000

            });
        });
  <?php } } ?>
  <?php if (!empty($notifyadmin)){ 
      foreach ($notifyadmin as $n) { if ($n['view'] != 1) {?>
        $(document).ready(function () {
            Snarl.addNotification({
                title: "<?php echo str_replace('"', "'", $n['title']); ?>",
                text: "<?php echo str_replace('"', "'", $n['content']); ?>",
                <?php if ($n['type'] == 1){ //CONTACT ?>
                  icon: '<i class="fa fa-comment" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 2) { //SUPPORT ?>
                  icon: '<i class="fa fa-question-circle-o" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 3) { //ERROR ?>
                  icon: '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 4) { //FORUM ?>
                  icon: '<i class="fa fa-comments-o" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 5) { //BOUTIQUE ?>
                  icon: '<i class="fa fa-shopping-cart" aria-hidden="true"></i>',
                <?php }else { ?>
                icon: '<i class="fa fa-quote-right" aria-hidden="true"></i>',
                <?php } ?>
                <?php if (!empty($n["link"])){?>
                action: "<?php echo $n["link"]; ?>",
                <?php } ?>
                timeout: 12000
            });
        });
  <?php } } } ?>
  </script>
  <!-- END JS -->
</head>
<body id="diamondbody">
  <?php if ($ban != false){ global $r_ban; ?>
    <div id="ban_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Vous avez été banni !</h4>
                    </div>
                    <div class="modal-body">
                        <h4>
                          <span style="font-family: 'Raleway';">Vous ne pouvez plus vous connecter car un administrateur vous a banni pour la raison suivante :</span><br/> 
                          <br>
                          <span class="text-danger"><?php echo $r_ban; ?></span></h4>

                        <br><p class="text-right"><em>Votre IP a été enregistrée.</em></p> 
                    </div>
                    <div class="modal-footer">
                        <a href="<?= LINK; ?>"><button type="button" class="btn btn-default">Fermer</button></a>
                    </div>
                </div>
            </div>
        </div>
  <?php } ?>
      <?php if (!isset($_SESSION['pseudo']) && empty($_SESSION['pseudo'])){ ?>
          <div id="inscription_modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h3 class="modal-title text-center"><i class="fa fa-sign-in" aria-hidden="true"></i> Inscription !</h3>
                      </div>
                      <div class="modal-body">
                        <h4 class="text-center" id="inscriptionReturn"></h4><br />
                        <form method="post" action="<?= LINK; ?>inscription" id="inscription_form">
                          <div class="form-group">
                            <label for="pseudo_inscription" class="col-form-label">Votre pseudo :</label>
                            <input class="form-control" type="text" name="pseudo_inscription" id="pseudo_inscription">
                            <small id="pseudoHelp" class="form-text text-muted">Il doit être le même que celui in-game.</small>
                          </div>
                          <div class="form-group">
                            <label for="email_inscription" class="col-form-label">Email</label>
                            <input class="form-control" type="email" id="email_inscription" name="email_inscription">
                            <small id="mpHelp" class="form-text text-muted">Elle ne sera pas divulguée et nous n'en abuserons pas.</small>
                          </div>
                          <div class="form-group">
                            <label for="mp_inscription" class="col-form-label">Mot de passe</label>
                            <input class="form-control" type="password" id="mp_inscription" name="mp_inscription">
                            <small id="mpHelp" class="form-text text-muted">Il doit faire plus de 6 caractères.</small>
                          </div>
                          <div class="form-group">
                            <label for="mp2_inscription" class="col-form-label">Confirmation du mot de passe</label>
                            <input class="form-control" type="password" id="mp2_inscription" name="mp2_inscription">
                            <small id="mp2Help" class="form-text text-muted">Répetez-le.</small>
                          </div>
                          <div class="form-check">
                            <label class="form-check-label">
                              <input type="checkbox" class="form-check-input" name="news">   S'abonner à la news-letter
                            </label>
                          </div>
                          <input type="hidden" value="<?php echo str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']); ?>" id="inscriptionPageReturn" name="page">
                          <!--<button type="submit" class="btn btn-custom align-right center-block">Valider et s'inscrire</button>-->
                          <button class="btn btn-custom align-right center-block ajax-simpleSend" data-api="<?php echo LINK; ?>api/" 
                          data-module="comptes/" data-verbe="get" data-func="addUser" data-tosend="#inscription_form" data-useform="true" data-callback="inscriptionCallback">Valider et se connecter</button>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <div class="container-fluid" style="padding: 0;">
                          <div class="rows">
                            <div class="col-lg-10">
                            <p style="text-align: justify; font-size: 11px;"><small><em>En poursuivant la procédure d'inscription, vous acceptez nos conditions générales d'utilisation et vous consentez au fait qu'un cookie soit déposé sur votre ordinateur le temps de votre navigation sur notre site internet, et que votre adresse IP soit conservée.</em></small></p>

                            </div>
                            <div class="col-lg-2">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
          <div id="connexion_modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h3 class="modal-title text-center"><i class="fa fa-key" aria-hidden="true"></i> Connexion !</h3>
                      </div>
                      <div class="modal-body">
                        <p class="text-right"><em>Pas encore de compte ? <a href="<?php echo LINK; ?>inscription"><i class="fa fa-sign-in" aria-hidden="true"></i> inscrivez-vous !</a></em></p>
                        <h4 class="text-center" id="connectReturn"></h4><br />
                        <form id="connexion_form" method="post">
                          <div class="form-group row">
                            <label for="pseudo_connexion" class="col-xs-2 col-form-label">Votre pseudo :</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="text" name="pseudo_connexion" id="pseudo_connexion" required>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label for="mp_connexion" class="col-xs-2 col-form-label">Password</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="password" id="mp_connexion" name="mp_connexion" required>
                            </div>
                          </div>
                          <div class="form-check">
                            <label class="souvenir">
                              <input class="form-check-input" type="checkbox" value="" id="souvenir" name="souvenir">
                              Se souvenir de moi
                            </label>
                          </div>
                          <input type="hidden" value="<?php echo str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']); ?>" name="page">
                          <!--<button type="submit" class="btn btn-custom align-right center-block">Valider et se connecter</button>-->
                          <button class="btn btn-custom align-right center-block ajax-simpleSend" data-api="<?php echo LINK; ?>api/" 
                          data-module="comptes/" data-verbe="get" data-func="connectUser" data-tosend="#connexion_form" data-useform="true" data-callback="connectCallback">Valider et se connecter</button>
                      </form>
                        <p class="text-right" style="margin-bottom: 0; margin-top: 2rem;"><em>Des difficultés à vous connecter ? <a href="#" class="pswd_reinit">Réinitialisez votre mot de passe.</a></em></p>
                      </div>
                      <div class="modal-footer">
                        <div class="container-fluid" style="padding: 0;">
                          <div class="rows">
                            <div class="col-lg-10">
                            <p style="text-align: justify; font-size: 11px;"><small><em>En poursuivant la procédure de connexion, vous consentez au fait qu'un cookie soit déposé sur votre ordinateur le temps de votre navigation sur notre site internet, ou qu'il soit conservé un an si vous avez coché la case "se souvenir de moi".</em></small></p>

                            </div>
                            <div class="col-lg-2">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
          <div id="reinit_modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h3 class="modal-title text-center"><i class="fa fa-question-circle" aria-hidden="true"></i> Mot de passe oublié</h3>
                      </div>
                      <div class="modal-body">
                        <p class="text-justify"><em>Pour procéder à la réinitialisation du mot de passe, il vous faut disposer de l'adresse email que vous avez indiqué lors de votre inscription. A partir de celle-ci, un code sera généré pour vous permettre de réinitialiser votre mot de passe.</em></p>
                        <h4 class="text-center" id="reinitReturn"></h4><br />
                        <form id="reinit_form" method="post">
                          <div class="form-group row">
                            <label class="col-xs-2 col-form-label">Email du compte :</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="text" name="email" required>
                            </div>
                          </div>
                          <!--<button type="submit" class="btn btn-custom align-right center-block">Valider et se connecter</button>-->
                          <button class="btn btn-custom align-right center-block ajax-simpleSend" data-api="<?php echo LINK; ?>api/" 
                          data-module="comptes/" data-verbe="set" data-func="startReinitPassword" data-tosend="#reinit_form" data-useform="true" data-callback="reinitCallback" data-showreturn="true" data-reload="false">Valider</button>
                      </form>
                      </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                      </div>
                  </div>
              </div>
          </div>
          <div id="unsubscribe_modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h3 class="modal-title text-center"><i class="fa fa-question-circle" aria-hidden="true"></i> Se désabonner</h3>
                      </div>
                      <div class="modal-body">
                        <p class="text-justify"><em>Pour procéder à votre désinscription de nos listes de diffusion, il vous faut disposer de l'adresse email que vous avez indiqué lors de votre inscription. La désinscription est alors immédiate.</em></p>
                        <br />
                        <form id="unsub_form" method="post">
                          <div class="form-group row">
                            <label class="col-xs-2 col-form-label">Email du compte :</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="text" name="email" required>
                            </div>
                          </div>
                          <!--<button type="submit" class="btn btn-custom align-right center-block">Valider et se connecter</button>-->
                          <button class="btn btn-custom align-right center-block ajax-simpleSend" data-api="<?php echo LINK; ?>api/" 
                          data-module="comptes/" data-verbe="get" data-func="emailUnsubscribe" data-tosend="#unsub_form" data-useform="true" data-showReturn="true" data-reload="false">Valider</button>
                      </form>
                      </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                      </div>
                  </div>
              </div>
          </div>
          <div id="endreinit_modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h3 class="modal-title text-center"><i class="fa fa-question-circle" aria-hidden="true"></i> Mot de passe oublié</h3>
                      </div>
                      <div class="modal-body">
                        <p class="text-justify"><em>Si vous ne recevez pas de code, ou que le serveur n'est pas en mesure de vous en envoyer, n'hésitez pas à contacter un administrateur par le formulaire de contact. Les administrateurs sont en mesure de vous délivrer le code demandé.</em></p>
                        <h4 class="text-center" id="endreinitReturn"></h4><br />
                        <form id="endreinit_form" method="post">
                          <div class="form-group">
                            <label for="code_reinit" class="form-label">Code :</label>
                            <input class="form-control" type="text" name="code_reinit" id="code_reinit" required>
                          </div>
                          <div class="form-group">
                            <label for="newmp_reinit" class="form-label">Nouveau mot de passe :</label>
                            <input class="form-control" type="text" name="newmp_reinit" id="newmp_reinit" required>
                          </div>
                          <!--<button type="submit" class="btn btn-custom align-right center-block">Valider et se connecter</button>-->
                          <button class="btn btn-custom align-right center-block ajax-simpleSend" data-api="<?php echo LINK; ?>api/" 
                          data-module="comptes/" data-verbe="get" data-func="endReinitPassword" data-tosend="#endreinit_form" data-useform="true" data-callback="endreinitCallback">Valider et se connecter</button>
                      </form>
                      <br>
                      </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                      </div>
                  </div>
              </div>
          </div>
      <?php } ?>
      <?php  if ($Serveur_Config['en_jouer']){ ?>
        <div id="modalJouer" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Jouer sur <?= $Serveur_Config['Serveur_name']; ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?= htmlspecialchars_decode($Serveur_Config['text_jouer_menu']); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
  <?php if (isset($_SESSION['user']) && isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'] && $_SESSION['user']->getLevel() >= 4){ ?>
  <div id="editing-top">
        <h4 class="text-center"><strong>Mode édition</strong> - <span class="ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="stopEditing" data-reload="true">Quitter le mode</span></h4>
  </div>
  <?php } ?>
  <nav class="navbar navbar-inverse">
          <div class="container-fluid">
            <div class="navbar-header page-scroll">
                <?php if ($Serveur_Config['logo_img'] != "name_server"){ ?>
                  <img class="img-responsive img-brand navbar-brand" style="padding: 0; margin: 0; margin-top: 4px;" src="<?= Manager::makeGetImageLink($Serveur_Config['logo_img']); ?>" alt="">
                <?php }else { ?>
                  <a class="navbar-brand bold" href="<?php echo LINK; ?>"><?php echo $Serveur_Config['Serveur_name']; ?></a>
                <?php } ?>
            </div>

              <div class="collapse navbar-collapse" id="navbar-collapse" style="margin-top: 4px;">
                  <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="<?= LINK; ?>"><i class="fa fa-home fa-2" aria-hidden="true"></i> Accueil</a></li>
                    <li><a href="<?= LINK; ?>forum" accesskey="2" title=""><i class="fa fa-comments-o" aria-hidden="true"></i> Forum</a></li>
                    <?php if ($Serveur_Config['en_boutique'] || (array_key_exists('boutique_ext', $Serveur_Config) && $Serveur_Config['boutique_ext']['en_boutique_externe'])) {?>
                      <?php if (array_key_exists('boutique_ext', $Serveur_Config) && $Serveur_Config['boutique_ext']['en_boutique_externe']) {?>
                        <li><a href="<?= $Serveur_Config['boutique_ext']['link_boutique_externe']; ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Boutique</a></li>
                      <?php }else{ ?>
                        <li><a href="<?= LINK; ?>boutique"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Boutique</a></li>
                      <?php } ?>
                <?php } ?>
                    <?php foreach ($mds as $md){
                      if ($md['is_menu']){ ?>
                        <li id="serveur" class="dropdown">
                          <a href="#" class="dropdown-toggle server" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $md['name']; ?> <span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <?php foreach ($md['pages'] as $p){ 
                                if (substr($p['link'], 0, 4) == "http"){ ?>
                                <li><a href="<?= $p['link']; ?>" ><?= $p['titre']; ?></a></li>
                                <?php }else { ?>
                                <li><a href="<?= LINK; ?><?= $p['link']; ?>" ><?= $p['titre']; ?></a></li>
                                <?php } ?>
                            <?php } ?>
                          </ul>
                        </li>
                      <?php }else { 
                        if (substr($md['link'], 0, 4) == "http"){ ?>
                          <li><a href="<?= $md['link']; ?>"><?= $md['name']; ?></a></li>
                        <?php }else { ?>
                          <li><a href="<?= LINK; ?><?= $md['link']; ?>"><?= $md['name']; ?></a></li>
                        <?php } ?>
                      <?php } ?>
                    <?php } ?>
                    <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && isset($_SESSION['user'])){?>
                      <li class="dropdown compte">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="padding-bottom: 0px;">
                        <img width="60" height="60" src="<?= LINK; ?>getprofileimg/<?php echo $_SESSION['user']->getPseudo(); ?>/180"> Mon Compte</a>
    
                      <ul class="dropdown-menu compte">
                        <li><a href="<?= LINK; ?>compte/">Mon Compte</a></li>
                        <li><a href="<?= LINK; ?>support/">Support</a></li>
                        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->isAdmin()){?>
                            <li><a href="<?= LINK; ?>admin/">Panel Admin</a></li>
                        <?php } ?>
                        <li><a href="#" class="ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                              data-module="comptes/" data-verbe="get" data-func="disconnect" data-reload="true">Deconnexion</a></li>
                      </ul>
                    <?php }else {?>
                      <li><a class="connexion" href="#"><i class="fa fa-key" aria-hidden="true"></i> Connexion</a></li>
                      <li><a class="inscription" href="#"><i class="fa fa-sign-in" aria-hidden="true"></i> Inscription</a></li>
                    <?php } ?>
                  </ul>
              </div>
          </div>
      </nav>

<?php if (!empty($controleur_def->getErrors())){ ?>
<div id="c_def_error" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nous rencontrons une ou plusieurs erreurs !</h4>
            </div>
            <div class="modal-body">
              <h4>Erreur(s) levée(s) par le systeme :</h4>
              <ul>
                <?php foreach ($controleur_def->getErrors() as $err) {?>
                  <li><p class=""><span style="font-weight: bold;"><?php echo $err; ?></span><br /><em>Besoin d'aide ? Connectez-vous et contactez notre support !</em><P></li>
                <?php } ?>
              </ul>
              <br />
              <p class="text-right"><em>N'hésitez pas à signaler ces erreurs aux administrateurs du site !</em></p>
            </div>
            <div class="modal-footer">
                <a href="<?php echo WEBROOT;?>"><button type="button" class="btn btn-custom">Revenir à l'accueil</button></a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php  if ($Serveur_Config['en_vote']){ ?>
<div id="voteModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Voter pour <?= $Serveur_Config['Serveur_name']; ?></h3>
            </div>
            <div class="modal-body">
                <p>Vous pouvez voter pour nous, chaque vote vous rapportera <?php echo (isset($Serveur_Config['tokens_vote'])) ? $Serveur_Config['tokens_vote'] : "1"; ?> <?= $Serveur_Config['Serveur_money']; ?><?php echo (isset($Serveur_Config['tokens_vote']) && is_numeric($Serveur_Config['tokens_vote']) && intval($Serveur_Config['tokens_vote'])>1) ? "s" : ""; ?>.</p>
                <p>Celui-ci pourra être échanger dans la boutique.</p>
                <p class="text-danger">Attention, vous ne pouvez voter qu'une fois par jour.</p>
                  <h4>Meilleurs voteurs :</h4>
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Pseudo</th>
                        <th>Nombre de vote</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        global $voteurs;
                        foreach ($voteurs as $voteur) {
                          echo '<tr>';
                          echo '<td><img width=26 height=26 src="' . $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'getprofileimg/'. $voteur['pseudo'] . '/26"></td><td>' . $voteur['pseudo'] .'</td>';
                          echo '<td>' . $voteur['votes'] .'</td>';
                          echo '</tr>';
                        }
                       ?>
                    </tbody>
                  </table>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
              
                <?php if (!isset($_SESSION['user'])){ ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Pour voter, connectez-vous !</button>
                <?php }else if (isset($_SESSION['user']) && $_SESSION['user']->hasVotedToday()){ ?>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Vous avez déjà voté aujourd'hui !</button>
                <?php }else { ?>

                  <button type="button" class="btn btn-custom ajax-simpleSend"
                          data-api="<?= LINK; ?>api/" data-module="comptes/" data-verbe="set" data-func="addVote" data-tosend="user_id=<?php echo $_SESSION['user']->getId(); ?>" 
                          data-useform="false" data-callback="voteCallback">Voter</button>
                <?php } ?>
                <!--<a href="<?= LINK; ?>vote" class="no" style="text-decoration: none;color: white;">
                <button type="button" class="btn btn-custom vote_b"><span class="vote_btext">Voter !</span></button>
                </a>-->
            </div>
        </div>
    </div>
</div>
<script>
$('a[href="<?= LINK; ?>voter"]').click(function(e){
  e.preventDefault();
  $("#voteModal").modal('show');
});
</script>
<?php } ?>
