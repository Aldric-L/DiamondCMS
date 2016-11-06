<?php
//$_SESSION['pseudo'] = "t";
global $Serveur_Config;
global $controleur_def;
global $erreur_vote;
global $erreur_inscription;
global $erreur_connexion;
$css = $controleur_def->css;
$title = $controleur_def->title;
?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <meta lang="fr">
  <meta name="author" content="DiamondCMS, par GougDEV pour <?= $Serveur_Config['Serveur_name']; ?>">
  <link rel="icon" type="image/png" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/img/<?= $Serveur_Config['favicon']; ?>.png" />

  <!-- Title -->
  <?php if (!empty($title)){?>
  <meta title="<?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?>">
  <title><?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?></title>
  <?php }else{?>
  <title><?= $Serveur_Config['Serveur_name']; ?></title>
  <?php } ?>

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/sources.css"/>
  <link rel="stylesheet" type="text/css" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/bootstrap.css"/>
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" media="screen">-->
  <?php if (!empty($css)){?>
  <link rel="stylesheet" type="text/css" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/<?= $css; ?>.css"/>
  <?php } ?>

  <!-- Polices -->
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">

  <!-- LIB JavaScript -->
  <script src="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <!--<script src="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>-->
  <script src="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.min.js"></script>

  <!-- JavaScript : Erreurs Modal, et Google analytics -->
  <script type="text/javascript">
  $(document).ready(function(){
      $("#serveur_box").hide();
  });
  </script>
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
  if (!empty($erreur_connexion)){ ?>
    <script type="text/javascript">
      $(document).ready(function(){
        $("#erreur_connexion_modal").modal('show');
        return true;
      });
    </script><?php }
  if (!empty($erreur_inscription)){ ?>
    <script type="text/javascript">
      $(document).ready(function(){
        $("#erreur_inscription_modal").modal('show');
        return true;
      });
    </script><?php }
  if (!empty($erreur_vote)){
    if ($erreur_vote == "pconnecter"){?>
      <script type="text/javascript">
        $(document).ready(function(){
          $("#erreur_vote_connexion").modal('show');
          return true;
        });
      </script>
    <?php }else { ?>
      <script type="text/javascript">
        $(document).ready(function(){
          $("#erreur_vote_modal").modal('show');
          return true;
        });
      </script>
    <?php }
  } ?>
  <!-- END JS -->
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
  <div id="myModal" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h3 class="modal-title">Voter pour <?= $Serveur_Config['Serveur_name']; ?></h3>
              </div>
              <div class="modal-body">
                  <p>Vous pouvez voter pour nous sur notre page RPGParadize, chaque vote vous rapportera 1 <?= $Serveur_Config['Serveur_money']; ?>.</p>
                  <p>Celui-ci pourras être échanger dans la boutique.</p>
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
                            echo '<td><img width=26 height=26 src="http://localhost:8080/API_diamond/face.php?id=1&u='. $voteur['pseudo'] . '&s=26">' . $voteur['pseudo'] .'</td>';
                            echo '<td>' . $voteur['votes'] .'</td>';
                            echo '</tr>';
                          }
                         ?>
                      </tbody>
                    </table>
                </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                  <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>vote"><button type="button" class="btn btn-primary">Voter !</button></a>
              </div>
          </div>
      </div>
  </div>
