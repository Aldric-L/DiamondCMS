<?php global $cats, $config, $serveurs;  ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Boutique - Gestion des articles</h1>
            <h5>DiamondCMS est livré avec une boutique qu'il convient de paramètrer : ici vous pouvez ajouter des articles et en définir le prix.</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Ajouter un article
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($cats)){ ?>
                            <p style="text-align: center;"><em>Impossible d'ajouter un article tant qu'aucune catégorie n'a été créée ! <br>Vous pouvez créer une catégorie <a href="<?= LINK; ?>admin/boutique/config/"> ici.</a></em></p>
                            <?php }else { ?>
                            <form action"" id="new_article" method="POST" enctype="multipart/form-data" >
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nom de l'article :</label>
                                        <input class="form-control" type="text" name="name" id="name">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Description de l'article :</label>
                                        <input class="form-control" type="text" name="desc" id="desc">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Catégorie :</label>
                                    <select class="form-control" name="cat" id="cat">
                                        <?php if (!empty($cats)) {
                                            foreach($cats as $c){ ?>
                                                <option value="<?= $c['id']; ?>"><?= $c['name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Prix de l'article en <?= $config['Serveur_money']; ?>s</label>
                                        <input type="number" name="prix" id="prix" min="0" step="1" class="form-control" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Image associée (Format carré nécessaire) :</label>
                                        <input type="file" class="form-control-file" placeholder="file" name="img" id="img">
                                    </div>
                                </div>
                                <hr>
                                <p style="text-align: center;">
                                    <em>DiamondCMS vous permet d'associer à vos articles des tâches à réaliser. <br>Celles-ci peuvent être manuelles (un colis à envoyer) ou automatiques (une commande à effectuer sur un serveur).</em>
                                    <br><br>
                                    <button class="btn btn-default add-manual-task">Ajouter une tâche manuelle</button>
                                    <button 
                                    <?php if (!defined("DServerLink") || !DServerLink) { ?> disabled <?php } ?>
                                    class="btn btn-default add-auto-task">Ajouter une tâche automatique sur un serveur</button>
                                    <?php if (!defined("DServerLink") || !DServerLink) { ?>
                                        <br><em>Vous devez disposer de Diamond-ServerLink pour créer des tâches automatiques</em>
                                    <?php } ?>
                                </p>
                                <hr>
                                <div id="tasks">
                                </div>
                                <input id="nb_auto_tasks" type="hidden" name="nb_auto_tasks" value="0">
                                <input id="nb_man_tasks" type="hidden" name="nb_man_tasks" value="0">
                                <div id="new-task-auto" style="display: none;">
                                        <p><strong>Nouvelle tâche automatique à exécuter :</strong></p>
                                        <div class="form-check" style="margin-bottom: 1em;">
                                            <label class="form-check-label" >
                                                Choisir le serveur sur lequel agir
                                            </label>
                                            <select  
                                            class="form-control server"
                                            data-originalName="server">
                                                <?php if (defined("DServerLink") && DServerLink) { foreach ($serveurs as $s){ if ($s['enabled'] == "true") { ?>
                                                    <option value="<?= $s['id']; ?>"><?= $s['name'];?> (<?= $s['game']; ?>)</option>
                                                <?php } } } ?>
                                            </select>                                            
                                            
                                            
                                        </div>
                                        <div class="form-check" style="margin-bottom: 1em;">
                                            <input class="form-check-input mustbe_connected" type="checkbox"  data-originalName="mustbe_connected">
                                            <label class="form-check-label">
                                                Le joueur doit être connecté au serveur pour recevoir son dû
                                            </label>
                                        </div>
                                        <div class="row control-group" style="margin-bottom: 1em;">
                                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                                <label>Commande à éxecuter : (max: 255 caractères, insérer {PLAYER} pour utiliser le pseudo du joueur)</label>
                                                <input class="form-control cmd" value="null" data-originalName="cmd" type="text">
                                            </div>
                                        </div>
                                        <hr>
                                </div>
                                <div id="new-task-man" style="display: none;">
                                        <p><strong>Nouvelle tâche manuelle à exécuter :</strong></p>
                                        <div class="row control-group">
                                            <div class="form-group col-xs-12 floating-label-form-group controls" style="margin-bottom: 1em;">
                                                <label>Descriptif de l'action à réaliser (max: 255 caractères)</label>
                                                <input class="form-control man_cmd" value="null" data-originalName="man_cmd" type="text">
                                            </div>
                                        </div>
                                        <hr>
                                </div>
                                    <p style="text-align: right"><button type="submit" class="send_new_article btn btn-success btn-md">Envoyer</button></p>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Articles enregistrés
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($cats)){ ?>
                                <p>Aucun article n'a pour le moment été enregistrée.</p>
                            <?php }else { 
                                foreach ($cats as $c){ ?>
                                    <h3> Articles de la catégorie <?= $c['name']; ?></h3>
                                    <?php 
                                    if (empty($c['articles'])){?>
                                        <p>Aucun article enregistré dans cette catégorie.</p>
                                    <?php }else {
                                        foreach ($c['articles'] as $a){ ?>
                                            <a id="line_<?php echo $a['id']; ?>" data="<?php echo $a['id']; ?>" class="list-group-item">
                                                <strong><?php echo $a['name']; ?></strong> (<?= $a['ventes']; ?> vente(s))
                                                <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                                    <button 
                                                    id="<?php echo $a['id']; ?>" type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                    class="open_modal btn btn-warning btn-sm">Modifier
                                                    </button>
                                                    <button 
                                                    data="<?= LINK; ?>admin/boutique/xhr/delete_article/<?php echo $a['id']; ?>" 
                                                    id="<?php echo $a['id']; ?>" type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                    class="delete_article btn btn-danger btn-sm">Supprimer
                                                    </button>
                                                </span>
                                            </a>
                                        <?php } ?>
                                        <br>
                                <?php } }
                             } ?>
                        </div>
                    </div>
        </div>
</div>
<?php if (!empty($cats)){
    foreach ($cats as $c){
        foreach ($c['articles'] as $a){ ?>
        
        <div id="modal_article_<?php echo $a['id']; ?>" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title"><?php echo $a['name']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"><img class="img-rounded" src="<?php echo $a['link'];?>" alt="<?php echo $a['name'];?>" /></p>
                        <h3><?php echo $a['name']; ?><small> au prix de <?php echo $a['prix']; ?> <?php echo $config['Serveur_money']; ?>s</small></h3>
                        <p><?php echo $a['description']; ?></p>
                        <hr>
                        <h3>Modifier l'article : </h3>
                            <form id="mod-form-<?php echo $a['id']; ?>" method="POST" >
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nom de l'article :</label>
                                        <input class="form-control" value="<?php echo $a['name']; ?>" name="name" type="text" id="<?php echo $a['id']; ?>_name">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Description de l'article :</label>
                                        <input class="form-control" value="<?php echo $a['description']; ?>" name="desc" type="text" id="<?php echo $a['id']; ?>_desc">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Catégorie :</label>
                                    <select name="cat" class="form-control" id="<?php echo $a['id']; ?>_cat">
                                        <?php if (!empty($cats)) {
                                            foreach($cats as $c){ ?>
                                                <option value="<?= $c['id']; ?>" <?php if ($c['id'] == $a['cat']) { ?> selected <?php } ?>><?= $c['name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Prix de l'article en <?= $config['Serveur_money']; ?>s</label>
                                        <input name="prix" type="number" value="<?php echo $a['prix']; ?>" id="<?php echo $a['id']; ?>_prix" min="0" step="1" class="form-control" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <hr>
                                <h4 id="saved-tasks-<?= $a['id']; ?>" data-nb="<?= sizeof($a['cmd']); ?>">Tâches enregistrées :</h4>
                                <?php if (empty($a['cmd'])){ ?>
                                    <p><em>Aucune tâche n'est associée à cet article.</em></p>
                                <?php }else { ?>
                                    <?php foreach ($a['cmd'] as $cmd){  ?>
                                            <div id="task-<?=$cmd['id']; ?>">
                                        <?php if (!$cmd['is_manual']){
                                            foreach ($serveurs as $s){ 
                                                if ($s['id'] == $cmd['server']) { ?>
                                                <p><strong>Tâche automatique sur le serveur <?= $s['name']; ?> (<?= $s['game']; ?>)</strong> : 
                                                </p>
                                                <?php if($cmd['connexion_needed']){ ?>
                                                    <p> Connexion du joueur obligatoire pour récupérer son dû.</p>
                                                <?php }else { ?>
                                                    <p>Connexion du joueur non-obligatoire pour récupérer son dû.</p>
                                                <?php } ?>
                                                <p <?php if($s['enabled'] == "true"){ ?> style="margin-bottom: 0;" <?php } ?>><strong>Commande à exécuter : </strong><?= $cmd['cmd'];?></p>
                                                <?php if($s['enabled'] == "false"){ ?>
                                                    <p style="margin-bottom: 0;"><strong><em><span style="color:red">Cette commande ne sera pas éxecutée car le serveur n'est pas activé.</span></em></strong></p>
                                                <?php } ?>
                                                <p style="text-align: right"><button data-id="<?=$cmd['id']; ?>" data-idarticle="<?= $a['id']; ?>"
                                            data-link="<?= LINK; ?>admin/boutique/xhr/delete_task/<?=$cmd['id']; ?>" class="del-saved-task btn btn-sm btn-danger">Supprimer la tâche</button></p>
                                                <hr>
                                            <?php }
                                            }//End foreach serveurs
                                         } else { ?>
                                            <p><strong>Tâche manuelle :</strong></p>
                                            <p style="margin-bottom: 0;"><strong>Descriptif : </strong><?= $cmd['cmd'];?></p>   
                                            <p style="text-align: right"><button data-id="<?=$cmd['id']; ?>" data-idarticle="<?= $a['id']; ?>"
                                            data-link="<?= LINK; ?>admin/boutique/xhr/delete_task/<?=$cmd['id']; ?>" class="del-saved-task btn btn-sm btn-danger">Supprimer la tâche</button></p>
                                            <hr>
                                        <?php } ?>
                                            </div>
                                    <?php } ?>
                                <?php } ?>
                                <p style="text-align: center;">
                                    <em>DiamondCMS vous permet d'associer à vos articles des tâches à réaliser. <br>Celles-ci peuvent être manuelles ou automatiques.</em>
                                    <br><br>
                                    <button class="btn btn-default add-manual-task-modif" data-id="<?php echo $a['id']; ?>">Ajouter une tâche manuelle</button>
                                    <button 
                                    <?php if (!defined("DServerLink") || !DServerLink) { ?> disabled <?php } ?>
                                    class="btn btn-default add-auto-task-modif" data-id="<?php echo $a['id']; ?>">Ajouter une tâche automatique sur un serveur</button>
                                    <?php if (!defined("DServerLink") || !DServerLink) { ?>
                                        <br><em>Vous devez disposer de Diamond-ServerLink pour créer des tâches automatiques</em>
                                    <?php } ?>
                                </p>
                                <hr>
                                <div id="tasks-modif-<?php echo $a['id']; ?>">
                                </div>
                                <input id="nb_auto_tasks-modif-<?php echo $a['id']; ?>" type="hidden" name="nb_auto_tasks" value="0">
                                <input id="nb_man_tasks-modif-<?php echo $a['id']; ?>" type="hidden" name="nb_man_tasks" value="0">
                                <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                            </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                         class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="submit" id="<?php echo $a['id']; ?>"
                         class="save_article_modifs btn btn-success"
                         data="<?= LINK; ?>admin/boutique/xhr/modify_article/<?php echo $a['id']; ?>">Sauvegarder</button>
                        <button 
                            data="<?= LINK; ?>admin/boutique/xhr/delete_article/<?php echo $a['id']; ?>" 
                            id="<?php echo $a['id']; ?>" type="submit" 
                            class="delete_article close_modal btn btn-danger">Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>    
        
        
        <?php }
    }
}