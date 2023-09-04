<?php 
namespace PageBuilders; 

class DefaultAdminPanel extends AdminPanel implements renderUIComponent{
    public function __construct(string $name, string $icon, renderUIComponent $content, string $col){
        parent::__construct($name, $icon, $content, $col);
        if ($icon != ""){
            $this->name = '<i class="fa '. $icon . ' fa-fw"></i> ' . $name;
        }
    }

    public function render() : string{
        \ob_start(); ?>
        <?php if ($this->should_col){ ?>
        <div class="col-<?php echo $this->col; ?>">
        <?php } ?>
            <div class="card shadow <?php echo $this->col; ?>">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom"><?php echo $this->name; ?></h6>
                </div>
                <div class="card-body">
                    <?php echo $this->content->render(); ?>
                </div>
            </div>
        <br />
        <?php if ($this->should_col){ ?>
        </div>
        <?php } ?>
        <?php return \ob_get_clean();
    }
}