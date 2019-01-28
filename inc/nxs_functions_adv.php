<?php
if (!function_exists("nxs_snapAjax")) {
    function nxs_snapAjax()
    {
        check_ajax_referer('nxsSsPageWPN');
        $arg = '';
        nxs_Filters::init(true);
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $networks = (!current_user_can('manage_options') && current_user_can('haveown_snap_accss')) ? $nxs_SNAP->nxs_acctsU : $nxs_SNAP->nxs_accts;
        $options =  $nxs_SNAP->nxs_options;

        if ($_POST['nxsact']=='getNTset') {
            $ii = $_POST['ii'];
            $nt = $_POST['nt'];
            $ntl = strtolower($nt);
            $pbo = $networks[$ntl][$ii];
            $pbo['ntInfo']['lcode'] = $ntl;
            $clName = 'nxs_snapClass'.$nt;
            $ntObj = new $clName();
            $ntObj->showNTSettings($ii, $pbo);
        }
        if ($_POST['nxsact']=='setNTset') {
            global $nxs_snapAvNts;
            unset($_POST['action']);
            unset($_POST['nxsact']);
            unset($_POST['_wp_http_referer']);
            unset($_POST['_wpnonce']); //unset($_POST['apDoSFB0']); // Do something
            if (get_magic_quotes_gpc() || (!empty($_POST['nxs_mqTest']) && $_POST['nxs_mqTest']=="\'")) {
                array_walk_recursive($_POST, 'nsx_stripSlashes');
            }
            array_walk_recursive($_POST, 'nsx_fixSlashes');
            unset($_POST['nxs_mqTest']);
            $nxs_SNAP->setSettingsFromPOST();
            $nxs_SNAP->saveNetworksOptions('', 1); /* prr($_POST); prr($nxs_SNAP->nxs_accts);  */ die('OK');
        }
        if ($_POST['nxsact']=='setNTS') {
            global $nxs_snapAvNts;
            unset($_POST['action']);
            unset($_POST['nxsact']);
            unset($_POST['_wp_http_referer']);
            unset($_POST['_wpnonce']); //unset($_POST['apDoSFB0']); // Do something
            if (get_magic_quotes_gpc() || (!empty($_POST['nxs_mqTest']) && $_POST['nxs_mqTest']=="\'")) {
                array_walk_recursive($_POST, 'nsx_stripSlashes');
            }
            array_walk_recursive($_POST, 'nsx_fixSlashes');
            unset($_POST['nxs_mqTest']);
            foreach ($nxs_snapAvNts as $avNt) {
                if (isset($_POST[$avNt['lcode']])) {
                    $clName = 'nxs_snapClass'.$avNt['code'];
                    if (!isset($networks[$avNt['lcode']])) {
                        $networks[$avNt['lcode']] = array();
                    }
                    $ntClInst = new $clName();
                    $ntOpt = $ntClInst->setNTSettings($_POST[$avNt['lcode']], $networks[$avNt['lcode']]);
                    $networks[$avNt['lcode']] = $ntOpt;
                }
            }
            $nxs_SNAP->saveNetworksOptions($networks); /* prr($nxs_SNAP->nxs_options); /* prr($_POST); prr($nxs_SNAP->nxs_accts);  */ die('OK');
        }
        if ($_POST['nxsact']=='delNTAcc' && isset($_POST['id'])) {
            $indx = (int)$_POST['id'];
            unset($networks[$_POST['nt']][$indx]);
            $nxs_SNAP->saveNetworksOptions($networks);
            die('OK');
        }

        if ($_POST['nxsact']=='nsDupl') {
            $indx = (int)$_POST['id'];
            $no = $networks[$_POST['nt']][$indx];
            $no['nName'] .= ' [Copy]';
            $networks[$_POST['nt']][] = $no;
            if (is_array($networks)) {
                nxs_save_ntwrksOpts($networks);
                $nxs_SNAP->nxs_accts = $networks;
                echo "OK";
            }
        }
        //## Update All
        if ($_POST['nxsact']=='setAllXS' && !empty($_POST['nid']) && !empty($_POST['uid'])) {
            $acnt = 0;
            $nid = explode('-', $_POST['nid']);
            $val = $networks[$nid[0]][$nid[2]][$nid[1]];
            $uid = $networks[$nid[0]][$nid[2]][$_POST['uid']];
            foreach ($networks[$nid[0]] as $ii=>$nta) {
                if (!empty($nta[$_POST['uid']]) && $nta[$_POST['uid']]==$uid) {
                    $networks[$nid[0]][$ii][$nid[1]] = $val;
                    $acnt++;
                }
            }
            if (is_array($networks)) {
                nxs_save_ntwrksOpts($networks);
                $nxs_SNAP->nxs_accts = $networks;
                echo "Done. ".$acnt.' accounts has been updated.';
            }
        }


        //############ Quick Post
        if ($_POST['nxsact']=='getNewPostDlg') {
            nxs_showNewPostForm($networks);
        }
        //## Save QP
        if ($_POST['nxsact']=='nxs_doSaveQP') {
            $user_id = get_current_user_id();
            $ttl = nsTrnc(!empty($_POST['mTitle'])?$_POST['mTitle']:$_POST['mText'], 200);
            if (empty($ttl)) {
                $ttl = 'Quick post ['.date('F j, Y, g:i a', time()+(get_option('gmt_offset') * HOUR_IN_SECONDS)).']';
            }
            $my_post = array( 'post_title' => $ttl, 'post_content' => $_POST['mText'], 'post_status' => 'publish', 'post_author' => $user_id, 'post_type' => 'nxs_qp', 'post_category' => array(  ) );//               prr($_POST); die();
            if (!empty($_POST['qpid'])) {
                $my_post['ID'] = $_POST['qpid'];
                wp_update_post($my_post);
            } else {
                $_POST['qpid'] = wp_insert_post($my_post);
            }
            //## Insert Post meta
            foreach ($_POST['mNts'] as $ntC) {
                $ntA = explode('--', $ntC);
                $ntOpts = $networks[$ntA[0]][$ntA[1]];
                $nts[] = $ntA[0].$ntA[1];
            }

            if (!empty($_POST['qpid'])) {
                $metaArrEx = get_post_meta($_POST['qpid'], '_nxs_snap_data', true);
                $metaArr = array('posts'=>array(), 'nts'=>$nts, 'postType'=>$_POST['mType'], 'imgURL'=>$_POST['mImg'], 'linkURL'=>$_POST['mLink']);
                if (!empty($metaArrEx['posts'])) {
                    $metaArr['posts'] = $metaArrEx['posts'];
                }
                update_post_meta($_POST['qpid'], '_nxs_snap_data', $metaArr);
            }
            echo 'OK';
            die();
        }
        if ($_POST['nxsact']=='doNewPost') {
            nxs_doNewNPPost($networks);
        }
        if ($_POST['nxsact']=='getQP') {
            $post =  get_post($_POST['id']);
            $or = get_post_meta($_POST['id'], '_nxs_snap_data', true);
            $info = new nxs_snapPostResults($or['posts']);
            $outTxt = $info->details;
            $out = array('title'=>$post->post_title, 'text'=>$post->post_content, 'postType'=>$or['postType'], 'imgURL'=>$or['imgURL'], 'linkURL'=>$or['linkURL'], 'nts'=>nxs_showNetworksList($networks, !empty($or['nts'])?$or['nts']:''), 'oldResults'=>$outTxt);
            echo json_encode($out);
            die();
        }
        if ($_POST['nxsact']=='delQP') {
            wp_delete_post($_POST['id'], true);
            echo "OK";
        }
        if ($_POST['nxsact']=='cancQ') {
            $cid = $_POST['cid'];
            global $wpdb;
            $wpdb->delete($wpdb->prefix . "nxs_query", array( 'id' => $cid ));
            echo "== OK-".$cid;
        }


        //## Get somrhting (like Boards or Cats) from NT
        if ($_POST['nxsact']=='getItFromNT') {
            $ntU = strtoupper($_POST['nt']);
            $clName = 'nxs_snapClass'.$ntU;
            $ntObj = new $clName();
            $fName = $_POST['fName'];
            $ntObj->$fName($networks);
            die();
        }

        if ($_POST['nxsact']=='testPost' || $_POST['nxsact']=='manPost') {
            $clName = 'nxs_snapClass'.strtoupper($_POST['nt']);
            $ntClInst = new $clName();
            if (method_exists($ntClInst, 'ajaxPost')) {
                $ntClInst->ajaxPost($networks);
            } else {
                $fNm = 'nxs_rePostTo'.strtoupper($_POST['nt']).'_ajax';
                $fNm();
            }
        }
        if ($_POST['nxsact']=='delPostSettings') {
            global $nxs_snapAvNts;
            $pid = (int)$_POST['pid'];
            foreach ($nxs_snapAvNts as $avNt) {
                delete_post_meta($pid, 'snap'.strtoupper($avNt['code']));
            }
            delete_post_meta($pid, 'snap_isAutoPosted');
            delete_post_meta($pid, 'snap_MYURL');
            echo "OK";
            die();
        }
        if ($_POST['nxsact']=='saveRpst') {
            if (!empty($_POST['pid'])) {
                $pid = $_POST['pid'];
            }
            $post = array(
        // 'ID'             => [ <post id> ] // Are you updating an existing post?
        'post_name'      => sanitize_title($_POST['post_title']),
        'post_title'     => $_POST['post_title'],
        'post_status'    => 'publish',
        'post_type'      => 'nxs_filter',
        'ping_status'    => 'closed',
        //  'post_date'      => [ Y-m-d H:i:s ] // The time post was made.
        //  'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
        'comment_status' => 'closed');
            $newReposter = empty($pid);
            if ($newReposter) {
                $pid = wp_insert_post($post);
            } else {
                $post['ID'] = $pid;
                $pid = wp_update_post($post);
            }
            if (!empty($pid)) {
                $flt = nxs_Filters::save_filter($pid);
                $rpstrOpts = nxs_Filters::save_schinfo($pid);
                if (!empty($_POST['resetStats'])) {
                    delete_post_meta($pid, 'nxs_rpstr_stats');
                    global $wpdb;
                    $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key = 'snap_isRpstd".$pid."'");
                }

                $stats = nxs_Filters::getStats($pid, '', $rpstrOpts);
                if ($newReposter) {
                    die('OK');
                } else {
                    echo $stats['statsText'];
                }
            } else {
                die('ERROR (PID)');
            }
        }

        /////===============================


        if ($_POST['nxsact']=='svEdFlds') {
            $cn = str_replace(']', '', $_POST['cname']);
            $cna = explode('[', $cn);
            $id = $_POST['pid'];
            $nt = $cna[0];
            $ntU = strtoupper($nt);
            $ii = $cna[1];
            $fname = $cna[2];
            $savedMeta = maybe_unserialize(get_post_meta($id, 'snap'.$ntU, true));
            $savedMeta[$ii][$fname] = $_POST['cval'];
            prr($savedMeta);
            delete_post_meta($id, 'snap'.$ntU);
            add_post_meta($id, 'snap'.$ntU, str_replace('\\', '\\\\', serialize($savedMeta)));
        }
        if ($_POST['nxsact']=='tknzsrch') {
            $termsOut = array();
            if (($_POST['nxtype']=='post')) {
                $posts = get_posts(array('orderby'=>'title', 'post_status' => array( 'pending', 'publish', 'future' ), 'post_type' =>  'any', 'posts_per_page'=>100, 's'=> $_POST['srch'] ));
                foreach ($posts as $post) {
                    $termsOut[] = array('value'=>$post->ID, 'text'=>$post->post_title);
                }

                if (!empty($_POST['srch']) && intval($_POST['srch'])>0) {
                    $posts = get_posts(array('orderby'=>'title', 'post_status' => array( 'pending', 'publish', 'future' ), 'post_type' =>  'any', 'posts_per_page'=>100, 'post__in' => array(intval($_POST['srch']) )));
                    foreach ($posts as $post) {
                        $termsOut[] = array('value'=>$post->ID, 'text'=>$post->post_title);
                    }
                }
                echo json_encode($termsOut);
            } else {
                $terms = get_terms($_POST['nxtype'], array('orderby'=>'name', 'hide_empty' => 0, 'number'=>10, 'name__like'=> $_POST['srch'] )); //$termsOut[] = array('value'=>'-', 'text'=>$_POST['srch'].' [Add]');
                foreach ($terms as $term) {
                    $termsOut[] = array('value'=>$term->term_id, 'text'=>$term->name);
                }
                echo json_encode($termsOut);
            }
        }
        //### Evil Buttons
        if ($_POST['nxsact']=='resetSNAPInfoPosts') {
            global $wpdb;
            $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE 'snap%'");
            $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE '_nxs_slinks'");
            _e('Done. All SNAP data has been removed from posts.', 'social-networks-auto-poster-facebook-twitter-g');
        }
        if ($_POST['nxsact']=='deleteAllSNAPInfo') {
            global $wpdb;
            $wpdb->query("DELETE FROM ". $wpdb->options ." WHERE option_name = 'nxsSNAPOptions'");
            $wpdb->query("DELETE FROM ". $wpdb->options ." WHERE option_name = 'nxsSNAPNetworks'");
            $wpdb->query("DELETE FROM ". $wpdb->options ." WHERE option_name = 'NS_SNriPosts'");
            $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE 'snap%'");
            $wpdb->query("DELETE FROM ". $wpdb->prefix . "nxs_query");
            $wpdb->query("DELETE FROM ". $wpdb->posts ." WHERE post_type = 'nxs_filter'");
            $wpdb->query("DELETE FROM ". $wpdb->posts ." WHERE post_type = 'nxs_qp'");
            $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE '_nxs_slinks'");
            if (((false && false && !empty($_POST['nt']) && $_POST['nt']=='mu' && current_user_can('manage_network_options'))) || true || true) {
                delete_site_option('nxsSNAPOptions');
                delete_site_option('__plugins_cache_242');
                delete_site_option('__plugins_cache_244');
                if (false && false) {
                    echo '-=MU=- <script> setTimeout(function () { location = "'.network_admin_url().'admin.php?page=nxssnap-ntadmin"; }, 3000); </script>';
                } else {
                    echo '<script> setTimeout(function () { location.reload(1); }, 3000); </script>';
                }
            } else {
                echo '<script> setTimeout(function () { location.reload(1); }, 3000); </script>';
            }
            _e('Done. All SNAP data has been removed. Please wait for the page to reload....', 'social-networks-auto-poster-facebook-twitter-g');
        }

        if ($_POST['nxsact']=='accsFltToAll') {
            foreach ($networks as $ntl => $nta) {
                foreach ($nta as $ii => $ntAcc) {
                    $networks[$ntl][$ii]['fltrs']['nxs_post_type'] = array('post');
                    $networks[$ntl][$ii]['fltrsOn'] = '1';
                }
            }
            $nxs_SNAP->saveNetworksOptions($networks);
            echo "Done";
        }

        if ($_POST['nxsact']=='restBackup') {
            $dbNts = get_option('nxsSNAPNetworks_bck4');
            $dbOpts = get_option('nxsSNAPOptions_bck4');
            $nxs_SNAP->saveNetworksOptions($dbNts, $dbOpts);
            _e('Done. Backup has been restored. <script> setTimeout(function () { location.reload(1); }, 3000); </script>', 'social-networks-auto-poster-facebook-twitter-g');
        }
        if ($_POST['nxsact']=='resetSNAPQuery') {
            global $wpdb;
            $wpdb->query("DELETE FROM ". $wpdb->prefix . "nxs_query");
            _e('Done. SNAP query has been cleared.', 'social-networks-auto-poster-facebook-twitter-g');
        }
        if ($_POST['nxsact']=='resetSNAPCron') {
            $cron = maybe_unserialize(get_option('cron'));
            $cronX = $cron;
            foreach ($cron as $itsk=>$tsk) {
                if (!empty($tsk) && is_array($tsk)) {
                    $nm = key($tsk);
                    if (stripos($nm, 'ns_doPublishTo')!==false) {
                        unset($cronX[$itsk]);
                    }
                }
            }
            update_option('cron', $cronX, false);
            _e('Done. All SNAP Crons has been deleted.', 'social-networks-auto-poster-facebook-twitter-g');
        }
        if ($_POST['nxsact']=='resetSNAPCache') {
            global $wpdb;
            $wpdb->query("DELETE FROM ". $wpdb->options ." WHERE option_name LIKE 'nxs_snap_%'");
            $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE '_nxs_slinks'");
            _e('Done. All SNAP Cache has been cleared.', 'social-networks-auto-poster-facebook-twitter-g');
        }
        //###
        do_action('nxsajax', $arg);
        die();
    }
}

