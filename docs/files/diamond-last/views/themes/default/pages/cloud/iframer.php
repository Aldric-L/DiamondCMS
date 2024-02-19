<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiamondCloud</title>
    <meta name="author" content="DiamondCMS, pour <?= $Serveur_Config['Serveur_name']; ?>">
    <link rel="icon" type="image/png" href="<?= LINK; ?>views/uploads/img/<?= $Serveur_Config['favicon']; ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.6/css/ionicons.min.css" />
    <link rel="stylesheet" href="<?php echo LINK . "views/themes/" . $Serveur_Config['theme'] . "/CSS/cloud/iframer.css"; ?>"  />
    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>views/themes/<?= $Serveur_Config['theme']; ?>/CSS/colors.css"/>
</head>
<body>
    <!--<div class="container flex-grow-1 light-style container-p-y">-->
    <div class="flex-grow-1 light-style">
        <div class="container-m-nx container-m-ny mb-3">
            <div class="container-p-x py-2">
                <div style="display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-pack: justify; justify-content: space-between;">
                    <ol class="breadcrumb">
                        <?php foreach ($path_array as $p){ ?>
                            <li class="breadcrumb-item">
                                <?php echo $p; ?>
                            </li>
                        <?php } ?>
                    </ol>
                    <div>
                        <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#upload-modal"><i class="ion ion-md-cloud-upload"></i>&nbsp; Upload</button>
                        <button type="button" id="btnCreateFolder" class="btn btn-secondary icon-btn mr-2"<?php if (!isset($_SESSION['user']) or is_null($_SESSION['user']) or !($_SESSION['user'] instanceof User) or (isset($_SESSION['user']) and $_SESSION['user'] instanceof User and $_SESSION['user']->getlevel() < 4)){ echo "disabled"; } ?>>
                        &nbsp;<i class="ion ion-ios-add"></i>&nbsp;</button>
                        <!--<button type="button" class="btn btn-secondary icon-btn mr-2" disabled="">&nbsp;<i class="ion ion-md-cloud-download"></i>&nbsp;</button>-->
                    </div>
                </div>
                
            </div>

            <hr class="m-0" />
        </div>

        <div class="file-manager-container file-manager-col-view">
            <div class="file-manager-row-header">
                <div class="file-item-name pb-2">Filename</div>
                <div class="file-item-changed pb-2">Changed</div>
            </div>

            <?php if (!$can_goback && empty($scanned_files)): ?>
            <p class="text-center"><em>Il n'y a aucun fichier disponible à afficher avec votre niveau d'autorisation.</em></p>
            <?php endif; ?>

            <?php if ($can_goback): ?>
            <div class="file-item item-folder" data-name="..">
                <div class="file-item-icon file-item-level-up fas fa-level-up-alt text-secondary"></div>
                <a href="<?php echo $previous_link; ?>" class="file-item-name">
                    ..
                </a>
            </div>
            <?php endif; ?>

            <div class="file-item" id="newFolderitem" style="display:none;">
                    <div class="file-item-icon far fa-folder text-secondary"></div>
                    
                    <div class="namer">
                        <span class="file-item-name name-span"></span>
                        <form class="namer_form" id="newFolderForm">
                            <input type="text" class="form-control" name="folder_name" id="newfolder_name">
                            <input type="hidden" name="path" value="<?php echo $path; ?>"> 
                        </form>
                        <span hidden class="simpleSendNewFolder" id="simpleSendNewFolder"
                            data-api="<?= LINK; ?>api/" data-module="cloud/" data-verbe="set" data-func="createFolder" 
                            data-tosend="#newFolderForm" data-useForm="true" data-reload="true" ></span>
                    </div>
                </div>

            <?php foreach ($scanned_files as $key => $f){ ?>
                <div class="file-item item-<?php echo $f['type']; ?>" data-id="<?php echo $key; ?>" id="elem_<?php echo $key; ?>"
                <?php //echo ($f['type'] == "file") ? 'draggable="true"' : ""; ?> draggable="true" data-name="<?php echo $f['name']; ?>">
                    <!--<div class="file-item-select-bg bg-primary"></div>
                    <label class="file-item-checkbox custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" />
                        <span class="custom-control-label"></span>
                    </label>-->
                    <?php if (strpos($f['icon'], LINK) === false): ?>
                        <div class="file-item-icon far <?php echo $f['icon']; ?> text-secondary"></div>
                    <?php else: ?>
                        <div class="file-item-img" style="background-image: url(<?php echo $f['icon']; ?>);"></div>
                    <?php endif; ?>
                    <div class="namer">
                        <a href="<?php echo (isset($f['link'])) ? $f['link'] : "javascript:void(0)"; ?>" data-prefixlink="<?php echo (isset($f['prefix_link'])) ? $f['prefix_link'] : ""; ?>" class="file-item-name name-link">
                            <?php echo $f['dispname']; ?>
                        </a>
                        <form class="namer_form" id="renamer_<?php echo $key; ?>">
                            <input type="text" class="form-control name-field" style="display:none;" name="newfilename" id="name_<?php echo $key; ?>" value="<?php echo $f['dispname']; ?>">
                            <input type="hidden" name="path" value="<?php echo $f['nofile_path']; ?>"> 
                            <input type="hidden" id="hidden-filename-field-<?php echo $key; ?>" class="hidden-filename-field" name="filename" value="<?php echo str_replace($f['prefix'] . "_" , "", $f['name']); ?>">
                            <input type="hidden" name="prefix" value="<?php echo $f['prefix']; ?>">
                        </form>
                        <span hidden class="simpleSendRenameAttr" id="ssattr_<?php echo $key; ?>"
                        data-api="<?= LINK; ?>api/" data-module="cloud/" data-verbe="set" data-func="renameFile" 
                        data-tosend="#renamer_<?php echo $key; ?>" data-useForm="true" data-reload="false" data-noloading="true" data-callback="renammer_callback"></span>
                    </div>
                    
                    <div class="file-item-changed"><?php echo $f['last_edit']; ?></div>
                    <div class="file-item-changed"><?php echo $f['path']; ?></div>
                    <div class="file-item-actions btn-group">
                        <button type="button" class="btn btn-default btn-sm rounded-pill icon-btn borderless md-btn-flat hide-arrow dropdown-toggle" data-toggle="dropdown"><i class="ion ion-ios-more"></i></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if ((array_key_exists("protected_name", $f) && $f["protected_name"] === true) OR 
                            (array_key_exists("protected", $f) && $f["protected"] === true) OR
                            ((!isset($_SESSION['user']) OR !($_SESSION['user'] instanceof User)) OR (array_key_exists("access_level", $f) && $f["access_level"] > $_SESSION['user']->getLevel()))): ?>
                                <a class="dropdown-item" disabled><i class="ion ion-ios-lock"></i> Rename</a>
                            <?php else: ?>
                                <a class="dropdown-item renamer" href="javascript:void(0)">Rename</a>
                            <?php endif; ?>
                            <?php if (((!isset($_SESSION['user']) OR !($_SESSION['user'] instanceof User)) OR (array_key_exists("access_level", $f) && ($f["access_level"] > $_SESSION['user']->getLevel() OR $_SESSION['user']->getLevel() < 4)))): ?>
                                <a class="dropdown-item" disabled><i class="ion ion-ios-lock"></i> Access settings</a>
                            <?php else: ?>
                                <a class="dropdown-item" data-toggle="modal" data-target="#modal-file-<?php echo $key; ?>">Access settings</a>
                            <?php endif; ?>
                            <?php if ((array_key_exists("protected", $f) && $f["protected"] === true) OR (array_key_exists("locked", $f) && $f["locked"] === true)OR 
                            ((!isset($_SESSION['user']) OR !($_SESSION['user'] instanceof User)) OR (array_key_exists("access_level", $f) && ($f["access_level"] > $_SESSION['user']->getLevel() OR $_SESSION['user']->getLevel() < 4)))): ?>
                                <a class="dropdown-item" disabled><i class="ion ion-ios-lock"></i> Remove</a>
                            <?php else: ?>
                                <a class="dropdown-item ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                                data-module="cloud/" data-verbe="set" data-func="deleteFile" data-tosend="path=<?php echo $f['path']; ?>" data-callback="deleter" data-id="elem_<?php echo $key; ?>">Remove</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
    <?php foreach ($scanned_files as $key => $f): $lock=false;?>
    <div id="modal-file-<?php echo $key; ?>" class="modal fade modal-file">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" role="document">
                <div class="modal-header">
                    <h5 class="modal-title"><strong>Edition des droits d'accès</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <div class="row">
                        <div class="col-lg-4">
                            <?php if (strpos($f['icon'], LINK) === false): ?>
                                <div class="file-item-icon file-item-icon-modal far <?php echo $f['icon']; ?> text-secondary"></div>
                            <?php else: ?>
                                <div class="file-item-img file-item-img-modal" style="background-image: url(<?php echo $f['icon']; ?>);"></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-8">
                            <p>
                                <strong>Fichier : </strong> <span class="dispname-modal" id="dispname-modal-<?php echo $key; ?>"><?php echo $f["dispname"]; ?></span> <br>
                                <em>Nom complet : <span class="truename-modal" id="truename-modal-<?php echo $key; ?>"><?php echo $f["name"]; ?></span> </em> <br>
                                Dernière modification : <?php echo $f["last_edit"]; ?> <br>
                                <strong>Lien d'accès : </strong> <a class="link-modal" id="link-modal-<?php echo $key; ?>" href="<?php echo (isset($f['link'])) ? $f['link'] : "javascript:void(0)"; ?>"> <?php echo (isset($f['link'])) ? $f['link'] : "Non-autorisé."; ?> </a>

                            </p>
                        </div>
                    </div>
                    <hr>
                    <?php if (array_key_exists("global_conf", $f) && array_key_exists("access_level", $f["global_conf"])):?>           
                    <div class="row">
                        <div class="col-lg-4">
                                <div class="file-item-icon file-item-icon-modal far ion-ios-lock text-secondary"></div>
                        </div>
                        <div class="col-lg-8">
                            <p>
                                <strong>Ce fichier reçoit en héritage des restrictions d'accès.</strong><br>
                                <?php if(array_key_exists("inherited_from", $f["global_conf"])): ?>
                                <em>Provenance de la restriction : <?php echo str_replace(ROOT, "", $f["global_conf"]["inherited_from"]); ?></em> <br>
                                <?php endif; ?>
                                <em>Héritage protégé : <?php echo (array_key_exists("inherited_forced", $f["global_conf"]) && $f["global_conf"]["inherited_forced"]) ? "Oui" : "Non"; ?></em> <br>
                                <strong>Niveau hérité : </strong> <?php echo $f["global_conf"]["access_level"]; ?> 
                                <?php if ((array_key_exists("protected", $f) && $f["protected"] === true) OR (array_key_exists("locked", $f) && $f["locked"] === true)):?>           
                                <br><em>Cette règle n'est pas appliquée car le CMS protège le fichier.</em>
                                <?php elseif (array_key_exists("access_level", $f) and is_numeric($f["access_level"]) and intval($f["access_level"]) > $f["global_conf"]["access_level"]): ?>           
                                <br><em>Cette règle n'est pas appliquée car le fichier est verrouillé avec un niveau d'accès supérieur.</em>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <?php endif; ?>
                    <?php if ((array_key_exists("protected", $f) && $f["protected"] === true) OR (array_key_exists("locked", $f) && $f["locked"] === true)): $lock=true;?>           
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Un service de DiamondCMS protège ce fichier.</strong> Il n'est donc pas supprimable, et ses autorisations ne sont pas éditables tant que le fichier est utilisé par DiamondCMS.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <br>
                    <?php elseif((array_key_exists("protected_name", $f) && $f["protected_name"] === true)): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Un service de DiamondCMS protège ce fichier.</strong> Il n'est donc pas possible de le renommer tant que le fichier est utilisé par DiamondCMS. Toutefois, vous pouvez éditer ses permissions.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (!array_key_exists("access_level", $f)){ $f["access_level"] = 1; } ?>
                    <?php $f["inherited_access_level"] = (array_key_exists("global_conf", $f) && array_key_exists("access_level", $f["global_conf"]) && intval($f["global_conf"]["access_level"]) > 1) ? intval($f["global_conf"]["access_level"]) : 1; ?>
                    <div class="editer">
                        <form id="filesproperties_<?php echo $key; ?>">
                            <div class="form-group row">
                                <label for="access_level" class="col-sm-6 col-form-label">Niveau d'autorisation requis (consultation)</label>
                                <div class="col-sm-6">
                                    <select <?php if (!(array_key_exists("protected", $f) && $f["protected"] === true) AND !(array_key_exists("locked", $f) && $f["locked"] === true)): ?>name="access_level"<?php endif; ?> id="access_level_<?php echo $key; ?>" <?php echo ($lock) ? "disabled" : "";?> class="<?php if (!(array_key_exists("protected", $f) && $f["protected"] === true) AND !(array_key_exists("locked", $f) && $f["locked"] === true)): ?>access_level-field<?php endif; ?> form-control" >
                                        <?php 
                                        $counter=($lock) ? intval($f["access_level"]) : max($f["inherited_access_level"], 1);
                                        for ($counter; $counter<6; $counter++): ?>
                                            <option value="<?php echo $counter;?>" <?php echo ($counter == $f["access_level"]) ? "selected" : "";?>><?php echo $counter;?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <small class="col-sm-12 text-center form-text text-muted"><strong>Alerte :</strong> cette action est récursive, c'est-à-dire que si vous modifiez les niveaux d'accès d'un dossier, cette modification se propage à tous les autres dossiers qu'il contient (sauf aux fichiers protégés).</small>
                                <br />
                                <div class="col-sm-12 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input hidden-access-field" id="hidden-access-field-<?php echo $key; ?>" name="hidden" <?php if (array_key_exists("hidden", $f) && $f['hidden']){ echo "checked"; } ?>>
                                        <label class="form-check-label" for="hidden-access-field-<?php echo $key; ?>">Cacher ce fichier aux utilisateurs de rang inférieur à 3</label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="path" value="<?php echo $f['path']; ?>"> 
                        </form>
                        <span hidden class="simpleSendEditAttr" id="sseditattr_<?php echo $key; ?>"
                            data-api="<?= LINK; ?>api/" data-module="cloud/" data-verbe="set" data-func="editAccessProperties" 
                            data-tosend="#filesproperties_<?php echo $key; ?>" data-useForm="true" data-reload="false" data-noloading="true"></span>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>    
    <?php endforeach; ?>

    <div id="upload-modal" class="modal fade modal-file">
        <div class="modal-dialog">
            <div class="modal-content" role="document">
                <div class="modal-header">
                    <h5 class="modal-title"><strong>Ajouter des fichiers</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <?php if (!isset($_SESSION['user']) or is_null($_SESSION['user']) or !($_SESSION['user'] instanceof User) or (isset($_SESSION['user']) and $_SESSION['user'] instanceof User and $_SESSION['user']->getlevel() < 4)): ?>
                        <div class="row">
                            <div class="col-lg-4">
                                    <div class="file-item-icon file-item-icon-modal far ion-ios-lock text-secondary"></div>
                            </div>
                            <div class="col-lg-8">
                                <p>
                                    <strong>La fonction d'upload est réservée aux administrateurs.</strong><br>         
                                    <em>Il vous faut disposer d'un niveau d'autorisation au moins égal à 4 pour pouvoir poursuivre cette action.</em>
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <form id="uploaderForm">
                            <div class="form-group row">
                                <label for="access_level" class="col-sm-6 col-form-label">Niveau d'autorisation requis (consultation)</label>
                                <div class="col-sm-6">
                                    <select name="access_level" id="access_level_newfield" class="access_level-newfield form-control" >
                                        <?php 
                                        $min = 1;
                                        if (isset($global_conf) && is_array($global_conf) && array_key_exists("access_level", $global_conf) && is_numeric($global_conf["access_level"]) && intval($global_conf["access_level"]) <= $_SESSION['user']->getLevel() )
                                            $min = intval($global_conf["access_level"]);
                                        for ($counter=$min; $counter<=$_SESSION['user']->getLevel(); $counter++): ?>
                                            <option value="<?php echo $counter;?>"><?php echo $counter;?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input hidden-access-newfield" id="hidden-access-newfield" name="hidden">
                                        <label class="form-check-label" for="hidden-access-newfield">Cacher ce fichier aux utilisateurs de rang inférieur à 3</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file" id="file-upload-input" data-neededForValidation="true" >
                                    <label class="custom-file-label" for="file-upload-input">Fichier à envoyer (max. 1 fichier par envoi)</label>
                                </div>
                            </div>
                            <input type="hidden" name="path" value="<?php echo ROOT . $path; ?>">    
                        </form>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <?php if (!(!isset($_SESSION['user']) or is_null($_SESSION['user']) or !($_SESSION['user'] instanceof User) or (isset($_SESSION['user']) and $_SESSION['user'] instanceof User and $_SESSION['user']->getlevel() < 4))): ?>
                    <button class="btn btn-danger ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                            data-module="cloud/" data-verbe="set" data-func="uploadFile" 
                            data-tosend="#uploaderForm" data-useForm="true" data-reload="true">
                            Envoyer</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>   
    <div style="display: none;">
        <form id="mooveFileForm">
            <input type="hidden" id="parentPath" value="<?php echo $real_previous_path; ?>">
            <input type="hidden" name="pathto">
            <input type="hidden" id="originalPathfrom" value="<?php echo ROOT . $path; ?>">
            <input type="hidden" name="pathfrom" value="<?php echo ROOT . $path; ?>">
            <input type="hidden" name="itemname">
            <span hidden class="simpleSendMooveAttr" id="simpleSendMooveAttr"
                data-api="<?= LINK; ?>api/" data-module="cloud/" data-verbe="set" data-func="mooveFile" 
                data-tosend="#mooveFileForm" data-useForm="true" data-reload="false" data-noloading="true"></span>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>

    <script src="<?php echo LINK . "views/themes/" . $Serveur_Config["theme"] . "/JS/plugins/listener/default.theme.js"; ?>" ></script>
    <script src="<?php echo LINK . "views/themes/" . $Serveur_Config["theme"] . "/JS/plugins/listener/bs-custom-file-input.admin.js"; ?>" ></script>
    <script src="<?php echo LINK . "views/themes/" . $Serveur_Config["theme"] . "/JS/pages/cloud/cloud.js"; ?>" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>


