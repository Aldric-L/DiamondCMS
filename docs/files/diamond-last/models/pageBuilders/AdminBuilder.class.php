<?php
// Ce fichier n'est plus appelé et était uniquement un fichier de développement. Les class ont été séparées depuis.

namespace PageBuilders; 

interface renderUIComponent {
    public function render() : string;
}

class UIString implements renderUIComponent{
    protected string $val;

    public function __construct(string $val){
        $this->val = $val;
    }

    /*public function setContent(renderUIComponent $content){
        $this->content = $content;
    }

    public function getContent() : renderUIComponent{
        return $this->content;
    }*/

    public function render() : string{
        return $this->val;
    }
}

class UINull implements renderUIComponent{
    public function render() : string{
        return "";
    }
}

class UIArray implements renderUIComponent{
    protected array $list;

    public function __construct(...$contentarray){
        $this->list = array();
        foreach ($contentarray as $content){
            if (is_array($content)){
                foreach ($content as $c) {
                    if (is_subclass_of($c, 'PageBuilders\renderUIComponent')){
                        $this->push($c);
                    }
                }
            }else if (is_subclass_of($content, 'PageBuilders\renderUIComponent')){
                $this->push($content);
            }else {
                throw new \Exception("Bad initialisation, UIArray sucks.");
            }
        }
        return $this;
    }

    public function push(renderUIComponent $content){
        array_push($this->list, $content);
        return $this;
    }

    public function to_array() : array{
        return $this->list;
    }

    public function render() : string {
        ob_start();
        foreach ($this->list as $c) {
            echo $c->render();
        }
        return ob_get_clean();
    }
}

class UIColumn implements renderUIComponent{
    protected array $list;
    protected string $col;

    public function __construct(string $col, $content){
        $this->list = array();
    
        if (\substr($col, 0, 4) !== "col-")
            $col = "col-" . $col;
        $this->col = $col;

        if (is_array($content)){
            foreach ($content as $c) {
                if (is_subclass_of($c, 'PageBuilders\renderUIComponent')){
                    $this->push($c);
                }  
                $this->push(new UIString("<br>"));
            }
        }else if ($content instanceof UIArray){
            foreach ($content->to_array() as $c) {
                if (is_subclass_of($c, 'PageBuilders\renderUIComponent')){
                    $this->push($c);
                }  
            }
        }else if (is_subclass_of($content, 'PageBuilders\renderUIComponent')){
            $this->push($content);
        }else {
            throw new \Exception("Bad initialisation, UIColumn sucks.");
        }
        return $this;
    }

    public function push(renderUIComponent $content){
        array_push($this->list, $content);
        return $this;
    }

    public function render() : string {
        ob_start();
        echo '<div class="' . $this->col .'">';
        foreach ($this->list as $c) {
            if (\is_subclass_of($c, "PageBuilders\AdminPanel"))
                $c->stop_col();
            echo $c->render();
        }
        echo '</div>';
        return ob_get_clean();
    }
}

class UIIframe implements renderUIComponent{
    protected string $source;
    protected string $div_class;

    public function __construct(string $source){
        $this->source = $source;
        $this->div_class = "";
        return $this;
    }

    public function setDiv(string $div_class="embed-responsive embed-responsive-16by9"){
        $this->div_class = $div_class;
        return $this;
    }

    public function render() : string {
        ob_start();
        echo '<div class="' . $this->div_class .'">';
        echo '<iframe  src="' . $this->source .'" frameborder="0"></iframe>';
        echo '</div>';
        return ob_get_clean();
    }
}

class ThemeBuilder {
    protected string $theme_name;
    protected string $namespace;

    const CACHE_DYN = 1;
    const CACHE_SEMISTATIC = 5;
    const CACHE_STATIC = 1440;
    const CACHE_NAMES = array(self::CACHE_DYN => "CACHE_DYN", self::CACHE_SEMISTATIC => "CACHE_SEMISTATIC", self::CACHE_STATIC => "CACHE_STATIC");

    public function __construct(string $theme_name, string $namespace="PageBuilders") {
        $this->theme_name = ucfirst($theme_name);
        $this->namespace = $namespace;
    }

