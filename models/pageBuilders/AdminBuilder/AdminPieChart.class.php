<?php
namespace PageBuilders; 

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