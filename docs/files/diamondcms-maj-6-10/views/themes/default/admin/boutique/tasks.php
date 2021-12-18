<?php global $tasks, $commandes, $config; $mt = $controleur_def->getManualTasks(); ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Boutique - Tâches et commandes récentes</h1>
            <h5>DiamondCMS est livré avec une boutique : sur celle-ci les utilisateurs peuvent acheter vos articles avec un monnaie virtuelle. Une tâche est une action à exécuter pour que l'acheteur reçoive son dû. (ex: une commande sur un serveur)</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <?php if (sizeof($mt) != 0){ ?>
                <div class="alert alert-danger" role="alert"><strong>Attention !</strong> <?= sizeof($mt); ?> tâche(s) manuelle(s) sont en attente. Les clients attendent que celles-ci soient terminées afin de pouvoir récuperer leur dû !</div>
            <?php } ?>
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Commandes récentes
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($commandes)){ ?>
                                <p>Aucune commande n'a été trouvée.</p>
                            <?php }else {
                                foreach ($commandes as $c){ ?>
                                    <a id="line_<?php echo $c['id']; ?>" data="<?php echo $c['id']; ?>" class="list-group-item">
                                        <strong>Commande n°<?php echo $c['id']; ?></strong> (uuid: <?php echo $c['uuid']; ?>) : le <?php echo $c['date']; ?> par <?php echo $c['user']; ?> (Article : <?php echo $c['article']; ?>, <?php echo $c['price']; ?> <?php echo $config['Serveur_money']; ?>(s) )
                                         - Statut : <?php if ($c['success'] != true && $c['success'] != 1 ){ ?>
                                            <strong><span style="color: red;">En cours</span></strong>
                                            <?php }else { ?>
                                            <strong><span style="color: green;">Terminée !</span></strong>
                                            <?php } ?>
                                        <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                            <button 
                                                class="btn btn-sm"
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                data-toggle="modal" data-target="#reçu-<?php echo $c['id']; ?>">
                                                Afficher le reçu client
                                            </button>    
                                        </span>
                                    </a>
                                <?php }
                            } ?>
                        </div>
                    </div>
        </div>
        <div class="col-lg-12">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Tâches enregistrées
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($tasks)){ ?>
                                <p>Aucune tâche n'a pour le moment été enregistrée.</p>
                            <?php }else { 
                                foreach ($tasks as $t){ 
                                    if (!$t['cmd']['is_manual']){ ?>
                                    <a id="line_<?php echo $t['id']; ?>" data="<?php echo $t['id']; ?>" class="list-group-item">
                                        <strong>Tache n°<?php echo $t['id']; ?></strong> associée à la commande n°<?php echo $t['id_commande']; ?> (de <?php echo $controleur_def->getPseudo($t['commande']['id_user']); ?>) - 
                                        <?php if ($t['done'] != true && $t['done'] != 1 ){ ?>
                                            <strong>Statut : <span style="color: orange;">En cours</span></strong>
                                            <?php }else if($t['stopped']) { ?>
                                            <strong>Statut : <span style="color: red;">Suspendue</span></strong>
                                            <?php }else { ?>
                                            <strong>Statut : <span style="color: green;">Terminée</span></strong>
                                            <?php } ?>
                                            - Tâche automatique à éxecuter sur le serveur <?php echo $t['cmd']['server_name']; ?>
                                        <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                            <?php if ($t['done'] != true && $t['done'] != 1 ){ ?>
                                            <button 
                                                data-link="<?= LINK; ?>admin/boutique/tasks/xhr/stop/<?php echo $t['id']; ?>" 
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="stop-task btn btn-danger btn-sm">
                                                Suspendre
                                            </button>                                            
                                            <?php }else { ?>
                                            <button 
                                                class="btn btn-sm"
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                disabled>
                                                Suspendre
                                            </button>    
                                            <?php } ?>
                                        </span>
                                    </a>
                                <?php }else { ?>
                                    <a id="line_<?php echo $t['id']; ?>" data="<?php echo $t['id']; ?>" class="list-group-item">
                                        <strong>Tache n°<?php echo $t['id']; ?></strong> de la commande n°<?php echo $t['id_commande']; ?> (de <?php echo $controleur_def->getPseudo($t['commande']['id_user']); ?>) - 
                                        <?php if ($t['done'] != true && $t['done'] != 1 ){ ?>
                                            <strong>Statut : <span style="color: orange;">En cours</span></strong>
                                            <?php }else if($t['stopped']) { ?>
                                            <strong>Statut : <span style="color: red;">Suspendue</span></strong>
                                            <?php }else { ?>
                                            <strong>Statut : <span style="color: green;">Terminée</span></strong>
                                            <?php } ?>
                                        - Tâche <strong>manuelle</strong>
                                        <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                            <button 
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="btn btn-info btn-sm"
                                                data-toggle="modal" data-target="#tman-<?php echo $t['id']; ?>">
                                                Informations
                                            </button>
                                            <?php if ($t['done'] != true && $t['done'] != 1 ){ ?>
                                            
                                            <button 
                                                data-link="<?= LINK; ?>admin/boutique/tasks/xhr/done/<?php echo $t['id']; ?>" 
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="stop-task btn btn-success btn-sm">
                                                Signaler comme terminée
                                            </button>
                                            <button 
                                                data-link="<?= LINK; ?>admin/boutique/tasks/xhr/stop/<?php echo $t['id']; ?>" 
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                class="stop-task btn btn-danger btn-sm">
                                                Suspendre
                                            </button>                                            
                                            <?php }else { ?>
                                            <button 
                                                class="btn btn-sm"
                                                style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                disabled>
                                                Suspendre
                                            </button>    
                                            <?php } ?>
                                        </span>
                                    </a>
                                <?php }
                                }
                             } ?>
                        </div>
                    </div>
        </div>
</div>
<?php foreach ($commandes as $c){ ?>
<div class="modal fade" id="reçu-<?php echo $c['id']; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Aperçu du reçu client de la commande (<?php echo $c['uuid']; ?>) de <?php echo $controleur_def->getPseudo($c['id_user']); ?></h4>
      </div>
      <div class="modal-body">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe  src="<?= LINK; ?>admin/boutique/iframe/<?php echo $c['uuid']; ?>" frameborder="0"></iframe>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php foreach ($tasks as $t){ 
    if ($t['cmd']['is_manual']){ ?>
    <div class="modal fade" id="tman-<?php echo $t['id']; ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Information sur la tâche n°<?= $t['id']; ?></h4>
        </div>
        <div class="modal-body">
            <p>Cette tâche est associée à la commande n°<?php echo $t['id_commande']; ?> (de <?php echo $controleur_def->getPseudo($t['commande']['id_user']); ?>).<br>
            <strong>Elle consiste en "<em><?php echo $t['cmd']['cmd']; ?></em>".</strong><br>
            Elle a été initiée le <?= $t['date_send']; ?>
            <br><br>
            <em>Cette tâche est une tâche manuelle. Elle nécessite donc l'intervention d'un administrateur. Le joueur, qui a acheté un article, ne peut pas récupérer son dû tant qu'un administrateur n'a pas effectué les tâches manuelles associées à sa commande.</em></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
        </div>
    </div>
    </div>
<?php } } ?>