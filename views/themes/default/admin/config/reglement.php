<?php global $current, $SCg; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS - Page Règlement</h1>
            <h5>Sur cette page, vous pouvez inscrire le texte affiché sur la page Règlement du CMS. Cette page peut être désactivée.</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-7">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Modifier la page
                        </div>
                        <div class="panel-body" class="">
                            <form action"" method="POST">
                                <div class="row control-group col-xs-12 floating-label-form-group controls">
                                    <div class="form-check">
                                        <?php if ($SCg['en_reglement'] == true) { ?>
                                                <input class="form-check-input" name ="en_reglement" type="checkbox" id="vote_en" >
                                                <label class="form-check-label" for="en_relement">
                                                    Désactiver cette page
                                                </label>
                                            <?php }else { ?>
                                                <input class="form-check-input" type="checkbox" name ="en_reglement" id="vote_en" >
                                                <label class="form-check-label" for="en_relement">
                                                    Activer cette page
                                                </label>
                                            <?php } ?>
                                    </div>
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nouveau contenu :</label>
                                        <textarea rows="10" id="content" class="form-control" name="content"><?= $current; ?></textarea>
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                    <p style="text-align: right"><button type="submit" class="save_modifs btn btn-success btn-md">Envoyer</button></p>
                            </form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-5">
            <div class="panel panel-green">
                    <div class="panel-heading">
                            Version actuelle
                        </div>
                        <div class="panel-body" class="">
                            <?php if (!$SCg['en_reglement']){ ?>
                                <p>Impossible de charger la visualisation : la page n'est pas activée.</p>
                            <?php }else { ?>
                                <div class="embed-responsive  embed-responsive-4by3">
                                    <iframe src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>/reglement" >
                                    </iframe>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
        </div>
</div>
