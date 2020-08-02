<?php global $contacts; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Page contact</h1>
            <h5>DiamondCMS fournit un systeme de contact pour permettre, par exemple, à des organismes exterieurs de vous contacter de manière privée. Comme ce formulaire est ouvert à tous (sans comptes), il faut donc répondre à ces demandes par mail.</h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-12">
        <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Demandes de contact enregistrées
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php if (empty($contacts)){ ?>
                                    <p>Il n'y a rien à afficher, aucune demande n'a été trouvée.</p>
                                <?php }else {
                                    foreach($contacts as $c){ ?>
                                        <a href="#" id="contact_line_<?php echo $c['id']; ?>" data="<?php echo $c['id']; ?>" class="list-group-item contact_modal_link">
                                            <strong>Demande de contact par <?php echo $c['name']; ?></strong> (<?php echo $c['email']; ?>)
                                            <span class="pull-right text-muted small"><em>le <?php echo $c['date']; ?></em></span>
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

<?php if(!empty($contacts)) {
    foreach($contacts as $c){ ?>
        <div id="contact_modal_<?php echo $c['id']; ?>" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title">Demande de contact par <?php echo $c['name']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <h3>Demande de <?php echo $c['name']; ?><small> via <?php echo $c['email']; ?> le <?php echo $c['date']; ?></small></h3>
                        <p><?php echo $c['text']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <a href="#" class="supp_contact" id="<?php echo $c['id']; ?>" data="<?= LINK; ?>admin/contact/delete/<?php echo $c['id']; ?>"><button type="button" class="btn btn-danger">Supprimer</button></a>
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

