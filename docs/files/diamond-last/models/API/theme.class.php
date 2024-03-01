<?php 

/**
 * theme - API Admin permettant de modifier le thème
 *  
 * @author Aldric L.
 * @copyright 2022
 */
class theme extends DiamondAPI {
    private $cur_theme_conf;

    public function __construct($paths, $pdo, $controleur, $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_mode" => array("mode"),
            "set_customColors" => array(),
            "set_fonts" => array(),
            "get_themeConf" => array(),
            "get_checkReload" => array(),
            "get_reloadConf" => array("theme")
        );

        $this->loadthemeconf();
        $this->get_checkReload();
    }

    /**
     * set_mode - Fonction permettant de définir le mode du thème utilisé
     * Attention, ne confient pas pour le mode Custom
     * 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_mode(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);

        if ($this->args['mode'] === "Custom")
            throw new Exception("Don't use this function to set custom mode. Please use set_customColors.", 710);

        if ((!is_array($this->cur_theme_conf['modes']) && !in_array($this->args['mode'], explode(", ", $this->cur_theme_conf['modes']))) || (is_array($this->cur_theme_conf['modes']) && !in_array($this->args['mode'], $this->cur_theme_conf['modes'])))
            throw new Exception("Unable to find theme's mode.", 710);

        //On réécrit le fichier colors.css dans le thème
        $this->changeColors($this->cur_theme_conf['Colors_' . $this->args['mode']]);

        //On édite la config ini du thème : 
        $this->setConfig(ROOT . "views/themes/" . $this->cur_theme_conf['name'] . "/theme.ini", array("mode" => $this->args['mode']));  
        //On sauvegarde le fichier dans la config du CMS en cas de mise à jour
        $this->backup_conf($this->cur_theme_conf);

        return $this->formatedReturn(1);
    }

    /**
     * set_customColors - Fonction permettant de définir le mode custom et d'en changer les couleurs
     * Il faut lui passer en POST un array avec le nom de la variable CSS et sa valeur associée
     * 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_customColors(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);

        if ($this->args == null || empty($this->args))
            throw new Exception("Missing arguments", 701);

        $this->changeColors($this->args);
        $this->cur_theme_conf['mode'] = "Custom";

        foreach ($this->args as $key => $color) {
            if (array_key_exists($key, $this->cur_theme_conf['Colors_Custom'])){
                $this->cur_theme_conf['Colors_Custom'][$key] = $color;
            }
        }
        $modes_tostring = "";
        foreach ($this->cur_theme_conf['modes'] as $key => $mode) {
            $modes_tostring .= $mode;
            if ($key != sizeof($this->cur_theme_conf['modes'])-1)
                $modes_tostring .= ", ";
        }
        $this->cur_theme_conf['modes'] = $modes_tostring;

        $this->setConfig(ROOT . "views/themes/" . $this->cur_theme_conf['name'] . "/theme.ini", $this->cur_theme_conf);  

        //On sauvegarde le fichier dans la config du CMS en cas de mise à jour
        $this->backup_conf($this->cur_theme_conf);
        return $this->formatedReturn(1);
    }

    /**
     * get_checkReload - Fonction permettant de vérifier que le thème n'a pas besoin d'être reload à cause d'une MAJ qui aurait fait perdre la personalisation
     * En cas de reload, il appelle get_reloadConf en lui précisant en args le bon thème à reload
     * 
     * @author Aldric L.
     * @copyright 2022
     */
    public function get_checkReload(){
        if ($this->cur_theme_conf['reload_changes'] == true){
            if (file_exists(ROOT . 'config/save_' . $this->cur_theme_conf['name'] . '.ini')){
                $this->args['theme'] = $this->cur_theme_conf['name'];
                return $this->get_reloadConf();
            }
        }
        return $this->formatedReturn("No reload needed");
    }

    /**
     * get_reloadConf - Fonction permettant de forcer le reload de la config d'un thème
     * IL faut lui préciser en args le bon thème à reload
     * 
     * @author Aldric L.
     * @copyright 2022
     */
    public function get_reloadConf(){
        if (!file_exists(ROOT . 'config/save_' . $this->args['theme'] . '.ini'))
            throw new Exception("Unable to open file.", 613);

        //On récupère la sauvegarde
        $save = cleanIniTypes(parse_ini_file(ROOT . 'config/save_' . $this->args['theme'] . '.ini', true));
        // la configuration actuelle du thème
        $actual_config = cleanIniTypes(parse_ini_file(ROOT . 'views/themes/' . $this->args['theme'] . '/theme.ini', true));
        //On récupère l'ancien mode (Normal, darkmode ?)
        $actual_config['mode'] = $save['mode'];
        //On indique bien que la config a bien été reloaded
        $actual_config['reload_changes'] = false;
        //On récupère les anciennes couleurs et polices par cette boucle qui fait que si des nouveaux champs ont été ajoutés, ils ne sont pas impactés
        foreach ($actual_config['Colors_Custom'] as $key => $value) {
            if (array_key_exists($key, $save['Colors_Custom'])){
                $actual_config['Colors_Custom'][$key] = $save['Colors_Custom'][$key];
            }
        }
        foreach ($actual_config['Fonts_Custom'] as $key => $value) {
            if (array_key_exists($key, $save['Fonts_Custom'])){
                $actual_config['Fonts_Custom'][$key] = $save['Fonts_Custom'][$key];
            }
        }
        $ini = new ini (ROOT . "views/themes/" . $this->args['theme'] . "/theme.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($actual_config);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        return $this->formatedReturn(1);
    }

    /**
     * set_fonts - Fonction permettant de définir les polices avec leur lien d'import
     * 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_fonts(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);

        if ($this->args == null || empty($this->args))
            throw new Exception("Missing arguments", 701);

        $this->changeFonts($this->args);

        foreach ($this->args as $key => $font) {
            if (array_key_exists($key, $this->cur_theme_conf['Fonts_Custom'])){
                $this->cur_theme_conf['Fonts_Custom'][$key] = $font;
            }
        }
        $modes_tostring = "";
        foreach ($this->cur_theme_conf['modes'] as $key => $mode) {
            $modes_tostring .= $mode;
            if ($key != sizeof($this->cur_theme_conf['modes'])-1)
                $modes_tostring .= ", ";
        }
        $this->cur_theme_conf['modes'] = $modes_tostring;

        $this->setConfig(ROOT . "views/themes/" . $this->cur_theme_conf['name'] . "/theme.ini", $this->cur_theme_conf);  

        //On sauvegarde le fichier dans la config du CMS en cas de mise à jour
        $this->backup_conf($this->cur_theme_conf);
        return $this->formatedReturn(1);
    }

    /**
     * get_themeConf - Fonction permettant de récupérer la configuration du thème en cours 
     * 
     * @author Aldric L.
     * @copyright 2022
     */
    public function get_themeConf(){
        $conf = $this->cur_theme_conf;
        if (!is_array($this->cur_theme_conf['modes']))
            $conf['modes'] = explode(", ", $this->cur_theme_conf['modes']);
        return $this->formatedReturn($conf);
    }

    /**
     * internalget_themeConf - Fonction permettant de récupérer la configuration du thème en cours pour l'utiliser en PHP
     * 
     * @author Aldric L.
     * @copyright 2023
     */
    public function internalget_themeConf(){
        $conf = $this->cur_theme_conf;
        if (!is_array($this->cur_theme_conf['modes']))
            $conf['modes'] = explode(", ", $this->cur_theme_conf['modes']);
        return $conf;
    }

    /**
     * loadthemeconf - Fonction permettant de charger la configuration du thème en cours proprement
     * Pour un accès publique, utiliser getThemeConf, que renvoit modes sous forme d'un array
     * 
     * @author Aldric L.
     * @copyright 2022
     * @access private
     */
    private function loadthemeconf(){
        $theme_name = $this->getIniConfig(ROOT . "config/config.ini");
        if (empty($theme_name) OR !isset($theme_name['theme']))
            throw new Exception("Unable to find current theme", 709);
        $theme_name = $theme_name['theme'];
        if ($dir = opendir(ROOT . 'views/themes/')) {
            while($file = readdir($dir)) {
              //On ouvre les sous-dossiers
              if(is_dir(ROOT . 'views/themes/' . $file) && !in_array($file, array(".",".."))) {
                if ($d = opendir(ROOT . 'views/themes/' . $file)) {
                  while($f = readdir($d)) {
                    //Dans ces sous-dossiers, on charge les fichiers nommés theme.ini qui s'occupent eux-même de charger les addons auquels ils appartiennent
                    if ($f == "theme.ini"){
                        $t = cleanIniTypes(parse_ini_file(ROOT . 'views/themes/' . $file . '/'. $f, true));
                        if ($t['version_cms'] == DCMS_VERSION && $t['name'] == $theme_name){
                            $this->cur_theme_conf = $t; 
                            //$this->cur_theme_conf['modes'] = explode(", ", $this->cur_theme_conf['modes']);
                        }
                    }
                  }
                  closedir($d);
                }
              }
            }
            closedir($dir);
        }else {
            throw new Exception("Unable to open themes' folder", 708);
        }
        if (empty($this->cur_theme_conf))
            throw new Exception("Unable to find current theme", 709);

        $this->cur_theme_conf['modes'] = explode(", ", $this->cur_theme_conf['modes']);
        if (!file_exists(ROOT . 'config/save_' . $theme_name . '.ini'))
            $this->setConfig(ROOT . 'config/save_' . $theme_name . '.ini', $this->cur_theme_conf);
    }

    /**
     * changeColors - Fonction permettant d'appliquer les changements de couleur de thème dans colors.css
     * 
     * @author Aldric L.
     * @copyright 2022
     * @param array $colors
     */
    private function changeColors($colors){
        $new_file = ":root {". "\n";
        foreach ($colors as $key => $color) {
            $new_file .= "   --" . $key . ": " . $color . ";" . "\n";
        }
        $new_file .= "}";
        file_put_contents(ROOT. 'views/themes/' . $this->cur_theme_conf['name'] . '/CSS/colors.css', $new_file);
    }

    /**
     * changeFonts - Fonction permettant d'appliquer les changements de fonts de thème dans fonts.css
     * 
     * @author Aldric L.
     * @copyright 2022
     * @param array $fonts
     */
    private function changeFonts($fonts){
        $new_file = "";
        foreach ($fonts as $key => $font) {
            if (substr($key,-4, 4) != "link"){
                /*$new_file .= "
                @font-face {
                    font-family:'" . $font . "';
                    src: url('". $fonts[$key . "-link"] . "') format('truetype');
                    font-weight:normal;
                    font-style:normal
                }";*/

                $new_file .= "@import url('". $fonts[$key . "-link"] . "');" . "\n";
            }
        }
        $new_file .= "\n" . ":root {". "\n";
            
        foreach ($fonts as $key => $font) {
            $new_file .= "   --" . $key . ' : "'  . $font . '";' . "\n";
        }
        $new_file .= "}";
        file_put_contents(ROOT. 'views/themes/' . $this->cur_theme_conf['name'] . '/CSS/fonts.css', $new_file);
    }

    /**
     * backup_conf - Fonction permettant de sauvegarder la configuration du thème
     * 
     * @author Aldric L.
     * @copyright 2022
     * @param array $conf
     */
    private function backup_conf($conf){
        if (!file_exists(ROOT . "config/save_" . $conf['name'] . ".ini"))
            throw new Exception("Unable to open file.", 613);

        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/save_" . $conf['name'] . ".ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array(cleanIniTypes($conf));
        //On écrit en lui demmandant de conserver les groupes
        return $ini->ecrire(true);
    }
}
