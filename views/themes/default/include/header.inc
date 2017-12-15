<?php
  global $Serveur_Config, $controleur_def, $erreur_vote, $param, $ban, $notify;
  $css = $controleur_def->css;
  $title = $controleur_def->title;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta lang="fr">
  <meta name="author" content="DiamondCMS, par GougDEV pour <?= $Serveur_Config['Serveur_name']; ?>">
  <link rel="icon" type="image/png" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/img/<?= $Serveur_Config['favicon']; ?>.png" />

  <!-- Title -->
  <?php if (!empty($title)){?>
  <meta title="<?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?>">
  <title><?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?></title>
  <?php }else{?>
  <title><?= $Serveur_Config['Serveur_name']; ?></title>
  <?php } ?>

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/sources.css"/>
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" media="screen">-->
  <?php if (!empty($css)){?>
  <link rel="stylesheet" type="text/css" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/<?= $css; ?>.css"/>
  <?php } ?>

  <!-- Polices -->
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">

  <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>
  <script src='//cdn.tinymce.com/4/tinymce.min.js'></script>

  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
  <?php
  if($jsfolder = opendir(ROOT . 'js/plugins/load')){
    $count = 0;
    while(false !== ($plugin = readdir($jsfolder))){
      if($plugin != '.' && $plugin != '..' && $plugin != 'plugins_load.js' && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
        if (substr($plugin, -9) != '.theme.js' || (substr($plugin, -9) == '.theme.js' && str_replace(".theme.js", "", $plugin) == $Serveur_Config['theme'])){
          $count++;
          if (substr($plugin, -3) == '.js'){
            echo '<script src="' . $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'js/plugins/load/' . $plugin . '"></script>';
          }else {
            echo '<link rel="stylesheet" type="text/css" href="' . $Serveur_Config['protocol'] . '://'.$_SERVER['HTTP_HOST'] . WEBROOT . 'js/plugins/load/'. $plugin .'"/>';
          }
        }
      }
    }
    if ($count != 0){
      echo "<!--Inclusion de " . $count . " plugins javascript ! -->";
      echo '<script src="' . $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'js/plugins/load/plugins_load.js"></script>';
    }else {
      echo '<!--Aucun plugin trouvé, chargement des libs de "base" -->';
    }
  }
  ?>
  <!-- JavaScript : Erreurs Modal, et Google analytics -->
  <?php if ($Serveur_Config['en_analytics'] == true){?>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', '<?php echo $Serveur_Config['id_analytics']; ?>', 'auto');
    ga('send', 'pageview');
  </script>
  <?php }
    if (!empty($controleur_def->getErrors())){ ?>
    <script type="text/javascript">
      $(function(){
        $("#c_def_error").modal('show');
        return true;
      });
    </script>
    <?php }else {?>
      <script type="text/javascript">
        $(document).ready(function(){
          <?php if ($param[0] == "connexion" && $ban == false){?>
          $("#connexion_modal").modal('show');
          <?php }else if ($param[0] == "inscription") { ?>
          $("#inscription_modal").modal('show');
          <?php }else if ($ban != false) { ?>
          $("#ban_modal").modal('show');
          <?php } ?>
          return true;
        });
      </script>
    <?php } ?>
  <script>
    $(document).ready(function () {
    		$("textarea").ghostDown();
    });
    <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !empty($notify)){ 
      foreach ($notify as $n) { ?>
        $(document).ready(function () {
            Snarl.addNotification({
                title: '<?php echo $n['title']; ?>',
                text: '<?php echo $n['content']; ?>',
                <?php if ($n['type'] == 1){ //CONTACT ?>
                  icon: '<i class="fa fa-comment" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 2) { //SUPPORT ?>
                  icon: '<i class="fa fa-question-circle-o" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 3) { //ERROR ?>
                  icon: '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>',
                <?php }else if ($n['type'] == 4) { //FORUM ?>
                  icon: '<i class="fa fa-comments-o" aria-hidden="true"></i>',
                <?php }else { ?>
                icon: '<i class="fa fa-quote-right" aria-hidden="true"></i>',
                <?php } ?>
                <?php if (!empty($n["link"])){?>
                action: '<?php echo $n["link"]; ?>',
                <?php } ?>
                timeout: 12000

            });
        });
  <?php } } ?>
  </script>
  <!-- END JS -->
