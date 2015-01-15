<?php
// Receive AJAX requests for conversions and supply the results
date_default_timezone_set('Europe/London');

function from_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}


if (from_ajax()) {
    if (isset($_POST["action"]) && !empty($_POST["action"]) && isset($_POST["input"]) && !empty($_POST["input"])) { 
        handleRequest($_POST['action'], $_POST['input']);
    }
}

function handleRequest($action, $input) {
    require dirname(dirname(__FILE__)).'/roman-numeral-tool.php';

    if ( $action == "parse" ) {
        $RomanNumeralTool = new RomanNumeralTool();
        echo $RomanNumeralTool->parse($input);
    }

    if ( $action == "generate" ) {
        $RomanNumeralTool = new RomanNumeralTool();
        echo $RomanNumeralTool->generate((integer)$input);
    }
}


?>