<?php
namespace PageBuilders; 

abstract class AdminBuilder {

    protected string $page_name;
    protected string $desc_page;
    protected string $cache_name;
    protected bool $en_cache;

    protected string $buffer;
    protected array $renderElements;
    protected array $js_to_render;
    protected array $css_to_render;
    protected $cacheinstance;

    public function __construct(string $page_name, string $desc_page="", bool $en_cache=false, string $cache_name="", $cache_type=ThemeBuilder::CACHE_DYN){
        $this->page_name = $page_name;
        $this->desc_page = $desc_page;
        $this->cache_name = $cache_name;
        $this->en_cache = $en_cache;
        $this->buffer="";
        $this->js_to_render = array();
        $this->css_to_render = array();
        $this->renderElements = array();
        if ($en_cache)
            $this->cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . ThemeBuilder::CACHE_NAMES[$cache_type] . "/", $cache_type);
        else 
            $this->cacheinstance = null;
    }

    public abstract function addPanel(AdminPanel $content);
    public abstract function addColumn(UIColumn $content);
    public abstract function addAlert(string $col, AdminAlert $content);

    public function addJSToRender(string $JS){
        \array_push($this->js_to_render, $JS);
        return $this;
    }

    public function addCSSToRender(string $CSS){
        \array_push($this->css_to_render, $CSS);
        return $this;
    }

    public function addModal(AdminModal $modal){
        \array_push($this->renderElements, $modal);
        return $this;
    }

    public abstract function getThemeConfig();

    public abstract function render();
}