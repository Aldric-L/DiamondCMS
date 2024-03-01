<?php 
namespace update;
require_once ROOT . 'models/update.class.php';
require_once 'update.class.php';

function init($path_to_files, $path_to_update_files){
    return new Update($path_to_files, $path_to_update_files);
} 