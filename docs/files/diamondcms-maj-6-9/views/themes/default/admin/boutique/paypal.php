<?php global $boutique_config, $config, $payments, $Serveur_config, $offres; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Boutique - Gestion de PayPal</h1>
            <h5>La boutique de DiamondCMS repose sur une monnaie virtuelle que les joueurs peuvent acheter. Pour cela, vous pouvez paramètrer des offres PayPal (plus rentable pour vous que DediPass par exemple).</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Offres Paypal et configuration
                        </div>
                        <div class="panel-body" class="">
                            <form method="post">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="en_paypal" <?php if ($boutique_config['PayPal']['en_paypal'] == "true") { ?> checked <?php } ?>>
                                    <label class="form-check-label" for="en_paypal">
                                        Activer le paiement par PayPal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="sandbox" <?php if ($boutique_config['PayPal']['sandbox'] == "true") { ?>  checked <?php } ?>>
                                    <label class="form-check-label" for="sandbox">
                                        Activer le mode Sandbox de PayPal
                                    </label>
                                </div><br>
                                <div class="form-group">
                                    <label for="money" class="col-form-label">Monnaie utilisée</label>
                                    <input class="form-control" type="text" name="money" id="money" value="<?= $boutique_config['PayPal']['money']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="id_pp" class="col-form-label">Id PayPal</label>
                                    <input class="form-control" type="text" name="id_pp" id="id_pp" value="<?= $boutique_config['PayPal']['id']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="secret_pp" class="col-form-label">Secret PayPal</label>
                                    <input class="form-control" type="text" name="secret_pp" id="secret_pp" value="<?= $boutique_config['PayPal']['secret']; ?>">
                                </div>
                                <p class="text-right">
                                <em class="explain">Vous devez remplir tous les champs du formulaire,<br> puis les déselectionner pour pouvoir valider.</em>
                                <br><button type="button" class="ppconf btn btn-info mod_button" data-link="<?= LINK; ?>admin/boutique/xhr/configpaypal/">Sauvegarder</button></p>
                            </form>
                            <hr>
                            <form method="post">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Titre de l'offre</label>
                                    <input class="form-control" type="text" name="name" id="name" placeholder="Il doit être assez court, et sert à différencier les offres.">
                                </div>
                                <div class="form-group">
                                    <label for="prix" class="col-form-label">Prix en <?= $Serveur_Config['money_name']; ?></label>
                                    <input class="form-control" type="number" name="prix" id="prix" min="0" >
                                </div>
                                <div class="form-group">
                                    <label for="nb" class="col-form-label">Nombre de <?= $Serveur_Config['Serveur_money']; ?></label>
                                    <input class="form-control" type="number" name="nb" id="nb" min="0">
                                </div>
                                <p class="text-right">
                                <button type="button" class="addpp btn btn-info mod_button" data-link="<?= LINK; ?>admin/boutique/xhr/addpaypal/">Ajouter</button></p>
                            </form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-8">
                <div class="panel panel-default">
                        <div class="panel-heading">
                            Offres enregistrées
                        </div>
                        <div class="panel-body" class="">
                            <?php if (empty($offres)){ ?>
                                <p>Aucun paiement n'a pour le moment été enregistré.</p>
                            <?php }else { ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Id</th>
                                            <th scope="col">Titre</th>
                                            <th scope="col">Prix</th>
                                            <th scope="col"><?= $Serveur_Config['Serveur_money']; ?>s</th>
                                            <th scope="col">UUID</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($offres as $o){ ?>
                                        <tr id="line_<?= $o['id']; ?>">
                                            <th><?= $o['id']; ?></th>
                                            <th><?= $o['name']; ?></th>
                                            <th><?= $o['price']; ?><?= $Serveur_Config['money']; ?></th>
                                            <th><?= $o['tokens']; ?></th>
                                            <th><?= $o['uuid']; ?></th>
                                            <th><button class="btn btn-danger btn-sm del_offre" data-id="<?= $o['id']; ?>"
                                            data-link="<?= LINK; ?>admin/boutique/xhr/delete_offre/<?php echo $o['id']; ?>">Supprimer</button></th>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                
                             <?php } ?>
                            </div>
                    </div>

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
                                            <th scope="col">Id PayPal</th>
                                            <th scope="col">Membre</th>
                                            <th scope="col">Email Paypal</th>
                                            <th scope="col">Prix</th>
                                            <th scope="col">Date</th>
                                            <th scope="col"><?= $Serveur_Config['Serveur_money']; ?>s</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $p){ ?>
                                        <tr>
                                            <th><?= $p['payment_id']; ?></th>
                                            <th><?= $controleur_def->getPseudo($p['user']); ?></th>
                                            <?php if ($p['payment_status'] == "created"){ ?>
                                                <th><span style="color: red;">Paiement inachevé</span></th>
                                            <?php }else { ?>
                                                <th><?= $p['payer_email']; ?></th>
                                            <?php } ?>
                                            <th><?= $p['payment_amount']; ?></th>
                                            <th><?= $p['payment_date']; ?></th>
                                            <th><?= $p['money_get']; ?></th>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                
                             <?php } ?>
                        </div>
                    </div>
        </div>
</div>
