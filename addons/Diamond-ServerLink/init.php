<?php 
define("DServerLink", true);
define("DServerLinkVersion", "1.0.0");
define("DServerLinkGamesSupported", array("Minecraft-Java"));

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
require_once(ROOT . 'addons/Diamond-ServerLink/LinkWithMinecraft.php');
$linkmc = new DServerLink\QueryWithMinecraft($controleur_def, $cm);

/*
Template for RCON :
$rconmc = new RConWithMinecraft($controleur_def);
$rconmc->connect();
$rconmc->execOnServer(1,  "banlist");
$rconmc->disconnect();*/