<?php
//## NextScripts Flipboard Connection Class
$nxs_snapAvNts[] = array('code'=>'FP', 'lcode'=>'fp', 'name'=>'Flipboard', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Autopost to your magazines');

if (!class_exists("nxs_snapClassFP")) {
    class nxs_snapClassFP extends nxs_snapClassNT
    {
        public $ntInfo = array('code'=>'FP', 'lcode'=>'fp', 'name'=>'Flipboard', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'https://www.nextscripts.com/instructions/flipboard-social-networks-auto-poster-setup-installation/');
  
        public $noFuncMsg = 'Sorry, but Flipboard doesn\'t have a built-in API for automated posts yet. <br/>You need a special API library module to be able to publish your content to Flipboard.';
        public function checkIfFunc()
        {
            return class_exists('nxsAPI_FP');
        }
  
        public function toLatestVer($ntOpts)
        {
            if (!empty($ntOpts['v'])) {
                $v = $ntOpts['v'];
            } else {
                $v = 340;
            }
            $ntOptsOut = '';
            switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];
        $ntOptsOut['msgFormat'] = $ntOpts['msgFrmt'];  $ntOptsOut['uName'] = $ntOpts['uName'];  $ntOptsOut['uPass'] = $ntOpts['uPass']; $ntOptsOut['mgzURL'] = $ntOpts['mgzURL'];
      break;
    }
            return !empty($ntOptsOut)?$ntOptsOut:$ntOpts;
        }
   
  
        //#### Show Common Settings
        public function showGenNTSettings($ntOpts)
        {
            $this->nt = $ntOpts;
            $this->showNTGroup();
            return;
        }
        //#### Show NEW Settings Page
        public function showNewNTSettings($ii)
        {
            $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'mgzURL'=>'', 'msgFormat'=>"%EXCERPT%\r\n\r\n%URL%");
            $this->showGNewNTSettings($ii, $defO);
        }
        //#### Show Unit  Settings
        public function checkIfSetupFinished($options)
        {
            return !empty($options['uPass']);
        }
        public function accTab($ii, $options, $isNew=false)
        {
            $ntInfo = $this->ntInfo;
            $nt = $ntInfo['lcode'];
            if (empty($options['session'])) {
                $options['session'] = '';
            }
            if (empty($options['cuid'])) {
                $options['cuid'] = '';
            }
            $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?>
  
    <div style="width:100%;"><strong><?php _e('Access Token', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> 
       <div style="font-size: 11px; margin: 0px;"><?php _e('[Optional] Please use this only if you are having troubles to login/post without it.', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
    </div>    
    <input style="width:400px;" name="<?php echo $nt; ?>[<?php echo $ii; ?>][session]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['session'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
    <br/>
    <div style="width:100%;"><strong><?php _e('User ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> 
       <div style="font-size: 11px; margin: 0px;"><?php _e('[Optional] Please use this only if you are having troubles to login/post without it.', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
    </div>    
    <input style="width:400px;" name="<?php echo $nt; ?>[<?php echo $ii; ?>][cuid]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['cuid'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
    <br/><br/>  
  
  
    <div style="width:100%;"><strong><?php _e('Where to Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('Flipboard Magazine URL', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="fp[<?php echo $ii; ?>][mgzURL]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['mgzURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>    
    <br/><?php $this->elemMsgFormat($ii, 'Comment Text Format', 'msgFormat', $options['msgFormat']);
        }
        public function advTab($ii, $options)
        {
            $this->showProxies($this->ntInfo['lcode'], $ii, $options);
        }
        //#### Set Unit Settings from POST
        public function setNTSettings($post, $options)
        {
            foreach ($post as $ii => $pval) {
                if (!empty($pval['uPass']) && !empty($pval['uPass'])) {
                    if (!isset($options[$ii])) {
                        $options[$ii] = array();
                    }
                    $options[$ii] = $this->saveCommonNTSettings($pval, $options[$ii]);
                    if (isset($pval['mgzURL'])) {
                        $options[$ii]['mgzURL'] = trim($pval['mgzURL']);
                    }
                } elseif (count($pval)==1) {
                    if (isset($pval['do'])) {
                        $options[$ii]['do'] = $pval['do'];
                    } else {
                        $options[$ii]['do'] = 0;
                    }
                }
            }
            return $options;
        }
    
        //#### Show Post->Edit Meta Box Settings
        public function showEdPostNTSettings($ntOpts, $post)
        {
            $post_id = $post->ID;
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code'];
            foreach ($ntOpts as $ii=>$ntOpt) {
                $isFin = $this->checkIfSetupFinished($ntOpt);
                if (!$isFin) {
                    continue;
                }
                $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true));
                if (is_array($pMeta) && !empty($pMeta[$ii])) {
                    $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);
                }
                if (empty($ntOpt['imgToUse'])) {
                    $ntOpt['imgToUse'] = '';
                }
                if (empty($ntOpt['urlToUse'])) {
                    $ntOpt['urlToUse'] = '';
                }
                $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
                $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):'';
                $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
                $imgToUse = $ntOpt['imgToUse'];
                $urlToUse = $ntOpt['urlToUse'];
                $ntOpt['ii']=$ii;
                $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?>
        <?php  $this->elemEdMsgFormat($ii, __('Comment Text Format:', 'social-networks-auto-poster-facebook-twitter-g'), $msgFormat); ?>        
        <?php /* ## Select Image & URL ## */ nxs_showURLToUseDlg($nt, $ii, $urlToUse);
                $this->nxs_tmpltAddPostMetaEnd($ii);
            }
        }
        public function showEdPostNTSettingsV4($ntOpt, $post)
        {
            $post_id = $post->ID;
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code'];
            $ii = $ntOpt['ii']; //prr($ntOpt['postType']);
                                                   
            if (empty($ntOpt['imgToUse'])) {
                $ntOpt['imgToUse'] = '';
            }
            if (empty($ntOpt['urlToUse'])) {
                $ntOpt['urlToUse'] = '';
            }
            $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
            $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):'';
            $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
            $imgToUse = $ntOpt['imgToUse'];
            $urlToUse = $ntOpt['urlToUse'];
            $ntOpt['ii']=$ii;
            $this->elemEdMsgFormat($ii, __('Comment Format:', 'social-networks-auto-poster-facebook-twitter-g'), $msgFormat);
            // ## Select Image & URL ##
            nxs_showURLToUseDlg($nt, $ii, $urlToUse);
        }
        //#### Save Meta Tags to the Post
  public function adjMetaOpt($optMt, $pMeta)
  {
      $optMt = $this->adjMetaOptG($optMt, $pMeta);  //   prr($optMt);
   
    return $optMt;
  }
  
        public function adjPublishWP(&$options, &$message, $postID)
        {
        }
    }
}

if (!function_exists("nxs_doPublishToFP")) {
    function nxs_doPublishToFP($postID, $options)
    {
        if (!is_array($options)) {
            $options = maybe_unserialize(get_post_meta($postID, $options, true));
        }
        $cl = new nxs_snapClassFP();
        $cl->nt[$options['ii']] = $options;
        return $cl->publishWP($options['ii'], $postID);
    }
}
?>