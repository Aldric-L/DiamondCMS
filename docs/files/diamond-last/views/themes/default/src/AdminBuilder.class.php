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

class DefaultAdminForm extends AdminForm implements renderUIComponent{
    public function addTextField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "text",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "text"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>> 
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addHiddenField(string $name, string $val="", bool $needed=false) {
        array_push($this->fields, array(
            "type" => "text",
            "name" => $name,
            "value" => $val, 
            "needed" => $needed,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        \ob_start(); ?>
            <input type="hidden" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> >
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }


    public function addTextAreaField(string $name, string $title="", string $val="", int $rows=5, bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "textarea",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "textarea"));
        }
        \ob_start(); ?>
            <div class="form-group floating-label-form-group controls">
                <?php if ($title != ""){ ?>
                <label><?php echo $title; ?></label>
                <?php } ?>
                <textarea rows="<?php echo $rows; ?>" class="form-control" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled readonly<?php }?>><?php echo $val; ?></textarea>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    /**
     * 
     * 
     * @param array $option : array(array("val" => val, "disp" => disp (Optionnel :, "selected" => true) ))
     */
    public function addSelectField(string $name, string $title="", array $options, bool $needed=false, $disabled=false, string $helptext=""){
        $field_options = array(
            "type" => "select",
            "name" => $name,
            "title" => $title,
            "options" => $options, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        );
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <select class="form-control" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                    <?php foreach($options as $opt){ ?>
                        <option value="<?= $opt["val"]; ?>" <?php if (array_key_exists("selected", $opt) && is_bool($opt['selected']) && $opt['selected']): $selected = $opt["val"]; echo "selected"; endif; ?>><?= $opt["disp"]; ?></option>
                    <?php }?>
                </select>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        if (isset($selected))
            $field_options['value'] = $selected;
        array_push($this->fields, $field_options);
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "select"));
        }
        return $this;
    }

    public function addMpField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "password",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "password"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="password" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addNumberField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "number",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "number"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="number" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addEmailField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "email",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "email"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="email" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addCheckField(string $name, string $title, bool $checked=false, bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "check",
            "name" => $name,
            "title" => $title,
            "value" => $checked, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "check"));
        }
        \ob_start(); ?>
            <div class="form-check">
                <input class="form-check-input" name="<?php echo $name; ?>" type="checkbox" id="<?php echo $id; ?>" <?php if ($checked) { ?> checked <?php } ?> <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <label for="<?php echo $name; ?>" class="form-check-label"><?php echo $title; ?></label>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addFileField(string $name, string $title, bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "file",
            "name" => $name,
            "title" => $title,
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "file"));
        }
        \ob_start(); ?>
        <div class="form-group">
            <div class="custom-file">
    <input type="file" class="custom-file-input" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <label class="custom-file-label" for="<?php echo $name; ?>"><?php echo $title; ?></label>
            </div>
            <?php if ($helptext != "") { ?>
                <small class="form-text text-muted"><?php echo $helptext; ?></small>
            <?php } ?>
        </div>   
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addAPIButton(AdminAPIButton $button){
        array_push($this->fields, array(
            "type" => "APIButton",
            "value" => $button
        ));
        if ($this->needAll && $button->getTosend() == $this){
            $button->setAllneeded();
        }
        array_push($this->buttonsBuffer, $button->render());
        return $this;
    }

    public function setButtonsLine(string $attr){
        $this->buttons_line = $attr;
        return $this;
    }

    public function addModalButton(AdminModalButton $button){
        array_push($this->fields, array(
            "type" => "ModalButton",
            "value" => $button
        ));
        array_push($this->buttonsBuffer, $button->render());
        return $this;
    }

    public function addCustom(renderUIComponent $custom, string $id="", $availableif=false){        
        if ($id !== "" && !is_bool($availableif) && $availableif instanceof AvailableIf){
            array_push($this->availableIfs, $availableif->setTarget($id, "custom", AvailableIf::HIDE));
        }
        if ($id !== "")
            array_push($this->inBuffer, '<span id="' . $id . '">' . $custom->render() . '</span>');
        else
            array_push($this->inBuffer, $custom->render());
        return $this;
    }

    public function setFilter(string $field_name, array $replacement){
        \ob_start(); ?>
        <script>
            $("#<?php echo $field_name; ?>").on("change", (e) => {
                var value = $("#<?php echo $field_name; ?>").val();
                <?php foreach($replacement as $key => $r){ 
                    if($key == '"') { ?>
                        value = value.replaceAll('<?php echo $key; ?>', '<?php echo $r; ?>');
                    <?php }else { ?>
                        value = value.replaceAll("<?php echo $key; ?>", "<?php echo $r; ?>");
                    <?php }
                 } ?>
                $("#<?php echo $field_name; ?>").val(value);
            });
            
        </script>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <form method="post" id="<?php echo $this->id; ?>">
            <?php foreach ($this->inBuffer as $buf) {
                    echo $buf;
            } ?>
            <p <?php echo $this->buttons_line; ?>>
            <?php foreach ($this->buttonsBuffer as $buf) {
                    echo $buf;
            } ?></p>
        </form>
        <?php
        if (is_array($this->availableIfs) && !empty($this->availableIfs)){ ?>
        <script>
            <?php foreach ($this->availableIfs as $if){ ?>
                $("#<?php echo $if->getCondFieldId(); ?>").on("<?php echo $if->getVerbe(); ?>", (e) => {
                    // ATTENTION, il ne faut pas renommer ces variables car elles sont utilisées dans la condition générée en PHP !
                    var value = getValue(e.target, true).value;
                    var targetValue = getValue($("#<?php echo $if->getTarget()['id']; ?>")[0], true).value;
                    
                    if (<?php echo $if->getCond(); ?>){
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").attr('disabled', false);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").show();
                        <?php } ?>
                    }else {
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").attr('disabled', true);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").hide();
                        <?php } ?>
                    }

                    //console.log(e, value, targetValue, <?php echo $if->getCond(); ?>);
                })
            <?php } ?>
        </script>
        <?php 
        }
        
        $this->buffer = \ob_get_clean();
        return $this->buffer;
    }
}

