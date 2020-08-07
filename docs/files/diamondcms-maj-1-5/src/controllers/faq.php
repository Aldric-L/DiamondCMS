<?php
if ($Serveur_Config['en_faq']){
  $controleur_def->loadModel('faq');
  
  $faqs = simplifySQL\select($controleur_def->bddConnexion(), false, "d_faq", "*", false, "id", true, array(0, 10));

  $controleur_def->loadView('pages/faq', '', 'F.A.Q.');
}else {
  header('Location: '. LINK);
}
