<?php
define( 'WP_USE_THEMES', false );
require_once( '../../../wp-load.php' );
require_once( 'services/odServices.php' );

update_debate();

function update_debate() {
    
if (get_option('purchased') == true) {
    
    $oxd = new OxD();
    $service = 'refresh_votes';
    $debateId  = $_POST["debateId"];
    $post = get_post($debateId);
    $key = get_option('key');

        $data = array(
            "debate_id"  => $debateId,
            "product_key" => $key,
        );

        //Json Encode
        $json_data = json_encode($data);  
        $result_json = service_call($json_data, $service);
        $result = json_decode($result_json);
        $check = $result->check;

        if ($check == 'OK') {
            echo $result->msg;
            return;
        }

        else {

            if ( $oxd->set_debate( $debateId, $post ) == FALSE ) {
                return FALSE;
            } else {
                update_debate();
            }
    }
}

return;
}


?>