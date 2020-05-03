<?php global $notify_page; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Notifications envoyées aux administrateurs</h1>
            <h5>Les 30 notifications envoyés dernièrement avec leur date et leur contenu.</h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-green">
                    <div class="panel-heading">
                            Besoin d'aide ?
                        </div>
                        <div class="panel-body">
                             <p class="title">Vous n'avez jamais vu ces notifications ?</p><br />
                             <p class="text-justify">Lorsque le CMS notifie les administrateurs, il ne le fait qu'une seule fois, c'est à dire que la notification ne s'affiche qu'une fois au premier administrateur connecté.
                             Ainsi, si vous ne connaissez pas ces notifications, c'est qu'il est probable qu'un autre administrateur ait-été notifié avant vous.
                             <br>Si malgré cette explication, vous souhaitez en savoir plus, vous pouvez consulter la documentation à propos des notifications du CMS.</p>
                             <hr>
                             <p><strong class="">Documentation des erreurs :</strong> <em>Disponible d'ici une prochaine mise à jour.</em><br/></p>
                        </div>
                    </div>
        </div>
        <div class="col-lg-8">
        <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Notifications aux administrateur du site
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php foreach($notify_page as $n){ ?>
                                    <a href="<?php echo $n["link"]; ?>" class="list-group-item">
                                    <?php if ($n['type'] == 1){ //CONTACT ?>
                                        <i class="fa fa-comment" aria-hidden="true"></i>
                                        <?php }else if ($n['type'] == 2) { //SUPPORT ?>
                                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                        <?php }else if ($n['type'] == 3) { //ERROR ?>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                        <?php }else { ?>
                                        <i class="fa fa-quote-right" aria-hidden="true"></i>
                                        <?php } ?>
                                      <strong><?php echo $n["title"]; ?></strong> <?php echo $n["content"]; ?>
                                    <span class="pull-right text-muted small"><em>le <?php echo $n['date']; ?></em>
                                    </span>
                                </a>
                                <?php } ?>
                            </div>
                            <!-- /.list-group -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
        </div>
    </div>
            <!-- /.col-lg-12 -->
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