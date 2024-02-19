<?php global $Serveur_Config, $controleur_def;
$link = LINK;
$js = $controleur_def->js;
$pages = $controleur_def->getFooterPages();

  /*
    Merci de ne pas supprimer le lien vers DiamondCMS.
    Conformément à nos conditions générales d'utilisation, DiamondCMS est un projet semi-libre, et n'autorise pas l'usage du projet sans un lien.
    Tout retrait non-autorisé vous expose à des poursuites pénales.

    Outre ces considérations légales, DiamondCMS a besoin de ce lien pour exister et continuer à croître. Sachez qu'il a nécessité plusieurs années de développement totalement bénévoles.
    Il semble qu'un simple lien soit un juste retour des choses.

    Toutefois, si vraiment vous souhaitez supprimer ce lien, vous pouvez nous contacter par Github : un compromis sera trouvé. 
  */
?>
<?php if (isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 4 && (!isset($_SESSION['editing_mode']) || $_SESSION['editing_mode'] != true)) { ?>
<div id="editbutton">
  <p class="ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-noloading="true" data-verbe="set" data-func="startEditing" data-reload="true"><i class="fa fa-pencil" aria-hidden="true"></i></p>
</div>
<?php } ?>
  <br /></br />
    <footer class="footer-distributed">
			<div class="footer-left">
				<h3><?php echo $Serveur_Config['Serveur_name']; ?></h3>
				<p class="footer-links">
          <?php $i = 1; foreach ( $pages as $fp){ 
            if ($fp['id_page'] == NULL || empty($fp['id_page'])){ 
              if (substr($fp['link'], 0, 4) != "http"){ ?>
					      <a href="<?php echo $link . $fp['link']; ?>"><?= $fp['titre']; ?></a> 
              <?php } else { ?>
					      <a href="<?php echo $fp['link']; ?>"><?= $fp['titre']; ?></a> 
              <?php } ?>
                <?php if ($i != sizeof($pages)){ ?>
                  ·
                <?php } ?>
            <?php }else { ?>
              <a href="<?php echo $link . $controleur_def->getPage($fp['id_page'])['name_raw']; ?>"><?= $controleur_def->getPage($fp['id_page'])['name']; ?></a>
              <?php if ($i != sizeof($pages)){ ?>
                  ·
                <?php } ?>
            <?php }
          $i++;
          } ?>
				</p>
				<p class="footer-company-name">Créé par <?= $Serveur_Config['Serveur_name']; ?>  © <?php echo date('Y'); ?><br /></p>
			</div>
			<div class="footer-right">
				<p class="footer-company-about">
					<span>A propos de nous</span>
					<span class="text_footer content-editable" data-apilink="<?php echo LINK . "API/"; ?>" data-page="footer"><?php echo $Serveur_Config['about_footer']; ?></span><br />
          <span id="msg_dcms">
            Nous utilisons la version gratuite de DiamondCMS beta pour notre site internet : <a href="https://aldric-l.github.io/DiamondCMS/">Découvrez-la !</a>
          </span>
				</p>
				<div class="footer-icons">
          <?php if (!empty($Serveur_Config['Social']['gl']) || !empty($Serveur_Config['Social']['fb']) || !empty($Serveur_Config['Social']['tw']) || !empty($Serveur_Config['Social']['yt']) || !empty($Serveur_Config['Social']['discord'])){ ?>
                <?php if (!empty($Serveur_Config['Social']['gl']) && $Serveur_Config['Social']['gl'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['gl']; ?>"><i class="fa fa-google-plus" aria-hidden="true"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['yt']) && $Serveur_Config['Social']['yt'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['yt']; ?>"><i class="fa fa-youtube" aria-hidden="true"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['fb']) && $Serveur_Config['Social']['fb'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['fb']; ?>"><i class="fa fa-facebook"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['tw']) && $Serveur_Config['Social']['tw'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['tw']; ?>"><i class="fa fa-twitter"></i></a><?php } ?>
								<?php if (!empty($Serveur_Config['Social']['discord']) && $Serveur_Config['Social']['discord'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['discord']; ?>"><img src="<?php echo LINK; ?>getimage/png/-/Discord-Logo-White/" alt="logo discord" width="35px" height="35px"></a><?php } ?> </p><?php
          }?>
				</div>
			</div>
		</footer>

    <?php
    $controleur_def->pluginloader(false, false);
    //On charge les CSS demandés par le controleur
    if (!empty($js)){
      foreach($js as $j){
        echo '<script src="'. $j . '"></script>';
      }
    }
    ?>
    <script src="<?php echo LINK . 'js/plugins/listener/plugins_listener.js'; ?>"></script>
		
		<!--Inclusion de la feuille CSS du footer -->
		<link rel="stylesheet" type="text/css" href="<?php echo $link; ?>views/themes/<?php echo $Serveur_Config['theme']; ?>/css/footer.css"/>

  </body>
</html>
