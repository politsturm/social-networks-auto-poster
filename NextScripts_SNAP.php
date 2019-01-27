<?php
/*
Plugin Name: NextScripts: Social Networks Auto-Poster
Plugin URI: https://www.nextscripts.com/social-networks-auto-poster-for-wordpress
Description: This plugin automatically publishes posts from your blog to your social media accounts on Facebook, Twitter, LinkedIn, Google+ and 25 more networks.
Author: NextScripts
Version: 4.2.7
Author URI: https://www.nextscripts.com
Text Domain: social-networks-auto-poster-facebook-twitter-g
Copyright 2012-2018  NextScripts Corp
*/
define('NextScripts_SNAP_Version', '4.2.7'); define('NextScripts_SNAP_Version_Date', 'Aug 15, 2018'); require_once "inc/nxs_functions_wp.php"; if (!defined('NXSSNAP_BASENAME')) {
    define('NXSSNAP_BASENAME', plugin_basename(__FILE__));
}

if (true===nxs_doSystemInitCheck()) { // error_reporting(E_ALL); ini_set('display_errors', '1');
    require_once "inc/nxs_functions.php";
    require_once "inc/nxs_functions_adv.php";
    require_once "inc/nxs_functions_engine.php";
    require_once "inc/nxs_class_http.php";
    require_once "inc/nxs_class_snap.php";
    require_once "inc/nxs_class_flt.php";
    require_once "inc/nxs_class_mgmt.php";
    require_once "inc/nxs_class_ntlist.php";
    require_once "inc/nxs_class_oauth.php";
    //## Some Globals and Constants
    global $nxs_snapAvNts, $nxs_SNAP, $nxs_snapSetPgURL, $nxs_snapThisPageUrl;
    $nxs_snapSetPgURL = nxs_get_admin_url('admin.php?page=nxssnap');
    $nxs_snapThisPageUrl = nxs_get_admin_url(str_ireplace('wp-admin/', '', $_SERVER['REQUEST_URI']));
    define('NXS_PLPATH', plugin_dir_path(__FILE__));
    define('NXS_PLURL', plugin_dir_url(__FILE__));
    define('NXS_SETV', 350);
    global $nxs_plurl;
    $nxs_plurl =  NXS_PLURL; // Remove once networks upgraded
    //## Get Available Networks
    do_action('nxs_actBeforeGetAvNetworks');
    if (!isset($nxs_snapAvNts) || !is_array($nxs_snapAvNts)) {
        $nxs_snapAvNts = array();
    }
    $nxs_snapAPINts = array();
    foreach (glob(NXS_PLPATH.'inc-cl/*.php') as $filename) {
        require_once $filename;
    }
    do_action('nxs_actAfterGetAvNetworks');
    //## MAIN
    add_action('init', 'nxs_initSNAP', 0);
    function nxs_initSNAP()
    {
        global $nxs_SNAP;
        $nxs_SNAP = new nxs_SNAP();
        nxs_checkAddQueryTable();
        nxs_checkAddLogTable();
        new nxs_Filters;
        $role = get_role('administrator');
        if (!empty($role)) {
            $role->add_cap('haveown_snap_accss');
        } //prr($nxs_SNAP->sMode); prr($nxs_SNAP->nxs_options);
        //##### Save Meta for posts / Add/Change meta on Save
        add_action('edit_post', array($nxs_SNAP, 'NS_SNAP_SavePostMetaTags'));
        add_action('publish_post', array($nxs_SNAP, 'NS_SNAP_SavePostMetaTags'));
        add_action('save_post', array($nxs_SNAP, 'NS_SNAP_SavePostMetaTags'));
        //## OG Tags
    if (!empty($nxs_SNAP->nxs_options['nxsOG']) && ($nxs_SNAP->nxs_options['nxsOG'] == 'A' || $nxs_SNAP->nxs_options['nxsOG'] == 'N')) {  // nxs_LogIt('BG','','','',$_SERVER["HTTP_USER_AGENT"]);
      if (!empty($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "ChXrome") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Google (") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "LinkedInBot") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "XING-contenttabreceiver") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Java/1.7.0_45") !== false)) {
          if (!is_admin()) {
              @ob_start('nxs_ogtgCallback');
          }
      }
        add_action('wp_head', 'nxs_addOGTagsPreHolder', 150);
        add_action('shutdown', 'nxs_end_flush_ob', 1000);
    }
        if ($nxs_SNAP->sMode['l']=='M') {
            if (function_exists('nxssnapmu_columns_head')) {
                add_filter('wpmu_blogs_columns', 'nxssnapmu_columns_head');
            }
            if (function_exists('nxssnapmu_columns_content')) {
                add_action('manage_blogs_custom_column', 'nxssnapmu_columns_content', 10, 2);
            }
            if (function_exists('nxssnapmu_columns_content')) {
                add_action('manage_sites_custom_column', 'nxssnapmu_columns_content', 10, 2);
            }
            if (function_exists('nxs_add_style')) {
                add_action('admin_footer', 'nxs_add_style');
            }
            if (function_exists('nxs_saveSiteSets_ajax')) {
                add_action('wp_ajax_nxs_saveSiteSets', 'nxs_saveSiteSets_ajax');
            }
        }
        //## Watch for New Posts
        if ($nxs_SNAP->sMode['r']) {
            add_action('transition_post_status', 'nxs_snapLogPublishTo', 100, 3);
        }
    }
    //## Actions
    add_action('admin_head', 'nxs_adminCSS');
    add_action('admin_enqueue_scripts', 'nxssnap_enqueue_scripts');
    add_action('admin_init', 'nxs_adminInitFunc');
    add_action('in_admin_header', 'nxs_admin_header');
    add_action('wp_ajax_nxs_getExpSettings', 'nxs_getExpSettings_ajax'); //## Export Settings
    //## Logs
    add_action('wp_ajax_nxs_clLgo', 'nxs_clLgo_ajax');
    add_action('wp_ajax_nxs_rfLgo', 'nxs_rfLgo_ajax');
    //## Ajax
    add_action('wp_ajax_nxs_snap_aj', 'nxs_snapAjax');
    //## Cron and Schedulle Actions
  add_filter('cron_schedules', 'cron_add_nxsAutPoster'); //## Adds NXS Autoposter(nxsquery) to WP Schedules List
  add_action('nxs_querypost_event', 'nxs_checkQuery'); //## Main NXS Cron function - nxs_querypost_event to run nxs_checkQuery() function.
  add_action('nxs_hourly_event', 'nxs_do_this_hourly'); //## Adds Hourly Event
  add_action('wp_loaded', 'nxs_activation'); //## Adds nxs_querypost_event to wp_schedule_event as nxsquery
  //## Cron reposter action functions
  function cron_add_nxsAutPoster($schedules)
  {
      global $nxs_SNAP;
      if (is_object($nxs_SNAP)) {
          $o = $nxs_SNAP->nxs_options;
          $i = !empty($o['queryInterval'])?$o['queryInterval']:60;
      } else {
          $i = 60;
      }
      $schedules['nxsquery'] = array( 'interval' => $i, 'display' => __('NextScripts AutoPoster'));
      return $schedules;
  }
    function nxs_activation()
    {
        if (!wp_next_scheduled('nxs_hourly_event')) {
            wp_schedule_event(time(), 'hourly', 'nxs_hourly_event');
        }
        if (!wp_next_scheduled('nxs_querypost_event')) {
            wp_schedule_event(time(), 'nxsquery', 'nxs_querypost_event');
        }
    }
    if (function_exists("add_shortcode")) {
        add_shortcode('nxs_links', 'nxs_links_func');
    }
}
