<?php

if (!class_exists('nxs_snapClassNT')) {
    class nxs_snapClassNT
    {
        public $nt = array();
        public $ntInfo = array();
        public $noFuncMsg = '';
        public $isMobile = false;
        public $isRepost = false;
        public $hideUnchecked = true;

        public function __construct()
        {
            $this->isMobile = nxs_isMobile();
        }
        public function checkIfSetupFinished($options)
        {
            return 1;
        }
        public function checkIfFunc()
        {
            return true;
        }
        public function doAuth()
        {
        }
        public function showAuthTop()
        {
            global $nxs_snapSetPgURL; ?>
        <div class="nxs_authPopup"><div class="nxs_authPopupHdr">Auth Info. This is normal technical authorization info that will dissapear unless you get some errors.<div class="nxs_authPopupClose"><a href="#" onclick="window.location = '<?php echo $nxs_snapSetPgURL; ?>'">Close</a></div></div><div class="nxs_authPopupIn">
        <?php echo '-= This is normal technical authorization info that will dissapear. (Unless you get some errors. If you do get errors please check them at the <a target="_blank" href="https://www.nextscripts.com/support-faq/">FAQ Page</a>) =- <br/><br/>';
        }
        public function toLatestVerNTGen($ntOpts)
        {
            $out = array();
            $out['rpstBtwDays'] = !empty($ntOpts['rpstBtwDays'])?$ntOpts['rpstBtwDays']:'';
            $out['rpstRndMins'] = !empty($ntOpts['rpstRndMins'])?$ntOpts['rpstRndMins']:'';
            $out['rpstPostIncl'] = !empty($ntOpts['rpstPostIncl'])?$ntOpts['rpstPostIncl']:'';
            $out['rpstType'] = !empty($ntOpts['rpstType'])?$ntOpts['rpstType']:'';
            $out['rpstTimeType'] = !empty($ntOpts['rpstTimeType'])?$ntOpts['rpstTimeType']:'';
            $out['rpstFromTime'] = !empty($ntOpts['rpstFromTime'])?$ntOpts['rpstFromTime']:'';
            $out['rpstToTime'] = !empty($ntOpts['rpstToTime'])?$ntOpts['rpstToTime']:'';
            $out['rpstOLDays'] = !empty($ntOpts['rpstOLDays'])?$ntOpts['rpstOLDays']:'';
            $out['rpstNWDays'] = !empty($ntOpts['rpstNWDays'])?$ntOpts['rpstNWDays']:'';
            $out['nxsCPTSeld'] = !empty($ntOpts['nxsCPTSeld'])?$ntOpts['nxsCPTSeld']:'';
            $out['tagsSel'] = !empty($ntOpts['tagsSel'])?$ntOpts['tagsSel']:'';
            $out['rpstBtwHrsT'] = !empty($ntOpts['rpstBtwHrsT'])?$ntOpts['rpstBtwHrsT']:'';
            $out['tagsSelX'] = !empty($ntOpts['tagsSelX'])?$ntOpts['tagsSelX']:'';
            $out['rpstBtwHrsType'] = !empty($ntOpts['rpstBtwHrsType'])?$ntOpts['rpstBtwHrsType']:'';
            $out['rpstBtwHrsF'] = !empty($ntOpts['rpstBtwHrsF'])?$ntOpts['rpstBtwHrsF']:'';
            $out['nDays'] = !empty($ntOpts['nDays'])?$ntOpts['nDays']:'';
            $out['nHrs'] = !empty($ntOpts['nHrs'])?$ntOpts['nHrs']:'';
            $out['proxy'] = !empty($ntOpts['proxy'])?$ntOpts['proxy']:'';
            $out['fltrs'] = !empty($ntOpts['fltrs'])?$ntOpts['fltrs']:'';
            $out['fltrsOn'] = !empty($ntOpts['fltrsOn'])?$ntOpts['fltrsOn']:'';
            $out['nMin'] = !empty($ntOpts['nMin'])?$ntOpts['nMin']:'';
            $out['qTLng'] = !empty($ntOpts['qTLng'])?$ntOpts['qTLng']:'';
            if (!empty($ntOpts['wpImgSize'])) {
                $out['wpImgSize'] = $ntOpts['wpImgSize'];
            }
            $out['v'] = NXS_SETV;
            return $out;
        }

        public function showNTGroup()
        {
            $cbo = count($this->nt);
            $this->doAuth(); ?> <div class="nxs_box" onmouseover="jQuery('.addMore<?php echo $this->ntInfo['code']; ?>').show();" onmouseout="jQuery('.addMore<?php echo $this->ntInfo['code']; ?>').hide();">
        <div class="nxs_box_header">
          <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$this->ntInfo['lcode']; ?>16.png);"><?php echo $this->ntInfo['name']; ?>
            <?php if ($cbo>1) {
                ?><div class="nsBigText"><?php echo '(<span id="nxsNumOfAcc_'.$this->ntInfo['lcode'].'">'.$cbo."</span> ";
                _e('accounts', 'social-networks-auto-poster-facebook-twitter-g');
                echo ")"; ?></div><?php
            } ?>
            <span style="display: none;" class="addMore<?php echo $this->ntInfo['code']; ?>">&nbsp;&nbsp;&nbsp;<a data-nt="<?php echo $this->ntInfo['code'].(1+max(array_keys($this->nt))); ?>" style="font-size: 12px; text-decoration: none;" href="#" class="nxs_snapAddNew">[<?php  _e('Add New', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php if (!$this->isMobile) {
                echo $this->ntInfo['name']; ?> <?php  _e('account', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php
            } ?> ]</a></span>
          </div>
        </div>
        <div class="nxs_box_inside"><?php $jj = 0;
            if (!$this->checkIfFunc()) {
                echo $this->noFuncMsg;
            } else {
                uasort($this->nt, 'nxsLstSort');
                foreach ($this->nt as $indx=>$pbo) {
                    $jj++;
                    if (!function_exists('ns_SMASV41') && $jj>1) {
                        break;
                    }
                    $pbo['jj']=$jj;
                    $pbo['cbo']=$cbo;
                    if ($indx!=='') {
                        $this->showNTLine($indx, $pbo);
                    }
                }
            }
            if ($jj>7) {
                ?> <div style="padding-left:5px;padding-top:5px;"><a href="#" onclick="jQuery('.showMore<?php echo $this->ntInfo['code']; ?>').show(); jQuery(this).parent().hide(); return false;">Show <?php echo $jj; ?> More[<?php echo($cbo-5); ?>]</a></div>  <?php
            } ?>        
        </div>
      </div> <?php
        }
        public function showNTLine($indx, $pbo)
        {
            if (!isset($pbo['aName'])) {
                $pbo['aName'] = '';
            }
            if (!isset($pbo['do']) && isset($pbo['do'.$this->ntInfo['code']])) {
                $pbo['do'] = $pbo['do'.$this->ntInfo['code']];
            }
            $jj = $pbo['jj'];
            $cbo = $pbo['cbo'];
            if (empty($pbo['nName'])) {
                $pbo['nName'] = $this->makeUName($pbo, $indx);
            }
            if (empty($pbo[$this->ntInfo['lcode'].'OK'])) {
                $pbo[$this->ntInfo['lcode'].'OK'] = $this->checkIfSetupFinished($pbo);
            } ?>
      <div id="dom<?php echo $this->ntInfo['code'].$indx; ?>Div" style="padding-bottom: 3px;<?php echo ($cbo>7 && $jj>5)?'display:none;" class="nxs_ntGroupWrapper showMore'.$this->ntInfo['code'].'"':'"  class="nxs_ntGroupWrapper"'; ?>  onmouseover="jQuery('.showInlineMenu<?php echo $this->ntInfo['code'].$indx; ?>').show();jQuery(this).addClass('nxsHiLightBorder');" onmouseout="jQuery('.showInlineMenu<?php echo $this->ntInfo['code'].$indx; ?>').hide();jQuery(this).removeClass('nxsHiLightBorder');"">
        <div style="margin:0px;margin-left:5px;"> <img id="<?php echo $this->ntInfo['code'].$indx; ?>LoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          <?php if ((int)$pbo['do'] > 0 && ((isset($pbo['fltrsOn']) && (int)$pbo['fltrsOn'] == 1))) {
                $fltInfo = nxsAnalyzePostFilters($pbo['fltrs']); ?> 
            <input type="radio" id="rbtn<?php echo $this->ntInfo['lcode'].$indx; ?>" value="2" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $indx; ?>][do]" checked="checked" class="nxs_acctcb" data-fltinfo="<?php echo $fltInfo; ?>" /> 
          <?php
            } else {
                ?>            
            <input value="0" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $indx; ?>][do]" type="hidden" />             
            <input value="1" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $indx; ?>][do]" type="checkbox" data-nxsid="<?php echo $this->ntInfo['lcode'].'-'.$indx; ?>" class="nxs_acctcb" <?php if ((int)$pbo['do'] > 0) {
                    echo "checked";
                } ?> />             
          <?php
            } ?>              
            <?php if (!$this->isMobile) {
                ?>
            <strong><?php _e('Auto-publish to', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php echo $this->ntInfo['name']; ?> <i style="color: #005800;"><?php if ($pbo['nName']!='') {
                    echo "(".$pbo['nName'].")";
                } ?></i></strong>
            <?php
            } else {
                ?>
            <strong><b style="color: #005800;"><?php echo (!empty($pbo['nName']))?$pbo['nName']:($this->ntInfo['name']." #".$indx); ?></b></strong>
            <?php
            } ?>            
            &nbsp;&nbsp;<?php if ($this->ntInfo['tstReq'] && empty($pbo[$this->ntInfo['lcode'].'OK'])) {
                ?><b style="color: #800000"><?php  _e('Attention required. Unfinished setup', 'social-networks-auto-poster-facebook-twitter-g'); ?> ==&gt;</b><?php
            } ?>              
            <span style="padding-left: 0px; display: none;" class="showInlineMenu<?php echo $this->ntInfo['code'].$indx; ?>"> <?php echo $this->isMobile?'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;':''; ?>
            <a id="do<?php echo $this->ntInfo['code'].$indx; ?>AG" href="#" onclick="doGetHideNTBlock('<?php echo $this->ntInfo['code']; ?>' , '<?php echo $indx; ?>');return false;">[<?php  _e('Show Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;          
            <a href="#" onclick="doDelAcct('<?php echo $this->ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) {
                echo $pbo['nName'];
            } ?>');return false;">[<?php  _e('Remove', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;          
            <?php global $nxs_SNAP;
            if ($nxs_SNAP->sMode['l']!='F') {
                ?>
              <a href="#" onclick="doDuplAcct('<?php echo $this->ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) {
                    echo $pbo['nName'];
                } ?>');return false;">[<?php  _e('Duplicate', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>
            <?php
            } ?></span>
        </div><div id="nxsNTSetDiv<?php echo $this->ntInfo['code'].$indx; ?>"></div> 
      </div><?php
        }

        public function showNoAPIMsg($ii, $options)
        {
            ?> <div id="do<?php echo $this->ntInfo['code'].$ii; ?>Div" class="insOneDiv<?php echo " clNewNTSets"; ?>"><div style="border: 2px solid darkred; padding: 25px 15px 15px 15px; margin: 3px; background-color: #fffaf0;"> 
            <span style="font-size: 16px; color:darkred;line-height: 24px;"><?php global $nxs_apiLInfo;
            if ($this->ntInfo['code']=='IG' && $nxs_apiLInfo['noIG']==true) {
                echo $this->noFuncMsg2;
            } else {
                echo $this->noFuncMsg;
            } ?></span><br/><a href="https://www.nextscripts.com/faq/third-party-libraries-autopost-google-pinterest/" target="_blank">More info about third party libraries.</a><br/><hr/> <div style="font-size: 16px; color:#005800; font-weight: bold; margin-top: 12px; margin-bottom: 7px;">You can get this API library from NextScripts.</div>
            <div style="padding-bottom: 5px;"><a href="https://www.nextscripts.com/snap-api/">SNAP Premium API libraries package</a> adds autoposting to:</div> <span class="nxs_txtIcon nxs_ti_fb">Facebook</span>, <span class="nxs_txtIcon nxs_ti_gp">Google+</span>, <span class="nxs_txtIcon nxs_ti_pn">Pinterest</span>, <span class="nxs_txtIcon nxs_ti_ig">Instagram</span>, <span class="nxs_txtIcon nxs_ti_rd">Reddit</span>, &nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_yt">YouTube</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_fp">Flipboard</span>, <span class="nxs_txtIcon nxs_ti_li">LinkedIn Groups</span><br><br>          
            <div style="padding-bottom: 10px; padding-top: 7px;" align="center">            
<b style="color: #008000">[Limited Time Only Offer]</b><br/> Get SNAP PRO Plugin for <b>Free</b> with the order of SNAP Premium API for WordPress</div>
            <div align="center"><a target="_blank" href="https://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts/#getit" class="NXSButton" id="nxs_snapUPG">Get SNAP Pro Plugin with SNAP API</a></div>
            <div style="font-size: 10px; margin-top: 20px;">*If you already have API, please follow instructions from the readme.txt file.</div>
          </div> </div> <?php
        }


        public function showGNewNTSettings($ii, $options)
        {
            ?><div id="dom<?php echo $this->ntInfo['code'].$ii; ?>Div"> <?php if (!$this->checkIfFunc()) {
                $this->showNoAPIMsg($ii, $options);
            } else {
                $this->showNTSettings($ii, $options, true);
            } ?></div> <?php
        }
        public function showNewNTSettings($mgpo)
        {
        }
        public function makeUName($options, $ii)
        {
            return $this->ntInfo['name']." #".$ii;
        }
        public function showNTSettings($ii, $options, $isNew=false)
        {
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code'];
            $isFin = $this->checkIfSetupFinished($options); ?> 
      <div id="do<?php echo $this->ntInfo['code'].$ii; ?>Div" class="insOneDiv<?php if ($isNew) {
                echo " clNewNTSets";
            } ?>">   <input type="hidden" name="apDoS<?php echo $this->ntInfo['code'].$ii; ?>" value="0" id="apDoS<?php echo $this->ntInfo['code'].$ii; ?>" />
        <?php if ($isNew) {
                ?>    <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do]" value="1" id="apDoNew<?php echo $this->ntInfo['code'].$ii; ?>" /> <?php
            } ?>
        <div class="nsx_iconedTitle" style="float: right; max-width: 392px; text-align: right; background-image: url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$nt; ?>16.png);"><a style="font-size: <?php echo !$isFin?'13':'12'; ?>px;" target="_blank"  href="<?php echo $this->ntInfo['instrURL']; ?>"><?php  printf(__('Detailed %s Configuration Instructions', 'social-networks-auto-poster-facebook-twitter-g'), $this->ntInfo['name']); ?></a>
        </div><?php if (!$isFin) {
                ?><div style="text-align: end;" ><img src="<?php echo NXS_PLURL; ?>img/arrow_r_green_c1.png" /></div><?php
            } ?>
        <?php if (empty($options['nName'])) {
                $options['nName'] = $this->makeUName($options, $ii);
            } ?>
        <div style="width:100%;"><strong><?php _e('Account Nickname', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> <i><?php _e('Just so you can easily identify it', 'social-networks-auto-poster-facebook-twitter-g'); ?></i> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][nName]" id="<?php echo $nt; ?>nName<?php echo $ii; ?>" style="font-weight: bold; color: #005800; border: 1px solid #ACACAC;" class="nxAccEdElem" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['nName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>        
        <ul class="nsx_tabs">
          <li><a href="#nsx<?php echo $nt.$ii ?>_tab1"><?php _e('Account Info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>    
          <?php if (!$isNew) {
                ?>  <li><a id="nsx<?php echo $nt.$ii ?>_tabAdv" href="#nsx<?php echo $nt.$ii ?>_tab2"><?php _e('Advanced', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>  <?php
            } ?>        
        </ul>
        <div class="nsx_tab_container"><?php /* ######################## Account Tab ####################### */ ?>
          <div id="nsx<?php echo $nt.$ii ?>_tab1" class="nsx_tab_content" style="background-image: url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$nt; ?>-bg.png); background-repeat: no-repeat;  background-position:90% 10%;">
            <?php $this->accTab($ii, $options, $isNew); ?>
            <?php if ($isNew) {
                ?> <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do]" value="1" /> <?php
            } ?>
            <?php if ($isFin) {
                ?>  <b><?php _e('Test your settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b>&nbsp;&nbsp;&nbsp; <a href="#" class="NXSButton" onclick="testPost('<?php echo $this->ntInfo['code']; ?>', '<?php echo $ii; ?>'); return false;"><?php printf(__('Submit Test Post to %s', 'social-networks-auto-poster-facebook-twitter-g'), $this->ntInfo['name']); ?></a><?php
            } ?>
          </div> 
          <?php /* ######################## Advanced Tab ####################### */ ?>
          <?php if (!$isNew && function_exists('_make_url_clickable_cb')) {
                ?>   
            <div id="nsx<?php echo $nt.$ii ?>_tab2" class="nsx_tab_content"> 
               <?php $this->showNTPosTypes($nt, $ii, $options);
                $this->showNTFilters($nt, $ii, $options);
                $this->showImgSizeChoice($nt, $ii, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
                $this->addPostingDelaySelV4($nt, $ii, $options);
                $this->advTab($ii, $options); ?>
            </div> <?php
            } ?> <?php /* #### End of Tab #### */ ?>
        </div><br/> <?php /* #### End of Tabs #### */ ?>
        
        
        <div class="submitX nxclear" style="padding-bottom: 0px;">
          <input type="button" id="svBtn<?php echo $nt.$ii ?>" onclick="nxs_svSetAdv('<?php echo $nt; ?>', '<?php echo $ii; ?>', '<?php echo $isNew?'dom'.$ntU.$ii.'Div':'nxsAllAccntsDiv'; ?>','nxs<?php echo $ntU; ?>MsgDiv<?php echo $ii; ?>','<?php echo $isNew?'r':''; ?>','1'); return false;" class="button-primary" value="<?php echo $isNew?__('Add Account', 'nxs_snap'):__('Update Account Info', 'nxs_snap'); ?>" />            
      
          <div id="nxsSaveLoadingImg<?php echo $nt.$ii; ?>" class="doneMsg">Saving.....</div> <div id="doneMsg<?php echo $nt.$ii; ?>" class="doneMsg">Done</div>
          <?php if ($isNew) {
                ?><input style="float: right;" type="button" onclick="jQuery.pgwModal('close');" class="button-primary" value="<?php _e('Close', 'social-networks-auto-poster-facebook-twitter-g') ?>" /><?php
            } ?>
          <?php global $nxs_apiLInfo;
            if (isset($nxs_apiLInfo) && !empty($nxs_apiLInfo) && !empty($this->ntInfo['l']) && !empty($nxs_apiLInfo[$this->ntInfo['l']])) {
                ?>
            <div style="float: right; display: block; clear: both; font-size: 10px; position: relative; bottom: -10px;">NextScripts <?php echo $this->ntInfo['name'].' '.$nxs_apiLInfo[$this->ntInfo['l']]; ?></div>
          <?php
            } ?>
        </div>    
                    
      </div><?php
        }
        //## Advanced Blocks
        public function showProxies($nt, $ii, $options)
        {
            if (empty($options['proxy'])) {
                $options['proxy'] = array('proxy'=>'','up'=>'');
            } ?> <div class="nxs_tls_cpt"><?php  _e('Proxy', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;<span class="nxsInstrSpan"><a href="https://www.nextscripts.com/snap-features/proxy" target="_blank"><?php _e('[Instructions]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></span></div><h3 style="padding-left: 15px; font-size: 16px;"> 
     <input type="checkbox" onchange="if (jQuery(this).is(':checked')) jQuery('#nxs_proxy<?php echo $nt.$ii; ?>').show(); else jQuery('#nxs_proxy<?php echo $nt.$ii; ?>').hide();" class="nxs_acctcb" <?php if (!empty($options['proxyOn'])) {
                echo "checked";
            } ?>  name="<?php echo $nt; ?>[<?php echo $ii; ?>][proxyOn]" value="1" /> 
     <?php  _e('Use Proxy', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><div id="nxs_proxy<?php echo $nt.$ii; ?>" style="margin-left: 30px;<?php if (empty($options['proxyOn'])) {
                echo "display:none;";
            } ?>"> 
    
   <div style="width:100%;"><strong><?php _e('IP:Port', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][proxy]" style="width: 30%;" value="<?php echo htmlentities($options['proxy']['proxy'], ENT_COMPAT, "UTF-8"); ?>"/>
   <div style="width:100%;"><strong><?php _e('Username:Password', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][proxyup]" style="width: 30%;" value="<?php echo htmlentities($options['proxy']['up'], ENT_COMPAT, "UTF-8"); ?>"/>
      <br/><hr/>
     
      </div> <?php
        }
        public function askForSURL($nt, $ii, $options)
        {
            if (!isset($options['useSURL'])) {
                global $nxs_SNAP;
                if (!isset($nxs_SNAP)) {
                    return;
                }
                $gOptions = $nxs_SNAP->nxs_options;
                if (!empty($gOptions['forceSURL'])) {
                    $options['useSURL']= 1;
                } else {
                    $options['useSURL'] = 0;
                }
            } ?> <div class="nxs_tls_cpt"><?php  _e('Shorten URL', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
      <?php  _e('Use Shortened URL for link shares', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
      <h3 style="padding-left: 15px; font-size: 16px;"><input type="checkbox" class="nxs_acctcb" <?php if (!empty($options['useSURL'])) {
                echo "checked";
            } ?>  name="<?php echo $nt; ?>[<?php echo $ii; ?>][useSURL]" value="1" /> 
      <?php  _e('Shorten URL', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><?php
        }
        //## Filters
    public function showNTPosTypes($nt, $ii, $options)
    {
        return; //## Maybe show this instead of part of Filters
      $builtin_types = get_post_types(array( 'public' => true, '_builtin' => true ));
        $custom_types = get_post_types(array( 'public' => true, '_builtin' => false ));
        $posts_types = array_merge($builtin_types, $custom_types);
        $posts_types[] = 'nxs_qp';
        natsort($posts_types); ?><div class="nxs_tls_cpt"><?php  _e('Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;<span class="nxsInstrSpan"><a href="https://www.nextscripts.com/snap-features/filters" target="_blank"><?php _e('[Instructions]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></span></div> <div style="padding-left: 15px;font-size: 16px;"><?php
      //prr($posts_types); //prr($options);

      foreach ($posts_types as $pt) {
          $isFOn = !empty($options['fltrs']) && is_array($options['fltrs']) && !empty($options['fltrs']['nxs_post_type']) && in_array($pt, $options['fltrs']['nxs_post_type']); ?>
         <input value="1" name="<?php echo $nt ?>[<?php echo $ii; ?>][pts][<?php echo $pt; ?>]" type="checkbox" class="nxs_acctcb" <?php if ($isFOn) {
              echo "checked";
          } ?> /><?php echo $pt; ?><br/>          
         
      <?php
      } ?></div> <?php
    }
        public function showNTFilters($nt, $ii, $options)
        {
            if (empty($options['fltrs'])) {
                $options['fltrs'] = array();
            }
            $isFOn = !empty($options['fltrsOn']) && (int)$options['fltrsOn'] == 1; ?> 
      <div class="nxs_tls_cpt"><?php  _e('Filters', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;<span class="nxsInstrSpan"><a href="https://www.nextscripts.com/snap-features/filters" target="_blank"><?php _e('[Instructions]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></span></div> <h3 style="padding-left: 15px;font-size: 16px;"> 
      <input value="1" name="<?php echo $nt ?>[<?php echo $ii; ?>][fltrsOn]" type="checkbox" onchange="if (jQuery(this).is(':checked')) jQuery('#nxs_flrts<?php echo $nt.$ii; ?>').show(); else jQuery('#nxs_flrts<?php echo $nt.$ii; ?>').hide();" class="nxs_acctcb" <?php if ($isFOn) {
                echo "checked";
            } ?> /> 
      <?php  _e('Filter Posts (Only posts that meet the following criteria will be autoposted)', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><div id="nxs_flrts<?php echo $nt.$ii; ?>" style="margin-left: 30px;<?php if (!$isFOn) {
                echo "display:none;";
            } ?>"> 
      <?php nxs_Filters::print_posts_metabox(0, $nt, $ii, $options['fltrs']); ?> </div> <?php
        }
        public function showImgSizeChoice($nt, $ii, $currSel='full')
        {
            ?><div class="nxs_tls_cpt"><?php  _e('Image size', 'social-networks-auto-poster-facebook-twitter-g'); ?> </div>
     <div id="nxs_flrts<?php echo $nt.$ii; ?>" style="margin-left: 16px;"> <?php  _e('What image size should be used if several sizes are avalible', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
     <?php global $_wp_additional_image_sizes;
            $sizes = array();
            foreach (get_intermediate_image_sizes() as $_size) {
                if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                    $sizes[ $_size ]['w']  = get_option("{$_size}_size_w");
                    $sizes[ $_size ]['h'] = get_option("{$_size}_size_h");
                    $sizes[ $_size ]['crop']   = (bool) get_option("{$_size}_crop");
                } elseif (isset($_wp_additional_image_sizes[ $_size ])) {
                    $sizes[ $_size ] = array('w'  => $_wp_additional_image_sizes[ $_size ]['width'],'h' => $_wp_additional_image_sizes[ $_size ]['height'],'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],);
                }
            }
            $pgs = '<select name="'.$nt.'['.$ii.'][wpImgSize]"><option class="nxsBlue" '.($currSel=='full'?'selected="selected"':'').' value="full">Full (Originally Uploaded)</option>';
            foreach ($sizes as $szk=>$isz) {
                $pgs .= '<option class="nxsBlue" '.($currSel==$szk?'selected="selected"':'').' value="'.$szk.'">'.$szk.' ('.$isz['w'].'x'.$isz['h'].')</option>';
            }
            $pgs .='</select>';
            echo $pgs; ?>   
     </div> <?php
        }

        //## Dealy
        public function addPostingDelaySelV4($nt, $ii, $opts)
        {
            if (function_exists('nxs_v4doSMAS4')) {
                ?> <div class="nxs_tls_cpt"><?php _e('Posting Delay', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>  
      <div class="nxs_tls_bd"><?php $nxsHTDP = nxs_getFromGlobalOpt('nxsHTDP');
                if ($nxsHTDP=='I') {
                    _e('Not Compatible with "Publish Immediately"');
                } else {
                    echo nxs_v4doSMAS4($nt, $ii, $opts);
                } ?></div>      
      <?php
            } else {
                echo '<br/>';
            }
        }
        //## Elements
        public function elemUserPass($ii, $u, $p, $t='', $onchange='')
        {
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code']; ?>
      <div style="width:100%;"><strong><?php echo $this->ntInfo['name']; ?>&nbsp;<?php _e('Login', 'social-networks-auto-poster-facebook-twitter-g');
            if ($t=='e') {
                echo " ";
                _e('Email', 'social-networks-auto-poster-facebook-twitter-g');
            } ?>:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][uName]" id="ap<?php echo $ntU; ?>UName<?php echo $ii; ?>" class="nxAccEdElem" value="<?php echo htmlentities($u, ENT_COMPAT, "UTF-8"); ?>"  onchange="if (jQuery(this).val()!='' && jQuery('#ap<?php echo $ntU; ?>Pass<?php echo $ii; ?>').val()!=''){jQuery('#<?php echo $nt.$ii; ?>getPgs').val(1);nxs_svSetAdv('<?php echo $nt; ?>', '<?php echo $ii; ?>','nxsAllAccntsDiv','<?php echo $nt.$ii; ?>pgsList','','');} return false;"/>
      <div style="width:100%;"><strong><?php echo $this->ntInfo['name']; ?>&nbsp;<?php _e('Password', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input autocomplete="false" readonly onfocus="this.removeAttribute('readonly');"  name="<?php echo $nt; ?>[<?php echo $ii; ?>][uPass]" id="ap<?php echo $ntU; ?>Pass<?php echo $ii; ?>" type="password" class="nxAccEdElem" value="<?php echo htmlentities((substr($p, 0, 5)=='n5g9a'||substr($p, 0, 5)=='g9c1a'||substr($p, 0, 5)=='b4d7s')?nsx_doDecode(substr($p, 5)):$p, ENT_COMPAT, "UTF-8"); ?>" <?php echo !empty($onchange)?'onchange="'.$onchange.'"':''; ?> /><br/><?php
        }
        public function elemKeySecret($ii, $lKey, $lSec, $key, $sec, $fnKey='appKey', $fnSec='appSec', $aurl='')
        {
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code'];
            $aurl = !empty($aurl)?' (<a style="font-size: 12px;" href="'.$aurl.'" target="_blank">'.$aurl.'</a>)':'';
            if (!empty($key)) {
                $key = (substr($key, 0, 5)=='x5g9a')?nsx_doDecode(substr($key, 5)):$key;
            }
            if (!empty($sec)) {
                $sec = (substr($sec, 0, 5)=='d3h0a')?nsx_doDecode(substr($sec, 5)):$sec;
            } ?>
     <div style="width:100%;"><b id="<?php echo $nt.$fnKey.$ii; ?>l" style="font-size: 14px;"><?php echo $lKey;
            echo $aurl; ?> </b></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fnKey; ?>]" id="<?php echo $nt.$fnKey.$ii; ?>" class="nxAccEdElem" value="<?php echo htmlentities($key, ENT_COMPAT, "UTF-8"); ?>" /> 
     <div style="width:100%;"><b id="<?php echo $nt.$fnSec.$ii; ?>l" style="font-size: 14px;"><?php echo $lSec; ?>:</b></div><input type="password" autocomplete="false" readonly onfocus="this.removeAttribute('readonly');"  name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fnSec; ?>]" id="<?php echo $nt.$fnSec.$ii; ?>" class="nxAccEdElem" value="<?php echo htmlentities($sec, ENT_COMPAT, "UTF-8"); ?>" /><?php
        }
        public function elemURL($ii, $fn, $val, $lbl, $subLbl)
        {
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code']; ?>
      <div style="width:100%;"><strong><?php echo $lbl; ?>:</strong><i><?php  echo $subLbl; ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fn; ?>]" class="nxAccEdElem" value="<?php echo htmlentities($val, ENT_COMPAT, "UTF-8"); ?>" /><br/><?php
        }
        public function elemMsgFormat($ii, $l, $fn, $val, $isVisible=true)
        {
            $nt = $this->ntInfo['lcode']; ?>
      <div class="nxsMsgFormatDiv" style="display:<?php echo ($isVisible)?"block":"none"; ?>;"> 
        <div style="width:100%;"><b style="font-size: 15px;"><?php echo $l; ?>:</b> (<a href="#" id="msgFrmt<?php echo $nt.$ii; ?>HintInfo" onclick="nxs_showHideFrmtInfo('<?php echo $fn.$nt.$ii; ?>'); return false;"><?php _e('Show format info', 'nxs_snap'); ?></a>)</div>
        <textarea cols="150" rows="3" id="nxsF<?php echo $fn.$nt.$ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fn; ?>]" class="nxAccEdElem" onfocus="jQuery('#nxsF<?php echo $fn.$nt.$ii; ?>').attr('rows', 6); nxs_showFrmtInfo('<?php echo $fn.$nt.$ii; ?>');"><?php echo htmlentities($val, ENT_COMPAT, "UTF-8"); ?></textarea> <?php nxs_doShowHint($fn.$nt.$ii); ?><br/>
      </div><?php
        }
        public function elemTitleFormat($ii, $l, $fn, $val, $isVisible=true)
        {
            $nt = $this->ntInfo['lcode']; ?> 
      <div class="nxsMsgTFormatDiv" style="display:<?php echo ($isVisible)?"block":"none"; ?>;">
        <div style="width:100%;"><b style="font-size: 15px;"><?php echo $l; ?>:</b> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fn; ?>]" id="<?php echo $nt.$ii; ?>SNAPformatT" class="nxAccEdElem" value="<?php echo htmlentities($val, ENT_COMPAT, "UTF-8"); ?>" onfocus="nxs_showFrmtInfo('msgFrmtT<?php echo $nt.$ii; ?>');" /><?php nxs_doShowHint("msgFrmtT".$nt.$ii); ?>
      </div><?php
        }

        //## Edit post Elements
        public function elemEdTitleFormat($ii, $l, $msgTFormat)
        {
            $nt = $this->ntInfo['lcode']; ?>      
      <div class="nxsPostEd_ElemWrap"><div  class="nxsPostEd_ElemLabel"><?php echo $l; ?></div>
        <div class="nxsPostEd_Elem">
          <input name="<?php echo $nt; ?>[<?php echo $ii; ?>][msgTFormat]" id="<?php echo $nt.$ii; ?>SNAPformatT" class="nxsEdElem" data-ii="<?php echo $ii; ?>" data-nt="<?php echo $nt; ?>" style="width: 95%;max-width: 610px;" value="<?php echo htmlentities($msgTFormat, ENT_COMPAT, "UTF-8"); ?>" onfocus="nxs_showFrmtInfo('msgFrmtT<?php echo $nt.$ii; ?>');" /><?php nxs_doShowHint("msgFrmtT".$nt.$ii); ?><?php nxs_doShowHint("msgFrmtT".$nt.$ii); ?>
        </div>
      </div><?php
        }
        public function elemEdMsgFormat($ii, $l, $msgFormat)
        {
            $nt = $this->ntInfo['lcode']; ?>
      <div class="nxsPostEd_ElemWrap"><div  class="nxsPostEd_ElemLabel"><?php echo $l; ?></div>
        <div class="nxsPostEd_Elem">
          <textarea cols="150" rows="2" id="<?php echo $nt.$ii; ?>msgFormat" name="<?php echo $nt; ?>[<?php echo $ii; ?>][msgFormat]" class="nxsEdElem" data-ii="<?php echo $ii; ?>" data-nt="<?php echo $nt; ?>" style="width:95%;max-width: 610px;" onfocus="jQuery('#<?php echo $nt.$ii; ?>msgFormat').attr('rows', 4); jQuery('.nxs_FRMTHint').hide();nxs_showFrmtInfo('msgFormat<?php echo $nt.$ii; ?>');"><?php echo $msgFormat; ?></textarea> <?php nxs_doShowHint("msgFormat".$nt.$ii); ?>
        </div>
      </div><?php
        }

        public function saveCommonNTSettings($pval, $o)
        {
            if (isset($pval['do'])) {
                $o['do'] = $pval['do'];
            } else {
                $o['do'] = 0;
            }
            if (isset($pval['nName'])) {
                $o['nName'] = trim($pval['nName']);
            }
            if (isset($pval['qTLng'])) {
                $o['qTLng'] = trim($pval['qTLng']);
            }
            if (isset($pval['delayDays'])) {
                $o['nDays'] = trim($pval['delayDays']);
            }
            if (isset($pval['delayHrs'])) {
                $o['nHrs'] = trim($pval['delayHrs']);
            }
            if (isset($pval['delayMin'])) {
                $o['nMin'] = trim($pval['delayMin']);
            }
            //## Common Items (Possible)
            if (isset($pval['uName'])) {
                $o['uName'] = trim($pval['uName']);
            } elseif (isset($o['uName'])) {
                unset($o['uName']);
            }
            if (!empty($pval['uPass'])) {
                $o['uPass'] = 'g9c1a'.nsx_doEncode($pval['uPass']);
            } elseif (isset($o['uPass'])) {
                unset($o['uPass']);
            }
            if (isset($pval['msgFormat'])) {
                $o['msgFormat'] = trim($pval['msgFormat']);
            } elseif (isset($o['msgFormat'])) {
                unset($o['msgFormat']);
            }
            if (isset($pval['msgTFormat'])) {
                $o['msgTFormat'] = trim($pval['msgTFormat']);
            } elseif (isset($o['msgTFormat'])) {
                unset($o['msgTFormat']);
            }
            if (isset($pval['msgAFormat'])) {
                $o['msgAFormat'] = trim($pval['msgAFormat']);
            } elseif (isset($o['msgAFormat'])) {
                unset($o['msgAFormat']);
            }
            if (isset($pval['msgATFormat'])) {
                $o['msgATFormat'] = trim($pval['msgATFormat']);
            } elseif (isset($o['msgATFormat'])) {
                unset($o['msgATFormat']);
            }
            if (isset($pval['appKey'])) {
                $o['appKey'] = 'x5g9a'.nsx_doEncode(trim($pval['appKey']));
            } elseif (isset($o['appKey'])) {
                unset($o['appKey']);
            }
            if (isset($pval['appSec'])) {
                $o['appSec'] = 'd3h0a'.nsx_doEncode(trim($pval['appSec']));
            } elseif (isset($o['appSec'])) {
                unset($o['appSec']);
            }
            if (isset($pval['postType'])) {
                $o['postType'] = $pval['postType'];
            }
            if (isset($pval['apiKey'])) {
                $o['apiKey'] = trim($pval['apiKey']);
            } elseif (isset($o['apiKey'])) {
                unset($o['apiKey']);
            }
            if (isset($pval['inclTags'])) {
                $o['inclTags'] = trim($pval['inclTags']);
            } elseif (isset($o['inclTags'])) {
                $o['inclTags'] = 0;
            }
            if (isset($pval['inclCats'])) {
                $o['inclCats'] = trim($pval['inclCats']);
            } elseif (isset($o['inclCats'])) {
                $o['inclCats'] = 0;
            }
            if (isset($pval['session'])) {
                $o['session'] = trim($pval['session']);
            }
            if (isset($pval['cuid'])) {
                $o['cuid'] = trim($pval['cuid']);
            }
            if (isset($pval['apiToUse'])) {
                $o['apiToUse'] = trim($pval['apiToUse']);
            }
            //## Filters
            if (isset($pval['fltrsOn'])) {
                $o['fltrsOn'] = trim($pval['fltrsOn']);
            } else {
                $o['fltrsOn'] = 0;
            }
            if (isset($pval['fltrAfter'])) {
                $o['fltrAfter'] = trim($pval['fltrAfter']);
            } elseif (isset($o['fltrAfter'])) {
                unset($o['fltrAfter']);
            }
            $o['fltrs'] = array();
            //## Proxy
      if (isset($pval['proxyOn'])) {
          $o['proxyOn'] = trim($pval['proxyOn']);
      } else {
          $o['proxyOn'] = 0;
      }  //prr($o);
      if (isset($pval['proxy'])) {
          $o['proxy']['proxy'] = trim($pval['proxy']);
      }
            if (isset($pval['proxyup'])) {
                $o['proxy']['up'] = trim($pval['proxyup']);
            }
            //## Image Selection
            if (isset($pval['wpImgSize'])) {
                $o['wpImgSize'] = trim($pval['wpImgSize']);
            }
            //## Use SURL
            if (isset($pval['useSURL'])) {
                $o['useSURL'] = trim($pval['useSURL']);
            } else {
                $o['useSURL'] = 0;
            }
            //##
            if (!empty($pval['nxs_ie_tags_names'])) {
                $o['fltrs']['nxs_ie_tags_names'] = $pval['nxs_ie_tags_names'];
            }
            if (isset($pval['nxs_tags_names'])) {
                foreach ($pval['nxs_tags_names'] as $jj=>$tag) {
                    $exT='';
                    if (is_numeric($tag)) {
                        $exT = term_exists((int)$tag, 'post_tag');
                    } else {
                        $exT = term_exists($tag, 'post_tag');
                    }
                    if (empty($exT)) {
                        $exT = wp_insert_term($tag, 'post_tag');
                    }
                    $pval['nxs_tags_names'][$jj]= $exT['term_id'];
                }
                $o['fltrs']['nxs_tags_names'] = $pval['nxs_tags_names'];
            }
            if (!empty($pval['nxs_ie_cats_names'])) {
                $o['fltrs']['nxs_ie_cats_names'] = $pval['nxs_ie_cats_names'];
            }
            if (isset($pval['nxs_cats_names'])) {
                foreach ($pval['nxs_cats_names'] as $jj=>$tag) {
                    $exT='';
                    if (is_numeric($tag)) {
                        $exT = term_exists((int)$tag, 'category');
                    } else {
                        $exT = term_exists($tag, 'category');
                    }
                    if (empty($exT)) {
                        $exT = wp_insert_term($tag, 'category');
                    }
                    $pval['nxs_cats_names'][$jj]= $exT['term_id'];
                }
                $o['fltrs']['nxs_cats_names'] = $pval['nxs_cats_names'];
            }
            if (isset($pval['nxs_post_status'])) {
                $o['fltrs']['nxs_post_status'] = $pval['nxs_post_status'];
            }
            if (!empty($pval['nxs_ie_posttypes'])) {
                $o['fltrs']['nxs_ie_posttypes'] = $pval['nxs_ie_posttypes'];
            }
            if (isset($pval['nxs_post_type'])) {
                $o['fltrs']['nxs_post_type'] = $pval['nxs_post_type'];
            }
            if (isset($pval['nxs_post_formats'])) {
                $o['fltrs']['nxs_post_formats'] = $pval['nxs_post_formats'];
            }
            if (isset($pval['nxs_user_names'])) {
                $o['fltrs']['nxs_user_names'] = $pval['nxs_user_names'];
            }
            if (isset($pval['nxs_langs'])) {
                $o['fltrs']['nxs_langs'] = $pval['nxs_langs'];
            }

            if (isset($pval['pts']) && is_array($pval['pts'])) {
                $o['fltrs']['nxs_post_type'] = array_keys($pval['pts']);
            };

            if (isset($pval['nxs_post_ids'])) {
                $o['fltrs']['nxs_post_ids'] = $pval['nxs_post_ids'];
            }
            if (!empty($pval['nxs_search_keywords'])) {
                $o['fltrs']['nxs_search_keywords'] = $pval['nxs_search_keywords'];
            }

            //## Meta
            if (!empty($pval['nxs_count_meta_compares'])) {
                $o['fltrs']['nxs_count_meta_compares'] = $pval['nxs_count_meta_compares'];
            }
            if (!empty($pval['nxs_meta_key'])) {
                $o['fltrs']['post_meta'][0]['operator'] = (isset($pval['nxs_meta_operator']))?$pval['nxs_meta_operator']:'';
                $o['fltrs']['post_meta'][0]['key'] = (isset($pval['nxs_meta_key']))?$pval['nxs_meta_key']:'';
                $o['fltrs']['post_meta'][0]['value'] = (isset($pval['nxs_meta_value']))?$pval['nxs_meta_value']:'';
                $o['fltrs']['post_meta'][0]['relation'] = (isset($pval['nxs_meta_relation']))?$pval['nxs_meta_relation']:'';
            }
            $jjj = 0;
            if (!empty($pval['nxs_count_meta_compares']) && (int)$pval['nxs_count_meta_compares']>1) {
                for ($jj = 2; $jj <= $pval['nxs_count_meta_compares']; $jj++) {
                    if (!empty($pval['nxs_meta_key_'.$jj])) {
                        $jjj++;
                        $o['fltrs']['post_meta'][$jjj]['operator'] = (isset($pval['nxs_meta_operator_'.$jj]))?$pval['nxs_meta_operator_'.$jj]:'';
                        $o['fltrs']['post_meta'][$jjj]['key'] = (isset($pval['nxs_meta_key_'.$jj]))?$pval['nxs_meta_key_'.$jj]:'';
                        $o['fltrs']['post_meta'][$jjj]['value'] = (isset($pval['nxs_meta_value_'.$jj]))?$pval['nxs_meta_value_'.$jj]:'';
                        $o['fltrs']['post_meta'][$jjj]['relation'] = (isset($pval['nxs_meta_relation_'.$jj]))?$pval['nxs_meta_relation_'.$jj]:'';
                    }
                }
            }

            //## Taxonomies
            if (!empty($pval['nxs_count_term_compares'])) {
                $o['fltrs']['nxs_count_term_compares'] = $pval['nxs_count_term_compares'];
            }
            if (!empty($pval['nxs_term_names'])) {
                $o['fltrs']['nxs_tax_names'] = (isset($pval['nxs_tax_names']))?$pval['nxs_tax_names']:'';
                //## Check/insert missing terms
                if (!empty($pval['nxs_term_names']) && !empty($pval['nxs_tax_names']) && is_array($pval['nxs_term_names'])) {
                    $outT = array();
                    foreach ($pval['nxs_term_names'] as $g) {
                        $term = get_term($g, $pval['nxs_tax_names']);
                        if (!is_object($term)) {
                            $t = wp_insert_term($g, $pval['nxs_tax_names']);
                            $outT[] = $t['term_id'];
                        } else {
                            $outT[] = $g;
                        }
                    }
                    $pval['nxs_term_names'] = $outT;
                }
                $o['fltrs']['nxs_term_names'] = (isset($pval['nxs_term_names']))?$pval['nxs_term_names']:'';
                $o['fltrs']['nxs_term_operator'] = (isset($pval['nxs_term_operator']))?$pval['nxs_term_operator']:'';
                $o['fltrs']['nxs_term_children'] = (isset($pval['nxs_term_children']))?$pval['nxs_term_children']:'';
                $o['fltrs']['nxs_term_relation'] = (isset($pval['nxs_term_relation']))?$pval['nxs_term_relation']:'';
            }
            if (!empty($pval['nxs_count_term_compares']) && (int)$pval['nxs_count_term_compares']>1) {
                for ($jj = 2; $jj <= $pval['nxs_count_term_compares']; $jj++) {
                    if (!empty($pval['nxs_term_names_'.$jj])) {
                        $o['fltrs']['nxs_tax_names_'.$jj] = (isset($pval['nxs_tax_names_'.$jj]))?$pval['nxs_tax_names_'.$jj]:'';
                        if (!empty($pval['nxs_term_names_' .$jj]) && !empty($pval['nxs_tax_names_' .$jj]) && is_array($pval['nxs_term_names_' .$jj])) {
                            $outT = array();//  prr($pval['nxs_term_names_' .$jj]);
                            foreach ($pval['nxs_term_names_' .$jj] as $g) {
                                $term = get_term($g, $pval['nxs_tax_names_' .$jj]);
                                if (!is_object($term)) {
                                    $t = wp_insert_term($g, $pval['nxs_tax_names_' .$jj]);
                                    $outT[] = $t['term_id'];
                                } else {
                                    $outT[] = $g;
                                }
                            }
                            $pval['nxs_term_names_' .$jj] = $outT;
                        }
                        $o['fltrs']['nxs_term_names_'.$jj] = (isset($pval['nxs_term_names_'.$jj]))?$pval['nxs_term_names_'.$jj]:'';
                        $o['fltrs']['nxs_term_operator_'.$jj] = (isset($pval['nxs_term_operator_'.$jj]))?$pval['nxs_term_operator_'.$jj]:'';
                        $o['fltrs']['nxs_term_children_'.$jj] = (isset($pval['nxs_term_children_'.$jj]))?$pval['nxs_term_children_'.$jj]:'';
                        $o['fltrs']['nxs_term_relation_'.$jj] = (isset($pval['nxs_term_relation_'.$jj]))?$pval['nxs_term_relation_'.$jj]:'';
                    }
                }
            }
            $o['v'] = NXS_SETV;
            return $o;
        }

        public function saveNTSettings()
        {
        }

        public function showEditPostNTSettings()
        {
        }

        public function showEditNTLine($ii, $pbo, $post)
        {
            if (!$this->checkIfFunc()) {
                return;
            }// prr($pbo);
            if (!isset($pbo['aName'])) {
                $pbo['aName'] = '';
            }
            if (!isset($pbo['do']) && isset($pbo['do'.$this->ntInfo['code']])) {
                $pbo['do'] = $pbo['do'.$this->ntInfo['code']];
            }
            $jj = $pbo['jj'];
            $cbo = $pbo['cbo'];
            if (empty($pbo['nName'])) {
                $pbo['nName'] = $this->makeUName($pbo, $ii);
            }
            $ntU = $this->ntInfo['code'];
            $nt = $this->ntInfo['lcode'];
            $ntName = $this->ntInfo['name'];
            $pMeta = maybe_unserialize(get_post_meta($post->ID, 'snap'.$this->ntInfo['code'], true));
            if (is_array($pMeta) && !empty($pMeta[$ii])) {
                $pbo = $this->adjMetaOpt($pbo, $pMeta[$ii]);
            }
            $pbo['ii'] = $ii; ?>
      <div id="dom<?php echo $this->ntInfo['code'].$ii; ?>Div" style=" padding-bottom: 3px;<?php echo ($pbo['hideMe'] || ($cbo>7 && $jj>5))?'display:none;':''; ?>" class="nxs_ntGroupWrapper<?php echo ($cbo>7 && $jj>5)?' showMore'.$this->ntInfo['code']:''; ?>"  onmouseover="if(!jQuery('#nxsNTSetDiv<?php echo $this->ntInfo['code'].$ii; ?>').is(':visible')) jQuery('.showInlineMenu<?php echo $this->ntInfo['code'].$ii; ?>').show();jQuery(this).addClass('nxsHiLightBorder');" onmouseout="jQuery('.showInlineMenu<?php echo $this->ntInfo['code'].$ii; ?>').hide();jQuery(this).removeClass('nxsHiLightBorder');">
        <div <?php if (!$this->isMobile) {
                ?>class="nxsEdWrapper"<?php
            } ?> style=" margin:0px;padding-left:0px; position: relative;"> <img id="<?php echo $this->ntInfo['code'].$ii; ?>LoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
        <?php
            //###################### LINE HEADER
        ?>            
        <div class="nxsEdNTLineHeader" data-nt="<?php echo $this->ntInfo['lcode']; ?>" data-ii="<?php echo $ii; ?>" style="background-color: #F0F0F0; padding-top: 4px; padding-right: 10px; padding: 5px; margin-top: 3px; border: 1px solid #E0E0E0; ">
        
        <input value="0" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $ii; ?>][do]" type="hidden" />          <?php // prr($post); var_dump($pbo['do']);?>  
        
          <?php if (($pbo['do']=='2') || ($post->post_status=='auto-draft' && !empty($pbo['do']) && !empty($pbo['fltrsOn']))) {
            $fltInfo = nxsAnalyzePostFilters($pbo['fltrs']); ?> 
            <input type="radio" id="rbtn<?php echo $this->ntInfo['lcode'].$ii; ?>" value="2" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $ii; ?>][do]" checked="checked" class="nxs_acctcb" data-fltinfo="<?php echo $fltInfo; ?>" /> 
          <?php
        } else {
            ?>            
            
            <input value="1" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $ii; ?>][do]" type="checkbox" class="nxs_acctcb nxs_acctcb<?php echo $this->ntInfo['lcode']; ?>" <?php if ((int)$pbo['do'] > 0) {
                echo "checked";
            } ?> />             
          <?php
        } ?>              
            <?php if (!$this->isMobile) {
            ?>
            <strong class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$this->ntInfo['lcode']; ?>16.png);background-size:14px 14px;font-size: 13px;"><?php /*  _e('Auto-publish to', 'social-networks-auto-poster-facebook-twitter-g'); */ ?> <?php echo $this->ntInfo['name']; ?> <span style="color: #005800;"><?php if ($pbo['nName']!='') {
                echo "(".$pbo['nName'].")";
            } ?></span></strong>
            <?php
        } else {
            ?>
            <strong class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$this->ntInfo['lcode']; ?>16.png);font-size: 13px;"><b style="color: #005800;"><?php echo (!empty($pbo['nName']))?$pbo['nName']:($this->ntInfo['name']." #".$ii); ?></b></strong>
            <?php
        } ?>                        
            
            
            <?php if (!$this->isMobile) {
            ?>            
              <span style="padding-left: 0px; display: none;" class="showInlineMenu<?php echo $this->ntInfo['code'].$ii; ?>"> <?php echo $this->isMobile?'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;':''; ?>
                <a id="do<?php echo $this->ntInfo['code'].$ii; ?>AG" href="#" class="nxsEdNTLineShowSettings" data-nt="<?php echo $this->ntInfo['lcode']; ?>" data-ii="<?php echo $ii; ?>"><?php echo !empty($pbo['do'])?'[Hide Settings]':'[Show Settings]'; ?></a>
              </span>
            <?php
        } ?>            
            
            <?php if ($this->isMobile) {
            ?> <br/><div style="clear: both;">&nbsp;</div> <?php
        } ?>
            
        </div>            
        <?php
            //###################### END of LINE HEADER
        ?>            
        <?php if (!$this->isMobile || 1==1) {
            $pHst = maybe_unserialize(get_post_meta($post->ID, '_snapPHST', true)); /* prr($pHst);  */ ?>
            <div style="position: absolute; <?php if ($this->isMobile) {
                ?> top:20px; <?php
            } else {
                ?> top:0; <?php
            } ?> right:0; padding-top: 4px; padding-right: 10px;">              
              <?php  if (is_array($pMeta) && isset($pMeta[$ii]) && is_array($pMeta[$ii]) && !empty($pMeta[$ii]['pgID'])) {
                ?>
                <span style="padding-top: 4px; padding-right: 10px;"><a class="showListOfPostsXX" <?php if (!empty($pHst)&& !empty($pHst[$nt][$ii])) {
                    ?> data-qid="#XpstdDiv<?php echo $ntU; ?><?php echo $ii; ?>" <?php
                } ?> id="pstd<?php echo $ntU; ?><?php echo $ii; ?>" style="font-size: 11px;" href="<?php echo (!empty($pMeta[$ii]['postURL']))?$pMeta[$ii]['postURL']:'#'; ?>" target="_blank"><?php
                  if (!$this->isMobile) {
                      printf(__('Posted on', 'nxs_snap'), $ntName); ?>  <?php echo (isset($pMeta[$ii]['pDate']) && $pMeta[$ii]['pDate']!='')?(nxs_adjTime($pMeta[$ii]['pDate'])):"";
                  } else {
                      _e('[Post Link]', 'social-networks-auto-poster-facebook-twitter-g');
                  } ?></a>
                </span>
                 <div style="display: none" id="XpstdDiv<?php echo $ntU; ?><?php echo $ii; ?>"> <?php
                 if (!empty($pHst)&& !empty($pHst[$nt][$ii])) {
                     foreach ($pHst[$nt][$ii] as $jj=>$lnk) {
                         if ($jj>=30) {
                             break;
                         } ?>
                     <span style="font-size: 10px;"><a id="pstd<?php echo $ntU.$ii.'-'.$jj; ?>" href="<?php echo $lnk['l']; ?>" target="_blank"><?php printf(__('Posted on', 'nxs_snap'), $ntName); ?>  <?php echo (isset($lnk['d']) && $lnk['d']!='')?(nxs_adjTime($lnk['d'])):""; ?></a> - [<?php if ($lnk['w']=='a') {
                             _e('Manually', 'social-networks-auto-poster-facebook-twitter-g');
                         } elseif ($lnk['w']=='s') {
                             _e('Autoposted', 'social-networks-auto-poster-facebook-twitter-g');
                         } elseif ($lnk['w']=='r') {
                             _e('Reposter', 'social-networks-auto-poster-facebook-twitter-g');
                         } ?>]</span><br/>
                 <?php
                     }
                 } ?>
          
               </div>  
               <?php
            } ?>
        
          <?php if ($post->post_status == "publish") { /** POST BUTTON **/ ?>        
            <a class="nxsPostNowBtn" data-nt="<?php echo $ntU; ?>" data-ii="<?php echo $ii; ?>" data-ntname="<?php echo $ntName; ?>" data-pid="<?php echo $post->ID; ?>" style="font-size: 14px; font-weight: bold;" href="#" onclick="return false;">[<?php
            _e('Post', 'social-networks-auto-poster-facebook-twitter-g');
                if (!$this->isMobile) {
                    echo "&nbsp;";
                    _e('Now', 'social-networks-auto-poster-facebook-twitter-g');
                } ?>]</a>
        
          <?php
            } ?>              
          </div>
        <?php
        } ?>
        
        </div>
        
         <?php if ($this->isMobile) { //## Mobile Interface?>
        <div style="margin:0px;margin-left:5px; position: relative;"><div style="padding-top: 0px; padding-right: 5px; margin-left: 14px;">
          <span> 
            <a id="do<?php echo $this->ntInfo['code'].$ii; ?>AG" href="#" style="font-size: 11px;" onclick="doGetHideNTBlock('<?php echo $this->ntInfo['code']; ?>' , '<?php echo $ii; ?>');return false;">[<?php  _e('Options', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;                      
            
            <a href="#" style="font-weight: bold;font-size: 11px;" onclick="doDelAcct('<?php echo $this->ntInfo['lcode']; ?>', '<?php echo $ii; ?>', '<?php if (isset($pbo['bgBlogID'])) {
            echo $pbo['nName'];
        } ?>');return false;">[<?php  _e('Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;          
          </span>
             <?php  if (is_array($pMeta) && isset($pMeta[$ii]) && is_array($pMeta[$ii]) && !empty($pMeta[$ii]['pgID'])) {
            ?> <span style="padding-top: 4px; padding-right: 10px;"><a class="showListOfPostsX" data-jXboxbtid="XpstdDiv<?php echo $ntU; ?><?php echo $ii; ?>" id="pstd<?php echo $ntU; ?><?php echo $ii; ?>" style="font-size: 11px;" href="<?php echo $pMeta[$ii]['postURL']; ?>" target="_blank"><?php
             if (!$this->isMobile) {
                 printf(__('Posted on', 'nxs_snap'), $ntName); ?>  <?php echo (isset($pMeta[$ii]['pDate']) && $pMeta[$ii]['pDate']!='')?(nxs_adjTime($pMeta[$ii]['pDate'])):"";
             } else {
                 _e('[Post Link]', 'social-networks-auto-poster-facebook-twitter-g');
             } ?></a></span>
             
              <div style="display: none" id="XpstdDiv<?php echo $ntU; ?><?php echo $ii; ?>">        
          <a id="pstd<?php echo $ntU; ?><?php echo $ii; ?>" style="font-size: 10px;" href="<?php echo $pMeta[$ii]['postURL']; ?>" target="_blank"><?php printf(__('Posted on', 'nxs_snap'), $ntName); ?>  <?php echo (isset($pMeta[$ii]['pDate']) && $pMeta[$ii]['pDate']!='')?(nxs_adjTime($pMeta[$ii]['pDate'])):""; ?></a>
        </div>  
             
             <?php
        } ?>
             </div>
        </div> 
        <?php
        } ?>
        <div id="nxsNTSetDiv<?php echo $this->ntInfo['code'].$ii; ?>" class="nxsEdPostMetaBlockBody" style="border: 1px solid #E0E0E0;display:<?php echo $pbo['do']>'0'?'block':'none'; ?>;">        
          <?php if (method_exists($this, 'showEdPostNTSettingsV4')) {
            $this->showEdPostNTSettingsV4($pbo, $post);
        } ?>        
        
        
        
        <?php //### TIme to Post
       if ($post->post_status != "publish" && function_exists('nxs_v4doSMAS5')) {
           $pbo['postTime'] = get_post_time('U', false, $post->ID);
           nxs_v4doSMAS5X($nt, $ii, $pbo);
       }
            if (((int)$pbo['do'] == 1) && $post->post_status == "publish" && isset($pbo['timeToRun']) && $pbo['timeToRun'] > time()) {
                ?> 
              <div>===&gt;&gt;&gt;&gt;&nbsp;<?php _e('Autopost has been schedulled for', 'social-networks-auto-poster-facebook-twitter-g') ?> <?php echo date('F j, Y, g:i a', $pbo['timeToRun']) ?></div>
           <?php
            } ?>       
            </div>
         </div><?php
        }


        public function nxs_tmpltImportComments($post, $ntOpt, $ii)
        {
            $pMeta = maybe_unserialize(get_post_meta($post->ID, 'snap'.$this->ntInfo['code'], true));
            if ($post->post_status == "publish") {
                ?>           
        <?php if (!empty($ntOpt['riComments']) && $ntOpt['riComments']=='1' && is_array($pMeta) && isset($pMeta[$ii]) && is_array($pMeta[$ii]) && !empty($pMeta[$ii]['pgID'])) {
                    ?>
        <input style="margin: 6px;" onclick="return false;" type="button" data-ii="<?php echo $ii; ?>" data-pid="<?php echo $post->ID; ?>" data-nt="<?php echo $this->ntInfo['lcode']; ?>" class="button riTo_button" value="<?php _e('Import Comments/Replies', 'nxs_snap') ?>" />
        <?php
                }
            }
        }


        public function adjMetaOptG($optMt, $pMeta)
        {
            $optMt['isPosted'] = isset($pMeta['isPosted'])?$pMeta['isPosted']:'';
            if (isset($pMeta['postType'])) {
                $optMt['postType'] = $pMeta['postType'];
            }
            if (isset($pMeta['msgFormat'])) {
                $optMt['msgFormat'] = $pMeta['msgFormat'];
            }
            if (isset($pMeta['msgTFormat'])) {
                $optMt['msgTFormat'] = $pMeta['msgTFormat'];
            }
            if (isset($pMeta['imgToUse'])) {
                $optMt['imgToUse'] = $pMeta['imgToUse'];
            }
            if (isset($pMeta['urlToUse'])) {
                $optMt['urlToUse'] = $pMeta['urlToUse'];
            }
            if (isset($pMeta['postType'])) {
                $optMt['postType'] = $pMeta['postType'];
            }
            if (isset($pMeta['timeToRun'])) {
                $optMt['timeToRun'] = $pMeta['timeToRun'];
            }
            $optMt['do'] = 0;
            if (isset($pMeta['do'])) {
                $optMt['do'] = $pMeta['do'];
            } else {
                if (isset($pMeta['msgFormat'])) {
                    $optMt['do'] = 0;
                }
            }  // What is that?
      if (isset($optMt['do'.$this->ntInfo['code']])) {
          unset($optMt['do'.$this->ntInfo['code']]);
      } //prr($optMt); die();
      return $optMt;
        }

        public function adjMetaOpt($optMt, $pMeta)
        {
            return $this->adjMetaOptG($optMt, $pMeta);
        }

        public function ajaxPost($options)
        {
            check_ajax_referer('nxsSsPageWPN');
            $postID = $_POST['id'];
            $nt = $this->ntInfo['lcode'];
            $ntU = $this->ntInfo['code'];
            $ntName = $this->ntInfo['name'];
            foreach ($options[$nt] as $ii=>$nto) {
                if ($ii==$_POST['nid']) {
                    $nto['ii'] = $ii;
                    $nto['pType'] = 'aj';
                    $po =  get_post_meta($postID, 'snap'.$ntU, true);
                    $po =  maybe_unserialize($po);
                    $clName = 'nxs_snapClass'.$ntU;
                    $ntClInst = new $clName();
                    if (is_array($po) && isset($po[$ii]) && is_array($po[$ii])) {
                        $nto = $ntClInst->adjMetaOpt($nto, $po[$ii]);
                    }
                    $result = $this->publish($postID, $nto);
                    if ($result == '200') {
                        die("Your post has been successfully sent to ".$ntName);
                    } else {
                        die($result);
                    }
                }
            }
        }

        public function publish($postID, $nto)
        {
            $fnName = 'nxs_doPublishTo'.$this->ntInfo['code'];
            return $fnName($postID, $nto);
        }

        public function adjPreFormatWP(&$options, $postID)
        {
        }
        public function adjAfterPost(&$options, &$ret)
        {
        }

        public function metaMarkAsPosted($postID, $ii, $args='')
        {
            $nt = $this->ntInfo['code'];
            $mpo =  get_post_meta($postID, 'snap'.$nt, true);
            $mpo =  maybe_unserialize($mpo);
            //prr($postID); prr('snap'.$nt);  prr($mpo); echo "#####".$postID."|".$nt."|".$ii."|".$args;
            if (!is_array($mpo)) {
                $mpo = array();
            }
            if (!isset($mpo[$ii]) || !is_array($mpo[$ii])) {
                $mpo[$ii] = array();
            }
            if ($args=='' || (is_array($args) && isset($args['isPosted']) && $args['isPosted']=='1')) {
                $mpo[$ii]['isPosted'] = '1';
            }
            if (is_array($args) && isset($args['isPrePosted']) && $args['isPrePosted']==1) {
                $mpo[$ii]['isPrePosted'] = '1';
            }
            if (is_array($args) && isset($args['pgID'])) {
                $mpo[$ii]['pgID'] = $args['pgID'];
            }
            if (is_array($args) && isset($args['postURL'])) {
                $mpo[$ii]['postURL'] = $args['postURL'];
                $w = 's';
                if (!empty($_POST['nxsact']) && $_POST['nxsact']=='manPost') {
                    $w = 'a';
                } elseif ($this->isRepost) {
                    $w = 'r';
                }
                $pHistory =  maybe_unserialize(get_post_meta($postID, '_snapPHST', true));
                if (empty($pHistory)) {
                    $pHistory = array();
                }
                $pHistory[strtolower($nt)][$ii][] = array('l'=> $args['postURL'], 'id'=>$args['pgID'], 'd'=>$args['pDate'], 'w'=>$w);
                delete_post_meta($postID, '_snapPHST');
                add_post_meta($postID, '_snapPHST', $pHistory);
            }
            if (is_array($args) && isset($args['pDate'])) {
                $mpo[$ii]['pDate'] = $args['pDate'];
            }
            /*$mpo = mysql_real_escape_string(serialize($mpo)); */ delete_post_meta($postID, 'snap'.$nt);
            add_post_meta($postID, 'snap'.$nt, str_replace('\\', '\\\\', serialize($mpo)));
        }

        public function publishWP($ii, $postID=0)
        {
            $options = $this->nt[$ii];
            $extInfo ='';
            $addParams = nxs_makeURLParams(array('NTNAME'=>$this->ntInfo['name'], 'NTCODE'=>$this->ntInfo['code'], 'POSTID'=>$postID, 'ACCNAME'=>$options['nName']));
            $blogTitle = htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES);
            if ($blogTitle=='') {
                $blogTitle = home_url();
            }
            if (!isset($options['imgToUse'])) {
                $options['imgToUse'] = '';
            }
            if (!isset($options['imgSize'])) {
                $options['imgSize'] = '';
            }
            if (empty($options['imgSize'])&&!empty($options['wpImgSize'])) {
                $options['imgSize'] = $options['wpImgSize'];
            }
            $logNT = '<span style="color:#FA5069">'.$this->ntInfo['name'].'</span> - '.$options['nName'];
            $snap_ap = get_post_meta($postID, 'snap'.$this->ntInfo['code'], true);
            $snap_ap = maybe_unserialize($snap_ap);
            if (!isset($options['pType'])) {
                $options['pType'] = 'im';
            }
            if ($options['pType']=='sh') {
                sleep(rand(1, 10));
            } //## Sho eto?
      if ($options['pType']!='aj' && is_array($snap_ap) && (nxs_chArrVar($snap_ap[$ii], 'isPosted', '1') || nxs_chArrVar($snap_ap[$ii], 'isPrePosted', '1'))) { //## Check this!!!!
        $snap_isAutoPosted = get_post_meta($postID, 'snap_isAutoPosted', true);
          if ($snap_isAutoPosted!='2') {
              nxs_LogIt('W', 'Notice', $logNT, '', '-=Duplicate=- Post ID:'.$postID, 'Already posted. No reason for posting duplicate |'.$uqID);
              return;
          }
      }
            $isNoImg = false;
            $tagsA = array();
            $tags = '';
            $catsA = array();
            $cats = '';
            global $nxs_urlLen;//## Fix this with defaults
            if ($postID=='0') {
                echo "Testing ... <br/><br/>";
                $urlToGo = home_url();
                $options['msgFormat'] = 'Test Post from '.$blogTitle."\r\n".$urlToGo;
                $options['msgTFormat'] = 'Test Post from '.$blogTitle;
                if (!empty($options['defImg'])) {
                    $imgURL = $options['defImg'];
                } else {
                    $imgURL = "https://direct.gtln.us/img/nxs/df/dfImg".rand(1, 15).".jpg";
                }
            } else {
                $post = get_post($postID);
                if (empty($post)) {
                    nxs_LogIt('E', 'Error', $logNT, '', 'No Post');
                }
                if (!isset($options['defImg'])) {
                    $options['defImg'] = '';
                }
                $nxs_urlLen = 0;
                $this->adjPreFormatWP($options, $postID);
                $options = nxs_getURL($options, $postID, $addParams);
                if (!empty($options['msgFormat'])) {
                    $options['msgFormat'] = nsFormatMessage($options['msgFormat'], $postID, $addParams, '', $options);
                }
                if (!empty($options['msgTFormat'])) {
                    $options['msgTFormat'] = nsFormatMessage($options['msgTFormat'], $postID, $addParams, '', $options);
                }
                //## MyURL - URLToGo code
                $urlToGo = $options['urlToUse'];
                if (is_object($post)) {
                    $urlToGo = apply_filters('nxs_adjust_ex_url', $urlToGo, $post->post_content);
                }
                if (!empty($options['imgToUse'])) {
                    $imgURL = $options['imgToUse'];
                } else {
                    $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full', $options['defImg']);
                }
                if (preg_match("/noImg.\.png/i", $imgURL)) {
                    $imgURL = '';
                    $isNoImg = true;
                }
                if (!empty($options['inclTags']) && $options['inclTags']=='1') {
                    $t = wp_get_post_tags($postID);
                    foreach ($t as $tagA) {
                        $tagsA[] = $tagA->name;
                    }
                    $tags = implode(',', $tagsA);
                }
                if (!empty($options['inclCats']) && $options['inclCats']=='1') {
                    $postCats = wp_get_post_categories($postID);
                    $cats = array();
                    foreach ($postCats as $c) {
                        $cat = get_category($c);
                        $catsA[] = str_ireplace('&', '&amp;', $cat->name);
                        $cats = implode(',', $catsA);
                    }
                }

                if (!empty($options['attchAsVid']) && $options['attchAsVid']=='A') {
                    $vids = nsFindVidsInPost($post);
                    if (count($vids)>0) {
                        if (strlen($vids[0])==11) {
                            $vidURL = 'http://www.youtube.com/watch?v='.$vids[0];
                            $imgVURL = 'http://img.youtube.com/vi/'.$vids[0].'/maxresdefault.jpg';
                        }
                        if (strlen($vids[0])==8) {
                            $vidURL = 'https://secure.vimeo.com/moogaloop.swf?clip_id='.$vids[0].'&autoplay=1';
                            //$mssg['source'] = 'http://player.vimeo.com/video/'.$vids[0];
                            $apiURL = "http://vimeo.com/api/v2/video/".$vids[0].".json?callback=showThumb";
                            $json = nxs_remote_get($apiURL);
                            if (!is_nxs_error($json)) {
                                $json = $json['body'];
                                $json = str_replace('showThumb(', '', $json);
                                $json = str_replace('])', ']', $json);
                                $json = json_decode($json, true);
                                $imgVURL = $json[0]['thumbnail_large'];
                            }
                        }
                    }
                    if (trim($imgVURL)!='') {
                        $imgURL = $imgVURL;
                    }
                }
                $extInfo = ' | PostID: '.$postID." - ".(is_object($post))?$post->post_title:'';
            }
            $message = array('siteName'=>$blogTitle, 'tags'=>$tags, 'tagsA'=>$tagsA, 'cats'=>$cats, 'catsA'=>$catsA, 'url'=>$urlToGo, 'imageURL'=>$imgURL, 'videoURL'=>'', 'urlLength'=>$nxs_urlLen, 'noImg'=>$isNoImg, 'message'=>'', 'urlTitle'=>'', 'urlDescr'=>'');
            //## Post
      //## Adjust Per network
      $this->adjPublishWP($options, $message, $postID);  //prr($options); prr($message); die();
      //## Actual Post
      $clName = 'nxs_class_SNAP_'.$this->ntInfo['code'];
            $ntToPost = new $clName();
            $ret = $ntToPost->doPostToNT($options, $message);
            //## Process Results
      if (!is_array($ret) || empty($ret['isPosted']) || $ret['isPosted']!='1') { //## Error
         if ($postID=='0') {
             prr($ret);
         }
          nxs_LogIt('E', 'Error', $logNT, $this->ntInfo['code'], '-=ERROR=- '.print_r($ret, true), $extInfo);
      } else {  // ## All Good - log it.
          if (!empty($ret['msg'])) {
              nxs_LogIt('I', 'Message', $logNT, $this->ntInfo['code'], print_r($ret['msg'], true), $extInfo);
          }
          if (!empty($_POST['nxsact'])&&($_POST['nxsact']=='manPost' || $_POST['nxsact']=='testPost')) {
              _e('SUCCESS', 'nxs_snap');
              echo '<br/><br/>'.$logNT.' Page.<br/>'.((!empty($ret['postURL']))?' Post link: <a href="'.$ret['postURL'].'" target="_blank">'.$ret['postURL'].'</a><br/><br/>':'').(!empty($ret['msg'])?print_r($ret['msg'], true):'').'<br/>';
          }
          if (!empty($ret['ck'])) {
              nxs_save_glbNtwrks($this->ntInfo['lcode'], $ii, $ret['ck'], 'ck');
          }
          if ($postID=='0') {
              nxs_LogIt('S', 'Test', $logNT, $this->ntInfo['code'], 'OK - TEST Message Posted | <a href="'.$ret['postURL'].'" target="_blank">Post Link</a>');
          } else {
              nxs_addToRI($postID);
              $this->metaMarkAsPosted($postID, $ii, array('isPosted'=>'1', 'pgID'=>$ret['postID'], 'postURL'=>$ret['postURL'], 'pDate'=>date('Y-m-d H:i:s')));
              $extInfo .= ' | <a href="'.$ret['postURL'].'" target="_blank">Post Link</a>';
              nxs_LogIt('S', 'Posted', $logNT, $this->ntInfo['code'], 'OK - Message Posted ', $extInfo);
          }
      }
            $this->adjAfterPost($options, $ret);
            //## Return Result
            if (!empty($ret['isPosted']) && $ret['isPosted']=='1') {
                return 200;
            } else {
                return print_r($ret, true);
            }
        }
    }
}

?>
