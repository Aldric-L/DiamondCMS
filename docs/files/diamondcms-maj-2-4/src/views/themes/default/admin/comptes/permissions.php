<?php global $permissions; //var_dump($permissions); ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Permissions des comptes utilisateurs</h1>
            <h5>Gestion des permissions associées à chaque type de compte utilisateur.</h5>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-info">
                    <div class="panel-heading">
                            Information
                        </div>
                        <div class="panel-body">
                             <p class="text-justify">DiamondCMS inclu, par défaut, un système de rôle pour les utilisateurs. A chaque rôle correspond des permissions qui doivent être définies avec précaution. Afin de vous laisser la plus grande liberté, le système DiamondCMS fonctionne selon une pondération des permissions.A chaque itération du poids, le poids supérieur hérite des permissions déjà allouées aux rôles à la pondération inférieure.</p>
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
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bars fa-fw"></i> Liste des rôles et des permissions associées
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <p style="text-align: justify;">Attention, la suppression d'un rôle est une action dangereuse : si un role est supprimé alors que des utilisateurs le possédent encore, DiamondCMS peut réagir de manière indéterminée.<br>
                    Pour cette raison, il est conseillé de conserver tous les roles, quite à en garder qui ne sont assignés à aucun utilisateur.</p><br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nom du rôle</th>
                                <th scope="col">Poids associé</th>
                                <th scope="col">Utilisateurs</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $p){ ?>
                                <tr id="line_<?php echo $p['id']; ?>">
                                    <td><?= $p['name'] ?></td>
                                    <td><?= $p['level'] ?></td>
                                    <td><?= $p['nb_users'] ?></td>
                                    <?php if ($p['can_be_deleted']){ ?>
                                        <td><a href="#" class="supp_role" id="<?php echo $p['id']; ?>" data="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>admin/comptes/del_role/<?php echo $p['id']; ?>"><button type="button" class="btn btn-danger btn_role_del_<?php echo $p['id']; ?>">Supprimer</button></a></td>
                                    <?php }else { ?>
                                        <td><a href="#" class="" id="" data=""><button type="button" class="btn btn-danger" disabled>Supprimer</button></a></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-user-plus fa-fw"></i> Création d'un rôle
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <form method="POST" action="" enctype="multipart/form-data" class="" id="">
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Nom du rôle</label>
                                <input type="text" class="form-control" placeholder="Nom affiché" name="name" id="name" required data-validation-required-message="Merci le nom du rôle.">
                                <p class="help-block text-danger"></p>
                            </div>
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
                        <p class="text-right"><button type="submit" class="btn btn-success btn-md">Envoyer</button></p>
                    </form>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    </div>
            <!-- /.col-lg-12 -->
</div>