    public function __call($name, $args){
        if (class_exists($this->namespace . str_replace(" ", "", '\ ') . $this->theme_name .  $name)){
            $cn = $this->namespace . str_replace(" ", "", '\ ') . $this->theme_name . $name;
            return new $cn(...$args);
        }else if (class_exists($this->namespace . str_replace(" ", "", '\ ') .  $name)){
            $cn = $this->namespace . str_replace(" ", "", '\ ') . $name;
            return new $cn(...$args);
        }else if (class_exists($name)){
            $cn = $name;
            return new $cn(...$args);
        }
        throw new \DiamondException("Class name invalid. No class found for " . $this->namespace . str_replace(" ", "", '\ ') . $this->theme_name .  $name, "native$999");
    }

    public static function renderFromCacheIfPossible($tbname, $cache_type){
        $cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . self::CACHE_NAMES[$cache_type] . "/", $cache_type);
        if (($tb = $cacheinstance->read(mb_strtolower($tbname) . ".dcms")) != false){
            \ob_start();
            require_once(ROOT . "views/themes/default/include/header_admin.inc");
            echo $tb;
            require_once(ROOT . "views/themes/default/include/footer_admin.inc");
            return \ob_get_clean();
        }
        return false;
    }

    public static function startCache($tbname, $cache_type, $callBack, $shouldrenderasview=true) : void{
        $cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . self::CACHE_NAMES[$cache_type] . "/", $cache_type);
        if (($tb = $cacheinstance->read(mb_strtolower($tbname) . ".dcms")) != false){
            if (($serialcontroleur = $cacheinstance->read(mb_strtolower($tbname) . ".serialController.dcms")) != false){
                $GLOBALS['controleur_def']->unSerialize($serialcontroleur);
            }
            $GLOBALS['controleur_def']->loadAsView($tb, true);
        }else if (is_callable($callBack)){
            $GLOBALS['DIAMOND_CACHE_PROCESSING'] = true;
            \ob_start();
            $refFunction = new \ReflectionFunction($callBack);
            $args = array();
            foreach($refFunction->getParameters() as $p){
                if (isset($GLOBALS[$p->getName()]))
                    array_push($args, $GLOBALS[$p->getName()]);
                else
                    throw new \DiamondException("Argument name is not a global variable.", "native$999");
            }
            $callBack(...$args);
            $cacheinstance->write(mb_strtolower($tbname) . ".dcms", $buffer=\ob_get_clean());
            $cacheinstance->write(mb_strtolower($tbname) . ".serialController.dcms", $GLOBALS['controleur_def']->serialize());
            $GLOBALS['DIAMOND_CACHE_PROCESSING'] = false;
            if ($shouldrenderasview)
                $GLOBALS['controleur_def']->loadAsView($buffer, true);
        }
    }

    public static function clearCache($tbname, $cache_type){
        $cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . self::CACHE_NAMES[$cache_type] . "/", $cache_type);
        return $cacheinstance->clean(true);
    }

    public static function FA(string $fa_name) : string {
        return '<i class="fa '. $fa_name . ' fa-fw"></i>';
    }
}

abstract class AdminBuilder {

    protected string $page_name;
    protected string $desc_page;
    protected string $cache_name;
    protected bool $en_cache;

    protected string $buffer;
    protected array $renderElements;
    protected array $js_to_render;
    protected array $css_to_render;
    protected $cacheinstance;

    public function __construct(string $page_name, string $desc_page="", bool $en_cache=false, string $cache_name="", $cache_type=ThemeBuilder::CACHE_DYN){
        $this->page_name = $page_name;
        $this->desc_page = $desc_page;
        $this->cache_name = $cache_name;
        $this->en_cache = $en_cache;
        $this->buffer="";
        $this->js_to_render = array();
        $this->css_to_render = array();
        $this->renderElements = array();
        if ($en_cache)
            $this->cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . ThemeBuilder::CACHE_NAMES[$cache_type] . "/", $cache_type);
        else 
            $this->cacheinstance = null;
    }

    public abstract function addPanel(AdminPanel $content);
    public abstract function addColumn(UIColumn $content);
    public abstract function addAlert(string $col, AdminAlert $content);

    public function addJSToRender(string $JS){
        \array_push($this->js_to_render, $JS);
        return $this;
    }

    public function addCSSToRender(string $CSS){
        \array_push($this->css_to_render, $CSS);
        return $this;
    }

    public function addModal(AdminModal $modal){
        \array_push($this->renderElements, $modal);
        return $this;
    }

    public abstract function getThemeConfig();

    public abstract function render();
}

abstract class AdminPanel{
    protected string $name;
    protected string $icon;
    protected string $col;
    protected bool $should_col;
    protected renderUIComponent $content;
    
