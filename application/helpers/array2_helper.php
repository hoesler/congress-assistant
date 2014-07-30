<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function implode_nonempty($glue, $pieces) {
    	return implode_array($glue, $pieces, "strlen");    
    }
    
    function implode_array($glue, $pieces, $filter_function){        
        $filtered_array = $pieces;
        if ($filter_function && function_exists($filter_function))
        	$filtered_array = array_filter($pieces, $filter_function);
        
        return implode($glue, $filtered_array);
    }

?>