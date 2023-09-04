<?php 
namespace PageBuilders; 

class DefaultAdminDropdownButton extends AdminDropdownButton implements UIButton {
    protected AdminButton $main_button;
    protected array $sub_buttons = array();

    public function __construct(AdminButton $main_button){
        parent::__construct($main_button);
        $this->main_button->addClass("dropdown-toggle");
        $this->main_button->addAttrS(array(
            "data-toggle" => "dropdown", 
            "aria-haspopup" => "true", 
            "aria-expanded" => "false"
        ));
    }

    public function addDivider() {
        array_push($this->sub_buttons, new UIString('<div class="dropdown-divider"></div>'));
        return $this;
    }

    public function render() : string {
        \ob_start();  ?>
        <div class="btn-group">
            <?php echo $this->main_button->render(); ?>
            <div class="dropdown-menu">
                <?php foreach ($this->sub_buttons as $key => $sb){ 
                    if ($sb instanceof UIButton){ 
                        echo $sb->customRender("button", "dropdown-item");
                    }else {
                        echo $sb->render();
                    }
                    ?>
                <?php } ?>
            </div>
        </div>
        <?php 
        return \ob_get_clean();
    }

    public function customRender(string $html_type, string $base_class="") : string{
        \ob_start();  ?>
        <div class="btn-group">
            <?php echo $this->main_button->customRender($html_type, $base_class); ?>
            <div class="dropdown-menu">
                <?php foreach ($this->sub_buttons as $key => $sb){ 
                    if ($sb instanceof UIButton){ 
                        echo $sb->customRender("a", "dropdown-item");
                    }else {
                        echo $sb->render();
                    }
                    ?>
                <?php } ?>
            </div>
        </div>
        <?php 
        return \ob_get_clean();
    }
}