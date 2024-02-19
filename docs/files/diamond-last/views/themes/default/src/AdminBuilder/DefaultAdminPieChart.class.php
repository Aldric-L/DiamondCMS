<?php 
namespace PageBuilders; 

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