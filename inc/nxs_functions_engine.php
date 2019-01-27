<?php
//## Main Function to Post
if (!function_exists("nxs_snapPublishTo")) {
    function nxs_snapPublishTo($postIDorObj, $aj=false)
    {
        global $nxs_SNAP, $nxs_snapAvNts, $blog_id, $nxs_tpWMPU;
        $uid=0; //  echo " | nxs_doSMAS2 | "; prr($postArr);
        if (!isset($nxs_SNAP)) {
            return;
        }
        if (!empty($_POST['nxs_snapPostOptions'])) {
            $NXS_POSTX = $_POST['nxs_snapPostOptions'];
            $NXS_POST = array();
            $NXS_POST = NXS_parseQueryStr($NXS_POSTX);
        } else {
            $NXS_POST = $_POST;
        }
        if (is_object($postIDorObj)) {
            $postObj = $postIDorObj;
            $postID = $postObj->ID;
        } else {
            $postID = $postIDorObj;
            $postObj = get_post($postID);
        }
        unset($postIDorObj);
        $isPost = isset($NXS_POST["snapEdIT"]);
  
        $postUser = $postObj->post_author;
        $isItUserWhoCan = (!user_can($postUser, 'manage_options') && user_can($postUser, 'haveown_snap_accss')); //if ($isItUserWhoCan) $uid = $postUser;
        if ($isItUserWhoCan) {
            $nxs_SNAP = new nxs_SNAP($postUser);
            $networks = $nxs_SNAP->nxs_acctsU;
            $uid = $postUser;
            global $nxs_uid;
            $nxs_uid = $uid;
        } else {
            $networks = $nxs_SNAP->nxs_accts;
        }
  
        $options = $nxs_SNAP->nxs_options;
        if (empty($options)) {
            return;
        }
        if ($postObj->post_status != 'publish') {
            sleep(5);
            $postObj = get_post($postID);
            if ($postObj->post_status != 'publish') {
                nxs_LogIt('I', 'Cancelled', '', '', 'Autopost Cancelled', 'Post is not "Published" Right now - Post ID:('.$postID.') - Current Post status -'.$postObj->post_status, 'snap', $uid);
                return;
            }
        }
        //## User Security (&& MU)
        if ($isPost && empty($options['skipSecurity']) && !current_user_can("make_snap_posts") && !current_user_can("haveown_snap_accss") && !current_user_can("manage_options")) {
            nxs_LogIt('I', 'Skipped', '', '', 'Current user can\'t autopost - Post ID:('.$postID.')', '', 'snap', $uid);
            return;
        }
        if (empty($options['skipSecurity']) && !user_can($postUser, "make_snap_posts") && !user_can($postUser, "haveown_snap_accss") && !user_can($postUser, "manage_options")) {
            nxs_LogIt('I', 'Skipped', '', '', 'User ID '.$postUser.' can\'t autopost (please see <a target="_blank" href="https://www.nextscripts.com/support-faq/#a17">FAQ #1.7</a> for more info/solution)  - Post ID:('.$postID.')', '', 'snap', $uid);
            return;
        }
        //## Post
        if ($isPost) {
            $nxs_SNAP->NS_SNAP_SavePostMetaTags($postID);
        }
    
        if (!isset($options['nxsHTDP']) || $options['nxsHTDP']=='S') {
            if (isset($NXS_POST["snapEdIT"]) && $NXS_POST["snapEdIT"]=='1') {
                $publtype='S';
                $delay = rand(2, 10);
            } else {
                $publtype='A';
            }
            if (!$aj && !empty($options['quLimit']) && $options['quLimit']=='1') {
                global $wpdb; //## Add to posting query
                $quNxTime=$wpdb->get_var("SELECT timetorun FROM ".$wpdb->prefix."nxs_query WHERE type='Q' ORDER BY timetorun DESC LIMIT 1");
                if (empty($quNxTime)) {
                    $quNxTime = time()+5;
                }
                $quNxTime=strtotime($quNxTime);
                $rndSec=$options['quLimitRndMins']*60;
                $pstEvrySec=$options['quDays']*86400+$options['quHrs']*3600+$options['quMins']*60;
                $rndTime=rand(0-$rndSec, $rndSec);
                $quNxTime=$quNxTime + $pstEvrySec + $rndTime;
                $dbItem = array('datecreated'=>date_i18n('Y-m-d H:i:s'), 'type'=>'Q', 'postid'=>$postID, 'timetorun'=> date_i18n('Y-m-d H:i:s', $quNxTime), 'descr'=> 'Post ID:('.$postID.')', 'uid'=>$postUser);
                $nxDB = $wpdb->insert($wpdb->prefix . "nxs_query", $dbItem);
                $lid = $wpdb->insert_id;
                nxs_recountQueryTimes();
                nxs_LogIt('I', 'Queried', '', '', $lid.'| Post ID:('.$postID.')', '', 'snap', $uid);
                return;
            }
        } else {
            $publtype = 'I';
        }
    
        //## Apply Global Filters (At the time of publishing)
        $rMsg = nxs_snapCheckFilters($options, $postObj);
        if ($rMsg!==false) {
            nxs_LogIt('I', 'Skipped', '', '', 'Filter(Global) - Excluded - Post ID:('.$postID.')', $rMsg, 'snap', $uid);
            return;
        }
    
        nxs_LogIt('BG', 'Start =- ', '', '', '------=========#### NEW AUTO-POST REQUEST ####=========------', ($blog_id>1?'BlogID:'.$blog_id:'').' PostID:('.$postID.') '.($publtype=='S'?'Scheduled +'.$delay:($publtype=='A'?'Automated':'Immediate')), 'snap', $uid);
    
        $snap_isAutoPosted = get_post_meta($postID, 'snap_isAutoPosted', true);
        if (!empty($snap_isAutoPosted)) {
            $snap_isAutoPosted = ($snap_isAutoPosted>1)?'(on '.date_i18n('Y-m-d H:i:s', $snap_isAutoPosted).')':'';
            nxs_LogIt('W', 'Skipped', '', '', 'Already Autoposted ', $snap_isAutoPosted.' - Post ID:('.$postID.')', 'snap', $uid);
            return;
        }
        $snap_isEdIT = get_post_meta($postID, 'snapEdIT', true);     //     var_dump($snap_isEdIT);
    
        if (function_exists('nxs_v4doSMAS2')) {
            nxs_v4doSMAS2($postObj, $NXS_POST, $publtype, $aj);
            return;
        } else { #################################### !!!!!!!!!!!!!!!!!!!!!!!!!!!
  
            foreach ($nxs_snapAvNts as $avNt) {
                if (!empty($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) {
                    $clName = 'nxs_snapClass'.$avNt['code'];
                    $ntClInst = new $clName();
                    $publTempType = '';
                    if ($isPost && isset($NXS_POST[$avNt['lcode']])) {
                        $po = $NXS_POST[$avNt['lcode']];
                    } else {
                        $po =  get_post_meta($postID, 'snap'.$avNt['code'], true);
                        $po =  maybe_unserialize($po);
                    }
                    if (isset($po) && is_array($po)) {
                        $isPostMeta = true;
                    } else {
                        $isPostMeta = false;
                        $po = $networks[$avNt['lcode']];
                        update_post_meta($postID, 'snap'.$avNt['code'], $po);
                    } // prr($po);
                    delete_post_meta($postID, 'snap_isAutoPosted');
                    add_post_meta($postID, 'snap_isAutoPosted', time());
                    $optMt = $networks[$avNt['lcode']][0];
                    if ($isPostMeta) {
                        $optMt = $ntClInst->adjMetaOpt($optMt, $po[0]);
                    }
                    if (!$ntClInst->checkIfSetupFinished($optMt)) {
                        continue;
                    } //prr($optMt);
                    if ($optMt['do']=='2') {
                        $rMsg = nxs_snapCheckFilters($optMt, $postObj);
                        if ($rMsg!==false) {
                            nxs_LogIt('I', 'Skipped', $avNt['name'].' ('.$optMt['nName'].')', '', 'Filter(Network) - Excluded - Post ID:('.$postID.')', $rMsg, 'snap', $uid);
                            continue;
                        } else {
                            $optMt['do'] = 1;
                        }
                    }
                    if ($optMt['do']=='1') {
                        $optMt['ii'] = 0;
                        if ($publtype=='A' && ($optMt['nMin']>0 || $optMt['nHrs']>0 || !empty($optMt['nTime']))) {
                            $publTempType='S';
                        }
                        if ($publtype=='S' || $publTempType=='S') {
                            $publTempType = '';
                            if (isset($optMt['nHrs']) && isset($optMt['nMin']) && ($optMt['nHrs']>0 || $optMt['nMin']>0)) {
                                $delay = $optMt['nMin']*60+$optMt['nHrs']*3600;
                                nxs_LogIt('I', 'Delayed', $avNt['name'].' ('.$optMt['nName'].')', '', 'Post has been delayed', ' for '.$delay.' Seconds ('.($optMt['nHrs']>0?$optMt['nHrs'].' Hours':'')." ".($optMt['nMin']>0?$optMt['nMin'].' Minutes':'').')', 'snap', $uid);
                            } else {
                                $delay = rand(2, 10);
                            }
                            $optMt['timeToRun'] = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS)+$delay;
                            global $wpdb;
                            $dbItem = array('datecreated'=>date_i18n('Y-m-d H:i:s'), 'type'=>'S', 'postid'=>$postID, 'nttype'=>$avNt['code'], 'refid'=>$optMt['ii'], 'timetorun'=> date_i18n('Y-m-d H:i:s', $optMt['timeToRun']), 'extInfo'=>serialize($optMt), 'descr'=> (!empty($optMt['nName'])?'('.$avNt['code'].' - '.$optMt['nName'].') ':'').'Post ID:('.$postID.')', 'uid'=>$postUser);
                            $nxDB = $wpdb->insert($wpdb->prefix . "nxs_query", $dbItem);
                            $lid = $wpdb->insert_id;
                            nxs_LogIt('BI', 'Scheduled', $avNt['name'].' ('.$optMt['nName'].')', '', 'Scheduled for '.date_i18n('Y-m-d H:i:s', $optMt['timeToRun']).")", ' PostID:('.$postID.')', 'snap', $uid);
                        } else {
                            $ntClInst->nt[0] = $optMt;
                            $ntClInst->publishWP(0, $postID);
                        }
                    } else {
                        nxs_LogIt('GR', 'Skipped', $avNt['name'].' ('.$optMt['nName'].')', '', '-=[Unchecked Account]=-', 'PostID: '.$postID, 'snap', $uid);
                    }
                }
            }
        }
        if (isset($isS) && $isS) {
            restore_current_blog();
        }
    }
}
if (!function_exists("nxs_noSing")) {
    function nxs_noSing(&$obj)
    {
        $obj->is_singular = false;
        $obj->is_single = false;
        return $obj;
    }
}

