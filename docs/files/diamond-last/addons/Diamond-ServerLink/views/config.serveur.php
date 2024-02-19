<?php global $config_serveurs, $serverid; ?>
<br><br>
<h1 class="text-gray-800 text-center">Modifier la configuration d'un serveur de jeu</h1>
<p class="text-center">Vous pouvez, avant de modifier la configuration, utiliser le <a href="<?php echo LINK; ?>Diamond-ServerLink/diagnostic/">nouvel outil de diagnostic</a> de votre installation proposé par DiamondCMS.</p>
<hr style="max-width: 60%;">
<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow lg-6">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Configuration du serveur</h6>
                </div>
                <div class="card-body">
                    <p><em>Une documentation pour apprendre à mettre en place le lien entre votre site et vos serveurs est disponible <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Lien-serveur(s)-de-jeu">ici.</a></em></p>
                    
                    <form method="post" id="serveur_config">
                        <div>
                        <input class="form-control in_req" type="hidden" name="id" value="<?= $config_serveurs[$serverid]['id']; ?>">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input in_req" name="enabled" type="checkbox" <?php if ($config_serveurs[$serverid]['enabled'] == "true") { ?> checked <?php } ?>>
                            <label class="form-check-label">
                                Activer le lien avec le serveur
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-form-label">Nom du serveur :</label>
                            <input class="form-control in_req" type="text" name="name" value="<?= $config_serveurs[$serverid]['name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="desc" class="col-form-label">Description du serveur</label>
                            <input class="form-control in_req" type="text" name="desc" value="<?= $config_serveurs[$serverid]['desc']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="host" class="col-form-label">Host (ip du serveur) :</label>
                            <input class="form-control in_req" type="text" name="host" value="<?= $config_serveurs[$serverid]['host']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="queryport" class="col-form-label">Port (Query) :</label>
                            <input class="form-control in_req" type="text" name="queryport" value="<?= $config_serveurs[$serverid]['queryport']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="rconport" class="col-form-label">Port (Rcon / JSONAPI) :</label>
                            <input class="form-control in_req" type="text" name="port" value="<?= $config_serveurs[$serverid]['port']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">Mot de passe (Rcon / JSONAPI) :</label>
                            <input class="form-control in_req" type="text" name="password" value="<?= $config_serveurs[$serverid]['password']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Jeu</label>
                            <select class="form-control in_req" name="game">
                                <?php if (defined("DServerLinkGamesSupported")) {
                                    foreach(DServerLinkGamesSupported as $g){ ?>
                                        <option value="<?= $g; ?>" <?php echo ($config_serveurs[$serverid]['game'] == $g) ? "selected" : ""; ?>><?= $g; ?></option>
                                <?php }
                                } ?>
                            </select>
                            <small class="form-text text-muted">Jeu actuel : <?= $config_serveurs[$serverid]['game']; ?>.</small>
                        </div>
                        <div class="form-group">
                            <label for="version" class="col-form-label">Version (ou mode de jeu pour GMod) :</label>
                            <input class="form-control in_req" type="text" name="version" value="<?= $config_serveurs[$serverid]['version']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <input style="width: 100%;" type="button" id="dic_launcher" data-whereisdic="<?php echo LINK . "views/themes/" . $Serveur_Config['theme'] . "/JS/plugins/listener/" ;?>" 
                                    data-wherearefiles="<?php echo LINK . "API/admin/get/uploadedImgs/" ;?>"
                                    data-imgWidth="1200" data-imgHeight="676"
                                    <?php if(substr($config_serveurs[$serverid]['img'], 0, 4) == "http"){ ?>
                                        data-linkdefault="<?= $config_serveurs[$serverid]['img']; ?>"
                                    <?php }else { ?>
                                        data-imgdefault="<?= $config_serveurs[$serverid]['img']; ?>"
                                    <?php } ?>
                                    data-callback="en_save"
                                    data-resetcallback="off_save"
                                    class="btn btn-custom" />                        
                        </div>
                        <hr>
                        <p class="text-center">
                            <button style="width: 49%;" type="button" class="btn btn-warning ajax-simpleSend" id="save"
                                data-api="<?= LINK; ?>api/" data-module="serveurs/" data-verbe="set" data-func="oneserverconfig" 
                                data-tosend="#serveur_config" data-useform="true" disabled>Sauvegarder</button>

                            <button style="width: 49%;" type="button" class="btn btn-danger ajax-simpleSend" 
                                data-api="<?= LINK; ?>api/" data-module="serveurs/" data-verbe="set" data-func="delserver" 
                                data-tosend="id= <?= $config_serveurs[$serverid]['id']; ?>" data-reload="true" data-callback="returnlobby">Supprimer le serveur</button>
                        </p>
                    </form>
                </div>
            </div>
            <br>
        </div>
        <div class="col-lg-6">
            <div class="card shadow lg-6">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Diagnostic du serveur</h6>
                </div>
                <div class="card-body">
                    <div class="server" data-id="<?php echo $serverid; ?>" data-link="<?php echo LINK . "api/serveurs/get/diagnostic/"; ?>">
                        <h5 class="loader" id="loader_<?php echo $config_serveurs[$serverid]['id']; ?>"><img src="<?= LINK; ?>views/uploads/img/ajax-loader.gif" alt="loading" /> Chargement en cours...</h5>
                        <div class="success" id="success_<?php echo $config_serveurs[$serverid]['id']; ?>">
                            <h5 class="text-custom"><strong><i class="fa fa-check-circle-o" aria-hidden="true"></i> Fonctionnement normal !</strong></h5>
                            <p>Tout fonctionne correctement, tant le Query que le RCon.</p>
                            <p class="text-right"><button class="btn btn-sm btn-custom rediag">Relancer la vérification</button></p>
                        </div>
                        <div class="disabled" id="disabled_<?php echo $config_serveurs[$serverid]['id']; ?>">
                            <h5 class="text-warning"><strong><i class="fa fa-question-circle" aria-hidden="true"></i> Serveur désactivé. </strong></h5>
                            <p>Impossible de diagnostiquer le serveur, celui-ci étant désactivé dans la configuration.</p>
                            <p class="text-right"><button class="btn btn-sm btn-warning rediag">Relancer la vérification</button></p>
                            <p></p>
                        </div>
                        <div class="failure" id="failure_<?php echo $config_serveurs[$serverid]['id']; ?>">
                            <h5 class="text-danger"><strong><i class="fa fa-times-circle" aria-hidden="true"></i> Erreur ! </strong></h5>
                            <p class="failure-error" id="failure-error_<?php echo $config_serveurs[$serverid]['id']; ?>">Une erreur imprévue est survenue. La requête à l'API s'est mal terminée.</p>
                            <p class="failure-help" id="failure-help_<?php echo $config_serveurs[$serverid]['id']; ?>"></p>
                            <br>
                            <p class="text-right"><button class="btn btn-sm btn-danger rediag">Relancer la vérification</button></p>
                            <p></p>
                        </div>
                    </div>                    
                </div>
            </div>
            <br>
        </div>
    </div>  
</div>


<style>
.step{
    display: block; 
    width: 70%; 
    margin: auto;
    margin-top: 3%;
}
.loader {
    text-align: center;
}
.success, .disabled, .failure, .failure-help{
    display: none;
}
.failure-error {
    margin: 0;
}
</style>