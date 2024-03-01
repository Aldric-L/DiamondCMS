<?php
namespace PageBuilders; 

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