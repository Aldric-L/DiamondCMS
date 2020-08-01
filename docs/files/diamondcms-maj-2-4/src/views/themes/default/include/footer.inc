<?php global $Serveur_Config, $controleur_def;
$link = $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT;
$js = $controleur_def->js;
$pages = $controleur_def->getFooterPages();
?>
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
					<!--<a href="<?php echo $link; ?>">Accueil</a>
					·
					<a href="<?php echo $link; ?>mentions-legales">Mentions légales</a>
					·
					<a href="<?php echo $link; ?>cgu">CGU / CGV</a>
					·
					<a href="<?php echo $link; ?>faq">F.A.Q.</a>
          <?php if ($Serveur_Config['en_reglement']){ ?>
          ·
					<a href="<?php echo $link; ?>reglement">Réglement</a>
          <?php }?>
					·
					<a href="<?php echo $link; ?>contact">Contact</a>-->
				</p>
				<p class="footer-company-name">Créé par <?= $Serveur_Config['Serveur_name']; ?>  © <?php echo date('Y'); ?><br /></p>
			</div>
			<div class="footer-right">
				<p class="footer-company-about">
					<span>A propos de nous</span>
					<?php echo $Serveur_Config['about_footer']; ?><br />
          Nous utilisons la version gratuite de DiamondCMS beta pour notre site internet : <a href="https://aldric-l.github.io/DiamondCMS/">Découvrez-la !</A>
				</p>
				<div class="footer-icons">
          <?php if (!empty($Serveur_Config['Social']['gl']) || !empty($Serveur_Config['Social']['fb']) || !empty($Serveur_Config['Social']['tw']) || !empty($Serveur_Config['Social']['yt']) || !empty($Serveur_Config['Social']['discord'])){ ?>
                <?php if (!empty($Serveur_Config['Social']['gl']) && $Serveur_Config['Social']['gl'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['gl']; ?>"><i class="fa fa-google-plus" aria-hidden="true"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['yt']) && $Serveur_Config['Social']['yt'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['yt']; ?>"><i class="fa fa-youtube" aria-hidden="true"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['fb']) && $Serveur_Config['Social']['fb'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['fb']; ?>"><i class="fa fa-facebook"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['tw']) && $Serveur_Config['Social']['tw'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['tw']; ?>"><i class="fa fa-twitter"></i></a><?php } ?>
								<?php if (!empty($Serveur_Config['Social']['discord']) && $Serveur_Config['Social']['discord'] != "disabled"){ ?><a href="<?= $Serveur_Config['Social']['discord']; ?>"><img src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/img/Discord-Logo-White.png" alt="logo discord" width="35px" height="35px"></a><?php } ?> </p><?php
          }?>
				</div>
			</div>
		</footer>
    <!-- LIB JavaScript -->
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.js"></script>
    <?php

    //On charge les CSS demandés par le controleur
    if (!empty($js)){
      foreach($js as $j){
        echo '<script src="'. $j . '"></script>';
      }
    }

    //Code pour charger les plugins js/css
    //On ouvre le dossier
    if($jsfolder = opendir(ROOT . 'js/plugins/listener')){
      //On initialise une variable "compteur"
      $count = 0;
      while(false !== ($plugin = readdir($jsfolder))){
        //Si c'est bien un plugin "listener"
        if($plugin != '.' && $plugin != '..' && $plugin != 'plugins_listener.js' && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
          //Qu'il appartient bien à notre theme
          if (substr($plugin, -9) != '.theme.js' || (substr($plugin, -9) == '.theme.js' && str_replace(".theme.js", "", $plugin) == $Serveur_Config['theme'])){
            $count++;
            //On l'inclu
            if (substr($plugin, -3) == '.js'){
              echo '<script src="'. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'js/plugins/listener/' . $plugin . '"></script>';
            }else {
              echo '<link rel="stylesheet" type="text/css" href="' . $Serveur_Config['protocol'] . '://'.$_SERVER['HTTP_HOST'] . WEBROOT . 'js/plugins/listener/'. $plugin .'"/>';
            }
          }
        }
      }
      //On affiche un commentaire html pour savoir combien de plugins ont été applelés
      if ($count != 0){
        echo "<!--Inclusion de " . $count . " plugins javascript ! -->";
      }else {
        echo '<!--Aucun plugin trouvé, chargement des libs de "base" -->';
      }
    }
    ?>
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'js/plugins/listener/plugins_listener.js'; ?>"></script>
		
		<!--Inclusion de la feuille CSS du footer -->
		<link rel="stylesheet" type="text/css" href="<?php echo $link; ?>views/themes/<?php echo $Serveur_Config['theme']; ?>/CSS/footer.css"/>

  </body>
</html>
<?php
  /*
    Merci de ne pas supprimer le lien vers DiamondCMS.
    Conformément à nos conditions générales d'utilisation, DiamondCMS est un projet semi-libre, et n'autorise pas l'usage du projet sans un lien.

    Outre ces considérations légales, DiamondCMS a besoin de ce lien pour exister et continuer à croître. Sachez qu'il a nécessité plusieurs années de développement totalement bénévoles.
    Il semble qu'un simple lien soit un juste retour des choses.

    Toutefois, si vraiment vous souhaitez supprimer ce lien, vous pouvez contacter par Github : un compromis sera trouvé. 
  */
?>
