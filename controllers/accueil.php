<?php
global $erreur_vote;
//On instancie le controleur en lui passant l'acces aux fichiers config
$controleur_def = new Controleur($Serveur_Config);
//On charge le model
$controleur_def->loadModel('accueil');
//On récupère la connexion à la base de donnée pour la fonction de récupération des articles
$news = getNews($controleur_def->bddConnexion(), 0, 3);
//On parcourt le tableau retourner par getPosts
foreach ($news as $key => $new) {
  //On les mets en forme
  $news[$key]['user'] = 'Par ' . $new['user'];
  $news[$key]['date_news'] = 'Le ' . $new['date_news'];
  $news[$key]['id'] = $new['id'];
  $news[$key]['img'] = $new['img'];
  $news[$key]['type_img'] = $new['type_img'];
  //On utilise le fonction htmlspecialchars pour eviter les failles de sécurités
  $news[$key]['news'] = wordwrap(htmlspecialchars($new['news']), 50, "<br />\r\n", true);
}

//On charge la vue, la fonction va charger 3 fichiers.
$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
