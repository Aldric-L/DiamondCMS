<?php global $nb_coms, $errors, $nb_tickets, $infos_cms, $errors_content, $Serveur_Config, $addons, $servers, $n_serveurs; ?>
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
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/">
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
                                    <div class="huge">0</div>
                                    <div>Ventes</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/">
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
                        <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/">
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
                <div class="col-lg-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Status du CMS
                        </div>
                        <div class="panel-body">
                            <?php if (isset($infos_cms) && !empty($infos_cms) && $infos_cms["allow"] == true) { ?>
                            <p><strong>Numéro d'identification du CMS :</strong> <?php echo $infos_cms["id_cms"]; ?><br />
                            <strong>Version du CMS :</strong> <?php echo $infos_cms["type_cms"]; ?><br :>
                            <strong>Enregistré le : </strong><?php echo $infos_cms["date_buy"]; ?> au près de Diamondcms.fr. <br />
                            <strong>Autorisé à utiliser le service : </strong><?php if ($infos_cms["allow"]){ echo '<span style="color: green;"><strong>Oui</strong></span>'; }else { echo "Non"; } ?><br />
                            <strong>Nombre de connexions aux serveurs de GougDev :</strong> <?php echo $infos_cms["actions"]; ?><br />
                            <strong>License delivrée pour l'url :</strong> <?php echo $infos_cms["url"]; ?></p>
                            <?php } else if (isset($infos_cms) && !empty($infos_cms) && $infos_cms["allow"] != true){ ?>
                            <p><strong><span style="color: red;"><strong>Erreur lors de l'installation du CMS</strong></span><br />
                            <strong>Merci de contacter DiamondCMS pour rétablir tous les services du CMS. La version en cours d'utilisation n'est pas une version officielle du CMS ou a été altérée.<br> Pour palier le problème, vous pouvez initier une réinstallation rapide du service.</strong></p>
                            <?php } else { ?>
                            <p><strong><span style="color: red;"><strong>Impossible de contacter l'API du CMS</strong></span><br />
                            <strong>Merci de contacter DiamondCMS pour rétablir tous les services du CMS</strong></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Addons installés
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php foreach($addons as $addon){ ?>
                                <a href="#" class="list-group-item">
                                     <?php echo $addon; ?>
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