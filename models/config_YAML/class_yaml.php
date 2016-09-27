<?php
//Merci à Mustangostang pour son travail sur cette API (trouvable sur gitHub, avec une license libre)
require_once('yamlapi.class.php');

/**
 * Write - Une class pour écrire dans un fichier YAML
 * @author Aldric.L
 * @author Mustangostang
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 */

class Write{
    public function __construct($reading_file, $array){
      $parser = new Spyc;
      $ymlFormat  = $parser->YAMLDump($array,4,60);
			//$ymlFormat = Spyc::YAMLDump($array,4,60);

			$file = fopen($reading_file, 'a+');
			ftruncate($file, 0);
			fputs($file, '#/!\Ce Fichier contient des informations essentielles au fonctionnement du site/!\ ' . $ymlFormat);
		}
}

/**
 * Load - Une class pour lire un fichier YAML
 * @author Aldric.L
 * @author Mustangostang
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */

class Load{
	private $recup;

	public function __construct($reading_file){
    $parser = new Spyc;
    $result  = $parser->load($reading_file);
		//$result = Spyc::YAMLLoad($reading_file);
		$this->recup = $result;
	}

	public function GetContentYml(){
		return $this->recup;
	}
}