class DefaultAdminModal extends AdminModal implements renderUIComponent{

    public function __construct(string $title, string $id, renderUIComponent $content, string $icon="", string $size=""){
        parent::__construct($title, $id, $content, $icon, $size);
        if ($icon != ""){
            $this->title = '<i class="fa '. $icon . ' fa-fw"></i> ' . $title;
        }
    }

    public function addAPIButton(renderUIComponent $button){
        array_push($this->buttonsBuffer, $button->render());
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <div id="<?php echo $this->getId(); ?>" class="modal fade">
            <div class="modal-dialog <?php echo $this->size; ?>" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title"><?php echo $this->title; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <?php echo $this->content->render(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button> 
                        <?php foreach ($this->buttonsBuffer as $buf) {
                                echo $buf;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        $this->buffer = \ob_get_clean();
        return $this->buffer;
    }
}

class DefaultAdminButton extends AdminButton implements UIButton{
    public function render() : string{
        \ob_start();  ?>
        <button type="button" 
        class="btn <?php echo $this->class; ?>" 
        <?php foreach ($this->attr as $key => $a){ ?>
            <?php echo $key; ?>="<?php echo $a; ?>"
        <?php } ?>
        ><?php echo $this->title; ?></button>    
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }

    public function customRender(string $html_type, string $base_class="") : string{
        \ob_start();  ?>
        <<?php echo $html_type; ?> <?php echo ($html_type == "button") ? 'type="button"' : ""; ?> 
        class="<?php echo $base_class . " " . $this->class; ?>" 
        <?php foreach ($this->attr as $key => $a){ ?>
            <?php echo $key; ?>="<?php echo $a; ?>"
        <?php } ?>
        ><?php echo $this->title; ?></<?php echo $html_type; ?>>    
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }
}

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

class DefaultAdminAPIButton extends AdminAPIButton implements UIButton{
    public function __construct(string $title, string $class, string $api_link, string $api_module, string $api_verbe, string $func, $to_send, string $private_callback="", $should_reload="true", $show_return="false", $no_loading="false", bool $disabled=false, string $private_id="", bool $needAll=false){
        parent::__construct($title, $class, $api_link, $api_module, $api_verbe, $func, $to_send, $private_callback, $should_reload, $show_return, $no_loading, $disabled, $private_id, $needAll);

        $this->buffer = new DefaultAdminButton($title, "ajax-simpleSend " . $class);

        $this->buffer->addAttrS(array(
            "data-module" => $this->api_module . "/",
            "data-verbe" => $this->api_verbe,
            "data-func" => $this->func,
            "data-reload" => ($this->should_reload) ? "true" : "false",
            "data-noloading" => $this->no_loading,
            "data-showreturn" => $this->show_return,
            "data-func" => $this->func,
        ));

        // On ne spécifie plus le link pour éviter des pbs de cache et de mauvais lien
        if ($this->api_link != LINK . "api/")
            $this->buffer->addAttr("data-api", $this->api_link);

        if (!is_string($this->to_send) && is_subclass_of($this->to_send, "PageBuilders\AdminForm")){
            $this->buffer->addAttrS(array(
                "data-tosend" => "#" . $this->to_send->getId(),
                "data-useform" => "true"
            ));

            if ($this->allneeded)
                $this->buffer->addAttr("data-needAll", "true");
        }else if (is_string($this->to_send) && $this->to_send != null) {
            $this->buffer->addAttrS(array(
                "data-tosend" => $this->to_send
            ));
        }

        if ($this->disabled)
            $this->buffer->addAttr("disabled", "disabled");
        

        if ($this->private_id !== "")
            $this->buffer->addAttr("id", $this->private_id);
        
        if ($this->private_callback !== "")
            $this->buffer->addAttr("data-callback", $this->private_callback);
        return $this;
    }
    
