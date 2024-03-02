<form id="config" data-apiLink="<?php echo LINK; ?>api/serveurs/">
<section id="etape0" class="step wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.2s" style="display: block; margin-top: 10%; ">
    <h1 class="text-gray-800 text-center">Bienvenue dans votre assistant de configuration !</h1>
    <h2 class="text-center">Vous souhaitez installer un nouveau serveur.</h2> 
    <h5 class="text-center">Ne vous inquiétez pas cette opération n'est pas longue et vous allez être guidés de bout en bout par cet assistant virtuel !</h5>
    <hr>
    <p class="text-center">Avant de poursuivre, vous devez evidemment avoir accès à la configuration de vos serveurs de jeu, et nous vous recommandons de ne pas réaliser la liaison site-serveur lorsque le serveur connaît une affluence importante (l'idéal est de lancer une maintenance de vos installations).<br>
    <em>Veuillez noter que dans le cadre de cette installation, un message peut être envoyé dans le chat de votre serveur. Celui-ci indique simplement que la configuration de l'addon est en cours, et est nécessaire pour tester le bon fonctionnement de votre configuration.</em></p>
    <p class="text-center"><button data-to="1" class="btn btn-custom btn-lg next">Passer à la suite</button></p>
</section>
<section id="etape1" class="step wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.2s" style="margin-top: 10%;">
    <h1 class="text-center">Pour commencer, il nous faut quelques informations évidentes :</h1>
    <br>
    <div class="form-group">
        <label for="name" class="col-form-label">Nom du serveur :</label>
        <input class="form-control" type="text" name="name" id="name" placeholder="Le nom qui sera affiché tant sur votre interface d'administration que sur tout votre site">
    </div>
    <div class="form-group">
        <label for="desc" class="col-form-label">Description du serveur</label>
        <input class="form-control" type="text" name="desc" id="desc" placeholder="Une petite description de ce qu'est le serveur...">
    </div>
    <p class="text-center">
    <button data-to="0" class="btn btn-secondary btn-lg back"><i class="fas fa-arrow-left"></i></button>   <button data-need="name, desc" data-to="2" class="btn btn-custom btn-lg next"><i class="fas fa-arrow-right"></i></button></p>
</section>
<section id="etape2" class="step wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.2s" style="margin-top: 2%;">
    <h2 class="text-center">Maintenant que vous êtes en forme, quelques informations plus techniques :</h2>
    <p class="text-center"><em>Pour cette partie, il est conseillé de ce suivre ce <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Lien-serveur(s)-de-jeu">mode d'emploi</a>.</em></p>
    <div class="form-group">
        <label for="version" class="col-form-label">IP du serveur <small>(localhost si sur la même machine)</small> :</label>
        <input class="form-control" type="text" name="host" id="host" >
    </div>
    <div class="form-group">
        <label>Jeu</label>
            <select class="form-control" name="game" id="game">
            <?php if (defined("DServerLinkGamesSupported")) {
            foreach(DServerLinkGamesSupported as $g){ ?>
                <option <?php echo ($g=="Minecraft-Java") ? "selected" : ""; ?> value="<?= $g; ?>"><?= $g; ?></option>
            <?php }
            } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="version" class="col-form-label">Version <small>(ou mode de jeu pour GMod)</small> :</label>
        <input class="form-control" type="text" name="version" id="version" >
    </div>
    <div class="form-group" id="querybloc">
        <label for="queryport" class="col-form-label">Port (Query) :</label>
        <input class="form-control" type="number" name="queryport" id="queryport" value="25565">                            
        <small class="form-text text-muted">La valeur est préremplie par le système mais vous devez vérifier que cette dernière est correcte avec votre configuration.</small>
    </div>
    <div class="form-group">
        <label for="rconport" class="col-form-label">Port (Rcon ou JSONAPI) :</label>
        <input class="form-control" type="number" name="rconport" id="rconport" value="25575">
        <small class="form-text text-muted">La valeur est préremplie par le système mais vous devez vérifier que cette dernière est correcte avec votre configuration.</small>
    </div>
    <div class="form-group JSON_hide">
        <label for="rconport" class="col-form-label">Utilisateur (JSONAPI) :</label>
        <input class="form-control" type="text" id="jsonsalt" value="Diamond-ServerLink" disabled>
        <small class="form-text text-muted">Pour information : pour utiliser JSONAPI avec DiamondServerLink, vous devez renseigner dans votre configuration ce nom d'utilisateur.</small>
    </div>
    <div class="form-group">
        <label for="password" class="col-form-label">Mot de passe (Rcon ou JSONAPI) :</label>
        <input class="form-control" type="text" name="password" id="password">
    </div>
    <div class="form-group JSON_hide">
        <label for="rconport" class="col-form-label">Salt (JSONAPI) :</label>
        <input class="form-control" type="text" id="jsonsalt" value="DiamondSALT" disabled>
        <small class="form-text text-muted">Pour information : pour utiliser JSONAPI avec DiamondServerLink, vous devez renseigner dans votre configuration ce salt.</small>
    </div>
    <p class="text-center"><button data-to="1" class="btn btn-secondary btn-lg back"><i class="fas fa-arrow-left"></i></button>   <button data-need="host, game, version, queryport, rconport, password" data-to="3" class="btn btn-custom btn-lg next"><i class="fas fa-arrow-right"></i></button></p>
</section>
<section id="etape3" class="step wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.2s" style="margin-top: 10%;">
    <h1 class="text-center">Maintenant, testons votre configuration !</h1>
    <p class="text-center"><em>Veuillez patienter, les tests sont automatiques...</em></p>
    <br>
    <h5>Test du Query :</h5>
    <p id="queryTest"><img src="<?= LINK; ?>getimage/gif/-/ajax-loader" alt="loading" /> Chargement en cours...</p>
    <p class="queryEchec text-justify"><em>Cette erreur nous empêche de poursuivre l'installation de votre serveur de jeu. Vous devez vérifier la configuration de ce dernier (les ports en particulier, et le fait qu'il soit bien allumé) et relire les informations que vous nous avez transmises. Prenez votre temps, <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Lien-serveur(s)-de-jeu">relisez notre guide</a> et contactez nous en cas de problème.</em></p>
    <p class="queryEchec text-center"><button class="btn btn-custom queryRetry">Relancer le test</button></p>
    <br>
    <h5>Test du RCon :</h5>
    <p id="rconTest">En attente d'un résultat favorable du test Query.</p>
    <p class="rconEchec text-justify"><em>Cette erreur nous empêche de poursuivre l'installation de votre serveur de jeu. Vous devez vérifier la configuration de ce dernier (les ports en particulier, et le fait qu'il soit bien allumé) et relire les informations que vous nous avez transmises. Prenez votre temps, <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Lien-serveur(s)-de-jeu">relisez notre guide</a> et contactez nous en cas de problème.</em></p>
    <p class="rconEchec text-center"><button class="btn btn-custom rconRetry">Relancer le test</button></p>
    <p class="text-center">
    <button data-to="2" class="btn btn-secondary btn-lg back"><i class="fas fa-arrow-left"></i></button>   <button data-to="4" class="btn btn-custom btn-lg next" id="disbtn" disabled><i class="fas fa-arrow-right"></i></button></p>
