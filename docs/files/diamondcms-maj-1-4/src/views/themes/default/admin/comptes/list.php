<?php global $comptes, $rolescanbeselected; //var_dump($comptes); ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Gestion des comptes utilisateurs</h1>
            <h5>Gestion des permissions accordées à chaque compte utilisateur.</h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Pseudo</th>
                        <th scope="col">Adresse email</th>
                        <th scope="col">Nombre de votes</th>
                        <th scope="col">Unités de monnaie virtuelle</th>
                        <th scope="col">Role</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comptes as $c) {?>
                    <tr id="line_compte_<?= $c['id']; ?>">
                        <th scope="col"><?= $c['pseudo']; ?></th>
                        <th scope="col"><?= $c['email']; ?></th>
                        <th scope="col"><?= $c['votes']; ?></th>
                        <th scope="col"><?= $c['money']; ?></th>
                        <th scope="col"><?= $c['role_name']; ?></th>
                        <?php if ($c['is_ban'] == 0 && $c['can_ban'] == true){ ?>
                            <th scope="col" id="is_ban_<?= $c['id']; ?>"><span style="color: green;">Normal</span></th>
                            <th scope="col">
                                <button type="button" class="btn btn-danger first_ban_button" data="<?= $c['id']; ?>">Bannir</button>
                                <?php if ($c['can_modify']){ ?>
                                    <button type="button" class="btn btn-info modify_first_button" data="<?= $c['id']; ?>">Modifier</button>
                                <?php }else { ?>
                                    <button type="button" class="btn btn-info modify_first_button" disabled="disabled">Modifier</button>
                                <?php } ?>
                            </th>
                        <?php } else if ($c['is_ban'] == 0 && $c['can_ban'] != true) { ?>
                            <th scope="col"><span style="color: green;">Normal</span></th>
                            <th scope="col">
                                <button type="button" class="btn btn-danger" disabled="disabled">Bannir</button>
                                <?php if ($c['can_modify']){ ?>
                                    <button type="button" class="btn btn-info modify_first_button" data="<?= $c['id']; ?>">Modifier</button>
                                <?php }else { ?>
                                    <button type="button" class="btn btn-info modify_first_button" disabled="disabled">Modifier</button>
                                <?php } ?>
                            </th>
                        <?php } else { ?>
                            <th scope="col"><span style="color: red;">Banni</span></th>
                            <th scope="col">
                                <button type="button" class="btn btn-danger" disabled="disabled">Bannir</button>
                                <?php if ($c['can_modify']){ ?>
                                    <button type="button" class="btn btn-info modify_first_button" data="<?= $c['id']; ?>">Modifier</button>
                                <?php }else { ?>
                                    <button type="button" class="btn btn-info modify_first_button" disabled="disabled">Modifier</button>
                                <?php } ?>
                            </th>
                        <?php } ?>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php foreach ($comptes as $c) {?>
<div id="ban_modal_<?= $c['id']; ?>" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Validation requise !</h4>
        </div>
        <div class="modal-body">
          <p>Êtes vous sûr de vouloir bannir définitivement <strong><?php echo $c['pseudo'];?></strong> ?</p>
          <br />
          <p>Veuillez indiquer une raison au bannissement : <input type="text" class="form-control reason" id="reason_<?= $c['id']; ?>"></input></p>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default close_mod" data="<?= $c['id']; ?>">Fermer</button>
          <!--<a href="#" id="ban_button">--><button type="button" class="btn btn-danger ban_button" data-link="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/list/ban/" data="<?= $c['id']; ?>">Valider</button><!--</a> -->
        </div>
      </div>
  </div>
</div>
<div id="modify_modal_<?= $c['id']; ?>" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title text-center"><i class="fa fa-sign-in" aria-hidden="true"></i> Modification d'un compte utilisateur</h3>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="container-fluid">
                        <div class="rows">
                            <div class="col-lg-4">
                                <img class="img-rounded img-responsive" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>getprofileimg/<?php echo $c['pseudo']; ?>/175">
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="pseudo" class="col-form-label">Pseudo du membre :</label>
                                    <input class="form-control" type="text" name="pseudo" id="pseudo_<?= $c['id']; ?>" placeholder="<?= $c['pseudo']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-form-label">Email du membre</label>
                                    <input class="form-control" type="email" id="email_<?= $c['id']; ?>" name="email" placeholder="<?= $c['email']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <br><br>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="money" class="col-form-label">Nombre d'unités de monnaie virtuelle</label>
                                    <input class="form-control" type="number" min="0" value="<?= $c['money']; ?>" id="money_<?= $c['id']; ?>" name="money">
                                    <small class="form-text text-muted">Valeur actuelle : <?= $c['money']; ?>.</small>
                                </div>
                                <div class="form-group">
                                    <label>Nouveau rôle</label>
                                    <select required data-validation-required-message="Merci d'indiquer le nouveau rôle." class="form-control" name="role" id="role_<?= $c['id']; ?>">
                                        <?php if (!empty($rolescanbeselected)) { 
                                            foreach($rolescanbeselected as $r){ ?>
                                                <option value="<?= $r['id']; ?>"><?= $r['name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <small class="form-text text-muted">Rôle actuel : <?= $c['role_name']; ?>.</small>
                                </div>
                                <?php if ($c['can_deban']){ ?>
                                    <?php if ($c['is_ban']){ ?>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="isban" id="isban_<?= $c['id']; ?>" >   Débannir ?
                                            </label>
                                            <small class="form-text text-muted">Raison du bannissement : <?= $c['r_ban']; ?>.</small>
                                        </div>
                                    <?php }else {  ?>
                                        <input type="hidden" value="no" name="isban" id="isban_<?= $c['id']; ?>">
                                    <?php }  ?>
                                <?php }else { ?>
                                    <?php if ($c['is_ban']){ ?>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="isban" disabled>   Débannir ?
                                            </label>
                                            <small class="form-text text-muted">Raison du bannissement : <?= $c['r_ban']; ?></small>
                                        </div>
                                    <?php }else {  ?>
                                        <input type="hidden" value="no" name="isban" id="isban_<?= $c['id']; ?>">
                                    <?php }  ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <div class="modal-footer">
          <button class="btn btn-default close_modify_mod" data="<?= $c['id']; ?>">Fermer</button>
          <button class="btn btn-success mod_button" data-link="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/list/mod/" data="<?= $c['id']; ?>">Enregistrer</button>
          <?php if ($c['profile_img'] != "profiles/no_profile.png"){ ?>
               <button class="btn btn-warning supp_profile_img" data-link="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/list/supp_profile_img/" data="<?= $c['id']; ?>">Réinitialiser la photo de profil</button>
          <?php } else { ?>
               <button class="btn btn-warning" disabled>Réinitialiser la photo de profil</button>
          <?php } ?>
          <?php if ($c['is_ban'] == 0 && $c['can_ban'] == true){ ?>
                <button class="btn btn-danger first_ban_button" data-dismiss="modal" data="<?= $c['id']; ?>">Bannir</button>
          <?php } else if ($c['is_ban'] == 0 && $c['can_ban'] != true) { ?>
                <button class="btn btn-danger" disabled="disabled">Bannir</button>
          <?php } ?>
        </div>
      </div>
  </div>
</div>
<?php } ?>