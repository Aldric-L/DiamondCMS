<?php global $faq, $config;  ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS - Page FAQ</h1>
            <h5>Sur cette page, vous pouvez inscrire des questions (et leurs réponses) auxquelles sont souvent confrontés vos utilisateurs.</h5>
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
                            <?php if ($config['en_faq']){ ?>
                                <p><strong>Pour désactiver cette page, cliquez-ici : </strong><button data="<?= LINK; ?>admin/faq/enable" type="submit" class="enable btn btn-danger btn-md">Désactiver</button></p>
                            <?php }else { ?>
                                <p><strong>Pour activer cette page, cliquez-ici : </strong><button data="<?= LINK; ?>admin/faq/enable" type="submit" class="enable btn btn-success btn-md">Activer</button></p>
                            <?php } ?>
                            <hr>
                            <form action"" method="POST">
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nouvelle question :</label>
                                        <input class="form-control" type="text" name="question" id="question">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nouveau contenu :</label>
                                        <textarea rows="10" id="reponse" class="form-control" name="reponse"></textarea>
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                    <p style="text-align: right"><button type="submit" class="save_modifs btn btn-success btn-md">Envoyer</button></p>
                            </form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Questions enregistrées
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($faq)){ ?>
                                <p>Aucune question n'a été enregistrée pour le moment.</p>
                            <?php }else { 
                                foreach ($faq as $f){ ?>
                                    <div id="line_<?= $f['id']; ?>">
                                        <h3><?= $f['question']; ?></h3>
                                        <p><?= $f['reponse']; ?></p>
                                        <p>Pour supprimer cette question : <button id="<?= $f['id']; ?>" data="<?= LINK; ?>admin/faq/delete/" type="submit" class="delete btn btn-danger btn-sm">Cliquez-ici</button></p>
                                        <hr>
                                    </div>
                                <?php }
                             } ?>
                        </div>
                    </div>
        </div>
</div>
