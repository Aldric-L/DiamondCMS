<?php 
/**
 * Page Admin/notify
 * 
 * Sur cette page on affiche les 50 dernières notications envoyées à TOUS les admiistrateurs
 * Cette page est réalisée en pageBuilder, il n'y a donc pas de vue.
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2017, 2022
 * 
 */
$notify_page =  simplifySQL\select($controleur_def->bddConnexion(), false, "d_notify", "*", array(array("USER", "=", "admin")), "id", true, 50);

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);

// On construit la page, avec le nom et la description
$adminBuilder = $tb->AdminBuilder("Notifications envoyées aux administrateurs", "Les 50 notifications envoyés dernièrement avec leur date et leur contenu.");

// On écrit le panel de gauche pour aider les admins à comprendre les erreurs
ob_start(); ?>
<p class="text-justify"><strong class="text-custom">Vous n'avez jamais vu ces notifications ?</strong><br />Lorsque le CMS notifie les administrateurs, il ne le fait qu'une seule fois, c'est à dire que la notification ne s'affiche qu'une fois au premier administrateur connecté. Ainsi, si vous ne connaissez pas ces notifications, c'est qu'il est probable qu'un autre administrateur ait-été notifié avant vous. <br>Si malgré cette explication, vous souhaitez en savoir plus, vous pouvez consulter la documentation à propos des notifications du CMS. </p>
<hr>
<p><strong class="">Documentation des notifications :</strong> <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Erreurs-et-notifications">Cliquez-ici</a><br/></p>
<?php $content_panel1 = ob_get_clean();

$panel1 = $tb->AdminPanel("Besoin d'aide ?", "fa-info-circle", $tb->UIString($content_panel1), "lg-4");
$adminBuilder->addPanel($panel1);

if (is_array($notify_page) && !empty($notify_page)){
    //On commence par générer la liste des notifications
    $list = $tb->AdminList();
    foreach ($notify_page as $n){
        $left = "";
        ob_start(); ?>
        <?php if ($n['type'] == 1){ //CONTACT ?>
            <i class="fa fa-comment" aria-hidden="true"></i>
        <?php }else if ($n['type'] == 2) { //SUPPORT ?>
            <i class="fa fa-question-circle-o" aria-hidden="true"></i>
        <?php }else if ($n['type'] == 3) { //ERROR ?>
            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
        <?php }else if ($n['type'] == 4) { //FORUM ?>
            <i class="fa fa-comments-o" aria-hidden="true"></i>
        <?php }else if ($n['type'] == 5) { //BOUTIQUE ?>
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        <?php }else { ?>
            <i class="fa fa-quote-right" aria-hidden="true"></i>
        <?php } 
        $left .= ob_get_clean();
        $left .= "<strong>" . $n['title'] ."</strong> " . $n['content'] ."";
        $list->addField($tb->UIString($left), $tb->UIString("<small>le " . $n['date'] ."</small>"), $n['link']);
    }
}else {
    $list = $tb->UIString("<p><em>Il n'y a aucune notification à afficher.</em><p>");
}
$panel2 = $tb->AdminPanel("Notifications aux administrateur du site", "fa-bell", $list, "lg-8");
$adminBuilder->addPanel($panel2);

// Et on oublie pas d'afficher les components qu'on a préparés !
echo $adminBuilder->render();