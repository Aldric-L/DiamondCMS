<?php 
namespace PageBuilders; 

class DefaultAdminTable extends AdminTable implements renderUIComponent{

public function addLine(array $line){
    if (false === ($lc = $this->checkIfLineValid($line)))
        throw new \Exception("Line is not correct according to head and type.", 129);
    $line = $lc;
    \ob_start(); ?>
        <tr id="<?php echo ($this->id !== "") ? $this->id . "_" : ""; ?>line_<?= sizeof($this->lines); ?>">
            <?php foreach ($line as $l){ ?>
                <th><?= $l->render(); ?></th>
            <?php } ?>
        </tr>
    <?php $buff = \ob_get_clean();
    array_push($this->lines, $buff);
    return $this;
}

public function render() : string{
    \ob_start(); ?> 
    <?php if ($this->responsive){ ?> <div class="table-responsive"><?php } ?>
    <table class="table <?php echo $this->class; ?>">
        <thead>
            <tr>
                <?php foreach ($this->head as $h) { ?>
                <th scope="col"><?php echo $h; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->lines as $l){ 
                echo $l;
            } ?>
        </tbody>
    </table>
    <?php if ($this->responsive){ ?> </div><?php } ?>
    <?php 
    $buffer = \ob_get_clean();
    return $buffer;
}
}