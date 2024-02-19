<?php 

if (isset($param[1]) && !empty($param[1]) && $param[1] == "browser"){
    $controleur_def->loadJS("cloud/iframe.notadmin");
    $controleur_def->loadView('pages/cloud/browser', '', 'DiamondCloud');
    die;
}


$path = $path_img = $path_folder = "views/uploads/";

if (isset($param[1]) && !empty($param[1])){
    $param_temp = array_slice($param, 1);
    $unknown = false;
    if (end($param_temp) == "unknown"){
        array_pop($param_temp);
        $unknown = true;
    }
    
    foreach ($param_temp as $key => $p){
        if (intval($key) != sizeof($param_temp)-1){
            $path_img .= $p;
            if (intval($key) != sizeof($param_temp)-2)
                $path_img .= "/";
            $path_folder .= $p;
            $path_folder .= "/";
        }else {
            if ($unknown)
                $path_img .= "/";
            else
                $path_img .= ".";
            $path_img .= $p;
            $path_folder .= $p;
            $path_folder .= "/";
        }
    }
}

if (is_dir(ROOT . $path_folder)){
    $path = $path_folder;
}else if(file_exists(ROOT . $path_img)){
    $path = $path_img;
}else {
    var_dump(ROOT . $path_folder, ROOT . $path_img);
    die("Error");
}

$path_array = explode("/", $path);
if (sizeof($path_array) > 2 && $path != "views/uploads/"){
    $working_array = array_slice($path_array, 2);
    $working_array = array_filter($working_array);
    $target = array_pop($working_array);
    $previous_path = (sizeof($working_array) > 0) ? implode("/",  $working_array) : "";
    $can_goback = true;
    $previous_link = LINK . "cloud/" . $previous_path;
}else {
    $can_goback = false;
}

if (is_dir(ROOT . $path_folder)){
    require_once("cloud/iframer.php");
}else if(file_exists(ROOT . $path_img)){
    $previous_path = (isset($previous_path) ? $previous_path : "");
    if (strlen($previous_path) > 1 && $previous_path[strlen($previous_path)-1] != '/')
        $previous_path .= "/";
        
    if (file_exists(ROOT . "views/uploads/" . $previous_path . "locked_files.dfiles")){
        $level_min = 1;
        $conf = json_decode(file_get_contents(ROOT . "views/uploads/" . $previous_path . "locked_files.dfiles"), true);
        if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]))
            $level_min = max($level_min, intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]));
        
        if (is_array($conf) && array_key_exists($target, $conf) && is_array($conf[$target]) &&
            array_key_exists("access_level", $conf[$target]) && is_numeric($conf[$target]["access_level"]) 
            && intval($conf[$target]["access_level"]) > 1){
                if ((array_key_exists("locked", $conf[$target]) and $conf[$target]["locked"]) or (array_key_exists("protected", $conf[$target]) and $conf[$target]["protected"]))
                    $level_min = intval($conf[$target]["access_level"]);
                else
                    $level_min = max($level_min, intval($conf[$target]["access_level"]));
        }
        if ($level_min > 1 AND 
        (!isset($_SESSION['user']) or !($_SESSION['user'] instanceof User) or (isset($_SESSION['user']) and $_SESSION['user'] instanceof User and $_SESSION['user']->getlevel() < $level_min))){
            if (!file_exists(ROOT . $path = 'views/uploads/img/img_blocked.jpg')){
                die("Forbidden access");
            }
        }
    }

    $extension = pathinfo(ROOT . $path);
    $extension = array_key_exists("extension", $extension) ? $extension['extension'] : "";
    header('Content-type:'.$extension);
    header('Cache-control:public, max-age=604800');
    header('Accept-ranges:bytes');
    header('Content-length:'.filesize($path));
    header('Last-Modified: '.date(DATE_RFC2822, filemtime(ROOT . $path)));
    header_remove('pragma');
    echo(file_get_contents(ROOT . $path));
}