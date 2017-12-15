<?php global $errors_content; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Erreurs levées aux utilisateurs</h1>
            <h5>Toutes les erreurs envoyés aux utilisateurs, avec leur description, et une aide pour les regler.</h5>
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
                             <p class="title">Vous ne comprenez pas ces erreurs ?</p><br />
                             <p class="text-justify"><strong class="important_explain">Ne vous inquietez pas, nous allons vous aider.</strong> Pour celà, vous pouvez consulter la documentation à propos de erreurs levées
                             par le CMS ou bien, si votre problème persiste, nous contacter via notre support : nous nous ferons un plaisir de vous aider !</p>
                             <hr>
                             <p><strong class="">Documentation des erreurs :</strong> lien<br/>
                             <strong class="">Support du CMS :</strong> lien</P>
                        </div>
                    </div>
        </div>
        <div class="col-lg-8">
        <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Erreurs levées par le systeme
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php foreach($errors_content as $error){ ?>
                                <a href="#" class="list-group-item">
                                    <?php if ($error['0'] == "332 "){?>
                                        <i class="fa fa-lock fa-fw"></i>
                                    <?php }else if ($error[0] == "311 "){ ?>
                                        <i class="fa fa-ban fa-fw"></i>
                                    <?php }else if ($error[0] == "121 "){ ?>
                                        <i class="fa fa-ban fa-cogs"></i>
                                    <?php }else { ?>
                                        <i class="fa fa-warning fa-fw"></i>
                                    <?php } ?>
                                      <?php echo $error[2]; ?> (Erreur n° <?php echo $error['0']; ?>)
                                    <span class="pull-right text-muted small"><em>le <?php echo $error['1']; ?></em>
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