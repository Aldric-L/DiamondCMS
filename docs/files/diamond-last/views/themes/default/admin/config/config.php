<?php global $Serveur_Config, $bddconfig, $config_serveurs, $img_available; ?>
<div id="modalJouer" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jouer sur <?= $Serveur_Config['Serveur_name']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal_jouer_content"><?= htmlspecialchars_decode($Serveur_Config['text_jouer_menu']); ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div id="modalAccueilPopup" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="accueilpopup_content"><?php echo (isset($Serveur_Config['text_popup_accueil'])) ? htmlspecialchars_decode($Serveur_Config['text_popup_accueil'])  :  "";  ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Configuration de DiamondCMS</h1>
    <p class="mb-4">Sur cette page, les principaux réglages de votre site internet sont modifiables.</p>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Principaux réglages</h6>
                </div>
                <div class="card-body">
                    <form method="post" id="genform">
                        <div class="form-group">
                            <label for="Serveur_name" class="col-form-label">Nom du serveur (ou de votre entreprise) :</label>
                            <input class="form-control" type="text" name="Serveur_name" id="Serveur_name" value="<?= $Serveur_Config['Serveur_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Logo en haut à gauche :</label>
                            <select class="form-control" name="logo_img" id="logo_img">
                                <option value="name_server">Utiliser le nom du serveur</option>
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ ?>
                                            <option value="<?= $i; ?>" <?php if ($i == $Serveur_Config['logo_img']){ ?> selected <?php } ?>><?= $i; ?></option>
                                <?php } }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Favicon (icone du site ):</label>
                            <select class="form-control" name="favicon" id="favicon">
                                <?php if (!empty($img_available)) {
                                    foreach($img_available as $i){ ?>
                                            <option value="<?= $i; ?>" <?php if ($i == $Serveur_Config['favicon']){ ?> selected <?php } ?>><?= $i; ?></option>
                                <?php } }
                                ?>
                            </select>
                        </div>       
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="en_support" id="en_support" <?php if ($Serveur_Config['en_support']) { ?> checked <?php } ?>>
                            <label class="form-check-label" for="en_support">
                                Activer la fonction support du CMS
                            </label>
                        </div>
                        <p class="text-right">
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="genconfig" data-tosend="#genform" data-useform="true"
                        >Sauvegarder</button></p>
                        </p>
                    </form>
                    
                </div>
            </div><br>
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Réglages du footer (bas de page)</h6>
                </div>
                <div class="card-body">
                    <form method="post" id="footerform">
                        <div class="form-group">
                            <label for="about_footer" class="col-form-label">A propos de vous : (texte du footer)</label>
                            <input class="form-control" type="text" name="about_footer" id="about_footer" value="<?= $Serveur_Config['about_footer']; ?>">
                        </div>
                        <hr>
                        <p class="text-justify"><em>Vous pouvez ajouter ici des liens vers vos réseaux sociaux qui apparîtront dans le footer : </em></p>
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
                        <p class="text-right">
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="genconfig" data-tosend="#footerform" data-useform="true"
                        >Sauvegarder</button></p>
                        </p>
                    </form>
                    
                </div>
            </div><br>
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Fenêtre "Vote"</h6>
                </div>
                <div class="card-body">
                <p style="text-align: justify;"><em>DiamondCMS vous propose une fonction vote qui vous permet de rétribuer vos utilisateurs qui votent pour votre serveur sur les sites de classement.</em></p>
                    <hr>
                    <form id="voteform">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="en_vote" id="en_vote" <?php if ($Serveur_Config['en_vote']) { ?> checked <?php } ?>>
                            <label class="form-check-label" for="en_vote">
                                Activer la fonction vote du CMS
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="lien_vote" class="col-form-label">Lien du site sur lequel le vote est enregistré :</label>
                            <input class="form-control" type="text" name="lien_vote" id="lien_vote" value="<?= $Serveur_Config['lien_vote']; ?>">
                        </div>   
                        <div class="form-group">
                            <label for="tokens_vote" class="col-form-label">Nombre de <?php echo $Serveur_Config['Serveur_money']; ?>s par vote :</label>
                            <input class="form-control" type="number" name="tokens_vote" id="tokens_vote" value="<?php echo (isset($Serveur_Config['tokens_vote'])) ? $Serveur_Config['tokens_vote'] : "1"; ?>">
                        </div>                  
                    </form>
                    <p class="text-right">
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="genconfig" data-tosend="#voteform" data-useform="true"
                        >Sauvegarder</button></p> 
                </div>
            </div><br>
            
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Page d'accueil</h6>
                </div>
                <div class="card-body">
                    <form method="post" id="accueilgenform">
                        
                        <div class="form-group">
                            <label for="desc" class="col-form-label">Description de votre serveur (ou collectif) :</label>
                            <input class="form-control" type="text" name="desc" id="desc" value="<?= $Serveur_Config['desc']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="bg" class="col-form-label">Arrière plan :</label>
                            <input type="button" name="bg" id="dic_launcher" 
                                data-whereisdic="<?php echo LINK . "views/themes/" . $Serveur_Config['theme'] . "/js/plugins/listener/" ;?>" 
                                data-wherearefiles="<?php echo LINK . "API/admin/get/uploadedImgs/" ;?>"
                                data-imgWidth="1200" data-imgHeight="676" data-imgdefault="<?php echo $Serveur_Config['bg']; ?>"
                                data-enNewImgLink="false"
                                class="btn btn-secondary"
                                style="width: 100%;" />
                        </div>
                        
                        <p class="text-right">
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="accueilconfig" data-tosend="#accueilgenform" data-useform="true"
                        >Sauvegarder</button></p>
                        </p>
                    </form>
                    <hr>
                    <p class="text-justify"><em>Vous pouvez activer une "Popup", c'est-à-dire une fenêtre qui s'ouvre à chaque visite de l'accueil, pour informer vos visiteurs d'un évènement par exemple.</em></p>
                    <form id="accueilformpopum">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="popup_accueil" id="popup_accueil" <?php if (isset($Serveur_Config['popup_accueil']) && $Serveur_Config['popup_accueil']) { ?> checked <?php } ?>>
                            <label class="form-check-label" for="popup_accueil">
                                Activer la PopUp
                            </label>
                        </div>
                        <input type="hidden" name="text_popup_accueil" value="<?php echo (isset($Serveur_Config['text_popup_accueil'])) ? $Serveur_Config['text_popup_accueil']  :  "";  ?>">
                    </form>
                    <br>
                    <div class="form-group col-xs-12 floating-label-form-group controls">
                        <label>Contenu du PopUp :</label>
                        <textarea rows="10" id="accueilpopupcontent" class="form-control" name="content"><?php echo (isset($Serveur_Config['text_popup_accueil'])) ? htmlspecialchars_decode($Serveur_Config['text_popup_accueil'])  :  "";  ?></textarea>
                        <p class="help-block text-danger"></p>
                    </div>
                    <p class="text-center">
                        <button type="button" class="btn btn-secondary "
                        data-toggle="modal" data-target="#modalAccueilPopup"
                        >Tester la fenêtre</button>
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="genconfig" data-tosend="#accueilformpopum" data-useform="true"
                        >Sauvegarder</button></p>
                </div>
            </div><br>
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Fenêtre "Jouer"</h6>
                </div>
                <div class="card-body">
                <p style="text-align: justify;"><em>Ici, vous pouvez inscrire le texte affiché dans le modal jouer du CMS. Cette fonctionnalité peut être désactivée, mais permet à vos joueurs de pouvoir se connecter à votre infrastructure plus facilement.</em></p>
                    <hr>
                    <form id="jouerform">
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="en_jouer" id="en_jouer" <?php if (isset($Serveur_Config['en_jouer']) && $Serveur_Config['en_jouer']) { ?> checked <?php } ?>>
                            <label class="form-check-label" for="en_jouer">
                                Activer la fonctionnalité "Jouer"
                            </label>
                    </div>
                    <br>
                    <input type="hidden" name="text_jouer_menu" value="<?= $Serveur_Config['text_jouer_menu']; ?>">
                    </form>
                    <div class="form-group col-xs-12 floating-label-form-group controls">
                        <label>Nouveau contenu :</label>
                        <textarea rows="10" id="jouercontent" class="form-control" name="content"><?= htmlspecialchars_decode($Serveur_Config['text_jouer_menu']); ?></textarea>
                        <p class="help-block text-danger"></p>
                    </div>
                    <p class="text-center">
                        <button type="button" class="btn btn-secondary "
                        data-toggle="modal" data-target="#modalJouer"
                        >Tester la fenêtre</button>
                        <!--<button type="button" class="btn btn-warning ajax-simpleSend"
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="enjouermodal" data-reload="true"
                        ><?php echo ($Serveur_Config['en_jouer'] == true) ?  "Désactiver" :  "Activer"; ?> la fonctionnalité</button>-->
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="jouermodal" data-tosend="#jouerform" data-useform="true"
                        >Sauvegarder</button></p>
                </div>
            </div><br>
            
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Réglages de la base de données</h6>
                </div>
                <div class="card-body">
                <p style="text-align: justify;"><span style="color: red"><strong>Attention ! Toute mauvaise manipulation rendrait l'interface totalement inaccessible.</strong></span><br> Dans ce cas, le seul moyen de réparer cette dernière est de modifier le fichier "bdd.ini", situé dans le dossier "config" du serveur web.</p>
                    <hr>
                    <form method="post" id="bddform">
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
                        <p class="text-right">
                        <button type="button" class="btn btn-warning ajax-simpleSend"
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="get" data-func="testDBConnection" data-tosend="#bddform" data-useform="true" data-showreturn="true"
                        >Tester la connexion</button>
                        <button type="button" class="btn btn-danger ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="bddconfig" data-tosend="#bddform" data-useform="true" data-showreturn="true"
                        >Sauvegarder</button></p>
                    </form>
                </div>
            </div><br>
            <div class="card shadow lg-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Réglages de TinyMCE</h6>
                </div>
                <div class="card-body">
                    <p class="text-justify"><em>TinyMCE est un éditeur de texte indépendant très pratique qui permet d'utiliser du gras, de l'italique, des puces, des liens ou encore des images dans les posts du forum ou dans l'écriture des pages du CMS. Pour le configurer, <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Editeur-de-texte-TinyMCE">rendez-vous ici (documentation officielle DiamondCMS)</a>.</em></p>
                    <form id="tinymceform">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enable" id="enable" <?php if ($conf_mce['editor']['enable']) { ?> checked <?php } ?>>
                            <label class="form-check-label" for="enable">
                                Activer l'éditeur TinyMCE
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="key" class="col-form-label">API Key</label>
                            <input class="form-control" type="text" name="key" id="key" data-def="<?= $conf_mce['editor']['def_key']; ?>" value="<?= $conf_mce['editor']['key']; ?>">
                        </div>
                        <?php if (($conf_mce['editor']['key'] == $conf_mce['editor']['def_key'] OR empty($conf_mce['editor']['key'])) && $conf_mce['editor']['enable']){ ?>
                            <p id="alerte_mce"><strong><span style="color:red;">Attention !</span> Vous n'avez pas renseigné votre propre API Key. TinyMCE ne peut pas fonctionner correctement. <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Editeur-de-texte-TinyMCE">Mode d'emploi</a></strong></p>
                        <?php } ?>
                        <p class="text-right">
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="tinymceconfig" data-tosend="#tinymceform" data-useform="true" data-reload="true"
                        >Sauvegarder</button></p>
                    </form>
                </div>
            </div><br>
        </div>
    </div><!-- /.rows -->   
</div><!-- /.container-fluid -->
