<?php
define( 'WP_USE_THEMES', false );
require_once( '../../../wp-load.php' );
require_once( 'services/odServices.php' );

$service = 'set_vote';
$debateId  = $_POST["debateId"];
$key = get_option('key');
$userId  = $_POST["userId"];
$voteType   = $_POST["voteType"];

    $data = array(
        "debate_id"  => $debateId,
        "posture"     => $voteType,
        "user_id"  => $userId,
        "product_key" => $key,
    );

    //Json Encode
    $json_data = json_encode($data);  
    $result = service_call($json_data, $service);
    setcookie('oxd-voted',$debateId . $voteType,time() + 86400,'/');
    echo $result;

?>