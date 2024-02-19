<?php 
namespace PageBuilders; 

class DefaultAdminCard extends AdminCard implements renderUIComponent{

public function render() : string{
    \ob_start(); ?> 
        <div class="card border-left-<?php echo $this->class; ?> text-white shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <?php if ($this->icon !== ""){ ?><div class="col mr-2"><?php } ?>
                        <div class="text-xs font-weight-bold text-<?php echo $this->class; ?> text-uppercase mb-1">
                        <?php echo $this->title; ?></div>
                        <?php if ($this->subtitle !== ""){ ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $this->subtitle; ?></div>
                        <?php } ?>
                    <?php if ($this->icon !== ""){ ?></div><?php } ?>
                    <?php if ($this->icon !== ""){ ?>
                            <div class="col-auto">
                                <i class="fas <?php echo $this->icon; ?> fa-2x text-gray-300"></i>
                            </div>
                        <?php } ?>
                        </div>
                
            </div>
        </div>
    <?php 
    $buffer = \ob_get_clean();
    return $buffer;
}
}