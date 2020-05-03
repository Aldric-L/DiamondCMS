<?php global $config, $img_available; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Configuration de DiamondCMS - Page d'Accueil</h1>
            <h5>La page d'accueil est le premier contact que vous avez avec vos visiteurs. Il est donc conseillé de lui apporter un soin particulier pour la rendre la plus attrayante possible.</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Configuration générale
                        </div>
                        <div class="panel-body" class="">
                            <form id="global_form">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="en_whois" <?php if ($config['Accueil']['en_whois'] == "true") { ?> checked <?php } ?>>
                                    <label class="form-check-label" for="en_whois">
                                        Afficher les profils des administrateurs
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="en_news" <?php if ($config['Accueil']['en_news'] == "true") { ?>  checked <?php } ?>>
                                    <label class="form-check-label" for="en_news">
                                        Afficher les dernières news
                                    </label>
                                </div><br>
                                <div class="form-group">
                                    <label>Image d'arrière plan (Liste des images disponibles sur le serveur) :</label>
                                    <select class="form-control" name="bg" id="bg">
                                        <?php if (!empty($img_available)) {
                                            foreach($img_available as $i){ ?>
                                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <small>Image actuelle : <?= $config['bg'];?></small>
                                </div>
                                    <p style="text-align: right"><button class="save_modifs btn btn-success btn-md">Sauvegarder</button></p>
                            </form>
                        </div>
                    </div>
        </div>
        <div class="col-lg-8">
            <div class="panel panel-default">
                    <div class="panel-heading">
                            Promotion 
                        </div>
                        <div class="panel-body" class="">
                            <p><em>Sur la page d'accueil, une section vous permet d'afficher trois textes pubicitaires, surmontés d'une image, afin de mettre en avant vos points forts.</em></p>
                            <p><strong>Version actuelle : </strong></p>
                            <div id="infos">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-lg-4"><center>
                                        <h3>
                                            <?php if ($config['Accueil']['img_1'] == "fa") { ?>
                                                <i class="fa-5x fa fa-<?php echo $config['Accueil']['fa_1'];?> " aria-hidden="true"></i>
                                            <?php }else { ?>
                                                <img width="120px" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?= $config['Accueil']['img_1']; ?>" alt="">
                                            <?php } ?>
                                        </h3>
                                        <h2><?php echo $config['Accueil']['titre_1'];?></h2>
                                        <p><?php echo $config['Accueil']['desc_1'];?></p>
                                    </center></div><!-- /.col-lg-4 -->
                                    <div class="col-lg-4"><center>
                                        <h3>
                                            <?php if ($config['Accueil']['img_2'] == "fa") { ?>
                                                <i class="fa-5x fa fa-<?php echo $config['Accueil']['fa_2'];?> " aria-hidden="true"></i>
                                            <?php }else { ?>
                                                <img width="120px" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?= $config['Accueil']['img_2']; ?>" alt="">
                                            <?php } ?>
                                        </h3>
                                        <h2><?php echo $config['Accueil']['titre_2'];?></h2>
                                        <p><?php echo $config['Accueil']['desc_2'];?></p>
                                    </center></div><!-- /.col-lg-4 -->
                                    <div class="col-lg-4"><center>
                                        <h3>
                                            <?php if ($config['Accueil']['img_3'] == "fa") { ?>
                                                <i class="fa-5x fa fa-<?php echo $config['Accueil']['fa_3'];?> " aria-hidden="true"></i>
                                            <?php }else { ?>
                                                <img width="120px" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?= $config['Accueil']['img_3']; ?>" alt="">
                                            <?php } ?>
                                        </h3>                                        
                                        <h2><?php echo $config['Accueil']['titre_3'];?></h2>
                                        <p><?php echo $config['Accueil']['desc_3'];?></p>
                                    </center></div><!-- /.col-lg-4 -->
                                    </div><!-- /.row -->
                                </div>
                            </div>
                            <hr>
                            <form id="propa_form">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="en_paypal" <?php if ($config['Accueil']['en_propa'] == "true") { ?> checked <?php } ?>>
                                    <label class="form-check-label" for="en_paypal">
                                        Activer la fonctionalité
                                    </label>
                                </div><br>
                                <p><strong>Première colonne :</strong></p>
                                <div class="form-group">
                                    <label for="titre-1" class="col-form-label">Titre :</label>
                                    <input class="form-control" type="text" name="titre-1" id="titre-1" value="<?php echo $config['Accueil']['titre_1'];?>">
                                </div>
                                <div class="form-group">
                                    <label for="content-1" class="col-form-label">Contenu :</label>
                                    <input class="form-control" type="text" name="content-1" id="content-1" value="<?php echo $config['Accueil']['desc_1'];?>">
                                </div>
                                <div class="form-group">
                                    <label>Image associée (Liste des images disponibles sur le serveur) :</label>
                                    <select class="form-control" name="img_1" id="img_1" >
                                        <option value="fa" <?php if ($config['Accueil']['img_1'] == "fa") { ?> selected <?php } ?>>Utiliser une icone</option>
                                        <?php if (!empty($img_available)) {
                                            foreach($img_available as $i){ ?>
                                                    <option value="<?= $i; ?>" <?php if ($config['Accueil']['img_1'] == $i) { ?> selected <?php } ?>><?= $i; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <small>Une image carré (120x120px) est fortement recommandée.</small>
                                </div>
                                <div class="form-group" id="div-fa-1" <?php if ($config['Accueil']['img_1'] != "fa") { ?> style="display:none;" <?php } ?>>
                                    <label for="fa-1" class="col-form-label">Icone (Font awesome) :</label>
                                    <input class="form-control" type="text" name="fa-1" id="fa-1" value="<?= $config['Accueil']['fa_1']; ?>">
                                </div>
                                <br>
                                <p><strong>Deuxième colonne :</strong></p>
                                <div class="form-group">
                                    <label for="titre-1" class="col-form-label">Titre :</label>
                                    <input class="form-control" type="text" name="titre-2" id="titre-2" value="<?php echo $config['Accueil']['titre_2'];?>">
                                </div>
                                <div class="form-group">
                                    <label for="content-2" class="col-form-label">Contenu :</label>
                                    <input class="form-control" type="text" name="content-2" id="content-2" value="<?php echo $config['Accueil']['desc_2'];?>">
                                </div>
                                <div class="form-group">
                                    <label>Image associée (Liste des images disponibles sur le serveur) :</label>
                                    <select class="form-control" name="img_2" id="img_2">
                                        <option value="fa" <?php if ($config['Accueil']['img_2'] == "fa") { ?> selected <?php } ?>>Utiliser une icone</option>
                                        <?php if (!empty($img_available)) {
                                            foreach($img_available as $i){ ?>
                                                    <option value="<?= $i; ?>" <?php if ($config['Accueil']['img_2'] == $i) { ?> selected <?php } ?>><?= $i; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <small>Une image carré (120x120px) est fortement recommandée.</small>
                                </div>
                                <div class="form-group" id="div-fa-2" <?php if ($config['Accueil']['img_2'] != "fa") { ?> style="display:none;" <?php } ?>>
                                    <label for="fa-1" class="col-form-label">Icone (Font awesome) :</label>
                                    <input class="form-control" type="text" name="fa-2" id="fa-2" value="<?= $config['Accueil']['fa_2']; ?>">
                                </div>
                                <br>
                                <p><strong>Troisième colonne :</strong></p>
                                <div class="form-group">
                                    <label for="titre-3" class="col-form-label">Titre :</label>
                                    <input class="form-control" type="text" name="titre-3" id="titre-3" value="<?php echo $config['Accueil']['titre_3'];?>">
                                </div>
                                <div class="form-group">
                                    <label for="content-3" class="col-form-label">Contenu :</label>
                                    <input class="form-control" type="text" name="content-3" id="content-3" value="<?php echo $config['Accueil']['desc_3'];?>">
                                </div>
                                <div class="form-group">
                                    <label>Image associée (Liste des images disponibles sur le serveur) :</label>
                                    <select class="form-control" name="img_3" id="img_3">
                                        <option value="fa" <?php if ($config['Accueil']['img_3'] == "fa") { ?> selected <?php } ?>>Utiliser une icone</option>
                                        <?php if (!empty($img_available)) {
                                            foreach($img_available as $i){ ?>
                                                    <option value="<?= $i; ?>" <?php if ($config['Accueil']['img_3'] == $i) { ?> selected <?php } ?>><?= $i; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <small>Une image carré (120x120px) est fortement recommandée.</small>
                                </div>
                                <div class="form-group" id="div-fa-3"  <?php if ($config['Accueil']['img_3'] != "fa") { ?> style="display:none;" <?php } ?>>
                                    <label for="fa-3" class="col-form-label">Icone (Font awesome) :</label>
                                    <input class="form-control" type="text" name="fa-3" id="fa-3" value="<?= $config['Accueil']['fa_3']; ?>">
                                </div>
                                    <p style="text-align: right"><button class="save_modifs_propa btn btn-success btn-md">Sauvegarder</button></p>
                            </form>
                        </div>
                        <hr>
                    </div>
        </div>
</div>
