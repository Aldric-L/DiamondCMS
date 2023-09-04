<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    if (($cache = PageBuilders\ThemeBuilder::renderFromCacheIfPossible("admin_noauth", PageBuilders\ThemeBuilder::CACHE_STATIC)) !== false){
        echo $cache;
    }else {
        $tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
        $adminBuilder = $tb->AdminBuilder("Vous n'avez pas l'autorisation d'accéder à ces réglages", "Veuillez contacter un administrateur pour obtenir un grade plus élevé.", true, "admin_noauth", PageBuilders\ThemeBuilder::CACHE_STATIC);
        echo $adminBuilder->render();
        die;
    }
}

PageBuilders\ThemeBuilder::startCache("admin_console", PageBuilders\ThemeBuilder::CACHE_STATIC, function ($controleur_def){
    $controleur_def->loadJS("admin/console");
    $controleur_def->loadViewAdmin('admin/console', 'admin/console', 'Console DiamondCMS');
});
