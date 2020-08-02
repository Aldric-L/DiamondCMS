<?php global $current, $page_name, $page_raw; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Gestion des pages - Page <?= $page_name; ?></h1>
            <?php if ($page_raw == "mentions-legales"){ ?>
                <h5>Sur cette page, vous pouvez inscrire le texte affiché sur la page Mentions légales du CMS. Cette page dans le droit français est obligatoire même si vous ne possédez pas de boutique.</h5>
            <?php }else if ($page_raw == "reglement"){ ?>
                <h5>Sur cette page, vous pouvez rédiger un réglement pour vos joueurs.</h5>
            <?php }else if ($page_raw == "cgu"){ ?>
                <h5>Sur cette page, vous pouvez inscrire le texte affiché sur la page CGU/CGV du CMS. Ces pages, dans le droit français, sont obligatoires dès lors que vous possédez une boutique.</h5>
            <?php }else { ?>
                <h5>Sur cette page, vous pouvez modifier et créer des pages pour toujours plus personnaliser votre site internet.</h5>
            <?php } ?>
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
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Nouveau contenu :</label>
                                        <textarea rows="10" id="content" class="form-control" name="content"><?= $current; ?></textarea>
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                    <p style="text-align: right"><button type="submit" class="save_modifs btn btn-success btn-md">Sauvegarder</button> 
                                    <button id="delete" 
                                    data-link="<?= LINK; ?>admin/pages/delete/<?= $page_raw; ?>"
                                    data-redirect="<?= LINK; ?>admin/pages/"
                                     type="submit" class="btn btn-danger btn-md">Supprimer la page</button></p>
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
                            <div class="embed-responsive  embed-responsive-4by3">
                                <iframe src="<?= LINK; ?>/<?= $page_raw; ?>" >
                                </iframe>
                            </div>
                        </div>
                    </div>
        </div>
</div>