    public function render() : string{
        return $this->buffer->render();
    }

    public function customRender(string $html_type, string $base_class="") : string{
        return $this->buffer->customRender($html_type, $base_class);
    }
}

class DefaultAdminModalButton extends AdminModalButton implements UIButton{
    public function __construct(string $title, string $class, AdminModal $modal, bool $disabled=false, string $id=""){
        parent::__construct($title, $class, $modal, $disabled, $id);
        $this->buffer = new DefaultAdminButton($title, $class);

        $this->buffer->addAttrS(array(
            "data-toggle" => "modal",
            "data-target" => "#" + $this->modal->getId(),
        ));

        if ($disabled)
            $this->buffer->addAttr("disabled", "disabled");

        if ($id !== "")
            $this->buffer->addAttr("id", $id);

    }

    public function render() : string{
        return $this->buffer->render();
    }

    public function customRender(string $html_type, string $base_class="") : string{
        return $this->buffer->customRender($html_type, $base_class);
    }
}

class DefaultAdminList extends AdminList implements renderUIComponent{

    public function addField(renderUIComponent $left, $right=null, $callBack=null, $class=null, string $id="", $availableif=null){
        if ($id !== "" && !is_bool($availableif) && $availableif instanceof AvailableIf){
            array_push($this->availableIfs, $availableif->setTarget($id, "custom", AvailableIf::HIDE));
        }
        \ob_start(); ?>
            <a 
            <?php if(is_subclass_of($callBack, "PageBuilders\AdminModal")){ ?>
            data-toggle="modal" data-target="#<?php echo $callBack->getId(); ?>" href="#"
            <?php }else if(is_string($callBack) && $callBack != ""){ ?>
            href="<?php echo $callBack; ?>"
            <?php } ?>
            <?php if ($id !== ""){ ?> id="<?php echo $id; ?>" <?php } ?>
            class="list-group-item <?php echo ($class != null && is_string($class)) ? $class : ""; ?>">
                <?php echo $left->render(); ?>
                <?php if($right != null && is_subclass_of($right, 'PageBuilders\renderUIComponent') ){ ?>
                    <span class="pull-right"><?php echo $right->render(); ?></span>
                <?php } ?>
            </a>
        <?php $buff = \ob_get_clean();
        array_push($this->fieldBuffer, $buff);
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <div class="list-group">
            <?php foreach ($this->fieldBuffer as $buf) {
                echo $buf;
            } ?>
        </div>
        <?php
        if (is_array($this->availableIfs) && !empty($this->availableIfs)){ ?>
        <script>
            <?php foreach ($this->availableIfs as $if){ ?>
                $("#<?php echo $if->getCondFieldName(); ?>").on("<?php echo $if->getVerbe(); ?>", (e) => {
                    // ATTENTION, il ne faut pas renommer ces variables car elles sont utilisées dans la condition générée en PHP !
                    var value = getValue(e.target, true).value;
                    var targetValue = getValue($("#<?php echo $if->getTarget()['name']; ?>")[0], true).value;
                    
                    if (<?php echo $if->getCond(); ?>){
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").attr('disabled', false);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").show();
                        <?php } ?>
                    }else {
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").attr('disabled', true);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").hide();
                        <?php } ?>
                    }

                    //console.log(e, value, targetValue, <?php echo $if->getCond(); ?>);
                })
            <?php } ?>
        </script>
        <?php 
        } 
        $this->buffer = \ob_get_clean();
        return $this->buffer;
    }
}

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

