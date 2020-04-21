<?php global $Serveur_Config, $bddconfig, $config_serveurs, $img_available;?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS</h1>
            <h5>Sur cette page, les principaux réglages de votre site internet sont modifiables.</h5>
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
                <?php// var_dump($Serveur_Config); ?>
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

                                <!--<div class="form-group">
                                    <label for="email_inscription" class="col-form-label">Nombre d'unités de monnaie virtuelle</label>
                                    <input class="form-control" type="number" min="0" id="money_" name="money">
                                    <small class="form-text text-muted">Valeur actuelle :.</small>
                                </div>    -->   
                        <p class="text-right"><button type="button" class="mainconf btn btn-info mod_button" data="">Sauvegarder</button></p>
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
                        <p class="text-right"><button type="button" class="bddconf btn btn-danger mod_button" data="">Sauvegarder</button></p>
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
                <?php if (!DServerLink || empty($config_serveurs)){ ?>
                    <p style="text-align: justify;"><span style="color: red"><strong>Impossible d'accèder à ces réglages.</strong></span><br> En effet, pour relier votre site à votre ou vos serveur(s), vous devez installer l'addon officiel nommé DServerLink. Si celui-ci est installé, vérifiez le fichier serveurs.ini dans le dossier config.</p>
                    <?php }else { 
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
                        <p class="text-right"><button type="button" class="saveserver btn btn-warning mod_button" data="<?= $c['id']; ?>">Sauvegarder</button> <button type="button" class="suppserver btn btn-danger mod_button" data="<?= $c['id']; ?>">Supprimer le serveur</button></p>
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
                            <select class="form-control" name="img" id="img_<?= $c['id']; ?>">
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ ?>
                                            <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php } }
                                } ?>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="en_ns">
                            <label class="form-check-label" for="en_ns">
                                Activer le lien avec le serveur
                            </label>
                        </div>
                        <p class="text-right"><button type="button" class="save_ns_server btn btn-danger mod_button">Sauvegarder</button></p>
                    </form>
                    
                    
                    
                    
                    <?php} ?>
                </div>
            </div>
        </div>

        
        
    </div>
            <!-- /.col-lg-12 -->
</div>
<script>
$(".mainconf").click(function(){
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/config/write/mainconf/',
    type : 'POST',
    data : 'Serveur_name=' + $('#Serveur_name').val() + '&protocol=' + $('#protocol').val() + '&desc=' + $('#desc').val() + '&about_footer=' + $('#about_footer').val() + '&support_en=' + $('#support_en').prop('checked') + '&vote_en=' + $('#vote_en').prop('checked') + '&lien_vote=' + $('#lien_vote').val() + '&socialgl=' + $('#socialgl').val() + '&socialtw=' + $('#socialtw').val() + '&socialyt=' + $('#socialyt').val() + '&socialdiscord=' + $('#socialdiscord').val() + '&socialfb=' + $('#socialfb').val(),
    dataType : 'html',
    success: function (data_rep) {
        console.log(data_rep);
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
});

$(".bddconf").click(function(){
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/config/write/bddconf/',
    type : 'POST',
    data : 'host=' + $('#host').val() + '&db=' + $('#db').val() + '&usr=' + $('#usr').val() + '&pwd=' + $('#pwd').val(),
    dataType : 'html',
    success: function (data_rep) {
        console.log(data_rep);
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
});

$(".saveserver").click(function(){
  var link = $(this).attr("data");
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/config/write/serveurs/' + link,
    type : 'POST',
    data : 'name=' + $('#name_'+link).val() + '&desc=' + $('#desc_' + link).val() + '&host=' + $('#host_'+link).val() + '&queryport=' + $('#queryport_'+link).val() + '&rconport=' + $('#rconport_'+link).val() + '&password=' + $('#password_'+link).val()+ '&version=' + $('#version_'+link).val() + '&enabled=' + $('#en_' +link).prop('checked') + '&game=' + $('#game_' + link + ' option:selected').val() + '&img=' + $('#img_' + link + ' option:selected').val(),
    dataType : 'html',
    success: function (data_rep) {
        console.log(data_rep);
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
});
$(".suppserver").click(function(){
  var link = $(this).attr("data");
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/config/write/serveurs/supp/',
    type : 'POST',
    data : 'id=' + link,
    dataType : 'html',
    success: function (data_rep) {
        console.log(data_rep);
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }else {
        location.reload();
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
});
$(".save_ns_server").click(function(){
    if ($('#name_ns').val() != "" && $('#desc_ns').val() != "" && $('#host_ns').val() != "" && $('#queryport_ns').val() != "" && $('#rconport_ns').val() != "" && $('#password_ns').val() != "" && $('#version_ns').val() != ""){
        $.ajax({
            url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/config/write/serveurs/new',
            type : 'POST',
            data : 'name=' + $('#name_ns').val() + '&desc=' + $('#desc_ns').val() + '&host=' + $('#host_ns').val() + '&queryport=' + $('#queryport_ns').val() + '&rconport=' + $('#rconport_ns').val() + '&password=' + $('#password_ns').val()+ '&version=' + $('#version_ns').val() + '&enabled=' + $('#en_ns').prop('checked') + '&game=' + $('#game_ns option:selected').val() + '&img=' + $('#img_ns option:selected').val(),
            dataType : 'html',
            success: function (data_rep) {
                console.log(data_rep);
                if (data_rep != "Success"){
                    alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
                }else {
                    location.reload();
                }
            },
            error: function() {
            alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
            }
        });
    }else {
        alert("Formulaire incomplet : Merci de le compléter entièrement avant de sauvegarder la nouvelle configuration.");
    }
  
});
</script>