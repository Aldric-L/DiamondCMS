<?php  
// Cette page de connexion ne devrait plus être utilisée mais est conservée à des fins de compatibilité, notamment avec la fonction maintenance

if (!isset($_SESSION['pseudo']) || empty($_SESSION['pseudo'])
&& !empty($_POST) && isset($_POST['mp_connexion']) && isset($_POST['pseudo_connexion']) && !empty($_POST['pseudo_connexion']) && !empty($_POST['mp_connexion'])){
  $controleur_def->loadModel("api.class");

  try {
    $rtrn = DiamondAPI::execute(true, $controleur_def, $controleur_def->getAvailableAddons(), 
            "comptes", "get", "connectUser", 
            (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getLevel() : -1,
            (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user'] : null, 
            (!empty($_POST)) ? $_POST : null);

    if (!is_array($rtrn) && array_key_exists("json_result", $rtrn)
    OR !(is_array($rtrn) && array_key_exists("output_buffer", $rtrn) && is_array($rtrn["output_buffer"]) && 
    ((array_key_exists("isAccount", $rtrn["output_buffer"]) && $rtrn["output_buffer"]['isAccount'] == true) OR !array_key_exists("isAccount", $rtrn["output_buffer"])))){
      if ($Serveur_Config['mtnc'] == true)
        require_once(ROOT . 'installation/mtnc.php');
      else
        header('Location: '. LINK);
    }
        
    header('Location: '. LINK);
  }catch (Throwable $e){
    if ($Serveur_Config['mtnc'] == true)
      require_once(ROOT . 'installation/mtnc.php');
    else
      header('Location: '. LINK);
  }
}