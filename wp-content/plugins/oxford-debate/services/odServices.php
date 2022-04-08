<?php 
/** Oxford Debate web services functions **/

/**
    * REST Services
*/

function service_call($json_data, $service) {

$method = 'getCallDetails';
$url = 'https://insight.oxfordstyledebate.com/oxdws/' . $service . '/';
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => "Accept: application/json\r\n" . "Content-Type: application/json\r\n",
        'content' => $json_data
    )
);
$context  = stream_context_create($opts);
$result = file_get_contents($url, false, $context);

return $result;
   
}

?>
