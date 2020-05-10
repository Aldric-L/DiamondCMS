<?php
$erreur_exts = ""; 
if (!extension_loaded("curl")){
    $erreur_exts .= "curl";
}
if (!extension_loaded("gd")){
    $erreur_exts .= "gd ";
}
if (!extension_loaded("pdo")){
    $erreur_exts .= "pdo ";
} 
if (!extension_loaded("date")){
  $erreur_exts .= "date ";
} 
if (!extension_loaded("hash")){
  $erreur_exts .= "hash ";
}
if (!extension_loaded("mbstring")){
  $erreur_exts .= "mbstring ";
}
if (!extension_loaded("session")){
  $erreur_exts .= "session ";
}

$errors_chmod = array();
function list_dir($name) {
  global $errors_chmod;
  if ($dir = opendir($name)) {
    while($file = readdir($dir)) {
      if(is_dir($name . $file) && !in_array($file, array(".","..")) && ( substr(sprintf('%o', fileperms($name . $file)), -4) == "0777" || substr(sprintf('%o', fileperms($name . $file)), -4) == "0666")) {
        list_dir($name  . $file . '/');
      }else if(!in_array($file, array(".","..")) && substr(sprintf('%o', fileperms($name . $file)), -4) != "0777" && substr(sprintf('%o', fileperms($name . $file)), -4) != "0666") {
		    array_push($errors_chmod, array($name . $file, substr(sprintf('%o', fileperms($name . $file)), -4), "0777 ou 0666"));
	    }
    }
    closedir($dir);
  }
}

function ch_dir($name) {
  if ($dir = opendir($name)) {
    while($file = readdir($dir)) {
      if(is_dir($name . $file) && !in_array($file, array(".","..")) && ( substr(sprintf('%o', fileperms($name . $file)), -4) == "0777" || substr(sprintf('%o', fileperms($name . $file)), -4) == "0666")) {
        ch_dir($name  . $file . '/');
      }else if(!in_array($file, array(".","..")) && substr(sprintf('%o', fileperms($name . $file)), -4) != "0777" && substr(sprintf('%o', fileperms($name . $file)), -4) != "0666") {
			  chmod($name . $file, 0777);
	    }
    }
    closedir($dir);
  }
}