</section>
<section id="etape4" class="step wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.2s" style="margin-top: 10%;">
    <h1 class="text-center">Félicitations, votre serveur est prêt à être ajouté !</h1>
    <p class="text-center">Il ne reste plus qu'à assigner une image à ce nouveau serveur.</p>
    <br>
    <p class="text-center">
        <input type="button" id="dic_launcher" data-whereisdic="<?php echo LINK . "views/themes/" . $Serveur_Config['theme'] . "/js/plugins/listener/" ;?>" 
        data-wherearefiles="<?php echo LINK . "API/admin/get/uploadedImgs/" ;?>"
        data-imgWidth="1200" data-imgHeight="676"
        data-callback="allowend"
        data-resetcallback="stopend"
        class="btn btn-lg btn-custom" />
    </p>
    <br />
    <p class="text-center">
    <button data-to="0" class="btn btn-secondary btn-lg back"><i class="fas fa-arrow-left"></i></button>   <button id="end" class="btn btn-custom btn-lg ajax-simpleSend"
    data-api="<?= LINK; ?>api/" data-module="serveurs/" data-verbe="set" data-func="addserver" 
    data-tosend="#config" data-useform="true" data-reload="true" disabled
    >Terminer</button></p>
</section>
</form>
<style>
.step{
    display:none;
    width: 70%; 
    margin: auto;
}
#loader {
    display: none;
}
.queryEchec, .JSON_hide {
    display: none;
}
</style>
<p id="loader"><img src="<?= LINK; ?>getimage/gif/-/ajax-loader" alt="" /> Chargement en cours...</p>
