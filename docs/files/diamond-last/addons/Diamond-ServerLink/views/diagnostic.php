<?php global $config_serveurs; ?>
<section id="etape0" class="step wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.2s">
    <h1 class="text-gray-800 text-center">Bienvenue dans votre assistant de configuration !</h1>
    <h2 class="text-center">Diagnostiquons votre installation.</h2> 
    <h5 class="text-center">Cette opération peut prendre quelques instants.</h5>
    <br>
    <hr>
    <?php
    foreach ($config_serveurs as $key => $serv) { ?>
        <div class="server" data-id="<?php echo $serv['id']; ?>" data-link="<?php echo LINK . "api/serveurs/get/diagnostic/"; ?>">
            <h4 class="text-center server-name">Serveur <?php echo $serv['id']; ?> : <strong><?php echo $serv['name']; ?></strong></h4>
            <h5 class="loader" id="loader_<?php echo $serv['id']; ?>"><img src="<?= LINK; ?>views/uploads/img/ajax-loader.gif" alt="loading" /> Chargement en cours...</h5>
            <div class="success" id="success_<?php echo $serv['id']; ?>">
                <h5 class="text-custom"><strong><i class="fa fa-check-circle-o" aria-hidden="true"></i> Fonctionnement normal !</strong></h5>
                <p>Tout fonctionne correctement, tant le Query que le RCon.</p>
            </div>
            <div class="disabled" id="disabled_<?php echo $serv['id']; ?>">
                <h5 class="text-warning"><strong><i class="fa fa-question-circle" aria-hidden="true"></i> Serveur désactivé. </strong></h5>
                <p>Impossible de diagnostiquer le serveur, celui-ci étant désactivé dans la configuration.</p>
                <p class="text-right"><a href="<?php echo LINK; ?>Diamond-ServerLink/config/<?php echo $serv['id']; ?>"><button class="btn btn-sm btn-warning">Accéder à la configuration du serveur</button></a></p>
                <p></p>
            </div>
            <div class="failure" id="failure_<?php echo $serv['id']; ?>">
                <h5 class="text-danger"><strong><i class="fa fa-times-circle" aria-hidden="true"></i> Erreur ! </strong></h5>
                <p class="failure-error" id="failure-error_<?php echo $serv['id']; ?>">Une erreur imprévue est survenue. La requête à l'API s'est mal terminée.</p>
                <p class="failure-help" id="failure-help_<?php echo $serv['id']; ?>"></p>
                <p class="text-right"><a href="<?php echo LINK; ?>Diamond-ServerLink/config/<?php echo $serv['id']; ?>"><button class="btn btn-sm btn-danger">Accéder à la configuration du serveur</button></a></p>
                <p></p>
            </div>
        </div>
        <hr>
    <?php } 
    if (empty($config_serveurs)) { ?>
    <p class="text-center"><em>Aucun serveur n'a pour l'instant été configuré.</em></p>
    <?php } ?>
    <br><br>
</section>
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