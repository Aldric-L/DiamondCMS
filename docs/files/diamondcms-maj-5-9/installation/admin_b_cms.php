<?php 
fopen(ROOT . 'installation/blocked.dcms', "w+");
define('DIAMOND_BLOCKED', true);
require_once('infodiamondcms.php');