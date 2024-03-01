<?php 
namespace PageBuilders; 

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