    protected string $buffer;

    public function __construct(string $name, string $icon, renderUIComponent $content, string $col){
        $this->name = $name;
        $this->icon = $icon;
        $this->content = $content;

        if (\substr($col, 0, 4) === "col-")
            $col = substr($col, 4);
        $this->col = $col;

        $this->buffer="";
        $this->should_col = true;
    }

    public function setContent(renderUIComponent $content){
        $this->content = $content;
    }

    public function getContent() : renderUIComponent{
        return $this->content;
    }

    public function stop_col(){
        $this->should_col = false;
    }

    public abstract function render();
}

abstract class AdminForm{
    protected string $buffer;
    protected string $id;
    protected array $inBuffer;
    protected array $buttonsBuffer;
    protected string $buttons_line;
    protected bool $needAll;

    protected bool $shall_rename_ids = true;

    protected array $availableIfs = array();
    protected array $fields = array();

    public function __construct(string $id, bool $needAll=false, bool $shall_rename_ids=true){
        $this->buffer="";
        $this->id = $id;
        $this->inBuffer = array();
        $this->buttons_line = "";
        $this->buttonsBuffer = array();
        $this->needAll = $needAll;
        $this->shall_rename_ids = $shall_rename_ids;
    }

    public function getId(){
        return $this->id;
    }

    public function getFields() : array {
        return $this->fields;
    }

