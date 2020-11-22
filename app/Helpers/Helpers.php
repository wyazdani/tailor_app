<?php
if (!function_exists('get_table_name')) {
    function get_table_name($table_name)
    {
        return with(new $table_name)->getTable();
    }
    function isDecimal( $value ) {
        if ( strpos( $value, "." ) !== false ) {
            return true;
        }
        return false;
    }
}
