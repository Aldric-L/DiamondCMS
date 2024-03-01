<?php
namespace PageBuilders; 

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