    public abstract function addTextField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addHiddenField(string $name, string $val="", bool $needed=false);
    public abstract function addEmailField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addNumberField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addTextAreaField(string $name, string $title="", string $val="", int $rows=5, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addSelectField(string $name, string $title="", array $options, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addMpField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addCheckField(string $name, string $title, bool $checked=false, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addFileField(string $name, string $title, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addCustom(renderUIComponent $custom);

    public abstract function setFilter(string $field_name, array $replacement);

    public abstract function addAPIButton(AdminAPIButton $button);
    public abstract function setButtonsLine(string $attr);
    public abstract function addModalButton(AdminModalButton $button);

    public abstract function render();
}

class AvailableIf{
    const EQUAL = "==";
    const NOT_EQUAL = "!=";
    const NOT_NULL = "NOTNULL";
    const SUP = ">";
    const EQSUP = ">=";
    const INF = "<";
    const EQINF = "<=";
    //const CLICKED = "CLICK";

    const DISABLED = "disabled";
    const HIDE = "hide";

    const OR = "||";
    const AND = "&&";

    protected AdminForm $form;
    protected string $field_name;
    protected string $field_id;
    protected $action = self::DISABLED;

    protected array $conditions = array();

    protected string $target_name = "";
    protected string $target_type = "";

    public function __construct(AdminForm &$form, string $field_name, string $operator, $value=null){
        $consts = array(self::EQUAL, self::NOT_EQUAL, self::NOT_NULL, 
                        self::SUP, self::EQSUP, self::INF, 
                        self::EQINF/*, self::CLICKED*/);
        if (!in_array($operator, $consts))
            throw new \DiamondException("Illegal value for AvailableIf operator", "native$999");

        $this->form = $form;
        $this->field_name = $field_name;
        $fields = $this->form->getFields();
        $def_value = $this->field_name;
        foreach ($fields as $f){
            if (array_key_exists('name', $f) && $f['name'] == $this->field_name){
                if (array_key_exists('id', $f))
                    $def_value = $f['id'];
            }
        }
        $this->field_id = $def_value;
        array_push($this->conditions, array(
            "logical" => "FIRST",
            "operator" => $operator,
            "value" => $value
        ));
        return $this;
    }

    public function setTarget(string $target_name, string $target_type, $action=self::DISABLED){
        $this->target_name = $target_name;
        $this->target_type = $target_type;
        $consts = array(self::DISABLED, self::HIDE);
        if ($action != self::DISABLED && !in_array($action, $consts))
            throw new \DiamondException("Illegal value for AvailableIf action", "native$999");
        $this->action = $action;
        
        return $this;
    }

    public function getTarget() : array {
        $fields = $this->form->getFields();
        $def_value = $this->target_name;
        foreach ($fields as $f){
            if (array_key_exists('name', $f) && $f['name'] == $this->target_name){
                if (array_key_exists('id', $f))
                    $def_value = $f['id'];
            }
        }
        return array(
            "id" => $def_value,
            "name" => $this->target_name,
            "type" => $this->target_type,
            "action" => $this->action
        );
    }

    public function addCondition(string $logical_operator, string $operator, $value=null) {
        $logicals = array(self::OR, self::AND);
        if (!in_array($logical_operator, $logicals))
            throw new \DiamondException("Illegal value for AvailableIf action", "native$999");
        
        array_push($this->conditions, array(
            "logical" => $logical_operator,
            "operator" => $operator,
            "value" => $value
        ));
        return $this;
    }

    public function getAction() {
        return $this->action;
    }

    public function getCondFieldName() : string {
        return $this->field_name;
    }

    public function getCondFieldId() : string {
        return $this->field_id;
    }

    public function getVerbe() : string {
        return "change";
    }

    public function getCond() : string {
        $final_cond = "";
        foreach ($this->conditions as $cond){
            switch ($cond['operator']) {
                case self::NOT_NULL :
                    if ($cond['logical'] != "FIRST")
                        $final_cond .= $cond['logical'];
                    $final_cond .= ' value != null && typeof(value) != "undefined" ';
                    break;
                
                default:
                    if ($cond['logical'] != "FIRST")
                            $final_cond .= $cond['logical'];

                    if (is_bool($cond['value'])){
                        if ($cond['value'])
                            $final_cond .= " value " . $cond['operator'] . " true ";
                        else                
                            $final_cond .= " value " . $cond['operator'] . " false ";
                    }else {
                        if (is_string($cond['value']))
                            $final_cond .= " value " . $cond['operator'] . '"' . strval($cond['value']) . '" ';
                        else
                            $final_cond .= " value " . $cond['operator'] . strval($cond['value']) . " ";
                    }
                    break;
            }
        }
        return $final_cond;
    }

    public function eval() : bool{
        $fields = $this->form->getFields();
        $def_value = null;
        foreach ($fields as $f){
            if (array_key_exists('name', $f) && $f['name'] == $this->field_name){
                if (array_key_exists('value', $f))
                    $def_value = $f['value'];
            }
        }

        $cond_results = array();
        $raw_results = array();

        foreach ($this->conditions as $cond){
            switch ($cond['operator']) {
                case self::EQUAL:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value == $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
                
                case self::NOT_EQUAL:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value != $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::NOT_NULL:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value != null)
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::SUP:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value > $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::EQSUP:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value >= $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::INF:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value < $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::EQINF:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value <= $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
                
                default:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=true
                    ));
                    array_push($raw_results, $r);
                    break;
            }
        }
        if (sizeof($cond_results) < 1){
            return true;
        }else if (sizeof($cond_results) == 1) {
            return $cond_results[0]['result'];
        }else {
            if ($cond_results[1]['logical'] == self::AND){
                if (array_sum($raw_results) == count($raw_results))
                    return true;
                return false;
            }else {
                if (array_sum($raw_results) > 0)
                    return true;
                return false;
            }
        }
    }

}

abstract class AdminList{
    protected string $buffer;
    protected array $fieldBuffer;
    protected array $availableIfs = array();

    public function __construct(){
        $this->buffer="";
        $this->fieldBuffer=array();
    }

    // On ne peut pas typer $right et $modalCallBack car les objects comme valeur par défaut des paramètres n'apparaissent en PHP qu'en 8.1... 
    // back to middle age so !
    public abstract function addField(renderUIComponent $left, $right=null, $callBack=null, $class=null, string $id="", $availableif=null);

    public abstract function render();
}

abstract class AdminTable{
    protected string $class;
    protected array $head;
    protected array $lines;
    protected string $id;
    protected bool $responsive;

    public function __construct(string $class="table-striped", string $id="", array $head=array(), bool $responsive=true){
        $this->class = $class;
        $this->head = $head;
        $this->id = $id;
        $this->responsive = $responsive;
        $this->lines = array();
    }

    public function setHead(array $head){
        $this->head = $head;
        return $this;
    }

    public abstract function addLine(array $line);

    protected function checkIfLineValid($line){
        foreach ($line as $key => $l){
            if (is_string($line[$key]) || is_numeric($line[$key]))
                $line[$key] = new UIString($line[$key]);

            if (!($line[$key] instanceof renderUIComponent || \is_subclass_of($line[$key], "PageBuilders\renderUIComponent")))
                return false;
        }
        if (sizeof($this->head) !== sizeof($line))
            return false;

        foreach ($this->head as $h){
            if (!\array_key_exists($h, $line))
                return false;
        }

        return $line;
    }

