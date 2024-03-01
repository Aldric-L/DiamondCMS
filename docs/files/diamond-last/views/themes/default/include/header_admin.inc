<?php
  global $Serveur_Config, $controleur_def, $erreur_vote, $erreur_inscription, $erreur_connexion, $param, $notifyadmin, $notify, $conf_mce;
  $css = $controleur_def->getCSS();
  $title = $controleur_def->getTitle();
  $pages = $controleur_def->getPages();
  $mt = $controleur_def->getManualTasks();
  $contacts = $controleur_def->getUnseenContacts();
  $n_notify=0;
  if (is_array($notifyadmin)){
    foreach ($notifyadmin as $n) {
      if (!$n['view']){
          $n_notify++;
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<?php // NE JAMAIS MODIFIER LES ATTRIBUTS DU HEAD SOUS PEINE DE PERTE DU FONCTIONNEMENT D'AJAX SIMPLESEND ?>
<head data-baselink="<?php echo LINK; ?>" id="diamondhead">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta lang="fr">
    <meta name="author" content="DiamondCMS, pour <?= $Serveur_Config['Serveur_name']; ?>">
    <link rel="icon" href="<?php echo Manager::makeGetImageLink($Serveur_Config['favicon']); ?>" />

    <!-- Title -->
    <?php if (!empty($title)){?>
    <meta title="Admin <?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?>">
    <title>Admin <?= $Serveur_Config['Serveur_name']; ?> - <?= $title; ?></title>
    <?php }else{?>
    <title>Admin <?= $Serveur_Config['Serveur_name']; ?></title>
    <?php } ?>

    <?php if (isset($controleur_def->is_iframer) && !$controleur_def->is_iframer){?>
    <!-- CSS -->
    <?php if (!empty($css)){?>
    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/<?= $css; ?>.css"/>
    <?php } ?>
    <?php if ($conf_mce['editor']['enable'] == true){ ?>
        <script src='https://cdn.tiny.cloud/1/<?= $conf_mce['editor']['key']; ?>/tinymce/5/tinymce.min.js'></script>
    <?php } ?>

    <?php $controleur_def->pluginloader(true, true); ?>

    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/admin/metisMenu.min.css"/>
    <link rel="stylesheet" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/animate.css">
    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/colors.css"/>
    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/fonts.css"/>
    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/admin/sources.css"/>

    <link href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/css/admin/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php } ?>
</head>

<script>
    $(document).ready(function () {
        <?php if (!empty($controleur_def->getErrors())){ ?>
            $("#c_def_error").modal('show');
        <?php } ?>
    });
    <?php if (!empty($notify)){ 
      foreach ($notify as $n) { ?>
        $(document).ready(function () {
            Snarl.addNotification({
                title: "<?php echo $n['title']; ?>",
                text: "<?php echo $n['content']; ?>",
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
                timeout: 12000
            });
        });
  <?php } } ?>
  <?php if (!empty($notifyadmin)){ 
      foreach ($notifyadmin as $n) { if ($n['view'] != 1) {?>
        $(document).ready(function () {
            Snarl.addNotification({
                title: "<?php echo $n['title']; ?>",
                text: "<?php echo $n['content']; ?>",
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
                timeout: 12000
            });
        });
  <?php } } } ?>
  </script>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper" <?php if ($controleur_def->is_iframer){ ?> style="background-color: #fff;"<?php } ?>>


        <?php if (!$controleur_def->is_iframer){ ?>
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-custom sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand brand-img d-flex align-items-center justify-content-center" style="padding-top: 4rem;" href="<?= LINK; ?>admin/">
                <div class="sidebar-brand-icon" >
                    <!--<i class="fas fa-laugh-wink"></i>-->
                    <img class="rounded-circle" width="85px" hight="85px" src="<?= LINK; ?>getprofileimg/<?php echo $_SESSION['pseudo']; ?>/300">

                </div>
            </a>

            <a class="sidebar-brand brand-text d-flex align-items-center justify-content-center" style="padding-top: 2.75rem;padding-bottom: 2.5rem;height:100px;" href="<?= LINK; ?>admin/">
                <div class="sidebar-brand-text">
                Panel Admin<br>DiamondCMS</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="<?= LINK; ?>admin/">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau de bord</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Administration
            </div>
            <!-- Nav Item - Config Collapse Menu -->
            <li class="nav-item">

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers"
                    aria-expanded="true" aria-controls="collapseUsers">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Communauté 
                        <?php if (sizeof($contacts) != 0){ ?>
                            <span style="color: red;!important"><i style="color: red !important;" class="fa fa-exclamation fa-fw" aria-hidden="true"></i></span>
                        <?php } ?>
                    </span>
                </a>
                <div id="collapseUsers" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <!--<h6 class="collapse-header">Custom Components:</h6>-->
                        <a class="collapse-item" href="<?= LINK; ?>admin/comptes/list/">Utilisateurs</a>
                        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() > 4){ ?>
                        <a class="collapse-item" href="<?= LINK; ?>admin/comptes/permissions/">Roles et accès</a>
                        <?php } ?>
                        <a class="collapse-item" href="<?= LINK; ?>admin/support">Support</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/contact">Contact 
                        <?php if (sizeof($contacts) != 0){ ?>
                            <span style="color: red;!important"><i style="color: red !important;" class="fa fa-exclamation fa-fw" aria-hidden="true"></i></span>
                        <?php } ?>
                        </a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/mails">Service mail</a>
                    </div>
                </div>
            </li>

            <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() > 4){ ?>
            <!-- Nav Item - Config Collapse Menu -->
            <li class="nav-item">

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig"
                    aria-expanded="true" aria-controls="collapseConfig">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Configuration générale</span>
                </a>
                <div id="collapseConfig" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <!--<h6 class="collapse-header">Custom Components:</h6>-->
                        <a class="collapse-item" href="<?= LINK; ?>admin/config/">Général et divers</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/faq/">Page FAQ</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/forum/">Forum</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/theme/">Gestion du thème</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/pages/">Gestion des pages</a>

                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <?php if (sizeof($mt) != 0){ ?>
                        <span>Boutique <span style="color: red;!important"><i style="color: red !important;" class="fa fa-warning fa-fw" aria-hidden="true"></i>  </span></span>
                    <?php }else { ?>
                    <span>Gestion de la boutique</span>
                    <?php } ?>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="<?= LINK; ?>admin/boutique/config/">Configuration générale</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/boutique/articles/">Gestion des articles</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/boutique/tasks/">Tâches et commandes
                        <?php if (sizeof($mt) != 0){ ?>
                                <span style="color: red;"><i class="fa fa-warning fa-fw" aria-hidden="true"></i>  </span>
                        <?php } ?>
                        </a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/boutique/paypal/">Service PayPal</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/boutique/dedipass/">Service Dedipass</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/boutique/statistiques/">Statistiques</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Config Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSystem"
                    aria-expanded="true" aria-controls="collapseSystem">
                    <i class="fas fa-fw fa-terminal"></i>
                    <span>Système</span>
                </a>
                <div id="collapseSystem" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">                        
                        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() > 4){ ?>
                        <a class="collapse-item" href="<?= LINK; ?>admin/console/">Console</a>
                        <?php } ?>
                        <a class="collapse-item" href="<?= LINK; ?>admin/errors/">Erreurs</a>
                        <a class="collapse-item" href="<?= LINK; ?>admin/notify/">Notifications</a>
                    </div>
                </div>
            </li>
            

            <?php } ?>
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Divers
            </div>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="<?= LINK; ?>admin/news/">
                    <i class="fas fa-fw fa-quote-right"></i>
                    <span>News</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= LINK; ?>admin/cloud/">
                    <i class="fas fa-fw fa-cloud"></i>
                    <span>DiamondCloud</span></a>
            </li>

            <?php $adminPages = $controleur_def->getAdminPages();
            if (is_array($adminPages) && !empty($adminPages)){
                foreach($adminPages as $aP){
                    if (is_array($aP)){
                        if (array_key_exists(2, $aP) && is_array($aP[2]) && !empty($aP[2]) && (!isset($aP[3]) || intval($aP[3]) < 1 || (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() >= intval($aP[3])))){ ?>
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#<?php echo clearString($aP[1], true, true, true); ?>"
                                aria-expanded="true" aria-controls="<?php echo clearString($aP[1], true, true, true); ?>">
                                <?php if (!empty($aP[0])){ ?>
                                    <i class="<?php echo $aP[0]; ?>"></i>
                                <?php } ?>
                                <span><?php echo (isset($aP[1]) && !empty($aP[1])) ? $aP[1] : "Page inconnue"; ?></span>
                            </a>
                            <div id="<?php echo clearString($aP[1], true, true, true); ?>" class="collapse" aria-labelledby="<?php echo clearString($aP[1], true, true, true); ?>" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                <?php foreach($aP[2] as $a){
                                        if (is_array($a)){ ?>
                                                <a class="collapse-item" href="<?php echo (isset($a[2]) && !empty($a[2])) ? str_replace("{LINK}", LINK, $a[2]) : LINK . "404"; ?>"><?php echo (isset($a[1]) && !empty($a[1])) ? $a[1] : "Page inconnue"; ?></a>
                                            <?php 
                                        }
                                    } ?>
                                </div>
                            </div>
                        </li>
                       <?php }else if(!isset($aP[3]) || intval($aP[3]) < 1 || (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() >= intval($aP[3]))) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo (isset($aP[2]) && !empty($aP[2])) ? str_replace("{LINK}", LINK, $aP[2]) : LINK . "404"; ?>">
                                    <?php if (!empty($aP[0]) && !is_array($aP[0])){ ?>
                                        <i class="<?php echo $aP[0]; ?>"></i>
                                    <?php } ?>
                                    <span><?php echo (isset($aP[1]) && !empty($aP[1])) ? $aP[1] : "Page inconnue"; ?></span></a>
                            </li>
                        <?php 
                        }
                    }
                }
            } ?>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Getback -->
            <li class="nav-item">
                <a class="nav-link" href="<?= LINK; ?>">
                    <i class="fas fa-fw fa-arrow-left"></i>
                    <span>Retour au site</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->
        <?php } ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" <?php if ($controleur_def->is_iframer){ ?> style="background-color: #fff;"<?php } ?>>

            <!-- Main Content -->
            <div id="content">


                <?php if (!$controleur_def->is_iframer){ ?>
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <?php if ($n_notify !=0){ ?>
                                    <!-- Counter - Alerts -->
                                    <span class="badge badge-danger badge-counter"><?php echo $n_notify; ?></span>
                                <?php }?>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Dernières notifications
                                </h6>
                                <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !empty($notifyadmin)){ 
                                    $i = 0;
                                    foreach ($notifyadmin as $n) { 
                                        if ($i < 5) { ?>
                                        <a class="dropdown-item d-flex align-items-center" href="<?php echo (isset($n['link']) ? $n['link'] : ""); ?>">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-custom">
                                                <?php if ($n['type'] == 1){ //CONTACT ?>
                                                    <i class="fas fa-comment text-white"></i>
                                                <?php }else if ($n['type'] == 2) { //SUPPORT ?>
                                                    <i class="far fa-question-circle text-white"></i>
                                                <?php }else if ($n['type'] == 3) { //ERROR ?>
                                                    <i class="fas fa-exclamation-circle text-white"></i>
                                                <?php }else if ($n['type'] == 4) { //FORUM ?>
                                                    <i class="fas fa-comments-o text-white"></i>
                                                <?php }else if ($n['type'] == 5) { //BOUTIQUE ?>
                                                    <i class="fas fa-shopping-cart text-white"></i>
                                                <?php }else { ?>
                                                    <i class="fas fa-quote-right text-white"></i>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500"><?php echo $n['date']; ?></div>
                                                <span class="font-weight-bold"><?php echo $n['title']; ?></span>
                                            </div>
                                        </a>
                                <?php $i++; } }  } ?>
                                <a class="dropdown-item text-center small text-gray-500" href="<?php echo LINK;?>admin/notify/">Voir toutes les notifications</a>
                            </div>
                        </li>

                        

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['pseudo']; ?></span>
                                <img class="img-profile rounded-circle" src="<?= LINK; ?>getprofileimg/<?php echo $_SESSION['pseudo']; ?>/150">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= LINK; ?>compte/">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= LINK; ?>">
                                    <i class="fas fa-arrow-left fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Retour au site
                                </a>
                                
                                <a class="dropdown-item ajax-simpleSend" href="#" data-api="<?= LINK; ?>api/" 
                                    data-module="comptes/" data-verbe="get" data-func="disconnect" data-reload="true">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Déconexion
                                </a>

                            </div>
                        </li>

                    </ul>
                </nav>
                <?php } ?>
                <!-- End of Topbar -->


                <?Php /*
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Blank Page</h1>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            */ ?>
<?php if (!empty($controleur_def->getErrors())){ ?>
<div id="c_def_error" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nous rencontrons une ou plusieurs erreurs !</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
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
            <a href="<?php echo WEBROOT;?>admin/"><button type="button" class="btn btn-custom">Revenir à l'accueil</button></a>
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
      </div>
  </div>
</div>
<?php } ?>