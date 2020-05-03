<?php 
$controleur_def->loadModel('admin/news');

if (isset($param[2]) && !empty($param[2]) && $param[2] == "del_news_from_modal" && isset($param[3]) && !empty($param[3])){
    if (delNews($controleur_def->bddConnexion(), $param[3])){
        die("Success");
    }
    echo "coucou";
    $controleur_def->addError(341);
}

//Si on reçoit des informations dans la variable $_POST
if (isset($_POST) && !empty($_POST)){
    //Si le formulaire a bien été rempli entierement
    if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['message']) && !empty($_POST['message'])){
      //Si une image est envoyée :
      $error = false;
      if (isset($_FILES['img']) && $_FILES['img']['size'] != 0){
        if (strrpos($_FILES['img']['type'], "image/") === false){
            $controleur_def->addError(524);
        }else {
            $upload = uploadFile('img', "news");
            if (is_int($upload)){
                $controleur_def->addError(500 + intval($upload));
            }else {
              $filename = $upload;
            }
        }    
      }else {
          $filename = "noimg";
      }
      if ($error == false)
        addNews($controleur_def->bddConnexion(), $_POST['name'], $_POST['message'], $filename, $_SESSION['user']->getId());
    }
  }

$news = simplifySQL\select($controleur_def->bddConnexion(), false, "d_news", "*", false, "date", true);

$controleur_def->loadJS("admin/news");
$controleur_def->loadViewAdmin('admin/news', 'accueil', 'Systeme de News');