<?php  if (!empty($erreur_vote) && $erreur_vote == "pconnecter"){ ?>
  <div id="erreur_vote_connexion" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h3 class="modal-title">Erreur de connexion !</h3>
              </div>
              <div class="modal-body">
                  <h4 class="text-danger">Vous devez être connecté pour voter !</h4>
                  <p>Connectez-vous en <a href="http://<?= $_SERVER['HTTP_HOST'] . WEBROOT; ?>connexion">cliquant ici !</a></p>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
              </div>
          </div>
      </div>
  </div>
<?php  } ?>
  <!--<div id="nav_bar_co">-->
    <?php
      /*if (!empty($Serveur_Config['yt']) || !empty($Serveur_Config['fb']) || !empty($Serveur_Config['tw'])){ ?>
          <div id="content"><p><span style="float-left;"> Nos résaux sociaux : <?php if (!empty($Serveur_Config['yt'])){ ?> <a href="<?= $Serveur_Config['yt']; ?>"><i class="fa fa-youtube" aria-hidden="true"></i> Youtube </a><?php }?>
            <?php if (!empty($Serveur_Config['fb'])){ ?><a href="<?= $Serveur_Config['fb']; ?>"><i class="fa fa-facebook-official" aria-hidden="true"></i> Facebook </a><?php }?>
            <?php if (!empty($Serveur_Config['tw'])){ ?><a href="<?= $Serveur_Config['tw']; ?>"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter </a><?php } ?></span> <?php
      }
      if (!empty($_SESSION['pseudo'])){?>
      <span style="float:right;">Bienvenue <?= $_SESSION['pseudo']; ?>, <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>compte/"><i class="fa fa-home" aria-hidden="true"></i> Mon compte  </a></span></p></div></div>
      <?php  }else { ?>
        <span style="float: right;">Vous n'êtes pas connecté(e), <a href="#" class="connexion"><i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a> ou <a class="inscription" href="#"><i class="fa fa-sign-in" aria-hidden="true"></i>
          Inscrivez-vous ! </a></span></p></div></div>
          <div id="inscription_modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h3 class="modal-title text-center">Inscription !</h3>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="inscription">
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
                            <label for="mp_inscription" class="col-form-label">Password</label>
                            <input class="form-control" type="mp_inscription" id="mp_inscription" name="mp_inscription">
                            <small id="mpHelp" class="form-text text-muted">Il doit faire plus de 6 caractères.</small>
                          </div>
                          <div class="form-group">
                            <label for="mp2_inscription" class="col-form-label">Password confirmation</label>
                            <input class="form-control" type="mp2_inscription" id="mp2_inscription" name="mp2_inscription">
                            <small id="mp2Help" class="form-text text-muted">Répetez-le.</small>
                          </div>
                          <div class="form-check">
                            <label class="form-check-label">
                              <input type="checkbox" class="form-check-input" name="news">   S'abonner à la news-letter
                            </label>
                          </div>
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
                          <h3 class="modal-title text-center">Connexion !</h3>
                      </div>
                      <div class="modal-body">
                        <p class="text-right"><em>Pas encore de compte : <a href="http://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?> inscription"><i class="fa fa-sign-in" aria-hidden="true"></i>inscrivez-vous !</a></em></p>
                        <form action="connexion" method="post">
                          <div class="form-group row">
                            <label for="pseudo_connexion" class="col-xs-2 col-form-label">Votre pseudo :</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="text" name="pseudo_connexion" id="pseudo_connexion">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label for="mp_connexion" class="col-xs-2 col-form-label">Password</label>
                            <div class="col-xs-10">
                              <input class="form-control" type="mp_connexion" id="mp_connexion" name="mp_connexion">
                            </div>
                          </div>
                          <div class="form-check">
                            <label class="souvenir">
                              <input class="form-check-input" type="checkbox" value="" id="souvenir" name="souvenir">
                              Se souvenir de moi
                            </label>
                          </div>
                          <button type="submit" class="btn btn-success align-right center-block acc">Valider et se connecter</button>
                        </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                      </div>
                  </div>
              </div>
          </div>
      <?php }*/
      if(!empty($erreur_vote) && $erreur_vote == "advoter"){?>
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
                        <a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><button type="button" class="btn btn-default">Fermer</button></a>
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
  <!--<div id="header-wrapper">
    <div id="header_menu" class="container">
      <div id="logo">
        <h1><a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>"><?= $Serveur_Config['Serveur_name']; ?></a></h1>
      </div>
      <div id="menu">
        <ul>
          <li class="current_page_item"><a href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT ?>" accesskey="1" title="">Accueil</a></li>
          <li><a href="http://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>forum" accesskey="2" title="">Forum</a></li>
          <li><a href="#" accesskey="3" title="">Boutique</a></li>
          <li><a href="#" accesskey="4" title="" class="serveur">Serveur</a></li>
          <li><a href="#" accesskey="6" title="" class="">Connexion</a>/<a href="#" accesskey="6" title="" class="">Inscription</a></li>
        </ul>
      </div>
    </div>
  </div>-->
  <nav class="navbar navbar-inverse">
          <div class="container-fluid">
          <div class="row">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header page-scroll">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                      <span class="sr-only">Toggle navigation</span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                  </button>
              </div>
              <div class="col-sm-2"></DIV>
              <div class="col-sm-4">
              <a class="navbar-brand" href="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT ?>"><?php echo $Serveur_Config['Serveur_name']; ?></a>
            </div>

              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse" id="navbar-collapse">
                  <ul class="nav navbar-nav">
                    <li><a href="http://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>forum" accesskey="2" title="">Forum</a></li>
                    <li><a href="#">Boutique</a></li>
                    <li><a href="#">Connexion</a></li>
                    <li><a href="#">Inscription</a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Serveur <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="#">BanList</a></li>
                        <li><a href="http://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>CGU">CGU</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#" class="vote">Voter</a></li>
                        <li><a href="#" class="jouer">Jouer !</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                      </ul>
                    </li>
                  </ul>
              </div>
              <!-- /.navbar-collapse -->
          </div>
          <!-- /.container-fluid -->
      </nav>
<!--<div id="serveur_box">
  <li><a href="#" accesskey="3" title="" class="vote">Voter</a></li>
  <li><a href="#" accesskey="5" title="" class="jouer">Jouer !</a></li>
</div>-->
  <script>
  $(".serveur").click(function(){
    $("#serveur_box").show();
  });
  $(".vote").click(function(){
    $("#myModal").modal('show');
  });
  $(".jouer").click(function(){
    $("#modalJouer").modal('show');
  });
  $(".inscription").click(function(){
    $("#inscription_modal").modal('show');
  });
  $(".connexion").click(function(){
    $("#connexion_modal").modal('show');
  });
  //http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>inscription
  </script>
  <div id="bg">
