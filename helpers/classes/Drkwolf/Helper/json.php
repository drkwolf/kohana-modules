<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 *
 * @author drkwolf
 * @package drkwolf.helper.json
 * @version 1.0
 */

/**
 * JSON Helper extension
 */
class Drkwolf_Helper_JSON {

    /**
     * export model to json
     * 
     * @param 	ORM/DB_result	$data: 
     * @param 	Array[string] $key: accepted keys
     *  - attribute name : to be included
     *  - atttribute => array() : fetch association keys(one->one), if array is empty 
     *      fetch them all, otherwise fetch the need keys
     *   TODO - attribute => 'fetch_all' : fetch all assocaition (
     * @return 	hash			:
     * 
     * @since 1.0
     * @author drkwolf 
     */
    public static function encode_model($data, array $keys = array()) {
        $results = array();
        if ($data instanceof ORM ) {
            if (empty($keys)) {
               $keys = array_keys($data->table_columns());
            }
            return self::filter($data, $keys);
        }
        //TODO throw exception of data is not instance of db_result
        foreach ($data as $record) {
            $result = array();
            foreach ($keys as $key => $value) {
                if (is_array($value)) {
                    if(empty($value)) {
                        $orm = $record->$key;
                        $fields = array_keys($orm->table_columns());
                        $result[$key] = self::filter($orm, $fields);
                    } else
                    $result[$key] = self::filter($record->$key, $value);
                } else 
                    $result[$value] = $record->$value;
            }
            $results[] = $result;
        }
        return $results;
    }
    /**
     * retrun hash key of the
     * @param ORM/DB_Resut $data
     * @param array $keys element to extract from data
     * @return Array 
     */
    public static function filter($data, array $keys = array() )
    {
        $result = array();
        foreach ($keys as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::filter($data->$key, $value);
            } else
                $result[$value] = $data->$value;
        }
        return $result;
    }

# ajax_as_html }}}
}
