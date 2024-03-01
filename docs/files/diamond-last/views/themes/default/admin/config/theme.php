<?php global $cur_theme_conf; ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Configuration de DiamondCMS - Gestion du thème</h1>
    <p class="mb-4">DiamondCMS fournit un systeme de personnalisation du thème graphique de votre site internet.</p>
    <div id="save_beforeleaving" class="alert alert-warning" style="display: none;"><strong>Attention !</strong> Pensez à sauvegarder vos modifications de couleur avant de quitter la page !</div>
    <?php if (!$cur_theme_conf['editable']){ ?>
    <div class="alert alert-danger"><strong>Le thème installé ne permet pas les modifications.</strong> Vous pouvez changer de thème ou contacter le support de développement sur Github.</div>
    <?php } else { ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Modifier le thème</h6>
                </div>
                <div class="card-body">
                    <p><strong>Configuration actuelle du thème :</strong> <?php echo $cur_theme_conf["mode"]; ?></p>
                    <hr>
                    <p><em>Vous pouvez choisir de modifier le mode du thème : </em></p>
                    <div class="row">
                        <?php foreach ($cur_theme_conf['modes'] as $mode) { ?>
                            <div class="col-lg-4">
                                <button type="button" style="width: 90%;" <?php if($cur_theme_conf["mode"] == $mode) { ?>disabled<?php } ?> class="btn btn-light ajax-simpleSend" 
                                data-api="<?= LINK; ?>api/" data-module="theme/" data-verbe="set" data-func="mode" data-tosend="mode=<?php echo $mode; ?>" data-reload="true"
                                ><?php echo $mode; ?></button>
                            </div>
                        <?php } ?>
                        <div class="col-lg-4">
                            <button style="width: 90%;" id="custom_button" <?php if($cur_theme_conf["mode"] == "Custom") { ?>disabled<?php } ?> class="btn btn-light">Personnalisé</button>
                        </div>
                    </div>
                    <div id="custom-depend" <?php if($cur_theme_conf["mode"] != "Custom") { ?> style="display: none;"<?php } ?>>
                            <br><hr>
                            <p><em>Pensez à appuyer sur le bouton Sauvegarder pour acter le passage au thème personnalisé.</em></p>
                            <form method="post" id="custom_colors">
                                <?php foreach ($cur_theme_conf['Colors_Custom'] as $key => $color) { ?>
                                <label for="cp" class=""><?php echo $key; ?> (<em><?php echo $cur_theme_conf['Colors_desc'][$key]; ?></em>) :</label>
                                <div class="cp input-group">
                                    <input class="form-control color-picker" type="text" name="<?= $key; ?>" value="<?= $color; ?>">
                                    <span class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                    </span>
                                </div>
                                <?php if ($cur_theme_conf['mode'] != "Custom"){ ?>
                                        <small class="form-text text-muted">Valeur par défaut du mode <?php echo $cur_theme_conf['mode']; ?> : <?php echo $cur_theme_conf['Colors_' . $cur_theme_conf['mode']][$key]; ?>.</small>
                                    <?php } else { ?>
                                        <small class="form-text text-muted">Valeur par défaut du mode Normal : <?php echo $cur_theme_conf['Colors_Normal'][$key]; ?>.</small>
                                    <?php } ?>
                                <br />
                                <?php } ?>       
                                <p class="text-justify">
                                <em><strong>Tip :</strong> N'oubliez pas que votre navigateur peut avoir conservé les paramètres de style en cache. Utilisez le Control+F5 sur Windows ou le Commande+Shift+R sur Mac pour voir les changements.</em>
                                </p>                         
                            <p class="text-right">
                            <button type="button" class="btn btn-custom ajax-simpleSend" 
                            data-api="<?= LINK; ?>api/" data-module="theme/" data-verbe="set" data-func="customColors" data-tosend="#custom_colors" data-useform="true" data-callback="success_save"
                            >Sauvegarder</button>
                        </form>                            
                    </div>
                </div>
            </div>
            <br>
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Informations sur le thème</h6>
                </div>
                <div class="card-body">
                    <p><strong>Nom du thème :</strong> <?php echo $cur_theme_conf["display_name"]; ?><br />
                        <strong>Description :</strong> <?php echo $cur_theme_conf["desc"]; ?><br />
                        <strong>Version :</strong> <?php echo $cur_theme_conf["version"]; ?><br />
                        <strong>Auteur :</strong> <?php echo $cur_theme_conf["author"]; ?><br />
                        <strong>Version du CMS :</strong> <?php echo $cur_theme_conf["version_cms"]; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow lg-8">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Prévisualisation des changements</h6>
                </div>
                <div class="card-body">
                    <div id="userframer" class="embed-responsive  embed-responsive-4by3">
                        <iframe id="useriframer" style="width:100%;height:100%;overflow-x:hidden;overflow-y:hidden;" src="<?php echo LINK; ?>compte/" frameborder="0"></iframe>
                    </div>
                    <hr>
                    <div id="adminframer" class="embed-responsive  embed-responsive-4by3">
                        <iframe id="adminiframer" style="width:100%;height:100%;overflow-x:hidden;overflow-y:hidden;" src="<?php echo LINK."admin/"; ?>" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
            <br>
            <div class="card shadow lg-8">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Polices de caractères</h6>
                </div>
                <div class="card-body">
                <p class="text-justify"><em>Nous vous encourageons à utiliser l'excellent outil Gooogle Font pour trouver vos polices. Vous pouvez utiliser le lien qui vous est fourni dans l'instruction @import.</em></p>
                    <form method="post" id="fonts_custom">
                                <?php foreach ($cur_theme_conf['Fonts_Custom'] as $key => $font) { if (substr($key,-4, 4) != "link"){ ?>
                                <h5 class="text-center"><strong><?php echo $key; ?> (<em><?php echo $cur_theme_conf['Fonts_desc'][$key]; ?></em>) :</strong></h5><br>
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Nom de la police :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="<?= $key; ?>" value="<?= $font; ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Lien de la police :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="<?= $key; ?>-link" value="<?php echo $cur_theme_conf['Fonts_Custom'][$key. '-link']; ?>">
                                    </div>
                                </div>
                                <small class="form-text text-muted">Valeur par défaut du thème : <?php echo $cur_theme_conf['Fonts_default'][$key]; ?> - <?php echo $cur_theme_conf['Fonts_Custom'][$key. '-link']; ?>.</small>
                                <br />
                                <hr>
                                <?php } } ?>                                
                            <p class="text-right">
                            <button type="button" class="btn btn-custom ajax-simpleSend" 
                            data-api="<?= LINK; ?>api/" data-module="theme/" data-verbe="set" data-func="fonts" data-tosend="#fonts_custom" data-useform="true"
                            >Sauvegarder</button>
                    </form>                         
                </div>
            </div>
        </div>
    </div>
    <?php }?>
</div>
<br>