//## Functions
if (!function_exists('nxs_getFromGlobalOpt')) {
    function nxs_getFromGlobalOpt($optName)
    {
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            if (class_exists('nxs_SNAP')) {
                $nxs_SNAP = new nxs_SNAP();
            } else {
                return '';
            }
        }
        $gOptions = $nxs_SNAP->nxs_options;
        if (!empty($gOptions[$optName])) {
            return $gOptions[$optName];
        } else {
            return '';
        }
    }
}

//## ===================== Cron/Query/Reposter Engine

//## Recount Query/Timeline
if (!function_exists("nxs_recountQueryTimes")) {
    function nxs_recountQueryTimes($force=false)
    {
        global $wpdb, $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $options = $nxs_SNAP->nxs_options;
        $currTime = nxs_getCurrTime();
        $quPosts = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "nxs_query WHERE type='Q' ORDER BY timetorun ASC", ARRAY_A);  // var_dump($quPosts);   prr($quPosts);
    if (count($quPosts)>0) {
        $pstEvrySec = $options['quDays']*86400+$options['quHrs']*3600+$options['quMins']*60;
        $rndSec = $options['quLimitRndMins']*60;
        $ttr = time(); //$ttr = strtotime('2050-10-15 10:10:10');
      //$ttr = $quPosts[0]['timetorun']; $quNxTime = ($ttr>'2050-10-15 10:10:00')?(time()+(get_option('gmt_offset')*HOUR_IN_SECONDS)):strtotime($ttr); //## ????? why did I do that row?
      $quNxTime = $ttr+(get_option('gmt_offset')*HOUR_IN_SECONDS)+$pstEvrySec;
        foreach ($quPosts as $row) {
            $id = $row['id'];
            if (!empty($row['postid'])) {
                $post = get_post($row['postid']);
                if (empty($post)) {
                    $wpdb->delete($wpdb->prefix."nxs_query", array('id' => $id));
                    continue;
                }
            }
            $quNxTimeTxt = date_i18n('Y-m-d H:i:s', $quNxTime);
            $wpdb->update($wpdb->prefix."nxs_query", array('timetorun' => $quNxTimeTxt), array('id' => $id));  //prr($quNxTimeTxt);
            $rndTime = rand(0-$rndSec, $rndSec);
            $quNxTime = $quNxTime + $pstEvrySec + $rndTime;
        }
    }
        $quPosts = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "nxs_query WHERE type='R' ORDER BY timetorun ASC", ARRAY_A); // prr($quPosts, 'KKKKKKKKKKKKKKKKKKKK');  // var_dump($quPosts);
        if (count($quPosts)>0) {
            foreach ($quPosts as $row) {
                $id = $row['id'];
                if ($force || $row['timetorun'] > date_i18n('Y-m-d H:i:s', $currTime-600)) {
                    $rpstrOpts = maybe_unserialize(get_post_meta($id, 'nxs_rpstr', true));
                    if (!empty($rpstrOpts) && is_array($rpstrOpts)) {
                        $rpstrOpts['rpstNxTime'] = nxs_getNextPostTime($rpstrOpts['rpstDays'], $rpstrOpts['rpstHrs'], $rpstrOpts['rpstMins'], $rpstrOpts['rpstRndMins']);
                        $dbItem = array('timetorun'=> date_i18n('Y-m-d H:i:s', $rpstrOpts['rpstNxTime']));
                        $whItem = array('id'=>$id);
                        $wpdb->update($wpdb->prefix . "nxs_query", $dbItem, $whItem);
                        nxs_Filters::save_meta($id, 'nxs_rpstr', $rpstrOpts);
                    }
                }
            }
        }
    }
}

