<?php global $boutique_config, $Serveur_config, $payments;  ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Boutique - Gestion de DediPass</h1>
            <h5>La boutique de DiamondCMS repose sur une monnaie virtuelle que les joueurs peuvent acheter. Pour cela, vous pouvez paramètrer le service de paiement DediPass.</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Configuration
                        </div>
                        <div class="panel-body" class="">
                            <form method="post">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="en_ddp" <?php if ($boutique_config['DediPass']['en_ddp'] == "true") { ?> checked <?php } ?>>
                                    <label class="form-check-label" for="en_ddp">
                                        Activer le paiement par DédiPass
                                    </label>
                                </div><br>
                                <div class="form-group">
                                    <label for="pub_key" class="col-form-label">Clée publique</label>
                                    <input class="form-control" type="text" name="pub_key" id="pub_key" value="<?= $boutique_config['DediPass']['public_key']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="priv_key" class="col-form-label">Clée privée</label>
                                    <input class="form-control" type="text" name="priv_key" id="priv_key" value="<?= $boutique_config['DediPass']['private_key']; ?>">
                                </div>
                                <p class="text-right">
                                <em class="explain">Vous devez remplir tous les champs du formulaire,<br> puis les déselectionner pour pouvoir valider.</em>
                                <br><button type="button" class="ddconf btn btn-info mod_button" data-link="<?= LINK; ?>admin/boutique/xhr/configddp/">Sauvegarder</button></p>
                            </form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-8">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Paiements enregistrés
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($payments)){ ?>
                                <p>Aucun paiement n'a pour le moment été enregistré.</p>
                            <?php }else { ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Id</th>
                                            <th scope="col">Membre</th>
                                            <th scope="col">Code utilisé</th>
                                            <th scope="col">Prix</th>
                                            <th scope="col"><?= $Serveur_Config['Serveur_money']; ?>s</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $p){ ?>
                                        <tr>
                                            <th><?= $p['id']; ?></th>
                                            <th><?= $controleur_def->getPseudo($p['id_user']); ?></th>
                                            <th><?= $p['code']; ?></th>
                                            <th><?= $p['payout']; ?></th>
                                            <th><?= $p['virtual_currency']; ?></th>
                                            <th><?= $p['date']; ?></th>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                
                             <?php } ?>
                        </div>
                    </div>
        </div>
</div>