class DefaultAdminAreaChart extends AdminAreaChart implements renderUIComponent{
    // data = array ("labels" => ...., "data" => ...)
    // axis = array("y_label"=>, "y_unit"=>)
    public function __construct(AdminBuilder &$builder, array $data, array $axis, string $id, bool $legend=false){
        parent::__construct($builder, $data, $axis, $id);
        $this->builder = $builder;
        $this->data = $data;
        $this->axis = $axis;
        if (is_array($this->builder->getThemeConfig()) && isset($this->builder->getThemeConfig()["mode"]) && isset($this->builder->getThemeConfig()["Colors_" . $this->builder->getThemeConfig()["mode"]]) && is_array($this->builder->getThemeConfig()["Colors_" . $this->builder->getThemeConfig()["mode"]]))
            $colors = $this->builder->getThemeConfig()["Colors_" . $this->builder->getThemeConfig()["mode"]];
        \ob_start(); ?>
            Chart.defaults.global.defaultFontColor = '<?php echo (isset($colors['admin-text-color'])) ? $colors['admin-text-color'] : "#000000"; ?>';
            Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';

            function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
            } 
            var ctx = document.getElementById("<?php echo $this->id; ?>");
            var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php $i=0; foreach ($data['labels'] as $k => $l) { echo '"' . $l . '"'; if (sizeof($data['labels'])-1 != $i){ echo ",";} $i++;} ?>],
                datasets: [
                    <?php if (!isset($data['data']) && isset($data['datasets'])){
                        foreach ($data['datasets'] as $key => $dta){ ?>
                        {
                        label: "<?php echo (isset($axis['y_label'][$key])) ? $axis['y_label'][$key] : "Type inconnu"; ?>",
                        lineTension: 0.3,
                        borderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                        pointRadius: 3,
                        pointBackgroundColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                        pointBorderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                        pointHoverBorderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: [<?php $i=0; foreach ($dta as $k => $l) { if (!is_numeric($l)) { echo '"' . $l . '"'; }else{ echo $l; } if (sizeof($dta)-1 != $i){ echo ",";} $i++;} ?>],
                        },
                    <?php 
                        }
                    }else { ?>
                    {
                    label: "<?php echo (isset($axis['y_label'])) ? $axis['y_label'] : "Type inconnu"; ?>",
                    lineTension: 0.3,
                    borderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                    pointRadius: 3,
                    pointBackgroundColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                    pointBorderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                    pointHoverBorderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [<?php $i=0; foreach ($data['data'] as $k => $l) { if (!is_numeric($l)) { echo '"' . $l . '"'; }else{ echo $l; } if (sizeof($data['data'])-1 != $i){ echo ",";} $i++;} ?>],
                    },
                <?php } ?>
                ],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
                },
                scales: {
                xAxes: [{
                    time: {
                    unit: 'date'
                    },
                    gridLines: {
                    display: false,
                    drawBorder: false
                    },
                    ticks: {
                    maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                        return number_format(value) <?php echo (isset($axis['y_unit'])) ? '+"' . $axis['y_unit'] . '"' : ""; ?>;
                    }
                    },
                    gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                    }
                }],
                },
                legend: {
                display: <?php echo ($legend ? "true" : "false"); ?>
                },
                tooltips: {
                backgroundColor: "<?php echo (isset($colors['admin-bg-color'])) ? $colors['admin-bg-color'] : "#fff"; ?>",
                bodyFontColor: "<?php echo (isset($colors['admin-text-color'])) ? $colors['admin-text-color'] : "#000000"; ?>",
                titleMarginBottom: 10,
                titleFontColor: '<?php echo (isset($colors['admin-text-color'])) ? $colors['admin-text-color'] : "#000000"; ?>',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + number_format(tooltipItem.yLabel) <?php echo (isset($axis['y_unit'])) ? '+"' . $axis['y_unit'] . '"' : ""; ?>;
                    }
                }
                }
            }
            });

        <?php 
        $this->js_buffer = \ob_get_clean();
        $builder->addJSToRender($this->js_buffer);
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <div class="chart-area">
            <canvas id="<?php echo $this->id; ?>"></canvas>
        </div>
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }
}

