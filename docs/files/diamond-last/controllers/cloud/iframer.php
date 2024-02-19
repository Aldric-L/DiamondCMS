<?php

$user_level = (isset($_SESSION["user"]) && $_SESSION["user"] instanceof User) ? $_SESSION["user"]->getLevel() : 1;

function scan($dir, $path, $user_level){
	$files = array();
    $conf = array();
	if(file_exists($dir)){
        $global_conf = array();
        $level_min = 0;
        $prelocked = false;
        if (file_exists($dir . "/locked_files.dfiles")){
            $conf = json_decode(file_get_contents($dir . "/locked_files.dfiles"), true);
            if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]))
                $global_conf = $conf['__GLOBAL-FOLDER-DIAMONDCONF__'];
            if (array_key_exists("access_level", $global_conf) && is_numeric($global_conf["access_level"]))
                $level_min = intval($global_conf["access_level"]);
            if ((array_key_exists("locked", $global_conf) && ($global_conf["locked"])) OR (array_key_exists("protected", $global_conf) && ($global_conf["protected"])))
                $prelocked = true;
        }
		foreach(scandir($dir) as $f) {
			if(!is_string($f[0]) || $f[0] == '.' || substr($f, -7, 7) == ".dfiles")
				continue; 
            
			if(is_dir($dir . '/' . $f)) {
                if (is_array($conf) && array_key_exists($f, $conf))
                    $conf_of_file = $conf[$f];
                else 
                    $conf_of_file = array();
                
                if (array_key_exists("hidden", $conf_of_file) && is_bool($conf_of_file["hidden"]) && $conf_of_file["hidden"] && $user_level < 3)
                    continue;

                if ((array_key_exists("access_level", $conf_of_file) && intval($conf_of_file["access_level"]) > $user_level) OR $level_min > $user_level){
                    array_push($files, array_merge(array(
                        "dispname" => str_replace("_", " ", $f),
                        "prefix" => "",
                        "name" => $f,
                        "type" => "folder",
                        "path" => $dir . '/' . $f,
                        "last_edit" => date("d/m/Y H:i:s", filemtime($dir . '/' . $f)),
                        "icon" => "ion-ios-lock",
                        "nofile_path" => $dir . '/',
                        "noroot_path" => str_replace(ROOT, "", $dir . '/' . $f),
                        "locked" => true,
                        "prelocked" => $prelocked,
                        "global_conf" => $global_conf,
                        "global_level_min" => $level_min,
                    ), $conf_of_file));
                }else {
                    array_push($files, array_merge(array(
                        "dispname" => str_replace("_", " ", $f),
                        "prefix" => "",
                        "name" => $f,
                        "type" => "folder",
                        "path" => $dir . '/' . $f,
                        "last_edit" => date("d/m/Y H:i:s", filemtime($dir . '/' . $f)),
                        "icon" => "fa-folder",
                        "nofile_path" => $dir . '/',
                        "noroot_path" => str_replace(ROOT, "", $dir . '/' . $f),
                        "prefix_link" => LINK . "cloud/" . str_replace(ROOT . "views/uploads/", "", $dir),
                        "link" => LINK . "cloud/" . str_replace(ROOT . "views/uploads/", "", $dir) . $f, 
                        "locked" => false OR $prelocked,
                        "prelocked" => $prelocked,
                        "global_conf" => $global_conf,
                        "global_level_min" => $level_min,
                    ), $conf_of_file));
                }
                
			}
			else if (substr($f, -7, 7) != ".dfiles") {
                if (is_array($conf) && array_key_exists($f, $conf))
                    $conf_of_file = $conf[$f];
                else 
                    $conf_of_file = array();

                if (array_key_exists("hidden", $conf_of_file) && is_bool($conf_of_file["hidden"]) && $conf_of_file["hidden"] && $user_level < 3)
                    continue;

                if ((array_key_exists("access_level", $conf_of_file) && intval($conf_of_file["access_level"]) > $user_level) OR $level_min > $user_level){
                    array_push($files, array_merge(array(
                        "dispname" => str_replace("_", " ", (strlen(explode("_", $f)[0])==13) ? implode("_", array_slice(explode("_", $f), 1)) : $f),
                        "prefix" => (strlen(explode("_", $f)[0])==13) ? (explode("_", $f)[0]) : "",
                        "name" => $f,
                        "type" => "file",
                        "icon" => "ion-ios-lock",
                        "last_edit" => date("d/m/Y H:i:s", filemtime($dir . '/' . $f)),
                        "ext" => pathinfo($dir . '/' . $f)['extension'],
                        "path" => $dir . '/' . $f,
                        "nofile_path" => $dir . '/',
                        "noroot_path" => str_replace(ROOT, "", $dir . '/' . $f),
                        "size" => filesize($dir . '/' . $f),
                        "locked" => true,
                        "prelocked" => $prelocked,
                        "global_conf" => $global_conf,
                        "global_level_min" => $level_min,
                    ), $conf_of_file));
                }else {
                    if (array_key_exists("extension", $ext=pathinfo($dir . '/' . $f))){
                        switch (mb_strtolower($ext=$ext['extension'])){
                            case "js":
                                $icon = "fa-js";
                                break;
                            case "doc":
                            case "docx":
                                $icon = "fa-file-word";
                                break;
                            case "html":
                                $icon = "fa-html5";
                                break;
                            case "pdf":
                                $icon = "fa-file-pdf";
                                break;
                            case "css":
                                $icon = "fa-css3";
                                break;
                            case "zip":
                                $icon = "fa-archive";
                                break;
                            case "rar":
                                $icon = "fa-archive";
                                break;
                            case "mp3":
                                $icon = "fa-file-audio";
                                break;
                            case "mp4":
                                $icon = "fa-file-video";
                                break;
                            case "avi":
                                $icon = "fa-file-video";
                                break;
                            case "mkv":
                                $icon = "fa-file-video";
                                break;
                            case "gif":
                            case "bmp":
                                $icon = "fa-file-image";
                                break;
                            case "png":
                            case "jpg":
                            case "jpeg":
                                $icon = LINK . "cloud/" . str_replace(ROOT . "views/uploads/", "", $dir) . str_replace("." . $ext, "", $f) . "/" . $ext;
                                break;
                            default:
                                $icon = "fa-file-alt";
                                break;
                        }
                    }else {
                        $icon = "fa-file-alt";
                        $ext = "unknown";
                    }
                    array_push($files, array_merge(array(
                        "dispname" => str_replace("_", " ", (strlen(explode("_", $f)[0])==13) ? implode("_", array_slice(explode("_", $f), 1)) : $f),
                        "prefix" => (strlen(explode("_", $f)[0])==13) ? (explode("_", $f)[0]) : "",
                        "name" => $f,
                        "type" => "file",
                        "icon" => $icon,
                        "last_edit" => date("d/m/Y H:i:s", filemtime($dir . '/' . $f)),
                        "ext" => $ext,
                        "path" => $dir . '/' . $f,
                        "nofile_path" => $dir . '/',
                        "noroot_path" => str_replace(ROOT, "", $dir . '/' . $f),
                        "size" => filesize($dir . '/' . $f),
                        "prefix_link" => LINK . "cloud/" . str_replace(ROOT . "views/uploads/", "", $dir),
                        "link" => LINK . "cloud/" . str_replace(ROOT . "views/uploads/", "", $dir) . str_replace("." . $ext, "", $f) . "/" . $ext, 
                        "locked" => false or $prelocked,
                        "prelocked" => $prelocked,
                        "global_conf" => $global_conf,
                        "global_level_min" => $level_min,
                    ), $conf_of_file));
                }
                
			}
		}
	}
	return $files;
}
//var_dump(ROOT . $path, scan(ROOT . $path));
$scanned_files = scan(ROOT . $path, $path, $user_level);
$real_previous_path = ROOT . "views/uploads/" . (isset($previous_path) ? $previous_path : "");
if ($real_previous_path[strlen($real_previous_path)-1] != '/')
    $real_previous_path .= "/";
if ($path[strlen($path)-1] != '/')
    $path .= "/";
require_once(ROOT . "views/themes/" . $Serveur_Config['theme'] . "/pages/cloud/iframer.php");