if (!function_exists("nxs_pushPostToNT")) {
    function nxs_pushPostToNT($ntCode, $ii, $postID, $type='a')
    {
        $postObj = get_post($postID);
        $nt = strtolower($ntCode);
        $ntU = strtoupper($ntCode);
        $postUser = $postObj->post_author;
        $isItUserWhoCan = (!user_can($postUser, 'manage_options') && user_can($postUser, 'haveown_snap_accss')); //if ($isItUserWhoCan) $uid = $postUser;
        if ($isItUserWhoCan) {
            $nxs_SNAP = new nxs_SNAP($postUser);
            $networks = $nxs_SNAP->nxs_acctsU;
            $uid = $postUser;
            global $nxs_uid;
            $nxs_uid = $uid;
        } else {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $networks = $nxs_SNAP->nxs_accts;
        }
        $optNt = maybe_unserialize(get_post_meta($postID, 'snap'.$ntU, true));
        $clName = 'nxs_snapClass'.$ntU;
        if (class_exists($clName)) {
            $cl = new $clName();
            if (empty($optNt) || !is_array($optNt)) {
                $optNt = $networks[$nt][$ii];
            } else {
                $optNt = $cl->adjMetaOpt($networks[$nt][$ii], $optNt[$ii]);
            }
            $optNt['pType'] = 'aj';
            $cl->nt[$ii] = $optNt;
            if ($type=='r') {
                $cl->isRepost=true;
            }
            return $cl->publishWP($ii, $postID);
        }
    }
}

