<?php
//## Check/Test Functions
if (!function_exists("nxs_doSystemInitCheck")) {
    function nxs_doSystemInitCheck()
    {
        global $nxs_mLimit;
        //## Check if Pinpressr is installed
        if (function_exists('pbpPostToPinterest') && function_exists('pbp_plugin_admin_init')) {
            add_action('admin_notices', 'nxs_noPinpressrMsg');
        }
        //## Memory Check
        $nxs_mLimit = ini_get('memory_limit');
        if (strpos($nxs_mLimit, 'G')) {
            $nxs_mLimit = (int)$nxs_mLimit * 1024;
        } else {
            $nxs_mLimit = (int)$nxs_mLimit;
        }
        if ($nxs_mLimit>0 && $nxs_mLimit<64) {
            add_filter('plugin_row_meta', 'nxs_row_meta_nomem', 10, 2);
            add_filter('plugin_action_links', 'ns_add_nomem_link', 10, 2);
            return false;
        }
  
        $disabled_functions = @ini_get('disable_functions');
        if (!function_exists('curl_init')) {
            echo("<br/><b style='font-size:16px; color:red;'>Error: No CURL Found</b> - <i style='font-size:12px; color:red;'>Social Networks AutoPoster needs the CURL PHP extension. Please install it or contact your hosting company to install it.</i><br/><br/>");
            return false;
        }
        if (stripos($disabled_functions, 'curl_exec')!==false) {
            echo("<br/><b style='font-size:16px; color:red;'>curl_exec function is disabled in php.ini</b> - <i style='font-size:12px; color:red;'>Social Networks AutoPoster needs the CURL PHP extension. Please enable it or contact your hosting company to enable it.</i><br/><br/>");
            return false;
        }
  
        //## All OK - Return true;
        return true;
    }
}
if (!function_exists("nxs_noPinpressrMsg")) {
    function nxs_noPinpressrMsg()
    {
        echo '<div class="error"><p><b>Message from NextScripts SNAP Plugin for Wordpress</b></p><p>Pinpressr plugin is using Pinterest API library stolen from NextScripts. That library is outdated and will mess up SNAP funtionlity. Please uninstall and remove Pinpressr from your system.</p></div>';
    }
}
if (!function_exists("ns_add_nomem_link")) {
    function ns_add_nomem_link($links, $file)
    {
        global $nxs_mLimit;
        static $this_plg;
        if (!$this_plg) {
            $this_plg = plugin_basename(__FILE__);
        }
        if ($file == $this_plg) {
            $sl = '<b style="color:red;">Not Active! Not Enough Memory allowed for PHP.</b> <br/> You have '.$nxs_mLimit.' MB. You need at least 64MB. (Please see <a target="_blank" href="https://www.nextscripts.com/support-faq/#a16">FAQ #1.6</a>';
            array_unshift($links, $sl);
        }
        return $links;
    }
}
add_filter('plugin_action_links', 'ns_add_settings_link', 10, 2);
//## Add settings link to plugins list
if (!function_exists("ns_add_settings_link")) {
    function ns_add_settings_link($links, $file)
    {
        static $this_plugin;
        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }
        if ($file == $this_plugin) {
            $settings_link = '<a href="options-general.php?page=NextScripts_SNAP.php">'.__("Settings", "default").'</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }
}

function nxs_row_meta($data, $page)
{
    if ($page != NXSSNAP_BASENAME) {
        return $data;
    }
    return array_merge($data, array(
        '<a href="'.  admin_url('admin.php?page=nxssnap').'" target="_self">' . __('Settings') . '</a>',
    ));
}
function nxs_row_meta_nomem($data, $page)
{
    if ($page != NXSSNAP_BASENAME) {
        return $data;
    }
    global $nxs_mLimit;
    return array_merge($data, array(
        '<b style="color:red;">Not Active! Not Enough Memory allowed for PHP.</b> <br/> <span style="color:red;">You have '.$nxs_mLimit.' MB. You need at least 64MB.</span> (Please see <a target="_blank" href="https://www.nextscripts.com/support-faq/#a16">FAQ #1.6</a>)'
    ));
}
add_filter('plugin_row_meta', 'nxs_row_meta', 10, 2);
//## WP related Functions
if (!function_exists("nxs_get_admin_url")) {
    function nxs_get_admin_url($path='')
    { //## Workaround for some buggy 'admin hiding' plugins.
        $admURL = admin_url($path);
        if (substr($admURL, 0, 4)!='http') {
            $admURL = admin_url($path, 'https');
            $admURL = str_ireplace('https://', 'http://', $admURL);
        }
        return $admURL;
    }
}
if (!function_exists("nxs_getURL")) {
    function nxs_getURL($options, $postID, $addURLParams='')
    {
        global $nxs_SNAP;
        $gOptions = $nxs_SNAP->nxs_options;
        if (!isset($options['urlToUse']) || trim($options['urlToUse'])=='') {
            $myurl =  trim(get_post_meta($postID, 'snap_MYURL', true));
        }
        $ssl = (!empty($gOptions['ht']) && $gOptions['ht'] == ord('h'));
        if ($myurl!='') {
            $options['urlToUse'] = $myurl;
        }
        if ((isset($options['urlToUse']) && trim($options['urlToUse'])!='') || $ssl) {
            $options['atchUse'] = 'F';
        } else {
            $options['urlToUse'] = get_permalink($postID);
        }
        $options['urlToUse'] = $ssl?$gOptions['useSSLCert']:$options['urlToUse']; // $addURLParams = trim($gOptions['addURLParams']);
        if ($addURLParams!='') {
            $options['urlToUse'] .= (strpos($options['urlToUse'], '?')!==false?'&':'?').$addURLParams;
        }
        $forceSURL = trim(get_post_meta($postID, '_snap_forceSURL', true));
        if (empty($forceSURL)) {
            $forceSURL = !empty($options['useSURL']);
        } else {
            $forceSURL = $forceSURL =='1';
        }
        if (!empty($options['suUName'])) {
            $forceSURL = false;
        } //## SU does not allow Shorteners
        if ($forceSURL) {
            $mysurl = '';
            $mysurlArr = get_post_meta($postID, 'snap_MYSURL', true);
            if (!empty($mysurlArr)) {
                $t = $mysurlArr['t'];
                $mysurl = ($t==$options['urlToUse'] && $mysurlArr['r']==$gOptions['nxsURLShrtnr'])?$mysurlArr['s']:'';
            }
            if (!empty($mysurl)) {
                $options['urlToUse'] = $mysurl;
            } else {
                $t = $options['urlToUse'];
                $options['urlToUse'] = nxs_mkShortURL($t, $postID);
                update_post_meta($postID, 'snap_MYSURL', array('s'=>$options['urlToUse'],'t'=>$t,'r'=>$gOptions['nxsURLShrtnr']));
            }
            $options['surlToUse'] = $options['urlToUse'];
        }
        if (!empty($gOptions['forcessl']) && $gOptions['forcessl'] == 'N') {
            $options['urlToUse'] = str_ireplace('https', 'http', $options['urlToUse']);
        }
        if (!empty($gOptions['forcessl']) && $gOptions['forcessl'] == 'S') {
            $options['urlToUse'] = str_ireplace('http', 'https', str_ireplace('https', 'http', $options['urlToUse']));
        }
        return $options;
    }
}
//## NXS DB Tables
if (!function_exists('nxs_checkAddLogTable')) {
    function nxs_checkAddLogTable()
    {
        global $wpdb;
        $charset_collate = 'DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
        $installed_ver = get_option("nxs_log_db_table_version");
        if ($installed_ver=='1.5') {
            return true;
        }
        if (! empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
            if (! empty($wpdb->collate)) {
                $charset_collate .= " COLLATE {$wpdb->collate}";
            }
        }
        $table_name = $wpdb->prefix . "nxs_log";
        $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '1970-01-01 00:00:01' NOT NULL,
    uid bigint(20) DEFAULT 0 NOT NULL,
    act VARCHAR(255) DEFAULT '' NOT NULL,
    nt VARCHAR(255) DEFAULT '' NOT NULL,
    type VARCHAR(255) DEFAULT '' NOT NULL,
    flt VARCHAR(20) DEFAULT '' NOT NULL,
    nttype VARCHAR(20) DEFAULT '' NULL,
    msg text NOT NULL,    
    extInfo text NULL,    
    UNIQUE KEY id (id)
  ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        delete_option("nxs_log_db_table_version");
        add_option("nxs_log_db_table_version", '1.5');
    }
}

if (!function_exists('nxs_checkAddQueryTable')) {
    function nxs_checkAddQueryTable()
    {
        global $wpdb;
        $charset_collate = '';
        $table_name = $wpdb->prefix.'nxs_query';
        $installed_ver = get_option("nxs_query_db_table_version");
        if ($installed_ver=='1.2') {
            return true;
        }
        if (! empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }
        if (! empty($wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }
        $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        datecreated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        type VARCHAR(55) DEFAULT '' NOT NULL,
        postid mediumint(9) NULL,
        uid bigint(20) DEFAULT 0 NOT NULL,
        nttype VARCHAR(55) NULL,
        timetorun datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        refid mediumint(9) NULL,
        descr VARCHAR(255) NULL,
        extInfo text NULL,    
        UNIQUE KEY id (id)
    ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        delete_option("nxs_query_db_table_version");
        add_option("nxs_query_db_table_version", '1.2');
    }
}



if (!function_exists('nxs_adminCSS')) {
    function nxs_adminCSS()
    {
        ?>
  <style type="text/css"> .nxs_modal { display: none; position: absolute; z-index: 1000; top: 0; left: 0; height: 100%; width: 100%; background: rgba( 240, 240, 240, .5 ) url('<?php echo NXS_PLURL.'img/ajax-loader-med.gif'; ?>') 50% 50% no-repeat;} 
  
  .nxsEdWrapper:before {
    content: '';
    float: right;
    display: block;
    width: 150px;
    margin: 0 0 15px 15px;
  }
  
  
  </style>
<?php
    }
}
if (!function_exists("nxssnap_enqueue_scripts")) {
    function nxssnap_enqueue_scripts()
    {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
        } else {
            return;
        } // prr($screen->id);  prr($screen);
        if (is_object($screen) && $screen->base == 'post' || stripos($screen->id, 'NextScripts_SNAP')!==false || stripos($screen->id, 'nxssnap')!==false || stripos($screen->id, '_page_nxs')!==false) {
            global $nxs_SNAP, $pagenow;
            $options = $nxs_SNAP->nxs_options;
            if (empty($options['fltrs'])) {
                $options['fltrs'] = array();
            }
            if (empty($options['fltrs']['nxs_post_type'])) {
                $options['fltrs']['nxs_post_type'] = array('post');
            }
            if ($screen->base =='post' && ((empty($options['fltrs']['nxs_ie_posttypes']) && !in_array($screen->post_type, $options['fltrs']['nxs_post_type'])) || (!empty($options['fltrs']['nxs_ie_posttypes']) && in_array($screen->post_type, $options['fltrs']['nxs_post_type'])))) {
                return;
            }
            $path =  str_ireplace('/inc/', '', plugin_dir_url(__FILE__));
            wp_enqueue_script('nxssnap-scripts', $path . '/js-css/js.js', array( 'jquery' ), NextScripts_SNAP_Version);
            wp_enqueue_style('nxssnap-style', $path . '/js-css/snap.css', array(), NextScripts_SNAP_Version);
      
            if (stripos($screen->id, 'nxssnap')!==false || stripos($screen->id, '_page_nxs')!==false) {
                wp_enqueue_style('nxssnap-style-gl', $path . '/js-css/snap-gl.css', array(), NextScripts_SNAP_Version);
            }
      
            wp_enqueue_script('nxssnap-scripts3p', $path . '/js-css/js3p.js', array( 'jquery' ), NextScripts_SNAP_Version);
            wp_enqueue_style('nxssnap-style3p', $path . '/js-css/css3p.css', array(), NextScripts_SNAP_Version);
      
            wp_enqueue_script('tokenize', $path . '/js-css/jquery.tokenize.js', array( 'jquery' ), NextScripts_SNAP_Version);
            wp_enqueue_style('tokenize', $path . '/js-css/jquery.tokenize.css', array( ), NextScripts_SNAP_Version);
      
            wp_enqueue_script('nxsdatepicker', $path . '/js-css/datepicker.min.js', array( 'jquery' ), NextScripts_SNAP_Version);
            wp_enqueue_style('nxsdatepicker', $path . '/js-css/datepicker.min.css', array( ), NextScripts_SNAP_Version);
            
            //wp_enqueue_script( 'selectize', $path . '/js-css/selectize.min.js', array( 'jquery' ),  NextScripts_SNAP_Version);
            //wp_enqueue_style( 'selectize',  $path . '/js-css/selectize.css', array( ),  NextScripts_SNAP_Version );
      
            if (stripos($screen->id, 'toplevel_page_nxssnap')!==false  || stripos($screen->id, '_page_nxssnap-settings')!==false || stripos($screen->id, '_page_nxssnap-reposter')!==false) {
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_style('jquery-ui-datepicker', $path . '/js-css/jquery-ui.css', array( ), NextScripts_SNAP_Version);
            }
            wp_localize_script('nxssnap-scripts', 'MyAjax', array( 'ajaxurl' => nxs_get_admin_url('admin-ajax.php'), 'nxsnapWPnonce' => wp_create_nonce('nxsnapWPnonce'),));
        }
    }
}
if (!function_exists("jsPostToSNAP")) {
    function jsPostToSNAP()
    {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
        } else {
            return;
        }
        if ($screen->base == 'post' || stripos($screen->id, 'NextScripts_SNAP')!==false || stripos($screen->id, 'nxssnap')!==false || stripos($screen->id, '_page_nxs')!==false) {
            global $nxs_SNAP, $pagenow, $snap_curPageURL;
            $options = $nxs_SNAP->nxs_options;
            if (empty($options['fltrs'])) {
                $options['fltrs'] = array();
            }
            if (empty($options['fltrs']['nxs_post_type'])) {
                $options['fltrs']['nxs_post_type'] = array('post');
            }
            if ($screen->base =='post' && ((empty($options['fltrs']['nxs_ie_posttypes']) && !in_array($screen->post_type, $options['fltrs']['nxs_post_type'])) || (!empty($options['fltrs']['nxs_ie_posttypes']) && in_array($screen->post_type, $options['fltrs']['nxs_post_type'])))) {
                return;
            }
            //## Choose image scripts...
            // add_filter( 'tiny_mce_before_init', 'nxs_tiny_mce_before_init' ); ?> <script type="text/javascript">   
  function doLic(){ var lk = jQuery('#eLic').val(); jQuery("#enterKeyAPI2xLoadingImg").show();
    jQuery.post(ajaxurl,{lk:lk, action: 'nxsDoLic', id: 0, _wpnonce: jQuery('input#doLic_wpnonce').val()}, function(j){ 
      if (j.indexOf('OK')>-1) window.location = "<?php echo $snap_curPageURL; ?>"; else alert('<?php _e('Wrong key, please contact support', 'social-networks-auto-poster-facebook-twitter-g'); ?>');
    }, "html")
  }
  function nxs_doAJXPopup(act, nt, nid, msg, addprms, butText){  var data = {  action:'nxs_snap_aj', nxsact: act, nt:nt, id: 0, nid: nid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
     jQuery('#nxs_gPopupContent').html(msg+" <p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p>");
     //jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#wpbody-content', opacity: 0.6, positionStyle: 'fixed'});  
     jQuery.pgwModal({ target: '#nxs_gPopupCntWrap', title: 'Box', maxWidth: 800, closeOnBackgroundClick : false});

     jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted'; if (butText === undefined || butText=='undefined' || butText=='') butText = 'Close';
       jQuery('#nxs_gPopupContent').html('<p> ' + response + '</p>' +'<input type="button" onclick="jQuery.pgwModal(\'close\');" class="bClose" value="'+butText+'" />');
     });   
  }   
  
  //## Test post and Manual post.
  function testPost(nt, nid){  var data = {  action:'nxs_snap_aj', nxsact: 'testPost', nt:nt, id: 0, nid: nid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
     jQuery('#nxs_gPopupContent').html("<p>Sending update to "+nt+" ....</p>" + "<p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p>");
     //jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, positionStyle: 'fixed'});  
     jQuery.pgwModal({ target: '#nxs_gPopupCntWrap', title: 'Test Post', maxWidth: 800, closeOnBackgroundClick : false});
     jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
       jQuery('#nxs_gPopupContent').html('<p> ' + response + '</p>' +'<input type="button" class="bClose" value="OK" />'); jQuery( ".bClose" ).on( "click",function(e) { jQuery.pgwModal('close'); });      
     });      
       
  }
  
  function nxs_doManPost(obj){ obj.preventDefault; var nt = jQuery( this ).data('nt'); var ii = jQuery( this ).data('ii');  var pid = jQuery( this ).data('pid'); var ntn = jQuery( this ).data('ntname');
     var data = {  action:'nxs_snap_aj', nxsact: 'manPost', nt:nt, id: pid, nid: ii, et_load_builder_modules:1, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
     jQuery('#nxs_gPopupContent').html("<p>Sending update to "+ntn+" ....</p>" + "<p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p>");
     jQuery.pgwModal({ target: '#nxs_gPopupCntWrap', title: 'Post', maxWidth: 800, closeOnBackgroundClick : true});        
     jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted'; 
       jQuery('#nxs_gPopupContent').html('<p> ' + response + '</p>' +'<input type="button"  onclick="jQuery.pgwModal(\'close\');" class="bClose" value="Close" />'); 
     }); 
  }    
  
  
  function nxs_doAllManPost(obj){ 
      jQuery.pgwModal({ target: '#nxs_gPopupCntWrap', title: 'Post', maxWidth: 800, closeOnBackgroundClick : true});  jQuery('#nxs_gPopupContent').html('<p></p>');  
    
      jQuery('.nxs_acctcb:checked').each(function() { var nnm = jQuery(this).attr('name').replace(']','');  var res = nnm.split("["); var nt = res[0]; var ii = res[1]; var ntn = nt; var pid = jQuery('input#post_ID').val();
        var data = {  action:'nxs_snap_aj', nxsact: 'manPost', nt:nt, id: pid, nid: ii, et_load_builder_modules:1, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
        jQuery('#nxs_gPopupContent').append("<div id='nxsTempImg"+nt+ii+"'><p>Sending update to "+ntn+" ....</p>" + "<p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p></div>");        
        jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
          jQuery('#nxsTempImg'+nt+ii).html('<p> ' + response + '</p>');
        });         
      }); jQuery('#nxs_gPopupContent').append('<input type="button" class="bClose"  onclick="jQuery.pgwModal(\'close\');" value="Close" />');
  }
  
  function nxsShowOnlySelected(obj)  {
      jQuery('.nxs_acctcb:not(:checked)').parent().parent().parent().hide();
      jQuery.each(  jQuery('.nxs_box_inside'), function( i, val ) { var valX = jQuery(val); if(valX.children(':visible').length == 0) valX.parent().hide(); });      
  }
  function nxsShowOnlySelectedAll(obj)  {
      jQuery('.nxs_ntGroupWrapper').show();
      jQuery('.nxs_box').show();
  }
  function nxsShowOnlySelectedEd(obj)  {
      jQuery('.nxs_acctcb:not(:checked)').parent().parent().parent().parent().hide();
      jQuery.each(  jQuery('.nxs_box_inside'), function( i, val ) { var valX = jQuery(val); if(valX.children(':visible').length == 0) valX.parent().hide(); });      
  }
  function nxsShowOnlySelectedAllEd(obj)  {
      jQuery('.nxs_ntGroupWrapper').show();
      jQuery('.nxs_box').show();
  }
  
  function nxs_do2StepCodeCheck(nt, svc, code){ 
     jQuery.post(ajaxurl,{svc:svc, nt:nt, code:code, action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"nxsCptCheck", id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){
           jQuery('#nxsCPTResults').html(j);
     }, "html")
  }    
  jQuery(document).ready(function() {         
    jQuery( "#nxsShowOnlySelected" ).on( "click", nxsShowOnlySelected);      
    jQuery( "#nxsShowOnlySelectedAll" ).on( "click", nxsShowOnlySelectedAll);      
    jQuery( "#nxsShowOnlySelectedEd" ).on( "click", nxsShowOnlySelectedEd);
    jQuery( "#nxsShowOnlySelectedAllEd" ).on( "click", nxsShowOnlySelectedAllEd);
    
    
    /* JS Playground */
       
  });        
