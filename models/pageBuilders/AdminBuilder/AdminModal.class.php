<?php
namespace PageBuilders; 

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