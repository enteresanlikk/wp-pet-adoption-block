<?php

class PetService
{
    public $pets;
    private $tableName;
    private $args;
    private $placeholders;

    public function __construct() {
        global $wpdb;

        $this->tableName = $wpdb->prefix . "pets";
        $this->args = $this->getArgs();
        $this->placeholders = $this->buildPlaceholders();

        $query = "SELECT * FROM $this->tableName";
        $query .= $this->buildWhereClause();
        $query .= " LIMIT 100";

        $countQuery = "SELECT Count(id) FROM $this->tableName";
        $countQuery .= $this->buildWhereClause();

        $this->pets = $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
        $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->placeholders));
    }

    function getArgs() {
        $temp = [];

        if (isset($_GET['favcolor'])) $temp['favcolor'] = sanitize_text_field($_GET['favcolor']);
        if (isset($_GET['species'])) $temp['species'] = sanitize_text_field($_GET['species']);
        if (isset($_GET['minyear'])) $temp['minyear'] = sanitize_text_field($_GET['minyear']);
        if (isset($_GET['maxyear'])) $temp['maxyear'] = sanitize_text_field($_GET['maxyear']);
        if (isset($_GET['minweight'])) $temp['minweight'] = sanitize_text_field($_GET['minweight']);
        if (isset($_GET['maxweight'])) $temp['maxweight'] = sanitize_text_field($_GET['maxweight']);
        if (isset($_GET['favhobby'])) $temp['favhobby'] = sanitize_text_field($_GET['favhobby']);
        if (isset($_GET['favfood'])) $temp['favfood'] = sanitize_text_field($_GET['favfood']);

        return $temp;
    }

    function buildPlaceholders() {
        return array_map(function($key) {
            return $key;
        }, $this->args);
    }

    function buildWhereClause() {
        $query = "";

        if (count($this->args) > 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", array_map(function($key) {
                return $this->specificQuery($key);
            }, array_keys($this->args)));
        }

        return $query;
    }

    function specificQuery($key) {
        switch ($key) {
            case 'minyear':
                return "birthyear >= %d";
            case 'maxyear':
                return "birthyear <= %d";
            case 'minweight':
                return "petweight >= %d";
            case 'maxweight':
                return "petweight <= %d";
            default:
                return "$key = %s";
        }
    }
}