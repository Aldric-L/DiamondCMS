<?php global $comptes, $rolescanbeselected; ?>
<div class="container-fluid">
    <h1 class="h3 mb-0 text-gray-800">Gestion des comptes utilisateurs</h1>
    <p class="mb-4">Gestion des permissions accordées à chaque compte utilisateur.</p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Liste des comptes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col">Pseudo</th>
                                    <th scope="col">Adresse email</th>
                                    <th scope="col">Votes</th>
                                    <th scope="col">Connexions</th>
                                    <th scope="col">Dernière connexion</th>
                                    <th scope="col">Monnaie virtuelle</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comptes as $c) {?>
                                <tr class="line_compte" data-id="<?php echo $c->getId(); ?>">
                                    <th scope="col"><?php echo $c->getPseudo(); ?></th>
                                    <th scope="col"><?php echo $c->getEmail(); ?></th>
                                    <th scope="col"><?php echo $c->getVotes(); ?></th>
                                    <th scope="col"><?php echo $c->getNbConnections(); ?></th>
                                    <th scope="col"><?php echo $c->getLastConnection(); ?></th>
                                    <th scope="col"><?php echo $c->getMoney(); ?></th>
                                    <th scope="col"><?php echo $c->getRoleName(); ?></th>
                                    <th scope="col" id="is_ban_<?php echo $c->getId(); ?>"><span <?php echo ($c->isBanned()) ? 'style="color: red"' : 'class="text-custom"'; ?>><?php echo ($c->isBanned()) ? "Banni" : "Normal"; ?></span></th>
                                    <th scope="col">
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modb_<?php echo $c->getId(); ?>" data-id="<?php echo $c->getId(); ?>"  <?php if (!$c->can_ban()){ ?> disabled="disabled" <?php } ?>>Bannir</button>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#mode_<?php echo $c->getId(); ?>" data-id="<?php echo $c->getId(); ?>" <?php if (!$c->can_edit()){ ?> disabled="disabled" <?php } ?>>Modifier</button>
                                    </th>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php foreach ($comptes as $c) {?>
<div id="modb_<?php echo $c->getId(); ?>" class="ban_modal modal fade">
  <div class="modal-dialog modal-lg"" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Validation requise !</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Êtes vous sûr de vouloir bannir définitivement <strong><?php echo $c->getPseudo(); ?></strong> ?</p>
          <hr>
          <p>DiamondCMS vous rappelle que bannir un utilisateur ayant procédé à un paiement sur la boutique en ligne peut constituer une violation des textes réglementatifs en vigueur en France. Nous vous conseillons de ne pas exclure des utilisateurs possédant encore des tokens sur votre boutique.</p>
          <br />
          <form action="" id="banform_<?php echo $c->getId(); ?>">
            <div class="form-group">
                <label for="reason"><strong>Veuillez indiquer une raison au bannissement :</strong></label>
                <input type="text" class="form-control" id="r_ban" name="r_ban" placeholder="Cette raison sera publique." required>
            </div>
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $c->getId(); ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button class="btn btn-danger ajax-simpleSend" data-api="<?= LINK; ?>api/" 
            data-module="comptes/" data-verbe="set" data-func="ban" data-tosend="#banform_<?php echo $c->getId(); ?>" data-useform="true" data-reload="true">
            Bannir</button>
        </div>
      </div>
  </div>
</div>
<div id="mode_<?php echo $c->getId(); ?>" class="edit_modal modal fade">
  <div class="modal-dialog modal-lg">
      <div class="modal-content" role="document">
            <div class="modal-header">
                <h5 class="modal-title">Modification d'un compte utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="edit_<?php echo $c->getId(); ?>">
                    <div class="container">
                        <div class="row">
                            <div class="col-4">
                                <img class="img-rounded img-responsive" src="<?= LINK; ?>getprofileimg/<?php echo $c->getPseudo(); ?>/175">
                            </div>
                            <!--<form action="" id="edit_<?php echo $c->getId(); ?>">-->
                            <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $c->getId(); ?>">
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="pseudo" class="col-form-label">Pseudo du membre :</label>
                                    <input class="form-control" type="text" name="pseudo" id="pseudo" value="<?php echo $c->getPseudo(); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-form-label">Email du membre</label>
                                    <input class="form-control" type="email" id="email" name="email" value="<?php echo $c->getEmail(); ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <br>
                            </div>
                            <?php if ($c->isUnderPasswordRecovery()): ?>
                            <div class="col-12">
                                <p class="text-center"><em>Une réinitialisation de mot de passe est en cours pour ce compte :</em></p>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Code de réinitialisation du mot de passe :</label>
                                    <input class="form-control" type="text" value="<?php echo ($_SESSION['user']->getLevel() > 4) ? $c->getInfos()["recovery_code"] : "**************"; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Date de fin de validité du code</label>
                                    <input class="form-control" type="text" value="<?php echo $c->getInfos()["recovery_deadline"]; ?>" readonly>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="col-12">
                                <br>
                            </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="money" class="col-form-label">Nombre d'unités de monnaie virtuelle</label>
                                    <input class="form-control" type="number" min="0" value="<?php echo $c->getMoney(); ?>" id="money" name="money">
                                    <small class="form-text text-muted">Valeur actuelle : <?php echo $c->getMoney(); ?>.</small>
                                </div>
                                <div class="form-group">
                                    <label>Rôle</label>
                                    <select required data-validation-required-message="Merci d'indiquer le nouveau rôle." class="form-control" name="role" id="role">
                                        <?php $rolescanbeselected = $c->get_underRoles($controleur_def->bddConnexion()); if (!empty($rolescanbeselected)) { 
                                            foreach($rolescanbeselected as $r){ ?>
                                                <option value="<?= $r['id']; ?>" <?php echo ($r['id'] == $c->getRole()) ? "selected" : ""; ?>><?= $r['name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <small class="form-text text-muted">Rôle actuel : <?php echo $c->getRoleName(); ?>.</small>
                                </div>
                                <?php if ($c->isBanned()){ ?>
                                    <div class="form-group">
                                        <label>Raison du bannissement</label>
                                        <input class="form-control" type="text" value="<?php echo $c->getRBan(); ?>" id="r_ban" name="r_ban">
                                        <small class="form-text text-muted">Motif actuel : <?php echo $c->getRBan(); ?></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Utilisateur à l'origine du bannissement</label>
                                        <input class="form-control" type="text" value="<?php echo ($c->getInfo()['user_id_ban'] != null && $c->getInfo()['user_role_ban'] != null) ? $c->getPseudoById($controleur_def->bddConnexion(), $c->getInfo()['user_id_ban']) . " (en tant que " . $c->getRoleNameById($controleur_def->bddConnexion(), intval($c->getInfo()['user_role_ban'])) . ")" : "Utilisateur inconnu."; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Date du bannissement</label>
                                        <input class="form-control" type="text" value="<?php echo ($c->getInfo()['date_ban'] != null) ? $c->getInfo()['date_ban'] : "Date inconnue."; ?>" readonly>
                                    </div>
                                <?php } ?>
                                <!--</form>-->
                                <hr>
                                <?php $payements = $c->getRealPayements($controleur_def->bddConnexion());
                                    if (empty($payements) || (empty($payements['Dedipass']) && empty($payements['PayPal'])) ){ ?>
                                        <p class="text-center">Aucun achat de monnaie virtuelle n'a été réalisé par ce compte.</p>
                                    <?php }else { ?>
                                    <p class="text-center font-weight-bold">Paiements réels enregistrés en boutique :</p>
                                        <ul>
                                            <?php foreach ($payements['PayPal'] as $p) {  ?>
                                                <li>
                                                <strong>Paiement PayPal d'une valeur de <?php echo $p['payment_amount']; ?> <?php echo $p['payment_currency']; ?></strong> réalisé le <?php echo $p['payment_date']; ?> avec le compte <?php echo $p['payer_email']; ?> en échange de <?php echo $p['money_get']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s).<br> <strong>Numéro de transaction :</strong> <?php echo $p['payment_id']; ?></strong> 
                                                </li>
                                            <?php  }   ?>
                                            <?php foreach ($payements['Dedipass'] as $p) {  ?>
                                                <li>
                                                <strong>Paiement Dedipass d'une valeur de <?php echo $p['payout']; ?> <?= $Serveur_Config['money']; ?></strong> réalisé le <?php echo $p['date']; ?> en échange de <?php echo $p['virtual_currency']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s).<br> <strong>Code de transaction :</strong> <?php echo $p['code']; ?></strong> 
                                                </li>
                                            <?php  }  ?>
                                        </ul>
                                    <?php } ?>
                                <hr>
                                <?php $commandes = $c->getCommandes($controleur_def->bddConnexion());
                                    if (empty($commandes)){ ?>
                                        <p class="text-center">Aucun achat d'articles de la boutique n'a été réalisé par ce compte.</p>
                                    <?php }else { ?>
                                    <p class="text-center font-weight-bold">Achats d'articles enregistrés en boutique :</p>
                                        <ul>
                                            <?php foreach ($commandes as $com) {  ?>
                                            <li>
                                                <strong>Achat de l'article "<?php if ($com['article'] != false) { echo $com['article']['name']; }else{ echo "Inconnu"; } ?>" pour <?php echo $com['price']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong> - 
                                                Numéro de commande : <?php echo $com['uuid']; ?> (passée le <?= $com['date']; ?>)
                                                <?php if ($com['success']){ ?>
                                                    - <span class="text-custom"><strong>Terminée avec succès.</strong></span>
                                                <?php }else { ?>
                                                    - <span style="color: orange;"><strong>Non-encore récupérée totalement.</strong></span>
                                                <?php } ?>
                                            </li>
                                            <?php  }   ?>
                                        </ul>
                            <?php  }   ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <div class="modal-footer text-center">
          <button class="btn btn-light" data-dismiss="modal" data="<?php echo $c->getId(); ?>">Fermer</button>
          <button class="btn btn-warning ajax-simpleSend" <?php if ( $c->can_ban()){ ?> data-api="<?= LINK; ?>api/" 
          data-module="comptes/" data-verbe="set" data-func="startReinitPassword" data-tosend="#edit_<?php echo $c->getId(); ?>" data-useform="true" data-reload="true" <?php }else { ?> disabled <?php } ?>>Réinitialiser le mot de passe</button>
          <?php  if ($c->getprofileimg() != "profiles/no_profile.png"){ ?>
               <button class="btn btn-warning ajax-simpleSend" data-api="<?= LINK; ?>api/" 
               data-module="comptes/" data-verbe="set" data-func="resetPDP" data-tosend="user_id=<?php echo $c->getId(); ?>" data-reload="true">
               Réinitialiser la photo de profil</button>
          <?php } else { ?>
               <button class="btn btn-warning" disabled>Réinitialiser la photo de profil</button>
          <?php } ?>
          <?php if ( $c->can_ban()){ ?>
                <button class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#modb_<?php echo $c->getId(); ?>">
                Bannir l'utilisateur</button>
          <?php } else if ($c->can_deban()) { ?>
                <button class="btn btn-danger ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                data-module="comptes/" data-verbe="set" data-func="deban" data-tosend="user_id=<?php echo $c->getId(); ?>" data-reload="true"
                >Débannir l'utilisateur</button>
          <?php } else { ?>
                <button class="btn btn-danger" disabled="disabled">Bannir l'utilisateur</button>
          <?php } ?>
          <button class="btn btn-custom ajax-simpleSend" data-api="<?= LINK; ?>api/" 
          data-module="comptes/" data-verbe="get" data-func="modifAccount" data-tosend="#edit_<?php echo $c->getId(); ?>" data-useform="true">Enregistrer</button>
        </div>
      </div>
  </div>
</div>
<?php } ?>