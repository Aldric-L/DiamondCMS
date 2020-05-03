<?php global $tasks, $commandes, $config;  ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Boutique - Tâches et commandes récentes</h1>
            <h5>DiamondCMS est livré avec une boutique : sur celle-ci les utilisateurs peuvent acheter vos articles avec un monnaie virtuelle. Une tâche est une action à exécuter pour que l'acheteur reçoive son dû. (ex: une commande sur un serveur)</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-7">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Commandes récentes
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($commandes)){ ?>
                                <p>Aucune commande n'a été trouvée.</p>
                            <?php }else {
                                foreach ($commandes as $c){ ?>
                                    <a id="line_<?php echo $c['id']; ?>" data="<?php echo $c['id']; ?>" class="list-group-item">
                                        <strong>Commande n°<?php echo $c['id']; ?></strong> (uuid: <?php echo $c['uuid']; ?>) : le <?php echo $c['date']; ?> par <?php echo $c['user']; ?> (Article : <?php echo $c['article']; ?>, <?php echo $c['price']; ?> <?php echo $config['Serveur_money']; ?>(s) )
                                        <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                            <?php if ($c['success'] != true && $c['success'] != 1 ){ ?>
                                            <strong><span style="color: red;">En cours...</span></strong>
                                            <?php }else { ?>
                                            <strong><span style="color: green;">Terminée !</span></strong>
                                            <?php } ?>
                                        </span>
                                    </a>
                                <?php }
                            } ?>
                        </div>
                    </div>
        </div>
        <div class="col-lg-5">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Tâches enregistrées
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($tasks)){ ?>
                                <p>Aucune tâche n'a pour le moment été enregistrée.</p>
                            <?php }else { 
                                foreach ($tasks as $t){ ?>
                                    <a id="line_<?php echo $t['id']; ?>" data="<?php echo $t['id']; ?>" class="list-group-item">
                                        <strong>Commande n°<?php echo $t['id']; ?></strong> Tâche à éxecuter sur le serveur <?php echo $t['cmd']['server_name']; ?>
                                        <span class="pull-right text-muted small" style="margin-top: 0; padding: 0;">
                                            <?php if ($t['done'] != true && $t['done'] != 1 ){ ?>
                                            <strong><span style="color: red;">En cours...</span></strong>
                                            <?php }else { ?>
                                            <strong><span style="color: green;">Terminée !</span></strong>
                                            <?php } ?>
                                        </span>
                                    </a>
                                <?php }
                             } ?>
                        </div>
                    </div>
        </div>
</div>