    public abstract function render();
}
abstract class AdminModal{
    protected string $title;
    protected string $buffer;
    protected string $icon;
    protected string $size;
    protected string $id;
    protected array $buttonsBuffer;
    protected renderUIComponent $content;

    public function __construct(string $title, string $id, renderUIComponent $content, string $icon="", string $size=""){
        $this->title=$title;
        $this->content=$content;
        $this->icon=$icon;
        $this->size=$size;
        $this->id = $id;
        $this->buttonsBuffer = array();
    }

    public function getId(){
        return $this->id;
    }
    
    public function setContent(renderUIComponent $content){
        $this->content = $content;
    }

    public function getContent() : renderUIComponent{
        return $this->content;
    }

    public abstract function addAPIButton(renderUIComponent $button);
    public abstract function render();
}

interface UIButton extends renderUIComponent {
    public function addAttr(string $name, string $val);
    public function addClass(string $class_name);
    public function addAttrS(array $attrs);

    public function customRender(string $html_type, string $base_class="") : string;
}


abstract class AdminAPIButton {
    protected string $title;
    protected string $class;
    protected string $api_link;
    protected string $api_module;
    protected string $api_verbe;
    protected string $func;
    protected string $private_callback;
    protected string $should_reload;
    protected string $show_return;
    protected string $no_loading;
    protected string $private_id;
    protected bool $disabled;
    protected bool $allneeded;

    protected $to_send;
    protected renderUIComponent $buffer;

    public function __construct(string $title, string $class, string $api_link, string $api_module, string $api_verbe, string $func, $to_send, string $private_callback="", $should_reload="true", $show_return="false", $no_loading="false", bool $disabled=false, string $private_id="", bool $needAll=false){
        $this->title = $title;
        $this->class = $class;
        $this->api_link = $api_link;
        $this->api_module = $api_module;
        $this->api_verbe = $api_verbe;
        $this->func = $func;
        $this->to_send = $to_send;
        $this->private_callback = $private_callback;

        if (!is_string($should_reload) && is_bool($should_reload)){
            if ($should_reload)
                $should_reload = "true";
            else
                $should_reload = "false";
        }
        $this->should_reload = $should_reload;

        if (!is_string($show_return) && is_bool($show_return)){
            if ($show_return)
                $show_return = "true";
            else
                $show_return = "false";
        }
        $this->show_return = $show_return;
        
        if (!is_string($no_loading) && is_bool($no_loading)){
            if ($no_loading)
                $no_loading = "true";
            else
                $no_loading = "false";
        }
        $this->no_loading = $no_loading;
        $this->disabled = $disabled;
        $this->private_id = $private_id;
        $this->allneeded = $needAll;
        $this->buffer = new UINull();
    }

    public function getTosend(){
        return $this->to_send;
    }

    public function setAllneeded(bool $needAll=true){
        $this->allneeded = $needAll;
    }

    public function addAttr(string $name, string $val){
        if (!($this->buffer instanceof UIButton))
            throw new \DiamondException("AdminAPIButton not initialized (addAttr unavailable).", "native$999");
        $this->buffer->addAttr($name, $val);
        return $this;
    }

    public function addClass(string $class_name){
        if (!($this->buffer instanceof UIButton))
            throw new \DiamondException("AdminAPIButton not initialized (addClass unavailable).", "native$999");
        $this->buffer->addClass($class_name);
        return $this;
    }

    public function addAttrS(array $attrs){
        if (!($this->buffer instanceof UIButton))
            throw new \DiamondException("AdminAPIButton not initialized (addAttrS unavailable).", "native$999");
        $this->buffer->addAttrS($attrs);
        return $this;
    }

    public abstract function render();
    public abstract function customRender(string $html_type, string $base_class="") : string;

}

abstract class AdminModalButton {
    protected string $title;
    protected string $class;
    protected AdminModal $modal;
    protected bool $disabled;
    protected string $id;

    protected renderUIComponent $buffer;

    public function __construct(string $title, string $class, AdminModal $modal, bool $disabled=false, string $id=""){
        $this->title = $title;
        $this->class = $class;
        $this->modal = $modal;
        $this->disabled = $disabled;
        $this->id = $id;
        $this->buffer = new UINull();
        
        return $this;
    }

