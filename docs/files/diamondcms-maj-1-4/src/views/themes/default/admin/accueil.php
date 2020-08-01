<?php global $nb_coms, $errors, $nb_tickets, $errors_content, $Serveur_Config, $all_addons, $servers, $n_serveurs, $themes, $config, $nb_ventes, $outdated, $version, $bc;  ?>
<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Tableau de Bord</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $nb_coms; ?></div>
                                    <div>Commentaires sur le forum</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/forum">
                            <div class="panel-footer">
                                <span class="pull-left">Voir plus...</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-shopping-cart fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?= $nb_ventes; ?></div>
                                    <div>Ventes</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/boutique/tasks">
                            <div class="panel-footer">
                                <span class="pull-left">Voir plus...</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-question-circle fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $nb_tickets; ?></div>
                                    <div>Tickets de Support</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>support/">
                            <div class="panel-footer">
                                <span class="pull-left">Voir plus...</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                    <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $errors; ?></div>
                                    <div>Erreurs levées</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/errors/">
                            <div class="panel-footer">
                                <span class="pull-left">Voir plus...</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12" id="broadcaster" data-version="<?= DCMS_INT_VERSION; ?>" data-link="https://aldric-l.github.io/DiamondCMS/broadcast.json">		
				</div>
                <div class="col-lg-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Status du CMS
                        </div>
                        <div class="panel-body">
                            <p><strong>Numéro d'identification du CMS :</strong> <?php echo $Serveur_Config["id_cms"]; ?><br />
                            <strong>Version du CMS :</strong> <?php echo DCMS_VERSION; ?><br />
                            <strong>Installé le : </strong><?php echo $Serveur_Config["date_install"]; ?> <br>
                            <span id="maj" data-link="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/check-maj/">
                            <?php if ($outdated){ ?>
                                DiamondCMS n'est pas à jour. RDV <a href="https://aldric-l.github.io/DiamondCMS/">ici pour vérifier les nouvelles versions.</a>
                            <?php }else { ?>
                                DiamondCMS est à jour
                            <?php } ?>
                            </span>
                            </p>
                            <?php if ($Serveur_Config['mtnc'] == "false"){ ?>
                                <a id="mtnc" data="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/mtnc/" class="btn btn-default btn-block mtnc">Démarrer une maitenance</a>
                            <?php }else { ?>
                                <a id="mtnc" data="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/mtnc/" class="btn btn-danger btn-block mtnc">Arrêter la maitenance en cours</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Thèmes installés et compatibles
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php foreach($themes as $t){ ?>
                                <a href="#" class="list-group-item">
                                   "<?php echo $t['name']; ?>" par <?php echo $t['author']; ?>
                                    <span class="pull-right text-muted small">
                                        <?php if ($t['enabled']){ ?>
                                            <button 
                                                data="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/theme/<?php echo $t['name']; ?>" 
                                                type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="modify_theme btn btn-danger btn-sm"
                                                <?php if (sizeof($themes) == 1){ ?> disabled <?php } ?>>Désactiver
                                            </button>
                                        <?php }else { ?>
                                            <button 
                                                data="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/theme/<?php echo $t['name']; ?>" 
                                                type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="modify_theme btn btn-success btn-sm">Activer
                                            </button>
                                        <?php } ?>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Addons installés
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php foreach($all_addons as $addon){ ?>
                                <a href="#" class="list-group-item">
                                     <?php echo $addon[0]; ?>
                                     <span class="pull-right text-muted small">
                                        <?php if (!$addon[1]){ ?>
                                            <button 
                                                data="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/addon/<?php echo $addon[0]; ?>" 
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="modify_addon btn btn-danger btn-sm">Désactiver
                                            </button>
                                        <?php }else { ?>
                                            <button 
                                                data="<?php echo $config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/accueil/addon/<?php echo $addon[0]; ?>" 
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="modify_addon btn btn-success btn-sm">Activer
                                            </button>
                                        <?php } ?>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                            <?php if (defined("DServerLink") && DServerLink){ ?>
                                <hr>
                                <?php for ($i=1; $i <= $n_serveurs; $i++){ ?>
                                <div class="request_depend">
                                <p> <span class="" id="serveur_name_<?php echo $i; ?>"></span> - <span id="etat_serveur_<?php echo $i; ?>"></span></p>
                                    
                                </div>
                                <?php } ?>
                                <p style="display:none;" id="infos-servers" data-link="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>" data-nb="<?php echo $n_serveurs; ?>"></p>
                                <h3 id="loader" style="display: block;" class="text-center bree-serif"><img src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/ajax-loader.gif" alt="loading" /> Chargement en cours...</h5>
                            <?php } ?>
                            
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Erreurs levées par le systeme
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php foreach($errors_content as $error){ ?>
                                <a href="#" class="list-group-item">
                                    <?php if ($error['0'] == "332 "){?>
                                        <i class="fa fa-lock fa-fw"></i>
                                    <?php }else if ($error[0] == "311 "){ ?>
                                        <i class="fa fa-ban fa-fw"></i>
                                    <?php }else if ($error[0] == "121 "){ ?>
                                        <i class="fa fa-ban fa-cogs"></i>
                                    <?php }else { ?>
                                        <i class="fa fa-warning fa-fw"></i>
                                    <?php } ?>
                                     Code d'erreur n° <?php echo $error['0']; ?>
                                    <span class="pull-right text-muted small"><em>le <?php echo $error['1']; ?></em>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/errors/" class="btn btn-default btn-block">Voir toutes les erreurs</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
    </div>
    <!-- /#wrapper -->