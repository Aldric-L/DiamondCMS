<?php 
/**
 * Page Admin/news
 * 
 * Sur cette page on permet aux administrateurs d'utiliser un système d'actualités pour leur serveur
 * Cette page est réalisée en pageBuilder, il n'y a donc pas de vue.
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2017, 2022
 * 
 */
$news = simplifySQL\select($controleur_def->bddConnexion(), false, "d_news", array("id", "name", "content_new", array("date", "%d/%m/%Y à %h:%i:%s", "date"), "img", "user"), false, "date", true);

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);

// On construit la page, avec le nom et la description
$adminBuilder = $tb->AdminBuilder("Actualités de votre réseau", "DiamondCMS fournit un systeme de news pour votre communauté. Vous pouvez ajouter ou supprimer une news. Les news sont affichées sur la page d'accueil.
<br><em>Attention, le système de cache peut induire un délai de quelques minutes entre la publication de la news et son affichage.</em>");

// On écrit le panel de gauche pour aider les admins à comprendre les erreurs
$news_form = $tb->AdminForm("newsForm")->addTextField("name","Titre de la news", "", true)->addTextAreaField("message","Contenu de la news", "", 5, true)->addFileField("img","Associer une image (Optionnel)");
$news_form->addAPIButton($tb->AdminAPIButton("Envoyer", "btn-custom", LINK . "api/", "admin", "set", "addNews", $news_form))->setButtonsLine('class="text-right"');
$panel1 = $tb->AdminPanel("Ajouter une news", "fa-plus-circle", $news_form, "lg-5");
$adminBuilder->addPanel($panel1);

if (is_array($news) && !empty($news)){
  //On commence par générer la liste des demandes de news
  $list = $tb->AdminList();
  foreach ($news as $n){
      //Chaque demande ouvre droit sur un modal qu'on écrit
      if (substr($n['img'], 0, 4) == "http"){
          $n['final_img_link'] = $n['img'];
      }else if (substr($n['img'], -4, 4) == ".png"){
          $n['final_img_link'] =  "getimage/png/" . substr($n['img'], 0, -4);
      }else if (substr($n['img'], -4, 4) == ".jpg"){
          $n['final_img_link'] =  "getimage/jpg/" . substr($n['img'], 0, -4). "/";
      }else if (substr($n['img'], -4, 4) == "jpeg"){
          $n['final_img_link'] =  "getimage/jpeg/" . substr($n['img'], 0, -5). "/";
      }else{
          $n['final_img_link'] =  "getimage/png/-/no_profile/";
      }
      ob_start(); ?>
      <?php if ($n['img'] != "noimg"){ ?>
        <p class="text-center"><img class="img-rounded" style="max-width: 80%;" src="<?php echo LINK . $n['final_img_link'];?>" alt="<?php echo $n['name'];?>" /></p>
      <?php } ?>
      <p><?php echo $n['content_new']; ?></p>
      <p class="text-right" style="margin-bottom: 0; padding-bottom: 0;"><em>Créée par <?php echo User::getPseudoById($controleur_def->bddConnexion(), $n['user']); ?> le <?php echo $n['date']; ?></em></p>
      <?php $mod_content = ob_get_clean(); 
      $adminBuilder->addModal(
          $modal = $tb->AdminModal($n['name'], "news_" . $n['id'], 
                                  $tb->UIString($mod_content), "", "modal-lg"));
      $modal->addAPIButton($tb->AdminAPIButton("Supprimer", "btn-danger", LINK . "api/", "admin", "set", "delNews", "id=" . (string)$n['id'], "", "true", "true"));
      $list->addField($tb->UIString("<strong>" . $n['name']."</strong><small> par ". User::getPseudoById($controleur_def->bddConnexion(), $n['user']) . "</small>"), $tb->UIString("<em>le " . $n['date'] ."</em>"), $modal);
    }
}else {
  $list = $tb->UIString("<p><em>Aucune actualité n'est à afficher.</em><p>");
}
//La page est constituée d'un panel principal qu'on écrit
$panel2 = $tb->AdminPanel("News enregistrées", "fa-newspaper-o", $list, "lg-7");
$adminBuilder->addPanel($panel2);

// Et on oublie pas d'afficher les components qu'on a préparés !
echo $adminBuilder->render();