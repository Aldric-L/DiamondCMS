<?php 
/**
 * Page Admin/errors
 * 
 * Sur cette page on affiche les 50 dernières erreurs et on permet de les supprimer.
 * Cette page est réalisée en pageBuilder, il n'y a donc pas de vue.
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2017, 2022, 2023
 * 
 */
$controleur_def->loadModel('admin/accueil');

$errors_raw = $controleur_def->getErrorsInLog();
$errors_content = array();
if (isset($param[2]) && $param[2] == "all")
    $min = sizeof($errors_raw);
else
    $min = (sizeof($errors_raw) > 100) ? 100 : sizeof($errors_raw);
for ($i=sizeof($errors_raw); $i>sizeof($errors_raw)-$min; $i--){
    $errors_raw[$i-1] = array_merge($errors_raw[$i-1], $controleur_def->getError($errors_raw[$i-1]['code']));
    array_push($errors_content, $errors_raw[$i-1]);
}

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);

// On construit la page, avec le nom et la description
$adminBuilder = $tb->AdminBuilder("Erreurs levées aux utilisateurs", "Toutes les erreurs envoyés aux utilisateurs, avec leur description, et une aide pour les regler.");

// On écrit le panel de gauche pour aider les admins à comprendre les erreurs
ob_start(); ?>
<p class="text-justify"><strong class="text-custom">Vous ne comprenez pas ces erreurs ?</strong><br />Vous pouvez consulter une documentation de toutes les erreurs émises par le CMS pour mieux les comprendre et réparer les possibles problèmes qu'elles révèlent.</p>
<hr>
<p><strong class="">Documentation des erreurs :</strong> <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Erreurs-et-notifications">Cliquez-ici</a><br/></p><hr>
<?php $first_content_panel1 = ob_get_clean();

$showtype0errors = $tb->AdminButton("Afficher les erreurs secondaires", "btn-sm btn-light")->addAttr("style", "width:100%;")->addAttr("id", "showtype0");

$content_panel1 = $tb->UIArray($tb->UIString($first_content_panel1),$showtype0errors);

$panel1 = $tb->AdminPanel("Besoin d'aide ?", "fa-info-circle", $content_panel1, "lg-4");
$adminBuilder->addPanel($panel1);

if (is_array($errors_content) && !empty($errors_content)){
    //On commence par générer la liste des erreurs
    $list = $tb->AdminList();
    foreach ($errors_content as $k => $e){
        //Chaque erreur ouvre droit sur un modal qu'on écrit
        $adminBuilder->addModal(
            $modal = $tb->AdminModal("Information sur l'erreur ". $e['display_code'], "me_" . $k, 
                $tb->UIString("<p><strong>Code originel :</strong> " . $e['truecode'] . "<br>" . "<strong>Code enregistré :</strong> " . $e['code'] . "<br>" 
                . "<strong>Propriétaire de l'erreur :</strong> " . $e['owner'] . "<br>" 
                . "<strong>Message :</strong> <em>" . $e['msg'] . "</em><br>"
                . "<strong>Degré de gravité :</strong> " . $e['type'] . " / 5<br><hr>"
                . "<strong>Date de survenance :</strong> " . $e['date'] . "<br>"
                . "<strong>Page concernée :</strong> " . $e['page'] . "<br>"
                . "<strong>Utilisateur concerné :</strong> " . (($e['user'] == null) ? "Utilisateur non-connecté" : $controleur_def->getPseudoById($e['user'])) . "<br>" . "</p>"), ""));
        $left = "";
        ob_start(); ?><i class="fa <?php echo (isset($e['icon'])) ? $e['icon'] : "fa-warning"; ?> fa-fw"></i>
        <?php $left .= ob_get_clean();
        $left .= $e['msg'] ." <small>(Code " . $e['display_code'] .")</small>";
        $list->addField($tb->UIString($left), $tb->UIString("<small>le " . $e['date'] ."</small>"), $modal, ((intval($e['type']) == 0 && $k != 0) ? "type0" : null) );
    }
}else {
    $list = $tb->UIString("<p><em>Il n'y a aucune erreur à afficher.</em><p>");
}
$panel2 = $tb->AdminPanel("Dernières erreurs levées par le systeme", "fa-bell", $list, "lg-8");
$adminBuilder->addPanel($panel2);

$adminBuilder->addCSSToRender('.type0{display: none; }');
$adminBuilder->addJSToRender('$("#showtype0").click((e)=>{
    if ($(".type0").css("display") == "none")
        $(".type0").css("display", "block");
    else
        $(".type0").css("display", "none");
});');

// Et on oublie pas d'afficher les components qu'on a préparés !
echo $adminBuilder->render();