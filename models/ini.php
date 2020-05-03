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
			echo '<strong>Erreur :</strong> Impossible d\'ajouter une valeur';
			return false;
		}
		else if (is_string($val) OR is_double($val) OR is_int($val)) {
			$this->ini .= "\n".$key.' = "'.$val.'"';
		}
		else if (is_bool($val)) {
			$this->ini .= "\n".$key.' = '.$val.'';
		}
		else {
			echo '<strong>Erreur :</strong> Le type de donnée n\'est pas supporté';
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
				echo '<strong>Erreur fatale :</strong> Le fichier ini existe déjà';
				$c = false;
				return false;
			}
		}
		if ($c) {
			$fichier = fopen($this->filename, 'w');
			if (!$fichier) {
				echo '<strong>Erreur fatale :</strong> Impossible d\'ouvrir le fichier';
				return false;
			}
			if (!fwrite($fichier, $this->ini)) {
				echo '<strong>Erreur fatale :</strong> Impossible d\'écrire dans le fichier';
				return false;
			}
			fclose($fichier);
		}
	}
}