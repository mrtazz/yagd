<?php
require_once("../config.php");
require_once("../phplib/GraphiteGraph.php");
include_once("header.php");
?>
<?php
    foreach ( $metrics as $metric) {

        $graph = new GraphiteGraph($CONFIG['graphitehost']);
        $graph->render($metric);
    }
?>

<?php
include_once("footer.php");
?>
