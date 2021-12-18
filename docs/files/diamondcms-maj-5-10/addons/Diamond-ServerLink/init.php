<?php 
if (file_exists(ROOT . 'addons/Diamond-ServerLink/disabled.dcms')){
    define("DServerLink", false);
}else {
    define("DServerLink", true);
}
//18-12-2021 @author Aldric L.
define("DServerLinkVersion", "1.1");
define("DServerLinkGamesSupported", array("Minecraft-Java", "GMod", "CS-GO", "Team-Fortress 2", "Minecraft-MPCE"));

require_once __DIR__ . '/src/MinecraftQuery.php';
require_once __DIR__ . '/src/MinecraftQueryException.php';


require_once __DIR__ . '/src/SourceQuery/Exception/SourceQueryException.php';
require_once __DIR__ . '/src/SourceQuery/Exception/AuthenticationException.php';
require_once __DIR__ . '/src/SourceQuery/Exception/InvalidArgumentException.php';
require_once __DIR__ . '/src/SourceQuery/Exception/SocketException.php';
require_once __DIR__ . '/src/SourceQuery/Exception/InvalidPacketException.php';

require_once __DIR__ . '/src/SourceQuery/Buffer.php';
require_once __DIR__ . '/src/SourceQuery/BaseSocket.php';
require_once __DIR__ . '/src/SourceQuery/Socket.php';
require_once __DIR__ . '/src/SourceQuery/SourceRcon.php';
require_once __DIR__ . '/src/SourceQuery/GoldSourceRcon.php';
require_once __DIR__ . '/src/SourceQuery/SourceQuery.php';


require_once(ROOT . 'addons/Diamond-ServerLink/configmanager.php');
$cm = new DServerLink\ConfigManager();
require_once(ROOT . 'addons/Diamond-ServerLink/Link.php');
$servers_link = new DServerLink\Query($controleur_def, $cm);

