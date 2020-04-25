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
                            <form action"" method="POST" enctype="multipart/form-data" >
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
                                <?php if (!defined("DServerLink") || !DServerLink) { ?>
                                    <p><strong style="color: red;">Pour agir sur les serveurs de jeu, installez Diamond-ServerLink.</strong></p>
                                <?php }else { 
                                    foreach ($serveurs as $s){ ?>
                                        <p><strong>Serveur <?= $s['name']; ?> (<?= $s['game']; ?>)</strong> :</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="mustbe_connected" name="<?= $s['id']; ?>_en_serveur" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                            <label class="form-check-label" for="mustbe_connected">
                                                Exécuter une commande sur ce serveur
                                            </label>
                                            <?php if ($s['enabled'] != "true") { ?>
                                                <small class="form-text text-muted">Activez le lien avec ce serveur pour éxecuter une commande sur ce dernier.</small>
                                            <?php } ?>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="mustbe_connected" name="<?= $s['id']; ?>_mustbe_connected" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                            <label class="form-check-label" for="mustbe_connected">
                                                Le joueur doit être connecté au serveur pour recevoir son dû
                                            </label>
                                        </div>
                                        <div class="row control-group">
                                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                                <label>Commande à éxecuter : (max: 255 caractères)</label>
                                                <input class="form-control" value="null" type="text" name="<?= $s['id']; ?>_cmd" id="cmd" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                <p class="help-block text-danger"></p>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php }
                                } ?>
                                    <p style="text-align: right"><button type="submit" class="send_new_cat btn btn-success btn-md">Envoyer</button></p>
                            </form>
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
                                                <strong><?php echo $a['name']; ?></strong>
                                                <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                                    <button 
                                                    id="<?php echo $a['id']; ?>" type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                                    class="open_modal btn btn-warning btn-sm">Modifier
                                                    </button>
                                                    <button 
                                                    data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/boutique/xhr/delete_article/<?php echo $a['id']; ?>" 
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
                        <br>
                        <h4>Commandes enregistrées :</h4>
                        <?php if (empty($a['cmd']) || empty($serveurs)){ ?>
                            <p><em>Aucune commande n'est à éxecuter sur un serveur de jeu.</em></p>
                        <?php }else { ?>
                            <ul>
                                <?php foreach ($a['cmd'] as $cmd){ 
                                    foreach ($serveurs as $s){ 
                                        if ($s['id'] == $cmd['server']) { ?>
                                        <li>
                                        <em>"<?= $cmd['cmd']; ?>"</em> sur le serveur <?= $s['name']; ?> (<?= $s['game']; ?>)
                                        <?php if($cmd['connexion_needed']){ ?>
                                            <br> Connexion du joueur obligatoire pour récupérer son dû.
                                        <?php }else { ?>
                                            <br> Connexion du joueur non-obligatoire pour récupérer son dû.
                                        <?php } ?>
                                        <?php if($s['enabled'] == "false"){ ?>
                                            <br><strong><span style="color:red">Cette commande ne sera pas éxecutée car le serveur n'est pas activé.</span></strong>
                                        <?php } ?>
                                        </li>
                                    <?php }
                                } ?>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        <hr>
                        <h3>Modifier l'article : </h3>
                            <form action"" method="POST" >
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nom de l'article :</label>
                                        <input class="form-control" value="<?php echo $a['name']; ?>" type="text" id="<?php echo $a['id']; ?>_name">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Description de l'article :</label>
                                        <input class="form-control" value="<?php echo $a['description']; ?>" type="text" id="<?php echo $a['id']; ?>_desc">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Catégorie :</label>
                                    <select class="form-control" id="<?php echo $a['id']; ?>_cat">
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
                                        <input type="number" value="<?php echo $a['prix']; ?>" id="<?php echo $a['id']; ?>_prix" min="0" step="1" class="form-control" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <hr>
                                <?php 
                                if (!defined("DServerLink") || !DServerLink) { $i = "false"; ?>
                                    <p><strong style="color: red;">Pour agir sur les serveurs de jeu, installez Diamond-ServerLink.</strong></p>
                                <?php }else { 
                                    $i = 0;
                                    foreach ($serveurs as $s){ 
                                        $i = $i+1;
                                        $display = false;
                                        foreach ($a['cmd'] as $cmd){ 
                                            if (intval($cmd['server']) == intval($s['id'])){
                                                $display = true; ?>
                                                    <p><strong>Serveur <?= $s['name']; ?> (<?= $s['game']; ?>)</strong> :</p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="<?php echo $a['id']; ?>_<?= $s['id']; ?>_en_serveur" checked <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                        <label class="form-check-label" for="mustbe_connected">
                                                            Exécuter une commande sur ce serveur
                                                        </label>
                                                        <?php if ($s['enabled'] != "true") { ?>
                                                            <small class="form-text text-muted">Activez le lien avec ce serveur pour éxecuter une commande sur ce dernier.</small>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="<?php echo $a['id']; ?>_<?= $s['id']; ?>_mustbe_connected" <?php if ($cmd['connexion_needed']) { ?> checked <?php } ?> <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                        <label class="form-check-label" for="mustbe_connected">
                                                            Le joueur doit être connecté au serveur pour recevoir son dû
                                                        </label>
                                                    </div>
                                                    <div class="row control-group">
                                                        <div class="form-group col-xs-12 floating-label-form-group controls">
                                                            <label>Commande à éxecuter : (max: 255 caractères)</label>
                                                            <input class="form-control" value="<?= $cmd['cmd']; ?>" type="text" id="<?php echo $a['id']; ?>_<?= $s['id']; ?>_cmd" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                            <p class="help-block text-danger"></p>
                                                        </div>
                                                    </div>
                                                <?php } // end if
                                        } // end foreach cmd
                                        if (!$display){ ?>
                                            <p><strong>Serveur <?= $s['name']; ?> (<?= $s['game']; ?>)</strong> :</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="<?php echo $a['id']; ?>_<?= $s['id']; ?>_en_serveur" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                <label class="form-check-label" for="mustbe_connected">
                                                    Exécuter une commande sur ce serveur
                                                </label>
                                                <?php if ($s['enabled'] != "true") { ?>
                                                    <small class="form-text text-muted">Activez le lien avec ce serveur pour éxecuter une commande sur ce dernier.</small>
                                                <?php } ?>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="<?php echo $a['id']; ?>_<?= $s['id']; ?>_mustbe_connected" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                <label class="form-check-label" for="mustbe_connected">
                                                    Le joueur doit être connecté au serveur pour recevoir son dû
                                                </label>
                                            </div>
                                            <div class="row control-group">
                                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                                    <label>Commande à éxecuter : (max: 255 caractères)</label>
                                                    <input class="form-control" value="null" type="text" id="<?php echo $a['id']; ?>_<?= $s['id']; ?>_cmd" <?php if ($s['enabled'] != "true") { ?> disabled <?php } ?>>
                                                    <p class="help-block text-danger"></p>
                                                </div>
                                            </div>
                                        <?php } // end display
                                     ?>
                                    <?php } // end foreach
                                } // foreach serveurs ?>
                                <input type="hidden" id="<?php echo $a['id']; ?>_nb_servers" value="<?= $i; ?>">
                            </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                         class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="submit" id="<?php echo $a['id']; ?>"
                         class="save_article_modifs btn btn-success"
                         data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/boutique/xhr/modify_article/<?php echo $a['id']; ?>">Sauvegarder</button>
                        <button 
                            data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/boutique/xhr/delete_article/<?php echo $a['id']; ?>" 
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