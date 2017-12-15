<?php global $news; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">News du Serveur</h1>
            <h5>DiamondCMS fournit un systeme de news pour votre communauté. Vous pouvez ajouter ou supprimer une news. Les news sont affichées sur la page d'accueil.</h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-7">
            <div class="panel panel-green">
                    <div class="panel-heading">
                            Ajouter une news
                        </div>
                        <div class="panel-body">
                            <form method="POST" class="" id="form_new_news">
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Titre de la news</label>
                                        <input type="text" class="form-control" placeholder="Titre" name="name" id="name" required data-validation-required-message="Merci d'entrer votre nom.">
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 floating-label-form-group controls fallback dropzone" id="dz">
                                    <!--<label>Image</label>
                                    <input type="file" name="file" />-->
                                </div>
                                <div class="row control-group">
                                    <div class="form-group col-xs-12 floating-label-form-group controls">
                                        <label>Message</label>
                                        <textarea rows="5" id="message" class="form-control" name="message"></textarea>
                                        <p class="help-block text-danger"></p>
                                    </div>
                                </div>
                                    <button type="submit" class="btn btn-success btn-lg">Envoyer</button>
                            </form>
                            <form action="/upload-target" class="dropzone"></form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-5">
        <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> News enregistrées
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php if (empty($news)){ ?>
                                    <p>Il n'y a rien à afficher, aucune news n'a été trouvée.</p>
                                <?php }else {
                                    foreach($news as $n){ ?>
                                        <a href="#" id="news_link_modal_<?php echo $n['id']; ?>" data="<?php echo $n['id']; ?>" class="list-group-item news_link_modal">
                                            <strong><?php echo $n['name']; ?></strong> par <?php echo $n['user']; ?>
                                            <span class="pull-right text-muted small"><em>le <?php echo $n['date']; ?></em></span>
                                        </a>
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

<?php if(!empty($news)) {
    foreach($news as $n){ ?>
        <div id="modal_news_<?php echo $n['id']; ?>" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title"><?php echo $n['name']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"><img class="img-rounded" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $n['img'];?>" alt="<?php echo $n['name'];?>" /></p>
                        <h3><?php echo $n['name']; ?><small> par <?php echo $n['user']; ?></small></h3>
                        <p><?php echo $n['content_new']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <a href="#" class="supp_new" id="<?php echo $n['id']; ?>" data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/news/del_news_from_modal/<?php echo $n['id']; ?>"><button type="button" class="btn btn-danger btn_news_del_<?php echo $n['id']; ?>">Supprimer</button></a>
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