    public function addAttr(string $name, string $val){
        if (!($this->buffer instanceof UIButton))
            throw new \DiamondException("AdminModalButton not initialized (addAttr unavailable).", "native$999");
        $this->buffer->addAttr($name, $val);
        return $this;
    }

    public function addClass(string $class_name){
        if (!($this->buffer instanceof UIButton))
            throw new \DiamondException("AdminModalButton not initialized (addClass unavailable).", "native$999");
        $this->buffer->addClass($class_name);
        return $this;
    }

    public function addAttrS(array $attrs){
        if (!($this->buffer instanceof UIButton))
            throw new \DiamondException("AdminModalButton not initialized (addAttrS unavailable).", "native$999");
        $this->buffer->addAttrS($attrs);
        return $this;
    }

    public abstract function render();
    public abstract function customRender(string $html_type, string $base_class="") : string;
}

abstract class AdminButton {
    protected $title;
    protected $class;
    protected $attr;

    public function __construct(string $title, string $class, array $attr=array()){
        $this->title = $title;
        $this->class = $class;
        $this->attr = $attr;
        return $this;
    }

    public function addAttr(string $name, string $val){
        if ($name == "class")
            throw new \Exception("Class should not be setted by addAttr but by constructor");
            
        $this->attr[$name] = $val;
        return $this;
    }

    public function addClass(string $class_name){
        $this->class .= " " . $class_name;
        return $this;
    }

    public function addAttrS(array $attrs){
        foreach ($attrs as $key => $a){
            $this->addAttr($key,$a);
        }
        return $this;
    }

    public abstract function render();
    public abstract function customRender(string $html_type, string $base_class="") : string;
}

abstract class AdminDropdownButton {
    protected AdminButton $main_button;
    protected array $sub_buttons = array();

    public function __construct(UIButton $main_button){
        $this->main_button = $main_button;
    }

    public function addAttr(string $name, string $val){
        if (!($this->main_button instanceof UIButton))
            throw new \DiamondException("AdminDropdownButton not initialized (addAttr unavailable).", "native$999");
        $this->main_button->addAttr($name, $val);
        return $this;
    }

    public function addClass(string $class_name){
        if (!($this->main_button instanceof UIButton))
            throw new \DiamondException("AdminDropdownButton not initialized (addClass unavailable).", "native$999");
        $this->main_button->addClass($class_name);
        return $this;
    }

    public function addAttrS(array $attrs){
        if (!($this->main_button instanceof UIButton))
            throw new \DiamondException("AdminDropdownButton not initialized (addAttrS unavailable).", "native$999");
        $this->main_button->addAttrS($attrs);
        return $this;
    }

    public function addSubButton(UIButton $sub_button){
        array_push($this->sub_buttons, $sub_button);
        return $this;
    }

    public abstract function addDivider();

    public abstract function render();
    public abstract function customRender(string $html_type, string $base_class="") : string;
}

abstract class AdminCard {
    protected string $title;
    protected string $subtitle;
    protected string $class;
    protected string $icon;

    // Class = color, ex: (text-)custom
    public function __construct(string $class, string $title, string $subtitle="", string $icon=""){
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->class = $class;
        $this->icon = $icon;
    }

    public abstract function render();

}

abstract class AdminAreaChart {
    protected array $data;
    protected array $axis;
    protected AdminBuilder $builder;
    protected string $id;

    protected string $js_buffer;


    // data = array ("labels" => ...., "data" => ...)
    public function __construct(AdminBuilder &$builder, array $data, array $axis, string $id, bool $legend=false){
        $this->builder = $builder;
        $this->data = $data;
        $this->axis = $axis;
        $this->js_buffer = "";
        $this->id = $id;
    }

    public abstract function render();
}

abstract class AdminPieChart {
    protected array $data;
    protected AdminBuilder $builder;
    protected string $id;

    protected string $js_buffer;


    // data = array ("labels" => ...., "data" => ...)
    public function __construct(AdminBuilder &$builder, array $data, string $id, bool $legend=false){
        $this->builder = $builder;
        $this->data = $data;
        $this->js_buffer = "";
        $this->id = $id;
    }

    public abstract function render();
}

abstract class AdminAlert {
    protected string $type;
    protected string $title;
    protected string $content;
    protected bool $dismissible;

    public function __construct(string $type, string $title, string $content="", bool $dismissible=true){
        $this->type = $type;
        $this->title = $title;
        $this->content = $content;
        $this->dismissible = $dismissible;
    }

    public abstract function render();
}