//## WP Time Adjust
if (!function_exists("nxs_adjTime")) {
    function nxs_adjTime($dateTime)
    {
        if (!function_exists('get_option')) {
            return $dateTime;
        }
        $tmCorr = get_option('gmt_offset') * HOUR_IN_SECONDS;
        $tm = strtotime($dateTime);
        $frmt = get_option('date_format').' '.get_option('time_format');
        return date($frmt, ($tm+$tmCorr));
    }
}
//## List sort
if (!function_exists('nxsLstSort')) {
    function nxsLstSort($a, $b)
    {
        if (empty($a['do'])) {
            $a['do'] = 0;
        }
        if (empty($b['do'])) {
            $b['do'] = 0;
        }
        if ($a['do']=='2') {
            $a['do'] = 1;
        }
        if ($b['do']=='2') {
            $b['do'] = 1;
        }
        if ($a['do'] == $b['do']) {
            if (empty($a['nName'])) {
                $a['nName'] = 'z';
            }
            if (empty($b['nName'])) {
                $b['nName'] = 'z';
            }
            return strcasecmp($a['nName'], $b['nName']);
        }
        return ($a['do'] > $b['do']) ? -1 : 1;
    }
}
if (!function_exists("nxs_arrMergeCheck")) {
    function nxs_arrMergeCheck($a1, $a2)
    {
        foreach ($a2 as $ak=>$a) {
            if (!in_array($ak, array_keys($a1))) {
                $a1[$ak] = $a;
            }
        }
        return $a1;
    }
}
if (!function_exists("nxs_contCron_js")) {
    function nxs_contCron_js()
    {
        $contCron = get_option('nxs_contCron');
        $output='<script type="text/javascript">jQuery.get( "'.home_url('?nxs-cronrun='.$contCron).'");</script>';
        echo $output;
    }
}
//## Format Message (WP)
if (!function_exists("nsFormatMessage")) {
    function nsFormatMessage($msg, $postID, $addURLParams='', $lng='', $ntOpts='')
    {
        global $ShownAds, $nxs_SNAP, $nxs_urlLen;
        if (defined('DOING_CRON') && empty($GLOBALS['nxswpdone'])) {
            $GLOBALS['nxswpdone'] = 1;
            do_action('wp');
        }
        $post = get_post($postID);
        $options = $nxs_SNAP->nxs_options;
        if (empty($ntOpts)) {
            $ntOpts = array();
        }
        if (!empty($options['brokenCntFilters'])) {
            $msg = str_replace('%FULLTITLE%', '%TITLE%', $msg);
            $msg = str_replace('%PANNOUNCE%', '%ANNOUNCE%', $msg);
            $msg = str_replace('%PANNOUNCER%', '%ANNOUNCER%', $msg);
            $msg = str_replace('%EXCERPT%', '%RAWEXCERPT%', $msg);
            $msg = str_replace('%FULLTEXT%', '%RAWTEXT%', $msg);
        }
        if (!empty($options['nxsHTSpace'])) {
            $htS = $options['nxsHTSpace'];
        } else {
            $htS = '';
        }
        if (!empty($options['nxsHTSepar'])) {
            $htSep = $options['nxsHTSepar'];
        } else {
            $htSep = ', ';
        }
        $htSep = str_replace('_', ' ', $htSep);
        $htSep = str_replace('c', ',', $htSep);
        // if ($addURLParams=='' && $options['addURLParams']!='') $addURLParams = $options['addURLParams'];
        $msg = str_replace('%TEXT%', '%EXCERPT%', $msg);
        $msg = str_replace('%RAWEXTEXT%', '%RAWEXCERPT%', $msg);
        $msg = stripcslashes($msg);
        if (isset($ShownAds)) {
            $ShownAdsL = $ShownAds;
        } // $msg = htmlspecialchars(stripcslashes($msg));
        $msg = nxs_doSpin($msg);
        if (stripos($msg, '%URL%')!==false) {
            if (!empty($ntOpts) && !empty($ntOpts['urlToUse'])) {
                $url = $ntOpts['urlToUse'];
            } else {
                $oo=array();
                $oo = nxs_getURL($oo, $postID, $addURLParams);
                $url = $oo['urlToUse'];
            }
            $nxs_urlLen = nxs_strLen($url);
            $msg = str_ireplace("%URL%", $url, $msg);
        }
        if (stripos($msg, '%SLUG%')!==false) {
            $msg = str_ireplace("%SLUG%", $post->post_name, $msg);
        }
        if (stripos($msg, '%MYURL%')!==false) {
            $url =  get_post_meta($postID, 'snap_MYURL', true);
            if ($addURLParams!='') {
                $url .= (strpos($url, '?')!==false?'&':'?').$addURLParams;
            }
            $nxs_urlLen = nxs_strLen($url);
            $msg = str_ireplace("%MYURL%", $url, $msg);
        }
        if (stripos($msg, '%SURL%')!==false) {
            if (!empty($ntOpts) && !empty($ntOpts['surlToUse'])) {
                $url = $ntOpts['surlToUse'];
            } else {
                if (!empty($ntOpts) && !empty($ntOpts['urlToUse'])) {
                    $url = $ntOpts['urlToUse'];
                } else {
                    $oo=array();
                    $oo = nxs_getURL($oo, $postID, $addURLParams);
                    $url = $oo['urlToUse'];
                }
                $url = nxs_mkShortURL($url, $postID);
            }
            $nxs_urlLen = nxs_strLen($url);
            $msg = str_ireplace("%SURL%", $url, $msg);
        }
        if (stripos($msg, '%ORID%')!==false) {
            $msg = str_ireplace("%ORID%", $postID, $msg);
        }
        if (stripos($msg, '%IMG%')!==false) {
            $imgURL = nxs_getPostImage($postID, !empty($ntOpts['wpImgSize'])?$ntOpts['wpImgSize']:'full');
            $msg = str_ireplace("%IMG%", $imgURL, $msg);
        }
        if (stripos($msg, '%TITLE%')!==false) {
            $title = nxs_doQTrans($post->post_title, $lng);
            $msg = str_ireplace("%TITLE%", $title, $msg);
        }
        if (stripos($msg, '%FULLTITLE%')!==false) {
            $title = apply_filters('the_title', nxs_doQTrans($post->post_title, $lng));
            $msg = str_ireplace("%FULLTITLE%", $title, $msg);
        }
        if (stripos($msg, '%STITLE%')!==false) {
            $title = nxs_doQTrans($post->post_title, $lng);
            $title = substr($title, 0, 115);
            $msg = str_ireplace("%STITLE%", $title, $msg);
        }
        if (stripos($msg, '%AUTHORNAME%')!==false) {
            $aun = $post->post_author;
            $aun = get_the_author_meta('display_name', $aun);
            $msg = str_ireplace("%AUTHORNAME%", $aun, $msg);
        }
        if (stripos($msg, '%AUTHORTWNAME%')!==false) {
            $aun = $post->post_author;
            $aun = get_the_author_meta('twitter', $aun);
            $msg = str_ireplace("%AUTHORTWNAME%", $aun, $msg);
        }
        if (stripos($msg, '%ANNOUNCE%')!==false) {
            $postContent = nxs_doQTrans($post->post_content, $lng);
            $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
            if (stripos($postContent, '#####!--more--!#####')!==false) {
                $postContentEx = explode('#####!--more--!#####', $postContent);
                $postContent = $postContentEx[0];
            } else {
                $postContent = nsTrnc($postContent, $options['anounTagLimit'], ' ', '');
            }
            $msg = str_ireplace("%ANNOUNCE%", $postContent, $msg);
        }
        if (stripos($msg, '%PANNOUNCE%')!==false) {
            $postContent = apply_filters('the_content', nxs_doQTrans($post->post_content, $lng));
            $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
            if (stripos($postContent, '#####!--more--!#####')!==false) {
                $postContentEx = explode('#####!--more--!#####', $postContent);
                $postContent = $postContentEx[0];
            } else {
                $postContent = nsTrnc($postContent, $options['anounTagLimit'], ' ', '');
            }
            $msg = str_ireplace("%PANNOUNCE%", $postContent, $msg);
        }
        if (stripos($msg, '%ANNOUNCER%')!==false) {
            $postContent = nxs_doQTrans($post->post_content, $lng);
            $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
            if (stripos($postContent, '#####!--more--!#####')!==false) {
                $postContentEx = explode('#####!--more--!#####', $postContent);
                $postContent = $postContentEx[1];
            } else {
                $postContent = str_replace(nsTrnc($postContent, $options['anounTagLimit'], ' ', ''), '', $postContent);
            }
            $msg = str_ireplace("%ANNOUNCER%", $postContent, $msg);
        }
        if (stripos($msg, '%PANNOUNCER%')!==false) {
            $postContent = apply_filters('the_content', nxs_doQTrans($post->post_content, $lng));
            $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
            if (stripos($postContent, '#####!--more--!#####')!==false) {
                $postContentEx = explode('#####!--more--!#####', $postContent);
                $postContent = $postContentEx[1];
            } else {
                $postContent = str_replace(nsTrnc($postContent, $options['anounTagLimit'], ' ', ''), '', $postContent);
            }
            $msg = str_ireplace("%PANNOUNCER%", $postContent, $msg);
        }
        if (stripos($msg, '%EXCERPT%')!==false) {
            $exc = get_the_excerpt($post);
            if (!empty($exc)) {
                $excerpt = strip_tags(strip_shortcodes(apply_filters('the_content', nxs_doQTrans($exc, $lng))));
            } else {
                $excerpt= nsTrnc(strip_tags(strip_shortcodes(apply_filters('the_content', nxs_doQTrans($post->post_content, $lng)))), 300, " ", "...");
            }
            $msg = str_ireplace("%EXCERPT%", $excerpt, $msg);
        }
        if (stripos($msg, '%RAWEXCERPT%')!==false) {
            $exc = get_the_excerpt($post);
            if (!empty($exc)) {
                $excerpt = strip_tags(strip_shortcodes(nxs_doQTrans($exc, $lng)));
            } else {
                $excerpt= nsTrnc(strip_tags(strip_shortcodes(nxs_doQTrans($post->post_content, $lng))), 300, " ", "...");
            }
            $msg = str_ireplace("%RAWEXCERPT%", $excerpt, $msg);
        }
        if (stripos($msg, '%RAWEXCERPTHTML%')!==false) {
            $exc = get_the_excerpt($post);
            if (!empty($exc)) {
                $excerpt = strip_shortcodes(nxs_doQTrans($exc, $lng));
            } else {
                $excerpt= nsTrnc(strip_tags(strip_shortcodes(nxs_doQTrans($post->post_content, $lng))), 300, " ", "...");
            }
            $msg = str_ireplace("%RAWEXCERPTHTML%", $excerpt, $msg);
        }
        $tagsExclFrmHT = $options['tagsExclFrmHT'];
        $tagsExclFrmHT = explode(',', $tagsExclFrmHT);
        foreach ($tagsExclFrmHT as $i=>$et) {
            $tagsExclFrmHT[$i] = trim(strtolower($et));
        }
        if (stripos($msg, '%TAGS%')!==false) {
            $t = wp_get_object_terms($postID, 'product_tag');
            if (empty($t) || is_wp_error($t) || !is_array($t)) {
                $t = wp_get_post_tags($postID);
            }
            $tggs = array();
            foreach ($t as $tagA) {
                $tg = $tagA->name;
                if (!in_array(strtolower($tg), $tagsExclFrmHT)) {
                    $tggs[] = $tg;
                }
            }
            $tags = implode(', ', $tggs);
            $msg = str_ireplace("%TAGS%", $tags, $msg);
        }
        if (stripos($msg, '%CATS%')!==false) {
            $t = wp_get_post_categories($postID);
            $cats = array();
            foreach ($t as $c) {
                $cat = get_category($c);
                $tg = str_ireplace('&', '&amp;', $cat->name);
                if (!in_array(strtolower($tg), $tagsExclFrmHT)) {
                    $cats[] = $tg;
                }
            }
            $ctts = implode(', ', $cats);
            $msg = str_ireplace("%CATS%", $ctts, $msg);
        }
        if (stripos($msg, '%HCATS%')!==false) {
            $t = wp_get_post_categories($postID);
            $cats = array();
            foreach ($t as $c) {
                $cat = get_category($c);
                $tg = trim(str_replace(' ', $htS, str_replace('  ', ' ', trim(str_ireplace('&', '', str_ireplace('&amp;', '', $cat->name))))));
                if (!in_array(strtolower($tg), $tagsExclFrmHT)) {
                    $cats[] = '#'.$tg;
                }
            }
            $ctts = implode($htSep, $cats);
            $msg = str_ireplace("%HCATS%", $ctts, $msg);
        }
        if (stripos($msg, '%HTAGS%')!==false) {
            $t = wp_get_object_terms($postID, 'product_tag');
            if (empty($t) || is_wp_error($t) || !is_array($t)) {
                $t = wp_get_post_tags($postID);
            }
            $tggs = array();
            foreach ($t as $tagA) {
                $tg = trim(str_replace(' ', $htS, nxs_clean_string(trim(nxs_ucwords(str_ireplace('&', '', str_ireplace('&amp;', '', $tagA->name)))))));
                if (!in_array(strtolower($tg), $tagsExclFrmHT)) {
                    $tggs[] = '#'.$tg;
                }
            }
            $tags = implode($htSep, $tggs);
            $msg = str_ireplace("%HTAGS%", $tags, $msg);
        }
        if (preg_match('/%+CF-[a-zA-Z0-9-_]+%/', $msg)) {
            $msgA = explode('%CF', $msg);
            $mout = '';
            foreach ($msgA as $mms) {
                if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false) {
                    $mGr = CutFromTo($mms, '-', '%');
                    $cfItem =  get_post_meta($postID, $mGr, true);
                    $mms = str_ireplace("-".$mGr."%", $cfItem, $mms);
                }
                $mout .= $mms;
            }
            $msg = $mout;
        }
        $mm = array();
        if (preg_match_all('/%H?CT-[a-zA-Z0-9_]+%/', $msg, $mm)) {
            $msgA = explode('%CT', str_ireplace("%HCT", "%CT", $msg));
            $mout = '';
            $i = 0;
            foreach ($msgA as $mms) {
                if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false) {
                    $h = strpos($mm[0][$i], '%HCT-')!==false;
                    $i++;
                    $mGr=CutFromTo($mms, '-', '%');
                    $cfItem=wp_get_post_terms($postID, $mGr, array("fields"=>"names"));
                    if (is_nxs_error($cfItem)) {
                        nxs_addToLogN('E', 'Error', 'MSG', '-=ERROR=- '.$mGr.'|'.print_r($cfItem, true), '');
                        $mms=str_ireplace("-".$mGr."%", '', $mms);
                    } else {
                        $tggs = array();
                        //foreach ($cfItem as $frmTag) { if ($h) $frmTag = trim(str_replace(' ', $htS, preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(nxs_ucwords(str_ireplace('&','',str_ireplace('&amp;','',$frmTag)))))));
                        foreach ($cfItem as $frmTag) {
                            if ($h) {
                                $frmTag = trim(str_replace(' ', $htS, nxs_clean_string(trim(nxs_ucwords(str_ireplace('&', '', str_ireplace('&amp;', '', $frmTag)))))));
                            }

                            $tggs[] = ($h?'#':'').$frmTag;
                        }
                        $cfItem = implode(' ', $tggs);
                        $mms=str_ireplace("-".$mGr."%", $cfItem, $mms);
                    }
                }
                $mout.=$mms;
            }
            $msg = $mout;
        }
        if (stripos($msg, '%FULLTEXT%')!==false) {
            $postContent = apply_filters('the_content', nxs_doQTrans($post->post_content, $lng));
            $msg = str_ireplace("%FULLTEXT%", $postContent, $msg);
        }
        if (stripos($msg, '%RAWTEXT%')!==false) {
            $postContent = nxs_doQTrans($post->post_content, $lng);
            $msg = str_ireplace("%RAWTEXT%", $postContent, $msg);
        }
        if (stripos($msg, '%SITENAME%')!==false) {
            $siteTitle = htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES);
            $msg = str_ireplace("%SITENAME%", $siteTitle, $msg);
        }
        if (stripos($msg, '%POSTFORMAT%')!==false) {
            $gg = get_post_format($postID);
            $txt =  get_post_format_string($gg ? $gg : 'Post');
            if (empty($txt)) {
                $txt = 'Post';
            }
            $msg = str_ireplace("%POSTFORMAT%", $txt, $msg);
        }
        if (stripos($msg, '%POSTDATE%')!==false) {
            $txt = get_the_date('', $postID);
            $msg = str_ireplace("%POSTDATE%", $txt, $msg);
        }
        if (stripos($msg, '%POSTTIME%')!==false) {
            $txt = get_the_time('', $postID);
            $msg = str_ireplace("%POSTTIME%", $txt, $msg);
        }
        if (isset($ShownAds)) {
            $ShownAds = $ShownAdsL;
        } // FIX for the quick-adsense plugin
        return trim($msg);
    }
}
//## Save Global Settings
if (!function_exists("nxs_save_glbNtwrks")) {
    function nxs_save_glbNtwrks($nt, $ii, $ntOptsOrVal, $field='', $networks='')
    {
        if (empty($ii) && $ii!=0 && $ii!='0') {
            return;
        }
        if (empty($networks)) {
            if ($field=='*') {
                $field='';
                $merge = true;
            } else {
                $merge = false;
            }
            if (function_exists("nxs_settings_open")) {
                $networks = nxs_settings_open();
            } else {
                if (class_exists('nxs_SNAP')) {
                    global $nxs_SNAP;
                    if (!isset($nxs_SNAP)) {
                        $nxs_SNAP = new nxs_SNAP();
                    }
                    $networks = $nxs_SNAP->nxs_accts;
                }
            }
        }
        if (!empty($field)) {
            $networks[$nt][$ii][$field] = $ntOptsOrVal;
        } else {
            $networks[$nt][$ii] = $merge?(array_merge($networks[$nt][$ii], $ntOptsOrVal)):$ntOptsOrVal;
        }
        if (function_exists('nxs_settings_save')) {
            nxs_settings_save($networks);
        }
        if (isset($nxs_SNAP)) {
            $nxs_SNAP->saveNetworksOptions($networks);
        } // prr($networks[$nt]); var_dump($merge);
    }
}
if (!function_exists("nxs_save_ntwrksOpts")) {
    function nxs_save_ntwrksOpts($networks)
    {
        if (function_exists('nxs_settings_save')) {
            nxs_settings_save($networks);
        } elseif (function_exists('get_option') && !empty($networks)) {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $nxs_SNAP->saveNetworksOptions($networks, 1);
        }
    }
}

