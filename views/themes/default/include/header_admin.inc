<?php
  global $Serveur_Config, $controleur_def, $erreur_vote, $erreur_inscription, $erreur_connexion, $param, $notifyadmin, $notify;
  $css = $controleur_def->css;
  $title = $controleur_def->title;
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta lang="fr">
    <meta name="author" content="DiamondCMS, par GougDEV pour <?= $Serveur_Config['Serveur_name']; ?>">
    <link rel="icon" type="image/png" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/img/<?= $Serveur_Config['favicon']; ?>.png" />

    <!-- Title -->
    <?php if (!empty($title)){?>
    <meta title="Admin <?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?>">
    <title>Admin <?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?></title>
    <?php }else{?>
    <title>Admin <?= $Serveur_Config['Serveur_name']; ?></title>
    <?php } ?>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/bootstrap.min.css"/>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" media="screen">-->
    <?php if (!empty($css)){?>
    <link rel="stylesheet" type="text/css" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/<?= $css; ?>.css"/>
    <?php } ?>


    <!-- MetisMenu CSS -->
    <link href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/admin/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/admin/sources.css"/>
    <!-- Morris Charts CSS -->
    <link href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/admin/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/admin/font-awesome.css" rel="stylesheet" type="text/css">
    <!-- Polices -->
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">

    <script src='//cdn.tinymce.com/4/tinymce.min.js'></script>
    
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php
    if($jsfolder = opendir(ROOT . 'js/plugins/load')){
        $count = 0;
        while(false !== ($plugin = readdir($jsfolder))){
        if($plugin != '.' && $plugin != '..' && $plugin != 'plugins_load.js' && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
            if (substr($plugin, -9) != '.theme.js' || (substr($plugin, -15) == '.admin.theme.js' && str_replace(".theme.js", "", $plugin) == $Serveur_Config['theme'])){
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
</head>

<body>
<script>
    $(document).ready(function () {
        <?php if (!empty($controleur_def->getErrors())){ ?>
            $("#c_def_error").modal('show');
        <?php } ?>
    		$("textarea").ghostDown();
    });
    <?php if (!empty($notify)){ 
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
                timeout: 12000
            });
        });
  <?php } } ?>
  </script>

    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>admin">Panel D'administration de DiamondCMS</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                    <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !empty($notifyadmin)){ 
                        $i = 0;
                        foreach ($notifyadmin as $n) { 
                            if ($i < 5) { ?>
                            <li>
                                <a>
                                    <div>
                                        <p style="margin-top: 0; margin-bottom: 0;">
                                            <strong><?php echo $n['title']; ?></strong>

                                            <?php if ($n['type'] == 1){ //CONTACT ?>
                                                <span class="pull-right text-muted">Contact</span><br>
                                            <?php }else if ($n['type'] == 2) { //SUPPORT ?>
                                                <span class="pull-right text-muted">Support</span><br>
                                            <?php }else if ($n['type'] == 3) { //ERROR ?>
                                                <span class="pull-right text-muted">Erreur</span><br>
                                            <?php }else { ?>
                                                <span class="pull-right text-muted">Autre</span><br>
                                            <?php } ?>
                                            
                                            <?php echo $n['content']; ?>
                                        </p>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                                            <?php $i++; } }  } ?>
                        
                        <li>
                            <a class="text-center" href="#">
                                <strong>Voir toutes les alertes</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                <!-- /.dropdown -->
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/"><i class="fa fa-user fa-fw"></i> Profil</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>"><i class="fa fa-arrow-left fa-fw"></i> Retour au site</a></li>
                        <li><a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/deconnexion/"><i class="fa fa-sign-out fa-fw"></i> Déconnexion</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <center><img class="img-rounded" src="http://api.diamondcms.fr/face.php?id=<?php echo $Serveur_Config['id_cms']; ?>&u=<?php echo $_SESSION['pseudo']; ?>&s=100"></center>
                            <h3 class="text-center">Goug3</h3>
                        </li>
                        <li>
                            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/"><i class="fa fa-dashboard fa-fw"></i> Tableau de Bord</a>
                        </li>
                        <li>
                            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/errors/"><i class="fa fa-warning fa-fw"></i> Erreurs levées</a>
                        </li>
                        <li>
                            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/notify/"><i class="fa fa-quote-right" aria-hidden="true"></i> Notifications </a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="flot.html">Flot Charts</a>
                                </li>
                                <li>
                                    <a href="morris.html">Morris.js Charts</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="tables.html"><i class="fa fa-table fa-fw"></i> Tables</a>
                        </li>
                        <li>
                            <a href="forms.html"><i class="fa fa-edit fa-fw"></i> Forms</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> UI Elements<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="panels-wells.html">Panels and Wells</a>
                                </li>
                                <li>
                                    <a href="buttons.html">Buttons</a>
                                </li>
                                <li>
                                    <a href="notifications.html">Notifications</a>
                                </li>
                                <li>
                                    <a href="typography.html">Typography</a>
                                </li>
                                <li>
                                    <a href="icons.html"> Icons</a>
                                </li>
                                <li>
                                    <a href="grid.html">Grid</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Multi-Level Dropdown<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Second Level Item</a>
                                </li>
                                <li>
                                    <a href="#">Second Level Item</a>
                                </li>
                                <li>
                                    <a href="#">Third Level <span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                    </ul>
                                    <!-- /.nav-third-level -->
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-files-o fa-fw"></i> Sample Pages<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="blank.html">Blank Page</a>
                                </li>
                                <li>
                                    <a href="login.html">Login Page</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>"><i class="fa fa-arrow-left fa-fw"></i> Retour au site</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
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
                        <button type="button" class="btn btn-default err_controleur_end" id="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>erreur/seen/" href="<?php echo WEBROOT;?>">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    