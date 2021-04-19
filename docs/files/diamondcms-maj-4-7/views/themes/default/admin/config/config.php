<?php global $Serveur_Config, $bddconfig, $config_serveurs, $img_available;?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS</h1>
            <h5>Sur cette page, les principaux réglages de votre site internet sont modifiables.<br><br>
            <strong>Pour accèder à la documentation : <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Boutique">Cliquez-ici</a></strong>
            </h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                        Principaux réglages
                </div>
                <div class="panel-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="Serveur_name" class="col-form-label">Nom du serveur (ou de votre entreprise) :</label>
                            <input class="form-control" type="text" name="Serveur_name" id="Serveur_name" value="<?= $Serveur_Config['Serveur_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="protocol" class="col-form-label">Protocol web (http ou https)</label>
                            <input class="form-control" type="text" name="protocol" id="protocol" value="<?= $Serveur_Config['protocol']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="desc" class="col-form-label">Description de votre serveur (ou collectif)</label>
                            <input class="form-control" type="text" name="desc" id="desc" value="<?= $Serveur_Config['desc']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="about_footer" class="col-form-label">A propos de vous : (texte du footer)</label>
                            <input class="form-control" type="text" name="about_footer" id="about_footer" value="<?= $Serveur_Config['about_footer']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Logo en haut à gauche :</label>
                            <select class="form-control" name="logo" id="logo">
                                <option value="name_server">Utiliser le nom du serveur</option>
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ ?>
                                            <option value="<?= $i; ?>" <?php if ($i == $Serveur_Config['name_logo']){ ?> selected <?php } ?>><?= $i; ?></option>
                                <?php } }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Favicon :</label>
                            <select class="form-control" name="favicon" id="favicon">
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ ?>
                                            <option value="<?= $i; ?>" <?php if ($i == $Serveur_Config['favicon']){ ?> selected <?php } ?>><?= $i; ?></option>
                                <?php } }
                                ?>
                            </select>
                        </div>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="support_en" <?php if ($Serveur_Config['en_support'] == "1") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="support_en">
                                Activer la fonction support du CMS
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="vote_en" <?php if ($Serveur_Config['en_vote'] == "1") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="vote_en">
                                Activer la fonction vote du CMS
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="lien_vote" class="col-form-label">Lien du site sur lequel le vote est enregistré</label>
                            <input class="form-control" type="text" name="lien_vote" id="lien_vote" value="<?= $Serveur_Config['lien_vote']; ?>">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="social.gl" class="col-form-label">Lien vers votre Google+ : (inscrire "disabled" sinon)</label>
                            <input class="form-control" type="text" name="social.gl" id="socialgl" value="<?= $Serveur_Config['Social']['gl']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="social.yt" class="col-form-label">Lien vers votre Youtube : (inscrire "disabled" sinon)</label>
                            <input class="form-control" type="text" name="social.yt" id="socialyt" value="<?= $Serveur_Config['Social']['yt']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="social.fb" class="col-form-label">Lien vers votre Facebook : (inscrire "disabled" sinon)</label>
                            <input class="form-control" type="text" name="social.fb" id="socialfb" value="<?= $Serveur_Config['Social']['fb']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="social.tw" class="col-form-label">Lien vers votre Twitter : (inscrire "disabled" sinon)</label>
                            <input class="form-control" type="text" name="social.tw" id="socialtw" value="<?= $Serveur_Config['Social']['tw']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="social.discord" class="col-form-label">Lien vers votre Discord : (inscrire "disabled" sinon)</label>
                            <input class="form-control" type="text" name="social.discord" id="socialdiscord" value="<?= $Serveur_Config['Social']['discord']; ?>">
                        </div>  
                        <p class="text-right"><button type="button" class="mainconf btn btn-info mod_button" data-link="<?= LINK; ?>admin/config/write/mainconf/">Sauvegarder</button></p>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-warning">
                <div class="panel-heading">
                        Réglages de la base de données
                </div>
                <div class="panel-body">
                <?php// var_dump($Serveur_Config); ?>
                    <p style="text-align: justify;"><span style="color: red"><strong>Attention ! Toute mauvaise manipulation rendrait l'interface totalement inaccessible.</strong></span><br> Dans ce cas, le seul moyen de réparer cette dernière est de modifier le fichier "bdd.ini", situé dans le dossier "config" du serveur web.</p>
                    <hr>
                    <form method="post">
                        <div class="form-group">
                            <label for="host" class="col-form-label">Host :</label>
                            <input class="form-control" type="text" name="host" id="host" value="<?= $bddconfig['host']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="db" class="col-form-label">Nom de la base de donnée allouée à DiamondCMS</label>
                            <input class="form-control" type="text" name="db" id="db" value="<?= $bddconfig['db']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="usr" class="col-form-label">Utilisateur :</label>
                            <input class="form-control" type="text" name="usr" id="usr" value="<?= $bddconfig['usr']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="pwd" class="col-form-label">Mot de passe de ce dernier</label>
                            <input class="form-control" type="text" name="pwd" id="pwd" value="<?= $bddconfig['pwd']; ?>">
                        </div>
                        <p class="text-right"><button type="button" class="bddconf btn btn-danger mod_button" data-link="<?= LINK; ?>admin/config/write/bddconf/">Sauvegarder</button></p>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                        Réglages de la connexion avec les serveurs de jeu
                </div>
                <div class="panel-body">
                <?php if (!DServerLink){ ?>
                    <p style="text-align: justify;"><span style="color: red"><strong>Impossible d'accèder à ces réglages.</strong></span><br> En effet, pour relier votre site à votre ou vos serveur(s), vous devez installer l'addon officiel nommé DServerLink. Si celui-ci est installé, vérifiez le fichier serveurs.ini dans le dossier config.</p>
                    <?php }else { 
                        if (empty($config_serveurs)){ ?>
                            <p>Aucun serveur n'est pour le moment configuré.</p>
                            <hr>
                        <?php }
                        foreach($config_serveurs as $c){ ?>
                        <h4>Serveur <?= $c['id']; ?> : </h4>
                    <form method="post">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Nom du serveur :</label>
                            <input class="form-control" type="text" name="name" id="name_<?= $c['id']; ?>" value="<?= $c['name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="desc" class="col-form-label">Description du serveur</label>
                            <input class="form-control" type="text" name="desc" id="desc_<?= $c['id']; ?>" value="<?= $c['desc']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="host" class="col-form-label">Host (ip du serveur) :</label>
                            <input class="form-control" type="text" name="host" id="host_<?= $c['id']; ?>" value="<?= $c['host']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="queryport" class="col-form-label">Port (Query) :</label>
                            <input class="form-control" type="text" name="queryport" id="queryport_<?= $c['id']; ?>" value="<?= $c['queryport']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="rconport" class="col-form-label">Port (Rcon) :</label>
                            <input class="form-control" type="text" name="rconport" id="rconport_<?= $c['id']; ?>" value="<?= $c['port']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">Mot de passe (Rcon) :</label>
                            <input class="form-control" type="text" name="password" id="password_<?= $c['id']; ?>" value="<?= $c['password']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Jeu</label>
                            <select class="form-control" name="game" id="game_<?= $c['id']; ?>">
                                <?php if (defined("DServerLinkGamesSupported")) {
                                    foreach(DServerLinkGamesSupported as $g){ ?>
                                        <option value="<?= $g; ?>"><?= $g; ?></option>
                                <?php }
                                } ?>
                            </select>
                            <small class="form-text text-muted">Jeu actuel : <?= $c['game']; ?>.</small>
                        </div>
                        <div class="form-group">
                            <label for="version" class="col-form-label">Version (ou mode de jeu pour GMod) :</label>
                            <input class="form-control" type="text" name="version" id="version_<?= $c['id']; ?>" value="<?= $c['version']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Image associée (Liste des images disponibles sur le serveur) :</label>
                            <select class="form-control" name="img" id="img_<?= $c['id']; ?>">
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ 
                                        if ($i == $c['img']){ ?>
                                            <option value="<?= $i; ?>" selected><?= $i; ?></option>
                                        <?php }else { ?>
                                            <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php } }
                                } ?>
                            </select>
                            <small class="form-text text-muted">Image actuelle : <?= $c['img']; ?></small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="en_<?= $c['id']; ?>" <?php if ($c['enabled'] == "true") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="en_<?= $c['id']; ?>">
                                Activer le lien avec le serveur
                            </label>
                        </div>
                        <p class="text-right"><button type="button" class="saveserver btn btn-warning mod_button" data-id="<?= $c['id']; ?>" data="<?= LINK; ?>admin/config/write/serveurs/<?= $c['id']; ?>">Sauvegarder</button> 
                        <button type="button" class="suppserver btn btn-danger mod_button" data-id="<?= $c['id']; ?>" data="<?= LINK; ?>admin/config/write/serveurs/supp/">Supprimer le serveur</button></p>
                    </form>
                    <hr>
                    <?php } ?> 
                    <h4>Ajouter un serveur : </h4>
                    <form method="post">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Nom du serveur :</label>
                            <input class="form-control" type="text" name="name" id="name_ns">
                        </div>
                        <div class="form-group">
                            <label for="desc" class="col-form-label">Description du serveur</label>
                            <input class="form-control" type="text" name="desc" id="desc_ns" >
                        </div>
                        <div class="form-group">
                            <label for="host" class="col-form-label">Host (ip du serveur) :</label>
                            <input class="form-control" type="text" name="host" id="host_ns" >
                        </div>
                        <div class="form-group">
                            <label for="queryport" class="col-form-label">Port (Query) :</label>
                            <input class="form-control" type="text" name="queryport" id="queryport_ns">
                        </div>
                        <div class="form-group">
                            <label for="rconport" class="col-form-label">Port (Rcon) :</label>
                            <input class="form-control" type="text" name="rconport" id="rconport_ns">
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">Mot de passe (Rcon) :</label>
                            <input class="form-control" type="text" name="password" id="password_ns">
                        </div>
                        <div class="form-group">
                            <label>Jeu</label>
                            <select class="form-control" name="game" id="game_ns">
                                <?php if (defined("DServerLinkGamesSupported")) {
                                    foreach(DServerLinkGamesSupported as $g){ ?>
                                        <option value="<?= $g; ?>"><?= $g; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="version" class="col-form-label">Version (ou mode de jeu pour GMod) :</label>
                            <input class="form-control" type="text" name="version" id="version_ns">
                        </div>
                        <div class="form-group">
                            <label>Image associée (Liste des images disponibles sur le serveur) :</label>
                            <select class="form-control" name="img" id="img_ns">
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ ?>
                                            <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php } }
                                ?>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="en_ns">
                            <label class="form-check-label" for="en_ns">
                                Activer le lien avec le serveur
                            </label>
                        </div>
                        <p class="text-right"><button type="button" class="save_ns_server btn btn-danger mod_button" data="<?= LINK; ?>admin/config/write/serveurs/new">Sauvegarder</button></p>
                    </form>           
            <?php } ?>
                </div>
            </div>
        </div>

        
        
    </div>
            <!-- /.col-lg-12 -->
</div>
