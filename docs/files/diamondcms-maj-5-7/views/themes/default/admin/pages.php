<?php global $pages, $footer_pages, $total_f_pages, $header_md, $links, $available_pages; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Gestion des pages</h1>
            <h5>Sur cette page, vous pouvez créer des pages personnalisées, et définir l'organisation des barres de navigation (en haut et en bas de chaque page).</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Ajouter une page
                </div>
                <div class="panel-body" class="">
                    <p style="text-align: justify;"><em>DiamondCMS vous permet de créer des pages personnalisées dans lesquelles vous pouvez ecrire du texte, ou insérer des images par exemple. A chaque page est associé un nom d'affichage, et un nom dit "raw", c'est-à-dire sans caractères spéciaux pour pouvoir être utilisé dans l'url par exemple. </em></p>  
                    <form method="post">
                          <div class="form-group">
                            <label for="name" class="col-form-label">Nom d'affichage :</label>
                            <input class="form-control" type="text" name="name" id="name" placeholder="Exemple: Ma Super Page !">
                            <small id="nameHelp" class="form-text text-muted">Il peut admettre des espaces et des caractères spéciaux.</small>
                          </div>
                          <div class="form-group">
                            <label for="name_raw" class="col-form-label">Nom "raw" :</label>
                            <input class="form-control" type="text" id="name_raw" name="name_raw" placeholder="Exemple: ma-super-page">
                            <small id="nameRawHelp" class="form-text text-muted">Il ne doit admettre aucun caractère spécial ni espace (sauf tirets). Vous pouvez laisser ce champ vide : il sera automatiquement completé. Veillez aussi à ce qu'il soit unique puisqu'il permet à DiamondCMS de retrouver la page.</small>
                          </div>
                          <div class="form-group">
                            <label for="fa_icon" class="col-form-label">Optionnel : Icone font-awesome :</label>
                            <input class="form-control" type="text" id="fa_icon" name="fa_icon" placeholder="Exemple : inscrire seulement file pour l'icone fa-file">
                            <small id="faIconHelp" class="form-text text-muted">La liste des icones disponibles est <a href="https://fontawesome.com/v4.7.0/icons/">ici</a></small>
                          </div>
                          <p style="text-align: right;"><button type="submit" class="btn btn-success acc">Valider</button></p>
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Gérer les pages
                </div>
                <div class="panel-body" class="">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Nom "raw"</th>
                                <th>Icone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pages as $p){ ?>
                            <tr>
                                <th><?= $p['name']; ?></th>
                                <th><?= $p['name_raw']; ?></th>
                                <?php if (isset($p['fa_icone']) && !empty($p['fa_icone'])){ ?>
                                    <th><?= $p['fa_icone']; ?></th>
                                <?php }else { ?>
                                    <th>Aucune</th>
                                <?php } ?>
                                <th>
                                <button  
                                    data-link="<?= LINK; ?>admin/pages/delete/<?= $p['name_raw']; ?>"
                                    data-redirect="<?= LINK; ?>admin/pages/"
                                     type="submit" class="delete btn btn-danger btn-sm">Supprimer</button></p>
                                </th>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Ajouter un lien
                </div>
                <div class="panel-body" class="">
                    <p style="text-align: justify;"><em>DiamondCMS vous permet aussi d'ajouter un lien externe vers un site tiers par exemple. Ces liens pourront être utilisés dans le header et dans le footer </em></p>  
                    <form method="post">
                          <div class="form-group">
                            <label for="name" class="col-form-label">Nom d'affichage :</label>
                            <input class="form-control" type="text" name="name_newlink" id="name" placeholder="Exemple: Mon super lien !">
                          </div>
                          <div class="form-group">
                            <label for="name_raw" class="col-form-label">Lien :</label>
                            <input class="form-control" type="text" id="link_newlink" name="link_newlink" placeholder="Exemple: http://un-super-site.com/la-super-page">
                          </div>
                          <p style="text-align: right;"><button type="submit" class="btn btn-success acc">Valider</button></p>
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Gérer les liens
                </div>
                <div class="panel-body" class="">
                <p style="text-align: justify;"><em>Les liens qui ne sont pas précédés par un protocol (ex: http), ou par un nom de domaine, pointent vers des pages internes au CMS (comme la FAQ par exemple). Ces liens concernent par exemple les addons, ou des fonctions secondaires du CMS. Il convient donc d'être précautionneux et de ne pas supprimer des liens vers des fonctions stratégiques. </em></p>  
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Lien</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($links as $l){ ?>
                            <tr>
                                <th><?= $l['titre']; ?></th>
                                <th><?= $l['link']; ?></th>
                                <th>
                                <button  
                                    data-link="<?= LINK; ?>admin/pages/delete_link/<?= $l['id']; ?>"
                                    data-redirect="<?= LINK; ?>admin/pages/"
                                     type="submit" class="delete btn btn-danger btn-sm">Supprimer</button></p>
                                </th>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="panel panel-green">
                <div class="panel-heading">
                    Gestion du footer
                </div> <!-- /. panel-heading -->
                <div class="panel-body" class="">
                            <div class="container-fluid" style="padding-left: 0px; padding-right: 0px;">
                                <p style="text-align: justify;"><em>Cliquez sur les flèches latérales pour modifier l'ordre des liens du footer. La sauvegarde est automatique.</em></p>
                                <hr>
                                <div class="rows">
                                    <div class="col-lg-9 list-links" data-nb="<?= sizeof($controleur_def->getFooterPages()); ?>" data-link="<?= LINK; ?>admin/pages/order/footer/" data-type="footer">
                                        <?php $i = 0; foreach ($footer_pages as $p){ 
                                            if ($p['disabled'] == 0){ ?>
                                            <div class="item-link" data-id="<?= $p['id']; ?>" style="float: left; padding-left: 6px; padding-right: 6px; padding-top: 12px;" data-pos="<?= $i; ?>" name="<?= $i; ?>" id="link_<?= $p['id']; ?>">
                                                <p style="text-align: center;">
                                                <a class="no arraw-moove" data-dir="left" data-id="<?= $p['id']; ?>"><strong><i class="fa fa-angle-double-left"></i></strong></a>  
                                                <?= $p['titre']; ?> (<a class="delete_link" data-link="<?= LINK; ?>admin/pages/footer_del/<?= $p['id']; ?>" style="color: red;"><strong><i class="fa fa-trash-o" aria-hidden="true"></i></strong></a>)  
                                                <a class="no arraw-moove" data-dir="right" data-id="<?= $p['id']; ?>"><strong><i class="fa fa-angle-double-right"></i></strong></a>
                                                </p>
                                            </div>
                                        <?php $i++; } } ?>
                                    </div><!-- /. col-lg-9 -->
                                    <div class="col-lg-3" style="border-left: 2px solid #eee; ">
                                        <p><em>Ajouter des liens :</em></p>
                                        <?php foreach ($footer_pages as $p){ 
                                            if ($p['disabled'] == 1){ ?>
                                            <div class="item-link-left" style="float: left; width: 100%;" data-pos="<?= $i; ?>" name="<?= $i; ?>" id="link_<?= $p['id']; ?>">
                                                <p style="text-align: left;">
                                                <a class="no arraw-moove-back"  data-redirect-link="<?= LINK; ?>admin/pages/" data-link="<?= LINK; ?>admin/pages/footer_add/<?= $p['id']; ?>" data-id="<?= $p['id']; ?>"><strong><i class="fa fa-angle-double-left"></i></strong></a>  
                                                <?= $p['titre']; ?>
                                                </p>
                                            </div>
                                        <?php } }?>
                                    </div><!-- /. col-lg-3 -->
                                </div><!-- /. rows -->
                            </div><!-- /. container-fluid -->
                </div><!-- /. panel-body -->
            </div><!-- /. panel -->
        <div class="panel panel-green">
                    <div class="panel-heading">
                            Gestion du header
                        </div>
                        <div class="panel-body" class="">
                            <div class="container-fluid" style="padding-left: 0px; padding-right: 0px;">
                                <p style="text-align: justify;"><em>Dans cet encadré, vous pouvez <strong>seulement créer des menus déroulants et les modifier.</strong> Vous pouvez aussi <strong>ajouter des liens vers des pages du CMS ou vers des sites externes directement dans la barre de navigation supérieur (header).</strong> Il n'est, contrairement au footer, pas possible de modifier l'ordre des autres liens.</em><br>
                                <br>
                                <em>Cliquez sur les flèches latérales pour modifier l'ordre des liens dans les menus déroulants. La sauvegarde est automatique.</em></p>
                                <div class="rows">
                                    <div class="col-sm-2">
                                    </div>
                                    <div class="col-sm-3">
                                        <button class="btn btn-info hlink" <?php if (empty($available_pages)){ ?> disabled <?php } ?>>Ajouter un lien au header</button>
                                        <select style="display: none" data-link="<?= LINK; ?>admin/pages/header_newmdlink" id="links_available" class="form-control">
                                            <?php foreach ($available_pages as $ap){ ?>
                                                <option data-val="<?= $ap['link']; ?>"><?= $ap['titre'];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                    <div class="col-sm-3">
                                    <button class="btn btn-success newmd" data-link="<?= LINK; ?>admin/pages/header_newmd">Créer un nouveau menu déroulant</button>

                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>
                                <br>
                                <br>
                                <hr>
                                    <?php if (empty($header_md)){ ?>
                                        <p style="text-align: center;"><em>Aucun menu déroulant n'a pour le moment été créé. Ajoutez-en un !</em></p>
                                    <?php }else {
                                        foreach ($header_md as $md){ ?>
                                            <div class="rows" id="md_<?= $md['id']; ?>">
                                                <div class="col-lg-7">
                                                    <h4 id="name_md_<?= $md['id']; ?>">
                                                    <?php if ($md['is_menu']){ ?>
                                                        Menu déroulant :
                                                    <?php }else { ?>
                                                        Lien simple :
                                                    <?php } ?>
                                                    <strong><?= $md['name']; ?></strong>
                                                    </h4>
                                                </div>
                                                <div class="col-lg-5">
                                                <p style="text-align: right;">
                                                    <?php if ($md['is_menu']){ ?>
                                                    <button class="btn btn-warning rename_md" data-link="<?= LINK; ?>admin/pages/header_renamemd/<?= $md['id']; ?>" data-id="<?= $md['id']; ?>">Renommer</button> 
                                                    <?php } ?>
                                                    <button class="btn btn-danger delete_md" data-link="<?= LINK; ?>admin/pages/header_delmd/<?= $md['id']; ?>" data-id="<?= $md['id']; ?>">Supprimer</button></p>
                                                </div>
                                                <br>
                                                <?php if ($md['is_menu']){ ?>
                                                <div class="col-lg-9 pages-list-md list-links" data-nb="<?= sizeof($md['pages']); ?>" data-link="<?= LINK; ?>admin/pages/order/header/" data-md="<?= $md['id']; ?>" data-type="header">
                                                    <?php $i = 0; foreach ($md['pages'] as $page) { ?>
                                                            <div class="item-link" data-id="<?= $page['id']; ?>" style="margin-left: auto; margin-right: auto;" data-pos="<?= $i; ?>" name="<?= $i; ?>" id="link_<?= $page['id']; ?>">
                                                                <p data-pos="<?= $page['pos']; ?>" data-id="<?= $page['id']; ?>" style="text-align: center;">
                                                                    <a class="no arraw-moove" data-dir="left" data-id="<?= $p['id']; ?>"><strong><i class="fa fa-angle-double-up"></i></strong></a>  
                                                                <?= $page['titre']; ?>
                                                                (<a class="delete_link" data-link="<?= LINK; ?>admin/pages/header_del/<?= $page['id']; ?>" style="color: red;"><strong><i class="fa fa-trash-o" aria-hidden="true"></i></strong></a>)
                                                                    <a class="no arraw-moove" data-dir="right" data-id="<?= $p['id']; ?>"><strong><i class="fa fa-angle-double-down"></i></strong></a>  
                                                                </p>
                                                                <hr class="header-divider" style="width: 50%; margin-left: auto; margin-right: auto;" >
                                                            </div>
                                                    <?php $i++; } ?>
                                                    <div class="no_pages" data-md="<?= $md['id']; ?>" <?php if (!empty($md['pages'])){ ?> style="display: none;" <?php } ?>>
                                                        <p style="text-align: center;">Aucun lien n'a été ajouté à ce menu déroulant.<br>
                                                        <em>Choisissez dans la liste à droite les liens que vous souhaitez ajouter.</em></p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3" style="border-left: 2px solid #eee; ">
                                                    <p><em>Ajouter des liens :</em></p>
                                                    <?php foreach ($md['available_pages'] as $p){ ?>
                                                        <div class="item-link-left" style="float: left; width: 100%;" data-pos="" name="" id="link_<?= $p['id']; ?>">
                                                            <p style="text-align: left;">
                                                            <a class="no arraw-moove-back" data-redirect-link="<?= LINK; ?>admin/pages/" data-link="<?= LINK; ?>admin/pages/header_add/<?= $md['id']; ?>/<?= $i; ?>/<?= $p['id']; ?>" data-id="<?= $p['id']; ?>"><strong><i class="fa fa-angle-double-left"></i></strong></a>  
                                                            <?= $p['titre']; ?>
                                                            </p>
                                                        </div>
                                                    <?php }?>
                                                </div><!-- /. col-lg-3 -->
                                                <?php } ?>
                                                <div class="col-lg-12">
                                                    <hr>
                                                </div>
                                                
                                            </div>
                                        <?php }
                                    }
                                    ?>
                            </div><!-- /. container-fluid -->
                        </div><!-- /. panel-body -->
        </div><!-- /. panel -->
    </div><!-- /. col-lg-8 -->
    </div><!-- /. rows -->
</div><!-- /. container -->
