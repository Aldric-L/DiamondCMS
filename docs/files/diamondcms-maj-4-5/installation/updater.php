<?php 
require_once 'updater.class.php';
define('ROOT', str_replace('installation/updater.php','', $_SERVER['SCRIPT_FILENAME']));
define('WEBROOT', str_replace('installation/updater.php','', $_SERVER['SCRIPT_NAME']));

/**
   * Attention, on utilise désormais cette méthode pour savoir si on utilise une connexion SSL
   * Le réglage $Serveur_config['protocol] est donc désormais DEPRECIE
   * Il convient d'utiliser la contante LINK pour créer des liens.
   */
  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    define('LINK', "https://" . $_SERVER['HTTP_HOST'] . WEBROOT);
  }else if ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) {
    define('LINK', "https://" . $_SERVER['HTTP_HOST'] . WEBROOT);
  }else {
    define('LINK', "http://" . $_SERVER['HTTP_HOST'] . WEBROOT);
  }

//On gère les erreurs et exceptions (code issu de l'index.php)
define('DEV_MODE', true);
require_once(ROOT . "models/errorhandler.php");
set_error_handler("diamondInstallerErrorHandler", E_ALL);
set_exception_handler('diamondInstallerExceptionHandler');
register_shutdown_function("installerShut");
$erreur = null;
require_once ROOT . 'installation/updater.class.php';
$updater = new Updater(ROOT, intval(file_get_contents($_SERVER['REQUEST_SCHEME']  . '://'. $_SERVER['SERVER_NAME'] . '/'. WEBROOT . "DiamondCMS/raw/version"))-1);
$majs = $updater->getAvailableMajs();
if (!empty($majs)){
    $updater->instance($majs[0]['path']);
}else {
    $erreur = "Aucune mise à jour n'a été trouvée.";
}

// ------------------- CHMOD
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
			  @chmod($name . $file, 0777);
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
        @chmod($f, 0777);
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


if (isset($_GET['action']) && $_GET['action'] == "setDB"){
  define('FORCE_INLINE_ERR', true);
  require_once(ROOT . "models/DiamondCore/db.class.php");
  require_once(ROOT . "models/bdd_connexion.php");

  $updater->installBDD(new BDD(parse_ini_file(ROOT . "config/bdd.ini")));
  require_once(ROOT . "models/DiamondCore/files.php");
  rrmdir($majs[0]['path']);
  @unlink(ROOT . "outdated.dcms");
  $_SESSION = array();
  die('Success');
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo LINK;?>installation/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href="<?php echo LINK;?>installation/sources.css" />
        <title>Installation de DiamondCMS</title>
    </head>

    <body>
    <script src="<?= LINK; ?>js/jquery-3.1.1.js"></script>
    <style>
      h1, h2{
        text-align: center;
      }
      h1{
        font-size: 2.5em;
        color: #197d62;
      }
      h3{
        font-size: 1.5em;
        font-family: "BreeSerif";
        color: #197d62;
      }
      h2{
        font-family: "Raleway-Light";
        font-size: 1.2em;
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br>
      <center>
      <img class="img-responsive" width="500" style="margin: 0;" src="<?php echo LINK;?>/installation/diamondcms.png">
      <h1>Installateur de mise à jour DiamondCMS</h1>
      <h2>DiamondCMS n'est plus à jour ? Ne tardez pas à installer la nouvelle version pour bénéficier de nouvelles fonctionnalités !</h2>
      <div style="text-align: center;padding-top: 10px;margin-left: 25%; margin-right: 25%;">
      <hr>
      <?php if ($erreur !== null){ ?>
        <p><strong>Erreur ! </strong><?= $erreur; ?></p>
      <?php }else { ?>
      <h2>Vous installez la mise à jour nommée <strong><?= $majs[0]['maj']['update_human_name']; ?></strong><br><br>
      Celle-ci est parue le <?= $majs[0]['maj']['update_date']; ?></h2>
      <br>
        <?php if ($updater->checkFiles(ROOT)){ ?>
            <p><strong>Succès !</strong> Félicitations, vous avez correctement installé les fichiers de la mise à jour.</p>
        <?php }else { ?>
            <p><strong>Erreur !</strong> Vous n'avez pas transféré les fichiers de la mise à jour. Vérifiez la notice d'installation.</p>
        <?php 
        } 
      } ?>
      <br>
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
      <em>Pour poursuivre, veuillez corriger ces erreurs et actualiser la page.</em></p>
      <?php } ?>
        
      </div>
        </p>
        <br>
        <p> 
                <button class="btn btn-lg btn-success green installBDD"  <?php if (!empty($errors_chmod) || !$updater->checkFiles(ROOT) || $erreur !== null ){ ?> disabled <?php }else { ?>
        data-link="<?= LINK; ?>installation/updater.php?action=setDB" <?php } ?>
        >Mettre à jour la base de données et terminer</button></p>
      </div>
      </center>
        
        <script>
        $('.installBDD').click(function(e){
          var url = $(this).attr('data-link');
          if (url != null || url != undefined){
            $(this).html("Chargement...");
            $.ajax({
              url : url,
              type : 'GET',
              dataType : 'html',
              success: function (data_rep) {
                if (data_rep == "" || data_rep == "Success"){
                  alert("Installation terminée avec succès ! Vous pouvez quitter la page.");
                  $('.installBDD').html("Terminé !");
                  $('.installBDD').prop('disabled', true);
                }else {
                  $('.installBDD').html("Réessayer !");
                  alert('Erreur: Voici l\'erreur levée :' + data_rep);
                }
              },
              error: function() {
                alert("Erreur, Code 111.");
              }
            });
          }
        });
        
        </script>
        <!-- LIB JavaScript -->
        <script src="<?= LINK; ?>js/font_awesome.js"></script>
        <script src="<?= LINK; ?>js/bootstrap.js"></script>
        <script src="<?= LINK; ?>installation/etape2.js"></script>
        <script src="<?= LINK; ?>installation/global.js"></script>
    </body>
</html>