$files = array(ROOT . 'addons/', ROOT . 'views/', ROOT . 'config/', ROOT . 'installation/', ROOT . 'logs/');
foreach ($files as $f){
  ch_dir($f);
}
foreach ($files as $f){
    if (substr(sprintf('%o', fileperms($f)), -4) != "0777" && substr(sprintf('%o', fileperms($f)), -4) != "0666"){
        chmod($f, 0777);
        if (substr(sprintf('%o', fileperms($f)), -4) != "0777" && substr(sprintf('%o', fileperms($f)), -4) != "0666"){
          array_push($errors_chmod, array($f, substr(sprintf('%o', fileperms($f)), -4), "0777 ou 0666"));
        }
    }else {
		$search = list_dir($f);
      if (!empty($search)){
        array_push($errors_chmod, $search);
      }
    }
};
function read($tab){
	echo "<tr>";
	foreach ($tab as $t){
		if (is_array($t)){
			foreach ($t as $g){
				if (is_array($g)){
					read($g);
				}
			}
		}else {
		
				echo "<th>$t</th>";
		}
	}
	echo "</tr>";
}
 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>installation/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>installation/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>
    <style>
      h1, h2{
        text-align: center;
      }
      h1{
        font-size: 3em;
        color: #197d62;
      }
      h2{
        font-family: "Raleway-Light";
        font-size: 1.5em;
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br>
      <center>
      <img class="img-responsive" width="550" style="margin: 0;" src="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>/installation/diamondcms.png">
      <h1>Merci d'avoir choisi DiamondCMS pour votre site internet !</h1>
      <h2>L'installation de votre site internet va commencer. Pour la mener à bien, munissez-vous d'une base de données MySQL et de quelques minutes !<br></h2>
      <div style="margin-left: 15%; margin-right: 15%;">
        <br><hr>
        <p><?php if (intval(substr(phpversion(), 0, 1)) <= 4 || 
        ( intval( substr(phpversion(), 0, 1) ) == 5 && intval( substr( phpversion(), 2, 1 ) ) < 6) ){ ?>
          <span style="color: red;"> <strong>Impossible de poursuivre l'installation : Version de PHP incompatible</strong> </span>
        <?php }else { ?> 
        Vous installez la version <strong><?= DCMS_VERSION; ?></strong> de DiamondCMS.</br>
        <?php if (DCMS_TYPE == "Extended"){ ?>
            Vous avez opté pour une installation complète incluant les addons :
            <?php foreach (DCMS_DEFAULT_ADDONS_INSTALLED as $a){ ?>
              <?= $a; ?>, 
            <?php } ?>
        <?php }else { ?>
            Vous avez opté pour une installation minimale (Vous pouvez ajouter des addons ou des thèmes par la suite)
        <?php } ?>
        
        </p>
        <hr>
        <?php if (empty($erreur_exts)){ ?>
        <p><strong>Succès !</strong> Votre installation de PHP est parfaite, l'installation de DiamondCMS peut continuer.</p>
        <?php if (PHP_VERSION_ID < 50600){ ?>
            <p><em>Attention toutefois à votre version de PHP qui est antérieure à PHP 7. Songez à la mettre à jour pour des questions de sécurité et de support.</em></p>
        <?php } ?>
      <?php }else { ?>
        <p><strong>Erreur !</strong> Votre installation de PHP est incomplète : DiamondCMS nécessite les extensions suivantes : <?= $erreur_exts; ?>.</p>
        <p>Vous devez modifier ces droits vous-même : DiamondCMS a déjà essayé et n'a pas la permission de le faire automatiquement.<br>
        <em>Pour poursuivre, veuillez corriger ces erreur et actualiser la page.</em></p>
      <?php } ?>
      <?php if (array_key_exists('ENV_HTACCESS_ALLOWED', $_SERVER)){ ?>
        <p><strong>Succès !</strong> Votre serveur WEB est bien compatible, l'installation de DiamondCMS peut continuer.</p>
      <?php }else { ?>
        <p><strong>Erreur !</strong> La configuration de votre serveur WEB est incompatible avec DiamondCMS.</p>
        <p>Vous devez ajouter dans votre configuration d'Apache la directive AllowOverride All et vérifier qu'un fichier .htaccess est bien présent à la racine du site.<br>(Plus d'informations ici : https://www.aidoweb.com/tutoriaux/fichier-htaccess-qui-ne-fonctionne-pas-solutions-configuration-apache-648).<br>
        <em>Pour poursuivre, veuillez corriger cette erreur et actualiser la page.</em></p>
      <?php } ?>
      <?php if (empty($errors_chmod)){ ?>
        <p><strong>Succès !</strong> DiamondCMS a bien tous les droits d'écriture sur ses fichiers. L'installation peut continuer.</p>
      <?php }else { ?>
        <p><strong>Erreur !</strong> DiamondCMS ne dispose pas des droits nécessaires pour accèder à ses fichiers.</p>
        <div style="margin-left: 15%; margin-right: 15%;">
        <table class="table">
            <thead>
                <tr>
                <th>Adresse du dossier</th>
                <th>Droits actuels</th>
                <th>Droits nécessaires</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($errors_chmod as $e){ ?>
                    <tr>
                        <?Php read($e); ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
      <p>Vous devez modifier ces droits vous-même : DiamondCMS a déjà essayé et n'a pas la permission de le faire automatiquement.<br>
	  <strong>Il est très important que vous appliquiez les droits de manière récursive, sur les dossiers, sous-dossiers, et fichiers.</strong><br>
      <em>Pour poursuivre, veuillez corriger ces erreur et actualiser la page.</em></p>
      <?php } ?>
        <br>
                <p><button <?php if (!empty($erreur_exts) || !empty($errors_chmod) || !array_key_exists('ENV_HTACCESS_ALLOWED', $_SERVER)){ ?> disabled <?php } ?> class="btn btn-lg btn-success green" id="next_button" data="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/next.php">Passer à l'étape suivante</button></p>
        <?php } ?>
      </div>
      </center>
        
        
        <!-- LIB JavaScript -->
        <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
        <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.js"></script>
        <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/global.js"></script>
    </body>
</html>