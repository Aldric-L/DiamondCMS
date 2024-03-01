<?php
namespace PageBuilders; 

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