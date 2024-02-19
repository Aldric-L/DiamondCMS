<?php global $permissions; //var_dump($permissions); ?>
<div class="container-fluid">
    <h1 class="h3 mb-0 text-gray-800">Permissions des comptes utilisateurs</h1>
    <p class="mb-4">Gestion des permissions associées à chaque type de compte utilisateur.</p>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom"><i class="fa fa-info-circle fa-fw"></i> Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <p class="text-justify">DiamondCMS inclu un système de rôle pour les utilisateurs. A chaque rôle correspond des permissions qui doivent être définies avec précaution. Afin de vous laisser la plus grande liberté, le système DiamondCMS fonctionne selon une pondération des permissions. A chaque itération du poids, le poids supérieur hérite des permissions déjà allouées aux rôles à la pondération inférieure.</p>
                    <br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Poids</th>
                                <th scope="col">Permissions associées</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>0</strong></td>
                                <td>Il s'agit du poids minimal, offert par défaut à tous les utilisateurs. Il n'offre aucune permission particulière.</td>
                            </tr>
                            <tr>
                                <td><strong>1</strong></td>
                                <td>Ce poids n'offre pas de permissions supplémentaires, mais permet au rôle qui lui est associé d'être indiqué devant le nom de l'utilisateur qui le possède.</td>
                            </tr>
                            <tr>
                                <td><strong>2</strong></td>
                                <td>A ce poids est associé l'autorisation de traiter les tickets de support initiés pas les utilisateurs</td>
                            </tr>
                            <tr>
                                <td><strong>3</strong></td>
                                <td>A ce poids est associé la possibilité de gérer les messages du forum.</td>
                            </tr>
                            <tr>
                                <td><strong>4</strong></td>
                                <td>A ce poids est associé l'autorisation d'éditer les réglages et la configuration du CMS.</td>
                            </tr>
                            <tr>
                                <td><strong>5</strong></td>
                                <td>A ce poids est associé toutes les autorisation, y compris celle de supprimer le site internet.</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom"><i class="fa fa-bars fa-fw"></i> Liste des rôles et des permissions associées</h6>
                </div>
                <div class="card-body">
                    <p style="text-align: justify;">Attention, la suppression d'un rôle est une action dangereuse : si un role est supprimé alors que des utilisateurs le possédent encore, DiamondCMS peut réagir de manière indéterminée.<br>
                    Pour cette raison, il n'est plus possible de supprimer un rôle, mais rien n'oblige à tous les utiliser.<br><br><em>Le rôle par défaut attribué aux nouveaux utilisateurs est indiqué en gras. Veuillez noter que diamond_master ne peut être rôle par défaut.</em></p><br>
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Rôle</th>
                                <th scope="col">Poids</th>
                                <th scope="col">Utilisateurs</th>
                                <th scope="col">Définir par défaut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $p){ ?>
                                <tr id="line_<?php echo $p->getId(); ?>">
                                    <td><?php echo $p->isDefault() ? "<strong>" : ""; ?><?php echo $p->getName(); ?><?php echo $p->isDefault() ? "</strong>" : ""; ?></td>
                                    <td><?php echo $p->getLevel(); ?></td>
                                    <td><?php echo $p->getNbUsers($controleur_def->bddConnexion()); ?></td>
                                    <td><p style="padding: 0; margin: 0;"class="text-center">
                                    <button class="btn btn-custom btn-circle btn-sm ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                            data-module="comptes/" data-verbe="set" data-func="defRole" data-tosend="role_id=<?php echo $p->getId(); ?>" data-reload="true" <?php echo $p->canBeDefault() ? "" : "disabled"; ?>>
                                        <i class="fas fa-user-circle "></i>
                                    </button>
                                    <button class="btn btn-warning btn-circle btn-sm"  data-toggle="modal" data-target="#mode_<?php echo $p->getId(); ?>" <?php echo $p->canBeEdited() ? "" : "disabled"; ?>>
                                        <i class="fas fa-wrench "></i>
                                    </button></p></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom"><i class="fa fa-user-plus fa-fw"></i> Création d'un rôle</h6>
                </div>
                <div class="card-body">
                    <form action="" id="new_role">
                        <div class="form-group">
                            <label for="pseudo" class="col-form-label">Nom du rôle :</label>
                            <input class="form-control" type="text" name="name" id="name" placeholder="Nom affiché">
                        </div>
                        <div class="form-group">
                            <label>Poids associé</label>
                            <select required data-validation-required-message="Merci d'indiquer le poids du rôle." class="form-control" name="level" id="level">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>  
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="def" id="def">
                            <label class="form-check-label" for="enable">
                                Définir comme rôle par défaut
                            </label>
                        </div>
                    </form>
                    <p class="text-right"><button class="btn btn-custom btn-md ajax-simpleSend"
                        data-api="<?= LINK; ?>api/" data-module="comptes/" data-verbe="set" data-func="addRole" data-tosend="#new_role" data-useform="true" data-reload="true">Envoyer</button></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php foreach ($permissions as $p) { if ($p->canBeEdited()){ ?>
<div id="mode_<?php echo $p->getId(); ?>" class="ban_modal modal fade">
  <div class="modal-dialog"" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modification du rôle</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" id="editform_<?php echo $p->getId(); ?>">
            <div class="form-group">
                <label for="pseudo" class="col-form-label">Nom du rôle :</label>
                <input class="form-control" type="text" name="name" id="name" value="<?php echo $p->getName(); ?>">
            </div>
            <div class="form-group">
                <label for="pseudo" class="col-form-label">Poids du rôle :</label>
                <select required data-validation-required-message="Merci d'indiquer le poids du rôle." class="form-control" name="level" id="level">
                    <?php for ($i=0; $i <=5; $i++){
                        echo '<option value="';
                        echo $i . '"'; 
                        if ($i === $p->getLevel())
                            echo " selected";
                        echo ">" . $i . "</option>";
                    }?>
                </select>
            </div>
            <div class="form-group">
                <label for="pseudo" class="col-form-label">Nombre d'utilisateurs du rôle :</label>
                <input class="form-control" type="text" value="<?php echo $p->getNbUsers($controleur_def->bddConnexion()); ?>" readonly>
                <small><i>Dont : 
                <?php foreach ($p->getLinkedAccounts($controleur_def->bddConnexion()) as $key => $a) {
                    echo $a['pseudo'];
                    if ($key != sizeof($p->getLinkedAccounts($controleur_def->bddConnexion()))-1)
                        echo ', ';
                }?></i></small>
            </div>
            <?php if ($p->isDefault()){ ?>
                <p><em>Ce compte est le compte par défaut qui est attribué à tous les nouveaux utilisateurs.</em></p>
            <?php } ?>
            <input type="hidden" class="form-control" id="role_id" name="role_id" value="<?php echo $p->getId(); ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button class="btn btn-custom ajax-simpleSend" data-api="<?= LINK; ?>api/" 
            data-module="comptes/" data-verbe="set" data-func="modifRole" data-tosend="#editform_<?php echo $p->getId(); ?>" data-useform="true" data-reload="true">
            Enregistrer</button>
        </div>
      </div>
  </div>
</div>
<?php } } ?>