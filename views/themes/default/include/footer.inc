<?php global $Serveur_Config, $controleur_def;
$link = $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT;
$js = $controleur_def->js;
?>
  <br /></br />
    <footer class="footer-distributed">
			<div class="footer-left">
				<h3><?php echo $Serveur_Config['Serveur_name']; ?></h3>
				<p class="footer-links">
					<a href="<?php echo $link; ?>">Accueil</a>
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
					<a href="<?php echo $link; ?>contact">Contact</a>
				</p>
				<p class="footer-company-name">Créé par GougDev.fr, DiamondCMS © 2016 - <?php echo date('Y'); ?><br />
				<br />DiamondCMS est en Beta est ne peut etre commertialisé comme tel.</p>
			</div>
			<div class="footer-right">
				<p class="footer-company-about">
					<span>A propos de nous</span>
					<?php echo $Serveur_Config['about_footer']; ?><br />
          Nous utilisons la version gratuite de DiamondCMS beta pour notre site internet : <a href="http://www.diamondcms.fr/">Découvrez-la !</A>
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
      //On lance une boucle
      while(false !== ($plugin = readdir($jsfolder))){
        //Si c'est bien un plugin "listener"
        if($plugin != '.' && $plugin != '..' && $plugin != 'plugins_listener.js' && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
          //Qu'il appartient bien à notre theme
          if (substr($plugin, -9) != '.theme.js' || (substr($plugin, -9) == '.theme.js' && str_replace(".theme.js", "", $plugin) == $Serveur_Config['theme'])){
            $count++;
            //On l'inclus
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
    Merci de ne pas supprimer les liens vers les sites de DiamondCMS et GougDev. En cas de suppression,
    votre site sera immobilisé et pourra faire l'objet de poursuites (Conformément aux CGU disponibles sur le site officiel de DiamondCMS).
    Modifier le footer (sans accord), c'est voler le CMS.
    Pour pouvoir modifier ce footer, merci de souscrire à une version Premium du CMS.
    Merci de votre compréhention, GougDev.

    Attention : ne modifiez pas les attributs des balises a ("footer_text_link"), sinon votre site sera bloqué automatiquement.

    NB: Les CGU/CGV sont disponibles sur le site : www.diamondcms.fr
    PS: Ces balises PHP ne sont là que pour avoir un commentaire invisible dans le navigateur (lors d'une inspection de page).
  */
?>
