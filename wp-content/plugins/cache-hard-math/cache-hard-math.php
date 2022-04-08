<?php
/*
Plugin Name:  Hard Math
*/



function do_hard_math($int){
    //$start is the starting integer

    $start = $int;

    $i =0;
    while($i <100000){
        $int = pow( sqrt( sqrt( sqrt( sqrt( $int) ) ) ), 16.0001);
		$i++;
    }

    // Return our array: what we started with and what resulted
	return array ( $start, $int );
}


function get_hard_math_transient(){
    //Get Transient
    //get_transient() will return false if the transient doesn’t exist. 
    //So it’s really important to test for the transient’s existence before using it

    $result = get_transient("hard_math");

    if(false !== $result){
        //transient exists
        return $result;
    }

    // Get array from doing "hard math" (on seconds elapsed in current minute)

    $mathed = do_hard_math(date("s"));


    // Attempt to set transient with array results; timeout is 10 seconds

    $bool_response = set_transient("hard_math", $mathed, 10);

    if(false ===$bool_response){
        	// Setting the transient didn't work, so return false for failure
		return false;

    }

    // Transient is now set, so get it and return it
	return get_transient( 'hard_math' );
}


function filter_content_with_hard_math_transient($content){
    //get the transient
    $result = get_hard_math_transient();

    // If transient isn't an array, just return content unaltered
	if ( ! is_array( $result ) ) {
		return $content;
	}

    // Prepend string with transient data to content and return it

    return '<p>(<small>I did some terrifyingly inefficient math on the number ' . ltrim( $result[0], '0' ) . ', and the result was: ' . $result[1] . '</small>)</p>' . $content;

}

add_filter("the_content", "filter_content_with_hard_math_transient");