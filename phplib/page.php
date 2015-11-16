<?php
require_once("../config.php");
require_once("../phplib/GraphiteGraph.php");
include_once("header.php");

use Yagd\GraphiteGraph;
?>
<div class="row">
<?php
    foreach ( $metrics as $metric) {

        $graph = new GraphiteGraph($CONFIG['graphite']['host'], $_GET["from"],
                                   null, $CONFIG['graphite']['hidelegend']);
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
    }
?>
</div>

<?php
include_once("footer.php");
?>
