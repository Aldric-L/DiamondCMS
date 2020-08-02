<?php global $images; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Gestionnaire d'images</h1>
            <h5>DiamondCMS permet à la fois aux utilisateurs, et aux administrateurs de télécharger des images. Cette page vous permet de les gérer, et d'étendre le catalogue d'images disponibles notamment pour les miniatures des serveurs.</h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-5">
            <div class="panel panel-green">
                    <div class="panel-heading">
                            Ajouter une image
                        </div>
                        <div class="panel-body" class="">
                            <form method="POST" action="" enctype="multipart/form-data" class="" id="form_new_news">
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Envoyer une image :</label>
                                        <input type="file" class="form-control-file" placeholder="file" name="img" id="img">
                                    </div>
                                </div>
                                <p style="text-align: right;"><button type="submit" id="submit-all" class="btn btn-success btn-md">Envoyer</button></p>
                            </form>
                            <!--<div class="image_upload_div">
                                <form action="/" class="dropzone" style="background: none repeat scroll 0 0 white; border: 2px dashed #C0C0C0; border-radius: 5px;" id="dz">
                                    <div class="dz-message">
                                        Drop files here or click to upload.<br>
                                        <span class="note">(This is for demo purpose. Selected files are not actually uploaded.)</span>
                                    </div>
                                </form>
                            </div>-->
                        </div>
                    </div>
        </div>
        <div class="col-lg-7">
        <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Images enregistrées à la racine
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <p><em>Ne sont listés ici que les fichiers se trouvant à la racine du dossier img.</em></p>
                            <div class="list-group">
                                <?php if (empty($images)){ ?>
                                    <p>Il n'y a rien à afficher, aucune image n'a été trouvée.</p>
                                <?php }else {
                                    foreach($images as $i){ 
                                        if ($i[6]){ ?>
                                            <a href="#" id="line_<?php echo $i[0]; ?>" data="<?php echo $i[0]; ?>" class="img_preview list-group-item">
                                                <strong><i class="fa fa-lock" aria-hidden="true"></i> <?php echo $i[1]; ?></strong> (<?php echo $i[4]; ?>) - Fichier protégé
                                                <span class="pull-right text-muted small"><em>Dernière modification le <?php echo date("d F Y H:i:s.", $i[3]); ?></em></span>
                                            </a>
                                        <?php } else { ?>
                                            <a href="#" id="line_<?php echo $i[0]; ?>" data="<?php echo $i[0]; ?>" class="img_preview list-group-item">
                                                <strong><?php echo $i[1]; ?></strong> (<?php echo $i[4]; ?>)
                                                <span class="pull-right text-muted small"><em>Dernière modification le <?php echo date("d F Y H:i:s.", $i[3]); ?></em></span>
                                            </a>
                                        <?php } ?>
                                    <?php }
                                } ?>
                                
                            </div>
                            <!-- /.list-group -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
        </div>
    </div>
            <!-- /.col-lg-12 -->

<?php if(!empty($images)) {
    foreach($images as $i){ ?>
        <div id="modal_img_<?php echo $i[0]; ?>" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title"><?php echo $i[1]; ?></h3>
                    </div>
                    <div class="modal-body">
                        <img class="img-rounded img-responsive" src="<?= LINK; ?>views/uploads/img/<?php echo $i[1];?>"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <?php if ($i[6]){ ?>
                            <button type="button" disabled class="btn btn-danger">Supprimer</button>
                        <?php }else { ?>
                            <a href="#" class="supp_img" id="<?php echo $i[0]; ?>" data="<?= LINK; ?>admin/images/delete/<?php echo $i[0]; ?>/<?php echo $i[5]; ?>"><button type="button" class="btn btn-danger">Supprimer</button></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>                   
    <?php }
} ?>
</div>
<style>
.title {
    font-size: 26px;
    margin-bottom: 0;
}
.important_explain{
    color: green;
    font-size: 16px;
}
</style>

