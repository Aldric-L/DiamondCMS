<?php
namespace PageBuilders; 

abstract class AdminAreaChart {
    protected array $data;
    protected array $axis;
    protected AdminBuilder $builder;
    protected string $id;

    protected string $js_buffer;


    // data = array ("labels" => ...., "data" => ...)
    public function __construct(AdminBuilder &$builder, array $data, array $axis, string $id, bool $legend=false){
        $this->builder = $builder;
        $this->data = $data;
        $this->axis = $axis;
        $this->js_buffer = "";
        $this->id = $id;
    }

    public abstract function render();
}