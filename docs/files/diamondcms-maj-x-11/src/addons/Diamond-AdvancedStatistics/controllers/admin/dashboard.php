<?php 

/**
 * Page Admin/Diamond-AdvancedStatistics
 * 
 * Sur cette page on traite les statistiques collectées et on les affiche
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2023
 * 
 */

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
$das_config = cleanIniTypes(parse_ini_file(ROOT . 'addons/Diamond-AdvancedStatistics/config.ini', true));

// On construit la page, avec le nom et la description
$adminBuilder = $tb->AdminBuilder("Statistiques", "L'addon Diamond-AdvancedStatistics vous permet de visualiser l'évolution de la fréquentation de votre site internet et ainsi améliorer votre présence digitale.");

require_once(ROOT . "addons/Diamond-AdvancedStatistics/models/corestats.php");
$corestats = new DiamondAdvancedStatistics\CoreStats($controleur_def->bddConnexion());

$dataraw = $corestats->totalHitsByDayOrMonth((isset($das_config['should_count_admin']) ? $das_config['should_count_admin'] : true));
$emptyStats = empty($dataraw['total_hits']);
$labels = array();
$dta_hits = array();
$dta_user = array();
if (sizeof($dataraw['total_hits']) > 99){
    foreach ($dataraw['total_month_hits'] as $date => $hitsbydate){
        array_push($labels, $date);
        array_push($dta_hits, $hitsbydate);
    }
    foreach ($dataraw['total_month_users'] as $date => $hitsbydate){
        array_push($dta_user, $hitsbydate);
    }
}else {
    foreach ($dataraw['total_hits'] as $date => $hitsbydate){
        array_push($labels, $date);
        array_push($dta_hits, $hitsbydate);
    }
    foreach ($dataraw['total_users'] as $date => $hitsbydate){
        array_push($dta_user, $hitsbydate);
    }
}
foreach ($dta_user as $k => $date){
    if (isset($date['no_usr']))
        $dta_user[$k] = ((sizeof($date)-1 >= 0) ? sizeof($date)-1 : 0);
    else 
        $dta_user[$k] = sizeof($date);
}

$labels= array_reverse($labels); $dta_hits= array_reverse($dta_hits); $dta_user= array_reverse($dta_user);
$areachart = $tb->AdminAreaChart($adminBuilder, array("labels" => $labels, "datasets" => array($dta_user, $dta_hits)), array("y_label" => array("Visites","Clics")), "hitschart");

$noStats = $tb->UIString("<p class=\"text-center\"><em>Aucune visite enregistrée pour l'instant.</em></p>");

$clicpanel = $tb->AdminPanel("Visiteurs uniques et clics enregistrés", "fa-mouse-pointer", ($emptyStats ? $noStats : $areachart), "lg-12");

$best_pages = $corestats->bestPages(true, true, (isset($das_config['should_count_admin']) ? $das_config['should_count_admin'] : true));
$labels = array();
$data = array();
foreach ($best_pages as $name => $p){
    array_push($labels, $name);
    array_push($data, $p);
}
$labels= array_reverse($labels); $data= array_reverse($data);


$piechart = $tb->AdminPieChart($adminBuilder, array("labels" => $labels, "data" => $data), "pageschart", false);
$piepanel = $tb->AdminPanel("Pages les plus vues (en %)", "fa-file-text", ($emptyStats ? $noStats : $piechart), "lg-4");


$best_referer = $corestats->bestReferer(true, true, true, (isset($das_config['should_count_admin']) ? $das_config['should_count_admin'] : true));
$labels = array();
$data = array();
foreach ($best_referer as $name => $p){
    array_push($labels, $name);
    array_push($data, $p);
}
$labels= array_reverse($labels); $data= array_reverse($data);


$refererpiechart = $tb->AdminPieChart($adminBuilder, array("labels" => $labels, "data" => $data), "refererchart", false);
$refererpanel = $tb->AdminPanel("Provenance du trafic (en %)", "fa-exchange", ($emptyStats ? $noStats : $refererpiechart), "lg-4");


$configform = $tb->AdminForm("das_config", false)
    ->addCheckField("should_count_admin", "Comptabiliser les pages admin", (isset($das_config['should_count_admin']) ? $das_config['should_count_admin'] : true))
    ->addCheckField("async", "Activer le mode asynchrone", (isset($das_config['async']) ? $das_config['async'] : true));
$configform
->addAPIButton($tb->AdminAPIButton("Purger la base de donnée", "btn-danger", LINK . "api/", "das_statistics", "set", "reset", "", "", "true"))
->addAPIButton($tb->AdminAPIButton("Enregistrer", "btn-custom", LINK . "api/", "das_statistics", "set", "editConfig", $configform, "", "true"))
->setButtonsLine('class="text-right mt-3"');

$ecodanger = $tb->UIString("<em>Attention, cet addon, aussi bon soit-il, peut ralentir le fonctionnement de votre site internet et charge la base de données. Il n'est pas conseillé de l'utiliser sans l'option asynchrone sur des serveurs web fragiles.</em><hr>");

$configpanel = $tb->AdminPanel("Configuration", "fa-wrench", $tb->UIArray($ecodanger, $configform), "lg-4");

$adminBuilder->addPanel($clicpanel)->addPanel($piepanel)->addPanel($refererpanel)->addPanel($configpanel);

// Et on oublie pas d'afficher les components qu'on a préparés !
echo $adminBuilder->render();