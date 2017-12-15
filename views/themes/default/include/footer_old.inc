<?php global $Serveur_Config;?>
  <br /></br />
    <!--<footer>
      <div class="principal">
        <?php if (!empty($Serveur_Config['Social']['gl']) || !empty($Serveur_Config['Social']['fb']) || !empty($Serveur_Config['Social']['tw']) || !empty($Serveur_Config['Social']['yt'])){ ?>
              <p>Nos résaux sociaux : <?php if (!empty($Serveur_Config['Social']['gl'])){ ?> <a class="bold" href="<?= $Serveur_Config['Social']['gl']; ?>"><i class="fa fa-google-plus" aria-hidden="true"></i> Google+ </a><?php }?>
              <?php if (!empty($Serveur_Config['Social']['yt'])){ ?><a class="bold" href="<?= $Serveur_Config['Social']['yt']; ?>"><i class="fa fa-youtube" aria-hidden="true"></i> Youtube </a><?php }?>
              <?php if (!empty($Serveur_Config['Social']['fb'])){ ?><a class="bold" href="<?= $Serveur_Config['Social']['fb']; ?>"><i class="fa fa-facebook-official" aria-hidden="true"></i> Facebook </a><?php }?>
              <?php if (!empty($Serveur_Config['Social']['tw'])){ ?><a class="bold" href="<?= $Serveur_Config['Social']['tw']; ?>"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter </a><?php } ?> </p><?php
        }?>
        <p>Site créé pour le serveur <?php echo $Serveur_Config['Serveur_name']; ?> avec <a class="footer_text_link bold" href="http://www.diamondcms.fr/">DiamondCMS</a>. Tous droits réservés, <a class="footer_text_link bold" href="http://www.GougDev.fr/">GougDev</a>.
        <br />Version gratuite et inachevée (bêta - Développement) de DiamondCMS</p>
      </div>

    </footer>-->
    <footer class="footer-distributed">
			<div class="footer-left">
				<h3><?php echo $Serveur_Config['Serveur_name']; ?></h3>
				<p class="footer-links">
					<a href="#">Home</a>
					·
					<a href="#">Blog</a>
					·
					<a href="#">Pricing</a>
					·
					<a href="#">About</a>
					·
					<a href="#">Faq</a>
					·
					<a href="#">Contact</a>
				</p>
				<p class="footer-company-name">DiamondCMS © 2016 - <?php echo date('Y'); ?></p>
			</div>
			<div class="footer-center">
				<div>
					<i class="fa fa-map-marker"></i>
					<p><span>21 Revolution Street</span> Paris, France</p>
				</div>
				<div>
					<i class="fa fa-phone"></i>
					<p>+1 555 123456</p>
				</div>
				<div>
					<i class="fa fa-envelope"></i>
					<p><a href="mailto:support@company.com">support@company.com</a></p>
				</div>
			</div>
			<div class="footer-right">
				<p class="footer-company-about">
					<span>A propos de nous</span>
					<?php echo $Serveur_Config['about_footer']; ?><br />
          Nous utilisons la version gratuite de DiamondCMS beta pour notre site internet : <a href="http://www.diamondcms.fr/">Découvrez-la !</A>
				</p>
				<div class="footer-icons">
          <?php if (!empty($Serveur_Config['Social']['gl']) || !empty($Serveur_Config['Social']['fb']) || !empty($Serveur_Config['Social']['tw']) || !empty($Serveur_Config['Social']['yt'])){ ?>
                <?php if (!empty($Serveur_Config['Social']['gl'])){ ?><a href="<?= $Serveur_Config['Social']['gl']; ?>"><i class="fa fa-google-plus" aria-hidden="true"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['yt'])){ ?><a href="<?= $Serveur_Config['Social']['yt']; ?>"><i class="fa fa-youtube" aria-hidden="true"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['fb'])){ ?><a href="<?= $Serveur_Config['Social']['fb']; ?>"><i class="fa fa-facebook"></i></a><?php }?>
                <?php if (!empty($Serveur_Config['Social']['tw'])){ ?><a href="<?= $Serveur_Config['Social']['tw']; ?>"><i class="fa fa-twitter"></i></a><?php } ?> </p><?php
          }?>
				</div>
			</div>
		</footer>
    <!-- LIB JavaScript -->
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.js"></script>
    <?php
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
    <style>
 .footer-distributed{
	background-color: #292c2f;
	box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.12);
	box-sizing: border-box;
	width: 100%;
	text-align: left;
	font: bold 16px sans-serif;

	padding: 55px 50px;
	margin-top: 80px;
}

.footer-distributed .footer-left,
.footer-distributed .footer-center,
.footer-distributed .footer-right{
	display: inline-block;
	vertical-align: top;
}

/* Footer left */

.footer-distributed .footer-left{
	width: 40%;
}

/* The company logo */

.footer-distributed h3{
	color:  #197d62;
  font-size: 40px;
  margin 0;
}


/* Footer links */

.footer-distributed .footer-links{
	color:  #ffffff;
	margin: 20px 0 12px;
	padding: 0;
}

.footer-distributed .footer-links a{
	display:inline-block;
	line-height: 1.8;
	text-decoration: none;
	color:  inherit;
}

.footer-distributed .footer-company-name{
	color:  #8f9296;
	font-size: 14px;
	font-weight: normal;
	margin: 0;
}

/* Footer Center */

.footer-distributed .footer-center{
	width: 35%;
}

.footer-distributed .footer-center i{
	background-color:  #33383b;
	color: #ffffff;
	font-size: 25px;
	width: 38px;
	height: 38px;
	border-radius: 50%;
	text-align: center;
	line-height: 42px;
	margin: 10px 15px;
	vertical-align: middle;
}

.footer-distributed .footer-center i.fa-envelope{
	font-size: 17px;
	line-height: 38px;
}

.footer-distributed .footer-center p{
	display: inline-block;
	color: #ffffff;
	vertical-align: middle;
	margin:0;
}

.footer-distributed .footer-center p span{
	display:block;
	font-weight: normal;
	font-size:14px;
	line-height:2;
}

.footer-distributed .footer-center p a{
	color:  #5383d3;
	text-decoration: none;;
}


/* Footer Right */

.footer-distributed .footer-right{
	width: 20%;
}

.footer-distributed .footer-company-about{
	line-height: 20px;
	color:  #92999f;
	font-size: 13px;
	font-weight: normal;
	margin: 0;
}

.footer-distributed .footer-company-about span{
	display: block;
	color:  #ffffff;
	font-size: 14px;
	font-weight: bold;
	margin-bottom: 20px;
}

.footer-distributed .footer-icons{
	margin-top: 25px;
}

.footer-distributed .footer-icons a{
	display: inline-block;
	width: 35px;
	height: 35px;
	cursor: pointer;
	background-color:  #33383b;
	border-radius: 2px;

	font-size: 20px;
	color: #ffffff;
	text-align: center;
	line-height: 35px;

	margin-right: 3px;
	margin-bottom: 5px;
}

/* If you don't want the footer to be responsive, remove these media queries */

@media (max-width: 880px) {

	.footer-distributed{
		font: bold 14px sans-serif;
	}

	.footer-distributed .footer-left,
	.footer-distributed .footer-center,
	.footer-distributed .footer-right{
		display: block;
		width: 100%;
		margin-bottom: 40px;
		text-align: center;
	}

	.footer-distributed .footer-center i{
		margin-left: 0;
	}

}

    </style>
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