if (!function_exists("nxs_saveOption")) {
    function nxs_saveOption($optName, $val)
    {
        if (function_exists('nxs_settings_save') && function_exists('nxs_settings_open')) {
            $n = nxs_settings_open();
            if (!empty($n) && is_array($n)) {
                $n['_opts'][$optName] = $val;
            }
            nxs_settings_save($n);
        } elseif (function_exists('get_option')) {
            update_option($optName, $val, false);
        }
    }
}
if (!function_exists("nxs_getOption")) {
    function nxs_getOption($optName)
    {
        $val = '';
        if (function_exists('nxs_settings_open')) {
            $n = nxs_settings_open();
            if (!empty($n) && !empty($n['_opts']) && !empty($n['_opts'][$optName])) {
                $val = $n['_opts'][$optName];
            }
        } elseif (function_exists('get_option')) {
            $val = get_option($optName);
        }
        return maybe_unserialize($val);
    }
}

//## No Lib Warning
if (!function_exists("nxs_show_noLibWrn")) {
    function nxs_show_noLibWrn($msg)
    {
        ?> <div style="border: 2px solid darkred; padding: 25px 15px 15px 15px; margin: 3px; background-color: #fffaf0;"> 
            <span style="font-size: 16px; color:darkred;"><?php echo $msg ?></span>&nbsp;<a href="https://www.nextscripts.com/faq/third-party-libraries-autopost-google-pinterest/" target="_blank">More info about third party API libraries.</a><br/><hr/> <div style="font-size: 16px; color:#005800; font-weight: bold; margin-top: 12px; margin-bottom: 7px;">You can get this API library from NextScripts.</div>
            <div style="padding-bottom: 5px;"><a href="https://www.nextscripts.com/snap-api/">SNAP Premium API libraries package</a> adds autoposting to:</div> <span class="nxs_txtIcon nxs_ti_fb">Facebook</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_gp">Google+</span>, <span class="nxs_txtIcon nxs_ti_pn">Pinterest</span>, <span class="nxs_txtIcon nxs_ti_rd">Reddit</span>, <span class="nxs_txtIcon nxs_ti_bg">Blogger</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_yt">YouTube</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_fp">Flipboard</span>, <span class="nxs_txtIcon nxs_ti_li">LinkedIn Groups</span><br><br>          
            <div style="padding-bottom: 10px; padding-top: 7px;" align="center">            
<b style="color: #008000">[Limited Time Only Offer]</b> <br> Get a lifetime SNAP PRO Plugin license for <b>Free</b> with the order of SNAP Premium API for WordPress</div>
            <div align="center"><a target="_blank" href="https://www.nextscripts.com/snap-api-premium-for-wordpress/#getit" class="NXSButton" id="nxs_snapUPG">Get SNAP Pro Plugin with SNAP API</a></div>
            <div style="font-size: 10px; margin-top: 20px;">*If you already have API, please follow instructions from the readme.txt file.</div>
          </div> <?php
    }
}
//## Tests
if (!function_exists("nxs_memCheck")) {
    function nxs_memCheck()
    {
        $mLimit = (int) ini_get('memory_limit');
        $mLimit = empty($mLimit) ? __('N/A') :$mLimit . __(' MByte');
        $mUsageP = function_exists('memory_get_usage') ? round(memory_get_peak_usage() / 1024 / 1024, 2) : 0;
        $mUsageP = empty($mUsageP) ? __('N/A') : $mUsageP . __(' MByte');

        $mUsage = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0;
        $mUsage = empty($mUsage) ? __('N/A') : $mUsage . __(' MByte'); ?>
    <div><strong><?php _e('PHP Version'); ?></strong>: <span><?php echo PHP_VERSION; ?>;&nbsp;</span>
      <strong><?php _e('PHP Memory limit'); ?></strong>: <span><?php echo $mLimit; ?>; &nbsp;</span><br/>
      <strong><?php _e('Memory usage'); ?></strong>: <span><?php echo $mUsage; ?>; &nbsp;</span> <strong><?php _e('Peak memory usage'); ?></strong>: <span><?php echo $mUsageP; ?>; &nbsp;</span>      
    </div> <?php
    }
}
//## Check SSL Sec
if (!function_exists("nxsCheckSSLCurl")) {
    function nxsCheckSSLCurl($url)
    {
        $ch = curl_init($url);
        $headers = array();
        $headers[] = 'Accept: text/html, application/xhtml+xml, */*';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Accept-Language: en-us';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)");
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        if ($err!=0) {
            return array('errNo'=>$err, 'errMsg'=>$errmsg);
        } else {
            return false;
        }
    }
}
if (!function_exists("nxs_cron_check")) {
    function nxs_cron_check()
    {
        if (stripos($_SERVER["REQUEST_URI"], 'wp-cron.php')!==false) {
            $cronCheckArray = get_option('NXS_cronCheck');
            if (empty($cronCheckArray)) {
                $cronCheckArray = array('cronCheckStartTime'=>time(), 'cronChecks'=>array());
            }
            if (($cronCheckArray['cronCheckStartTime']+900)>time()) {
                ($offset = get_option('gmt_offset') * HOUR_IN_SECONDS);
                $cronCheckArray['cronChecks'][] = '['.date_i18n('Y-m-d H:i:s', $_SERVER["REQUEST_TIME"]+$offset).'] - WP Cron called from '.(!empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'Unknown IP').' ('.(!empty($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:'Unknown UA').')';
            //nxs_addToLogN('S', 'Cron Check', '', 'WP Cron called from '.(!empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'Unknown IP').' ('.$_SERVER["HTTP_USER_AGENT"].')', date_i18n('Y-m-d H:i:s', $_SERVER["REQUEST_TIME"]+$offset));
            } elseif (empty($cronCheckArray['status']) &&  is_array($cronCheckArray['cronChecks'])) {
                $cronCheckArray['status'] = (count($cronCheckArray['cronChecks'])<17 && count($cronCheckArray['cronChecks'])>1)?1:0;
            }
            update_option("NXS_cronCheck", $cronCheckArray, false);
        }
    }
}

//## New RePoster popup
if (!function_exists('nxs_rpstPopupCode')) {
    function nxs_rpstPopupCode()
    {
        if (!empty($_POST['nxs_snap_reposter_update'])) {
            nxs_Filters::save_filter($_GET['item']);
        }
        nxs_Filters::showEdit('nxs-flt-popup'); ?>
  <form method="post" id="nxs_form_rep"> <input name="action" value="nxs_snap_aj" type="hidden" /><input name="nxsact" value="saveRpst" type="hidden" />
    <div class="wrap"><div id="nxsDivWrap"><h2><?php _e('Add New Reposter Action'); ?></h2>
      <div id="poststuffPopup" class="metabox-holder">
        <div id="post-body" class="has-sidebar"><div id="post-body-content" class="has-sidebar-content"><?php do_meta_boxes('nxs-flt-popup', 'normal', null); ?></div></div>
      </div>
    </div></div> 
    <div id="nsx_addFlt"><input type="button" id="svBtn" onclick="nxs_svRep('')" class="button-primary" value="Save Reposter"></div>
  </form><?php
    }
}

//## ShortCode [nxs_links acctype='' accnum=0 useicons='']
if (!function_exists('nxs_links_func')) {
    function nxs_links_func($atts)
    {
        extract(shortcode_atts(array('accnum' => '0', 'acctype' => '', 'useicons' => ''), $atts));
        $txtOut = '';
        global $nxs_snapAvNts;
        $pid = get_the_ID();
        if (!empty($acctype)) {
            $po =  maybe_unserialize(get_post_meta($pid, 'snap'.strtoupper($acctype), true));
            foreach ($nxs_snapAvNts as $avNt) {
                if (strtoupper($acctype)==$avNt['code']) {
                    break;
                }
            }
            if (is_array($po)) {
                if (!empty($accnum)) {
                    if (is_array($po[$accnum]) && !empty($po[$accnum]['postURL'])) {
                        $po = $po[$accnum]['postURL'];
                        $txtOut .= '<a target="_blank" href="'.$po.'">'.(!empty($useicons)?'<img class="nxs-bklIcon" src="'.NXS_PLURL.'img/'.$avNt['lcode'].'16.png"/>':'').$avNt['name'].'</a><br/>';
                    }
                } else {
                    foreach ($po as $pp) {
                        if (is_array($pp) && !empty($pp['postURL'])) {
                            $po = $pp['postURL'];
                            $txtOut .= '<a target="_blank" href="'.$po.'">'.(!empty($useicons)?'<img class="nxs-bklIcon" src="'.NXS_PLURL.'img/'.$avNt['lcode'].'16.png"/>':'').$avNt['name'].'</a><br/>';
                        }
                    }
                }
            }
        } else {
            foreach ($nxs_snapAvNts as $avNt) {
                $po = maybe_unserialize(get_post_meta($pid, 'snap'.strtoupper($avNt['code']), true));
                if (is_array($po)) {
                    if (!empty($accnum)) {
                        if (is_array($po[$accnum]) && !empty($po[$accnum]['postURL'])) {
                            $po = $po[$accnum]['postURL'];
                            $txtOut .= '<a target="_blank" href="'.$po.'">'.(!empty($useicons)?'<img class="nxs-bklIcon" src="'.NXS_PLURL.'img/'.$avNt['lcode'].'16.png"/>':'').$avNt['name'].'</a><br/>';
                        }
                    } else {
                        foreach ($po as $pp) {
                            if (is_array($pp) && !empty($pp['postURL'])) {
                                $po = $pp['postURL'];
                                $txtOut .= '<a target="_blank" href="'.$po.'">'.(!empty($useicons)?'<img style="padding-right: 3px;" src="'.NXS_PLURL.'img/'.$avNt['lcode'].'16.png"/>':'').$avNt['name'].'</a><br/>';
                            }
                        }
                    }
                }
            }
        }
        return $txtOut;
    }
}

//## ShortCode [nxs_fbembed accnum=0]
if (!function_exists("nxs_postedlinks_func")) {
    function nxs_postedlinks_func($atts)
    {
        extract(shortcode_atts(array('accnum' => '0'), $atts));
        $pid = get_the_ID();
        global $nxs_snapAvNts;
        $txtOut = '';
        global $plgn_NS_SNAutoPoster;
        if (!isset($plgn_NS_SNAutoPoster)) {
            return;
        }
        $options = $plgn_NS_SNAutoPoster->nxs_options;

        foreach ($nxs_snapAvNts as $avNt) {
            $opt =  get_post_meta($pid, 'snap'.$avNt['code'], true);
            $opt =  maybe_unserialize($opt);
            if (!empty($opt) && is_array($opt)) {
                foreach ($opt as $ii=>$op) {
                    if (!empty($op['postURL'])) {
                        $txtOut .= '<a href="'.$op['postURL'].'">'.$avNt['name'].(!empty($options[$avNt['lcode']][$ii]['nName'])?' ('.$options[$avNt['lcode']][$ii]['nName'].')':'').'</a><br/>';
                    }
                }
            }
        }
        return $txtOut;
    }
}

if (function_exists("add_shortcode")) {
    add_shortcode('nxs_postedlinks', 'nxs_postedlinks_func');
}

//## ShortCode [nxs-ntinsrlist accnum=0]
if (!function_exists("nxs_scInstrList")) {
    function nxs_scInstrList($atts)
    {
        extract(shortcode_atts(array('accnum' => '0'), $atts));
        global $nxs_snapAvNts;
        $outHtml = '<div class="nxsright"><div style="padding-left: 0px; padding-bottom:5px;"><a style="font-size: 14px;" target="_blank" href="http://www.nextscripts.com/instructions/">'.__('Setup/Installation Instructions:', 'social-networks-auto-poster-facebook-twitter-g').'</a></div>';
        foreach ($nxs_snapAvNts as $avNt) {
            $clName = 'nxs_snapClass'.$avNt['code'];
            $nt = new $clName();
            $outHtml .= '<div style="padding-left: 10px; padding-top:5px;"><a style="background-image:url('.NXS_PLURL.'img/'.$avNt['lcode'].'16.png) !important;" class="nxs_icon16" target="_parent" href="'.$nt->ntInfo['instrURL'].'">  '.$nt->ntInfo['name'].'</a></div>';
        }
        $outHtml .= '</div>';
        return $outHtml;
    }
}

if (function_exists("add_shortcode")) {
    add_shortcode('nxs-ntinsrlist', 'nxs_scInstrList');
}
?>
