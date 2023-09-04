<?php 

namespace ModulesManager;

class DefaultModulesManager extends ModulesManager {
    public function renderModules(\Controleur &$controleur_def, bool $editing_mode=false) : string{
        if (!$this->is_initialised)
            throw new \DiamondException("ModulesManager is not initialized", "native$803");
        $loadedModulesNames = array();
        $render = "";
        \ob_start(); ?>
        <div id="loaded_modules_<?php echo $this->page_name; ?>" class="loaded_modules_container">
        <?php foreach ($this->loadedModules as $key => $m) { 
            if ($m instanceof Module || ($editing_mode && is_array($m))){
                // Il ne faut jamais que un module soit chargé du cache lorsqu'on est en editing mode, dans ce cas, on recrée la classe pour ne pas charger le cache
                if (is_array($m) && $editing_mode)
                    $this->loadedModules[$key] = $m = new $m['class_name'](...$m['parameters']);
                array_push($loadedModulesNames, $m::$name) ?>
                <div class="m_<?php echo $this->page_name; ?>_<?php echo $m::$name; ?>" id="m_<?php echo $key; ?>">
                    <?php if ($editing_mode){ ?>
                        <h4 class="text-center" style="margin-top: 2em;"><span class="editmodule">
                        Module : "<?php echo $m::$name; ?>" - Actions : 
                        
                        <?php if ($key != 0){ ?>
                        <i class="fa fa-chevron-up module_up ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="changeModulePos" data-tosend="mod_name=<?php echo $m::$name; ?>&mm=<?php echo $this->page_name; ?>&cur_pos=<?php echo $key; ?>&new_pos=<?php echo $key-1; ?>" data-reload="true" data-section_name="<?php echo "m_" . $this->page_name . "_" . $m::$name; ?>" data-name="<?php echo $m::$name; ?>" aria-hidden="true"></i> 
                        <?php } ?>
                        <?php if ($key != sizeof($this->loadedModules)-1){ ?>
                        <i class="fa fa-chevron-down module_down ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="changeModulePos" data-tosend="mod_name=<?php echo $m::$name; ?>&mm=<?php echo $this->page_name; ?>&cur_pos=<?php echo $key; ?>&new_pos=<?php echo $key+1; ?>" data-reload="true" data-section_name="<?php echo "m_" . $this->page_name . "_" . $m::$name; ?>" data-name="<?php echo $m::$name; ?>" aria-hidden="true"></i> 
                        <?php } ?>
                        <i class="fa fa-trash-o module_delete ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="deleteModule" data-tosend="mod_name=<?php echo $m::$name; ?>&mm=<?php echo $this->page_name; ?>&mod_key=<?php echo $key; ?>" data-reload="true" data-section_name="<?php echo "m_" . $this->page_name . "_" . $m::$name; ?>" data-name="<?php echo $m::$name; ?>" aria-hidden="true"></i>
                        </span>
                        
                        </h4>
                        
                    <?php } ?> 
                    <?php 
                    echo $buffer = $m->render($editing_mode);
                    if ($m::$allowCache != 0 && $editing_mode != true){
                        $cacheinstance = new \DiamondCache(ROOT . "tmp/ModulesManager/" . self::CACHE_NAMES[$m::$allowCache] . "/" . $this->page_name . "/", $m::$allowCache);
                        $cacheinstance->write(mb_strtolower($m::$name) . ".dcms", $buffer);
                    } 
            }else if (is_array($m) && !$editing_mode){
                array_push($loadedModulesNames, $m['name']); ?>
                <div class="m_<?php echo $this->page_name; ?>_<?php echo $m['name']; ?>" id="m_<?php echo $key; ?>">
                    <?php if ($editing_mode){ ?>
                        <h4 class="text-center" style="margin-top: 2em;"><span class="editmodule">
                        Module : "<?php echo $m['name']; ?>" - Actions : 
                        <?php if ($key != 0){ ?>
                        <i class="fa fa-chevron-up module_up ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="changeModulePos" data-tosend="mod_name=<?php echo $m['name']; ?>&mm=<?php echo $this->page_name; ?>&cur_pos=<?php echo $key; ?>&new_pos=<?php echo $key-1; ?>" data-reload="true" data-section_name="<?php echo "m_" . $this->page_name . "_" . $m['name']; ?>" data-name="<?php echo $m['name']; ?>" aria-hidden="true"></i> 
                        <?php } ?>
                        <?php if ($key != sizeof($this->loadedModules)-1){ ?>
                        <i class="fa fa-chevron-down module_down ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="changeModulePos" data-tosend="mod_name=<?php echo $m['name']; ?>&mm=<?php echo $this->page_name; ?>&cur_pos=<?php echo $key; ?>&new_pos=<?php echo $key+1; ?>" data-reload="true" data-section_name="<?php echo "m_" . $this->page_name . "_" . $m['name']; ?>" data-name="<?php echo $m['name']; ?>" aria-hidden="true"></i> 
                        <?php } ?>
                        <i class="fa fa-trash-o module_delete ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="deleteModule" data-tosend="mod_name=<?php echo $m['name']; ?>&mm=<?php echo $this->page_name; ?>&mod_key=<?php echo $key; ?>" data-reload="true" data-section_name="<?php echo "m_" . $this->page_name . "_" . $m['name']; ?>" data-name="<?php echo $m['name']; ?>" aria-hidden="true"></i>
                        </span>
                        
                            </h4>
                        
                    <?php } ?> 
                    <?php echo $m['cache']; 
            }else { ?>
                <p class="text-center"><em>Un problème pour générer le module est survenu.</em></p>
            <?php } ?>
            <?php if ($key !== sizeof($this->loadedModules)-1){ ?>
                <hr>
            <?php } ?>
            
            </div>
        <?php } ?>
        </div>
        <br><br>
        <?php if ($editing_mode) { ?>
        <div id="addModule" style="padding-top: 0.25em;">
        <h4 class="text-center" data-toggle="modal" data-target="#addModuleModal"><span class="editmodule"><span style="color: darkred;">Edition de la page : </span><i class="fa fa-plus-circle" aria-hidden="true"></i> Ajouter des Modules</span></h4>
        </div>
        <style>
        .editmodule {
            padding: 0.75em;
            background-color: lightgrey;
        }
        </style>


        <div id="addModuleModal" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Ajouter des modules sur la page <?php echo \ucfirst($this->page_name); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($mnc = $this->getAvailableModulesNamesForThisMM() as $key => $m){
                            $m = (str_replace(" ", "", "ModulesManager\ ") . $m); ?>
                            <h4><?php echo $m::$name; ?> <small>(par <?php echo $m::$owner; ?>)</small></h4>
                            <p style="margin-left: 2%;margin-right: 2%;"><?php echo $m::$description; ?><br>
                            <em>Page(s) compatible(s) : <?php if (empty($m::$compatiblePages)) { echo "Toutes."; }else { foreach ($m::$compatiblePages as $p){ echo $p . " "; }} ?></em></p>
                            <?php if (\in_array($m::$name, $loadedModulesNames) && !$m::$canBeLoadedTwice){ ?>
                                <p class="text-right"><em>Ce module est déjà chargé sur cette page et ne peut l'être deux fois.</em></p>
                            <?php }else { ?>
                                <p class="text-right"><button class="btn btn-sm btn-custom ajax-simpleSend" data-api="<?= LINK; ?>api/" data-module="editing/" data-verbe="set" data-func="addModule" data-tosend="mod_name=<?php echo $m::$name; ?>&mm=<?php echo $this->page_name; ?>" data-reload="true">Ajouter à la page</button></p>
                            <?php } ?> 
                            <?php if ($key != sizeof($mnc)-1){ ?>
                                <hr>
                            <?php } ?>
                        <?php } ?>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button> 
                        
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        
        <?php 
        $render .= \ob_get_flush();

        foreach ($this->loadedModules as $key => $m) {
            if ($m instanceof Module){
                $initpath = $m->getInitPath($m::$name);
                foreach($m::$JS as $js){
                    $controleur_def->loadJSAddon(LINK . \str_replace(ROOT, "", $initpath) . $js);
                }
            }
        }

        return $render;
    }
}