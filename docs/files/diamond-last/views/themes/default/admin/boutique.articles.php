<?php global $cats, $config, $serveurs;  ?>
<div class="container-fluid">
    <h1 class="h3 mb-0 text-gray-800">Boutique - Gestion des articles</h1>
    <p class="mb-4">DiamondCMS est livré avec une boutique qu'il convient de paramètrer : ici vous pouvez ajouter des articles et en définir le prix.
    <br><strong>Pour accèder à la documentation : <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Boutique">Cliquez-ici</a></strong></p>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-custom">Ajouter un article</h6>
                    </div>
                        <div class="card-body" class="">
                            <?php if (empty($cats)){ ?>
                            <p style="text-align: center;"><em>Impossible d'ajouter un article tant qu'aucune catégorie n'a été créée ! <br>Vous pouvez créer une catégorie <a href="<?= LINK; ?>admin/boutique/config/"> ici.</a></em></p>
                            <?php }else { ?>
                            <form action"" id="new_article" method="POST" enctype="multipart/form-data" >
                                <div class="form-group">
                                    <label>Nom de l'article :</label>
                                    <input class="form-control" type="text" name="name" id="name" data-neededForValidation="true">
                                </div>
                                <div class="form-group">
                                    <label>Description de l'article :</label>
                                    <textarea class="form-control content" name="description" data-neededForValidation="true" cols="30" rows="10"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Catégorie :</label>
                                    <select class="form-control" name="cat" id="cat" data-neededForValidation="true">
                                        <?php if (!empty($cats)) {
                                            foreach($cats as $c){ ?>
                                                <option value="<?= $c['id']; ?>"><?= $c['name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Prix de l'article en <?= $config['Serveur_money']; ?>s</label>
                                    <input type="number" name="prix" id="prix" min="0" step="1" class="form-control" data-neededForValidation="true"/>
                                </div>
                                <div class="form-group">
                                    <label>Image associée (Format carré nécessaire) :</label>
                                    <input type="button" id="dic_launcher" data-whereisdic="<?php echo LINK . "views/themes/" . $Serveur_Config['theme'] . "/JS/plugins/listener/" ;?>" 
                                            data-wherearefiles="<?php echo LINK . "API/boutique/get/imgAvailable/" ;?>"
                                            data-imgWidth="1200" data-imgHeight="1200"
                                            data-imgformat="square"
                                            class="btn btn-sm btn-custom"
                                            data-neededForValidation="true" />
                                    <!--<input type="file" class="form-control-file" placeholder="file" name="img" id="img">-->
                                </div>
                                <hr>
                                <p style="text-align: center;">
                                    <em>DiamondCMS vous permet d'associer à vos articles des tâches à réaliser. <br>Celles-ci peuvent être manuelles (un colis à envoyer) ou automatiques (une commande à exécuter sur le site internet, ou sur un serveur de jeu avec l'addon DiamondServerLink).</em>
                                    <br><br>
                                    <button class="btn btn-default add-task" data-ismanual="true" data-ismod="false">Ajouter une tâche manuelle</button>
                                    <button 
                                    class="btn btn-default add-task" data-ismanual="false" data-ismod="false">Ajouter une tâche automatique</button>
                                </p>
                                <div id="tasks">
                                </div>
                                <input id="nb_auto_tasks" type="hidden" name="nb_auto_tasks" value="0">
                                <input id="nb_man_tasks" type="hidden" name="nb_man_tasks" value="0">
                                    <p style="text-align: right">
                                    <!--<button type="submit" class="send_new_article btn btn-success btn-md">Envoyer</button>-->
                                    <button class="btn btn-custom ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                            data-module="boutique/" data-verbe="set" data-func="addArticle" 
                                            data-tosend="#new_article" data-reload="true" data-useform="true"
                                            data-needAll="false">Enregistrer</button></p>
                            </form>
                            <div id="new-task-auto" class="new-task-auto" style="display: none;">
                                    <hr>
                                    <p><strong>Nouvelle tâche automatique à exécuter :</strong>
                                    <button type="button" class="close arrow-delete" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </p>
                                    <div class="form-group" style="margin-bottom: 1em;">
                                        <label class="form-check-label" >
                                            Choisir la cible
                                        </label>
                                        <select  
                                        class="form-control server new-task-serverSelect"
                                        data-originalName="server">
                                            <option value="-1">Site WEB (Commande interne)</option>
                                            <?php if (defined("DServerLink") && DServerLink) { foreach ($serveurs as $s){ if ($s['enabled'] == "true") { ?>
                                                <option value="<?= $s['id']; ?>"><?= $s['name'];?> (Serveur <?= $s['game']; ?>)</option>
                                            <?php } } } ?>
                                        </select>   
                                    </div>
                                    <div class="form-check" style="margin-bottom: 1em;">
                                        <input class="form-check-input mustbe_connected new-task-mustbeconnectedCheckbox" type="checkbox"  data-originalName="mustbe_connected">
                                        <label class="form-check-label">
                                            Le joueur doit être connecté au serveur pour recevoir son dû
                                        </label>
                                    </div>
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Commande à éxecuter : (max: 255 caractères, insérer {PLAYER} pour utiliser le pseudo du joueur, {USER_ID} pour son identifiant sur le site)</label>
                                        <input class="form-control cmd new-task-cmdImput" value="null" data-originalName="cmd" type="text">
                                    </div>
                            </div>
                            <div id="new-task-man" class="new-task-man" style="display: none;">
                                    <hr>
                                    <p><strong>Nouvelle tâche manuelle à exécuter :</strong>
                                    <button type="button" class="close arrow-delete" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </p>
                                        <div class="form-group" style="margin-bottom: 1em;">
                                            <label>Descriptif de l'action à réaliser (max: 255 caractères)</label>
                                            <input class="form-control man_cmd new-task-manImput" value="null" data-originalName="man_cmd" type="text">
                                        </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
        </div>
        <div class="col-lg-6">
            <?php foreach ($cats as $c){ ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-custom">Articles enregistrés dans <?= $c['name']; ?></h6>
                </div>
                <div class="card-body" class="">
                    <?php if (empty($c['articles'])){?>
                        <p><em>Aucun article enregistré dans cette catégorie.</em></p>
                    <?php }else {
                        foreach ($c['articles'] as $a){ ?>
                                            <a id="line_<?php echo $a['id']; ?>" data="<?php echo $a['id']; ?>" class="list-group-item">
                                                <strong><?php echo $a['name']; ?></strong> (<?= $a['ventes']; ?> vente(s))
                                                <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                                    <button 
                                                    id="<?php echo $a['id']; ?>" type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                    class="btn btn-warning btn-sm"
                                                    data-toggle="modal" data-target="#modal_article_<?php echo $a['id']; ?>">Modifier
                                                    </button>
                                                    <button class="btn btn-danger btn-sm ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                                        data-module="boutique/" data-verbe="set" data-func="delArticle" 
                                                        data-tosend="id_article=<?php echo $a['id']; ?>" data-reload="true"
                                                        style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" >
                                                        Supprimer</button>
                                                </span>
                                            </a>
                    <?php } } ?>
                </div>
            </div>
        <?php } ?>
        </div>
</div>
<?php if (!empty($cats)){
    foreach ($cats as $c){
        foreach ($c['articles'] as $a){ ?>
        
        <div id="modal_article_<?php echo $a['id']; ?>" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" role="document">
                    <div class="modal-header">
                        <h5 class="modal-title"><strong>Modification de <?php echo $a['name']; ?></strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="edit_<?php echo $a['id']; ?>">
                            <input type="hidden" name="id_article" value="<?php echo $a['id']; ?>">
                            <div class="container">
                                <div class="row">
                                    <div class="col-5">
                                        <img class="img-rounded img-responsive" src="<?php echo $a['link'];?>">
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group">
                                            <label for="pseudo" class="col-form-label">Nom</label>
                                            <input class="form-control" type="text" name="name" id="<?php echo $a['id']; ?>_name" value="<?php echo $a['name']; ?>">
                                        </div>
                                        <div class="form-group ">
                                            <label>Prix de l'article en <?= $config['Serveur_money']; ?>s</label>
                                            <input name="prix" type="number" value="<?php echo $a['prix']; ?>" id="<?php echo $a['id']; ?>_prix" min="0" step="1" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Catégorie </label>
                                            <select name="cat" class="form-control" id="<?php echo $a['id']; ?>_cat">
                                                <?php if (!empty($cats)) {
                                                    foreach($cats as $c){ ?>
                                                        <option value="<?= $c['id']; ?>" <?php if ($c['id'] == $a['cat']) { ?> selected <?php } ?>><?= $c['name']; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                            <p class="help-block text-danger"></p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="email" class="col-form-label">Description</label>
                                            <textarea class="form-control content" name="description" id="description"  id="<?php echo $a['id']; ?>_description" cols="30" rows="10"><?php echo $a['description']; ?></textarea>
                                            <!--<input class="form-control" value="<?php echo $a['description']; ?>" name="desc" type="text" id="<?php echo $a['id']; ?>_desc">-->
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h3 id="saved-tasks-<?= $a['id']; ?>" data-nb="<?= sizeof($a['cmd']); ?>">Tâches déjà enregistrées</h3>
                                <?php if (empty($a['cmd'])){ ?>
                                    <p><em>Aucune tâche n'est associée à cet article.</em></p>
                                <?php }else { ?>
                                    <br>
                                    <?php foreach ($a['cmd'] as $key => $cmd){  ?>
                                            <div id="task-<?=$cmd['id']; ?>">
                                        <?php if ($cmd['is_manual'] == 0 && is_array($serveurs) && $cmd['server'] != -1){
                                            foreach ($serveurs as $s){ 
                                                if ($s['id'] == $cmd['server']) { ?>
                                                <h5><strong>Tâche automatique sur le serveur <?= $s['name']; ?> (<?= $s['game']; ?>)</strong> : </h5>
                                                <?php if($cmd['connexion_needed']){ ?>
                                                    <p> Connexion du joueur <strong>obligatoire</strong> pour récupérer son dû.</p>
                                                <?php }else { ?>
                                                    <p>Connexion du joueur <strong>non-</strong>obligatoire pour récupérer son dû.</p>
                                                <?php } ?>
                                                <p <?php if($s['enabled'] == true){ ?> style="margin-bottom: 0;" <?php } ?>><strong>Commande à exécuter : </strong><?= $cmd['cmd'];?></p>
                                                <?php if($s['enabled'] == false){ ?>
                                                    <p style="margin-bottom: 0;"><strong><em><span style="color:red">Cette commande ne sera pas éxecutée car le serveur n'est pas activé.</span></em></strong></p>
                                                <?php } ?>
                                                <p style="text-align: right">
                                                    <button class="btn btn-sm btn-danger btn-sm ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                                        data-module="boutique/" data-verbe="set" data-func="delTask" 
                                                        data-tosend="id_task=<?=$cmd['id']; ?>" data-reload="true">
                                                        Supprimer la tâche</button>
                                                </p>
                                                <?php if ($key !== sizeof($a['cmd'])-1){ ?>
                                                <hr>
                                                <?php } ?>
                                            <?php }
                                            }//End foreach serveurs ?>
                                         <?php } else if($cmd['is_manual'] == 0 && (!is_array($serveurs) || $cmd['server'] == -1)) { ?>
                                            <?php if ($cmd['server'] != -1){ ?>
                                                <h5><strong>Ancienne tache automatique :</strong></h5>
                                                <p style="margin-bottom: 0;color: red;"><em>Diamond-ServerLink n'étant plus installé il est conseillé de supprimer ces tâches.</em></p>
                                            <?php }else{ ?>
                                                <h5><strong>Tâches automatiques à exécuter sur le serveur WEB :</strong></h5>
                                            <?php } ?>
                                            <p style="margin-bottom: 0;"><strong>Commande à exécuter : </strong><?= $cmd['cmd'];?></p>   
                                            <p style="text-align: right">
                                                    <button class="btn btn-sm btn-danger btn-sm ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                                        data-module="boutique/" data-verbe="set" data-func="delTask" 
                                                        data-tosend="id_task=<?=$cmd['id']; ?>" data-reload="true">
                                                        Supprimer la tâche</button>
                                            </p>
                                         <?php } else { ?>
                                            <h5><strong>Tâche manuelle :</strong></h5>
                                            <p style="margin-bottom: 0;"><strong>Descriptif : </strong><?= $cmd['cmd'];?></p>   
                                            <p style="text-align: right">
                                                    <button class="btn btn-sm btn-danger btn-sm ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                                        data-module="boutique/" data-verbe="set" data-func="delTask" 
                                                        data-tosend="id_task=<?=$cmd['id']; ?>" data-reload="true">
                                                        Supprimer la tâche</button>
                                                </p>
                                            
                                        <?php } ?>
                                        <?php if ($key !== sizeof($a['cmd'])-1){ ?>
                                                <hr>
                                                <?php } ?>
                                            </div>
                                    <?php } ?>
                                <?php } ?>
                                <br>
                                <hr>
                                <h3 id="add-tasks-<?= $a['id']; ?>" data-nb="<?= sizeof($a['cmd']); ?>">Ajouter de nouvelles tâches</h3>
                                <p style="text-align: center;">
                                    <button class="btn btn-default add-task" data-ismanual="true" data-ismod="true" data-id_mod="<?php echo $a['id']; ?>" data-id="<?php echo $a['id']; ?>">Ajouter une tâche manuelle</button>
                                    <button 
                                    class="btn btn-default add-task" data-ismanual="false" data-ismod="true" data-id_mod="<?php echo $a['id']; ?>" data-id="<?php echo $a['id']; ?>">Ajouter une tâche automatique</button>

                                </p>
                                <div id="tasks-modif-<?php echo $a['id']; ?>">
                                </div>
                                <input id="nb_auto_tasks-modif-<?php echo $a['id']; ?>" type="hidden" name="nb_auto_tasks" value="0">
                                <input id="nb_man_tasks-modif-<?php echo $a['id']; ?>" type="hidden" name="nb_man_tasks" value="0">
                                <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                            </div>
                        </form>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                         class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button class="btn btn-success ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                            data-module="boutique/" data-verbe="set" data-func="modArticle" 
                            data-tosend="#edit_<?php echo $a['id']; ?>" data-useform="true" data-reload="true">
                            Sauvegarder</button>
                        <!--<button type="submit" id="<?php echo $a['id']; ?>"
                         class="save_article_modifs btn btn-success"
                         data="<?= LINK; ?>admin/boutique/xhr/modify_article/<?php echo $a['id']; ?>">Sauvegarder</button>-->
                        <button class="btn btn-danger ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                            data-module="boutique/" data-verbe="set" data-func="delArticle" 
                            data-tosend="id_article=<?php echo $a['id']; ?>" data-reload="true">
                            Supprimer</button>
                    </div>
                </div>
            </div>
        </div>    
        
        
        <?php }
    }
}