<?php
if (!class_exists('nxs_adminMgmt')) {
    class nxs_adminMgmt
    {
        public $page;
        public $pluginName = 'SNAP';

        public function __construct()
        {
            //add_action( 'admin_menu', array( $this, 'nxs_adminMenu' ) );
        }
        public function init()
        {
            add_action('admin_menu', array( $this, 'nxs_adminMenu' ));
        }
        //##MU
        public function ntAdminMenu()
        {
            global $nxs_SNAP;   //   prr($nxs_SNAP->sMode['l']);
            if ($nxs_SNAP->sMode['l']=='F' || $nxs_SNAP->sMode['l']=='P') {
                $this->page = add_menu_page('Social Networks Auto Poster', 'SNAP|AutoPoster', 'manage_options', 'nxssnap-ntadmin', array($this, 'showPage_ntdashboard'), NXS_PLURL.'img/snap-icon.png');
            } else {
                $this->page = add_menu_page('Social Networks Auto Poster', 'SNAP|AutoPoster', 'manage_options', 'nxssnap', array( $this, 'showPage_accounts' ), NXS_PLURL.'img/snap-icon.png');
                add_submenu_page('nxssnap', __('Accounts', 'social-networks-auto-poster-facebook-twitter-g'), __('Accounts', 'social-networks-auto-poster-facebook-twitter-g'), 'manage_options', 'nxssnap', array( $this, 'showPage_accounts' ), 0);
                add_submenu_page('nxssnap', __('Users/Sites', 'social-networks-auto-poster-facebook-twitter-g'), __('Users/Sites', 'social-networks-auto-poster-facebook-twitter-g'), 'manage_options', 'nxssnap-ntadmin', array( $this, 'showPage_userssites' ), 0);
                //add_submenu_page( 'nxssnap',__( 'Query/Timeline', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Query/Timeline', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxssnap-query', array( $this, 'showPage_query' ) ,0 );
                add_submenu_page('nxssnap', __('Help/Support', 'social-networks-auto-poster-facebook-twitter-g'), __('Help/Support', 'social-networks-auto-poster-facebook-twitter-g'), 'manage_options', 'nxs-help', array( $this, 'showPage_about' ), 0);
            }
        }
        public function showPage_ntdashboard()
        {
            add_meta_box($this->page.'_dashboard', __('Welcome to the Social Networks Autoposter (SNAP)', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_ntdashboard' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('Dashboard', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function metabox_ntdashboard($post)
        {
            global $nxs_SNAP;     // prr($nxs_SNAP->sMode['l']);
            if ($nxs_SNAP->sMode['l']=='F' || $nxs_SNAP->sMode['l']=='P') {
                $cs = get_site_option('nxs_nts');
                if (function_exists('get_sites')) {
                    $sitesX = get_sites(array('public'=> 1, 'archived' => 0, 'mature' => 0, 'spam' => 0, 'deleted' => 0));
                    $sites = array();
                    foreach ($sitesX as $ss) {
                        $sites[] = array('blog_id'=>$ss->blog_id, 'domain'=>$ss->domain, 'path'=>$ss->path) ;
                    }
                } else {
                    $sites = wp_get_sites(array('public'=> 1, 'archived' => 0, 'mature' => 0, 'spam' => 0, 'deleted' => 0));
                }
                //## Update from V3
                if (empty($cs)) {
                    switch_to_blog(1);
                    $k = get_option('nxsSNAPNetworks');
                    if (!empty($k)) {
                        update_site_option('nxs_nts', 1);
                    } else {
                        $k = get_option('NS_SNAutoPoster');
                        if (!empty($k)) {
                            update_site_option('nxs_nts', 1);
                        }
                    }
                }

                $nxsOne = NextScripts_SNAP_Version;
                $sMode = $nxs_SNAP->sMode; ?>
              <?php _e('Plugin Version', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <span style="color:#008000;font-weight: bold;"><?php echo $nxsOne; ?></span>
              <?php if ($sMode['l']=='P') {
                    ?> [Pro]&nbsp;&nbsp;&nbsp;(One User, Multiple Accounts)&nbsp;&nbsp;<?php
                } elseif ($sMode['l']=='M') {
                    ?> [Multiuser Pro]&nbsp;&nbsp;&nbsp;(Multiple Users, Multiple Accounts)<?php
                } else {
                    ?> <span style="color:#800000; font-weight: bold;">[Free]&nbsp;&nbsp;&nbsp;(One User, One account per Network)</span><?php
                } ?>
           <?php if (defined('NXSAPIVER')) {
                    echo '<span id="nxsAPIUpd">API</span> Version: <span style="color:#008000;font-weight: bold;">'.NXSAPIVER.'</span>';
                }

                if (defined('NXSAPIVER')) {
                    ?>&nbsp;&nbsp;|&nbsp;&nbsp; <img id="checkAPI2xLoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' /><a href="" id="checkAPI2x">[Check for API Update]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="showLic">[Change Activation Key]</a><?php
                }
                echo '<br/><br/>';

                if (!empty($_POST['nxs_ntsiteid']) && check_admin_referer('nxsSsPageWPN', 'nxsSsPageWPN_wpnonce')) {
                    $csN = (int)$_POST['nxs_ntsiteid'];
                    if (!empty($cs) && $csN!=$cs) {
                        switch_to_blog($cs);
                        delete_option('nxsSNAPNetworks');
                        delete_option('nxsSNAPOptions');
                        restore_current_blog();
                    }
                    $cs = $csN;
                    update_site_option('nxs_nts', $cs);
                }
                foreach ($sites as $i => $site) {
                    $blog = get_blog_details($site['blog_id']);
                    $sites[$i]['name'] = $blog->blogname;
                    if ($sites[$i]['blog_id']==$cs) {
                        $cSite = $sites[$i];
                    }
                }
                // uasort( $sites, function( $site_a, $site_b ) { return strcasecmp( $site_a[ 'name' ], $site_b[ 'name' ] ); });
            ?>  <?php _e('You are using "Single User" version of the plugin. This version could be used only on a single site by a single user.', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;<a target="_blank" href="https://www.nextscripts.com/social-networks-auto-poster-pro-for-wpmu"><?php _e('Get SNAP Pro Multiuser Version', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> <br/><br/>
            <form method="post" action="" novalidate="novalidate"> <?php wp_nonce_field('nxsSsPageWPN', 'nxsSsPageWPN_wpnonce'); ?>
              <span style="font-size: 15px; font-weight: bold;"><?php _e('Please select a site where you would like to use plugin: ', 'social-networks-auto-poster-facebook-twitter-g'); ?></span> <select name="nxs_ntsiteid"> <?php
            foreach ($sites as $i => $site) {
                echo '<option '.($site['blog_id']==$cs?'selected':'').' value="'.$site['blog_id'].'">[ID: '.$site['blog_id'].'] '.$site['name'].' - '.$site['domain'].($site['path']!='/'?$site['path']:'').'</option>';
            } ?></select><br/> <div style="padding-top:10px;">
            <?php if (!empty($cs) && !empty($cSite) && is_array($cSite)) {
                ?> <div style="padding-bottom:10px;">
              <?php _e('You already have SNAP Configured on: ', 'social-networks-auto-poster-facebook-twitter-g'); ?><b><?php echo 'ID:'.$cSite['blog_id'].' - '.$cSite['name'].' ('.$cSite['domain'].($cSite['path']!='/'?$cSite['path']:'').')'; ?></b><br/><br/>
              !!!! <?php _e('Selecting a different site will <b>delete</b> all current settings', 'social-networks-auto-poster-facebook-twitter-g'); ?> !!!!<br/><br/>
              <input type="checkbox" onchange="jQuery('#nxsNTOSubmit').toggle();"/> <?php _e('I undestand that all current settings will be deleted', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
            </div><?php
            } ?>
            <input type="submit" <?php if (!empty($cs) && !empty($cSite) && is_array($cSite)) {
                ?>style="display: none" <?php
            } ?> name="submit" id="nxsNTOSubmit" class="button button-primary" value="<?php _e('Save Changes', 'social-networks-auto-poster-facebook-twitter-g'); ?>"></div></form> <?php
            } else { //## MU COde
             //  if (function_exists('showSNAP_WPMU_OptionsPageExt')) { showSNAP_WPMU_OptionsPageExt($nxs_SNAP); }
            }
        }

        public function nxs_adminMenu()
        {
            wp_enqueue_script('postbox');
            //$this->page = add_menu_page( 'Social Networks Auto Poster', '-={<span style="font-weight:bold; color:#2ecc2e;">SNAP</span>}=-','manage_options','nxssnap',array( $this, 'showPage_dashboard' ), NXS_RELPATH.'/img/snap-icon.png');
            // $this->page = add_menu_page( 'Social Networks Auto Poster', '-={SNAP}=-','manage_options','nxssnap',array( $this, 'showPage_dashboard' ), NXS_RELPATH.'/img/snap-icon.png');
            $this->page = add_menu_page('Social Networks Auto Poster', 'SNAP|AutoPoster', 'haveown_snap_accss', 'nxssnap', array( $this, 'showPage_accounts' ), NXS_PLURL.'img/snap-icon.png');

            add_submenu_page('nxssnap', __('Accounts', 'social-networks-auto-poster-facebook-twitter-g'), __('Accounts', 'social-networks-auto-poster-facebook-twitter-g'), 'haveown_snap_accss', 'nxssnap', array( $this, 'showPage_accounts' ), 0);
            add_submenu_page('nxssnap', __('Quick Post', 'social-networks-auto-poster-facebook-twitter-g'), __('Quick Post', 'social-networks-auto-poster-facebook-twitter-g'), 'haveown_snap_accss', 'nxssnap-post', array( $this, 'showPage_dashboard' ));
            add_submenu_page('nxssnap', __('Query/Timeline', 'social-networks-auto-poster-facebook-twitter-g'), __('Query/Timeline', 'social-networks-auto-poster-facebook-twitter-g'), 'haveown_snap_accss', 'nxssnap-query', array( $this, 'showPage_query' ), 0);
            add_submenu_page('nxssnap', __('Reposter', 'social-networks-auto-poster-facebook-twitter-g'), __('Reposter', 'social-networks-auto-poster-facebook-twitter-g'), 'manage_options', 'nxssnap-reposter', array( $this, 'showPage_reposter' ), 0);
            add_submenu_page('nxssnap', __('Settings', 'social-networks-auto-poster-facebook-twitter-g'), __('Settings', 'social-networks-auto-poster-facebook-twitter-g'), 'manage_options', 'nxssnap-settings', array( $this, 'showPage_settings' ), 0);
            add_submenu_page('nxssnap', __('Log/History', 'social-networks-auto-poster-facebook-twitter-g'), __('Log/History', 'social-networks-auto-poster-facebook-twitter-g'), 'haveown_snap_accss', 'nxs-log', array( $this, 'showPage_log' ), 0);
            add_submenu_page('nxssnap', __('Help/Support', 'social-networks-auto-poster-facebook-twitter-g'), __('Help/Support', 'social-networks-auto-poster-facebook-twitter-g'), 'manage_options', 'nxs-help', array( $this, 'showPage_about' ), 0);
        }

        public function showPage($title, $object=null)
        {
            ?>
            <div class="wrap" id="<?php echo $this->page; ?>"><div id="nxsDivWrap"> <?php // $this->show_follow_icons();?>
                <h2><?php echo $title ?></h2>
                <div id="poststuff">
                  <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content" style="position: relative;" class=""><?php do_meta_boxes($this->page, 'normal', $object); ?></div>
                    <div id="postbox-container-1" class="postbox-container"><?php  do_meta_boxes($this->page, 'side', null); ?></div>
                    <br class="clear"/>
                  </div>
                </div>
            </div></div>
            <script type="text/javascript">
              jQuery(document).ready(function($) { $('.if-js-closed').removeClass('if-js-closed').addClass('closed'); if (typeof(postboxes)!='undefined') postboxes.add_postbox_toggles('<?php echo $this->page; ?>');});
            </script> <?php
        }
        //## Side Boxes
        public function showPage_side()
        {
            add_meta_box($this->page.'_info', __('SNAP Info', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_side_info' ), $this->page, 'side');
            if ((current_user_can('manage_options'))) {
                if (!function_exists('ns_SMASV41')) {
                    add_meta_box($this->page.'_purchase', __('SNAP "Pro" Version', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_side_getit' ), $this->page, 'side');
                }
                if (!class_exists('nxsAPI_XI')) {
                    add_meta_box($this->page.'_purchaseAPI', __('SNAP Premium API', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_side_getitAPI' ), $this->page, 'side');
                }
                add_meta_box($this->page.'_supportbox', __('Support', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_side_supbox' ), $this->page, 'side');
            }
            add_meta_box($this->page.'_instrbox', __('Instructions', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_side_instrbox' ), $this->page, 'side');
        }
        public function showPage_dashboard()
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $t = $nxs_SNAP->nxs_accts;
            $nc = 0;
            if (!empty($t)) {
                foreach ($t as $kt=>$tt) {
                    if ((strlen($kt)==2 || strlen($kt)==3) && is_array($tt)) {
                        $nc = $nc + count($tt);
                    }
                }
            }
            if ($nc>0) {
                add_meta_box($this->page.'_newPost', __('New Post to Social Networks', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_newPost' ), $this->page, 'normal');
            } else {
                add_meta_box($this->page.'_newPost', __('Please add at least one account...', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_emptyaccss' ), $this->page, 'normal');
            }
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('Dashboard', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function showPage_accounts()
        {
            add_meta_box($this->page.'_accounts', __('Social Networks Autoposter (SNAP) Accounts', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_accounts' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('SNAP Accounts', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function showPage_userssites()
        {
            add_meta_box($this->page.'_accounts', __('Social Networks Autoposter (SNAP) Users/Sites Management', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_userssites' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('SNAP Accounts', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function showPage_query()
        {
            add_meta_box($this->page.'_query', __('Social Networks Autoposter (SNAP) Query', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_query' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('SNAP Query', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function showPage_reposter()
        {
            $post = null;

            if (!empty($_GET['action'])&& $_GET['action']=='edit' && !empty($_GET['item'])) {
                $post = get_post($_GET['item']);
                if (!empty($_POST['nxs_snap_reposter_update'])) {
                    nxs_Filters::save_filter($_GET['item']);
                } ?>
              <form method="post" id="nxs_form_rep"> <input name="pid" value="<?php echo $_GET['item']; ?>" type="hidden" /> <input name="action" value="nxs_snap_aj" type="hidden" /> <input id="nxs_resetStats" name="resetStats" value="0" type="hidden" />
                <input name="nxsact" value="saveRpst" type="hidden" /> <?php nxs_Filters::showEdit($this->page);
            } elseif (!empty($_GET['action'])&& $_GET['action']=='delete' && !empty($_GET['item'])) {
                wp_delete_post($_GET['item']); ?> <script type="text/javascript">window.location = "<?php echo nxs_get_admin_url('admin.php?page=nxssnap-reposter'); ?>"</script>  <?php
            } elseif (!empty($_POST['action'])&& $_POST['action']=='delete' && !empty($_POST['nxs_filter'])) {
                foreach ($_POST['nxs_filter'] as $rr) {
                    wp_delete_post($rr);
                } ?> <script type="text/javascript">window.location = "<?php echo nxs_get_admin_url('admin.php?page=nxssnap-reposter'); ?>"</script>  <?php
            } else {
                add_meta_box($this->page.'_reposter', __('Social Networks Autoposter (SNAP) Auto-Reposter', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_reposter' ), $this->page, 'normal');
            }

            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('Auto-Reposter Configuration', 'social-networks-auto-poster-facebook-twitter-g'), $post);

            if (!empty($post)) {
                echo "</form>";
            }
        }
        public function showPage_settings()
        {
            add_meta_box($this->page.'_settings', __('Social Networks Autoposter (SNAP) Settings', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_settings' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('SNAP Settings', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function showPage_log()
        {
            add_meta_box($this->page.'_log', __('Social Networks Autoposter (SNAP) Log/History', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_log' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('SNAP Log/History', 'social-networks-auto-poster-facebook-twitter-g'));
        }
        public function showPage_about()
        {
            add_meta_box($this->page.'_about', __('Social Networks Autoposter (SNAP) Help/Support', 'social-networks-auto-poster-facebook-twitter-g'), array( $this, 'metabox_about' ), $this->page, 'normal');
            $this->showPage_side();
            $this->showPage($this->pluginName.': '. __('SNAP Help/Support', 'social-networks-auto-poster-facebook-twitter-g'));
        }

        public function metabox_accounts($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $nxs_SNAP->showAccountsTab();
        }

        public function metabox_userssites($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $nxs_SNAP->showUsersSitesMUTab();
        }

        public function metabox_emptyaccss($post)
        {
            _e('Please add at least one account', 'social-networks-auto-poster-facebook-twitter-g');
            echo '&nbsp;-&gt;&nbsp;<a href="'.nxs_get_admin_url('admin.php?page=nxssnap').'">'.__('Accounts', 'social-networks-auto-poster-facebook-twitter-g').'</a>';
        }

        public function metabox_query($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $nxs_SNAP->showQueryTab();
        }
        public function metabox_settings($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            // if (isset($_POST['nxsMainFromElementAccts']) || isset($_POST['nxsMainFromSupportFld'])) $options = $nxs_SNAP->saveSNAPSettings($nxs_SNAP->nxs_options); //## Save Settings
            $nxs_SNAP->showSettingsTab();
        }
        public function metabox_log($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $nxs_SNAP->showLogHistoryTab();
        }
        public function metabox_about($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $nxs_SNAP->showAboutTab();
        }

        public function metabox_reposter($post)
        {
            $itemsTable = new nxs_ReposterListTable();
            $itemsTable->prepare_items(); ?>
           <div class="wrap">
             <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;margin-bottom: 7px;">
                <p><?php _e('Here you can configure automatic repost of the existing posts to your social network accounts.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <a href="http://nxs.fyi/rpst" target="_blank"><?php _e('Instructions', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></p>
             </div>
             <div style="padding-top: 8px; padding-bottom: 8px;"> <a id="nxsFltAddButton" href="#" class="NXSButton"><?php _e('Add new Reposter Action', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </div>
             <form id="movies-filter" method="get"> <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /><?php $itemsTable->display(); ?></form>
            </div> <div id="nxs_spFltPopup"><span class="nxspButton bClose"><span>X</span></span><div id="nxs_spFltPopupU" style="min-height: 300px;"><?php nxs_rpstPopupCode(); ?></div></div><?php
        }

        public function metabox_dashboard($post)
        {
            ?> Quick links: <br/>


          <div align="center"><div class="nxsInfoMsg">Start here <img style="position: relative; top: 8px;" alt="Arrow" src="<?php echo NXS_PLURL; ?>img/arrow_r_green_c1.png"></div>
          <a href="admin.php?page=nxssnap-accounts" class="NXSButton" id="nxs_snapConfAccs"><?php _e('Configure Social Networks', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonB" id="nxs_snapConfRepstr"><?php _e('Configure Auto-Reposter', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonB" id="nxs_snapConfSets"><?php _e('Plugin Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonB" id="nxs_snapConfLog"><?php _e('View Log', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></div>
           <?php
        }

        public function metabox_newPost($post)
        {
            $itemsTable = new nxs_QPListTable();
            $itemsTable->prepare_items(); ?>

        <div class="nxswrap" style="overflow: auto;">
        <ul class="nsx_tabs">
          <li><a class="ab-item" id="nsx_tab1_ttl" href="#nsx_tab1"><span style="font-weight:bold; font-size: 1.2em; color:#2ecc2e; margin: 3px;">&dArr;</span><?php _e('New Quick Post', 'social-networks-auto-poster-facebook-twitter-g') ?><span style="font-weight:bold; font-size: 1.2em; color:#2ecc2e; margin: 3px;">&dArr;</span></a></li>
          <li><a href="#nsx_tab2"><?php _e('Quick Posts History', 'social-networks-auto-poster-facebook-twitter-g') ?></a></li>
        </ul>
        <div class="nsx_tab_container">
          <div id="nsx_tab1" class="nsx_tab_content">
            <?php global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $networks = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? $nxs_SNAP->nxs_acctsU : $nxs_SNAP->nxs_accts;
            nxs_showNewPostForm($networks, false); ?>
          </div>
          <div id="nsx_tab2" class="nsx_tab_content">
            <div class="wrap">
              <form id="movies-filter" method="get"> <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /><?php $itemsTable->display() ?></form>
            </div>
          </div>
        </div> </div> <?php
        }

        public function metabox_side_getit($post)
        {
            ?>

        <a href="http://nxs.fyi/get-snap" target="_blank">SNAP Pro version</a> upgrade adds the ability to configure more then one account for each social network and some addidional features.<br/><div style="padding-bottom: 10px; padding-top: 7px;" align="center">
<b style="color: #008000">[Limited Time Only Offer]</b> - ($49.95 value) <br/> Get SNAP PRO Plugin for <b>Free</b> with the order of SNAP Premium API for WordPress</div><div align="center"><a  href="http://nxs.fyi/get-snap" target="_blank" class="NXSButton" id="nxs_snapUPG">Get SNAP Pro Plugin with SNAP API</a></div>  <?php if (function_exists('nxsDoLic_ajax')) {
                ?> <br/><div align="center">
            <span style="color: #00FF00; font-weight: bold; font-size: 28px;">--&gt;</span>
            <a style="color: #008000; font-weight: normal; font-size: 13px;" class="showLic" href="#">[<?php  _e('Enter your Activation Key', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>
            <span style="color: #00FF00; font-weight: bold; font-size: 28px;">&lt;--</span>
            </div>
            <?php
            }
        }

        public function metabox_side_getitAPI($post)
        {
            ?>
          <style type="text/css">.nxs_txtIcon { margin: 0px; padding-left: 20px; background-repeat: no-repeat;} .nxs_ti_gp {background-image: url('<?php echo NXS_PLURL; ?>img/gp16.png');}
            .nxs_ti_li {background-image: url('<?php echo NXS_PLURL; ?>img/li16.png');}  .nxs_ti_rd {background-image: url('<?php echo NXS_PLURL; ?>img/rd16.png');}
            .nxs_ti_fp {background-image: url('<?php echo NXS_PLURL; ?>img/fp16.png');}  .nxs_ti_yt {background-image: url('<?php echo NXS_PLURL; ?>img/yt16.png');}
            .nxs_ti_bg {background-image: url('<?php echo NXS_PLURL; ?>img/bg16.png');}  .nxs_ti_pn {background-image: url('<?php echo NXS_PLURL; ?>img/pn16.png');}
            .nxs_ti_ig {background-image: url('<?php echo NXS_PLURL; ?>img/ig16.png');}  .nxs_ti_xi {background-image: url('<?php echo NXS_PLURL; ?>img/xi16.png');}
            .nxs_ti_fb {background-image: url('<?php echo NXS_PLURL; ?>img/fb16.png');}
          </style>

        <div style="padding-bottom: 12px;"><a href="https://www.nextscripts.com/snap-api-premium-for-wordpress/">SNAP Premium API</a> adds autoposting to:</div>
        <div style="padding-bottom: 15px;">
        <span class="nxs_txtIcon nxs_ti_fb">Facebook</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_gp">Google+</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_pn">Pinterest</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_ig">Instagram</span>, <span class="nxs_txtIcon nxs_ti_bg">Blogger</span>,&nbsp;&nbsp;&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_yt">YouTube</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_li">LinkedIn</span>, <br/><span class="nxs_txtIcon nxs_ti_fp">Flipboard</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_rd">Reddit</span>,&nbsp;&nbsp;&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_xi">Xing</span></div>

        <div align="center">
        <a  href="https://www.nextscripts.com/snap-api-premium-for-wordpress/" target="_blank" class="NXSButton" id="nxs_snapUPG">Get<?php if (!function_exists('ns_SMASV41')) {
                ?> SNAP Pro Plugin with <?php
            } ?> SNAP API</a></div>

         <?php
        }

        public function metabox_side_supbox($post)
        {
            ?>  <?php _e('Most common issues/questions are already answered in the', 'social-networks-auto-poster-facebook-twitter-g'); ?>:<br/><a style="font-weight: normal; font-size: 16px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/support-faq/"><?php _e('Troubleshooting FAQ', 'social-networks-auto-poster-facebook-twitter-g'); ?></a><br/><br/>
          <?php _e('Can\'t find the answer ins the FAQ? Have troubles/problems/found a bug?', 'social-networks-auto-poster-facebook-twitter-g'); ?>:<br/><a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="https://www.nextscripts.com/support">Open support ticket</a><br/><br/>
          <?php _e('Have questions/suggestions?', 'social-networks-auto-poster-facebook-twitter-g'); ?>:<br/><a style="font-weight:normal;font-size:18px;line-height:24px;" target="_blank" href="https://www.nextscripts.com/contact-us">Contact us</a><br/>
          <?php
        }

        public function metabox_side_instrbox($post)
        {
            global $nxs_snapAvNts;
            foreach ($nxs_snapAvNts as $avNt) {
                $clName = 'nxs_snapClass'.$avNt['code'];
                $nt = new $clName(); ?>
              <div style="padding-left: 10px; padding-top:5px;"><a style="background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo $avNt['lcode']; ?>16.png) !important;" class="nxs_icon16" target="_parent" href="<?php echo $nt->ntInfo['instrURL']; ?>">  <?php echo $nt->ntInfo['name']; ?> </a></div>
            <?php
            }
        }

        public function metabox_side_info($post)
        {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $options = $nxs_SNAP->nxs_options;
            $nxsOne = NextScripts_SNAP_Version;
            $sMode = $nxs_SNAP->sMode; ?>
            <div align="center"><a target="_blank" href="https://www.nextscripts.com"><img src="<?php echo NXS_PLURL; ?>img/SNAP_Logo_2014.png"></a></div> <br/>
              <?php _e('Plugin Version', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <span style="color:#008000;font-weight: bold;"><?php echo $nxsOne; ?></span>
              <?php if ($sMode['l']=='P') {
                ?> [Pro]<br/>&nbsp;&nbsp;&nbsp;(One User, Multiple Accounts)&nbsp;&nbsp;<?php
            } elseif ($sMode['l']=='M') {
                ?> [Pro]<br/>&nbsp;&nbsp;&nbsp;(Multiple Users, Multiple Accounts)<?php
            } else {
                ?> <span style="color:#800000; font-weight: bold;">[Free]<br/>&nbsp;&nbsp;&nbsp;(One User, One account per Network)</span><?php
            } ?><br/>
           <?php if (defined('NXSAPIVER')) {
                echo '<span id="nxsAPIUpd">API</span> Version: <span style="color:#008000;font-weight: bold;">'.NXSAPIVER.'</span>';
            } ?><?php wp_nonce_field('doLic', 'doLic_wpnonce'); ?>

           <div id="showLicForm"><span class="nxspButton bClose"><span>X</span></span> <div id="nxs_showLicForm" style="min-height: 200px;">
            <div style="position: absolute; right: 10px; top:50px; font-size: 34px; font-weight: lighter;"><?php  _e('Activation', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
              <br/><br/>
              <h3><?php  _e('Multiple Accounts Edition and Google+ and Pinterest Auto-Posting', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><br/><?php  _e('You can find your key on this page', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <a href="https://www.nextscripts.com/mypage">https://www.nextscripts.com/mypage</a>
                <br/><br/> <?php _e('Enter your Key', 'social-networks-auto-poster-facebook-twitter-g'); ?>:  <input name="eLic" id="eLic"  style="width: 50%;"/>
                <input type="button" class="button-primary" name="eLicDo" onclick="doLic();" value="Enter" />
                <br/><img id="enterKeyAPI2xLoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' /><br/><?php _e('Your plugin will be automatically upgraded', 'social-networks-auto-poster-facebook-twitter-g'); ?>. <?php wp_nonce_field('doLic', 'doLic_wpnonce'); ?>
              </div>
            </div>

         <?php
        }
    }
}
?>
