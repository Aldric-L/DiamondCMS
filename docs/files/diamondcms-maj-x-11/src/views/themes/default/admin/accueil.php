<?php global $nb_coms, $errors, $nb_tickets, $errors_content, $Serveur_Config, $all_addons, $servers, $n_serveurs, $themes, $config, $nb_ventes, $outdated, $version, $bc, $mce_error; ?>
<div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Tableau de Bord</h1>
            </div>
            <!-- /.row -->
            <div class="row">
                <!-- Pending Requests Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Commentaires sur le forum</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nb_coms; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Ventes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nb_ventes; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Tickets de support</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nb_tickets; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Erreurs levées</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $errors; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12" id="broadcaster" data-version="<?= DCMS_INT_VERSION; ?>" data-link="https://aldric-l.github.io/DiamondCMS/broadcast.json">		
				</div>
                <div class="col-lg-4">
                    <div class="card shadow lg-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-custom">Status du CMS</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Numéro d'identification du CMS :</strong> <?php echo $Serveur_Config["id_cms"]; ?><br />
                            <strong>Version du CMS :</strong> <?php echo DCMS_VERSION; ?><br />
                            <strong>Installé le : </strong><?php echo $Serveur_Config["date_install"]; ?> <br>
                            <span id="maj" data-link="<?= LINK; ?>API/admin/GET/maj/">
                            <?php if ($outdated){ ?>
                                <strong style="color: red;">DiamondCMS n'est pas à jour.</strong> RDV <a href="https://aldric-l.github.io/DiamondCMS/">ici pour vérifier les nouvelles versions.</a>
                            <?php }else { ?>
                                DiamondCMS est à jour
                            <?php } ?>
                            </span>
                            <br>
                            <span id="MCE">
                            <?php if ($mce_error){ ?>
                                <strong><span style="color: red;">Attention ! </span>TinyMCE n'est pas installé correctement. Le fonctionnement des editeurs de texte est perturbé. <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Editeur-de-texte-TinyMCE">Mode d'emploi</a></strong>
                            <?php }else { ?>
                                <strong>Editeurs de texte : </strong> Fonctionnement normal
                            <?php } ?>
                            </span>
                            </p>
                            <?php if (!$Serveur_Config['mtnc']){ ?>
                                <a data-api="<?= LINK; ?>api/" data-module="admin/" data-verbe="set" data-func="mtnc" data-reload="true" class="btn btn-light btn-block mtnc ajax-simpleSend">Démarrer une maitenance</a>
                            <?php }else { ?>
                                <a data-api="<?= LINK; ?>api/" data-module="admin/" data-verbe="set" data-func="mtnc" data-reload="true" class="btn btn-danger btn-block ajax-simpleSend">Arrêter la maitenance en cours</a>
                            <?php } ?>
                        </div>
                    </div><br>
                    <div class="card shadow lg-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-custom">Thèmes installés et compatibles</h6>
                        </div>
                        <div class="card-body">
                        <div class="list-group">
                                <?php foreach($themes as $t){ ?>
                                <a href="#" class="list-group-item">
                                   "<?php echo $t['name']; ?>" par <?php echo $t['author']; ?>
                                    <span class="pull-right text-muted small">
                                        <?php if (!$t['enabled']){ ?>
                                            <button 
                                                data-api="<?= LINK; ?>api/" data-module="admin/" data-verbe="set" data-func="theme" data-tosend="theme=<?php echo $t['name']; ?>" data-reload="true"
                                                type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="btn btn-success ajax-simpleSend btn-sm">Activer
                                            </button>
                                        <?php } ?>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow lg-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-custom">Addons installés</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach($controleur_def->getAddons() as $addon){ ?>
                                <a href="#" class="list-group-item">
                                     <?php echo (array_key_exists("name", $addon)) ? $addon["name"] : $addon["name_raw"]; ?>
                                     <span class="pull-right text-muted small">
                                        <?php if ($addon['enabled']){ ?>
                                            <button 
                                                data-api="<?= LINK; ?>api/" data-module="admin/" data-verbe="set" data-func="addonstate" data-tosend="addon=<?php echo $addon['name_raw']; ?>" data-reload="true"
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="ajax-simpleSend btn btn-danger btn-sm">Désactiver
                                            </button>
                                        <?php }else { ?>
                                            <button 
                                                data-api="<?= LINK; ?>api/" data-module="admin/" data-verbe="set" data-func="addonstate" data-tosend="addon=<?php echo $addon['name_raw']; ?>" data-reload="true"
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="ajax-simpleSend btn btn-success btn-sm">Activer
                                            </button>
                                        <?php } ?>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                            <hr>
                            <div class="addons-iframes">
                            <?php foreach($addav=$controleur_def->getAddons() as $key => $addon){ if ($addon['enabled'] && $addon['addAdminIframe']){ ?>
                                <div id="addon-iframe-<?php echo $addon['name_raw']; ?>" class="addon-iframe" data-name="<?php echo $addon['name']; ?>" data-src="<?php echo LINK . $addon['name_raw'] . "/admin_iframe"; ?>"></div>
                                <?php if ($key != sizeof($addav)-1){ ?>
                                    <hr>
                                <?php } ?>
                            <?php } } ?>
                                <div id="iframe-error" style="display: none;">
                                    <p class="text-center"><img class="img-responsive" style="max-width: 85%;"src="<?php echo LINK . "views/uploads/img/logo.png"; ?>" alt="Logo de DiamondCMS"></p>
                                    <p class="mt-1 text-center"><strong>Erreur :</strong> Impossible de charger la ressource demandée de l'addon <span class="addon-error-name"></span>.</hp>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-4 -->
                <div class="col-lg-4">
                    <div class="card shadow lg-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-custom"><i class="fa fa-bell fa-fw"></i> Erreurs levées par le systeme</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach($errors_content as $error){ ?>
                                <a href="#" class="list-group-item">
                                        <i class="fa <?php echo $error['icon']; ?> fa-fw"></i>
                                     Code d'erreur <?php echo $error['display_code']; ?>
                                    <span class="pull-right text-muted small"><em>le <?php echo $error['date']; ?></em>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                            <br>
                            <a href="<?= LINK; ?>admin/errors/" class="btn btn-light btn-block">Voir toutes les erreurs</a>
                        </div>
                    </div>
                </div>
                <br>
                <!-- /.col-lg-4 -->
        </div>
    </div>
    <!-- /container fluid -->

