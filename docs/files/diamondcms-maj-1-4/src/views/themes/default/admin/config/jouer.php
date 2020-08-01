<?php global $current, $SCg; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS - Fenêtre "jouer"</h1>
            <h5>Sur cette page, vous pouvez inscrire le texte affiché dans le modal jouer du CMS. Cette fonctionnalité peut être désactivée, mais permet à vos joueurs de pouvoir se connecter à votre infrastructure plus facilement.</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-9">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Modifier le contenu
                        </div>
                        <div class="panel-body" class="">
                            <form action"" method="POST">
                                <div class="row control-group col-xs-12 floating-label-form-group controls">
                                    <div class="form-check">
                                        <?php if ($SCg['en_jouer'] == true) { ?>
                                                <input class="form-check-input" name ="en_jouer" type="checkbox" id="en_jouer" >
                                                <label class="form-check-label" for="en_jouer">
                                                    Désactiver cette fonctionnalité
                                                </label>
                                            <?php }else { ?>
                                                <input class="form-check-input" type="checkbox" name ="en_jouer" id="en_jouer" >
                                                <label class="form-check-label" for="en_jouer">
                                                    Activer cette fonctionnalité
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
</div>