</head>
<body>
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
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><button type="button" class="btn btn-default">Fermer</button></a>
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
                        <div id="champs_inscription" style="display: none;">
                            <h3 class="text-center text-alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Merci de remplir tous les champs ! <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h3><br />
                        </div>
                        <form method="post" action="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT;?>inscription" id="inscription_form">
                          <div class="form-group">
                            <label for="pseudo_inscription" class="col-form-label">Votre pseudo :</label>
                            <input class="form-control" type="text" name="pseudo_inscription" id="pseudo_inscription">
                            <small id="pseudoHelp" class="form-text text-muted">Il doit être le même que celui in-game.</small>
                          </div>
                          <div class="form-group">
                            <label for="email_inscription" class="col-form-label">Email</label>
                            <input class="form-control" type="email" id="email_inscription" name="email_inscription">
                            <small id="mpHelp" class="form-text text-muted">Elle ne sera pas divulgué et nous n'en abuserons pas.</small>
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
                          <div class="form-check">
                            <label class="souvenir">
                              <input class="form-check-input" type="checkbox" value="" id="souvenir" name="souvenir">
                              Se souvenir de moi
                            </label>
                          </div>
                          <input type="hidden" value="<?php echo str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']); ?>" name="page">
                          <button type="submit" class="btn btn-success align-right center-block acc">Valider et s'inscrire</button>
                        </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
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
                        <p class="text-right"><em>Pas encore de compte ? <a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>inscription"><i class="fa fa-sign-in" aria-hidden="true"></i> inscrivez-vous !</a></em></p>
                        <div id="champs_co" style="display: none;">
                            <h3 class="text-center text-alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>Merci de remplir tous les champs !<i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h3><br />
                        </div>
                        <form action="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT;?>connexion" id="connexion_form" method="post">
                          <div class="form-group row">
                            <label for="pseudo_connexion" class="col-xs-2 col-form-label">Votre pseudo :</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="text" name="pseudo_connexion" id="pseudo_connexion">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label for="mp_connexion" class="col-xs-2 col-form-label">Password</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="password" id="mp_connexion" name="mp_connexion">
                            </div>
                          </div>
                          <div class="form-check">
                            <label class="souvenir">
                              <input class="form-check-input" type="checkbox" value="" id="souvenir" name="souvenir">
                              Se souvenir de moi
                            </label>
                          </div>
                          <input type="hidden" value="<?php echo str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']); ?>" name="page">
                          <button type="submit" class="btn btn-success align-right center-block acc">Valider et se connecter</button>
                        </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                      </div>
                  </div>
              </div>
          </div>
      <?php }
      if (!empty($erreur_inscription)){ ?>
        <div id="erreur_inscription_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Erreur !</h4>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-danger"><?php echo $erreur_inscription; ?></h5>
                    </div>
                    <div class="modal-footer">
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><button type="button" class="btn btn-default">Fermer</button></a>
                    </div>
                </div>
            </div>
        </div>
      <?php }
      if (!empty($erreur_connexion)){ ?>
        <div id="erreur_connexion_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Erreur !</h4>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-danger"><?php echo $erreur_connexion; ?></h5>
                    </div>
                    <div class="modal-footer">
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><button type="button" class="btn btn-default">Fermer</button></a>
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
                        <p><?= $Serveur_Config['text_jouer_menu']; ?></p>
                        <p class="text-info">Adresse du serveur : <?= $Serveur_Config['ip_serveur']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">FERMER</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
  <nav class="navbar navbar-inverse">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <?php if ($Serveur_Config['logo_img']){ ?>
                  <img class="img-responsive img-brand navbar-brand" style="padding: 0; margin: 0; margin-top: 4px;" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/img/<?= $Serveur_Config['name_logo']; ?>" alt="">
                <?php }else { ?>
                  <a class="navbar-brand bold" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT ?>"><?php echo $Serveur_Config['Serveur_name']; ?></a>
                <?php } ?>
            </div>

              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse" id="navbar-collapse" style="margin-top: 4px;">
                  <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>"><i class="fa fa-home fa-2" aria-hidden="true"></i> Accueil</a></li>
                    <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>forum" accesskey="2" title=""><i class="fa fa-comments-o" aria-hidden="true"></i> Forum</a></li>
                    <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>boutique"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Boutique</a></li>
                    <li id="serveur" class="dropdown">
                      <a href="#" class="dropdown-toggle server" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Serveurs <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>BanList"><i class="fa fa-ban" aria-hidden="true"></i> BanList</a></li>
                        <?php if ($Serveur_Config['en_d_warns']){ echo '<li><a href="' . $Serveur_Config['protocol']. '://'. $_SERVER['HTTP_HOST'] . WEBROOT .'warns"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Warn(s)</a></li>'; } ?>
                        <?php if ($Serveur_Config['en_faq']){ echo '<li><a href="' . $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT .'faq"><i class="fa fa-question-circle" aria-hidden="true"></i> F.A.Q.(s)</a></li>'; } ?>
                        <li role="separator" class="divider"></li>
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>CGU"><i class="fa fa-bars" aria-hidden="true"></i> CGU</a></li>
                        <?php if ($Serveur_Config['en_reglement']){ echo '<li><a href="'. $Serveur_Config['protocol']. '://'. $_SERVER['HTTP_HOST'] . WEBROOT .'reglement"><i class="fa fa-shield" aria-hidden="true"></i> Reglement</a></li>'; } ?>
                        <?php  if ($Serveur_Config['en_vote'] || $Serveur_Config['en_jouer']){ ?>
                        <li role="separator" class="divider"></li>
                        <?php }  if ($Serveur_Config['en_vote']){ ?>
                        <li><a href="#" class="vote"><i class="fa fa-commenting-o" aria-hidden="true"></i> Voter</a></li>
                        <?php } ?>
                        <?php  if ($Serveur_Config['en_jouer']){ ?>
                        <li><a href="#" class="jouer"><i class="fa fa-server" aria-hidden="true"></i> Jouer !</a></li>
                        <?php } ?>
                      </ul>
                    </li>
                    <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){?>
                      <li class="dropdown compte">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="padding-bottom: 0px;">
                          <img width="60" height="60" src="http://api.diamondcms.fr/face.php?id=<?php echo $Serveur_Config['id_cms'];?>&amp;u=<?php echo $_SESSION['pseudo']; ?>&amp;s=64"> Mon Compte</a>
                      <ul class="dropdown-menu compte">
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/">Mon Compte</a></li>
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/">Support</a></li>
                        <?php if (isset($_SESSION['admin']) && !empty($_SESSION['admin']) && $_SESSION['admin']){?>
                            <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/">Panel Admin</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/deconnexion/">Deconnexion</a></li>
                      </ul>
                    <?php }else {?>
                      <li><a class="connexion" href="#"><i class="fa fa-key" aria-hidden="true"></i> Connexion</a></li>
                      <li><a class="inscription" href="#"><i class="fa fa-sign-in" aria-hidden="true"></i> Inscription</a></li>
                    <?php } ?>
                  </ul>
              </div>
              <!-- /.navbar-collapse -->
          </div>
          <!-- /.container-fluid -->
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
                <button type="button" class="btn btn-default err" href="<?php echo WEBROOT;?>">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script>
$(".err").click(function(){
    $.ajax({
       url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>erreur/seen/',
       type : 'GET',
       dataType : 'html',
       success: function (data) {
          $("#c_def_error").modal('hide');
       },
       error: function() {
          alert("Erreur, impossible de fermer la fenetre. (Code D'erreur 111, Merci de contacter les administrateurs du site.)");
       }
    });
});
</script>
<?php } ?>
<?php  if ($Serveur_Config['en_vote']){ ?>
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Voter pour <?= $Serveur_Config['Serveur_name']; ?></h3>
            </div>
            <div class="modal-body">
                <p>Vous pouvez voter pour nous sur notre page RPGParadize, chaque vote vous rapportera 1 <?= $Serveur_Config['Serveur_money']; ?>.</p>
                <p>Celui-ci pourra être échanger dans la boutique.</p>
                <p class="text-danger">Attention, vous ne pouvez voter qu'une fois par jour.</p>
                  <h4>Meilleurs voteurs :</h4>
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Pseudo</th>
                        <th>Nombre de vote</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        global $voteurs;
                        foreach ($voteurs as $voteur) {
                          echo '<tr>';
                          echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $voteur['pseudo'] . '&s=26">' . $voteur['pseudo'] .'</td>';
                          echo '<td>' . $voteur['votes'] .'</td>';
                          echo '</tr>';
                        }
                       ?>
                    </tbody>
                  </table>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary vote_b"><span class="vote_btext"><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>vote" class="no" style="text-decoration: none;color: white;">Voter !</a></span></button>
            </div>
        </div>
    </div>
</div>
<script>

$(".vote").click(function(){
  $("#myModal").modal('show');

  $.ajax({
     url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>vote/',
     <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){?>
       type : 'POST',
       data : 'pseudo=<?php echo $_SESSION['pseudo']; ?>',
     <?php }else { ?>
       type : 'GET',
     <?php } ?>
     dataType : 'html',
     success: function (data_rep) {
       if (data_rep != ""){
          $('.vote_btext').html(data_rep);
          $('.vote_b').attr('class', "btn btn-danger vote_b");
          $('.vote_b').attr('href', "");
       }
     }
  });
});
</script>
<?php } ?>