class DefaultAdminPieChart extends AdminPieChart implements renderUIComponent{
    // data = array ("labels" => ...., "data" => ...)
    // axis = array("y_label"=>, "y_unit"=>)
    public function __construct(AdminBuilder &$builder, array $data, string $id, bool $legend=false){
        parent::__construct($builder, $data, $id);
        $this->builder = $builder;
        $this->data = $data;
        if (is_array($this->builder->getThemeConfig()) && isset($this->builder->getThemeConfig()["mode"]) && isset($this->builder->getThemeConfig()["Colors_" . $this->builder->getThemeConfig()["mode"]]) && is_array($this->builder->getThemeConfig()["Colors_" . $this->builder->getThemeConfig()["mode"]]))
            $colors = $this->builder->getThemeConfig()["Colors_" . $this->builder->getThemeConfig()["mode"]];
        \ob_start(); ?>
            Chart.defaults.global.defaultFontColor = '<?php echo (isset($colors['admin-text-color'])) ? $colors['admin-text-color'] : "#000000"; ?>';
            Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';

            var ctx = document.getElementById("<?php echo $this->id; ?>");
            var myPieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [<?php $i=0; foreach ($data['labels'] as $k => $l) { echo '"' . $l . '"'; if (sizeof($data['labels'])-1 != $i){ echo ",";} $i++;} ?>],
                    datasets: [
                        {
                        label: "<?php echo (isset($axis['y_label'])) ? $axis['y_label'] : "Type inconnu"; ?>",
                        borderColor: "<?php echo (isset($colors['admin-theme-color'])) ? $colors['admin-theme-color'] : "#000000"; ?>",
                        backgroundColor: [
                            <?php $i=0; foreach ($data['data'] as $k => $l){ echo "'#" . dechex(rand(0,255*255*255)) . "'"; if (sizeof($data['data'])-1 != $i){ echo ",";} $i++; } ?>
                            ],
                        data: [<?php $i=0; foreach ($data['data'] as $k => $l) { if (!is_numeric($l)) { echo '"' . $l . '"'; }else{ echo $l; } if (sizeof($data['data'])-1 != $i){ echo ",";} $i++;} ?>],
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    "responsive": true,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10
                    },
                    legend: {
                        display: <?php echo ($legend ? "true" : "false"); ?>,
                        position: 'bottom'
                    },
                    cutoutPercentage: 80,
                }
            });

        <?php 
        $this->js_buffer = \ob_get_clean();
        $builder->addJSToRender($this->js_buffer);
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <div class="chart-pie">
            <canvas id="<?php echo $this->id; ?>"></canvas>
        </div>
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }
}

class DefaultAdminAlert  extends AdminAlert implements renderUIComponent{

    public function render() : string {
        \ob_start(); ?> 
        <div class="alert alert-<?php echo $this->type; ?> <?php echo $this->dismissible ? "alert-dismissible" :""; ?> fade show" role="alert">
            <strong><?php echo !empty($this->title) ? $this->title:""; ?></strong> <?php echo !empty($this->content) ? $this->content:""; ?>
            <?php if($this->dismissible): ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?php endif; ?>
        </div>
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }
}