<?php
require_once("../config.php");
require_once("../phplib/GraphiteGraph.php");
include_once("header.php");
?>
<div class="row">
<?php
    foreach ( $metrics as $metric) {

        $graph = new GraphiteGraph($CONFIG['graphitehost'], $_GET["from"]);
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
    }
?>
</div>

<?php
include_once("footer.php");
?>
