<?php

include_once("../config.php");

if (isset($CONFIG["times"])) {
    $TIMES = $CONFIG["times"];
} else {
    $TIMES = ['1hour', '4hours', '12hours', '1day', '2days', '1week', '1month', '3months', '6months', '1year'];
}

?>

<form method='get' action='hosts.php' style='margin-top: 15px'class='pull-right'>
<select name='from' onchange='this.form.submit()'>
<?php
    foreach ($TIMES as $timefrom) {
        $current = $_GET["from"] ?: "-4hours";
        $selected = ($current == "-${timefrom}") ? "selected" : "";
        echo "<option value='-{$timefrom}' {$selected}>{$timefrom}</option>";
}
?>
</select>
</form>
