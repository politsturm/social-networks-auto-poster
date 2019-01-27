<?php
//## NextScripts Instagram Connection Class
$nxs_snapAPINts[] = array('code'=>'IG', 'lcode'=>'ig', 'name'=>'Instagram');

if (!class_exists("nxs_class_SNAP_IG")) {
    class nxs_class_SNAP_IG
    {
        public $ntCode = 'IG';
        public $ntLCode = 'ig';
    
        public function nxsCptCheck()
        {
            if (function_exists('nxs_getOption')) {
                $opVal = array();
                $opNm = $_POST['svc'];
                $opVal = nxs_getOption($opNm);
                $nt = new nxsAPI_IG();
                if (!empty($opVal['ck'])) {
                    $nt->ck = $opVal['ck'];
                }
                if (!empty($opVal['proxy'])&&!empty($opVal['proxyOn'])) {
                    $nt->proxy['proxy'] = $opVal['proxy']['proxy'];
                    if (!empty($opVal['proxy']['up'])) {
                        $nt->proxy['up'] = $opVal['proxy']['up'];
                    }
                };
                $ck = $nt->checkCode($opVal['url'], $_POST['code']);
                if ($ck!==false) {
                    $opVal['ck'] = $ck;
                    nxs_saveOption($opNm, $opVal);
                    echo '<br/><br/> Your Code has been accepted. You can post to this account now. Reloading the page.....<script type="text/javascript">setTimeout(function(){ window.location = window.location; }, 3000);</script>';
                    die('All OK');
                } else {
                    die('Your code is incorrect');
                }
            }
        }
        public function doPost($options, $message)
        {
            if (!is_array($options)) {
                return false;
            }
            $out = array();
            foreach ($options as $ii=>$ntOpts) {
                $out[$ii] = $this->doPostToNT($ntOpts, $message);
            }
            return $out;
        }
        public function doPostToNT($options, $message)
        {
            $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
            if (!class_exists("nxsAPI_IG")) {
                $badOut['Error'] .= "Instagram API not found";
                return $badOut;
            }
            //## Check settings
            if (!is_array($options)) {
                $badOut['Error'] = 'No Options';
                return $badOut;
            }
            if (empty($options['uPass'])) {
                $badOut['Error'] = 'Not Configured';
                return $badOut;
            }
            //## Format
            if (!empty($message['pText'])) {
                $msg = $message['pText'];
            } else {
                $msg = nxs_doFormatMsg($options['msgFormat'], $message);
            }
            if (isset($message['imageURL'])) {
                $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize']));
            } else {
                $imgURL = '';
            }
            $urlToGo = (!empty($message['url']))?$message['url']:'';
            if (empty($options['imgAct'])) {
                $options['imgAct'] = 'E';
            }
            $msg = nsTrnc(html_entity_decode($msg), 2200);
            $pass = substr($options['uPass'], 0, 5)=='g9c1a'?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];
            //## Get Saved Login Info
            $opNm = 'nxs_snap_ig_'.sha1('nxs_snap_ig'.$options['uName'].$options['uPass']);
            if (function_exists('nxs_getOption')) {
                $opVal = array();
                $opVal = nxs_getOption($opNm);
                if (!empty($opVal) & is_array($opVal)) {
                    $options = array_merge($options, $opVal);
                }
            }
      
            $nt = new nxsAPI_IG();
            $nt->opNm = $opNm;
            if (!empty($options['ck'])) {
                $nt->ck = $options['ck'];
            }
            if (!empty($message['session']) || !empty($options['session'])) {
                $nt->sid = !empty($message['session'])?$message['session']:$options['session'];
            }
            if (!empty($options['proxy'])&&!empty($options['proxyOn'])) {
                $nt->proxy['proxy'] = $options['proxy']['proxy'];
                if (!empty($options['proxy']['up'])) {
                    $nt->proxy['up'] = $options['proxy']['up'];
                }
            };
            $loginErr = $nt->connect($options['uName'], $pass);
            if (!$loginErr) {
                $ret = $nt->post($msg, $imgURL, $options['imgAct']);
            } else {
                if ($loginErr=='cpt') {
                    if (!empty($_POST)&&!empty($_POST['nxsact'])) {
                        echo '<br/><b>Security Checkpoint</b><br/>';
                        echo 'Code:&nbsp;<input type="text" id="nxsIGCP"/><input type="button" value="Submit Code" onclick="nxs_do2StepCodeCheck(\'ig\', \''.$opNm.'\' ,jQuery(\'#nxsIGCP\').val());"/>';
                        die('<br/>Instagram asked you to enter the security code. Please check your email or phone and enter the code.<div style="color: #3897f0;font-weight: bold;" id="nxsCPTResults"></div>');
                    } else {
                        $loginErr = 'Instagram asked you to enter the security code. Please go to the account settings and click "Submit Test Post" to get and enter code.';
                    }
                }
                $badOut['Error'] .= 'Something went wrong - '.print_r($loginErr, true);
                $ret = $badOut;
            }
            //## Save Login Info
            if (function_exists('nxs_saveOption')) {
                if (empty($opVal['ck'])) {
                    $opVal['ck'] = '';
                }
                if (is_array($ret) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) {
                    $opVal['ck'] = $nt->ck;
                    nxs_saveOption($opNm, $opVal);
                }
            }
            return $ret;
        }
    }
}
