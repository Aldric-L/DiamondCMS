<?php
// Une class pour écrire dans un ini 
// Merci à Samuel ROZE (http://sroze.io/2008/05/18/ecrire-des-fichiers-ini-simplement-avec-php/) pour la réalisation de la class
// Modifications par Aldric L.
class ini
{
 
	var $ini;
	var $filename;
 
	function __construct($filename, $commentaire = false){
		$this->ini($filename, $commentaire = false);
	}
	private function ini ($filename, $commentaire = false) {
		$this->filename = $filename;
		$this->ini = (!$commentaire) ? ' ' : ';'.$commentaire;
	}
 
	public function ajouter_array ($array) {
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$this->sous_tableau($val, $key);
			}
			else if (is_string($key)) {
				$this->ajouter_valeur($key, $val);
			}
		}
	}
 
	private function sous_tableau ($tab, $groupe = false) {
		if ($groupe) {
			$this->ini .= "\n".'['.$groupe.']';
		}
		foreach ($tab as $key => $val) {
            if (is_array($val)){
                $this->sous_tableau($val);
            }
			if (!$this->ajouter_valeur($key, $val)) return false;
		}
		$this->ini .= "\n";
		return true;
	}
 
	private function ajouter_valeur ($key, $val) {
		if (is_array($val)) {
			throw new Exception("Impossible d'ajouter une valeur.", 610);
			return false;
		}
		else if (is_string($val) OR is_double($val) OR is_int($val)) {
			$this->ini .= "\n".$key.' = "'.$val.'"';
		}
		else if (is_bool($val) && $val) {
			$this->ini .= "\n".$key.' = "true"';
		}else if (is_bool($val) && !$val) {
			$this->ini .= "\n".$key.' = "false"';
		}else if (is_null($val)) {
			$this->ini .= "\n".$key.' = "null"';
		}
		else {
			throw new Exception("Le type de données n'est pas supporté.", 611);
			return false;
		}
		return true;
	}
 
	public function ecrire ($rewrite = false) {
		$c = true;
		if (file_exists($this->filename)) {
			if ($rewrite) {
				@unlink($filename);
			}
			else if (!$rewrite) {
				throw new Exception("Le fichier ini existe déjà.", 612);
				$c = false;
				return false;
			}
		}
		if ($c) {
			$fichier = fopen($this->filename, 'w');
			if (!$fichier) {
				throw new Exception("Impossible d'ouvrir le fichier.", 613);
				return false;
			}
			if (!fwrite($fichier, $this->ini)) {
				throw new Exception("Impossible d'écrire dans le fichier", 614);
				return false;
			}
			fclose($fichier);
		}
	}
}

/**
 * cleanIniTypes - Fonction pour arrêter de faire n'importe quoi avec le type des valeurs stockées dans la config
 * 
 * @author Aldric L.
 * @copyright 2022
 * @param $array|array : array à nettoyer de façon récursive
 * 
 */

function cleanIniTypes($array){
    foreach ($array as $key => $val){
        if ($val == "true"){
            $array[$key] = true;
        }else if ($val == "false"){
            $array[$key] = false;
        }else if ($val == "off"){
            $array[$key] = false;
        }else if ($val == "on"){
            $array[$key] = true;
        }else if ($val == "null"){
            $array[$key] = null;
        }else if ($val === ""){
            $array[$key] = null;
        }else if ($val === " "){
            $array[$key] = null;
        }else if (is_numeric($val)){
            $array[$key] = intval($val);
        }else if ($val === true){
            $array[$key] = true;
        }else if ($val === false){
            $array[$key] = false;
        }else if (is_array($val)){
			$array[$key] = cleanIniTypes($val);
        }
    }
    return $array;
}
