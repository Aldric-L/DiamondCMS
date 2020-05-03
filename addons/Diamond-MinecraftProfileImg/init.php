<?php 
if (file_exists(ROOT . 'addons/Diamond-MinecraftProfileImg/disabled.dcms')){
    define("DMcProfileImg", false);
}else {
    define("DMcProfileImg", true);
}
define("DMcProfileImgVersion", "1.0.0");

