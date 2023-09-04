<?php
namespace PageBuilders; 

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