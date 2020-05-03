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
                        <th scope="col">Status</th>
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
          <h5 class="text-danger">Attention ! Vous vous apprétez à éffectuer une action irréversible !</h5>
          <p>Êtes vous sûr de vouloir bannir définitivement <?php echo $c['pseudo'];?> ?</p>
          <br />
          <p>Veuillez indiquer une raison au bannissement : <input type="text" class="reason" id="reason_<?= $c['id']; ?>"></input></p>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default close_mod" data="<?= $c['id']; ?>">Fermer</button>
          <!--<a href="#" id="ban_button">--><button type="button" class="btn btn-danger ban_button" data="<?= $c['id']; ?>">Valider</button><!--</a> -->
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
                    <div class="form-group">
                        <label for="pseudo_inscription" class="col-form-label">Pseudo du membre :</label>
                        <input class="form-control" type="text" name="pseudo" id="pseudo_<?= $c['id']; ?>" placeholder="<?= $c['pseudo']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email_inscription" class="col-form-label">Email du membre</label>
                        <input class="form-control" type="email" id="email_<?= $c['id']; ?>" name="email" placeholder="<?= $c['email']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email_inscription" class="col-form-label">Nombre d'unités de monnaie virtuelle</label>
                        <input class="form-control" type="number" min="0" id="money_<?= $c['id']; ?>" name="money">
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
                                    <input type="checkbox" class="form-check-input" name="isban" id="isban_<?= $c['id']; ?>" checked >   Débannir ?
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
                                    <input type="checkbox" class="form-check-input" name="isban" id="isban_<?= $c['id']; ?>" checked disabled>   Débannir ?
                                </label>
                                <small class="form-text text-muted">Raison du bannissement : <?= $c['r_ban']; ?></small>
                            </div>
                        <?php }else {  ?>
                            <input type="hidden" value="no" name="isban" id="isban_<?= $c['id']; ?>">
                        <?php }  ?>
                    <?php } ?>
                </form>
            </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default close_modify_mod" data="<?= $c['id']; ?>">Fermer</button>
          <button type="button" class="btn btn-success mod_button" data="<?= $c['id']; ?>">Valider</button>
        </div>
      </div>
  </div>
</div>
<?php } ?>
<script>
var id;
$(".modify_first_button").click(function(){
    id = $(this).attr("data");
    $("#modify_modal_" + $(this).attr("data")).modal('show');
});
$(".close_modify_mod").click(function(){
    id = $(this).attr("data");
    $("#modify_modal_" + $(this).attr("data")).modal('hide');
});
$(".mod_button").click(function(){
  var link = $(this).attr("data");
  if ($('#isban_' + link).val() == "no"){
    $.ajax({
        url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/list/mod/' + link + '/',
        type : 'POST',
        data : 'money=' + $('#money_' + link).val() + "&role=" + $('#role_' + link).val(),
        dataType : 'html',
        success: function (data_rep) {
            console.log("rep :" + data_rep);
        if (data_rep != "Success"){
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
        }else {    
            $(".content-page").hide();               
            $(".ban_user").show();
        }
        },
        error: function() {
        alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
    });
  }else {
    $.ajax({
        url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/list/mod/' + link + '/',
        type : 'POST',
        data : 'money=' + $('#money_' + link).val() + "&role=" + $('#role_' + link).val()+ "&isban=" + $('#isban_' + link).val(),
        dataType : 'html',
        success: function (data_rep) {
            console.log("rep :" + data_rep);
        if (data_rep != "Success"){
            alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
        }else {    
            $(".content-page").hide();               
            $(".ban_user").show();
        }
        },
        error: function() {
        alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
        }
    });
  }
  $("#ban_modal_" + link).modal('hide');
  location.reload();
});


$(".first_ban_button").click(function(){
    id = $(this).attr("data");
    $("#ban_modal_" + $(this).attr("data")).modal('show');
});
$(".close_mod").click(function(){
    id = $(this).attr("data");
    $("#ban_modal_" + $(this).attr("data")).modal('hide');
});
$(".ban_button").click(function(){
  var link = $(this).attr("data");
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/list/ban/' + link + '/',
    type : 'POST',
    data : 'reason=' + $('#reason_' + link).val(),
    dataType : 'html',
    success: function (data_rep) {
        console.log(data_rep);
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }else {    
        $(".content-page").hide();               
        $(".ban_user").show();
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
  $("#is_ban_" + link).html("<span style=\"color: red;\">Banni</span>");
  $("#ban_modal_" + link).modal('hide');
});
</script>