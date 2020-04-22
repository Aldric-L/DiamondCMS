<?php global $cats, $config;  ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS - Forum</h1>
            <h5>DiamondCMS est livré avec un forum. Toutefois, il est possible de le désactiver, de le paramètrer et de le remplacer un forum externe.</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Modifier la page
                        </div>
                        <div class="panel-body" class="">
                            <?php if ($config['en_forum']){ ?>
                                <p><strong>Pour désactiver le forum par défaut, cliquez-ici : </strong><button data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/forum/enable" type="submit" class="enable btn btn-danger btn-md">Désactiver</button></p>
                                <hr>
                                <p><em>Pour modifier ces réglages, désactivez dabord le forum par défaut.</em></p>
                            <?php }else { ?>
                                <p><strong>Pour activer le forum par défaut, cliquez-ici : </strong><button data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/forum/enable" type="submit" class="enable btn btn-success btn-md">Activer</button></p>
                                <hr>
                            <?php } ?>
                                
                                <form action"">
                                    <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" name="en_forum_externe" id="en_forum_externe" <?php if ($config['en_forum']){ ?> disabled <?php } ?> <?php if ($config['other_forum'] == "1") { ?> checked <?php } ?>>
                                            <label class="form-check-label">
                                                Activer un forum externe
                                            </label>
                                        </div>
                                        <div class="row control-group">
                                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                                <label>Lien vers un forum externe :</label>
                                                <input class="form-control" type="text" value="<?= $config['link_forum']; ?>" name="link" <?php if ($config['en_forum']){ ?> disabled <?php } ?> id="link">
                                                <p class="help-block text-danger"></p>
                                            </div>
                                        </div>
                                        <p style="text-align: right"><button type="submit" class="save_modifs btn btn-success btn-md" <?php if ($config['en_forum']){ ?> disabled <?php } ?>>Sauvegarder</button></p>
                                </form>
                            <hr>
                            <p><strong>Ajouter une catégorie au forum (par défaut) :</strong></p>
                            <form action"" method="POST">
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nouvelle catégorie :</label>
                                        <input class="form-control" type="text" <?php if (!$config['en_forum']){ ?> disabled <?php } ?> name="new_cat" id="new_cat">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                    <p style="text-align: right"><button type="submit" <?php if (!$config['en_forum']){ ?> disabled <?php } ?> class="send_new_cat btn btn-success btn-md">Envoyer</button></p>
                            </form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Catégories enregistrées
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($cats)){ ?>
                                <p>Aucune catégorie n'a pour le moment été enregistrée.</p>
                            <?php }else { 
                                foreach ($cats as $c){ ?>
                                    <a id="line_<?php echo $c['id']; ?>" data="<?php echo $c['id']; ?>" class="list-group-item">
                                        <strong><?php echo $c['titre']; ?></strong> (<?= $c['nb']; ?> sujets enregistrés à l'intérieur)
                                        <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                            <button 
                                            data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/forum/delete/<?php echo $c['id']; ?>" 
                                            id="<?php echo $c['id']; ?>" type="submit" style ="padding-left: 8px; padding-right: 8px; padding-top: 1px; padding-bottom: 1px;" 
                                            class="delete_cat btn btn-danger btn-sm">Supprimer
                                            </button>
                                        </span>
                                    </a>
                                <?php }
                             } ?>
                        </div>
                    </div>
        </div>
</div>
