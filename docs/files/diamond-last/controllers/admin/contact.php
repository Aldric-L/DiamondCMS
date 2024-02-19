<?php 
/**
 * Page Admin/contact
 * 
 * Sur cette page on affiche les demandes de contact et on permet de les supprimer.
 * Cette page est réalisée en pageBuilder, il n'y a donc pas de vue.
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2017, 2022
 * 
 */

$contacts = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), false, "d_contact", "*", false, "id", true));
$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
if (($cache = $tb::renderFromCacheIfPossible("admin_contact", PageBuilders\ThemeBuilder::CACHE_DYN)) !== false){
    echo $cache;
}else {
    // On construit la page, avec le nom et la description
    $adminBuilder = $tb->AdminBuilder("Page contact", "DiamondCMS fournit un systeme de contact pour permettre, par exemple, à des organismes exterieurs de vous contacter de manière privée. Comme ce formulaire est ouvert à tous (sans comptes), il faut donc répondre à ces demandes par mail.", true, "admin_contact", PageBuilders\ThemeBuilder::CACHE_DYN);

    if (is_array($contacts) && !empty($contacts)){
        //On commence par générer la liste des demandes de contact
        $list = $tb->AdminList();
        foreach ($contacts as $c){
            //Chaque demande ouvre droit sur un modal qu'on écrit
            $adminBuilder->addModal(
                $modal = $tb->AdminModal("Demande de contact par ". $c['name'], "contact_" . $c['id'], 
                                        $tb->UIString("<h3>Demande de " . $c['name'] . "</h3><p><em>via ". htmlspecialchars_decode($c['email']). " le ". $c['date'] . "</p><p>". htmlspecialchars_decode($c['text']) ."</em></p>"), "", "modal-lg"));
            $modal->addAPIButton($tb->AdminAPIButton("Supprimer", "btn-danger", LINK . "api/", "admin", "set", "delContact", "id=" . (string)$c['id'], "", "true", "true"));
            $list->addField($tb->UIString(
                (!boolval($c['seen']) ? '<span style="color: red;!important"><i style="color: red !important;" class="fa fa-exclamation fa-fw" aria-hidden="true"></i>  </span>' : '') . 
                "<strong>Demande de contact par " . $c['name']."</strong> ". $c['email']), $tb->UIString("<em>le " . $c['date'] ."</em>"), $modal, boolval($c['seen']) ? "" : "ajax-simpleSend", "", null, 
            boolval($c['seen']) ? array() : 
            array(
                'data-api' => LINK . "api/",
                "data-module" => "admin/",
                "data-verbe" => "get",
                "data-func" => "contactSeen",
                "data-tosend" => "id=".$c['id'],
                "data-reload" => "false"
            ));
        }
    }else {
        $list = $tb->UIString("<p><em>Il n'y a aucune demande de contact à afficher.</em><p>");
    }
    //La page est constituée d'un panel principal qu'on écrit
    $panel1 = $tb->AdminPanel("Demandes de contact enregistrées", "fa-bell", $list, "lg-12");
    $adminBuilder->addPanel($panel1);

    // Et on oublie pas d'afficher les components qu'on a préparés !
    echo $adminBuilder->render();
}