<?php 
function checkFiles($root){
    if (@file_exists($root[0] . "models/DiamondCore/init.php")
    && @file_exists($root[0] . "views/themes/default/admin/pages.php")
    && @file_exists($root[0] . "js/themes/default/admin/pages/modify.js")
    && @file_exists($root[0] . "logs/dev_errors.log")
    && @file_exists($root[0] . "controllers/admin/pages.php")){
        return true;
    }
    return false;
}