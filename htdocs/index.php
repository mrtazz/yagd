<?php
include_once("header.php");
?>
<?php
        require_once "../phplib/GraphiteGraph.php";

        $graph = new GraphiteGraph("https://graphite.unwiredcouch.com");

        $graph->render("carbon.agents.vlad_unwiredcouch_com-a.committedPoints");
?>

<?php
include_once("footer.php");
?>
