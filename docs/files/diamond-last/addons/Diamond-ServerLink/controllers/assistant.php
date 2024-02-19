<?php 

PageBuilders\ThemeBuilder::startCache("dsl_mainassistant", PageBuilders\ThemeBuilder::CACHE_STATIC, function ($controleur_def){
    $controleur_def->loadViewAddon(ROOT . "addons/Diamond-ServerLink/views/assistant.php", true, false, "Assistant de configuration");
});