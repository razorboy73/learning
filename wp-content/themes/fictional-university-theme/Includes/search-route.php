<?php

add_action("rest_api_init", "universityRegisterSearch");


function universityRegisterSearch(){
    register_rest_route("university/v1","search", array(
        "methods" => WP_REST_Server::READABLE,
        //instead of GET
        "callback" => "universitySearchResults"
    ));
}


function  universitySearchResults(){
    return "You created a route";
}