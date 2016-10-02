<?php
$_SESSION['pseudo'] = "t";
global $Serveur_Config;
global $controleur_def;
global $erreur_vote;
$css = $controleur_def->css;
$title = $controleur_def->title;
?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <meta lang="fr">
  <meta name="author" content="DiamondCMS, par GougDEV pour <?= $Serveur_Config['Serveur_name']; ?>">
  <link rel="icon" type="image/png" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/img/<?= $Serveur_Config['favicon']; ?>.png" />

  <?php
  if (!empty($title)){?>
    <meta title="<?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?>">
    <title><?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?></title>
  <?php }else{?>
    <title><?= $Serveur_Config['Serveur_name']; ?></title>
  <?php } ?>
  <link rel="stylesheet" type="text/css" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/sources.css"/>

  <!-- Polices -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">

  <script src="https://use.fontawesome.com/0a203004bc.js"></script>
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <!--<script src="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.min.js"></script>-->
  <script src="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.0.min.js"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" media="screen">
  <!--<link href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/statics.php" rel="stylesheet" type="text/css" media="all" />-->
  <?php
    if (!empty($css)){?>
      <link rel="stylesheet" type="text/css" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/<?= $css; ?>.css"/>
    <?php } ?>
    <?php if (!empty($erreur_vote)){?>
      <script type="text/javascript">
      $(document).ready(function(){
        $("#erreur_vote_modal").modal('show');
        return true;
      });
      </script>
    <?php } ?>
</head>
<style>
body {
  /*font-family: 'Source Sans Pro', sans-serif;*/
  font-size: 15px;
  font-family: 'Raleway', sans-serif;
}

h1, h2, h3, h4, h5{
  font-family: 'Bree Serif';
}
</style>
<body>
  <?php if (!empty($_SESSION['pseudo'])){ ?>
  <div id="myModal" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Voter pour <?= $Serveur_Config['Serveur_name']; ?></h4>
              </div>
              <div class="modal-body">
                  <p>Vous pouvez voter pour nous sur notre page RPGParadize, chaque vote vous rapportera 1 <?= $Serveur_Config['Serveur_money']; ?>.</p>
                  <p>Celui-ci pourras être échanger dans la boutique.</p>
                  <h5 class="text-danger">Attention, vous ne pouvez voter qu'une fois par jour.</h5>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                  <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>vote"><button type="button" class="btn btn-primary">Voter !</button>
              </div>
          </div>
      </div>
  </div>
<?php  }else { ?>
  <div id="myModal" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Erreur</h4>
              </div>
              <div class="modal-body">
                  <h5 class="text-danger">Vous devez être connecté pour voter !</h5>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
              </div>
          </div>
      </div>
  </div>
<?php  } ?>
  <div id="nav_bar_co">
    <?php
      if (!empty($_SESSION['pseudo'])){?>
        <p style="text-align: right;">Bienvenue <?= $_SESSION['pseudo']; ?>, <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>compte/"><i class="fa fa-home" aria-hidden="true"></i> Mon compte  </a></p>
      <?php  }else { ?>
        <p style="text-align: right;">Vous n'êtes pas connecté(e), <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>inscription"><i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a> ou <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>inscription"><i class="fa fa-sign-in" aria-hidden="true"></i>
          Inscrivez-vous ! </a></p>
      <?php }
      if(!empty($erreur_vote)){?>
        <div id="erreur_vote_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Erreur</h4>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-danger">Merci, mais vous ne pouvez voter qu'une fois par jour !</h5>
                    </div>
                    <div class="modal-footer">
                        <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><button type="button" class="btn btn-default">Fermer</button></a>
                    </div>
                </div>
            </div>
        </div>
      <?php } ?>
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
  </div>
  <div id="header-wrapper">
    <div id="header_menu" class="container">
      <div id="logo">
        <h1><a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><?= $Serveur_Config['Serveur_name']; ?></a></h1>
      </div>
      <div id="menu">
        <ul>
          <li class="current_page_item acc arr"><a href="http://<?= $Serveur_Config['host']; ?><?= WEBROOT ?>" accesskey="1" title="">Accueil</a></li>
          <li><a href="http://<?= $Serveur_Config['host']; ?><?=WEBROOT; ?>forum" accesskey="2" title="">Forum</a></li>
          <li><a href="#" accesskey="3" title="">Boutique</a></li>
          <li><a href="#" accesskey="4" title="">Serveur</a></li>
          <li><a href="#" accesskey="3" title="" class="vote">Voter</a></li>
          <li><a href="#" accesskey="5" title="" class="jouer">Jouer !</a></li>
        </ul>
      </div>
    </div>
  </div>
  <script>
  $(".vote").click(function(){
    $("#myModal").modal('show');
  });
  $(".jouer").click(function(){
    $("#modalJouer").modal('show');
  });
  </script>
  <div id="bg">
