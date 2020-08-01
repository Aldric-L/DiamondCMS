<?php global $controleur_def;
$js = $controleur_def->js;
    //Code pour charger les plugins js/css
    //On ouvre le dossier
    if($jsfolder = opendir(ROOT . 'js/plugins/listener')){
      //On initialise une variable "compteur"
      $count = 0;
      while(false !== ($plugin = readdir($jsfolder))){
        //Si c'est bien un plugin "listener"
        if($plugin != '.' && $plugin != '..' && $plugin != 'plugins_listener.js' && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
          //Qu'il appartient bien à notre theme
          if (substr($plugin, -9) != '.theme.js' || (substr($plugin, -15) == '.admin.theme.js' && str_replace(".admin.theme.js", "", $plugin) == $Serveur_Config['theme'])){
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
    //On charge les JS demandés par le controleur
    if (!empty($js)){
      foreach($js as $j){
        echo '<script src="'. $j . '"></script>';
      }
    }
    ?>
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
    <script src='//cdn.tinymce.com/4/tinymce.min.js'></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/plugins/load/admin.theme.js"></script>
</body>

</html>