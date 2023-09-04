<?php
namespace PageBuilders; 

interface renderUIComponent {
    public function render() : string;
}

class UIString implements renderUIComponent{
    protected string $val;

    public function __construct(string $val){
        $this->val = $val;
    }

    public function render() : string{
        return $this->val;
    }
}

class UINull implements renderUIComponent{
    public function render() : string{
        return "";
    }
}

class UIArray implements renderUIComponent{
    protected array $list;

    public function __construct(...$contentarray){
        $this->list = array();
        foreach ($contentarray as $content){
            if (is_array($content)){
                foreach ($content as $c) {
                    if (is_subclass_of($c, 'PageBuilders\renderUIComponent')){
                        $this->push($c);
                    }
                }
            }else if (is_subclass_of($content, 'PageBuilders\renderUIComponent')){
                $this->push($content);
            }else {
                throw new \Exception("Bad initialisation, UIArray sucks.");
            }
        }
        return $this;
    }

    public function push(renderUIComponent $content){
        array_push($this->list, $content);
        return $this;
    }

    public function to_array() : array{
        return $this->list;
    }

    public function render() : string {
        ob_start();
        foreach ($this->list as $c) {
            echo $c->render();
        }
        return ob_get_clean();
    }
}

class UIColumn implements renderUIComponent{
    protected array $list;
    protected string $col;

    public function __construct(string $col, $content){
        $this->list = array();
    
        if (\substr($col, 0, 4) !== "col-")
            $col = "col-" . $col;
        $this->col = $col;

        if (is_array($content)){
            foreach ($content as $c) {
                if (is_subclass_of($c, 'PageBuilders\renderUIComponent')){
                    $this->push($c);
                }  
                $this->push(new UIString("<br>"));
            }
        }else if ($content instanceof UIArray){
            foreach ($content->to_array() as $c) {
                if (is_subclass_of($c, 'PageBuilders\renderUIComponent')){
                    $this->push($c);
                }  
            }
        }else if (is_subclass_of($content, 'PageBuilders\renderUIComponent')){
            $this->push($content);
        }else {
            throw new \Exception("Bad initialisation, UIColumn sucks.");
        }
        return $this;
    }

    public function push(renderUIComponent $content){
        array_push($this->list, $content);
        return $this;
    }

    public function render() : string {
        ob_start();
        echo '<div class="' . $this->col .'">';
        foreach ($this->list as $c) {
            if (\is_subclass_of($c, "PageBuilders\AdminPanel"))
                $c->stop_col();
            echo $c->render();
        }
        echo '</div>';
        return ob_get_clean();
    }
}

class UIIframe implements renderUIComponent{
    protected string $source;
    protected string $div_class;

    public function __construct(string $source){
        $this->source = $source;
        $this->div_class = "";
        return $this;
    }

    public function setDiv(string $div_class="embed-responsive embed-responsive-16by9"){
        $this->div_class = $div_class;
        return $this;
    }

    public function render() : string {
        ob_start();
        echo '<div class="' . $this->div_class .'">';
        echo '<iframe  src="' . $this->source .'" frameborder="0"></iframe>';
        echo '</div>';
        return ob_get_clean();
    }
}

interface UIButton extends renderUIComponent {
    public function addAttr(string $name, string $val);
    public function addClass(string $class_name);
    public function addAttrS(array $attrs);

    public function customRender(string $html_type, string $base_class="") : string;
}
