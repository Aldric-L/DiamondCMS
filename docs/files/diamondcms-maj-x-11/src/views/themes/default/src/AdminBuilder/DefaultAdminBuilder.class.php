<?php 
namespace PageBuilders; 

class DefaultAdminBuilder extends AdminBuilder {

    protected string $page_name;
    protected string $desc_page;

    protected string $buffer;

    public function addPanel(AdminPanel $content){
        \array_push($this->renderElements, $content);
        return $this;
    }

    public function addColumn(UIColumn $content){
        \array_push($this->renderElements, $content);
        return $this;
    }

    public function addAlert(string $col, AdminAlert $content){
        $final_content = new UIArray(
            new UIString('<div class="' . ((substr($col, 0, 4) == "col-") ? $col : ("col-" . $col)) .'">'),
            $content,
            new UIString('</div>'),
        );
        \array_push($this->renderElements, $final_content);
        return $this;
    }

    public function getThemeConfig(){
        $theme_conf = cleanIniTypes(parse_ini_file(ROOT . 'views/themes/default/theme.ini', true));
        return $theme_conf;
    }

    public function render() : string{
        \ob_start(); 
        require_once(ROOT . "views/themes/default/include/header_admin.inc");
        \ob_start(); ?> 
        <div class="container-fluid">
            <h1 class="h3 text-gray-800"><?php echo $this->page_name; ?></h1>
            <p class=""><?php echo $this->desc_page; ?></p>
            <div class="row">
                <?php foreach ($this->renderElements as $re) {
                    echo $re->render();
                } ?>
            </div>
            <!-- /.row -->
        </div>
        <?php if (!empty($this->js_to_render)){ ?>
        <script>
        <?php foreach ($this->js_to_render as $js){
            echo $js . "\n";
        } ?>
        </script>
        <?php } ?>
        <?php if (!empty($this->css_to_render)){ ?>
        <style>
        <?php foreach ($this->css_to_render as $css){
            echo $css . "\n";
        } ?>
        </style>
        <?php } ?>
        <?php 
        $buff = \ob_get_clean();
        if ($this->cacheinstance !== null){
            $this->cacheinstance->write(mb_strtolower($this->cache_name) . ".dcms", $buff);
        }
        echo $buff;
        require_once(ROOT . "views/themes/default/include/footer_admin.inc");
        $this->buffer = \ob_get_clean();
        //var_dump(\ob_get_status());
        return $this->buffer;
    }
}