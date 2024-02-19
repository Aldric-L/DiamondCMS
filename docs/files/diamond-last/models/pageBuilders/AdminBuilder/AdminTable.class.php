<?php
namespace PageBuilders; 

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