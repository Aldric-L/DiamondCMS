<?php
namespace PageBuilders; 

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
