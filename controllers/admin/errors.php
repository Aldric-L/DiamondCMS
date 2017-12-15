<?php 
$controleur_def->loadModel('admin/accueil');

$errors_content = analiserLog($controleur_def, 22);
/*foreach($errors_content as $error){
    if (substr($error[0], 3,4) == " "){
        $error[2] = $controleur_def->getContentError(substr($error[0], 0,3));
        //echo $error[2];
        //echo $controleur_def->getContentError(substr($error[0], 0,3));   
    }else {
        $error[2] = $controleur_def->getContentError($error[0]);
        //echo $controleur_def->getContentError($error[0]);
    }
    
}*/
//var_dump($errors_content);
$controleur_def->loadViewAdmin('admin/errors', 'accueil', 'Erreurs du CMS');