if (!function_exists("nxs_getCurrTime")) {
    function nxs_getCurrTime()
    {
        $tm = microtime();
        $tma = explode(' ', $tm);
        $tmCorr = (function_exists('get_option'))?get_option('gmt_offset') * HOUR_IN_SECONDS :0;
        $tm = number_format((float)$tma[0]+(float)$tma[1], 8, '.', '');
        return $tm+$tmCorr;
    }
}
if (!function_exists("nxs_getNextPostTime")) {
    function nxs_getNextPostTime($d, $h, $m, $r=0)
    {
        $currTime = nxs_getCurrTime();
        $sec = $d*86400+$h*3600+$m*60;
        $rndSecs = isset($r)?$r*60:0;
        $rndTime = rand(0-$rndSecs, $rndSecs);
        return $currTime + $sec + $rndTime;
    }
}

//## New Scheduled Engine Functions
//## Main Cron Engine Function.
if (!function_exists("nxs_checkQuery")) {
    function nxs_checkQuery()
    {
        set_time_limit(0);
        $arrOut = array();
        nxs_cron_check();
        if (empty($_SERVER["HTTP_USER_AGENT"])) {
            $_SERVER["HTTP_USER_AGENT"] = '?';
        }
        $tmCorr = get_option('gmt_offset') * HOUR_IN_SECONDS ;
        $isDebug = !empty($_GET['dbg']); //$isDebug = true;
        $tm = microtime();
        $tma = explode(' ', $tm);
        $tm = number_format((float)$tma[0]+(float)$tma[1], 8, '.', '');
        $currTime = $tm+$tmCorr;
        $tmL = get_option('nxs_last_nxs_cron');
        update_option('nxs_last_nxs_cron', $tm, false);
        $tmL2=$tmL+19;
        $tmL3=$tmL+600;
        //## Log Cron Request
    if (isset($_GET['nxs-cronrun'])) {
        $contCron = get_option('nxs_contCron');
        if ($isDebug) {
            echo $_GET['nxs-cronrun'],'('.$contCron.')';
        } //## Manual/Forced cron request.
        nxs_addToLogN('L', 'NXS Cron Request (Forced)', '', number_format(($tm-$tmL), 2, '.', '').'s after the previous one. ', 'CNT: '.$_GET['nxs-cronrun'].'('.$contCron.')'.print_r($_SERVER, true));
    } else { //## Cron request from WP itself
        if ($tm<$tmL2) {
            nxs_addToLogN('W', '**WARNING. Unhealthy Cron Request**', ' [<a target="_blank" href="http://nxs.fyi/uhcr">More info</a>] ', 'Too close ('.number_format(($tm-$tmL), 2, '.', '').'s) to the previous one. ', 'Now - '.date_i18n('H:i:s', $currTime).' | Previous - '.date_i18n('H:i:s', $tmL+$tmCorr).  '| Cron called from '.(!empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'Unknown IP').' ('.nsTrnc((!empty($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:'Unknown UA'), 70).')', 'cron');  /* return; */
        } elseif ($tm>$tmL3) {
            nxs_addToLogN('W', '**WARNING. Unhealthy Cron Request**', ' [<a target="_blank" href="http://nxs.fyi/uhcr">More info</a>] ', 'Too far ('.number_format(($tm-$tmL), 2, '.', '').'s) from the previous one. ', 'Now - '.date_i18n('H:i:s', $currTime).' | Previous - '.date_i18n('H:i:s', $tmL+$tmCorr).  '| Cron called from '.(!empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'Unknown IP').' ('.nsTrnc((!empty($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:'Unknown UA'), 70).')', 'cron');  /* return; */
        } else {
            nxs_addToLogN('L', 'Cron Request', '', number_format(($tm-$tmL), 2, '.', '').'s after the previous one. ', '| Cron called from '.(!empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'Unknown IP').' ('.nsTrnc((!empty($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:'Unknown UA'), 70).')', 'cron');
        }
    }
    
        global $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $options = $nxs_SNAP->nxs_options;
        if (empty($options['numOfTasks']) || !is_int($options['numOfTasks']) || $options['numOfTasks']<10) {
            $options['numOfTasks'] = 30;
        }
        if (!empty($options['riActive']) && $options['riActive']=='1') {
            $howOften = !empty($options['riHowOften'])?$options['riHowOften']:15;
            $lastCommentsCheck = get_option('nxs_cronLastCommentsCheck'); //$howOften = 3;
            if (!empty($lastCommentsCheck)) {
                if ($lastCommentsCheck+($howOften*60)<$currTime) {
                    update_option('nxs_cronLastCommentsCheck', $currTime, false);
                    nxs_importComments();
                    update_option('nxs_cronLastCommentsCheck', $currTime, false);
                }
            } else {
                update_option('nxs_cronLastCommentsCheck', $currTime, false);
            }
        }
        //nxs_addToLogN('L', 'Cron Run', '', 'Cron IN - '.$tm.' | Last - '.$tmL);
    global $wpdb, $doing_wp_cron; //echo "SELECT * FROM ". $wpdb->prefix . "nxs_query WHERE timetorun<'".date_i18n('Y-m-d H:i:s')."' ORDER BY timetorun DESC";
    if ($isDebug) {
        echo "==-- CRON --==";
    }
    
        //## Debug only - delete later - shows all reords in the Query
        $sqql = "SELECT * FROM ". $wpdb->prefix . "nxs_query ORDER BY timetorun DESC LIMIT ".$options['numOfTasks'];
        $quPosts = $wpdb->get_results($sqql, ARRAY_A);
        if ($isDebug) {
            prr($sqql);
            prr($quPosts);
            prr(date_i18n('Y-m-d H:i:s'));
        }
        //## / Debug only - delete later - shows all reords in the Query
    
        //## Get count of tasks
        $ttr = "FROM ". $wpdb->prefix . "nxs_query WHERE timetorun<'".date_i18n('Y-m-d H:i:s')."'";
        $quPostsCnt = $wpdb->get_var("SELECT COUNT(id) ".$ttr);
        if ($isDebug) {
            prr($ttr, 'TTR:');
        }
        if ($isDebug) {
            prr($quPostsCnt, 'COUNT:');
        }
        if ((int)$quPostsCnt<1) {
            return;
        } //## Nothing in Query - return;
        //## Get 20 tasks
        $quPosts = $wpdb->get_results("SELECT * ".$ttr." ORDER BY timetorun DESC LIMIT ".$options['numOfTasks'], ARRAY_A);
        if ($isDebug) {
            var_dump($quPosts);
            prr($quPosts);
        }
        $quPosts = array_reverse($quPosts);
        if (count($quPosts)>0) {
            foreach ($quPosts as $row) {
                $id = $row['id'];
                if (!empty($row['postid'])) {
                    $postID = $row['postid'];
                } else {
                    $postID = '';
                } //prr($row); prr($row['type']);
                switch ($row['type']) {
          case 'Q':  //## Post from Query (to All Networks)
            nxs_LogIt('BG', 'Query', '', '', '--===#### POST REQUEST FROM QUERY - Post ID:('.$postID.') ####===--', '', 'snap', $row['uid']); nxs_snapPublishTo($postID, true); $wpdb->delete($wpdb->prefix . "nxs_query", array('id'=>$id));
            if ($options['nxsOverLimit']=='D') {
                $dateC = date("d", $row['datecreated']);
                $dayN = date("d", $row['timetorun']);
                if ($dayN!=$dateC) {
                    $wpdb->delete($wpdb->prefix . "nxs_query", array('type'=>'Q'));
                }
            }
          break;
          case 'S': //## Post shedulled post. (to Individual Network)
            $res = nxs_pushPostToNT($row['nttype'], $row['refid'], $postID); if ($res=='200') {
                $arrOut[] = 'All OK - ID: '.$id.' ('.$row['nttype'].'|'.$row['refid'].'|'.$postID.')';
            }
            $wpdb->delete($wpdb->prefix . "nxs_query", array('id'=>$id)); //nxsLogIt(array('msg'=>'S-Del:'.$id, 'extInfo'=>$doing_wp_cron));
          break;
          case 'R': //## Reposter is active - check if it's time to re-post.
            $tsd = get_post_meta($row['postid'], 'nxs_rpstr'); $tsd = get_post_meta($row['postid'], 'nxs_rpstr_data');
            $fltrOpts = maybe_unserialize(get_post_meta($row['postid'], 'nxs_rpstr_data', true)); $rpstrOpts = maybe_unserialize(get_post_meta($row['postid'], 'nxs_rpstr', true)); //prr($rpstrOpts, 'rpstrOpts'); prr($fltrOpts, 'FTT OPTS');
            //## Delete Ghosted Posters
            if (empty($rpstrOpts['rpstOn'])) {
                $wpdb->delete($wpdb->prefix . "nxs_query", array( 'postid' => $row['postid'] ));
                nxs_LogIt('DBG', 'Ghosted Reposter [Deleted]', 'RP', '', 'Reposter ID:'.$row['postid'].' ', '-=[ '.print_r($rpstrOpts, true).' ]=-');
                break;
            }
            //##
            if ($rpstrOpts['rpstNxTime']>$currTime) {
                echo "Post ID: ".$row['postid']." - No TIme Yet:".$rpstrOpts['rpstNxTime']." > ".$currTime." | ". date_i18n('Y-m-d H:i:s', $rpstrOpts['rpstNxTime']). ' > '. date_i18n('Y-m-d H:i:s', $currTime)."<br/>";
                nxs_recountQueryTimes();
                break;
            }
            //## Time to Post
            nxs_LogIt('I', 'Reposter [Time to Post]', 'RPSTR', '', 'Reposter ID:'.$row['postid'].' ', '-=[ '.print_r($rpstrOpts, true).' ]=--=[ '.print_r($fltrOpts, true).' ]=-'); echo 'Reposter [Time to Post] '. 'Reposter ID:'.$row['postid'].' ';
            
            if (!empty($fltrOpts)) {
                $fltrOpts['posts_per_page'] = '1';
            
                if (isset($rpstrOpts['rpstBtwHrsType']) && $rpstrOpts['rpstBtwHrsType']=='D') {
                    //## Check Days
                    if (isset($rpstrOpts['rpstBtwDays']) && count($rpstrOpts['rpstBtwDays'])>0) {
                        $rpstBtwDays = $rpstrOpts['rpstBtwDays'];
                    } else {
                        $rpstBtwDays = array();
                    }
                    if (is_array($rpstBtwDays) && count($rpstBtwDays)>0) {
                        $currDay = (int)date_i18n('w');
                        if (!(in_array($currDay, $rpstBtwDays))) { // echo "D :( ";
                            nxs_LogIt('I', 'Reposter: Skipped - Excluded Day - '.$currDay, 'Reposter ID:'.$row['postid'].' (Post ID: '.$ids[0].')', '', '-=[ - ]=-', print_r($rpstrOpts, true));
                            continue;
                        }
                    }
                    //## Check Hours
                    if (isset($rpstrOpts['rpstBtwHrsF']) && (int)$rpstrOpts['rpstBtwHrsF']>0) {
                        $rpstBtwHrsF = (int)$rpstrOpts['rpstBtwHrsF'];
                    } else {
                        $rpstBtwHrsF = 0;
                    }
                    if (isset($rpstrOpts['rpstBtwHrsT']) && (int)$rpstrOpts['rpstBtwHrsT']>0) {
                        $rpstBtwHrsT = (int)$rpstrOpts['rpstBtwHrsT'];
                    }
                    if ($rpstBtwHrsT>0) {
                        $currHour = (int)date_i18n('H', $currTime);  //echo "H ".$currHour." ?";
                  if (!(($rpstBtwHrsF<$rpstBtwHrsT && $currHour<$rpstBtwHrsT && $currHour>=$rpstBtwHrsF) || ($rpstBtwHrsF>$rpstBtwHrsT && $currHour<$rpstBtwHrsF && $currHour>=$rpstBtwHrsT))) {  //echo "H :( ";
                    nxs_LogIt('I', 'Reposter: Skipped - Excluded Hour - '.$currHour, 'Reposter ID:'.$row['postid'].' (Post ID: '.$ids[0].')', '', '-=[ - ]=-', print_r($rpstrOpts, true));
                      continue;
                  }
                    }
                }
             
                //## Type of Post New-to-Old, Old-to-New, Random
                if (empty($rpstrOpts['rpstType']) || $rpstrOpts['rpstType']=='2') {
                    $fltrOpts['orderby']='post_date';
                    $fltrOpts['order'] = 'ASC';
                } elseif ($rpstrOpts['rpstType']=='3') {
                    $fltrOpts['orderby']='post_date';
                    $fltrOpts['order'] = 'DESC';
                } elseif ($rpstrOpts['rpstType']=='1') {
                    $fltrOpts['orderby']='rand';
                    $rpstrOpts['rpstStop']='W';
                } //else
                //## Apply filters
                nxs_removeAllWPQueryFilters();
                $ids = get_posts_ids_by_filter($fltrOpts);
                $pCnt = count($ids);
                //## Do post to all specified networks
                if (!empty($ids[0])) {
                    nxs_LogIt('I', 'Reposter [Got Post ID]', 'Reposter ID:'.$row['postid'].' (Post ID: '.$ids[0].')', '', '', '');
                    foreach ($fltrOpts['nxs_NPNts'] as $ntCode=>$ntts) {
                        foreach ($ntts as $iis=>$accs) {
                            $res = nxs_pushPostToNT($ntCode, $iis, $ids[0], 'r');
                        }
                    }
                    update_post_meta($ids[0], 'snap_isAutoPosted', '1');
                    update_post_meta($ids[0], 'snap_isRpstd'.$row['postid'], time());
                    nxs_LogIt('I', 'Reposter [After the Post]', 'Reposter ID:'.$row['postid'].' (Post ID: '.$ids[0].')', '', '-=[ ]=-', print_r($rpstrOpts, true));
                    $rpstrOpts['lastID'] = $ids[0];
                    //## Add to Stats
                    $stats = maybe_unserialize(get_post_meta($postID, 'nxs_rpstr_stats', true));
                    if (empty($stats['posted'])) {
                        $stats['posted'] = 0;
                    }
                    $stats['posted']++;
                    $stats['pstdLst'][] = $ids[0];
                    nxs_Filters::save_meta($row['postid'], 'nxs_rpstr_stats', $stats);
                    //## When finished: Repeat N times  - Stop it
                    if ($rpstrOpts['rpstStop']=='N') {
                        $n = $rpstrOpts['rpstStopRpt'];
                        if ($stats['posted']>=$n) {
                            $rpstrOpts['rpstOn'] = 0;
                            $wpdb->delete($wpdb->prefix . "nxs_query", array( 'postid' => $row['postid'] ));
                            $rpstrOpts['rpstOn'] = 'F';
                            nxs_LogIt('INF', 'Reposter Finished: Set to repeat '.$n.' times', 'RP', '', 'Reposter ID:'.$row['postid'].' ', '-=[ '.print_r($rpstrOpts, true).' ]=-');
                            nxs_Filters::save_meta($row['postid'], 'nxs_rpstr', $rpstrOpts);
                            break;
                        }
                    }
                } else {
                    echo "END of the LINE!";
                    //## Turn Reposting off
                    if ($rpstrOpts['rpstStop']=='O') {
                        $rpstrOpts['rpstOn'] = 'F';
                        $wpdb->delete($wpdb->prefix . "nxs_query", array( 'postid' => $row['postid'] ));
                        nxs_LogIt('INF', 'Reposter Finished: End of the line', 'RP', '', 'Reposter ID:'.$row['postid'].' ', '-=[ '.print_r($rpstrOpts, true).' ]=-');
                        nxs_Filters::save_meta($row['postid'], 'nxs_rpstr', $rpstrOpts);
                        break;
                    }
                    //## Loop it - restart from the beginning
                    if ($rpstrOpts['rpstStop']=='R') {
                        $wpdb->query("DELETE FROM ". $wpdb->postmeta ." WHERE meta_key = 'snap_isRpstd".$row['postid']."'");  //delete_post_meta($row['postid'], 'nxs_rpstr_stats');
                       nxs_LogIt('INF', 'Reposter Finished: Restarting...', 'RP', '', 'Reposter ID:'.$row['postid'].' ', '-=[ '.print_r($rpstrOpts, true).' ]=-');
                    }
                    //## Wait for new posts (Do nothing)
                    if ($rpstrOpts['rpstStop']=='W') {
                    }
                }
                //## Figure our NextPost Date/Time
                if ($rpstrOpts['rpstOn'] =='1' && (empty($rpstrOpts['rpstTimes']) || $rpstrOpts['rpstTimes']=='A')) {
                    $rpstrOpts['rpstNxTime'] = nxs_getNextPostTime($rpstrOpts['rpstDays'], $rpstrOpts['rpstHrs'], $rpstrOpts['rpstMins'], $rpstrOpts['rpstRndMins']);
                } else {
                    global $nxs_cTime;
                    $nxs_cTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
                    nxs_LogIt('I', 'Reposter Check', 'Reposter ID:'.$row['postid'].' (CNT: '.$pCnt.')', '', '-=[ '.date('Y-m-d H:i:s', $nxs_cTime).' ]=-');
                    if (isset($rpstrOpts['rpstCustTD']) && is_array($rpstrOpts['rpstCustTD'])) {
                        $rpstrOpts['rpstCustTD'] = array_filter($rpstrOpts['rpstCustTD'], create_function('$value', 'global $nxs_cTime; return !empty($value) && strtotime($value)>$nxs_cTime;'));
                        sort($rpstrOpts['rpstCustTD']);
                    } else {
                        $rpstrOpts['rpstCustTD'] = array();
                    }
                    if (!empty($rpstrOpts['rpstCustTD'][0])) {
                        $rpstrOpts['rpstNxTime'] = strtotime($rpstrOpts['rpstCustTD'][0]);
                    } else {
                        $rpstrOpts['rpstNxTime'] = '';
                        $rpstrOpts['rpstOn'] = 0;
                    }
                }
                nxs_Filters::save_meta($row['postid'], 'nxs_rpstr', $rpstrOpts);
                
                $whItem = array('id'=>$id);
                if (!empty($rpstrOpts['rpstNxTime'])) {
                    $dbItem = array('timetorun'=> date_i18n('Y-m-d H:i:s', $rpstrOpts['rpstNxTime']));
                    $wpdb->update($wpdb->prefix . "nxs_query", $dbItem, $whItem);
                } else {
                    $wpdb->delete($wpdb->prefix . "nxs_query", $whItem);
                }
            }
          break;
          case 'F': //## Post from Quick Post From
            $postUser = $row['uid']; $isItUserWhoCan = (!user_can($postUser, 'manage_options') && user_can($postUser, 'haveown_snap_accss')); //if ($isItUserWhoCan) $uid = $postUser;
            if ($isItUserWhoCan) {
                $nxs_SNAP = new nxs_SNAP($postUser);
                $networks = $nxs_SNAP->nxs_acctsU;
                global $nxs_uid;
                $nxs_uid = $postUser;
            } else {
                $networks = $nxs_SNAP->nxs_accts;
            }
            $post  = unserialize($row['extInfo']);  $arrOut = nxs_postFromForm($post, $networks, true); $wpdb->delete($wpdb->prefix . "nxs_query", array('id'=>$id));
          break;
        }
            }
            if (!empty($arrOut)) {
                nxs_addToLogN('L', 'Cron Run ('.$doing_wp_cron.')', '', 'Cron:'. is_array($arrOut)?print_r($arrOut, true):$arrOut);
            }
        }
        //## If more then 20 tasks - lets continue.
        if ((int)$quPostsCnt>$options['numOfTasks']) {
            // update_option('nxs_contCron',$quPostsCnt); //## Finish that. Disabled, 2017-12-05. Bad Idea? Genertes duplicates if a lot of shedulled posts.
        } else {
            update_option('nxs_contCron', false);
        }
    }
}

//## Hook for Manual NXS Cron - ?nxs-cronrun=1
if (!function_exists("nxs_cron_manual")) {
    function nxs_cron_manual()
    {
        if (isset($_GET['nxs-cronrun'])) {
            nxs_checkQuery(); /* nxs_recountQueryTimes(); */ die();
        }
    }
} add_action('wp_loaded', 'nxs_cron_manual');
