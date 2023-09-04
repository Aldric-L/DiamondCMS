<?php
namespace PageBuilders; 

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