</script>
<?php
        }
    }
}
//## Init Functions
// if (!function_exists("nxs_tiny_mce_before_init")) { function nxs_tiny_mce_before_init($init) { $init['setup'] = "function(ed) {ed.on('NodeChange', function(e){nxs_updateGetImgsX(e);});}"; return $init; }}
if (!function_exists("nxs_admin_header")) {
    function nxs_admin_header()
    {
        wp_nonce_field('nxsSsPageWPN', 'nxsSsPageWPN_wpnonce');
    }
}
if (!function_exists("nxs_adminInitFunc")) {
    function nxs_adminInitFunc()
    {
        global $nxs_SNAP, $pagenow;
        $options = $nxs_SNAP->nxs_options;
        //## Quick Post Type
        $labels = array(
            'name'               => __('SNAP Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'singular_name'      => __('Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'add_new'            => __('Add Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'add_new_item'       => __('Add new Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'edit_item'          => __('Edit Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'new_item'           => __('New Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'view_item'          => __('View Quick Post', 'social-networks-auto-poster-facebook-twitter-g'),
            'search_items'       => __('Find Quick Posts', 'social-networks-auto-poster-facebook-twitter-g'),
            'not_found'          => __('Quick Post not found', 'social-networks-auto-poster-facebook-twitter-g'),
            'not_found_in_trash' => __('Quick Post not found in trash', 'social-networks-auto-poster-facebook-twitter-g'),
            'menu_name'          => __('Quick Posts', 'social-networks-auto-poster-facebook-twitter-g')
        );
        
        $args = array(
            'labels'    => $labels,
            'show_ui'   => false,
            'menu_icon' => 'dashicons-forms',
            'supports'  => array( 'title', 'author', 'thumbnail' ),
            'rewrite'            => array( 'slug' => 'nxs_qp' ),
            'capabilities' => array(
              'edit_post'          => 'edit_nxs_qp'
          //       'create_posts' => false, // Removes support for the "Add New" function
            )
            
        );
        register_post_type('nxs_qp', $args);
  
        if (function_exists('nxsDoLic_ajax')) {
            add_action('wp_ajax_nxsDoLic', 'nxsDoLic_ajax');
        }
        //## Javascript to Admin Panel
        add_action('admin_head', 'jsPostToSNAP');
  
        //## Add MEtaBox to Post Edit Page
        if (current_user_can("haveown_snap_accss") || current_user_can("see_snap_box") || current_user_can("manage_options")) {
            add_action('add_meta_boxes', array($nxs_SNAP, 'addCustomBoxes'));
            // if (!($pagenow=='options-general.php' && !empty($_GET['page']) && $_GET['page']=='NextScripts_SNAP.php')) add_action( 'admin_bar_menu', 'nxs_toolbar_link_to_mypage', 999 );
        }
    }
}


add_action('admin_footer', 'nxs_admin_footer_pp');
if (!function_exists("nxs_admin_footer_pp")) {
    function nxs_admin_footer_pp()
    {
        ?><div id="nxsPPHolder" style="display:none;"><div id="nxs_gPopupCntWrap" style="min-height: 300px;"><div id="nxs_gPopupContent"></div></div></div><?php
    }
}

//## Add Network Functions
if (!function_exists('nxs_addQTranslSel')) {
    function nxs_addQTranslSel($nt, $ii, $selLng)
    {
        if (function_exists('nxs_doSMAS6')) {
            return nxs_doSMAS6($nt, $ii, $selLng);
        } else {
            return '';
        }
    }
}
if (!function_exists('nxs_doShowHint')) {
    function nxs_doShowHint($t, $ex='', $wdth='79')
    {
        ?>
<div id="<?php echo $t; ?>Hint" class="nxs_FRMTHint" style="font-size: 11px; margin: 2px; margin-top: 0px; padding:7px; border: 1px solid #C0C0C0; width: <?php echo $wdth; ?>%; background: #fff; display: none;"><span class="nxs_hili">%TITLE%</span> - <?php _e('Inserts the Title of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%URL%</span> - <?php _e('Inserts the URL of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%SURL%</span> - <?php _e('Inserts the <b>shortened URL</b> of your post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%IMG%</span> - <?php _e('Inserts the featured image URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%EXCERPT%</span> - <?php _e('Inserts the excerpt of the post (processed)', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%RAWEXCERPT%</span> - <?php _e('Inserts the excerpt of the post (as typed)', 'social-networks-auto-poster-facebook-twitter-g'); ?>,  <span class="nxs_hili">%ANNOUNCE%</span> - <?php _e('Inserts the text till the &lt;!--more--&gt; tag or first N words of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%FULLTEXT%</span> - <?php _e('Inserts the processed body(text) of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%RAWTEXT%</span> - <?php _e('Inserts the body(text) of the post as typed', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%TAGS%</span> - <?php _e('Inserts post tags', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%CATS%</span> - <?php _e('Inserts post categories', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%HTAGS%</span> - <?php _e('Inserts post tags as hashtags', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%HCATS%</span> - <?php _e('Inserts post categories as hashtags', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%AUTHORNAME%</span> - <?php _e('Inserts the author\'s name', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%SITENAME%</span> - <?php _e('Inserts the the Blog/Site name', 'social-networks-auto-poster-facebook-twitter-g'); ?>. <?php echo $ex; ?></div>
<?php
    }
}
if (!function_exists('nxs_doSMAS')) {
    function nxs_doSMAS($nType, $typeii)
    {
        ?><div id="do<?php echo $typeii; ?>Div" class="clNewNTSets" style="margin-left: 10px; display:none; "><div style="font-size: 15px; text-align: center;"><div align="center"><a target="_blank" href="https://www.nextscripts.com/social-networks-autoposter-wordpress-plugin-pro/"><img src="<?php echo NXS_PLURL; ?>img/SNAP_Logo_2014.png" alt="SNAP Pro"></a></div><br/><br/>
<?php printf(__('You already have %s configured. This plugin supports only one %s account. <br/><br/>If you would like to add another %s account please consider getting <a target="_blank" href="https://www.nextscripts.com/social-networks-autoposter-wordpress-plugin-pro/">SNAP Pro Plugin for WordPress</a><br/><br/>SNAP Pro Plugin for WordPress allows adding unlimited number of %s accounts.', 'social-networks-auto-poster-facebook-twitter-g'), $nType, $nType, $nType, $nType); ?>
</div></div><?php
    }
}

// WP Image Functions
if (!function_exists('nxs_getImageSizes')) {
    function nxs_getImageSizes($size='')
    {
        global $_wp_additional_image_sizes;
        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();
        foreach ($get_intermediate_image_sizes as $_size) {
            if (in_array($_size, array( 'thumbnail', 'medium', 'large' ))) {
                $sizes[ $_size ]['width'] = get_option($_size . '_size_w');
                $sizes[ $_size ]['height'] = get_option($_size . '_size_h');
                $sizes[ $_size ]['crop'] = (bool) get_option($_size . '_crop');
            } elseif (isset($_wp_additional_image_sizes[ $_size ])) {
                $sizes[ $_size ] = array( 'width' => $_wp_additional_image_sizes[ $_size ]['width'], 'height' => $_wp_additional_image_sizes[ $_size ]['height'], 'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']);
            }
        }
        if ($size) {
            if (isset($sizes[ $size ])) {
                return $sizes[ $size ];
            } else {
                return false;
            }
        }
        return $sizes;
    }
}
if (!function_exists("nxs_getImgfrOpt")) {
    function nxs_getImgfrOpt($imgOpts, $defSize='')
    {
        if (!is_array($imgOpts)) {
            return $imgOpts;
        }// prr($imgOpts);
        if ($defSize!='' && isset($imgOpts[$defSize]) && trim($imgOpts[$defSize])!='') {
            return $imgOpts[$defSize];
        }
        if (isset($imgOpts['large']) && trim($imgOpts['large'])!='') {
            return $imgOpts['large'];
        }
        if (isset($imgOpts['original']) && trim($imgOpts['original'])!='') {
            return $imgOpts['original'];
        }
        if (isset($imgOpts['thumb']) && trim($imgOpts['thumb'])!='') {
            return $imgOpts['thumb'];
        }
        if (isset($imgOpts['medium']) && trim($imgOpts['medium'])!='') {
            return $imgOpts['medium'];
        }
    }
}
if (!function_exists('nxs_chckRmImage')) {
    function nxs_chckRmImage($url, $chType='head')
    {
        if (ini_get('allow_url_fopen')=='1' && @getimagesize($url)!==false) {
            return true;
        }
        $hdrsArr = nxs_getNXSHeaders();
        $nxsWPRemWhat = 'wp_remote_'.$chType;
        $url = str_replace(' ', '%20', $url);
        $rsp  = $nxsWPRemWhat($url, nxs_mkRemOptsArr($hdrsArr));
        if (is_nxs_error($rsp)) {
            nxsLogIt(array('type'=>'E', 'msg'=>'Could not get image ('.$url.'), will post without it', 'extInfo'=>serialize($rsp)));
            return false;
        }
        if (is_array($rsp) && ($rsp['response']['code']=='200' || ($rsp['response']['code']=='403' &&  $rsp['headers']['server']=='cloudflare-nginx'))) {
            return true;
        } else {
            if ($chType=='head') {
                return  nxs_chckRmImage($url, 'get');
            } else {
                nxsLogIt(array('type'=>'E', 'msg'=>'Could not get image ('.$url.'), will post without it', 'extInfo'=>serialize($rsp)));
                return false;
            }
        }
    }
}
if (!function_exists('nxs_getPostImage')) {
    function nxs_getPostImage($postID, $size='large', $def='')
    {
        $imgURL = '';
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $options = $nxs_SNAP->nxs_options;
        $options['sImg'] = (defined('NXSAPIVER') && NXSAPIVER == '2.15.11')?1:0;
        if (!isset($options['ogImgDef'])) {
            $options['ogImgDef'] = '';
        }
        if (empty($options['imgNoCheck']) || $options['imgNoCheck'] != '1') {
            $indx = rand(0, 2);
            $iTstArr = array('https://www.bing.com/s/a/hpc12.png','https://www.apple.com/global/elements/flags/16x16/usa_2x.png','https://s.yimg.com/rz/l/yahoo_en-US_f_p_142x37.png');
            $imgURL = $iTstArr[$indx];
            $res = nxs_chckRmImage($imgURL);
            $imgURL = '';
            if (!$res) {
                $options['imgNoCheck'] = '1';
            }
        }
        if ($options['sImg']==1) {
            return $options['useSSLCert'].'/logo2.png';
        }
        //## Featured Image from Specified Location
        if ((int)$postID>0 && isset($options['featImgLoc']) && $options['featImgLoc']!=='') {
            $afiLoc= get_post_meta($postID, $options['featImgLoc'], true);
            if (is_array($afiLoc) && $options['featImgLocArrPath']!='') {
                $cPath = $options['featImgLocArrPath'];
                while (strpos($cPath, '[')!==false) {
                    $arrIt = CutFromTo($cPath, '[', ']');
                    $arrIt = str_replace("'", "", str_replace('"', '', $arrIt));
                    $afiLoc = $afiLoc[$arrIt];
                    $cPath = substr($cPath, strpos($cPath, ']'));
                }
            }
            $imgURL = trim($options['featImgLocPrefix']).trim($afiLoc);
            if ($imgURL!='' && stripos($imgURL, 'http')===false) {
                $imgURL =  home_url().$imgURL;
            }
        }
        if ($imgURL!='' && empty($options['imgNoCheck']) && nxs_chckRmImage($imgURL)==false) {
            $imgURL = '';
        }
        if ($imgURL!='') {
            return $imgURL;
        }
        //## Featured Image
        if ($imgURL=='') {
            if ((int)$postID>0 && function_exists("get_post_thumbnail_id") && function_exists('has_post_thumbnail') && has_post_thumbnail($postID)) {
                $imgURL = wp_get_attachment_image_src(get_post_thumbnail_id($postID), $size);
                $imgURL = $imgURL[0];
                if ((trim($imgURL)!='')  && substr($imgURL, 0, 4)!='http') {
                    $imgURL = site_url($imgURL);
                }
            }
        }
        if ($imgURL!='' &&  empty($options['imgNoCheck']) && nxs_chckRmImage($imgURL)==false) {
            $imgURL = '';
        }
        if ($imgURL!='') {
            return $imgURL;
        }
        //## plugin/categories-images
        if ((int)$postID>0 && function_exists('z_taxonomy_image_url')) {
            $post_categories = wp_get_post_categories($postID);
            foreach ($post_categories as $c) {
                $cat = get_category($c);
                $imgURL = trim(z_taxonomy_image_url($cat->term_id));
                if ($imgURL!='') {
                    break;
                }
            }
            if ($imgURL!='' && substr($imgURL, 0, 4)!='http') {
                $stURL = site_url();
                if (substr($stURL, -1)=='/') {
                    $stURL = substr($stURL, 0, -1);
                }
                if ($imgURL!='') {
                    $imgURL = $stURL.$imgURL;
                }
            }
        }
        if ($imgURL!='' &&  empty($options['imgNoCheck']) && nxs_chckRmImage($imgURL)==false) {
            $imgURL = '';
        }
        if ($imgURL!='') {
            return $imgURL;
        }
        //## YAPB
        if ((int)$postID>0 && class_exists("YapbImage")) {
            $imgURLObj = YapbImage::getInstanceFromDb($postID);
            if (is_object($imgURLObj)) {
                $imgURL = $imgURLObj->uri;
            }
            $stURL = site_url();
            if (substr($stURL, -1)=='/') {
                $stURL = substr($stURL, 0, -1);
            }
            if ($imgURL!='') {
                $imgURL = $stURL.$imgURL;
            }
        }
        if ($imgURL!='' &&  empty($options['imgNoCheck']) && nxs_chckRmImage($imgURL)==false) {
            $imgURL = '';
        }
        if ($imgURL!='') {
            return $imgURL;
        }
        //## Find Images in Post
  if ((int)$postID>0 && $imgURL=='') {
      $post = get_post($postID);
      $imgsFromPost = nsFindImgsInPost($post, $options['useUnProc'] == '1');
      if (is_array($imgsFromPost) && count($imgsFromPost)>0) {
          $imgURL = $imgsFromPost[0];
      }
  } //echo "##".count($imgsFromPost); prr($imgsFromPost);
  if ($imgURL!='' &&  empty($options['imgNoCheck']) && nxs_chckRmImage($imgURL)==false) {
      $imgURL = '';
  }
        if ($imgURL!='') {
            return $imgURL;
        }
        //## Attachements
        if ((int)$postID>0 && $imgURL=='') {
            $attachments = get_posts(array('post_type' => 'attachment', 'posts_per_page' => -1, 'post_parent' => $postID));
            if (is_array($attachments) && count($attachments)>0 && is_object($attachments[0])) {
                $imgURL = wp_get_attachment_image_src($attachments[0]->ID, $size);
                $imgURL = $imgURL[0];
            }
        }
        if ($imgURL!='' &&  empty($options['imgNoCheck']) && nxs_chckRmImage($imgURL)==false) {
            $imgURL = '';
        }
        if ($imgURL!='') {
            return $imgURL;
        }
        //## Default
        if (trim($imgURL)=='' && trim($def)=='') {
            $imgURL = $options['ogImgDef'];
        }
        if (trim($imgURL)=='' && trim($def)!='') {
            $imgURL = $def;
        }

        return $imgURL;
    }
}
if (!function_exists('nsFindImgsInPost')) {
    function nsFindImgsInPost($post, $advImgFnd=false)
    {
        global $ShownAds;
        if (isset($ShownAds)) {
            $ShownAdsL = $ShownAds;
        }
        $postImgs = array();
        if (!is_object($post)) {
            return;
        }
        if ($advImgFnd) {
            $postCntEx = apply_filters('the_content', $post->post_excerpt);
        } else {
            $postCntEx = $post->post_excerpt;
        }
        if ($advImgFnd) {
            $postCnt = apply_filters('the_content', $post->post_content);
        } else {
            $postCnt = $post->post_content;
        }
        $postCnt = $postCntEx.$postCnt;
        //$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $postCnt, $matches ); if ($output === false){return false;}
        //$postCnt = str_replace("'",'"',$postCnt); $output = preg_match_all( '/src="([^"]*)"/', $postCnt, $matches ); if ($output === false){return false;}
        $postCnt = str_replace("'", '"', $postCnt);
        $output = preg_match_all('/< *img[^>]*src *= *["\']?([^"\']*)/i', $postCnt, $matches); // prr($matches);
        if ($output === false || $output == 0) {
            $vids = nsFindVidsInPost($post, $advImgFnd==false);
            if (count($vids)>0) {
                $postImgs[] = 'http://img.youtube.com/vi/'.$vids[0].'/0.jpg';
            } else {
                return false;
            }
        } else {
            foreach ($matches[1] as $match) {
                if (!preg_match('/^https?:\/\//', $match)) {
                    $match = site_url('/') . ltrim($match, '/');
                }
                $postImgs[] = $match;
            }
            if (isset($ShownAds)) {
                $ShownAds = $ShownAdsL;
            }
        }
        return $postImgs;
    }
}


if (!function_exists('nsFindAudioInPost')) {
    function nsFindAudioInPost($post, $raw=true)
    {  //### !!!   $raw=false Breaks ob_start() [ref.outcontrol]: Cannot use output buffering in output buffering display handlers - Investigate
        global $ShownAds;
        if (isset($ShownAds)) {
            $ShownAdsL = $ShownAds;
        }
        $postVids = array();
        if (is_object($post)) {
            if ($raw) {
                $postCnt = $post->post_content;
            } else {
                $postCnt = apply_filters('the_content', $post->post_content);
            }
        } else {
            $postCnt = $post;
        }
        $regex_pattern = "((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*\.(mp3|aac|m4a))";
        $output = preg_match_all($regex_pattern, $postCnt, $matches);
        if ($output === false) {
            return false;
        }
        foreach ($matches[0] as $match) {
            $postAu[] = $match;
        }
        if (is_array($postAu)) {
            $postAu = array_unique($postAu);
        }
        if (isset($ShownAds)) {
            $ShownAds = $ShownAdsL;
        }
        return $postAu;
    }
}
if (!function_exists('nsGetYTThumb')) {
    function nsGetYTThumb($yt)
    {
        $out = 'http://img.youtube.com/vi/'.$yt.'/maxresdefault.jpg';
        $response  = wp_remote_get($out);
        if (is_nxs_error($response) || $response['response']['code']!='200') {
            $out = 'http://img.youtube.com/vi/'.$yt.'/sddefault.jpg';
            $response  = wp_remote_get($out);
            if (is_nxs_error($response) || $response['response']['code']!='200') {
                $out = 'http://img.youtube.com/vi/'.$yt.'/0.jpg';
            }
        }
        return $out;
    }
}
if (!function_exists('nsFindVidsInPost')) {
    function nsFindVidsInPost($post, $raw=true)
    {  //### !!!  $raw=false ## Breaks ob_start() [ref.outcontrol]: Cannot use output buffering in output buffering display handlers - Investigate
        global $ShownAds;
        if (isset($ShownAds)) {
            $ShownAdsL = $ShownAds;
        }
        $postVids = array();
        if (is_object($post)) {
            if ($raw) {
                $postCnt = $post->post_content;
            } else {
                $postCnt = apply_filters('the_content', $post->post_content);
            }
        } else {
            $postCnt = $post;
        } //prr($postCnt);
        $postCnt = preg_replace('/youtube.com\/vi\/(.*)\/(.*).jpg/isU', "youtube.com/v/$1/", $postCnt);
        $output = preg_match_all('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?(#[a-z_.-][a-z0-9+\$_.-]*)?)*)@', $postCnt, $matches);
        if ($output === false) {
            return false;
        }
        foreach ($matches[0] as $match) {
            $output2 = preg_match_all('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"<>&?/ ]{11})%i', $match, $matches2);
            if ($output2 === false) {
                return false;
            }
            foreach ($matches2[1] as $match2) {
                $match2 = trim($match2);
                if (strlen($match2)==11) {
                    $postVids[] = $match2;
                }
            }
            $output3 = preg_match_all('/^https?:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $match, $matches3);
            if ($output3 === false) {
                return false;
            }
            foreach ($matches3[3] as $match3) {
                $match3 = trim($match3);
                if (strlen($match3)==8) {
                    $postVids[] = $match3;
                }
            }
            $output3 = preg_match_all('#https?://(player\.)?vimeo\.com(/video)?/(\d+)#i', $match, $matches3);
            if ($output3 === false) {
                return false;
            }
            foreach ($matches3[3] as $match3) {
                $match3 = trim($match3);
                if (strlen($match3)==8) {
                    $postVids[] = $match3;
                }
            }
        }
        $postVids = array_unique($postVids);
        if (isset($ShownAds)) {
            $ShownAds = $ShownAdsL;
        }
        return $postVids;
    }
}

//######################## Post Functions ########################//
//## Watch status change _publish
if (!function_exists("nxs_snapLogPublishTo")) {
    function nxs_snapLogPublishTo($new_status, $old_status, $post)
    {
        clean_post_cache($post->ID);
        $uid = 0;
        if ($post->post_type=='nxs_filter' || $post->post_type=='nxs_qp') {
            return;
        }
        $postUser = $post->post_author;
        $isItUserWhoCan = (!user_can($postUser, 'manage_options') && user_can($postUser, 'haveown_snap_accss'));
        if ($isItUserWhoCan) {
            $uid = $postUser;
        }
        if ($old_status!='publish' && $old_status!='trash' && $new_status == 'publish') {
            nxs_LogIt('BG', "*** ID: {$post->ID}, Type: {$post->post_type}", '', '', ' Status Changed: '."{$old_status}_to_{$new_status}".'. Autopost requested.', '', 'snap', $uid);
            nxs_snapPublishTo($post);
        }
    }
}
//##
//## Common Dialogs
if (!function_exists('nxs_showImgToUseDlg')) {
    function nxs_showImgToUseDlg($nt, $ii, $imgToUse, $hide=false)
    {
        ?>
   <div class="nxsPostEd_ElemWrap" id="altFormatIMG<?php echo $nt.$ii; ?>" style="<?php echo $hide?'display:none;':''; ?>"><div class="nxsPostEd_ElemLabel" style="display: inline;"><?php _e('Image to use:', 'social-networks-auto-poster-facebook-twitter-g') ?></div>
     <div class="nxsPostEd_Elem" style="display: inline;"><input type="checkbox" class="isAutoImg" <?php if ($imgToUse=='') {
            ?>checked="checked"<?php
        } ?>  id="isAutoImg-<?php echo $nt; ?><?php echo $ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][isAutoImg]" value="A"/> <?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?>
                  <?php if ($imgToUse!='') {
            ?> <a onclick="nxs_clPrvImgShow('<?php echo $nt; ?><?php echo $ii; ?>');return false;" href="#"><?php _e('Show all', 'social-networks-auto-poster-facebook-twitter-g'); ?></a><br/>  
                    <div class="nxs_prevImagesDiv" id="nxs_<?php echo $nt; ?><?php echo $ii; ?>_idivD"><img class="nxs_prevImages" src="<?php echo $imgToUse; ?>"><div style="display:block;" class="nxs_checkIcon"><div class="media-modal-icon"></div></div></div>
                  <?php
        } else {
            ?><?php
        } ?>
                    <div id="imgPrevList-<?php echo $nt; ?><?php echo $ii; ?>" class="nxs_imgPrevList" style="display: none;"></div>  
                    <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][imgToUse]" class="nxsEdElem" value="<?php echo $imgToUse ?>" id="imgToUse-<?php echo $nt; ?><?php echo $ii; ?>" data-ii="<?php echo $ii; ?>" data-nt="<?php echo $nt; ?>" /> 
     </div>
   </div> 
<?php
    }
}
if (!function_exists('nxs_showURLToUseDlg')) {
    function nxs_showURLToUseDlg($nt, $ii, $urlToUse)
    {
        ?>
 <div class="nxsPostEd_ElemWrap" style=""><div class="nxsPostEd_ElemLabel" style="display: inline;"><?php _e('URL to use:', 'social-networks-auto-poster-facebook-twitter-g') ?></div>
   <div class="nxsPostEd_Elem" style="display: inline;"><input type="checkbox" class="isAutoURL nxsEdElem" data-ii="<?php echo $ii; ?>" data-nt="<?php echo $nt; ?>" <?php if ($urlToUse=='') {
            ?>checked="checked"<?php
        } ?>  id="isAutoURL-<?php echo $nt; ?><?php echo $ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][isAutoURL]" value="A"/> <?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post URL or globally defined URL will be used', 'social-networks-auto-poster-facebook-twitter-g'); ?></i>                  
     <div class="nxs_prevURLDiv" <?php if (trim($urlToUse)=='') {
            ?> style="display:none;"<?php
        } ?> id="isAutoURLFld-<?php echo $nt; ?><?php echo $ii; ?>"><br/>
       &nbsp;&nbsp;&nbsp;<?php _e('URL:', 'social-networks-auto-poster-facebook-twitter-g') ?> <input style="width:60%;max-width: 610px;" class="nxsEdElem" data-ii="<?php echo $ii; ?>" data-nt="<?php echo $nt; ?>" type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][urlToUse]" value="<?php echo $urlToUse ?>" id="URLToUse-<?php echo $nt; ?><?php echo $ii; ?>" />                       
     </div>                  
 </div><div style="clear: both;"></div></div> 
<?php
    }
}

//## Log Functions
if (!function_exists('nxs_getnxsLog')) {
    function nxs_getnxsLog($prm='', $pg=0)
    {
        global $wpdb;
        $pg = $pg*300;
        $wh = array();
        $wh2 = '';
        $whOut = '';
        $uidQ = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? ' uid = '.get_current_user_id().' ' : '';
        if (!empty($prm) && is_array($prm)) {
            if (!empty($prm[1]) && $prm[1]==1) {
                $wh[] = 'flt = "snap"';
            } elseif (!empty($prm[0]) && $prm[0]==1) {
                $wh[] = '(flt = "snap" AND (type = "E" OR type="W"))';
            }
            if (!empty($prm[3]) && $prm[3]==1) {
                $wh[] = 'flt = "cron"';
            } elseif (!empty($prm[2]) && $prm[2]==1) {
                $wh[] = '(flt = "cron" AND (type = "E" OR type="W"))';
            }
            if (!empty($prm[4]) && $prm[4]==1) {
                $wh[] = 'flt = "sys"';
            }
            if (!empty($wh)) {
                $wh = ' ('.implode(' OR ', $wh).') ';
            }
            if (!empty($wh2)) {
                $wh2 = ' ('.implode(' OR ', $wh2).') ';
            }
            $whOut = ((!empty($wh) || !empty($wh2))?' WHERE ':'').(!empty($wh)?$wh:'').((!empty($wh) && !empty($wh2))?' AND ':'').$wh2;
            if (!empty($uidQ)) {
                $whOut .= (!empty($whOut)?' AND':' WHERE').$uidQ;
            }
            echo "| ".$whOut." |<br/>";
      
            /*


            if (!empty($prm[0]) && $prm[0]==1) $wh[] = 'flt = "snap"'; if (!empty($prm[1]) && $prm[1]==1) $wh[] = 'flt = "sys"'; if (!empty($prm[2]) && $prm[2]==1) $wh[] = 'flt = "cron"';
            if (!empty($prm[3]) && $prm[3]==1) $wh2[] = ' (type = "E" OR type="W") '; if (!empty($prm[4]) && $prm[4]==1) $wh2[] = ' (type = "BG" OR type="S" OR type="L" OR type="I") ';
            if (!empty($wh)) $wh = ' ('.implode(' OR ', $wh).') ';
            if (!empty($wh2)) $wh2 = ' ('.implode(' OR ', $wh2).') ';
            $whOut = ((!empty($wh) || !empty($wh2))?' WHERE ':'').$wh.((!empty($wh) && !empty($wh2))?' AND ':'').$wh2;
            echo "| ".$whOut." |<br/>"; */
        }
        $log = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "nxs_log ".$whOut." ORDER BY id DESC LIMIT ".$pg.",300", ARRAY_A);
        if (!is_array($log)) {
            return array();
        } else {
            return $log;
        }
    }
}

if (!function_exists('nxs_do_this_hourly')) {
    function nxs_do_this_hourly()
    {
        global $wpdb, $nxs_SNAP; // nxsLogIt('Hourly Event');
        if (isset($nxs_SNAP)) {
            $options = $nxs_SNAP->nxs_options;
        }
        if (!empty($options) && !empty($options['numLogRows'])) {
            $numLogRows = $options['numLogRows'];
        } else {
            $numLogRows = 1000;
        }
        $wpdb->query('UPDATE '.$wpdb->prefix . 'nxs_log SET flt="snap" WHERE flt IS NULL OR flt=""'); // prr($wpdb->last_query); prr($wpdb->last_error);
  $wpdb->query('DELETE FROM '.$wpdb->prefix . 'nxs_log WHERE flt="cron" AND id NOT IN (SELECT id FROM (SELECT id FROM `'.$wpdb->prefix . 'nxs_log` ORDER BY id DESC LIMIT 360) foo)'); // prr($wpdb->last_query); prr($wpdb->last_error);
  $wpdb->query('DELETE FROM '.$wpdb->prefix . 'nxs_log WHERE id <=(SELECT id FROM (SELECT id FROM `'.$wpdb->prefix . 'nxs_log` ORDER BY id DESC LIMIT 1 OFFSET '.$numLogRows.') foo)'); //  prr($wpdb->last_query); prr($wpdb->last_error);
  //## ErrorLog to Email
  if (isset($options['errNotifEmailCB']) && (int)$options['errNotifEmailCB'] == 1 && isset($options['errNotifEmail']) && trim($options['errNotifEmail']) != '') {
      $logToSend = maybe_unserialize(get_option('NSX_LogToEmail')); //  prr($logToSend);
    if (is_array($logToSend) && count($logToSend)>0) {
        $to = $options['errNotifEmail'];
        $subject = "SNAP Error Log for ".$_SERVER["SERVER_NAME"];
        $message = print_r($logToSend, true);
        $eml = get_bloginfo('admin_email');
        if (trim($eml)=='') {
            $eml = "snap-notify@".str_ireplace('www.', '', $_SERVER["SERVER_NAME"]);
        }
        $headers = "From: " . $eml . "\r\n";
        $headers .= "Reply-To: ". $eml . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $retval = wp_mail($to, $subject, $message, $headers);
        echo($to ."|". $subject."|". $message."|". $headers);
        nxsLogIt('Ready to Send');
        if ($retval == true) {
            $logMsg = array('type'=>'S', 'msg'=>'Log sent to email '.$options['errNotifEmail'], 'extInfo'=>count($logToSend).' records sent');
        } else {
            $logMsg = array('type'=>'ER', 'msg'=>'[FALIED] Log to email '.$options['errNotifEmail'], 'extInfo'=>count($logToSend).' records were NOT sent');
        }
        nxsLogIt($logMsg);
        delete_option("NSX_LogToEmail");
    }
  }
    }
}

if (!function_exists('nxsLogIt')) {
    function nxsLogIt($log)
    {
        global $wpdb;
        if (!is_array($log)) {
            $log = array('msg'=>$log);
        }
        if (empty($log['uid'])) {
            global $nxs_uid;
            $log['uid'] = !empty($nxs_uid)?$nxs_uid:get_current_user_id();
        }
        if (empty($log['act']) && !empty($log['type']) && $log['type']=='E') {
            $log['act'] = 'Error';
        }
        $logItem = array('date'=>date_i18n('Y-m-d H:i:s'), 'act'=>!empty($log['act'])?$log['act']:'SNAP', 'type'=>!empty($log['type'])?$log['type']:'L', 'nt'=>!empty($log['ntName'])?$log['ntName']:'',  'nttype'=>!empty($log['ntType'])?$log['ntType']:'',
    'flt'=>!empty($log['flt'])?$log['flt']:'snap', 'uid'=>!empty($log['uid'])?$log['uid']:'0', 'msg'=> strip_tags($log['msg']), 'extInfo'=>!empty($log['extInfo'])?$log['extInfo']:'0');
        $nxDB = $wpdb->insert($wpdb->prefix . "nxs_log", $logItem);// prr($wpdb->last_query); //prr($wpdb->last_error); //$wpdb->show_errors = true; $wpdb->print_error(); // $lid = $wpdb->insert_id; prr($lid,'IDD'); prr($wpdb->last_query); // $lid = $lid-$numLogRows;
        if (!empty($log['type']) && $log['type']=='E' && (isset($options['errNotifEmailCB']) && (int)$options['errNotifEmailCB'] == 1 && isset($options['errNotifEmail']) && trim($options['errNotifEmail']) != '')) {
            $logDB = maybe_unserialize(get_option('NSX_LogToEmail'));
            if (!is_array($logDB)) {
                $logDB = array();
            }
            $logDB[] = $logItem;
            delete_option("NSX_LogToEmail");
            add_option("NSX_LogToEmail", $logDB, '', 'no');
        }
    }
}
if (!function_exists('nxs_LogIt')) {
    function nxs_LogIt($type, $action, $ntName, $ntType='', $title='', $extInfo='', $flt='snap', $uid=0)
    {
        $log = array( 'act'=>$action, // [Action]
    'type'=>$type, // L - Log, W - Warning, E - Error, S - System, GR - Gray, BG - ??
    'ntName'=>$ntName, // Facebook [Facebook Page NX]
    'ntType'=>$ntType, // FB, TW, LI
    'flt'=>$flt, // snap, cron, sys,
    'uid'=>$uid, // UserID
    'msg'=>$title, // Plain text
    'extInfo'=>$extInfo, // HTML Text
  );
        nxsLogIt($log);
    }
}
if (!function_exists('nxs_addToLog')) {
    function nxs_addToLog($type, $action, $nt, $msg='')
    {
        nxs_LogIt($type, $action, $nt, '', $msg);
    }
}
if (!function_exists('nxs_addToLogN')) {
    function nxs_addToLogN($type, $action, $nt, $msg, $extInfo='', $flt='snap')
    {
        nxs_LogIt($type, $action, $nt, '', $msg, $extInfo, $flt);
    }
}


if (!function_exists("nxs_clLgo_ajax")) {
    function nxs_clLgo_ajax()
    {
        check_ajax_referer('nxsSsPageWPN');
        global $wpdb;
        $uidQ = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? ' WHERE uid = '.get_current_user_id().' ' : '';
        //update_option('NS_SNAutoPosterLog', '');
        $wpdb->query('DELETE FROM '.$wpdb->prefix . 'nxs_log'.$uidQ);
        echo "OK";
    }
}
if (!function_exists("nxs_rfLgo_ajax")) {
    function nxs_rfLgo_ajax()
    {
        check_ajax_referer('nxsSsPageWPN');
        echo "Y:";
        $prm = $_POST['prm'];
        $uidQ = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? ' WHERE uid = '.get_current_user_id().' ' : '';
        //$log = get_option('NS_SNAutoPosterLog'); $logInfo = maybe_unserialize(get_option('NS_SNAutoPosterLog'));
        $logInfo = nxs_getnxsLog($prm);
        if (is_array($logInfo)) {
            foreach ($logInfo as $logline) {
                if ($logline['type']=='E') {
                    $actSt = "color:#FF0000;";
                } elseif ($logline['type']=='M') {
                    $actSt = "color:#585858;";
                } elseif ($logline['type']=='BG') {
                    $actSt = "color:#008000; font-weight:bold;";
                } elseif ($logline['type']=='I') {
                    $actSt = "color:#0000FF;";
                } elseif ($logline['type']=='W') {
                    $actSt = "color:#DB7224;";
                } elseif ($logline['type']=='A') {
                    $actSt = "color:#580058;";
                } elseif ($logline['type']=='BI') {
                    $actSt = "color:#0000FF; font-weight:bold;";
                } elseif ($logline['type']=='GR') {
                    $actSt = "color:#008080;";
                } elseif ($logline['type']=='S') {
                    $actSt = "color:#005800; font-weight:bold;";
                } else {
                    $actSt = "color:#585858;";
                }
                if ($logline['type']=='E') {
                    $msgSt = "color:#FF0000;";
                } elseif ($logline['type']=='BG') {
                    $msgSt = "color:#008000; font-weight:bold;";
                } else {
                    $msgSt = "color:#585858;";
                }
                if ($logline['nt']!='') {
                    $ntInfo = ' ['.$logline['nt'].'] ';
                } else {
                    $ntInfo = '';
                }
                echo '<snap style="color:#008000">['.$logline['date'].']</snap> - <snap style="'.$actSt.'">['.$logline['act'].']</snap>'.$ntInfo.'-  <snap style="'.$msgSt.'">'.$logline['msg'].'</snap> '.$logline['extInfo'].'<br/>';
            }
        }
    }
}
//## Comments import
if (!function_exists("nxs_addToRI")) {
    function nxs_addToRI($postID)
    {
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $options = $nxs_SNAP->nxs_options;
        $riPosts = get_option('NS_SNriPosts');
        if (!is_array($riPosts)) {
            $riPosts = array();
        }
        $options['riHowManyPostsToTrack'] =  (int) $options['riHowManyPostsToTrack'];
        if ($options['riHowManyPostsToTrack']==0) {
            return;
        }
        array_unshift($riPosts, $postID);
        $riPosts = array_unique($riPosts);
        $riPosts = array_slice($riPosts, 0, $options['riHowManyPostsToTrack']);
        update_option('NS_SNriPosts', $riPosts, false);
    }
}

//## Language
if (!function_exists("nxs_doQTrans")) {
    function nxs_doQTrans($txt, $lng='')
    {
        if (!function_exists("qtrans_split") && !function_exists("qtranxf_split")) {
            return $txt;
        }
        $txt = str_ireplace('<3', '&lt;3', $txt);
        $txt = str_ireplace('<(', '&lt;(', $txt); //$txt = preg_replace('/\[caption\s[^\]]*\]/', '', $txt);
  $txt = preg_replace('/\[caption[\s]{0,}(.*?)\][\s]{0,}(<a[\s]{0,}.*?<\/a>)[\s]{0,}(.*?)\[\/caption\]/ims', '<p $1> $2 <snap class="wpimgcaption">$3</snap> </p>', $txt); // WP Image with Caption fix
  if (function_exists("qtrans_split") && strpos($txt, '<!--:')===false) {
      $tta = qtrans_split($txt);
      if ($lng!='') {
          return $tta[$lng];
      } else {
          return reset($tta);
      }
  }
        if (function_exists("qtranxf_split") && (strpos($txt, '<!--:')===false || strpos($txt, '[:')===false)) {
            $tta = qtranxf_split($txt);
            if ($lng!='') {
                return $tta[$lng];
            } else {
                return reset($tta);
            }
        }
    }
}
//## Format
if (!function_exists('nxs_makeURLParams')) {
    function nxs_makeURLParams($params)
    {
        $templ = html_entity_decode(nxs_getFromGlobalOpt('addURLParams'));
        if (empty($templ)) {
            return false;
        }
        if (preg_match('/%NTNAME%/', $templ)) {
            $templ = str_ireplace("%NTNAME%", urlencode(trim($params['NTNAME'])), $templ);
        }
        if (preg_match('/%NTCODE%/', $templ)) {
            $templ = str_ireplace("%NTCODE%", urlencode(trim($params['NTCODE'])), $templ);
        }
        if (preg_match('/%ACCNAME%/', $templ)) {
            $templ = str_ireplace("%ACCNAME%", urlencode(trim($params['ACCNAME'])), $templ);
        }
        if (preg_match('/%POSTID%/', $templ)) {
            $templ = str_ireplace("%POSTID%", urlencode(trim($params['POSTID'])), $templ);
        }
        if (preg_match('/%POSTTITLE%/', $templ)) {
            $post = get_post($params['POSTID']);
            if (is_object($post)) {
                $postName = $post->post_title;
                $templ = str_ireplace("%POSTTITLE%", urlencode(trim($postName)), $templ);
            }
        }
        if (preg_match('/%SITENAME%/', $templ)) {
            $siteTitle = urlencode(trim(htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES)));
            $templ = str_ireplace("%SITENAME%", $siteTitle, $templ);
        }
        return $templ;
    }
}
//## Settings Export
if (!function_exists("nxs_noR")) {
    function nxs_noR(&$item, &$key)
    {
        $item = is_string($item)?(str_replace("\r", "\n", str_replace("\n\r", "\n", str_replace("\r\n", "\n", $item)))):$item;
    }
}
if (!function_exists("nxs_getExpSettings_ajax")) {
    function nxs_getExpSettings_ajax()
    { /* check_ajax_referer('nsDN'); */  $filename = preg_replace('/[^a-z0-9\-\_\.]/i', '', $_POST['filename']);
        header("Cache-Control: ");
        header("Content-type: text/plain");
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $exp['u'] = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? $nxs_SNAP->nxs_acctsU : $nxs_SNAP->nxs_accts;
        if (!empty($_POST['chN'])) {
            $arr = explode(',', $_POST['chN']);
            if (!empty($arr)) {
                $outArr = array();
                foreach ($exp['u'] as $ntN=>$nt) {
                    foreach ($nt as $ii=>$dt) {
                        if (in_array($ntN.'-'.$ii, $arr)) {
                            $outArr[$ntN][$ii] = $dt;
                        }
                    }
                }
                $exp['u'] = $outArr;
            }
        }
        if (current_user_can('manage_options')) {
            $exp['o'] = $nxs_SNAP->nxs_options;
        }
        array_walk_recursive($exp, "nxs_noR");
        $ser = serialize($exp);
        echo $ser;
        die();
    }
}
//## OG:Tags
function nxs_end_flush_ob()
{
    if (!empty($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "ChXrome") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Google (") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "LinkedInBot") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "XING-contenttabreceiver") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Java/1.7.0_45") !== false)) {
        if (!is_admin()) {
            @ob_end_flush();
        }
    }
}
    
function nxs_ogtgCallback($content)
{
    global $post, $nxs_SNAP;
    if (stripos($content, 'og:title')!==false) {
        $ogOut = "\r\n";
    } else {
        if (!isset($nxs_SNAP)) {
            $options = get_option('NS_SNAutoPoster');
        } else {
            $options = $nxs_SNAP->nxs_options;
        }
        $ogimgs = array();
        $accs = $nxs_SNAP->nxs_accts;  //$content = json_encode($accs['fb']).$content;
        if (!empty($post) && !is_object($post) && (int)$post>0) {
            $post = get_post($post);
        }
        if (empty($options['advFindOGImg'])) {
            $options['advFindOGImg'] = 0;
        }
    
    
        if (empty($post->post_title)) {
            $title = preg_match('/<title>(.*)<\/title>/', $content, $title_matches);
            if ($title !== false && count($title_matches) == 2) {
                $ogT ='<meta property="og:title" content="' . $title_matches[1] . '" />'."\r\n";
            } else {
                if (is_home() || is_front_page()) {
                    $ogT = get_bloginfo('name');
                } else {
                    $ogT = get_the_title();
                }
                $ogT =  '<meta property="og:title" content="' . esc_attr(apply_filters('nxsog_title', $ogT)) . '" />'."\r\n";
            }
        } else {
            $ogT =  '<meta property="og:title" content="' . esc_attr(apply_filters('nxsog_title', $post->post_title)) . '" />'."\r\n";
        }
        $prcRes = preg_match('/<meta name="description" content="(.*)"/', $content, $description_matches);
        if ($prcRes !== false && count($description_matches) == 2) {
            $ogD = '<meta property="og:description" content="' . $description_matches[1] . '" />'."\r\n";
        }
        {
      if (!empty($post) && is_object($post) && is_singular()) {
          if (has_excerpt($post->ID)) {
              $ogD=strip_tags(nxs_snapCleanHTML($post->post_excerpt));
          } else {
              $ogD= str_replace("  ", ' ', str_replace("\r\n", ' ', trim(substr(strip_tags(nxs_snapCleanHTML(strip_shortcodes($post->post_content))), 0, 200))));
          }
      } else {
          $ogD = get_bloginfo('description');
      }  $ogD = preg_replace('/\r\n|\r|\n/m', '', $ogD);
      $ogD = '<meta property="og:description" content="'.esc_attr(apply_filters('nxsog_desc', $ogD)).'" />'."\r\n";
    }
        $ogSN = '<meta property="og:site_name" content="'.get_bloginfo('name').'" />'."\r\n";
        $ogLoc = strtolower(esc_attr(get_locale()));
        if (strlen($ogLoc)==2) {
            $ogLoc .= "_".strtoupper($ogLoc);
        }
        $ogLoc = '<meta property="og:locale" content="'.$ogLoc.'" />'."\r\n";
        $iss = is_home();
    
        if (!empty($accs['fb']) && count($accs['fb'])>0) {
            $kx = array_slice($accs['fb'], 0, 1);
            $k = array_shift($kx);
            $ogappID = (!empty($k) && !empty($k['appKey']))  ? ('<meta property="fb:app_id" content="'.nxs_gak($k['appKey']).'"/>'."\r\n"):'';
            $fbURL = (!empty($k) && !empty($k['pgID']))  ? ('https://www.facebook.com/'.$k['pgID'].'/'):'';
        } else {
            $ogappID = '';
            $fbURL = '';
        }
        if (empty($options['ogAuthorFB']) && !empty($fbURL)) {
            $options['ogAuthorFB'] = $fbURL;
        }
        if (!empty($options['ogAuthorFB'])) {
            $author = "<meta property='article:author' content='".$options['ogAuthorFB']."' /><meta property='article:publisher' content='".$options['ogAuthorFB']."' />";
        }
        $ogType = is_singular()?'article':'website';
        if (empty($vidsFromPost)) {
            $ogType = '<meta property="og:type" content="'.esc_attr(apply_filters('nxsog_type', $ogType)).'" />'."\r\n";
        }
        if (is_home() || is_front_page()) {
            $ogUrl = get_bloginfo('url');
        } else {
            $ogUrl = 'http' . (is_ssl() ? 's' : '') . "://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        $ogUrl = '<meta property="og:url" content="'.esc_url(apply_filters('nxsog_url', $ogUrl)) . '" />' . "\r\n";
  
        if (!is_home()) { /*
      $vidsFromPost = nsFindVidsInPost($post); if ($vidsFromPost !== false && is_singular()) {  echo '<meta property="og:video" content="http://www.youtube.com/v/'.$vidsFromPost[0].'" />'."\n";
      echo '<meta property="og:video:type" content="application/x-shockwave-flash" />'."\n";
      echo '<meta property="og:video:width" content="480" />'."\n";
      echo '<meta property="og:video:height" content="360" />'."\n";
      echo '<meta property="og:image" content="http://i2.ytimg.com/vi/'.$vidsFromPost[0].'/mqdefault.jpg" />'."\n";
      echo '<meta property="og:type" content="video" />'."\n";
    } */
            if (is_object($post)) {
                $imgURL = nxs_getPostImage($post->ID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full', $options['ogImgDef']);
                if (!empty($imgURL)) {
                    $ogimgs[] = $imgURL;
                }
                $imgsFromPost = nsFindImgsInPost($post, (int)$options['advFindOGImg']==1);
                if ($imgsFromPost !== false && is_singular() && is_array($ogimgs) && is_array($imgsFromPost)) {
                    $ogimgs = array_merge($ogimgs, $imgsFromPost);
                }
            }
        }
        //## Add default image to the endof the array
        if (count($ogimgs)<1 && isset($options['ogImgDef']) && $options['ogImgDef']!='') {
            $ogimgs[] = $options['ogImgDef'];
        }
        //## Output og:image tags
        $ogImgsOut = '';
        if (!empty($ogimgs) && is_array($ogimgs)) {
            foreach ($ogimgs as $ogimage) {
                $ogImgsOut .= '<meta property="og:image" content="'.esc_url(apply_filters('ns_ogimage', $ogimage)).'" />'."\r\n";
            }
        }
        $ogOut  = "\r\n".$ogSN.$ogT.$ogD.$ogType.$ogUrl.$ogLoc.$ogImgsOut.$ogappID.$author;
    }
    $content = str_ireplace('<!-- ## NXSOGTAGS ## -->', $ogOut, $content);
    return $content;
}
function nxs_addOGTagsPreHolder()
{
    echo "<!-- ## NXS/OG ## --><!-- ## NXSOGTAGS ## --><!-- ## NXS/OG ## -->\n\r";
}


//## Post from "Quick Post Form"
if (!function_exists('nxs_doNewNPPost')) {
    function nxs_doNewNPPost($networks)
    {
        global $nxs_snapAvNts, $wpdb;
        $postResults = '';
        $currTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
        if (!empty($_POST['ddt'])) {
            $ddt = strtotime(str_replace(',', '', $_POST['ddt']));
            $isSch = $ddt>$currTime;
        } else {
            $isSch = false;
        }
        if (get_magic_quotes_gpc() || $_POST['nxs_mqTest']=="\'") {
            $_POST['mText'] = stripslashes($_POST['mText']);
            $_POST['mTitle'] = stripslashes($_POST['mTitle']);
        }
        $ttl = nsTrnc(!empty($_POST['mTitle'])?$_POST['mTitle']:$_POST['mText'], 200);
        if (empty($ttl)) {
            $ttl = 'Quick post ['.date('F j, Y, g:i a', time()+(get_option('gmt_offset') * HOUR_IN_SECONDS)).']';
        } //## Format title for saving info ....
  { //###### Make it savable as option. Put If () here.
    //## Insert Post
    $user_id = get_current_user_id();
    $my_post = array( 'post_title' => $ttl, 'post_content' => $_POST['mText'], 'post_status' => 'publish', 'post_author' => $user_id, 'post_type' => 'nxs_qp', 'post_category' => array(  ) );
    if (!empty($_POST['qpid'])) {
        $my_post['ID'] = $_POST['qpid'];
        wp_update_post($my_post);
    } else {
        $_POST['qpid'] = wp_insert_post($my_post);
    }
    //## Insert Post meta
    if (!empty($_POST['qpid'])) {
        $metaArrEx = get_post_meta($_POST['qpid'], '_nxs_snap_data', true);
        $metaArr = array('posts'=>array(), 'postType'=>$_POST['mType'], 'imgURL'=>$_POST['mImg'], 'linkURL'=>$_POST['mLink']);
        if (!empty($metaArrEx['posts'])) {
            $metaArr['posts'] = $metaArrEx['posts'];
        }
        update_post_meta($_POST['qpid'], '_nxs_snap_data', $metaArr);
    }
  }
        if (!empty($_POST['mNts']) && is_array($_POST['mNts'])) {
            nxs_addToLogN('S', '-=== New Quick Form Post '.($isSch?'Schedulled for '.$_POST['ddt']:'requested').' ===-', 'Form', count($_POST['mNts']).' Networks', print_r($_POST['mNts'], true));
            $message = array('title'=>'', 'text'=>'', 'siteName'=>'', 'url'=>'', 'imageURL'=>'', 'videoURL'=>'', 'tags'=>'', 'urlDescr'=>'', 'urlTitle'=>'');
            if ($isSch) {
                $dbItem = array('datecreated'=>date_i18n('Y-m-d H:i:s'), 'type'=>'F', 'timetorun'=> date_i18n('Y-m-d H:i:s', $ddt), 'postid'=>$_POST['qpid'], 'extInfo'=>serialize($_POST), 'descr'=>$ttl, 'uid'=>get_current_user_id()); //prr($dbItem);
                $nxDB = $wpdb->insert($wpdb->prefix . "nxs_query", $dbItem);
                $lid = $wpdb->insert_id;
                echo '<br/>Post ID: '.$lid.'. Schedulled for '.$_POST['ddt'];
            } else {
                echo nxs_postFromForm($_POST, $networks);
            }
        }
    }
}
if (!function_exists('nxs_postFromForm')) {
    function nxs_postFromForm($post, $networks, $isSilent=false)
    {
        global $nxs_snapAvNts;
        $postResults = '';
        if (!empty($post['mNts']) && is_array($post['mNts'])) {
            nxs_addToLogN('S', '-=== New Qiuck Post ===-', 'Form', count($post['mNts']).' Networks', print_r($post['mNts'], true)); //.'<br/>|<br/><pre>'.print_r($post, true).'</pre>');
            $message = array('title'=>'', 'text'=>'', 'siteName'=>'', 'url'=>'', 'imageURL'=>'', 'videoURL'=>'', 'tags'=>'', 'urlDescr'=>'', 'urlTitle'=>'', 'urlCaption'=>'');
            if (get_magic_quotes_gpc() || $post['nxs_mqTest']=="\'") {
                $post['mText'] = stripslashes($post['mText']);
                $post['mTitle'] = stripslashes($post['mTitle']);
            }
            $message['pText'] = nxs_doSpin($post['mText']);
            $message['pTitle'] = nxs_doSpin($post['mTitle']);
            //## Get URL info
            if (!empty($post['mLink']) && substr($post['mLink'], 0, 4)=='http') {
                $message['url'] = $post['mLink'];
                $flds = array('id'=>$message['url'], 'scrape'=>'true');
                $response =  wp_remote_post('http://graph.facebook.com', array('body' => $flds));
                if (is_wp_error($response)) {
                    $badOut['Error'] = print_r($response, true)." - ERROR";
                } else {
                    $response = json_decode($response['body'], true);
                    if (!empty($response['description'])) {
                        $message['urlDescr'] = $response['description'];
                    }
                    if (!empty($response['title'])) {
                        $message['urlTitle'] =  $response['title'];
                    }
                    if (!empty($response['site_name'])) {
                        $message['siteName'] = $response['site_name'];
                    }
                    if (!empty($response['image'][0]['url'])) {
                        $message['imageURL'] = $response['image'][0]['url'];
                    }
                }
            }
            if (!empty($post['mImg']) && substr($post['mImg'], 0, 4)=='http') {
                $message['imageURL'] = $post['mImg'];
            }
            $nts = array();
            $postResultsArr = array('date'=> time(), 'errors'=>0, 'ok'=>0, 'data'=>array());
          
            foreach ($post['mNts'] as $ntC) {
                $ntA = explode('--', $ntC);
                $ntOpts = $networks[$ntA[0]][$ntA[1]];
                $nts[] = $ntA[0].$ntA[1]; //  nxs_addToLogN('L', 'IN', $logNT, 'Go ', print_r($ntA, true));
                if (!empty($ntOpts) && is_array($ntOpts)) {
                    $logNT = $ntA[0];
                    $clName = 'nxs_class_SNAP_'.strtoupper($logNT);
                    $logNT = '<span style="color:#800000">'.strtoupper($logNT).'</span> - '.$ntOpts['nName'];    //    prr($ntOpts);
                    $message['pText'] = nxs_doSpin($post['mText']);
                    $message['pTitle'] = nxs_doSpin($post['mTitle']);
                    $ntOpts['postType'] = $post['mType'];
                    $ntToPost = new $clName();
                    $ret = $ntToPost->doPostToNT($ntOpts, $message);   //   nxs_addToLogN('L', 'OUT', $logNT, 'Ex ', print_r($ntA, true));
          if (!is_array($ret) || empty($ret['isPosted']) || $ret['isPosted']!='1') { //## Error
             nxs_addToLogN('E', 'Error', $logNT, '-=ERROR=- '.print_r($ret, true), '');
              $postResults .= $logNT ." - Error (Please see log)<br/>";
              $postResultsArr['errors']++;
          } else {  // ## All Good - log it.
             if (!empty($ret['postURL'])) {
                 $extInfo = '<a href="'.$ret['postURL'].'" target="_blank">Post Link</a>';
             } //$extInfo .= ' | '.print_r($message, true).' | '.print_r($ntOpts, true);
             nxs_addToLogN('S', 'Posted', $logNT, 'OK - Message Posted ', $extInfo);
              $postResults .= $logNT ." - OK - ".$extInfo."<br/>";
              $postResultsArr['data'][] = array('nName'=>$logNT, 'link'=>(!empty($ret['postURL']))?$ret['postURL']:'');
              $postResultsArr['ok']++;
          }
                }
            }
            $out = "Done. Results:<br/> ".$postResults;
    
            //## Save AutoPost Info to Saved QP
            if (!empty($post['qpid'])) {
                $metaArr = get_post_meta($post['qpid'], '_nxs_snap_data', true);
                $metaArr['nts'] = $nts;
                $metaArr['posts'][] = $postResultsArr;
                update_post_meta($post['qpid'], '_nxs_snap_data', $metaArr);
            }
            if (!$isSilent) {
                echo $out;
            } else {
                return $out;
            }
        }
    }
}

if (!function_exists('nxs_doNewBPPost')) {
    function nxs_doNewBPPost($aid, $user_id, $content)
    { //prr($user_id); prr($content); die();
        global $nxs_SNAP, $wpdb;
        $postResults = '';
        $currTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
        $content = stripslashes($content);
        $pt = 't';
        $networks = array();
        $isSilent = false;
        if (empty($nxs_SNAP)) {
            $nxs_SNAP = new nxs_SNAP();
        }
        foreach ($nxs_SNAP->nxs_acctsU as $act=>$acc) {
            foreach ($acc as $ii=>$ac) {
                if (is_array($ac)&&is_array($ac['fltrs'])&&!empty($ac['fltrs']['nxs_post_type'])&&is_array($ac['fltrs']['nxs_post_type'])&&in_array('BuddyPress_Activity', $ac['fltrs']['nxs_post_type'])) {
                    $networks[] = $act.'--'.$ii;
                }
            }
        }
        if (!empty($networks) && is_array($networks)) {
            nxs_addToLogN('S', '-=== New BuddyPress Post ===-', 'Form', count($networks).' Networks', print_r($networks, true));
            $message = array('title'=>'', 'text'=>'', 'siteName'=>'', 'url'=>'', 'imageURL'=>'', 'videoURL'=>'', 'tags'=>'', 'urlDescr'=>'', 'urlTitle'=>'', 'urlCaption'=>'');
            //## Check if Extended Post
            if (!empty($_POST['data']) && !empty($_POST['data']['bpfb_link_url'])) {
                $message['url'] =  $_POST['data']['bpfb_link_url'];
                $pt = 'a';
                $content = $_POST['content']."\r\n".$message['url'];
                if (!empty($_POST['data']['bpfb_link_body'])) {
                    $message['urlDescr'] = $_POST['data']['bpfb_link_body'];
                }
                if (!empty($_POST['data']['bpfb_link_title'])) {
                    $message['urlTitle'] =  $_POST['data']['bpfb_link_title'];
                }
                if (!empty($_POST['data']['bpfb_link_image'])) {
                    $message['imageURL'] =  $_POST['data']['bpfb_link_image'];
                }
            }
            if (!empty($_POST['data']) && !empty($_POST['data']['bpfb_photos'])) {
                $content = $_POST['content'];
                $message['imageURL'] =  $_POST['data']['bpfb_photos'][0];
                $pt = 'i';
            }
            $nts = array();
            $postResultsArr = array('date'=> time(), 'errors'=>0, 'ok'=>0, 'data'=>array());
            foreach ($networks as $ntC) {
                $ntA = explode('--', $ntC);
                $ntOpts = $nxs_SNAP->nxs_acctsU[$ntA[0]][$ntA[1]];
                $nts[] = $ntA[0].$ntA[1];
                if (!empty($ntOpts) && is_array($ntOpts)) {
                    $logNT = $ntA[0];
                    $clName = 'nxs_class_SNAP_'.strtoupper($logNT);
                    $logNT = '<span style="color:#800000">'.strtoupper($logNT).'</span> - '.$ntOpts['nName'];
                    $message['pText'] = nxs_doSpin($content); // prr($message);
                    $ntOpts['postType'] = $pt;
                    $ntToPost = new $clName();
                    $ret = $ntToPost->doPostToNT($ntOpts, $message);
                    if (!is_array($ret) || empty($ret['isPosted']) || $ret['isPosted']!='1') { //## Error
                        nxs_addToLogN('E', 'Error', $logNT, '-=ERROR=- '.print_r($ret, true), '');
                        $postResults .= $logNT ." - Error (Please see log)<br/>";
                        $postResultsArr['errors']++;
                    } else {  // ## All Good - log it.
             if (!empty($ret['postURL'])) {
                 $extInfo = '<a href="'.$ret['postURL'].'" target="_blank">Post Link</a>';
             } //$extInfo .= ' | '.print_r($message, true).' | '.print_r($ntOpts, true);
             nxs_addToLogN('S', 'Posted', $logNT, 'OK - Message Posted ', $extInfo);
                        $postResults .= $logNT ." - OK - ".$extInfo."<br/>";
                        $postResultsArr['data'][] = array('nName'=>$logNT, 'link'=>(!empty($ret['postURL']))?$ret['postURL']:'');
                        $postResultsArr['ok']++;
                    }
                }
            }
            $out = "Done. Results:<br/> ".$postResults;
            if (!$isSilent) {
                echo $out;
            } else {
                return $out;
            }
        }
    }
}
add_action('bp_activity_posted_update', 'nxs_doNewBPPost', 10, 3);

if (!class_exists('nxs_snapPostResults')) {
    class nxs_snapPostResults
    {
        public $info = array();
        public $summary = '';
        public $details = '';
        public function __construct($info)
        {
            foreach ($info as $inf) {
                $this->createSummary($inf);
                $this->createDetailedList($inf);
            }
        }
        public function createSummary($info)
        {
            $out = __('Posted', 'social-networks-auto-poster-facebook-twitter-g').': '. date('F j, Y, g:i a', $info['date']+(get_option('gmt_offset') * HOUR_IN_SECONDS)).' - ';
            if (!empty($info['ok'])) {
                $out .= $info['ok'].' '.__('accounts - OK', 'social-networks-auto-poster-facebook-twitter-g').'; ';
            }
            if (!empty($info['errors'])) {
                $out .= $info['errors'].' '.__('Errors', 'social-networks-auto-poster-facebook-twitter-g').'; ';
            }
            $this->summary .= $out.'<br/>';
        }
        public function createDetailedList($info)
        {
            $dt = date('F j, Y, g:i a', $info['date']+(get_option('gmt_offset') * HOUR_IN_SECONDS));
            $info = $info['data'];
            $out = '';
            foreach ($info as $inf) {
                $out .= '['.$dt.'] '.$inf['nName'].' - '.'<a href="'.$inf['link'].'" target="_blank">Post Link</a><br/>';
            }
            $this->details .= $out;
        }
    }
}
//## New Post Form
if (!function_exists('nxs_showNewPostForm')) {
    function nxs_showNewPostForm($networks, $air = true)
    {
        global $nxs_snapAvNts; ?> 
  <div id="nxsNewSNPost" style="width: 880px;">
  
    <div><h3>New Post to the Configured Social Networks</h3></div>
    
    <div class="nxsNPRow" id="nxsQPNewSave" style="display: none; font-size: 14px;"><span>Editing Post ID:</span>&nbsp;<span id="nxsQPID"></span>&nbsp;&nbsp;<input style="width: 100px;" id="nxs_QPSaveButton" type="button" onclick="nxs_doSaveQP();" value="Save Post"></div> <br/>
    
    <div class="nxsNPRow"><label class="nxsNPLabel">Message:</label><span style="float: right;" id="nxsQPTWcnt"> </span> <br/><textarea id="nxsNPText" name="textarea" cols="90" rows="8"></textarea></div>
    <div class="nxsNPRow"><label class="nxsNPLabel">Title (Will be used where possible):</label><br/><input id="nxsNPTitle" type="text" size="80"></div>
    
    <div class="nxsNPRow"><label class="nxsNPLabel">Post Type:</label><br/><input type="radio" name="nxsNPType"  id="nxsNPTypeT" value="T" checked="checked" /><label class="nxsNPRowSm">Text Post</label><br/>
    
    <br/><input type="radio" name="nxsNPType"  id="nxsNPTypeL" value="A"><label class="nxsNPRowSm">Link Post</label>
      <div class="nxsNPRowSm"><label class="nxsNPLabel">URL (Will be attached where possible, text post will be made where not):</label><br/><input id="nxsNPLink" onfocus="jQuery('#nxsNPTypeL').attr('checked', 'checked')" type="text" size="80" /></div>
    <br/><input type="radio" name="nxsNPType" id="nxsNPTypeI" value="I"><label class="nxsNPRowSm">Image Post</label>
      <div class="nxsNPRowSm"><label class="nxsNPLabel">Image URL (Will be used where possible, text post will be made where not):</label><br/><input id="nxsNPImg" onfocus="jQuery('#nxsNPTypeI').attr('checked', 'checked')" type="text" size="80" /></div>
    </div>
    <div class="nxsNPRow">
      
      <div class="nxsNPRightZ" style="float: none;">
     
    <div class="nxsNPRow">
    <div style="float: right; font-size: 12px;" >
      <a href="#" onclick="jQuery('.nxsNPDoChb').attr('checked','checked'); return false;"><?php  _e('Check All', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;<a href="#" onclick="jQuery('.nxsNPDoChb').removeAttr('checked'); return false;"><?php _e('Uncheck All', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>
    </div>
    <label class="nxsNPLabel">Networks:</label><br/> 
    <div class="nxsNPRow" id="nxsNPRowNetworks" style="font-size: 12px;">
      <?php echo nxs_showNetworksList($networks); ?> 
    </div>
   
  </div>   
  </div>
      
  <div class="nxsNPLeftZ" style=" style="float: none;" display: inline-block;"> <div class="misc-pub-section curtime misc-pub-curtime" style="width:100%">
  
    <span id="timestamp">Publish <b><select onchange="if (jQuery(this).val() == 'I') { jQuery('#pf_timestampdiv').hide(); jQuery('#pf_postButton').val('Post'); } else { jQuery('#pf_timestampdiv').show(); jQuery('#pf_postButton').val('Schedule'); }" name="pfPostTimeSel"><option value="I">Immediately</option><option value="S">Schedule to...</option></select></b></span>    
    <div style="width: 400px; display: block;">
    <div id="pf_timestampdiv" class="hide-if-js"><div class="timestamp-wrap"><select id="nxs_mm" name="nxs_mm">
            <option value="1" <?php if (date_i18n('n')=='1') {
            echo 'selected="selected"';
        } ?>>01-Jan</option> <option value="2" <?php if (date_i18n('n')=='2') {
            echo 'selected="selected"';
        } ?>>02-Feb</option> 
            <option value="3" <?php if (date_i18n('n')=='3') {
            echo 'selected="selected"';
        } ?>>03-Mar</option> <option value="4" <?php if (date_i18n('n')=='4') {
            echo 'selected="selected"';
        } ?>>04-Apr</option> 
            <option value="5" <?php if (date_i18n('n')=='5') {
            echo 'selected="selected"';
        } ?>>05-May</option> <option value="6" <?php if (date_i18n('n')=='6') {
            echo 'selected="selected"';
        } ?>>06-Jun</option> 
            <option value="7" <?php if (date_i18n('n')=='7') {
            echo 'selected="selected"';
        } ?>>07-Jul</option> <option value="8" <?php if (date_i18n('n')=='8') {
            echo 'selected="selected"';
        } ?>>08-Aug</option> 
            <option value="9" <?php if (date_i18n('n')=='9') {
            echo 'selected="selected"';
        } ?>>09-Sep</option> <option value="10" <?php if (date_i18n('n')=='10') {
            echo 'selected="selected"';
        } ?>>10-Oct</option>
            <option value="11" <?php if (date_i18n('n')=='11') {
            echo 'selected="selected"';
        } ?>>11-Nov</option> <option value="12" <?php if (date_i18n('n')=='12') {
            echo 'selected="selected"';
        } ?>>12-Dec</option> </select>
            
<input type="text" id="nxs_jj" name="nxs_jj" value="<?php echo date_i18n('d'); ?>" size="2" maxlength="2" autocomplete="off">, <input type="text" id="nxs_aa" name="nxs_aa" value="<?php echo date_i18n('Y'); ?>" size="4" maxlength="4" autocomplete="off"> @ <input type="text" id="nxs_hh" name="nxs_hh" value="<?php echo date_i18n('H'); ?>" size="2" maxlength="2" autocomplete="off"> : <input type="text" id="nxs_mn" name="nxs_mn" value="<?php echo date_i18n('i'); ?>" size="2" maxlength="2" autocomplete="off"></div>
    </div></div>
      
      <div id="nxsNPLoaderPost" style="display: none";> <img  src="<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif" /> Posting....  it could take some time...  </div>
            
      <div class="submitX"><input style="font-weight: bold; width: 100px;" id="pf_postButton" type="button" onclick="nxs_doNP();" value="Post">
      <?php if ($air) {
            ?>&nbsp;&nbsp;&nbsp;&nbsp;<input id="nxsNPCloseBt" style="width: 70px;" onclick="jQuery.pgwModal('close');" class="bClose" type="button" value="Cancel"> <?php
        } ?>
      </div> 
      
      <div id="nxsNPResult">&nbsp;</div>
      
      <div id="nxsNPResult2">&nbsp;</div>
      
  </div> </div>
      
  </div> 
  </div> 
  
  <?php
    }
}

if (!function_exists('nxs_showNetworksList')) {
    function nxs_showNetworksList($networks, $selected='')
    {
        global $nxs_snapAvNts;
        $out = '';
        foreach ($nxs_snapAvNts as $avNt) {
            $clName = 'nxs_snapClass'.$avNt['code'];
            $ntClInst = new $clName();
            if (isset($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) {
                $out .= '<div class="nsx_iconedTitle" style="margin-bottom:10px;margin-top:10px;background-image:url('.NXS_PLURL.'img/'.$avNt['lcode'].'16.png);">'.$avNt['name'].'<br/></div><div style="margin-left: 14px;">';
                $ntOpts = $networks[$avNt['lcode']];
                foreach ($ntOpts as $indx=>$pbo) {
                    $out .= '<input class="nxsNPDoChb" value="'.$avNt['lcode'].'--'.$indx.'" name="nxs_NPNts" type="checkbox"'.(((empty($selected)&&(int)$pbo['do'] == 1) || (!empty($selected)&&(in_array($avNt['lcode'].$indx, $selected))))? "checked":'').' />';
                    $out .= $avNt['name'].'<i style="color: #005800;">'.(($pbo['nName']!='')?"(".$pbo['nName'].")":'').'</i></br>';
                }
                $out .= '</div>';
            }
        }
        return $out;
    }
}

//######### Comments import functions
if (!function_exists("nxs_postNewComment")) {
    function nxs_postNewComment($cmnt, $aa = false)
    {
        $cmnt['comment_post_ID'] = (int) $cmnt['comment_post_ID'];
        $cmnt['comment_parent'] = isset($cmnt['comment_parent']) ? absint($cmnt['comment_parent']) : 0;
        $ae =  get_option('admin_email');
        //$u = get_user_by( 'email', get_option('admin_email') );   $cmnt['user_id'] = $u->ID; //???
        $u = get_user_by('email', $cmnt['comment_author_email']);
        if (!empty($u)) {
            $cmnt['user_id'] = $u->ID;
        } else {
            $cmnt['user_id'] = 0;
        }

        $parent_status = (0 < $cmnt['comment_parent']) ? wp_get_comment_status($cmnt['comment_parent']) : '';
        $cmnt['comment_parent'] = ('approved' == $parent_status || 'unapproved' == $parent_status) ? $cmnt['comment_parent'] : 0;
        $cmnt['comment_author_IP'] = '';
        if (empty($cmnt['comment_agent'])) {
            $cmnt['comment_agent'] = 'SNAP';
        }
        $cmnt['comment_date'] =  get_date_from_gmt($cmnt['comment_date_gmt']);
        $cmnt = wp_filter_comment($cmnt);
        if ($aa) {
            $cmnt['comment_approved'] = 1;
        } else {
            $cmnt['comment_approved'] = nxs_wp_allow_comment($cmnt);
        } // echo "INSERT";  prr($cmnt);
        if ($cmnt['comment_approved'] != 'spam' && $cmnt['comment_approved']>1) {
            return $cmnt['comment_approved'];
        } else {
            $cmntID = wp_insert_comment($cmnt);
            do_action('comment_post', $cmntID, $cmnt['comment_approved']);
        }
        if (empty($cmntID)) {
            nxs_addToLogN('E', 'Error', 'Comments', '-=ERROR=-', print_r($cmnt, true));
            return;
        }
  
        if ('spam' !== $cmnt['comment_approved']) {
            if ('0' == $cmnt['comment_approved']) {
                wp_notify_moderator($cmntID);
            }
            $post = get_post($cmnt['comment_post_ID']);
            if (get_option('comments_notify') && $cmnt['comment_approved'] && (! isset($cmnt['user_id']) || $post->post_author != $cmnt['user_id'])) {
                wp_notify_postauthor($cmntID);
            }
            global $wpdb, $dsq_api;
            if (isset($dsq_api) && is_object($post)) {
                $plugins_url = str_replace('social-networks-auto-poster-facebook-twitter-g/', '', plugin_dir_path(__FILE__));
                if (file_exists($plugins_url.'disqus-conditional-load/disqus-core/export.php')) {
                    require_once($plugins_url.'disqus-conditional-load/disqus-core/export.php');
                } elseif (file_exists($plugins_url.'disqus-comment-system/export.php')) {
                    require_once($plugins_url.'disqus-comment-system/export.php');
                }
                if (function_exists('dsq_export_wp')) {
                    $comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID = %d", $cmntID));
                    $wxr = nxs_dsq_export_wp($post, $comments);
                    $response = $dsq_api->import_wordpress_comments($wxr, time());
                }
            }
        }
        return $cmntID;
    }
}

//#### #2 Native WP Function that has wp_die in the middle of it ?????
function nxs_wp_allow_comment($commentdata)
{
    global $wpdb;
    extract($commentdata, EXTR_SKIP);
    // Simple duplicate check // expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_parent = '$comment_parent' AND comment_approved != 'trash' AND ( comment_author = '$comment_author' ";
    if ($comment_author_email) {
        $dupe .= "OR comment_author_email = '$comment_author_email' ";
    }
    $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    $dupeID = $wpdb->get_var($dupe);
    if ($dupeID) {
        do_action('comment_duplicate_trigger', $commentdata);
        return $dupeID;
    }
    do_action('check_comment_flood', $comment_author_IP, $comment_author_email, $comment_date_gmt);
    if (! empty($user_id)) {
        $user = get_userdata($user_id);
        $post_author = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1", $comment_post_ID));
    }
    if (isset($user) && ($user_id == $post_author || $user->has_cap('moderate_comments'))) { // The author and the admins get respect.
        $approved = 1;
    } else { // Everyone else's comments will be checked.
        if (check_comment($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent, $comment_type)) {
            $approved = 1;
        } else {
            $approved = 0;
        }
        if (wp_blacklist_check($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent)) {
            $approved = 'spam';
        }
    }
    $approved = apply_filters('pre_comment_approved', $approved, $commentdata);
    return $approved;
}
//#### #3
if (!function_exists("ns_get_avatar")) {
    function ns_get_avatar($avatar, $id_or_email, $size=96, $default='', $alt='')
    {
        if (is_object($id_or_email)) {
            if ($id_or_email->comment_agent=='SNAP' && stripos($id_or_email->comment_author_url, 'facebook.com')!==false) {
                $fbuID = str_ireplace('@facebook.com', '', $id_or_email->comment_author_email);
                $avatar = "<img alt='{$id_or_email->comment_author}' src='https://graph.facebook.com/$fbuID/picture' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
            }
            if (stripos($id_or_email->comment_agent, 'SNAP||')!==false && stripos($id_or_email->comment_author_url, 'twitter.com')!==false) {
                $fbuID = str_ireplace('SNAP||', '', $id_or_email->comment_agent);
                $avatar = "<img alt='{$id_or_email->comment_author}' src='{$fbuID}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
            }
        }
        return $avatar;
    }
}
add_filter('get_avatar', 'ns_get_avatar', 10, 5);
//#### #4
if (!function_exists('nxs_importComments')) {
    function nxs_importComments()
    {
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $networks = $nxs_SNAP->nxs_accts;
        $options = $nxs_SNAP->nxs_options;
        $riPosts = get_option('NS_SNriPosts');
        if (!is_array($riPosts)) {
            $riPosts = array();
        } //## Check for Incoming Comments if nessesary.
        if (empty($options['riActive']) || $options['riActive'] != '1' || count($riPosts)<1) {
            return;
        }
        nxs_addToLogN('S', 'Comments Import', 'ALL', 'Checking for new comments now...', print_r($riPosts, true));
        //## Facebook
        if (!empty($networks['fb']) && is_array($networks['fb'])) {
            foreach ($networks['fb'] as $ii=>$fbo) {
                if ($fbo['riComments']=='1') {
                    $fbo['ii'] = $ii;
                    $fbo['pType'] = 'aj';
                    foreach ($riPosts as $postID) {
                        $fbpo =  get_post_meta($postID, 'snapFB', true);
                        $fbpo =  maybe_unserialize($fbpo);
                        if (is_array($fbpo) && isset($fbpo[$ii]) && is_array($fbpo[$ii]) && isset($fbpo[$ii]['pgID']) && trim($fbpo[$ii]['pgID'])!='') {
                            $ntClInst = new nxs_snapClassFB();
                            $fbo = $ntClInst->adjMetaOpt($fbo, $fbpo[$ii]);
                            $ntClInst->importComments($fbo, $postID, $fbpo[$ii]);
                            // 4.2.2 echo "X3"; $ntClInst->importComments($fbo, $postID, $fbpo[$ii], '1019404527_10212971242848671');
                        }
                    }
                }
            }
        }
        //## Twitter
        if (!empty($networks['tw']) && is_array($networks['tw'])) {
            foreach ($networks['tw'] as $ii=>$fbo) {
                if (!empty($fbo['riComments']) && $fbo['riComments']=='1') {
                    $fbo['ii'] = $ii;
                    $fbo['pType'] = 'aj';
                    foreach ($riPosts as $postID) {
                        $fbpo =  get_post_meta($postID, 'snapTW', true);
                        $fbpo =  maybe_unserialize($fbpo);
                        if (is_array($fbpo) && isset($fbpo[$ii]) && is_array($fbpo[$ii])  && isset($fbpo[$ii]['pgID']) && trim($fbpo[$ii]['pgID'])!='') {
                            $ntClInst = new nxs_snapClassTW();
                            $fbo = $ntClInst->adjMetaOpt($fbo, $fbpo[$ii]);
                            $ntClInst->importComments($fbo, $postID, $fbpo[$ii]);
                        }
                    }
                }
            }
        }
    }
}


?>