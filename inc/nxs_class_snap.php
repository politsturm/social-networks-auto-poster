<?php
//## Define SNAP class

if (!class_exists("nxs_SNAP")) {
    class nxs_SNAP
    {//## SNAP General Class
        public $dbOptionsName = "nxsSNAPOptions";
        public $dbNtsName = "nxsSNAPNetworks";
        public $dbNtsNameU = "nxsSNAPNetworksU";
        public $nxs_snapAvNts;
        public $nxs_options = "";
        public $nxs_ntoptions = array();
        public $nxs_accts = "";
        public $nxs_acctsU = "";
        public $sMode = array('s'=>'S', 'l'=>'F', 'u'=>'O', 'a'=>'S', 's'=>'S', 't'=>false);
        public $old_dbOptionsName = "NS_SNAutoPoster";
        
        public function __construct($u='')
        {
            load_plugin_textdomain('social-networks-auto-poster-facebook-twitter-g', false, substr(dirname(plugin_basename(__FILE__)), 0, -4).'/lang/');
            if (empty($u)) {
                $u = wp_get_current_user();
                $u = $u->ID;
            }
            $this->dbNtsNameU .= $u;
            $this->getAPOptions();
            $this->sMode['r'] = false;
            $this->sMode['s'] = (defined('MULTISITE') && MULTISITE==true)?'M':'S';
            $snapMgmt = new nxs_adminMgmt();
            $this->sMode['l'] = function_exists("ns_SMASV41")?(function_exists("ns_SMASV4M1")?'M':'P'):'F';
            $this->sMode['t'] = $this->sMode['l']=='M' || $this->sMode['l']=='P';
            if ($this->sMode['s']=='M') {
                global $blog_id;
                add_action('network_admin_menu', array($snapMgmt,'ntAdminMenu'));
                if ($this->sMode['l']!='M') {
                    if (get_site_option('nxs_nts')==$blog_id) {
                        $this->sMode['r'] = true;
                        $snapMgmt->init();
                    } else {
                        $this->sMode['a']='I';
                    }
                }
                if ($this->sMode['l']=='M') {
                    $s = (!empty($this->nxs_ntoptions['l']))?$this->nxs_ntoptions['l']:array();
                    $this->sMode['u'] = !empty($s[$blog_id])?$s[$blog_id]:(!empty($this->nxs_ntoptions['nxsSUType'])?$this->nxs_ntoptions['nxsSUType']:'O');
                    if ($this->sMode['u']=='O') {
                        $this->sMode['r'] = true;
                        $snapMgmt->init();
                    }
                    if ($this->sMode['u']=='S') {
                        $this->sMode['r'] = true;
                        switch_to_blog(1);
                        $this->getAPOptions();
                        restore_current_blog();
                    }
                }
            } else {
                $this->sMode['r'] = true;
                $snapMgmt->init();
            } //  prr($this->nxs_ntoptions);
    //QP Post Type
        }
        public function toLatestVer($options)
        {
            global $nxs_snapAvNts;
            if (!empty($options['v'])) {
                $v = $options['v'];
            } else {
                $v = 340;
            }
            $optionsOut = array(); // prr($v);
            switch ($v) {
     case 340:
       //## Networks
       $nts = array(); foreach ($nxs_snapAvNts as $avNt) {
           if (!empty($options[$avNt['lcode']])) {
               foreach ($options[$avNt['lcode']] as $aNt) {
                   if (!empty($aNt)) {
                       $clName = 'nxs_snapClass'.$avNt['code'];
                       $ntt = new $clName;
                       if (method_exists($ntt, 'toLatestVer')) {
                           $nts[$avNt['lcode']][] = $ntt->toLatestVer($aNt);
                       } else {
                           $nts[$avNt['lcode']][] = $aNt;
                       }
                   }
               }
           }
           unset($options[$avNt['lcode']]);
       }
       //## Options
       $options['fltrsOn'] = 1; $options['nxs_post_type'][] = 'post'; if (!empty($options['useForPages'])) {
           $options['fltrs']['nxs_post_type'][] = 'page';
       } unset($options['useForPages']);
       if (!empty($options['nxsCPTSeld'])) {
           $nxsCPTSeld = maybe_unserialize($options['nxsCPTSeld']);
           foreach ($nxsCPTSeld as $cpt) {
               $options['fltrs']['nxs_post_type'][] = $cpt;
           }
           unset($options['nxsCPTSeld']);
       }
       if (!empty($options['exclCats'])) {
           $excCs = maybe_unserialize($options['exclCats']);
           foreach ($excCs as $excC) {
               $options['fltrs']['nxs_cats_names'][] = $excC;
           }
           $options['fltrs']['nxs_ie_cats_names'] = 1;
           unset($options['exclCats']);
       }
       if (!empty($options['lku'])) {
           $l = '__plugins_cache_244';
           $k = get_site_option($l);
           if (empty($k)) {
               $k = array('lku'=>$options['lku'], 'ukver'=>$options['ukver'], 'uklch'=>$options['uklch']);
               update_site_option($l, $k);
           }
       }
       
       if (defined('MULTISITE') && MULTISITE!=false) {
           $ntoptions = get_site_option('NS_SNAutoPoster');
           if (!empty($ntoptions)) {
               $ntoOut = array();
               $ntoOut['nxsSUType'] = $ntoptions['nxsSUType'];
               $args = array('fields'=>'ids');
               if (function_exists('get_sites')) {
                   $sites = get_sites($args);
               } else {
                   $sitesX = wp_get_sites(array('public'=> 1, 'archived' => 0, 'mature' => 0, 'spam' => 0, 'deleted' => 0));
                   $sites = array();
                   foreach ($sitesX as $ss) {
                       $sites[] = $ss['blog_id'];
                   }
               }
               foreach ($sites as $st) {
                   switch_to_blog($st);
                   $o = get_option('NS_SNAutoPoster');
                   if (!empty($o['suaMode'])) {
                       $ntoOut['l'][$st] = $o['suaMode'];
                   }
               }
               restore_current_blog();
               delete_site_option('NS_SNAutoPoster');
               update_site_option($this->dbOptionsName, $ntoOut);
           }
       }
     break;
    }
            $options['v'] = NXS_SETV;
            $this->saveNetworksOptions($nts, $options);
            delete_option($this->old_dbOptionsName);
            return $options;
        }
        public function getAPOptions()
        {
            global $nxs_skipSSLCheck, $blog_id;
            $options = get_option($this->dbOptionsName);
            $this->nxs_accts = get_option($this->dbNtsName);
            $this->nxs_acctsU = get_option($this->dbNtsNameU);
            //var_dump($options); // global $nxs_snapAvNts; prr($nxs_snapAvNts); prr($options); prr($this->nxs_accts);// prr($this->nxs_accts, 'RER:');
            if (empty($options) || empty($options['nxsHTDP'])) {
                $oldOpts = get_option($this->old_dbOptionsName);
                if (!empty($oldOpts)) {
                    $options = $this->toLatestVer($oldOpts);
                } else {
                    $fltrs = array();
                    $fltrs[0] = array('nxs_ie_posttypes]'=>'0','nxs_post_type][]'=>'post');
                    $options = array('nxsHTDP'=>'S','quDays'=>'0','quHrs'=>'0','quMins'=>'3','quLimitRndMins'=>'2','nxsOverLimit'=>'D','showNTListCats'=>'1','fltrsOn'=>'1','nxsURLShrtnr'=>'O','gglAPIKey'=>'','bitlyUname'=>'','bitlyAPIKey'=>'','xcoAPIKey'=>'','clkimAPIKey'=>'','postAPIKey'=>'','adflyUname'=>'','adflyAPIKey'=>'','adflyDomain'=>'adf.ly','rblyAPIKey'=>'','rblyDomain'=>'','YOURLSURL'=>'','YOURLSKey'=>'','riHowManyPostsToTrack'=>'10','riHowOften'=>'15','addURLParams'=>'','forcessl'=>'','nxsHTSpace'=>'','nxsHTSepar'=>'c_','anounTagLimit'=>'300','ogImgDef'=>'','imgNoCheck'=>'set','imgSizeImg'=>'full','imgSizeAttch'=>'medium','featImgLoc'=>'','featImgLocArrPath'=>'','featImgLocPrefix'=>'','errNotifEmail'=>'', 'fltrs'=>$fltrs, 'v'=>NXS_SETV, 'ver'=>306);
                }
            } // prr($options);
            $this->nxs_ntoptions = get_site_option($this->dbOptionsName);
            $nxs_UPPath = 'nxs-snap-pro-upgrade';
            $dir = plugin_dir_path(__FILE__);
            $dir = explode('social-networks-auto-poster-facebook-twitter-g', $dir);
            $dir = $dir[0];
            $pf = $dir.$nxs_UPPath.'/'.$nxs_UPPath.'.php';
            if (file_exists($pf) && !class_exists('nxs_wpAPIEngine')) {
                require_once $pf;
            }
            if (class_exists('nxs_wpAPIEngine')) {
                $cl = new nxs_wpAPIEngine();
                $cl->check();
            }
            if (defined('NextScripts_UPG_SNAP_Version') && version_compare(NextScripts_UPG_SNAP_Version, '1.4.0')<0) {
                add_action('admin_notices', 'nxs_admin_notice__wrongProHelper');
            }
            //## Backup
            $lBckTime = get_option('nxs_lBckTime');
            if (empty($lBckTime) || $lBckTime<strtotime("-1 week")) {
                update_option('nxsSNAPNetworks_bck4', $this->nxs_accts, false);
                update_option('nxsSNAPOptions_bck4', $this->nxs_options, false);
                update_option('nxs_lBckTime', time(), false);
            }
         
            //if (function_exists('nxs_getInitAdd')) nxs_getInitAdd($options); if (!empty($options['uk'])) $options['uk']='API';
            //if (defined('NXSAPIVER') && (empty($options['ukver']) || $options['ukver']!=NXSAPIVER)){$options['ukver']=NXSAPIVER; $this->saveNetworksOptions('',$options);}
            //## OG tags Update
            if (empty($options['nxsOG'])) {
                if (!empty($options['nsOpenGraph']) && (int)$options['nsOpenGraph'] == 1) {
                    $options['nxsOG'] = 'A';
                } else {
                    if (!function_exists('wpfbogp_start_ob') && !function_exists('webdados_fb_run') && !class_exists('Ngfb') && !class_exists('Wpsso') && !class_exists('Open_Graph_Pro') && !class_exists('NY_OG_Output') && !class_exists('iworks_opengraph') && !function_exists('fbogmeta_header') && !function_exists('sfogi_wp_head') && !class_exists('OpenGraphMetabox') && !function_exists('wpseo_auto_load') && !function_exists('aioseop_activate') && !function_exists('the_seo_framework_pre_load')) {
                        $options['nxsOG'] = 'N';
                    } else {
                        $options['nxsOG'] = 'D';
                    }
                }
            }
            if (!empty($options['ukver']) && $options['ukver'] == nsx_doDecode('q234t27414r2q2')) {
                $options['ht'] = 104;
            }
            if (isset($options['skipSSLSec'])) {
                $nxs_skipSSLCheck = $options['skipSSLSec'];
            }
            $options['useSSLCert'] = '8416o4u5d4p2o22646060474k5b4t2a4u5s4';
            $this->nxs_options = $options;
            if (!empty($options)&&(empty($options['v'])||($options['v']<NXS_SETV))) {
                if (empty($options['v'])) {
                    add_action('admin_enqueue_scripts', 'nxs_snap_pointer_admin_enqueue_scripts');
                }
                $options = $this->toLatestVer($options);
            } //## Check if first run after V3-V4 update.
            $contCron = get_option('nxs_contCron');
            if ((int)$contCron>0) {
                add_action('wp_head', 'nxs_contCron_js');
            }
    
            //## CHeck for V4 API Update
            $g = get_site_option('nxs_v4APIMn');
            if (empty($g) && defined('NXSAPIVER') && stripos(NXSAPIVER, 'NXSID')===false && (int)substr(NXSAPIVER, 0, 1)<4 && function_exists('nxs_doChAPIU')) {
                nxs_doChAPIU();
                update_site_option('nxs_v4APIMn', 1);
            }
    
            if (isset($_GET['page']) && $_GET['page']=='nxs-help' && isset($_GET['do']) && $_GET['do']=='test') {
                error_reporting(E_ALL);
                ini_set('error_reporting', E_ALL);
                ini_set('display_errors', 1);
                echo "Testting... cURL (SSL/HTTPS Connections)<br/>SNAP Ver: ".NextScripts_SNAP_Version.(defined('NXSAPIVER')?"  API: ".NXSAPIVER:'').(defined('NextScripts_UPG_SNAP_Version')?" | SNAP Helper Ver: ".NextScripts_UPG_SNAP_Version:'')."<br/>Deflate - ";
                echo (function_exists('gzdeflate'))?"Yes":"No";
                echo "<br/><br/>";
                nxs_cURLTest("https://whatismyip.org/", "HTTPS to whatismyip", 'getMyIP');
                nxs_cURLTest("https://www.nextscripts.com/", "HTTPS to NXS", "Social Networks");
                nxs_cURLTest("http://35.184.37.105/", "HTTPS to NXSA", "NextScripts Cloud");
                nxs_cURLTest("http://www.google.com/intl/en/contact/", "HTTP to Google", "Mountain View, CA");
                nxs_cURLTest("https://www.google.com/intl/en/contact/", "HTTPS to Google", "Mountain View, CA");
                nxs_cURLTest("https://www.facebook.com/", "HTTPS to Facebook", 'id="facebook"');
                nxs_cURLTest("https://graph.facebook.com/", "HTTPS to API (Graph) Facebook", 'get');
                nxs_cURLTest("https://www.linkedin.com/", "HTTPS to LinkedIn", 'rel="canonical" href="https://www.linkedin.com/');
                nxs_cURLTest("https://twitter.com/", "HTTPS to Twitter", '<link rel="canonical" href="https://twitter.com');
                nxs_cURLTest("https://www.pinterest.com/", "HTTPS to Pinterest", 'content="Pinterest"');
                nxs_cURLTest("https://www.livejournal.com/login.bml", "HTTPS to LiveJournal", 'livejournal.com/about');
                die('Done');
            }
            if (isset($_GET['page']) && $_GET['page']=='nxs-help' && isset($_GET['do']) && $_GET['do']=='crtest') {
                if (isset($_GET['redo']) && $_GET['redo']=='1') {
                    delete_option("NXS_cronCheck"); ?><script type="text/javascript">window.location = "<?php echo nxs_get_admin_url('admin.php?page=nxs-help&do=crtest'); ?>"</script><?php die();
                }
                $cr = get_option('NXS_cronCheck');
                if (!empty($cr) && is_array($cr)) {
                    $checks = $cr['cronChecks'];
                    $numChecks = count($checks);
                    echo '<div style="font-family:\'Open Sans\',sans-serif;font-size: 15px;">';
                    if (($cr['cronCheckStartTime']+900)>(time())) {
                        echo "<b>Cron Check is in Progress.....</b> will be finished in ".($cr['cronCheckStartTime']+900-time()).' seconds. Please <input type="button" value="Reload" onClick="location.reload()"> this page to see more results.... <br/><br/>';
                    } else {
                        echo "Cron Check Results:<br/>";
                        echo '<span style="color:#761616">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;==== Cron was executed <b>'.$numChecks.'</b> times in 15 minutes ===</span>';
                        if ($numChecks>15 || $numChecks<2) {
                            echo '<b style="color:#FF0000"><br/><br/>Your WP Cron is not healthy</b><br/><br/><span style="color:#761616">'.(($numChecks>15)?('WP Cron should NOT be executed more then once per minute.'):('WP Cron should be executed at least once in 5-10 minutes.')).'  Some functionality (like auto-reposting) will be disabled.</span><br/><br/><span style="color:#005858; font-weight:bold;">Why this is important?</span><br/><span style="color:#005858">Please see this post: <a href="https://www.nextscripts.com/blog/troubles-wp-cron-existing-posts-auto-reposter/" target="_blank">Troubles with WP Cron and existing posts auto-reposter</a></span><br/><br/><span style="color:#005858; font-weight:bold;">Solution</span><br/><span style="color:#005858">Please see the instructions for the correct WP Cron setup: <a href="https://www.nextscripts.com/tutorials/wp-cron-scheduling-tasks-in-wordpress/" target="_blank">WP-Cron: Scheduling Tasks in WordPress</a></span>';
                        } else {
                            echo '<b style="color:#0000FF"><br/><br/>Your WP Cron is OK</b>';
                        }
                    } ?> <br/><br/><span style="color:#000058; font-weight:normal;">Technical Info:</span> <?php prr($cr); ?>&nbsp;&nbsp;====&nbsp;<a href="<?php echo nxs_get_admin_url('admin.php?page=nxs-help&do=crtest&redo=1'); ?>">Re-do Cron Check</a> (it will take 15 minutes to complete)<?php
                } else {
                    echo 'Check is not started yet... Please <input type="button" value="Reload" onClick="location.reload()"> this page in couple minutes.<br/>If you still see this message after several minutes, your cron is NOT RUNNING.';
                }
                echo '</div>';
                die();
            }
        }
        public function saveNetworksOptions($networks, $options='')
        { //## Set or just save (=1) Options and Networks
            if (!empty($networks)) {
                if (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) {
                    if ($networks!=1) {
                        $this->nxs_acctsU = $networks;
                    }
                    update_option($this->dbNtsNameU, $this->nxs_acctsU, false);
                } else {
                    if ($networks!=1) {
                        $this->nxs_accts = $networks;
                    }
                    update_option($this->dbNtsName, $this->nxs_accts, false);
                }
            }
            if (!empty($options)) {
                if ($options!=1) {
                    $this->nxs_options = $options;
                }
                update_option($this->dbOptionsName, $this->nxs_options, false);
            }
        }
  
        public function showUsersSitesMUTab()
        {
            global $nxs_snapAvNts;
            $ntoptions = get_site_option('nxsSNAPOptions'); ?>
  
   <h3> Manage SNAP for each individual site: </h3>        
   
   <script type="text/javascript">jQuery(document).ready(function(){jQuery(".nsxmu_tab_content").hide(); jQuery("ul.nsxmu_tabs li:first").addClass("active").show(); jQuery(".nsxmu_tab_content:first").show();
    jQuery("ul.nsxmu_tabs li").click(function() { jQuery("ul.nsxmu_tabs li").removeClass("active"); jQuery(this).addClass("active"); jQuery(".nsxmu_tab_content").hide(); 
      var activemuTab = jQuery(this).find("a").attr("href"); jQuery(activemuTab).fadeIn(); return false;
    }); });
    
    function nxs_saveNewSiteDefSets(val){ jQuery('#nxsWmpuLoadingImg').show(); 
      var data = { action: 'nxs_saveNewSiteDefSets', id: 0, sset: val, _wpnonce: jQuery('input#nxssnap_wpnonce').val()}; jQuery.post(ajaxurl, data, function(response) {  
         jQuery('#nxsWmpuLoadingImg').hide();
      });
    }
    
  </script> 
     
    &nbsp;&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;<a style="font-weight: normal; font-size: 14px; line-height: 24px;" href="<?php echo admin_url(); ?>network/sites.php">Click here to go to the list of sites and manage SNAP for each site</a>
   <h3> Default Settings for New Sites: </h3>      
   <div style="margin-left: 25px;">
      <input type="radio" onchange="nxs_saveNewSiteDefSets(this.value);"  <?php if (isset($ntoptions['nxsSUType']) && $ntoptions['nxsSUType']=='S') {
                echo ' checked="checked" ';
            } ?> name="nxsSUType" value="S" /> <b>Super Admin Mode.</b> <i>Super Admin configures all Social Networks. All posts made in all blogs will be re-posted to all neteworks. Blog owners and other users don't see and can't change any settings or options</i><br/>        
      <input type="radio" onchange="nxs_saveNewSiteDefSets(this.value);" <?php if (isset($ntoptions['nxsSUType']) && $ntoptions['nxsSUType']=='O') {
                echo ' checked="checked" ';
            } ?> name="nxsSUType" value="O" /> <b>Regular Mode</b> <i>Blog owners can configure and autopost to their own Social Networks</i><br/>   
      <input type="radio" onchange="nxs_saveNewSiteDefSets(this.value);" <?php if ((!isset($ntoptions['nxsSUType']) || isset($ntoptions['nxsSUType']) && $ntoptions['nxsSUType']=='D')) {
                echo ' checked="checked" ';
            } ?> name="nxsSUType" value="D" /> <b>Disabled</b> <i>SNAP is disabled for new sites.</i><br/>
      </div>      
          <br/><img id="nxsWmpuLoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />      
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style="font-size: 14px;" onclick="nxs_saveSiteSets('A', 'S');return false;" href="#">[Set "Super Admin Mode" on all sites]</a>
&nbsp;&nbsp;<a style="font-size: 14px;" onclick="nxs_saveSiteSets('A', 'O');return false;" href="#">[Set "Regular Mode" on all sites]</a>
&nbsp;&nbsp;<a style="font-size: 14px;" onclick="nxs_saveSiteSets('A', 'D');return false;" href="#">[Disable SNAP on all sites]</a><?php
        }
        
        public function showAccountsTab()
        {
            global $nxs_snapAvNts, $nxsOne;
            $nxsOne = '';
            $trrd=0;
            $parts = parse_url(home_url());
            $nxs_snapThisPageUrl = "{$parts['scheme']}://{$parts['host']}" . add_query_arg(null, null);
            $cst=strrev('enifed');
            $isMobile = nxs_isMobile();
            if (function_exists('nxs_doSMAS2')) {
                $rf = new ReflectionFunction('nxs_doSMAS2');
                $trrd++;
                $rff = $rf->getFileName();
                if (stripos($rff, "'d code")===false) {
                    $cst(chr(100).$trrd, $trrd);
                }
            }
            //## Import Settings
            if (isset($_POST['upload_NS_SNAutoPoster_settings'])) {
                if (get_magic_quotes_gpc() || $_POST['nxs_mqTest']=="\'") {
                    array_walk_recursive($_POST, 'nsx_stripSlashes');
                }
                array_walk_recursive($_POST, 'nsx_fixSlashes');
                $secCheck =  wp_verify_nonce($_POST['nxsChkUpl_wpnonce'], 'nxsChkUpl');
                if ($secCheck!==false && isset($_FILES['impFileSettings_button']) && is_uploaded_file($_FILES['impFileSettings_button']['tmp_name'])) {
                    $fileData = trim(file_get_contents($_FILES['impFileSettings_button']['tmp_name']));
                    while (substr($fileData, 0, 1)!=='a') {
                        $fileData = substr($fileData, 1);
                    }
                    $uplOpt = maybe_unserialize($fileData);
                    if (is_array($uplOpt) && (isset($uplOpt['imgNoCheck']) || isset($uplOpt['useSSLCert']))) {
                        $options = $uplOpt; //### V3 import
          if (!empty($options)&&(empty($options['v'])||($options['v']<NXS_SETV))) {
              if (empty($options['v'])) {
                  add_action('admin_enqueue_scripts', 'nxs_snap_pointer_admin_enqueue_scripts');
              }
              $options = $this->toLatestVer($options);
          }  //## Check if first run after V3-V4 update.
            else {
                $this->saveNetworksOptions($options['n'], $options['o']);
            }
                    } elseif (is_array($uplOpt) && (isset($uplOpt['o']) || isset($uplOpt['u']))) { //### V4 import
                        if (!empty($_POST['nxs_doAccMrg'])) {
                            $networks = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? $this->nxs_acctsU : $this->nxs_accts;
                            $uplOpt['u'] = array_merge_recursive($networks, $uplOpt['u']);
                        }
                        $this->saveNetworksOptions($uplOpt['u'], $uplOpt['o']);
                    } else {
                        ?><div class="error" id="message"><p><strong>Incorrect Import file.</div><?php
                    }
                }
            }
       
            // $networks = !empty($this->nxs_acctsU)?$this->nxs_acctsU:$this->nxs_accts; $options = $this->nxs_options; $isNoNts = true;  - Something wierd! Why would we show default/admin if iuser has no accounts?
            $networks = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? $this->nxs_acctsU : $this->nxs_accts;
            $options = $this->nxs_options;
            $isNoNts = true;
            //## Get rid of empty networks
            $nt2=array();
            if (!empty($networks)&&is_array($networks)) {
                foreach ($networks as $ntnt=>$ntas) {
                    if (!empty($ntas)&&is_array($ntas)) {
                        foreach ($ntas as $ntii=>$nta) {
                            if (isset($ntii)&&$ntii!=='') {
                                $nt2[$ntnt][$ntii]=$nta;
                            }
                        }
                    }
                }
            }
            if ($nt2!=$networks) {
                $networks=$nt2;
                $this->saveNetworksOptions($nt2);
            }
    
            foreach ($nxs_snapAvNts as $avNt) {
                if (isset($networks[$avNt['lcode']]) && is_array($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) {
                    $isNoNts = false;
                    break;
                }
            } ?> <form method="post" id="nsStForm" action=""><input type="hidden" name="nxsMainFromSupportFld" id="nxsMainFromSupportFld" value="1" />
       <input name="action" value="nxs_snap_aj" type="hidden" />
       <input name="nxsact" value="setNTS" type="hidden" />       
       <input name="nxs_mqTest" value="'" type="hidden" />
       <input type="hidden" id="svSetRef" name="_wp_http_referer" value="" />
       <input type="hidden" id="svSetNounce" name="_wpnonce" value="" />
     
      <a href="#" class="NXSButton" id="nxs_snapNewAcc"><?php _e('Add new account', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>
      
      <?php if (!$isMobile) {
                ?><div class="nxsInfoMsg"><img style="position: relative; top: 8px;" alt="Arrow" src="<?php echo NXS_PLURL; ?>img/arrow_l_green_c1.png"/> You can add Facebook, Twitter, Google+, Pinterest, LinkedIn, Tumblr, Blogger, ... accounts</div><?php
            } ?><br/>
      
      <div style="padding-bottom: 10px; padding-top: 10px; text-align: right"><a href="#" onclick="jQuery('.nxs_acctcb').attr('checked','checked'); jQuery('.nxs_acctcb').iCheck('update'); return false;">[<?php  _e('Select All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" onclick="jQuery('.nxs_acctcb').removeAttr('checked'); nxs_showHideMetaBoxBlocks(); jQuery('.nxs_acctcb').iCheck('update'); return false;">[<?php _e('Unselect All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" id="nxsShowOnlySelected" onclick="return false;">[<?php _e('Show Only Selected', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" id="nxsShowOnlySelectedAll" onclick="return false;">[<?php _e('Show All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a></div>  
      
      
      <div id="nxs_spPopup" class="white-popupx xmfp-hide"><span class="nxspButton bClose"><span>X</span></span><div id="nxs_spPopupU" style="min-height: 300px;"><select onchange="doShowFillBlockX(this.value);" id="nxs_ntType" class="nxs_ntType"><option value =""><?php _e('Please select network...', 'social-networks-auto-poster-facebook-twitter-g'); ?></option>
      
      <?php  if (empty($options['showNTListCats'])) {
                foreach ($nxs_snapAvNts as $avNt) {
                    if (!isset($networks[$avNt['lcode']]) || count($networks[$avNt['lcode']])==0) {
                        $mt=0;
                    } else {
                        $mt = 1+max(array_keys($networks[$avNt['lcode']]));
                    }
                    echo '<option value ="'.$avNt['code'].$mt.'" data-imagesrc="'.NXS_PLURL.'img/'.(!empty($avNt['imgcode'])?$avNt['imgcode']:$avNt['lcode']).'16.png">'.$avNt['name'].'</option>';
                }
            } else {
                $nxs_snapAvNtsDD = array();
                foreach ($nxs_snapAvNts as $avNt) {
                    if (!empty($avNt['type'])) {
                        $nxs_snapAvNtsDD[$avNt['type']][] = $avNt;
                    } else {
                        $nxs_snapAvNtsDD['Other'][] = $avNt;
                    }
                }
                uksort($nxs_snapAvNtsDD, 'nxs_add_array_sort');//prr($nxs_snapAvNtsDD);
                foreach ($nxs_snapAvNtsDD as $ttp => $avNtD) {
                    echo '<option data-title="1" data-imagesrc="'.NXS_PLURL.'img/arrow_r_green_c1.png">'.$ttp.'</option>';
                    foreach ($avNtD as $avNt) {
                        if (!isset($networks[$avNt['lcode']]) || count($networks[$avNt['lcode']])==0) {
                            $mt=0;
                        } else {
                            $mt = 1+max(array_keys($networks[$avNt['lcode']]));
                        }
                        echo '<option value="'.$avNt['code'].$mt.'" data-imagesrc="'.NXS_PLURL.'img/'.$avNt['lcode'].'16.png" sdata-description="Add '.$avNt['name'].'">'.$avNt['name'].'</option>';
                    }
                }
            } ?>
        
        </select>           
        <div id="nsx_addNT">
          <?php foreach ($nxs_snapAvNts as $avNt) {
                $clName = 'nxs_snapClass'.$avNt['code'];
                $ntClInst = new $clName();
                if (!isset($networks[$avNt['lcode']]) || count($networks[$avNt['lcode']])==0) {
                    $ntClInst->showNewNTSettings(0);
                } else {
                    $mt = 1+max(array_keys($networks[$avNt['lcode']]));
                    if (class_exists("nxs_wpAPIEngine") && function_exists('nxs_doSMAS1')) {
                        nxs_doSMAS1($ntClInst, $mt);
                    } else {
                        nxs_doSMAS($avNt['name'], $avNt['code'].$mt);
                    }
                }
            } ?>           
        </div>
      </div> </div>
        
       <div id="nxsAllAccntsDiv"><div class="nxs_modal"></div> 
         <?php  foreach ($nxs_snapAvNts as $avNt) {
                $clName = 'nxs_snapClass'.$avNt['code'];
                $ntClInst = new $clName();
                if (isset($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) {
                    $ntClInst->showGenNTSettings($networks[$avNt['lcode']]);
                }
            } ?>
       </div><?php
         if ($isNoNts) {
             ?><br/><br/><br/>You don't have any configured social networks yet. Please click "Add new account" button.<br/><br/>
           <input onclick="jQuery('#impFileSettings_button').click(); return false;" type="button" class="button" name="impSettings_repostButton" id="impSettings_button"  value="<?php _e('Import Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />     
       <?php
         } else {
             ?>
         <input value="'" type="hidden" name="nxs_mqTest" /> 
         <div class="submitX nxclear" style="padding-bottom: 0px;">       
           <input type="button" id="svBtnSettings" onclick="nxs_saveAllNetworks();" class="button-primary" value="<?php _e('Update Accounts', 'social-networks-auto-poster-facebook-twitter-g') ?>" />      
           <div id="nxsSaveLoadingImg" class="doneMsg">Saving.....</div> <div id="doneMsg" class="doneMsg">Done</div>
         </div>   
         
         <div style="margin-top: 10px;">
           <span><img style="padding: 3px;top: 5px;position: relative;" src="<?php echo NXS_PLURL; ?>img/cbch.png"/><?php _e('Checked checkbox - posts will be autposted to that network by default', 'social-networks-auto-poster-facebook-twitter-g') ?><br/></span>
           <span><img style="padding: 3px;top: 5px;position: relative;" src="<?php echo NXS_PLURL; ?>img/cbun.png"/><?php _e('Unchecked checkbox - posts will NOT be autposted to that network by default', 'social-networks-auto-poster-facebook-twitter-g') ?><br/></span>
           <span><img style="padding: 3px;top: 5px;position: relative;" src="<?php echo NXS_PLURL; ?>img/cbrb.png"/><?php _e('Radiobutton - Filters are on. Posts will be autposted or not autoposted depending on filters', 'social-networks-auto-poster-facebook-twitter-g') ?><br/></span>           
         </div>
         
                 
      <?php
         } //## If No Options - Save defaults ?>   
    </form>
      
      <div class="popShAtt" id="popOnlyCat"><?php _e('Filters are "ON". Only selected categories/tags will be autoposted to this account. Click "Show Settings->Advanced" to change', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
      <div class="popShAtt" style="width: 400px;" id="popShAttFLT" data-text="<?php _e('Filters are active. Click Show Settings->Advanced to change.<br/>', 'social-networks-auto-poster-facebook-twitter-g'); ?>"></div>
      <div class="popShAtt" id="popReActive"><?php _e('Reposter is activated for this account', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
      <div class="popShAtt" id="fbAttachType"><h3><?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3> <img src="<?php echo NXS_PLURL; ?>img/fb2wops.png" width="600" height="257" alt="<?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
      <div class="popShAtt" id="fbPostTypeDiff"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
         
         <div class="nxs_dropdown">
<button onclick="document.getElementById('nxs_myDropdown').classList.toggle('nxs_show');" class="nxs_dropbtn">Import/Export Accounts</button>
  <div id="nxs_myDropdown" class="nxs_dropdown-content">
    <a href="#" onclick="nxs_expSettings(false); return false;"><?php _e('Export All Accounts', 'social-networks-auto-poster-facebook-twitter-g') ?></a>
    <a href="#" onclick="nxs_expSettings(true); return false;"><?php _e('Export Only Checked Accounts', 'social-networks-auto-poster-facebook-twitter-g') ?></a>
    <a href="#" onclick="jQuery('#nxs_doAccMrg').val(0); jQuery('#impFileSettings_button').click(); return false;"><?php _e('Import and Replace', 'social-networks-auto-poster-facebook-twitter-g') ?></a>
    <a href="#" onclick="jQuery('#nxs_doAccMrg').val(1); jQuery('#impFileSettings_button').click(); return false;"><?php _e('Import and Merge', 'social-networks-auto-poster-facebook-twitter-g') ?></a>
  </div>
</div>
         
    <form method="post" enctype="multipart/form-data"  id="nsStFormUpl" action="<?php echo $nxs_snapThisPageUrl?>">
      <input type="file" accept="text/plain" onchange="jQuery('#nsStFormUpl').submit();" id="impFileSettings_button" name="impFileSettings_button" style="display: block; visibility: hidden; width: 1px; height: 0;" size="chars">
      <input type="hidden" value="1" name="upload_NS_SNAutoPoster_settings" /> <input value="'" type="hidden" name="nxs_mqTest" /> <input value="0" type="hidden" name="nxs_doAccMrg" id="nxs_doAccMrg"/>  <?php wp_nonce_field('nxsChkUpl', 'nxsChkUpl_wpnonce'); ?> 
    </form><?php // prr($networks);
        }
        
        public function showSettingsTab($useSec=true)
        {
            global $nxs_snapAvNts, $snap_curPageURL, $nxs_snapThisPageUrl, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU;
            $nxsOne = '';
            $options = $this->nxs_options; ?>
              <script type="text/javascript">setTimeout( function(){ document.getElementById( "nsStFormMisc" ).reset();},5);</script>
              
    <form method="post" id="nsStFormMisc" action="<?php echo $snap_curPageURL; ?>"> <div id="nxsSettingsDiv">   <input type="hidden" name="nxsMainFromElementAccts" id="nxsMainFromElementAccts" value="" />
       <input type="hidden" name="nxsMainFromSupportFld" id="nxsMainFromSupportFld" value="1" />
       
       <?php if ($useSec) {
                ?>
       
       <input name="action" value="nxs_snap_aj" type="hidden" />
       <input name="nxsact" value="setNTset" type="hidden" />
       <input name="nxs_mqTest" value="'" type="hidden" />       
       <input type="hidden" id="svSetRef" name="_wp_http_referer" value="" />
       <input type="hidden" id="svSetNounce" name="_wpnonce" value="" />
       
       <?php
            } ?>
       
     <!-- ##################### OTHER #####################-->            

     <div class="submitX nxclear" style="padding-bottom: 10px;">       
        <div style="display:inline-block;"><input type="button" id="svBtnSettingsTop" onclick="nxs_savePluginSettings();" class="button-primary" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" /></div>
        <div style="display:inline-block;"><div class="nxsSvSettingsAjax" style="display: none; width:200px;"> <img  src="<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif" /> <?php _e('Saving....', 'social-networks-auto-poster-facebook-twitter-g') ?></div></div> 
        <div style="display:inline-block;"><div class="doneMsg"><?php _e('Saved', 'social-networks-auto-poster-facebook-twitter-g') ?></div></div>
      </div>
     
     <!-- How to make auto-posts? --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('How to make auto-posts?', 'social-networks-auto-poster-facebook-twitter-g') ?> &lt;-- (<a id="showShAttIS" onmouseover="showPopShAtt('IS', event);" onmouseout="hidePopShAtt('IS');"  onclick="return false;" class="underdash" href="#"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g') ?></a>)</h3></div>
         <div class="popShAtt" id="popShAttIS">
        <h3><?php _e('The difference between "Immediately" and "Scheduled"', 'social-networks-auto-poster-facebook-twitter-g') ?></h3>
        <?php _e('<b>"Immediately"</b> - Once you click "Publish" button plugin starts pushing your update to configured social networks. At this time you need to wait and look at the turning circle. Some APIs are pretty slow, so you have to wait and wait and wait until all updates are posted and page released back to you.', 'social-networks-auto-poster-facebook-twitter-g') ?><br/><br/>
        <?php _e('<b>"Scheduled"</b> - Releases the page immediately back to you, so you can proceed with something else and it schedules all auto-posting jobs to your WP-Cron. This is much faster and much more efficient, but it could not work if your WP-Cron is disabled or broken.', 'social-networks-auto-poster-facebook-twitter-g') ?>
      </div>
             <div class="nxs_box_inside"> 
             
              <div class="itemDiv">
               <input type="radio" name="nxsHTDP" value="I" <?php if (isset($options['nxsHTDP']) && $options['nxsHTDP']=='I') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Publish Immediately', 'social-networks-auto-poster-facebook-twitter-g') ?></b>  - <i><?php _e('No WP Cron will be used. Choose if WP Cron is disabled or broken on your website', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
              </div>  
              
              <div class="itemDiv">
              <input type="radio" name="nxsHTDP" value="S" <?php if (!isset($options['nxsHTDP']) || $options['nxsHTDP']=='S') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Use WP Cron to Schedule autoposts', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <i><?php _e('Recommended for most sites. Faster Performance - requires working WP Cron', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/> <?php /* ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="runNXSCron" value="1"> <b><?php _e('Try to process missed "Scheduled" posts.', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <i><?php _e('Usefull when WP Cron is disabled or broken, but can cause some short performance issues and duplicates. It is <b>highly</b> recomended to setup a proper cron job of fix WP Cron instead', 'social-networks-auto-poster-facebook-twitter-g') ?></i>. <?php */ ?>
              </div>         
              
              <div class="itemDiv">
              <div style="margin-left: 20px;">
              
              <?php $cr = get_option('NXS_cronCheck');
            if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') {
                ?> <span style="color: red"> *** <?php _e('Your WP Cron is not working correctly. This feature may not work properly, and might cause duplicate postings and stability problems.<br/> Please see the test results and recommendations here:', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl;
                echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span> <br/>
            <?php
            } ?>
              
              <input type="checkbox" name="quLimit" value="1" <?php if (isset($options['quLimit']) && $options['quLimit']=='1') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Limit autoposting speed', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <i><?php _e('Recommended for busy sites with a lot of new posts.', 'social-networks-auto-poster-facebook-twitter-g') ?> </i><br/> 
              <div style="margin-left: 10px;">
              Do not autopost more then one post per network every <input name="quDays" style="width: 36px;" maxlength="3" value="<?php echo isset($options['quDays'])?$options['quDays']:'0'; ?>" /> Days,&nbsp;&nbsp;
              <input name="quHrs" style="width: 33px;" maxlength="3" value="<?php echo isset($options['quHrs'])?$options['quHrs']:'0'; ?>" /> Hours,&nbsp;&nbsp;
              <input name="quMins" style="width: 33px;" maxlength="3" value="<?php echo isset($options['quMins'])?$options['quMins']:'3'; ?>" /> Minutes.
                <div style="margin-left: 10px;">
                 <b><?php _e('Randomize posting time &#177;', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b>
     <input type="text" name="quLimitRndMins" style="width: 35px;" value="<?php echo isset($options['quLimitRndMins'])?$options['quLimitRndMins']:'2'; ?>" />&nbsp;<?php _e('Minutes', 'social-networks-auto-poster-facebook-twitter-g'); ?>
                </div>
                 
                 <div style="margin-left: 10px;">
                 <?php _e('What to do with the rest of the posts if there are more posts then daily limit?', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
                    <input type="radio" name="nxsOverLimit" value="D" <?php if (!isset($options['nxsOverLimit']) || $options['nxsOverLimit']=='D') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Skip/Discard/Don\'t Autopost ', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>
                    <input type="radio" name="nxsOverLimit" value="S" <?php if (isset($options['nxsOverLimit']) && $options['nxsOverLimit']=='S') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Schedule for tomorrow', 'social-networks-auto-poster-facebook-twitter-g') ?></b>  - <i><?php _e('Not recommended, may cause significant delays', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
                 </div>
              </div>
              </div>
              </div>                          
              
              
           </div></div>
     <!-- #### Who can see auto-posting options on the "New Post" pages? ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('User Privileges/Security', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> 
              <div class="itemDiv">
              
             <input value="set" id="skipSecurity" name="skipSecurity"  type="checkbox" <?php if (!empty($options['skipSecurity']) && (int)$options['skipSecurity'] == 1) {
                echo "checked";
            } ?> />  <b><?php _e('Skip User Security Verification.', 'social-networks-auto-poster-facebook-twitter-g') ?></b>     
             <span style="font-size: 11px; margin-left: 1px;"><?php _e('NOT Recommended, but useful in some situations. This will allow autoposting for everyone even for the non-existent users.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>  
              
              <h4><?php _e('Who can make autoposts without seeing any auto-posting options?', 'social-networks-auto-poster-facebook-twitter-g') ?></h4>
              
              <?php $editable_roles = get_editable_roles();
            if (!isset($options['whoCanMakePosts']) || !is_array($options['whoCanMakePosts'])) {
                $options['whoCanMakePosts'] = array();
            } ?>
    
<?php    foreach ($editable_roles as $role => $details) {
                $name = translate_user_role($details['name']);
                echo '<input type="checkbox" ';
                if (in_array($role, $options['whoCanMakePosts']) || $role=='administrator') {
                    echo ' checked="checked" ';
                }
                if ($role=='administrator') {
                    echo '  disabled="disabled" ';
                }
                echo 'name="whoCanMakePosts[]" value="'.esc_attr($role).'" /> '.$name;
                if ($role=='administrator') {
                    echo ' - Somebody who has access to all the administration features';
                }
                if ($role=='editor') {
                    echo " - Somebody who can publish and manage posts and pages as well as manage other users' posts, etc. ";
                }
                if ($role=='author') {
                    echo ' - Somebody who can publish and manage their own posts ';
                }
                if ($role=='contributor') {
                    echo ' - Somebody who can write and manage their posts but not publish them';
                }
                if ($role=='subscriber') {
                    echo ' - Somebody who can only manage their profile';
                }
                echo '<br/>';
            } ?> <br/> <input type="checkbox" <?php if (!empty($options['zeroUser'])) {
                echo ' checked="checked" ';
            } ?> name="zeroUser" value="1" /><?php _e('User "0" - Sometimes used for Imported/Automated posts.', 'social-networks-auto-poster-facebook-twitter-g') ?>  <br/>
    
     <h4><?php _e('Who can see auto-posting options on the "New Post" and "Edit Post" pages and make autoposts?', 'social-networks-auto-poster-facebook-twitter-g') ?></h4>
              
              <?php $editable_roles = get_editable_roles();
            if (!isset($options['whoCanSeeSNAPBox']) || !is_array($options['whoCanSeeSNAPBox'])) {
                $options['whoCanSeeSNAPBox'] = array();
            }

            foreach ($editable_roles as $role => $details) {
                $name = translate_user_role($details['name']);
                echo '<input type="checkbox" ';
                if (in_array($role, $options['whoCanSeeSNAPBox']) || $role=='administrator') {
                    echo ' checked="checked" ';
                }
                if ($role=='administrator' || $role=='subscriber') {
                    echo '  disabled="disabled" ';
                }
                echo 'name="whoCanSeeSNAPBox[]" value="'.esc_attr($role).'"> '.$name;
                if ($role=='administrator') {
                    echo ' - Somebody who has access to all the administration features';
                }
                if ($role=='editor') {
                    echo " - Somebody who can publish and manage posts and pages as well as manage other users' posts, etc. ";
                }
                if ($role=='author') {
                    echo ' - Somebody who can publish and manage their own posts ';
                }
                if ($role=='contributor') {
                    echo ' - Somebody who can write and manage their posts but not publish them';
                }
                if ($role=='subscriber') {
                    echo ' - Somebody who can only manage their profile';
                }
                echo '<br/>';
            }
    
            if (function_exists('showSNAP_WPMU_OptionsPageExt')) {
                ?>    
    
    <h4><?php _e('Who can setup his/her own accounts?', 'social-networks-auto-poster-facebook-twitter-g') ?></h4>
              
              <?php $editable_roles = get_editable_roles();
                if (!isset($options['whoCanHaveOwnSNAPAccs']) || !is_array($options['whoCanHaveOwnSNAPAccs'])) {
                    $options['whoCanHaveOwnSNAPAccs'] = array();
                }

                foreach ($editable_roles as $role => $details) {
                    $name = translate_user_role($details['name']);
                    echo '<input type="checkbox" ';
                    if (in_array($role, $options['whoCanHaveOwnSNAPAccs']) || $role=='administrator') {
                        echo ' checked="checked" ';
                    }
                    if ($role=='administrator' || $role=='subscriber') {
                        echo '  disabled="disabled" ';
                    }
                    echo 'name="whoCanHaveOwnSNAPAccs[]" value="'.esc_attr($role).'"> '.$name;
                    if ($role=='administrator') {
                        echo ' - Somebody who has access to all the administration features';
                    }
                    if ($role=='editor') {
                        echo " - Somebody who can publish and manage posts and pages as well as manage other users' posts, etc. ";
                    }
                    if ($role=='author') {
                        echo ' - Somebody who can publish and manage their own posts ';
                    }
                    if ($role=='contributor') {
                        echo ' - Somebody who can write and manage their posts but not publish them';
                    }
                    if ($role=='subscriber') {
                        echo ' - Somebody who can only manage their profile';
                    }
                    echo '<br/>';
                }
            } ?>
    
    
    
    
              </div>
              
           </div></div>      
           
             <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Interface', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
            <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('How to show the Networks List in the "Add New Network" dropdown', 'social-networks-auto-poster-facebook-twitter-g') ?></span> <br/>
              <div class="itemDiv">
              <input type="radio" name="showNTListCats" value="1" <?php if (!empty($options['showNTListCats'])) {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Categorized', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <?php _e('Please show supported networks with categories', 'social-networks-auto-poster-facebook-twitter-g') ?><br/>
              <input type="radio" name="showNTListCats" value="0" <?php if (empty($options['showNTListCats'])) {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Plain', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <?php _e('Please don\'t confuse me, just show the plain list', 'social-networks-auto-poster-facebook-twitter-g') ?>            
              </div>
              
              <br/><div class="itemDiv"><span><?php _e('How to show list of networks on the "Add New Post" page', 'social-networks-auto-poster-facebook-twitter-g') ?>:</span><br/>
              &nbsp;&nbsp;&nbsp;<input type="radio" name="howToShowNTS" value="C" <?php if (!empty($options['howToShowNTS']) && $options['howToShowNTS']=='C') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Collapsed', 'social-networks-auto-poster-facebook-twitter-g') ?>Collapsed.</b><br/>
              &nbsp;&nbsp;&nbsp;<input type="radio" name="howToShowNTS" value="E" <?php if (!empty($options['howToShowNTS']) && $options['howToShowNTS']=='E') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Expanded', 'social-networks-auto-poster-facebook-twitter-g') ?>.</b><br/>
              &nbsp;&nbsp;&nbsp;<input type="radio" name="howToShowNTS" value="M" <?php if (empty($options['howToShowNTS']) || $options['howToShowNTS']=='M') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Show checked networks expanded and unchecked collapsed', 'social-networks-auto-poster-facebook-twitter-g') ?>.</b><br/>
              </div>
              
              
               <div class="itemDiv">
              
             &nbsp;&nbsp;&nbsp;<input value="set" id="hideUnchecked" name="hideUnchecked"  type="checkbox" <?php if (!empty($options['hideUnchecked']) && (int)$options['hideUnchecked'] == 1) {
                echo "checked";
            } ?> />  <b><?php _e('Hide unchecked and filtered out networks', 'social-networks-auto-poster-facebook-twitter-g') ?></b>     
             <span style="font-size: 11px; margin-left: 1px;"><?php _e('Helps to remove clutter from "New post" page.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>  
              
              
              </div>
              
            </div></div>  
            
            
            
            
            
           
    <!-- #### Filters ##### --> 
                
   <?php //## Conver <3.5 settings to filters
   if (empty($options['fltrs']) || empty($options['fltrs']['nxs_post_type'])) {
       $options['fltrs']=array();
       $options['fltrs']['nxs_post_type'] = array();
       $options['fltrs']['nxs_post_type'][] = 'post';
       $options['fltrsOn']='1';
   }
            if (isset($options['exclCats'])) {
                $ccts = maybe_unserialize($options['exclCats']);
                $options['fltrsOn']='1';
                $options['fltrs']['nxs_ie_cats_names'] = 1;
                $options['fltrs']['nxs_cats_names'] = array_merge($ccts, $options['fltrs']['nxs_cats_names']);
                unset($options['exclCats']);
            }
            if (isset($options['nxsCPTSeld'])) {
                $ccts = maybe_unserialize($options['nxsCPTSeld']);
                $options['fltrs']['nxs_post_type'] = array_merge($ccts, $options['fltrs']['nxs_post_type']);
                unset($options['nxsCPTSeld']);
            }
            if (isset($options['useForPages']) && $options['useForPages'] =='1') {
                $options['fltrs']['nxs_post_type'][] = 'page';
                unset($options['useForPages']);
            } ?>
                
     <?php if (empty($options['fltrsOn'])) {
                $options['fltrsOn'] = '';
            }
            if (empty($options['fltrAfter'])) {
                $options['fltrAfter'] = '';
            } ?>                    
           <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Autoposting Filters', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>      
             <div class="nxs_box_inside"> 
             <div class="itemDiv" style="overflow: visible;"> 
               <span style="font-size: 11px; margin-left: 1px;"><?php _e('You can setup general criteria for what posts should be autoposted', 'social-networks-auto-poster-facebook-twitter-g') ?></span>  
                <div style="float: right;"> <a href="https://www.nextscripts.com/snap-features/filters" target="_blank">Instructions</a> </div><h3 style="padding-left: 0px;font-size: 16px;"> 
   <input value="1" name="fltrsOn" type="checkbox" onchange="if (jQuery(this).is(':checked')) jQuery('#nxs_flrts').show(); else jQuery('#nxs_flrts').hide();" <?php if ((int)$options['fltrsOn'] == 1) {
                echo "checked";
            } ?> /> 
   <?php  _e('Filter Posts (Only posts that meet the following criteria will be autoposted)', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><div id="nxs_flrts" style="margin-left: 30px;<?php if ((int)$options['fltrsOn'] != 1) {
                echo "display:none;";
            } ?>"> 

   <?php  nxs_Filters::print_posts_metabox(0, 'fltrs', '0', $options['fltrs']); ?><hr/>
   <!--
   <input value="1" name="fltrAfter" type="checkbox" <?php if ((int)$options['fltrAfter'] == 1) {
                echo "checked";
            } ?> /><?php  _e('[Not Recomended] Apply Filters at the time of autoposting, not at the time of publishing. Please use only if your posts are not ready at the time of publishing.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
   -->
   </div>
             
             </div>
             </div>           
           </div>
    <!-- ##################### URL Shortener #####################-->
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('URL Shortener', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
            <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;">Please use %SURL% in "Message Format" to get shortened urls or check "Force Shortened Links". </span> <br/>
              <!-- <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="G" <?php if (!isset($options['nxsURLShrtnr']) || $options['nxsURLShrtnr']=='' || $options['nxsURLShrtnr']=='G') {
                echo 'checked="checked"';
            } ?> /> <b>gd.is</b> (Default) - fast, simple, free, no configuration nessesary.            
              </div> -->
              <div class="itemDiv">
              
     <input type="checkbox" name="forceSURL" value="1" <?php if (isset($options['forceSURL']) && $options['forceSURL']=='1') {
                echo 'checked="checked"';
            } ?> /> <b><?php _e('Force Shortened Links', 'social-networks-auto-poster-facebook-twitter-g') ?></b>
     <br/><br/>         
              <input type="radio" name="nxsURLShrtnr" value="O" <?php if (!isset($options['nxsURLShrtnr']) || (isset($options['nxsURLShrtnr']) && ($options['nxsURLShrtnr']=='O' || $options['nxsURLShrtnr']=='G'))) {
                echo 'checked="checked"';
            } ?> /> <b>is.gd</b>&nbsp;[Default] <i>Simple, no additional configuration required.</i><br/>
              </div>
              
              <?php if (function_exists('wp_get_shortlink')) {
                ?><div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="W" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='W') {
                    echo 'checked="checked"';
                } ?> /> <b>Wordpress Built-in Shortener</b> (wp.me if you use Jetpack)<br/> 
              </div><?php
            } ?>
              <!-- ## bitly ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="B" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='B') {
                echo 'checked="checked"';
            } ?> /> <b>bit.ly</b>  - <i>Enter bit.ly username and <a target="_blank" href="http://bitly.com/a/your_api_key">API Key</a> below.</i> (<i style="font-size: 12px;">If https://bitly.com/a/your_api_key is not working, please go for the API key to "Your Account->Advanced Settings->API Support"</i>)<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bit.ly Username: <input name="bitlyUname" style="width: 20%;" value="<?php if (isset($options['bitlyUname'])) {
                _e(apply_filters('format_to_edit', $options['bitlyUname']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bit.ly&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="bitlyAPIKey" style="width: 20%;" value="<?php if (isset($options['bitlyAPIKey'])) {
                _e(apply_filters('format_to_edit', $options['bitlyAPIKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>
              
              <!-- ## u.to ##-->
              <div class="itemDiv">
                <input type="radio" name="nxsURLShrtnr" value="U" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='U') {
                echo 'checked="checked"';
            } ?> /> <b>u.to</b>  <i>Simple and anonymous (no accounts, no stats) use only, No additional configuration required.</i>
              </div>
              
              <!-- ## x.co ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="X" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='X') {
                echo 'checked="checked"';
            } ?> /> <b>x.co</b>  - <i>Enter x.co <a target="_blank" href="http://app.x.co/Settings.aspx">API Key</a> below. You can get API key from your x.co settings page: <a target="_blank" href="http://app.x.co/Settings.aspx">http://app.x.co/Settings.aspx</a>.</i><br/>              
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x.co&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="xcoAPIKey" style="width: 20%;" value="<?php if (isset($options['xcoAPIKey'])) {
                _e(apply_filters('format_to_edit', $options['xcoAPIKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>
              <!-- ## clk.im ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="C" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='C') {
                echo 'checked="checked"';
            } ?> /> <b>clk.im</b>  - <i>Enter clk.im <a target="_blank" href="http://clk.im/apikey">API Key</a> below. You can get API key from your clk.im page: <a target="_blank" href="http://clk.im/apikey">http://clk.im/apikey</a>. Please see the "Developers/Publishers" section on the right</i><br/>              
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;clk.im&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="clkimAPIKey" style="width: 20%;" value="<?php if (isset($options['clkimAPIKey'])) {
                _e(apply_filters('format_to_edit', $options['clkimAPIKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>
              <!-- ## po.st ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="P" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='P') {
                echo 'checked="checked"';
            } ?> /> <b>po.st</b>  - <i>Enter po.st <a target="_blank" href="https://re.po.st/partner/campaigns">API Key</a> below. You can get API key from your "Campaigns" page: <a target="_blank" href="https://re.po.st/partner/campaigns">https://re.po.st/partner/campaigns</a></i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;po.st&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="postAPIKey" style="width: 20%;" value="<?php if (isset($options['postAPIKey'])) {
                _e(apply_filters('format_to_edit', $options['postAPIKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>
              
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="A" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='A') {
                echo 'checked="checked"';
            } ?> /> <b>adf.ly</b>  - <i>Enter adf.ly user ID and <a target="_blank" href="https://adf.ly/publisher/tools#tools-api">API Key</a> below</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;adf.ly User ID: <input name="adflyUname" style="width: 20%;" value="<?php if (isset($options['bitlyUname'])) {
                _e(apply_filters('format_to_edit', $options['adflyUname']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;adf.ly&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="adflyAPIKey" style="width: 20%;" value="<?php if (isset($options['adflyAPIKey'])) {
                _e(apply_filters('format_to_edit', $options['adflyAPIKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
             <div style="width:100%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;adf.ly Domain: <select name="adflyDomain" id="adflyDomain">
            <?php  $adflyDomains = '<option value="adf.ly">adf.ly</option><option value="q.gs">q.gs</option>';
            if (isset($options['adflyDomain']) && $options['adflyDomain']!='') {
                $adflyDomains = str_replace($options['adflyDomain'].'"', $options['adflyDomain'].'" selected="selected"', $adflyDomains);
            }
            echo $adflyDomains; ?>
            </select> <i>Please note that j.gs is not availabe for API use.</i> </div>
              </div>
              
               <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="R" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='R') {
                echo 'checked="checked"';
            } ?> /> <b>Rebrandly</b>  - <i>Enter Rebrandly API Key and <a target="_blank" href="https://www.rebrandly.com/api-settings">API Key</a> and domain below. If domain is not set, rebrand.ly will be used</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rebrandly&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="rblyAPIKey" style="width: 20%;" value="<?php if (isset($options['rblyAPIKey'])) {
                _e(apply_filters('format_to_edit', $options['rblyAPIKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" /><br/>             
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rebrandly&nbsp;&nbsp;Domain:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="rblyDomain" style="width: 20%;" value="<?php if (isset($options['rblyDomain'])) {
                _e(apply_filters('format_to_edit', $options['rblyDomain']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />&nbsp; 
             </div>
              
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="Y" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='Y') {
                echo 'checked="checked"';
            } ?> /> <b>YOURLS (Your Own URL Shortener)</b> - 
            &nbsp;<i>YOURLS API URL - usually sonething like http://yourdomain.cc/yourls-api.php; YOURLS API Secret Signature Token can be found in your YOURLS Admin Panel-&gt;Tools</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YOURLS API URL: <input name="YOURLSURL" style="width: 19.4%;" value="<?php if (isset($options['YOURLSURL'])) {
                _e(apply_filters('format_to_edit', $options['YOURLSURL']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YOURLS API Secret Signature Token:&nbsp;&nbsp;&nbsp;<input name="YOURLSKey" style="width: 13%;" value="<?php if (isset($options['YOURLSKey'])) {
                _e(apply_filters('format_to_edit', $options['YOURLSKey']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>
              
            </div></div>
            
            <!-- ##################### Auto-Import comments from Social Networks #####################-->
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Auto-Import comments from Social Networks', 'social-networks-auto-poster-facebook-twitter-g') ?><span class="nxs_newLabel">[<?php _e('New', 'social-networks-auto-poster-facebook-twitter-g') ?>]</span></h3></div>
             <div class="nxs_box_inside"> 
             
             <?php $cr = get_option('NXS_cronCheck');
            if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') {
                ?> <span style="color: red"> *** <?php _e('Your WP Cron is not working correctly. This feature may not work properly, and might cause duplicate postings and stability problems.<br/> Please see the test results and recommendations here:', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl;
                echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span> <br/>
            <?php
            } ?>             
             
             <span style="font-size: 11px; margin-left: 1px;">Plugin will automatically grab the comments posted on Social Networks and insert them as "Comments to your post". Plugin will check for the new comments every hour. </span> <br/>
              <div class="itemDiv">
              <input value="set" id="riActive" name="riActive"  type="checkbox" <?php if (!empty($options['riActive']) && $options['riActive'] == '1') {
                echo "checked";
            } ?> /> 
              <strong>Enable "Comments Import"</strong>
              </div>
              <div class="itemDiv">  
             <strong style="font-size: 12px; margin: 10px; margin-left: 1px;">How many posts should be tracked:</strong>
<input name="riHowManyPostsToTrack" style="width: 50px;" value="<?php if (isset($options['riHowManyPostsToTrack'])) {
                _e(apply_filters('format_to_edit', $options['riHowManyPostsToTrack']), 'social-networks-auto-poster-facebook-twitter-g');
            } else {
                echo "10";
            } ?>" /> <br/>
              
             <span style="font-size: 11px; margin-left: 1px;">Setting two many will degrade your website's performance. 10-20 posts are recommended</span> 
              </div>
              <div class="itemDiv">  
             <strong style="font-size: 12px; margin: 10px; margin-left: 1px;">How Often should we check for new comments. Every:</strong>

             <select name="riHowOften" id="riHowOften">
              <option <?php if (empty($options['riHowOften']) || $options['riHowOften']=='15') {
                echo "selected";
            } ?> value="15">15 Minutes</option>
              <option <?php if (!empty($options['riHowOften']) && $options['riHowOften']=='30') {
                echo "selected";
            } ?> value ="30">30 Minutes</option>
              <option <?php if (!empty($options['riHowOften']) && $options['riHowOften']=='60') {
                echo "selected";
            } ?> value ="60">1 Hour</option>
              </select>

 <br/>
              
             <span style="font-size: 11px; margin-left: 1px;">Setting two many will degrade your website's performance. 10-20 posts are recommended</span> 
              </div>
              
           </div></div>
           
     <!-- ##################### URL Parameters #####################-->   
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('URL Parameters', 'social-networks-auto-poster-facebook-twitter-g') ?> <span class="nxs_newLabel">[<?php _e('New', 'social-networks-auto-poster-facebook-twitter-g') ?>]</span></h3></div>            
            
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Here you can set what should be done to backlinks.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
                <b><?php _e('Force HTTPS/SSL?', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/> <?php if (empty($options['forcessl'])) {
                $options['forcessl']  = 'D';
            } ?>
                <input type="radio" name="forcessl" value="D" <?php if ($options['forcessl'] == 'D') {
                echo 'checked="checked"';
            } ?> /> <?php _e('Don\'t do anything, let Wordpress to set the links.', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('All links will be exactly as provided by Wordpress', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                     
                <input type="radio" name="forcessl" value="S" <?php if ($options['forcessl'] == 'S') {
                echo 'checked="checked"';
            } ?> /> <?php _e('Force SSL', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('All links will be https/ssl', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                   
                <input type="radio" name="forcessl" value="N" <?php if ($options['forcessl'] == 'N') {
                echo 'checked="checked"';
            } ?> /> <?php _e('Force NON-SSL', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('All links will be http', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                    
              </div>               
              
              <div class="itemDiv">
                <b><?php _e('Additional URL Parameters:', 'social-networks-auto-poster-facebook-twitter-g') ?></b>  <input name="addURLParams" style="width: 800px;" value="<?php if (isset($options['addURLParams'])) {
                _e(apply_filters('format_to_edit', $options['addURLParams']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>               
             <span style="font-size: 11px; margin-left: 1px;"> <?php _e('You can use %NTNAME% for social network name, %NTCODE% for social network two-letter code, %ACCNAME% for account name,  %POSTID% for post ID,  %POSTTITLE% for post title, %SITENAME% for website name. <b>Any text must be URL Encoded</b><br/>Example: utm_source=%NTCODE%&utm_medium=%ACCNAME%&utm_campaign=SNAP%2Bfrom%2B%SITENAME%', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
           </div></div>  
           
           <!-- ##### HashTag Settings ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Auto-HashTags Settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('How to generate hashtags if tag is longer then one word', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('Spaces in hashtags', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <select name="nxsHTSpace" id="nxsHTSpace">
              <option <?php if (empty($options['nxsHTSpace'])) {
                echo "selected";
            } ?> value=""><?php _e('Remove Spaces', 'social-networks-auto-poster-facebook-twitter-g') ?></option>
              <option <?php if (!empty($options['nxsHTSpace']) && $options['nxsHTSpace']=='_') {
                echo "selected";
            } ?> value ="_"><?php _e('Replace spaces with _ (Underscore)', 'social-networks-auto-poster-facebook-twitter-g') ?></option>              
              </select>
              </div>   
               <span style="font-size: 11px; margin-left: 1px;"><?php _e('How to separate hashtags', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('Separate hashtags with ', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <select name="nxsHTSepar" id="nxsHTSepar">
              <option <?php if (!empty($options['nxsHTSepar']) && $options['nxsHTSepar']=='_') {
                echo "selected";
            } ?> value ="_">[ ] Space</option>
              <option <?php if (empty($options['nxsHTSepar']) || $options['nxsHTSepar']=='c_') {
                echo "selected";
            } ?> value="c_">[, ] Comma and Space</option>
              <option <?php if (!empty($options['nxsHTSepar']) && $options['nxsHTSepar']=='c') {
                echo "selected";
            } ?> value ="c">[,] Comma</option>              
              </select>
              </div>   
              
              <div class="itemDiv">
              <b><?php _e('Exclude tags from hashtags', 'social-networks-auto-poster-facebook-twitter-g') ?></b><input name="tagsExclFrmHT" style="width: 800px;" value="<?php if (isset($options['tagsExclFrmHT'])) {
                _e(apply_filters('format_to_edit', $options['tagsExclFrmHT']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>   
                       
           </div></div> 
           
            <!-- ##### ANOUNCE TAG ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('%ANNOUNCE% tag settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Plugin will take text untill the &lt;!--more--&gt; tag. Please specify how many characters should it get if &lt;!--more--&gt; tag is not found', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('How many characters:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="anounTagLimit" style="width: 100px;" value="<?php if (isset($options['anounTagLimit'])) {
                _e(apply_filters('format_to_edit', $options['anounTagLimit']), 'social-networks-auto-poster-facebook-twitter-g');
            } else {
                echo "300";
            } ?>" />              
              </div>              
           </div></div>  
                           
     <!-- ##################### Open Graph #####################-->
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('"Open Graph" Tags', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('SNAP has a very simple implementation of the "Open Graph" metatags. Please see here for more info - SNAP Open Graph Tags', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>              
              <div class="itemDiv"> <?php
                $ogMsgT =  __('plugin has been detected', 'social-networks-auto-poster-facebook-twitter-g').' '.__('It has support for OG Metatags. Please disable SNAP OG Tags to avoid conflicts.', 'social-networks-auto-poster-facebook-twitter-g');
            $ogMsg = '';
                
            if (function_exists('wpfbogp_start_ob')) {
                $ogMsg = '"WP Facebook Open Graph protocol"';
            }
            if (function_exists('webdados_fb_run')) {
                $ogMsg = '"Facebook Open Graph, Google+ and Twitter Card Tags"';
            }
            if (function_exists('fbogmeta_header')) {
                $ogMsg = '"Facebook Featured Image and Open Graph Meta Tags"';
            }
            if (function_exists('sfogi_wp_head')) {
                $ogMsg = '"Simple Facebook OG image"';
            }
                
            if (class_exists('Ngfb')) {
                $ogMsg = '"NextGEN Facebook"';
            }
            if (class_exists('Wpsso')) {
                $ogMsg = '"WPSSO (Core Plugin)"';
            }
            if (class_exists('Open_Graph_Pro')) {
                $ogMsg = '"Open Graph Pro"';
            }
            if (class_exists('NY_OG_Output')) {
                $ogMsg = '"WP Open Graph"';
            }
            if (class_exists('iworks_opengraph')) {
                $ogMsg = '"OG (Very tiny Open Graph plugin)"';
            }
            if (class_exists('OpenGraphMetabox')) {
                $ogMsg = '"Open Graph Metabox"';
            }
                
            if (function_exists('wpseo_auto_load')) {
                $ogMsg = '"Yoast SEO"';
            }
            if (function_exists('aioseop_activate')) {
                $ogMsg = '"All In One SEO Pack"';
            }
            if (function_exists('the_seo_framework_pre_load')) {
                $ogMsg = '"The SEO Framework"';
            }
                
                
            if (!empty($ogMsg)) {
                echo '<div style="color:darkred;">'.$ogMsg.' '.$ogMsgT.'</div></br>';
            } ?>
              <input value="A" id="nsOpenGraph" name="nxsOG"  type="radio" <?php if ($options['nxsOG'] == 'A') {
                echo "checked";
            } ?> /> <b><?php _e('Add Open Graph Tags', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>
              <input value="N" id="nsOpenGraph" name="nxsOG"  type="radio" <?php if ($options['nxsOG'] == 'N') {
                echo "checked";
            } ?> /> <b><?php _e('Add Open Graph Tags only when nessesary', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>
              <input value="D" id="nsOpenGraph" name="nxsOG"  type="radio" <?php if ($options['nxsOG'] == 'D') {
                echo "checked";
            } ?> /> <b><?php _e('Do not add Open Graph Tags', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>
              </div>                           
              <div class="itemDiv">
             <b><?php _e('Default Image URL for og:image tag:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> 
            <input name="ogImgDef" style="width: 30%;" value="<?php if (isset($options['ogImgDef'])) {
                _e(apply_filters('format_to_edit', $options['ogImgDef']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>             
              <div class="itemDiv">
             <b><?php _e('Author\'s Facebook URL', 'social-networks-auto-poster-facebook-twitter-g') ?></b> 
             <input name="ogAuthorFB" style="width: 30%;" value="<?php if (isset($options['ogAuthorFB'])) {
                _e(apply_filters('format_to_edit', $options['ogAuthorFB']), 'social-networks-auto-poster-facebook-twitter-g');
            } ?>" />
              </div>              
           </div></div>    
            <!-- #### "Featured" Image ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Advanced "Featured" Image Settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> 
              <div class="itemDiv">
              <input value="set" id="imgNoCheck" name="imgNoCheck"  type="checkbox" <?php /* ## Reversed Intentionally!!! */ if (empty($options['imgNoCheck']) || (int)$options['imgNoCheck'] != 1) {
                echo "checked";
            } ?> /> <strong>Verify "Featured" Image</strong>             
              <br/><span style="font-size: 11px; margin-left: 1px;"><?php _e('Advanced Setting. Uncheck only if you are 100% sure that your images are valid or if you have troubles with image verification.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              </div>
              
               <div class="itemDiv">
             <input value="1" id="useUnProc" name="useUnProc"  type="checkbox" <?php if (isset($options['useUnProc']) && (int)$options['useUnProc'] == 1) {
                echo "checked";
            } ?> /> 
             <b><?php _e('Use advanced image finder', 'social-networks-auto-poster-facebook-twitter-g') ?></b>
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;"> <?php _e('Check this if your images could be found only in the fully processed posts. <br/>This feature could interfere with some plugins using post processing functions incorrectly. Your site could become messed up, have troubles displaying content or start giving you "ob_start() [ref.outcontrol]: Cannot use output buffering in output buffering display handlers" errors.', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div> 
              
              <div class="itemDiv"> 
             <b><?php _e('If there is a choice what image size should be used:', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>              
             <b>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Image posts:', 'social-networks-auto-poster-facebook-twitter-g') ?></b><select name="imgSizeImg">
             <?php  $imgSizes = nxs_getImageSizes();
            if (empty($options['imgSizeImg'])) {
                $options['imgSizeImg'] = 'full';
            } ?>
             <option <?php if ($options['imgSizeImg']=='full') {
                echo "selected";
            } ?> value ="full"><?php _e('Original Size'); ?></option>
             <?php
             foreach ($imgSizes as $sn=>$sa) {
                 ?><option <?php if ($options['imgSizeImg']==$sn) {
                     echo "selected";
                 } ?> value="<?php echo $sn; ?>"><?php echo ucfirst($sn); ?>&nbsp;(<?php echo $sa['width']." x ".$sa['height']; ?>)</option>
             <?php
             } ?>
              </select> <br/>
             <b>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Attachment images:', 'social-networks-auto-poster-facebook-twitter-g') ?></b><select name="imgSizeAttch">
             <?php if (empty($options['imgSizeAttch'])) {
                 $options['imgSizeAttch'] = 'medium';
             } ?>
             <option <?php if ($options['imgSizeAttch']=='full') {
                 echo "selected";
             } ?> value="full"><?php _e('Original Size'); ?></option>
             <?php
             foreach ($imgSizes as $sn=>$sa) {
                 ?><option <?php if ($options['imgSizeAttch']==$sn) {
                     echo "selected";
                 } ?> value="<?php echo $sn; ?>"><?php echo ucfirst($sn); ?>&nbsp;(<?php echo $sa['width']." x ".$sa['height']; ?>)</option>
             <?php
             } ?>
              </select>           
              
              </div>  
              
           </div></div>        
    
      <!-- ##### Alternative "Featured Image" location ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Alternative "Featured Image" location', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Plugin uses standard Wordpress "Featured Image" by default. If your theme stores "Featured Image" in the custom field, please enter the name of it. Use prefix if your custom field has only partial location.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('Custom field name:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="featImgLoc" style="width: 200px;" value="<?php if (isset($options['featImgLoc'])) {
                 _e(apply_filters('format_to_edit', $options['featImgLoc']), 'social-networks-auto-poster-facebook-twitter-g');
             } ?>" />
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;"><?php _e('Set the name of the custom field that contains image info', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div>
              <div class="itemDiv">
             <b><?php _e('Custom field Array Path:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="featImgLocArrPath" style="width: 200px;" value="<?php if (isset($options['featImgLocArrPath'])) {
                 _e(apply_filters('format_to_edit', $options['featImgLocArrPath']), 'social-networks-auto-poster-facebook-twitter-g');
             } ?>" /> 
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;">[<?php _e('Optional', 'social-networks-auto-poster-facebook-twitter-g') ?>] <?php _e('If your custom field contain an array, please enter the path to the image field. For example: [\'images\'][\'image\']', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div>
              <div class="itemDiv">
             <b><?php _e('Custom field Image Prefix:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="featImgLocPrefix" style="width: 200px;" value="<?php if (isset($options['featImgLocPrefix'])) {
                 _e(apply_filters('format_to_edit', $options['featImgLocPrefix']), 'social-networks-auto-poster-facebook-twitter-g');
             } ?>" /> 
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;">[<?php _e('Optional', 'social-networks-auto-poster-facebook-twitter-g') ?>] <?php _e('If your custom field contain only the last part of the image path, please enter the prefix', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div>
           </div></div>    
           
            <!-- ##### Ext Debug/Report Settings ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('System Settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Report Settings', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
             
             <div class="itemDiv">
                <b><?php _e('Check Query Every (Sec):', 'social-networks-auto-poster-facebook-twitter-g') ?></b> 
                <input name="queryInterval" style="width: 50px;" value="<?php if (isset($options['queryInterval'])) {
                 _e(apply_filters('format_to_edit', $options['queryInterval']), 'social-networks-auto-poster-facebook-twitter-g');
             } else {
                 echo "60";
             } ?>" />
                <span style="font-size: 11px; margin-left: 1px;"><?php _e('60 seconds is optimal for most sites. Please set 30 seconds if you post a lot of posts, 120 if you post rarely. Do not set more then 600 seconds and less then 30 seconds.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              </div>
              
             <div class="itemDiv">
                <b><?php _e('Number of Query Tasks to process each time:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> 
                <input name="numOfTasks" style="width: 50px;" value="<?php if (isset($options['numOfTasks'])) {
                 _e(apply_filters('format_to_edit', $options['numOfTasks']), 'social-networks-auto-poster-facebook-twitter-g');
             } else {
                 echo "30";
             } ?>" />
                <span style="font-size: 11px; margin-left: 1px;"><?php _e('30 is fine for most sites. Increase if you have a lot of accounts and busy and powerfiul server.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              </div> 
                          
             <div class="itemDiv">
               <b><?php _e('Number of Log records to keep:', 'social-networks-auto-poster-facebook-twitter-g') ?></b>
               <input name="numLogRows" style="width: 50px;" value="<?php if (isset($options['numLogRows'])) {
                 _e(apply_filters('format_to_edit', $options['numLogRows']), 'social-networks-auto-poster-facebook-twitter-g');
             } else {
                 echo "1000";
             } ?>" />
               <br/>
              </div>
             
             <div class="itemDiv">
             <input value="1" id="brokenCntFilters" name="brokenCntFilters"  type="checkbox" <?php if (isset($options['brokenCntFilters']) && (int)$options['brokenCntFilters'] == 1) {
                 echo "checked";
             } ?> /> 
              <strong>My Content Filters (<i>apply_filters('the_content'</i>) are broken, don't use them</strong>
               - <span style="font-size: 11px; margin-left: 1px;"><?php _e('Some third party plugin break content filters. Check this if some networks do not post silently(without any errors in the log). This will make %EXCERPT% work as %RAWEXCERPT%, %FULLTEXT% as %RAWTEXT%, etc... ', 'social-networks-auto-poster-facebook-twitter-g') ?></span>               
              <br/>                             
              </div>
              
              <div class="itemDiv">
              <input value="set" id="errNotifEmailCB" name="errNotifEmailCB"  type="checkbox" <?php if (isset($options['errNotifEmailCB']) && (int)$options['errNotifEmailCB'] == 1) {
                 echo "checked";
             } ?> /> 
              <strong>Send Email notification for errors</strong>
               - <span style="font-size: 11px; margin-left: 1px;"><?php _e('Send Email notification for all autoposting errors. No more then one email per hour will be sent.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>               
              <br/>               
              <div style="margin-left: 18px;">
              <b><?php _e('Email:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="errNotifEmail" style="width: 200px;" value="<?php if (isset($options['errNotifEmail'])) {
                 _e(apply_filters('format_to_edit', $options['errNotifEmail']), 'social-networks-auto-poster-facebook-twitter-g');
             } ?>" />
              <span style="font-size: 11px; margin-left: 1px;"><?php _e('wp_mail will be used. Some email providers (gmail, hotmail) might have problems getting such mail', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              </div>
              </div>
              
              <?php $cr = get_option('NXS_cronCheck');
            if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') {
                ?> 
                <div class="itemDiv">             
             <span style="color: red"> *** <?php _e('Your WP Cron is not working correctly.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
             &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl;
                echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span> <br/>             
              <input value="set" id="forceBrokenCron" name="forceBrokenCron"  type="checkbox" <?php if (isset($options['forceBrokenCron']) && (int)$options['forceBrokenCron'] == 1) {
                    echo "checked";
                } ?> /> 
              <strong>Enable Cron functions even if WP Cron is not working correctly.</strong>
               <br/><span style="color:red; font-weight: bold;"><?php _e('I understand that this could cause duplicate postings as well as performance and stability problems.', 'social-networks-auto-poster-facebook-twitter-g') ?></span> - 
               <span style="margin-left: 1px; color:red;"><?php _e('Please do not check this unless you absolutely sure that you know what are you doing.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>
               <br/><span style="margin-left: 1px; color:#005800;"><?php _e('Setting up WP Cron correctly will be much better solution:', 'social-networks-auto-poster-facebook-twitter-g') ?>
                 <a href="https://www.nextscripts.com/tutorials/wp-cron-scheduling-tasks-in-wordpress/" target="_blank">WP-Cron: Scheduling Tasks in WordPress</a>
               </span>
               
               </div>              
             <?php
            } ?> 
              
           </div></div>               
    
           
     
     <?php if (function_exists("nxs_showPRXTab")) {
                ?>          
      <h3 style="font-size: 14px; margin-bottom: 2px;">Show "Proxies" Tab</h3>             
        <p style="margin: 0px;margin-left: 5px;"><input value="set" id="showPrxTab" name="showPrxTab"  type="checkbox" <?php if ((int)$options['showPrxTab'] == 1) {
                    echo "checked";
                } ?> /> 
          <strong>Show "Proxies" Tab</strong> <span style="font-size: 11px; margin-left: 1px;">Advanced Setting. Check to enable "Proxies" tab where you can setup autoposting proxies.</span>            
        </p>    
      <?php
            } ?>       
           
      <div class="submitX nxclear" style="padding-bottom: 10px;">       
        <div style="display:inline-block;"><input type="button" id="svBtnSettingsTop" onclick="nxs_savePluginSettings();" class="button-primary" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" /></div>
        <div style="display:inline-block;"><div class="nxsSvSettingsAjax" style="display: none; width:200px;"> <img  src="<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif" /> <?php _e('Saving....', 'social-networks-auto-poster-facebook-twitter-g') ?></div></div> 
        <div style="display:inline-block;"><div class="doneMsg"><?php _e('Saved', 'social-networks-auto-poster-facebook-twitter-g') ?></div></div>
      </div>
      
      </div>
      </form>
            
            <?php
        }
        
        public function showLogHistoryTab()
        {
            global $nxs_snapAvNts, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU;
            $nxsOne = '';
            $options = $this->nxs_options;
            $uidQ = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? ' WHERE uid = '.get_current_user_id().' ' : ''; ?><div style="width:99%;">
    
    <div style="float: right"><a href="#" onclick="nxs_rfLog();return false;" class="NXSButton" id="nxs_clearLog">Refresh</a></div>
    <div style="float: right">Show: <input type="checkbox" id="nxs_shLogSE" checked="checked" onchange="nxs_rfLog();">SNAP Errors&nbsp;&nbsp;<input type="checkbox" id="nxs_shLogSI" checked="checked" onchange="nxs_rfLog();">SNAP Events&nbsp;&nbsp;
      <input type="checkbox" id="nxs_shLogCE" checked="checked" onchange="nxs_rfLog();">Cron Warnings&nbsp;&nbsp;<input type="checkbox" id="nxs_shLogCI" onchange="nxs_rfLog();">All Cron Events&nbsp;&nbsp;<input type="checkbox" id="nxs_shLogSY" onchange="nxs_rfLog();">System Events&nbsp;&nbsp;       
    </div>
    
    Showing last 150 records &nbsp;&nbsp;&nbsp;<a href="#" onclick="nxs_clLog();return false;" class="NXSButton" id="nxs_clearLog">Clear Log</a><br/><br/>    
      <div style="overflow: auto; border: 1px solid #999; width: 100%; height: 800px; font-size: 11px;" class="logDiv" id="nxslogDiv">
        <?php //$logInfo = maybe_unserialize(get_option('NS_SNAutoPosterLog'));
        $logInfo = nxs_getnxsLog(array(1,1,1,0,0));  //  prr($logInfo);
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
                if (empty($uidQ) && !empty($logline['uid'])) {
                    $uu = ' [User ID:'.$logline['uid'].']';
                } else {
                    $uu = '';
                }
                echo '<snap style="color:#008000">['.$logline['date'].']</snap>'.$uu.' - <snap style="'.$actSt.'">['.$logline['act'].']</snap>'.$ntInfo.'-  <snap style="'.$msgSt.'">'.$logline['msg'].'</snap> '.$logline['extInfo'].'<br/>';
            }
        } ?>
      </div>                  
    </div> <?php
        }
        public function showQueryTab()
        {
            global $wpdb, $nxs_snapAvNts, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU;
            $nxsOne = '';
            $options = $this->nxs_options;
            $uidQ = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? ' WHERE uid = '.get_current_user_id().' ' : ''; //echo "SELECT * FROM ". $wpdb->prefix . "nxs_query ".$uidQ." ORDER BY timetorun DESC";
            $quPosts = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "nxs_query ".$uidQ." ORDER BY timetorun DESC", ARRAY_A); ?>
         <div style="width:99%;">
    <a href="#" style="float: right" onclick="nxs_rfLog();return false;" class="NXSButton" id="nxs_clearLog">Refresh</a>
      <?php if (!is_array($quPosts)) {
                $quPosts = array();
            }
            if (count($quPosts)>0) {
                ?><div>
      <?php _e('Future Posts Timeline', 'social-networks-auto-poster-facebook-twitter-g') ?>&nbsp;(<?php _e('Time Now:', 'social-networks-auto-poster-facebook-twitter-g');
                echo '&nbsp;'.date_i18n('Y-m-d H:i'); ?>)
      
      <?php $tmL = get_option('nxs_last_nxs_cron');
                if (!empty($tmL)) {
                    _e('Last Cron Execution Time:', 'social-networks-auto-poster-facebook-twitter-g');
                    $tmCorr = get_option('gmt_offset') * HOUR_IN_SECONDS;
                    echo '&nbsp;'.date_i18n('Y-m-d H:i', $tmL+$tmCorr);
                    $td = time()-$tmL;
                    if ($td>3600) {
                        echo '<br/><span style="color:red;font-weight:bold;">&nbsp;&nbsp;';
                        _e('Problem: Cron was not executed in the last hour. Query is not running.', 'social-networks-auto-poster-facebook-twitter-g');
                        echo '[<a target="_blank" href="http://gd.is/nttg">More info</a>]';
                        echo '</span>';
                    } elseif ($td>600) {
                        echo '<span style="color:orange;">&nbsp;&nbsp;';
                        _e('Warning: Cron was not executed in the last 5 minutes', 'social-networks-auto-poster-facebook-twitter-g');
                        echo '</span>';
                    }
                } ?>      
      </div>
      <div style="overflow: auto; border: 1px solid #999; width: 99%; height: 800px; font-size: 11px;" class="logDiv" id="nxsQUDiv">
      <?php //prr($quPosts);
         if (is_array($quPosts)) {
             foreach (array_reverse($quPosts) as $logline) {
                 $btns = '';
                 $actSt = '';
                 $typeTXT = '';
                 $pstLine = '';
                 if (!empty($logline['postid'])) {
                     $post = get_post($logline['postid']);
                     if (empty($post)) {
                         continue;
                     }
                     $pstLine = $logline['postid'].' - '.$post->post_title;
                     ;
                 } else {
                     $pstLine = $logline['descr'];
                 }
                 $btnC = '<a href="#" id="nxs_PQ_'.$logline['id'].'" class="nxs_Cancel_Q">[Cancel]</a>';
                 switch ($logline['type']) {
              case 'Q': $typeTXT = 'Queried Post'; $actSt = "color:#0000FF;"; $btns = $btnC;

              break;
              case 'S': /* prr($logline); */ $typeTXT = 'Scheduled Autopost to '.$logline['nttype'].' - '.$logline['descr']; $actSt = "color:#DB7224;"; $btns = $btnC;

              break;
              case 'R': $typeTXT = 'Next Post from '.$logline['descr']; $actSt = "color:#0000FF;"; $btns = $btnC;

              break;
              case 'F': $typeTXT = 'Scheduled "Quick Form" Post'.$logline['refid'] ; $actSt = "color:#005800;"; $btns = $btns = $btnC.'&nbsp;<a href="#" >[Post NOW]</a>';

              break;
            }
                 $userInfo = (empty($uidQ) && !empty($logline['uid']))?'&nbsp;User ID: '.$logline['uid'].'&nbsp;':'';
                 echo '<div id="nxs_QU_'.$logline['id'].'"><snap style="color:#008000">['.$logline['timetorun'].']</snap> '.$btns.$userInfo.' - <snap style="'.$actSt.'">['.$typeTXT.']</snap>&nbsp;'.$pstLine.'<br/></div>';
             }
         } ?>
      </div>
      <?php
            } else {
                _e('Query is empty', 'social-networks-auto-poster-facebook-twitter-g');
            } //prr($quPosts);
      
      ?>
      
    </div>        
        <?php
        }
        
        public function showAboutTab()
        {
            global $nxs_snapAvNts, $nxs_snapThisPageUrl, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU;
            $nxsOne = '';
            $options = $this->nxs_options;
            $nxsVer = NextScripts_SNAP_Version;
            if (defined('NXSAPIVER')) {
                $nxsVer .= " (<span id='nxsAPIUpd'>API</span> Version: ".NXSAPIVER.")";
            } ?>
    <div style="max-width:1000px;"> 
        
        <?php  $nxsVer = NextScripts_SNAP_Version;
            if (defined('NXSAPIVER')) {
                $nxsVer .= " (<span id='nxsAPIUpd'>API</span> Version: ".NXSAPIVER.")";
            }

            _e('Plugin Version', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <span style="color:#008000;font-weight: bold;"><?php echo $nxsVer; ?></span> <?php if ($this->sMode['l']=='P') {
                ?> [Pro Edition]&nbsp;&nbsp;<?php
            } elseif ($this->sMode['l']=='M') {
                ?> [Pro Multiuser Edition]&nbsp;&nbsp;<?php
            } else {
                ?>
           <span style="color:#800000; font-weight: bold;">[Free - One account per network edition]</span> <?php
            } ?><br/>
<?php  global $nxs_apiLInfo;
            if (isset($nxs_apiLInfo) && !empty($nxs_apiLInfo)) {
                if ($nxs_apiLInfo['1']==$nxs_apiLInfo['2'] || empty($nxs_apiLInfo['2'])) {
                    echo $nxs_apiLInfo['1'];
                } else {
                    echo "<b>API:</b> (Google+, Pinterest, LinkedIn, Reddit, Flipboard): ".$nxs_apiLInfo['1']."<br/><b>API:</b> (Instragram): ".$nxs_apiLInfo['2'];
                }
                echo "&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            if (defined('NXSAPIVER')) {
                ?><br/>
  <img id="checkAPI2xLoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' /><a href="" id="checkAPI2x">[Check for API Update]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="showLic">[Change Activation Key]</a> <br/><br/>
<?php
            } elseif (defined('NextScripts_UPG_SNAP_Version')) {
                ?> <br/><span style="color:red;">You have "SNAP Upgrade helper" installed, now please&nbsp;<a href="#" class="showLic">[Enter Activation Key]</a></span><br/><br/> <?php
            } ?><br/>
        
            
        <div class="nxscontainer">
         
          <div class="nxsleft">
            <h3 style="margin-top: 0px; padding-left: 0px; font-size: 18px;"><?php _e('System Tests', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>            
            <h5 style="margin-top: 5px; margin-bottom: 2px; padding-left: 0px; font-size: 18px;"><?php _e('System', 'social-networks-auto-poster-facebook-twitter-g'); ?></h5>
            <div style="padding-bottom: 10px;"><?php nxs_memCheck(); ?></div>
            <h5 style="margin-top: 5px; margin-bottom: 2px; padding-left: 0px; font-size: 18px;"><?php _e('Cron', 'social-networks-auto-poster-facebook-twitter-g'); ?></h5>
            
            <div style="padding-bottom: 10px;">Internal WP Cron execution:&nbsp;<?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==true) {
                echo "Disabled - OK";
            } else {
                echo "Enabled - OK, but not reccomended";
            } ?><br/>
            SNAP Query Cron Event:&nbsp;<?php if (wp_next_scheduled('nxs_querypost_event')) {
                echo "Shedulled - OK";
            } else {
                echo "NOT Found";
            } ?>&nbsp;(Last Cron Execution:&nbsp;<?php $tmCorr = get_option('gmt_offset') * HOUR_IN_SECONDS ;
            $tmL = get_option('nxs_last_nxs_cron');
            echo date_i18n('Y-m-d H:i:s', $tmL+$tmCorr)." - ";
            $tm = microtime();
            $tma = explode(' ', $tm);
            $tm = number_format((float)$tma[0]+(float)$tma[1], 8, '.', '');
            $currTime = $tm+$tmCorr;
            echo number_format(($tm-$tmL), 2, '.', '').'sec ago';
            if (($tm-$tmL)>60) {
                echo '&nbsp;<span style="color:red;"> - NOT OK</span>';
            }
            echo "<br/>"; ?><a style="font-weight: normal; font-size: 16px; line-height: 24px;" target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=crtest">Show Cron Test Results</a><br/></div>                       
            
            
            <h5 style="margin-top: 5px; margin-bottom: 2px; padding-left: 0px; font-size: 18px;"><?php _e('Connections', 'social-networks-auto-poster-facebook-twitter-g'); ?></h5>
            
            
            <a style="font-weight: normal; font-size: 16px; line-height: 24px;" target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=test">Check HTTPS/SSL Connections</a><br/>
          
            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Plugin Features Documentation', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/snap-features/">All SNAP Features</a><br/>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/instructions/"><?php _e('Setup/Installation Instructions for each network', 'social-networks-auto-poster-facebook-twitter-g'); ?></a><br/>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/tutorials/"><?php _e('Setup/Configuration/Usage Tutorials', 'social-networks-auto-poster-facebook-twitter-g'); ?></a><br/>
    
            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('General Questions', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/faq">FAQ</a><br/>

            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Solutions for the most common problems', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/support-faq/">Troubleshooting FAQ</a><br/>

            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Have troubles/problems/found a bug?', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/support">===&gt; Open support ticket &lt;===</a>


            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Have questions/suggestions?', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/contact-us">===&gt; Contact us &lt;===</a> <br/>

            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;;"><?php _e('Like the Plugin? Would you like to support developers?', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3>
              <div style="line-height: 24px;">
              <b>Here is what you can do:</b><br/>
              <?php if (class_exists('nxsAPI_GP')) {
                ?><s><?php
            } ?><img src="<?php echo NXS_PLURL; ?>img/snap-icon12.png"/> Get the <a href="https://www.nextscripts.com/social-networks-autoposter-wordpress-plugin-pro/#getit">"Pro" Edition</a>. You will be able to add several accounts for each network as well as post to Google+, Pinterest and LinkedIn company pages.<?php if (class_exists('nxsAPI_GP')) {
                ?></s> <i>Done! Thank you!</i><?php
            } ?><br/>
              <img src="<?php echo NXS_PLURL; ?>img/snap-icon12.png"/> Rate the plugin 5 stars at <a href="http://wordpress.org/extend/plugins/social-networks-auto-poster-facebook-twitter-g/">wordpress.org page</a>.<br/>
              <img src="<?php echo NXS_PLURL; ?>img/snap-icon12.png"/> <a href="<?php echo nxs_get_admin_url(); ?>post-new.php">Write a blogpost</a> about the plugin and don't forget to auto-post this blogpost to all your social networks ;-).<br/>
            </div>
          </div>
        </div><br style="clear: both;"/>
        <div style="width:100%">       
          <h4><?php _e('Some evil buttons:', 'social-networks-auto-poster-facebook-twitter-g'); ?> (<?php _e("Don't click unless you know what are you doing", 'social-networks-auto-poster-facebook-twitter-g'); ?>)</h4>
          
   &nbsp;&nbsp;<a id="nxs_accsFltToAll" href="#">[<?php _e('Set "Post Type" Filter for all accounts to "Post" only', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('this will set "Post Type" filter for all accounts to "Post" only.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
          
   &nbsp;&nbsp;<a id="nxs_restBackup" href="#">[<?php _e('Restore settings from the last backup', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('this will replace your current settings and configured networks with backup saved on', 'social-networks-auto-poster-facebook-twitter-g'); ?> <b><?php $offSet = (get_option('gmt_offset') * HOUR_IN_SECONDS);
            echo date("F j, Y, g:i a", get_option('nxs_lBckTime')+ $offSet); ?>.</b> <br/>
   &nbsp;&nbsp;<a id="nxs_resetSNAPInfoPosts" href="#">[<?php _e('Remove all SNAP metainfo in the posts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('this will remove all SNAP data that was saved in posts.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
   &nbsp;&nbsp;<a id="nxs_resetSNAPQuery" href="#">[<?php _e('Clear Query', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('this will remove all pending posts from your query.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
   &nbsp;&nbsp;<a id="nxs_resetSNAPCron" href="#">[<?php _e('Remove all pending SNAP Cron Tasks', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('this will delete all pending SNAP Cron tasks.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
   &nbsp;&nbsp;<a id="nxs_resetSNAPCache" href="#">[<?php _e('Clear Cache', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('this will cached networks data.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
&nbsp;&nbsp;<a id="nxs_deleteAllSNAPInfo" <?php echo is_network_admin()?'data-mu="mu"':''; ?> href="#">[<?php _e('Delete all SNAP Data', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a> - <?php _e('This is a complete "Start Over". This will delete all SNAP data from the posts and all SNAP settings including all configured networks.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
          
        </div>
    </div><?php
        }
        
        public function NS_SNAP_AddPostMetaTags()
        {
            global $post, $nxs_snapAvNts;
            $post_id = $post;
            if (is_object($post_id)) {
                $post_id = $post_id->ID;
            }
            if (!is_object($post) || empty($post->post_status)) {
                $post = get_post($post_id);
            }
            $postType = $post->post_type;
            $accts = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? $this->nxs_acctsU :  $this->nxs_accts;
            $options = $this->nxs_options; ?>
          <style type="text/css">div#popShAtt {display: none; position: absolute; width: 600px; padding: 10px; background: #eeeeee; color: #000000; border: 1px solid #1a1a1a; font-size: 90%; }
            .underdash {border-bottom: 1px #21759B dashed; text-decoration:none;} .underdash a:hover {border-bottom: 1px #21759B dashed}
          </style>

       <div id="NXS_MetaFields" class="NXSpostbox">  <input value="'" type="hidden" name="nxs_mqTest" /> <input value="" type="hidden" id="nxs_snapPostOptions" name="nxs_snapPostOptions" />
         <div id="nxs_gPopup"><span class="nxspButton bClose"><span>X</span></span><div id="nxs_gPopupContentX"></div></div>         
         <div id="NXS_MetaFieldsIN" class="NXSpostbox">
       <?php /* ################## WHAT URL to USE */ ######################?>
          <div style="text-align: left; font-size: 14px; " class="showURL">
          <div class="inside" style="border: 1px #E0E0E0 solid; padding: 10px;"><div id="postftfp">
          
          <b>URL to use for links, attachments and %MYURL%:&nbsp;</b>    
          <?php if ($post->post_status != "auto-draft") {
                ?>
          <div style="float: right;"> <?php if ($post->post_status == "publish") {
                    ?><a href="#" class="NXSButtonSm manualAllPostBtn" onclick="return false;">Post to All Checked Networks</a><?php
                } ?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonSm" onclick="nxs_doResetPostSettings('<?php echo $post_id; ?>'); return false;">Reset all SNAP data</a></div>
          <?php
            } ?>
          
          <input type="checkbox" class="isAutoURL" <?php  $forceSURL = get_post_meta($post_id, '_snap_forceSURL', true);
            if (empty($forceSURL) && !empty($this->nxs_options['forceSURL']) || $forceSURL=='1') {
                ?>checked="checked"<?php
            } ?>  id="useSURL" name="useSURL" value="1"/> <?php _e('Shorten URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>
          &nbsp;&nbsp;&nbsp;  
          <input type="checkbox" class="isAutoURL" <?php $urlToUse = get_post_meta($post_id, 'snap_MYURL', true);
            if ($urlToUse=='') {
                ?>checked="checked"<?php
            } ?>  id="isAutoURL-" name="isAutoURL" value="A"/> <?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post URL will be used', 'social-networks-auto-poster-facebook-twitter-g'); ?></i>                  
                    <div class="nxs_prevURLDiv" <?php if (trim($urlToUse)=='') {
                ?> style="display:none;"<?php
            } ?> id="isAutoURLFld-">
                      &nbsp;&nbsp;&nbsp;<?php _e('URL:', 'social-networks-auto-poster-facebook-twitter-g') ?> <input size="90" type="text" name="urlToUse" value="<?php echo $urlToUse ?>" id="URLToUse" /> 
                    </div>
          </div></div></div>
          <div id="NXS_MetaFieldsBox" class="postbox" style="border: 0px #E0E0E0 solid;"><div class="inside" style="padding-left:0px; padding-right:0px; border: 0px #E0E0E0 solid;"><div id="postftfp"> <input value="1" type="hidden" name="snapEdIT" />   
          <div class="popShAtt" style="width: 200px;" id="popShAttFLT"><?php _e('Filters are "ON". Will be posted or skipped based on filters', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
          <div class="popShAtt" style="width: 200px;" id="popShAttSV"><?php _e('If you made any changes to the format, please "Update" the post before reposting', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
          
          <script type="text/javascript">
          jQuery(document).ready(function($) {<?php  if (!empty($options['howToShowNTS']) && $options['howToShowNTS']=='C') {
                ?> 
            jQuery('.nxsEdPostMetaBlockBody').hide();  jQuery('.nxs_ldos').html('[+]');  <?php
            } elseif (!empty($options['howToShowNTS']) && $options['howToShowNTS']=='E') {
                ?> jQuery('.nxsEdPostMetaBlockBody').show();  jQuery('.nxs_ldos').html('[-]'); 
          <?php
            } ?>
          });</script>
          
          <div style="float: left;">  <h3 style="margin-left: 0px; padding-left: 0px;display: inline-block;">Autopost to ....</h3>&nbsp;&nbsp;&nbsp;&nbsp;
          
           <a href="#" onclick="jQuery('#nxsLockIt').val('1'); jQuery('.nxs_acctcb').attr('checked','checked'); nxs_showHideMetaBoxBlocks(); jQuery('.nxs_acctcb').iCheck('update'); return false;">[<?php  _e('Select All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" onclick="jQuery('#nxsLockIt').val('1');jQuery('.nxs_acctcb').removeAttr('checked'); nxs_showHideMetaBoxBlocks(); jQuery('.nxs_acctcb').iCheck('update'); return false;">[<?php _e('Unselect All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>          
           
           <a href="#" onclick="jQuery('.nxsEdPostMetaBlockBody').show(); return false;">[<?php  _e('Expand All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" onclick="jQuery('.nxsEdPostMetaBlockBody').hide(); return false;">[<?php _e('Collapse All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" id="nxsShowOnlySelectedEd" onclick="return false;">[<?php _e('Show Only Selected', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" id="nxsShowOnlySelectedAllEd" onclick="return false;">[<?php _e('Show All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>          
          </div><input type="hidden" id="nxsLockIt" value="0" />       
          
          <?php if ($post->post_status != "publish") {
                ?>
          
          <?php
            } else {
                ?> <script type="text/javascript"> jQuery(document).ready(function() {  nxs_hideMetaBoxBlocks(); }); </script> <?php
            } ?>
          
          <div id="nxsPostMetaData"><?php // prr($options['tw']); WHAT IS IT?????????????/
          foreach ($nxs_snapAvNts as $avNt) {
              $clName = 'nxs_snapClass'.$avNt['code'];
              if (isset($avNt['lcode']) && isset($accts[$avNt['lcode']]) && count($accts[$avNt['lcode']])>0) {
                  $ntClInst = new $clName();
                  if (method_exists($ntClInst, 'showPostMeta')) {
                      $ntClInst->showPostMeta($accts[$avNt['lcode']], $post);
                  }
              }
          } ?></div>
        
          <div id="nxsMetaBox" style="display: block; clear:both;"><?php
          foreach ($nxs_snapAvNts as $avNt) {
              $clName = 'nxs_snapClass'.$avNt['code'];
              if (isset($avNt['lcode']) && isset($accts[$avNt['lcode']]) && count($accts[$avNt['lcode']])>0) {
                  $ntClInst = new $clName();
                  $ntClInst->nt = $accts[$avNt['lcode']];
                  //## Count only finsihed accounts. Get rid of unfinnished accounts...
                  $cbo = 0;
                  $cboInx = 0;
                  $jXj = 0;
                  foreach ($ntClInst->nt as $indx=>$pbo) {
                      if (!empty($pbo['fltrsOn']) && $pbo['fltrsOn'] == '1') {
                          if (empty($pbo['fltrs'])) {
                              $pbo['fltrs'] = array();
                          }
                          if (empty($pbo['fltrs']['nxs_post_type'])) {
                              $pbo['fltrs']['nxs_post_type'] = array('post');
                          }
                          $fltPostTypeExcl = !in_array($postType, $pbo['fltrs']['nxs_post_type']);
                          if (!empty($pbo['fltrs']['nxs_ie_posttypes'])) {
                              $fltPostTypeExcl = !$fltPostTypeExcl;
                          }
                      } else {
                          $fltPostTypeExcl = false;
                      }//  prr($indx, 'IDX'); prr($pbo, 'PBO');
                      if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) {
                          $pbo[$ntClInst->ntInfo['lcode'].'OK'] = $ntClInst->checkIfSetupFinished($pbo);
                      }
                      if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) {
                          continue;
                      } else {
                          $cbo++;
                          $cboInx = $indx;
                      }
                      $pbo['hideMe'] = !empty($options['hideUnchecked']) && (empty($pbo['do']) || $fltPostTypeExcl);
                      if (!$pbo['hideMe']) {
                          $jXj++;
                      }
                  }
                  if ($cbo==0) {
                      continue;
                  }
                  if (!$this->sMode['t']) {
                      $cbo = 1;
                  }
                  if ($cbo==1) {
                      ?>
               <?php  $pbo = $ntClInst->nt[$cboInx];
                      $pbo['jj']=$cboInx;
                      $pbo['cbo']=$cbo;
                      $pbo['hideMe'] = !empty($options['hideUnchecked']) && (empty($pbo['do']) || $fltPostTypeExcl);
                      $ntClInst->showEditNTLine($cboInx, $pbo, $post); ?>
             <?php
                  } else {
                      ?>
             <div class="nxs_box" <?php if ($jXj==0) {
                          echo ' style="display:none;" ';
                      } ?> onmouseover="jQuery('.selAll<?php echo $avNt['code']; ?>').show();" onmouseout="jQuery('.selAll<?php echo $avNt['code']; ?>').hide();">
               <div class="nxs_box_header">
                 <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($avNt['imgcode']))?$avNt['imgcode']:$avNt['lcode']; ?>16.png);"><?php echo $avNt['name']; ?>
                   <?php if ($cbo>1) {
                          ?><div class="nsBigText"><?php echo '(<span id="nxsNumOfAcc_'.$avNt['lcode'].'">'.$cbo."</span> ";
                          _e('accounts', 'social-networks-auto-poster-facebook-twitter-g');
                          echo ")"; ?></div><?php
                      } ?>
                   <span style="display: none;" class="selAll<?php echo $avNt['code']; ?>">&nbsp;&nbsp;
                   <a onclick="jQuery('.nxs_acctcb<?php echo $avNt['lcode']; ?>').attr('checked','checked'); jQuery('.nxs_acctcb<?php echo $avNt['lcode']; ?>').iCheck('update'); return false;" style="font-size: 12px; text-decoration: none;" href="#">[<?php  _e('Select All', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;<a onclick="jQuery('.nxs_acctcb<?php echo $avNt['lcode']; ?>').removeAttr('checked'); jQuery('.nxs_acctcb<?php echo $avNt['lcode']; ?>').iCheck('update'); return false;" style="font-size: 12px; text-decoration: none;" href="#">[<?php  _e('Unselect All', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>        
                   </span>
                 </div>
               </div>
               <div class="nxs_box_inside"><?php $jj = 0;
                      if (!$ntClInst->checkIfFunc()) {
                          echo $ntClInst->noFuncMsg;
                      } //### List of accountns
                      else {
                          uasort($ntClInst->nt, 'nxsLstSort');
                          foreach ($ntClInst->nt as $indx=>$pbo) {
                              if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) {
                                  $pbo[$ntClInst->ntInfo['lcode'].'OK'] = $ntClInst->checkIfSetupFinished($pbo);
                              }
                              if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) {
                                  continue;
                              }
                     
                              if (!empty($pbo['fltrsOn']) && $pbo['fltrsOn'] == '1') {
                                  if (empty($pbo['fltrs'])) {
                                      $pbo['fltrs'] = array();
                                  }
                                  if (empty($pbo['fltrs']['nxs_post_type'])) {
                                      $pbo['fltrs']['nxs_post_type'] = array('post');
                                  }
                                  $fltPostTypeExcl = !in_array($postType, $pbo['fltrs']['nxs_post_type']);
                                  if (!empty($pbo['fltrs']['nxs_ie_posttypes'])) {
                                      $fltPostTypeExcl = !$fltPostTypeExcl;
                                  }
                              } else {
                                  $fltPostTypeExcl = false;
                              }
                                      
                              $pbo['hideMe'] = $options['hideUnchecked'] && (empty($pbo['do']) || $fltPostTypeExcl);
                              if (!$pbo['hideMe']) {
                                  $jj++;
                              }
                     
                              $pbo['jj']=$jj;
                              $pbo['cbo']=$cbo;
                              $ntClInst->showEditNTLine($indx, $pbo, $post);
                          }
                      }
                      if ($jj>7) {
                          ?> <div style="padding-left:5px;padding-top:5px;"><a href="#" onclick="jQuery('.showMore<?php echo $avNt['code']; ?>').show(); jQuery(this).parent().hide(); return false;">Show More[<?php echo($cbo-5); ?>]</a></div>  <?php
                      }
                      if ($jj==0 && $jXj>0) {
                          ?> <span>&nbsp;&nbsp;&nbsp;--&nbsp;<?php  _e('No completed accounts available', 'social-networks-auto-poster-facebook-twitter-g'); ?></span> <?php
                      } ?>
               </div>
             </div><?php
                  }
              }
          } ?></div>
         
         <div class="popShAtt" id="fbAttachType"><h3><?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3> <img src="<?php echo NXS_PLURL; ?>img/fb2wops.png" width="600" height="257" alt="<?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
           <div class="popShAtt" id="fbPostTypeDiff"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
         
         <div id="showSetTime" style="display: none;background-color: #fff;"><span class="nxspButton bClose"><span>X</span></span>           
         <div id="showSetTimeInt" style="min-height: 300px; padding: 25px;">
           Set Time: (Current Time: <?php echo date_i18n('Y-m-d H:i'); ?> ) <div id="nxs_timestampdiv" class="hide-if-js" style="display: block;"><div class="timestamp-wrap"><select id="nxs_mm" name="nxs_mm">
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
            
<input type="text" id="nxs_jj" name="nxs_jj" value="<?php echo date_i18n('d'); ?>" size="2" maxlength="2" autocomplete="off">, <input type="text" id="nxs_aa" name="nxs_aa" value="<?php echo date_i18n('Y'); ?>" size="4" maxlength="4" autocomplete="off"> @ <input type="text" id="nxs_hh" name="nxs_hh" value="<?php echo date_i18n('H'); ?>" size="2" maxlength="2" autocomplete="off"> : <input type="text" id="nxs_mn" name="nxs_mn" value="<?php echo date_i18n('i'); ?>" size="2" maxlength="2" autocomplete="off"></div><input type="hidden" id="nxs_ss" name="nxs_ss" value="58">
<p>
<a href="#" class="button bClose" onclick="var tid = jQuery('#nxs_timeID').val(); var tmTxt = nxs_makeTimeTxt2(tid); jQuery('#'+tid+'timeToRunTxt').html(tmTxt); return false;">OK</a>
<a href="#" class="bClose">Cancel</a>
<input type="hidden"  id="nxs_timeID" value="" /> <input type="hidden"  id="nxs_timeID_ED" value="0" />
</p>
</div></div></div>

</div></div></div> </div> </div> <?php
        }
        public function NS_SNAP_SavePostMetaTags($id)
        {
            global $nxs_snapAvNts, $nxs_SNAP;
            if (!empty($_POST['nxs_snapPostOptions'])) {
                $NXS_POSTX = $_POST['nxs_snapPostOptions'];
                $NXS_POST = array();
                $NXS_POST = NXS_parseQueryStr($NXS_POSTX);
            } else {
                $NXS_POST = $_POST;
            }
            if (count($NXS_POST)<1 || !isset($NXS_POST["snapEdIT"]) || empty($NXS_POST["snapEdIT"])) {
                return;
            }
            if (get_magic_quotes_gpc() || (!empty($_POST['nxs_mqTest']) && $_POST['nxs_mqTest']=="\'")) {
                array_walk_recursive($NXS_POST, 'nsx_stripSlashes');
            }
            array_walk_recursive($NXS_POST, 'nsx_fixSlashes');
            if (!isset($nxs_SNAP)) {
                return;
            }
            $options = $nxs_SNAP->nxs_accts; //  echo "| NS_SNAP_SavePostMetaTags - ".$id." |";
            $post = get_post($id);
            if ($post->post_type=='revision' && $post->post_status=='inherit' && $post->post_parent!='0') {
                return;
            } // prr($NXS_POST);
          if (empty($NXS_POST["useSURL"])) {
              $NXS_POST["useSURL"] = '2';
          }
            delete_post_meta($id, '_snap_forceSURL');
            add_post_meta($id, '_snap_forceSURL', $NXS_POST["useSURL"]);
            delete_post_meta($id, 'snap_MYURL');
            add_post_meta($id, 'snap_MYURL', $NXS_POST["urlToUse"]);
            delete_post_meta($id, 'snapEdIT');
            add_post_meta($id, 'snapEdIT', '1');
            $snap_isAutoPosted = get_post_meta($id, 'snap_isAutoPosted', true);
            if ($snap_isAutoPosted=='1' &&  $post->post_status=='future') {
                delete_post_meta($id, 'snap_isAutoPosted');
                add_post_meta($id, 'snap_isAutoPosted', '2');
            }
            foreach ($nxs_snapAvNts as $avNt) {// echo "--------------------------------------------";  prr($avNt);
              if (isset($options[$avNt['lcode']]) && count($options[$avNt['lcode']])>0 && isset($NXS_POST[$avNt['lcode']]) && count($NXS_POST[$avNt['lcode']])>0) {
                  $savedMeta = maybe_unserialize(get_post_meta($id, 'snap'.$avNt['code'], true)); //prr($savedMeta);
              if (is_array($NXS_POST[$avNt['lcode']])) {
                  foreach ($NXS_POST[$avNt['lcode']] as $ii=>$pst) { // echo "###########";  prr($pst);
                      if (is_array($pst) && empty($pst['do'.$avNt['code']]) && empty($NXS_POST[$avNt['lcode']][$ii]['do'.$avNt['code']])) {
                          $NXS_POST[$avNt['lcode']][$ii]['do'.$avNt['code']] = 0;
                      }
                  }
              }
                  $newMeta = $NXS_POST[$avNt['lcode']];
                  if (is_array($savedMeta) && is_array($newMeta)) {
                      $newMeta = nxsMergeArraysOV($savedMeta, $newMeta);
                  } // echo "#####~~~~~~~~~ ".$id."| snap".$avNt['code']; prr($savedMeta); echo "||"; prr($newMeta);// $newMeta = 'AAA';
                  delete_post_meta($id, 'snap'.$avNt['code']);
                  add_post_meta($id, 'snap'.$avNt['code'], str_replace('\\', '\\\\', serialize($newMeta)));
              }
            }         //   die('KK');
          // prr($_POST);
        }
        //## Add MetaBox to Post->Edit
  public function addCustomBoxes()
  {
      global $nxs_SNAP;
      if (!isset($nxs_SNAP)) {
          return;
      }
      if ($nxs_SNAP->sMode['a']=='I') {
          return;
      }
      $options = $nxs_SNAP->nxs_options;  //## Add to Post, Page and Custom Post Types
     $args=array('public'=>true, '_builtin'=>false);
      $output = 'names';
      $operator = 'and';
      $post_types = array();
      if (function_exists('get_post_types')) {
          $post_types=get_post_types($args, $output, $operator);
      }
      $post_types['post'] = 'post';
      $post_types['page'] = 'page';
      if (empty($options['fltrs']) || !is_array($options['fltrs'])) {
          $options['fltrs'] = array();
      }
      if (empty($options['fltrs']['nxs_post_type']) || !is_array($options['fltrs']['nxs_post_type'])) {
          $options['fltrs']['nxs_post_type'] = array('post');
      }
      //prr($options['fltrs']['nxs_post_type']); prr($post_types);
      foreach ($post_types as $cptID=>$cptName) {
          if ((empty($options['fltrs']['nxs_ie_posttypes']) && in_array($cptID, $options['fltrs']['nxs_post_type'])) || (!empty($options['fltrs']['nxs_ie_posttypes']) && !in_array($cptID, $options['fltrs']['nxs_post_type']))) {
              add_meta_box('NS_SNAP_AddPostMetaTags', __('NextScripts: Social Networks Auto Poster - Post Options', 'social-networks-auto-poster-facebook-twitter-g'), array($this, 'NS_SNAP_AddPostMetaTags'), $cptID);
          }
      }
  }
        
  
        public function setSettingsFromPOST()
        {
            $options = $this->nxs_options;
            $pvData = nxs_Filters::sanitize_data($_POST);
            if (isset($pvData['apCats'])) {
                $options['apCats'] = $pvData['apCats'];
            }
            if (isset($pvData['nxsHTDP'])) {
                $options['nxsHTDP'] = $pvData['nxsHTDP'];
            }
            if (isset($pvData['ogImgDef'])) {
                $options['ogImgDef'] = $pvData['ogImgDef'];
            }
            if (isset($pvData['ogAuthorFB'])) {
                $options['ogAuthorFB'] = $pvData['ogAuthorFB'];
            }
            if (isset($pvData['featImgLoc'])) {
                $options['featImgLoc'] = $pvData['featImgLoc'];
            }
            if (isset($pvData['imgSizeImg'])) {
                $options['imgSizeImg'] = $pvData['imgSizeImg'];
            }
            if (isset($pvData['imgSizeAttch'])) {
                $options['imgSizeAttch'] = $pvData['imgSizeAttch'];
            }
            if (isset($pvData['anounTagLimit'])) {
                $options['anounTagLimit'] = $pvData['anounTagLimit'];
            }
            if (isset($pvData['nxsHTSpace'])) {
                $options['nxsHTSpace'] = $pvData['nxsHTSpace'];
            } else {
                $options['nxsHTSpace'] = "";
            }
            if (isset($pvData['nxsHTSepar'])) {
                $options['nxsHTSepar'] = $pvData['nxsHTSepar'];
            } else {
                $options['nxsHTSepar'] = "c_";
            }
            if (isset($pvData['featImgLocPrefix'])) {
                $options['featImgLocPrefix'] = $pvData['featImgLocPrefix'];
            }
            if (isset($pvData['featImgLocArrPath'])) {
                $options['featImgLocArrPath'] = $pvData['featImgLocArrPath'];
            }
            
            if (isset($pvData['errNotifEmailCB'])) {
                $options['errNotifEmailCB'] = 1;
            } else {
                $options['errNotifEmailCB'] = 0;
            }
            if (isset($pvData['errNotifEmail'])) {
                $options['errNotifEmail'] = $pvData['errNotifEmail'];
            }
            
            if (isset($pvData['forceBrokenCron'])) {
                $options['forceBrokenCron'] = 1;
            } else {
                $options['forceBrokenCron'] = 0;
            }
            
            if (isset($pvData['nxsURLShrtnr'])) {
                $options['nxsURLShrtnr'] = $pvData['nxsURLShrtnr'];
            }
            if (isset($pvData['bitlyUname'])) {
                $options['bitlyUname'] = $pvData['bitlyUname'];
            }
            if (isset($pvData['bitlyAPIKey'])) {
                $options['bitlyAPIKey'] = $pvData['bitlyAPIKey'];
            }
            
            if (isset($pvData['adflyUname'])) {
                $options['adflyUname'] = $pvData['adflyUname'];
            }
            if (isset($pvData['adflyAPIKey'])) {
                $options['adflyAPIKey'] = $pvData['adflyAPIKey'];
            }
            if (isset($pvData['adflyDomain'])) {
                $options['adflyDomain'] = $pvData['adflyDomain'];
            }
            
            if (isset($pvData['YOURLSKey'])) {
                $options['YOURLSKey'] = $pvData['YOURLSKey'];
            }
            if (isset($pvData['YOURLSURL'])) {
                $options['YOURLSURL'] = $pvData['YOURLSURL'];
            }
            
            if (isset($pvData['clkimAPIKey'])) {
                $options['clkimAPIKey'] = $pvData['clkimAPIKey'];
            }
            if (isset($pvData['postAPIKey'])) {
                $options['postAPIKey'] = $pvData['postAPIKey'];
            }
                        
            if (isset($pvData['gglAPIKey'])) {
                $options['gglAPIKey'] = $pvData['gglAPIKey'];
            }
            
            if (isset($pvData['fltrs'])) {
                $options = nxs_adjFilters($pvData['fltrs'][0], $options);
            }
            if (isset($pvData['fltrsOn'])) {
                $options['fltrsOn'] = 1;
            } else {
                $options['fltrsOn'] = 0;
            }
            
            if (!isset($options['nxsURLShrtnr'])) {
                $options['nxsURLShrtnr'] = 'G';
            }
            if ($options['nxsURLShrtnr']=='B' && (trim($pvData['bitlyAPIKey'])=='' || trim($pvData['bitlyAPIKey'])=='')) {
                $options['nxsURLShrtnr'] = 'G';
            }
            if ($options['nxsURLShrtnr']=='Y' && (trim($pvData['YOURLSKey'])=='' || trim($pvData['YOURLSURL'])=='')) {
                $options['nxsURLShrtnr'] = 'G';
            }
            if ($options['nxsURLShrtnr']=='A' && (trim($pvData['adflyAPIKey'])=='' || trim($pvData['adflyAPIKey'])=='')) {
                $options['nxsURLShrtnr'] = 'G';
            }
            
            if ($options['nxsURLShrtnr']=='C' && trim($pvData['clkimAPIKey'])=='') {
                $options['nxsURLShrtnr'] = 'G';
            }
            if ($options['nxsURLShrtnr']=='P' && trim($pvData['postAPIKey'])=='') {
                $options['nxsURLShrtnr'] = 'G';
            }
            
            if (isset($pvData['forceSURL'])) {
                $options['forceSURL'] = 1;
            } else {
                $options['forceSURL'] = 0;
            }
            if (isset($pvData['brokenCntFilters'])) {
                $options['brokenCntFilters'] = 1;
            } else {
                $options['brokenCntFilters'] = 0;
            }
            if (isset($pvData['numLogRows'])) {
                $options['numLogRows'] = $pvData['numLogRows'];
            } else {
                $options['numLogRows'] = 1000;
            }
            if (isset($pvData['queryInterval'])) {
                $options['queryInterval'] = $pvData['queryInterval'];
            } else {
                $options['queryInterval'] = 60;
            }
            if (isset($pvData['numOfTasks'])) {
                $options['numOfTasks'] = $pvData['numOfTasks'];
            } else {
                $options['numOfTasks'] = 30;
            }
            
            $numQU = intval($options['queryInterval']);
            if (!is_numeric($numQU) || ($numQU < 30 || $numQU > 600)) {
                $options['queryInterval'] = 60;
            }
            
            if (isset($pvData['nsOpenGraph'])) {
                $options['nsOpenGraph'] = $pvData['nsOpenGraph'];
            } else {
                $options['nsOpenGraph'] = 0;
            }
            if (isset($pvData['nxsOG'])) {
                $options['nxsOG'] = $pvData['nxsOG'];
            } else {
                $options['nxsOG'] = 'N';
            }
            
            if (isset($pvData['imgNoCheck'])) {
                $options['imgNoCheck'] = 0;
            } else {
                $options['imgNoCheck'] = 1;
            }
            if (isset($pvData['useForPages'])) {
                $options['useForPages'] = 1;
            } else {
                $options['useForPages'] = 0;
            }
                        
            if (isset($pvData['showPrxTab'])) {
                $options['showPrxTab'] = 1;
            } else {
                $options['showPrxTab'] = 0;
            }
            if (isset($pvData['useRndProxy'])) {
                $options['useRndProxy'] = 1;
            } else {
                $options['useRndProxy'] = 0;
            }
            
            if (!empty($pvData['showNTListCats'])) {
                $options['showNTListCats'] = 1;
            } else {
                $options['showNTListCats'] = 0;
            }
            if (!empty($pvData['howToShowNTS'])) {
                $options['howToShowNTS'] = $pvData['howToShowNTS'];
            }
            
            if (isset($pvData['prxList'])) {
                $options['prxList'] = $pvData['prxList'];
            }
            if (isset($pvData['addURLParams'])) {
                $options['addURLParams'] = $pvData['addURLParams'];
            }
            
            if (isset($pvData['tagsExclFrmHT'])) {
                $options['tagsExclFrmHT'] = $pvData['tagsExclFrmHT'];
            }
            
            
            if (isset($pvData['forcessl'])) {
                $options['forcessl'] = $pvData['forcessl'];
            }
            
            if (isset($pvData['riActive'])) {
                $options['riActive'] = 1;
            } else {
                $options['riActive'] = 0;
            }
            if (isset($pvData['riHowManyPostsToTrack'])) {
                $options['riHowManyPostsToTrack'] = $pvData['riHowManyPostsToTrack'];
            }
            if (isset($pvData['riHowOften'])) {
                $options['riHowOften'] = $pvData['riHowOften'];
            }
            
            if (isset($pvData['useUnProc'])) {
                $options['useUnProc'] = $pvData['useUnProc'];
            } else {
                $options['useUnProc'] = 0;
            }
            if (!empty($pvData['nxsCPTSeld']) && is_array($pvData['nxsCPTSeld'])) {
                $cpTypes = $pvData['nxsCPTSeld'];
            } else {
                $cpTypes = array();
            }
            $options['nxsCPTSeld'] = serialize($cpTypes);
            
            if (!isset($pvData['whoCanSeeSNAPBox'])) {
                $pvData['whoCanSeeSNAPBox'] = array();
            }
            $pvData['whoCanSeeSNAPBox'][] = 'administrator';
            if (isset($pvData['whoCanSeeSNAPBox'])) {
                $options['whoCanSeeSNAPBox'] = $pvData['whoCanSeeSNAPBox'];
            }
            if (!isset($pvData['whoCanMakePosts'])) {
                $pvData['whoCanMakePosts'] = array();
            }
            $pvData['whoCanMakePosts'][] = 'administrator';
            if (isset($pvData['whoCanMakePosts'])) {
                $options['whoCanMakePosts'] = $pvData['whoCanMakePosts'];
            }
            
            if (!isset($pvData['whoCanHaveOwnSNAPAccs'])) {
                $pvData['whoCanHaveOwnSNAPAccs'] = array();
            }
            $pvData['whoCanHaveOwnSNAPAccs'][] = 'administrator';
            if (isset($pvData['whoCanHaveOwnSNAPAccs'])) {
                $options['whoCanHaveOwnSNAPAccs'] = $pvData['whoCanHaveOwnSNAPAccs'];
            }
            
            if (isset($pvData['skipSecurity'])) {
                $options['skipSecurity'] = 1;
            } else {
                $options['skipSecurity'] = 0;
            }
            if (!empty($pvData['zeroUser'])) {
                $options['zeroUser'] = 1;
            } else {
                $options['zeroUser'] = 0;
            }
            
            if (isset($pvData['hideUnchecked'])) {
                $options['hideUnchecked'] = 1;
            } else {
                $options['hideUnchecked'] = 0;
            }
            
            
            if (isset($pvData['quLimit'])) {
                $options['quLimit'] = 1;
            } else {
                $options['quLimit'] = 0;
            }
            
            //## Query has been activated
            $isTimeChanged = ((isset($pvData['quDays']) && isset($options['quDays']) && $pvData['quDays']!=$options['quDays']) || (!isset($options['quDays']) && !empty($pvData['quDays']))) ||
                ((isset($pvData['quHrs']) && isset($options['quHrs']) && $pvData['quHrs']!=$options['quHrs']) || (!isset($options['quHrs']) && !empty($pvData['quHrs']))) ||
                ((isset($pvData['quMins']) && isset($options['quMins']) && $pvData['quMins']!=$options['quMins']) || (!isset($options['quMins']) && !empty($pvData['quMins'])));
              
            if (isset($pvData['nxsOverLimit'])) {
                $options['nxsOverLimit'] = $pvData['nxsOverLimit'];
            }
            if (isset($pvData['quLimitRndMins'])) {
                $options['quLimitRndMins'] = $pvData['quLimitRndMins'];
            }
            if (isset($pvData['quDays'])) {
                $options['quDays'] = $pvData['quDays'];
            } else {
                $options['quDays'] = 0;
            }
            if (isset($pvData['quHrs'])) {
                $options['quHrs'] = $pvData['quHrs'];
            } else {
                $options['quHrs'] = 0;
            }
            if (isset($pvData['quMins'])) {
                $options['quMins'] = $pvData['quMins'];
            } else {
                $options['quMins'] = 0;
            }
            
            if ($isTimeChanged) {
                $currTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
                $pstEvrySec = $options['quDays']*86400+$options['quHrs']*3600+$options['quMins']*60;
                $options['quNxTime'] = $currTime + $pstEvrySec; // ???????? Do we still need it if we have nxs_recountQueryTimes()?
                global $nxs_SNAP;
                $nxs_SNAP->nxs_options = $options;
                nxs_recountQueryTimes();
            }
            
            
            if (isset($pvData['rpstActive'])) {
                $options['rpstActive'] = 1;
            } else {
                $options['rpstActive'] = 0;
            }      //     prr($options);
            
//            $options = nxs_adjRpst($options, $pvData);
            
            
            if (!empty($nxs_isWPMU) && $nxs_isWPMU && (!isset($options['suaMode'])||$options['suaMode'] == '')) {
                $options['suaMode'] = $nxs_tpWMPU;
            }
            $editable_roles = get_editable_roles();
            foreach ($editable_roles as $roleX => $details) {
                $role = get_role($roleX);
                $role->remove_cap('see_snap_box');
                $role->remove_cap('make_snap_posts');
                $role->remove_cap('haveown_snap_accss');
            }
            
            foreach ($options['whoCanSeeSNAPBox'] as $uRole) {
                $role = get_role($uRole);
                $role->add_cap('see_snap_box');
                $role->add_cap('make_snap_posts');
            }
            foreach ($options['whoCanMakePosts'] as $uRole) {
                $role = get_role($uRole);
                $role->add_cap('make_snap_posts');
            }
            foreach ($options['whoCanHaveOwnSNAPAccs'] as $uRole) {
                $role = get_role($uRole);
                $role->add_cap('haveown_snap_accss');
            }
            $this->nxs_options = $options;
            return $options;
        }
    }
}

//## NXS Reposters List Table
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class nxs_ReposterListTable extends WP_List_Table
{
    public $fltsInfo='';
    public $isActive=false;

    public function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular'  => 'nxs_filter',     //singular name of the listed records
            'plural'    => 'nxs_filters',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ));
    }
    //## Defines Columns
    public function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Title',
            'posts'     => 'Posts',
            'filters'    => 'Info',
            'nextdate'  => 'Next Post'
        );
        return $columns;
    }
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'title':
            case 'nextdate':
                return $item->$column_name;
            case 'filters':
                return $item->guid;
            case 'posts':
                return 'No';
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
    
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ 
            $item->ID                //The value of the checkbox should be the record's id
        );
    }
    public function column_title($item)
    {
        $mt = maybe_unserialize(get_post_meta($item->ID, 'nxs_rpstr', true)); // $isActiveTxtD = var_export($mt['rpstOn'], true);
        $this->isActive = (!empty($mt['rpstOn']) && $mt['rpstOn']=='1');
        $isActiveTxt =  $this->isActive ?__('Active'):((!empty($mt['rpstOn']) && $mt['rpstOn']=='F')?__('Finished'):__('Inactive'));
        $color = $this->isActive?'Blue':'Gray';
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&item=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->ID),
            'delete'    => sprintf('<a href="?page=%s&action=%s&item=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->ID),
        );
        
        $grOptions = maybe_unserialize(get_post_meta($item->ID, 'nxs_rpstr_data', true));
        if (!empty($grOptions)) {
            $grOptions['fullreturn'] = 1;
            nxs_removeAllWPQueryFilters();
            $this->fltsInfo = get_posts_ids_by_filter($grOptions);
        }
        //Return the title contents
        return sprintf(
            '%1$s <span style="color:%2$s">(%3$s)</span>%4$s',
            /*$1%s*/ $item->post_title,
            /*$2%s*/ $color,
            /*$3%s*/ $isActiveTxt,
            /*$4%s*/ $this->row_actions($actions)
        );
    }

    public function column_posts($item)
    {
        return count($this->fltsInfo['p']);
    }
    public function column_filters($item)
    {
        return $this->fltsInfo['i']; /* ."<pre>".print_r($this->fltsInfo, true)."</pre>"; */
    }
    
    public function column_nextdate($item)
    {
        $gr2Options = maybe_unserialize(get_post_meta($item->ID, 'nxs_rpstr', true)); //prr($gr2Options);
        if ($this->isActive && !empty($gr2Options['rpstNxTime'])) {
            $pNextTime =  $gr2Options['rpstNxTime']>0?date_i18n('Y-m-d H:i', $gr2Options['rpstNxTime']):'Never';
            return $pNextTime;
        }
        return 'n/a';
    }
    
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'title'     => array('title',true),     //true means it's already sorted
            'date'  => array('date',false)
        );
        return $sortable_columns;
    }
    public function get_bulk_actions()
    {
        $actions = array(
            'delete'    => 'Delete',
            'activate'    => 'Activate',
            'deactivate'    => 'Deactivate'
        );
        return $actions;
    }
    public function process_bulk_action()
    {
        if ('delete'===$this->current_action()) {
            $items = $_REQUEST['nxs_filter'];
            $jj = 0;  //prr($_REQUEST);
            foreach ($items as $item) {
                wp_delete_post($item, true);
                $jj++;
            }
            wp_die($jj.' Items deleted.');
        }
        if ('activate'===$this->current_action()) {
            $items = $_REQUEST['nxs_filter'];
            $jj = 0;  //prr($_REQUEST);
            foreach ($items as $item) {
                $o = maybe_unserialize(get_post_meta($item, 'nxs_rpstr', true));
                $o['rpstOn']='1';
                nxs_Filters::save_meta($item, 'nxs_rpstr', $o);
                $jj++;
            }
            wp_die($jj.' Items activated.');
        }
        if ('deactivate'===$this->current_action()) {
            $items = $_REQUEST['nxs_filter'];
            $jj = 0;  //prr($_REQUEST);
            foreach ($items as $item) {
                $o = maybe_unserialize(get_post_meta($item, 'nxs_rpstr', true));
                $o['rpstOn']='0';
                nxs_Filters::save_meta($item, 'nxs_rpstr', $o);
                $jj++;
            }
            wp_die($jj.' Items deactivated.');
        }
    }

    public function prepare_items()
    {
        $per_page = 75;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $args=array('post_type' => 'nxs_filter', 'posts_per_page' => 1000, 'orderby'=> 'date',  'order' => 'DESC');
        $query = new WP_Query($args);
        $data = get_posts($args); //  prr($data);

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            //$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            $result = strcmp($a->$orderby, $b->$orderby); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
       
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, (($current_page-1)*$per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ));
    }
}
class nxs_QPListTable extends WP_List_Table
{
    public function __construct()
    {
        global $status, $page;
        @parent::__construct(array(
            'singular'  => 'nxs_qpost',     //singular name of the listed records
            'plural'    => 'nxs_qposts',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ));
    }
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'post':
            case 'post_date':
                return $item->$column_name;
            case 'summary':
                return $item->guid;
            case 'author':
                return $item->$column_name;
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    public function column_post_title($item)
    {
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&item=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->ID),
            'delete'    => sprintf('<a href="?page=%s&action=%s&item=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->ID),
        );
        //Return the title contents
        return sprintf(
            '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item->post_title,
            /*$2%s*/ $item->ID,
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ 
            $item->ID                //The value of the checkbox should be the record's id
        );
    }
    
    public function column_summary($item)
    {
        $outTxt = '';
        $snapData = maybe_unserialize(get_post_meta($item->ID, '_nxs_snap_data', true));
        $info = new nxs_snapPostResults($snapData['posts']);
        
        $outTxt .= $info->summary;
        
        return $outTxt;
    }
    
    public function column_author($item)
    {
        return get_the_author_meta('display_name', $item->post_author);
    }

    public function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'post_title'     => 'Title',
            'author'     => 'Author',
            'summary'    => 'Summary',
            'post_date'  => 'Date'
        );
        return $columns;
    }
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'post_title'     => array('post_title',false),     //true means it's already sorted
          //  'summary'    => array('summary',false),
            'author'    => array('post_author',false),
            'post_date'  => array('post_date',false)
        );
        return $sortable_columns;
    }
    public function get_bulk_actions()
    {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    public function process_bulk_action()
    {
        if ('delete'===$this->current_action()) {
            foreach ($_REQUEST['nxs_qpost'] as $qp) {
                wp_delete_post($qp, true);
            }
            $url = nxs_get_admin_url().'admin.php?page=nxssnap-post';
            echo '<script type="text/javascript">parent.location.replace(\''.$url.'\');</script>';
            die();
        }
    }

    public function prepare_items()
    {
        $per_page = 50;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $args=array('post_type' => 'nxs_qp', 'posts_per_page' => 500, 'orderby'=> 'date',  'order' => 'DESC');
        if ((!current_user_can('manage_options') && current_user_can('haveown_snap_accss'))) {
            $args['author__in'] = get_current_user_id();
        }
        $query = new WP_Query($args);
        $data = get_posts($args);//   prr($data);

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'post_date'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //prr($order); //If no order, default to asc
            if (is_array($a)) {
                $result = strcmp($a[$orderby], $b[$orderby]);
            } else {
                $result = strcmp($a->$orderby, $b->$orderby);
            } //prr($a);//Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }                                    //  prr($data);
        usort($data, 'usort_reorder');
        //   prr($data);
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, (($current_page-1)*$per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page),   //WE have to calculate the total number of pages
            'orderby'    => ! empty($_REQUEST['orderby']) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'post_title',
            'order'        => ! empty($_REQUEST['order']) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'desc'
        ));
    }
    
    public function display()
    {
        wp_nonce_field('ajax-custom-list-nonce', '_ajax_custom_list_nonce');
        echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
        parent::display();
    }
    public function ajax_response()
    {
        check_ajax_referer('ajax-custom-list-nonce', '_ajax_custom_list_nonce');
        $this->prepare_items();
        extract($this->_args);
        extract($this->_pagination_args, EXTR_SKIP);
        ob_start();
        if (! empty($_REQUEST['no_placeholder'])) {
            $this->display_rows();
        } else {
            $this->display_rows_or_placeholder();
        }
        $rows = ob_get_clean();
        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();
        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();
        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();
        $response = array( 'rows' => $rows );
        $response['pagination']['top'] = $pagination_top;
        $response['pagination']['bottom'] = $pagination_bottom;
        $response['column_headers'] = $headers;
        if (isset($total_items)) {
            $response['total_items_i18n'] = sprintf(_n('1 item', '%s items', $total_items), number_format_i18n($total_items));
        }
        if (isset($total_pages)) {
            $response['total_pages'] = $total_pages;
            $response['total_pages_i18n'] = number_format_i18n($total_pages);
        }
        die(json_encode($response));
    }
}

function nxs_ajax_fetch_custom_list_callback()
{
    $wp_list_table = new nxs_QPListTable();
    $wp_list_table->ajax_response();
}
add_action('wp_ajax__ajax_fetch_custom_list', 'nxs_ajax_fetch_custom_list_callback');
/**
 * This function adds the jQuery script to the plugin's page footer
 */
function nxs_qp_ajax_script()
{
    $screen = get_current_screen();
    if (stripos($screen->id, '_page_nxssnap-post')===false) {
        return false;
    } ?>
<script type="text/javascript">

function nxs_doNP(){ jQuery("#nxsNPLoaderPost").show(); var mNts = []; jQuery('input[name=nxs_NPNts]:checked').each(function(i){ mNts[i] = jQuery(this).val(); }); var ddt = nxs_makeTimeTxt(); var qpid = jQuery('#nxsQPID').html();
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"doNewPost", qpid:qpid, mText: jQuery('#nxsNPText').val(), mTitle: jQuery('#nxsNPTitle').val(), mType: jQuery('input[name=nxsNPType]:checked').val(), mLink: jQuery('#nxsNPLink').val(), mImg: jQuery('#nxsNPImg').val(), mNts: mNts, ddt:ddt, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, 
    function(j){  jQuery("#nxsNPResult").html(j); jQuery("#nxsNPLoaderPost").hide(); jQuery("#nxsNPCloseBt").val('Close'); 
           
            
            var data = {
                paged:  '1',
                order:  'desc',
                orderby: 'post_date'
            };
            list.update( data );
    
    }
  , "html")     
}
function nxs_doSaveQP(){ jQuery("#nxsNPLoaderPost").show(); var mNts = []; jQuery('input[name=nxs_NPNts]:checked').each(function(i){ mNts[i] = jQuery(this).val(); }); var ddt = nxs_makeTimeTxt(); var qpid = jQuery('#nxsQPID').html();
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"nxs_doSaveQP", qpid:qpid, mText: jQuery('#nxsNPText').val(), mTitle: jQuery('#nxsNPTitle').val(), mType: jQuery('input[name=nxsNPType]:checked').val(), mLink: jQuery('#nxsNPLink').val(), mImg: jQuery('#nxsNPImg').val(), mNts: mNts, ddt:ddt, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  jQuery("#nxsNPResult").html(j); jQuery("#nxsNPLoaderPost").hide(); jQuery("#nxsNPCloseBt").val('Close'); }, "html")     
}

(function($) {
list = {
   
    init: function() {
        // This will have its utility when dealing with the page number input
        var timer;
        var delay = 500;
        
        $('.edit a').on('click', function(e) { e.preventDefault();            
            var query = this.search.substring( 1 );
            
            var data = { page: list.__query( query, 'page' ) || 'nxssnap-post', action: list.__query( query, 'action' ) || 'edit', item: list.__query( query, 'item' ) || '0' };            
            
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getQP", id:data.item, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val(), dataType: 'json'}, function(j){ var res = JSON.parse(j);                 
                 if (res.title.indexOf('uick post [')!=1) jQuery('#nxsNPTitle').val(res.title);                  
                 jQuery('#nxsNPText').val(res.text);  jQuery('#nxsNPLink').val(res.linkURL); jQuery('#nxsNPImg').val(res.imgURL);                                      
                 jQuery('input[name=nxsNPType][value='+res.postType+']').prop('checked',true);
                 jQuery('#nxsNPRowNetworks').html(res.nts);  jQuery('#nxsNPResult2').html(res.oldResults);   
                 jQuery('#nxsQPNewSave').show(); jQuery('#nxsQPID').html(data.item);
                                  
                 jQuery('#nsx_tab1_ttl').click();
            }, "html");
        });
        
        $('.delete a').on('click', function(e) { e.preventDefault();            
            var query = this.search.substring( 1 );            
            var data = { page: list.__query( query, 'page' ) || 'nxssnap-post', action: list.__query( query, 'action' ) || 'delete', item: list.__query( query, 'item' ) || '0' };            
            
            var answer = confirm("Remove post?");
            if (answer){            
              jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"delQP", id:data.item, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  console.log( jQuery(e.target).parent().parent().parent().parent());    console.log( j);
                 if (j=='OK') jQuery(e.target).parent().parent().parent().parent().fadeOut("slow");                 
              }, "html");
            }
        });
        
        // Pagination links, sortable link
        $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
            // We don't want to actually follow these links
            e.preventDefault();
            // Simple way: use the URL to extract our needed variables
            var query = this.search.substring( 1 );
            
            var data = {
                paged: list.__query( query, 'paged' ) || '1',
                order: list.__query( query, 'order' ) || 'asc',
                orderby: list.__query( query, 'orderby' ) || 'post_title'
            };
            list.update( data );
        });
        // Page number input
        $('input[name=paged]').on('keyup', function(e) {
            // If user hit enter, we don't want to submit the form
            // We don't preventDefault() for all keys because it would
            // also prevent to get the page number!
            if ( 13 == e.which )
                e.preventDefault();
            // This time we fetch the variables in inputs
            var data = {
                paged: parseInt( $('input[name=paged]').val() ) || '1',
                order: $('input[name=order]').val() || 'asc',
                orderby: $('input[name=orderby]').val() || 'post_title'
            };
            // Now the timer comes to use: we wait half a second after
            // the user stopped typing to actually send the call. If
            // we don't, the keyup event will trigger instantly and
            // thus may cause duplicate calls before sending the intended
            // value
            window.clearTimeout( timer );
            timer = window.setTimeout(function() {
                list.update( data );
            }, delay);
        });
    },
    /** AJAX call
     * 
     * Send the call and replace table parts with updated version!
     * 
     * @param    object    data The data to pass through AJAX
     */
    update: function( data ) {
        $.ajax({
            // /wp-admin/admin-ajax.php
            url: ajaxurl,
            // Add action and nonce to our collected data
            data: $.extend(
                {
                    _ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
                    action: '_ajax_fetch_custom_list',
                    page: 'nxssnap-post',
                },
                data
            ),
            // Handle the successful result
            success: function( response ) { // WP_List_Table::ajax_response() returns json
                var response = $.parseJSON( response );
                // Add the requested rows
                if ( response.rows.length )  $('#the-list').html( response.rows );
                // Update column headers for sorting
                if ( response.column_headers.length ) { $('thead tr, tfoot tr').html( response.column_headers ); console.log( response.column_headers); }
                // Update pagination for navigation
                if ( response.pagination.bottom.length ) $('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
                if ( response.pagination.top.length ) $('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );
                // Init back our event handlers
                list.init();
            }
        });
    },
    /**
     * Filter the URL Query to extract variables
     * 
     * @see http://css-tricks.com/snippets/javascript/get-url-variables/
     * 
     * @param    string    query The URL query part containing the variables
     * @param    string    variable Name of the variable we want to get
     * 
     * @return   string|boolean The variable value if available, false else.
     */
    __query: function( query, variable ) {
        var vars = query.split("&");
        for ( var i = 0; i <vars.length; i++ ) {
            var pair = vars[ i ].split("=");
            if ( pair[0] == variable )
                return pair[1];
        }
        return false;
    },
}
// Show time!
list.init();
})(jQuery);
</script>
<?php
}
//add_action('admin_footer', 'nxs_qp_ajax_script');

add_action('admin_menu', 'hook_that');
function hook_that()
{
    add_action('admin_footer', 'nxs_qp_ajax_script');
}

?>