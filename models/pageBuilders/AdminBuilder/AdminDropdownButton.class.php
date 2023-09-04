<?php
namespace PageBuilders; 

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
