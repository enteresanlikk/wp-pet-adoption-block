<?php
/*
    Plugin Name: Pet Adoption
    Version: 1.0.0
    Author: Bilal Demir
    Author URI: https://bilaldemir.dev
*/

if( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path(__FILE__) . 'inc/generatePet.php';

define( 'PET_ADOPTION_PATH', plugin_dir_path( __FILE__ ));

class PetAdoptionTablePlugin {
    function __construct() {
        global $wpdb;
        $this->charset = $wpdb->get_charset_collate();
        $this->tablename = $wpdb->prefix . 'pets';

        add_action('activate_new-database-table/new-database-table.php', array($this, 'onActivate'));
        //add_action('admin_head', array($this, 'onAdminRefresh'));
        add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
        //add_filter('template_include', array($this, 'loadTemplate'), 99);

        add_action('admin_post_create_pet', array($this, 'ajaxCreatePet'));
        add_action('admin_post_delete_pet', array($this, 'ajaxDeletePet'));
    }

    function onActivate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta("
        CREATE TABLE $this->tablename (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `birthyear` smallint(5) DEFAULT 0,
          `petweight` smallint(5) DEFAULT 0,
          `favfood` varchar(60) DEFAULT '',
          `favhobby` varchar(60) DEFAULT '',
          `favcolor` varchar(60) DEFAULT '',
          `petname` varchar(60) DEFAULT '',
          `species` varchar(60) DEFAULT '',
          PRIMARY KEY  (`id`)
        ) $this->charset;");
    }

    function onAdminRefresh() {
        $this->populateFast();
    }

    function loadAssets() {
        if (is_page('pets')) {
            wp_enqueue_style('petadoptioncss', plugin_dir_url(__FILE__) . 'assets/css/style.css');
        }
    }

    function loadTemplate($template) {
        if (is_page('pets')) {
            return PET_ADOPTION_PATH . 'inc/template-pets.php';
        }
        return $template;
    }

    function populateFast() {
        $query = "INSERT INTO $this->tablename (`species`, `birthyear`, `petweight`, `favfood`, `favhobby`, `favcolor`, `petname`) VALUES ";
        $numberofpets = 100000;
        for ($i = 0; $i < $numberofpets; $i++) {
            $pet = generatePet();
            $query .= "('{$pet['species']}', {$pet['birthyear']}, {$pet['petweight']}, '{$pet['favfood']}', '{$pet['favhobby']}', '{$pet['favcolor']}', '{$pet['petname']}')";
            if ($i != $numberofpets - 1) {
                $query .= ", ";
            }
        }

        global $wpdb;
        $wpdb->query($query);
    }

    function ajaxCreatePet() {
        if(current_user_can('administrator')) {
            $pet = generatePet();
            $pet['petname'] = sanitize_text_field($_POST['petname']);
            global $wpdb;
            $wpdb->insert($this->tablename, $pet);

            return wp_safe_redirect(site_url('/pets'));
        }

        return wp_safe_redirect(site_url());
    }

    function ajaxDeletePet() {
        if(current_user_can('administrator')) {
            global $wpdb;
            $id = sanitize_text_field($_POST['id']);
            $wpdb->delete($this->tablename, ['id' => $id]);

            return wp_safe_redirect(site_url('/pets'));
        }

        return wp_safe_redirect(site_url());
    }
}

$petAdoptionTablePlugin = new PetAdoptionTablePlugin();

class OurPluginPlaceholderBlock {
    function __construct($name) {
        $this->name = $name;
        add_action('init', [$this, 'onInit']);
    }

    function ourRenderCallback($attributes, $content) {
        ob_start();
        require PET_ADOPTION_PATH . 'our-blocks/' . $this->name . '.php';
        return ob_get_clean();
    }

    function onInit() {
        wp_register_script($this->name, plugin_dir_url(__FILE__) . "/our-blocks/{$this->name}.js", array('wp-blocks', 'wp-editor'));

        register_block_type("pet-adoption/{$this->name}", array(
            'editor_script' => $this->name,
            'render_callback' => [$this, 'ourRenderCallback']
        ));
    }
}

new OurPluginPlaceholderBlock("list");