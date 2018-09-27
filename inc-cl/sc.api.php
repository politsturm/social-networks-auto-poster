<?php    
//## NextScripts App.net Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
rdUName - Reddit User Name
rdPass - Reddit User Passord
rdSubReddit - Name of the Sub-Reddit
postType - A or T - "Attached link" or "Text"

rdTitleFormat
rdTextFormat

2. Post Info

url
title - [up to 300 characters long] - title of the submission
text

*/
$nxs_snapAPINts[] = array('code'=>'SC', 'lcode'=>'sc', 'name'=>'Scoop.It');

if (!class_exists("nxs_class_SNAP_SC")) { class nxs_class_SNAP_SC {
    
    var $ntCode = 'SC';
    var $ntLCode = 'sc';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }

    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (empty($options['accessToken']) && empty($options['uPass'])) { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format Post
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message);
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';  $postType = $options['postType'];       
      
      
      if (class_exists('nxsAPI_SC') && $options['uName']!='' && $options['uPass']!='') {
        //## Get Saved Login Info
        if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_sc_'.sha1('nxs_snap_sc'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } //  prr($opVal);
        $uname = $options['uName']; $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
        $ck = !empty($options['ck'])?maybe_unserialize($options['ck']):''; if (!empty($ck)) $ck = nxsClnCookies($ck);
        $nt = new nxsAPI_SC(); if (!empty($ck)) $nt->ck = $ck; if (!empty($options['proxy'])&&!empty($options['proxyOn'])){ $nt->proxy['proxy'] = $options['proxy']['proxy']; if (!empty($options['proxy']['up'])) $nt->proxy['up'] = $options['proxy']['up']; }
        $loginErr = $nt->connect($uname, $pass); $nt->t = $options['topicURL'];//        prr($message);
        if ($loginErr) { $badOut['Error'] .= 'Can\'t Connect - '.print_r($loginErr, true); return $badOut; }        
        $opVal['ck'] = $nt->ck; nxs_saveOption($opNm,$opVal); //$options['ck'] = $nt->ck; if (function_exists('nxs_save_glbNtwrks')) nxs_save_glbNtwrks('li', $options['ii'], $nt->ck, 'ck');         
        if (!empty($message['tags']) && !is_array($message['tags'])) $message['tags'] = explode(',', $message['tags']);
        $iURL = (($postType=='I' || $postType=='A') && !empty($imgURL))?$imgURL:''; $pURL = $postType=='A'?$message['url']:''; $html = '';
        $ret = $nt->post($text, $pURL, $iURL, $msgT, $html, $message['tags']); return $ret; 
        
      } else { 
        require_once('apis/scOAuth.php');   $tum_oauth = new wpScoopITOAuth(nxs_gak($options['appKey']), nxs_gas($options['appSec']), $options['accessToken'], $options['accessTokenSec']);
        $tiID = $tum_oauth->makeReq('http://www.scoop.it/api/1/topic', array('urlName'=>$options['topicURL']));  
        if (!empty($tiID) && is_array($tiID) && !empty($tiID['topic']) && !empty($tiID['topic']['id'])) $tiID = $tiID['topic']['id']; else { $badOut['Error'] .= print_r($tiID, true); return $badOut; }
        $postArr = array('action'=>'create', 'title'=>$msgT, 'content'=>$text, 'url'=>$postType=='A'?$message['url']:'', 'imageUrl'=>(($postType=='I' || $postType=='A') && !empty($imgURL))?$imgURL:'', 'topicId'=>$tiID);  
        $postinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/post', $postArr, 'POST'); // prr($postinfo);      
        if (is_array($postinfo) && isset($postinfo['post'])) { $apNewPostID = $postinfo['post']['id']; $apNewPostURL = $postinfo['post']['scoopUrl']; 
          if ($options['inclTags']=='1') { $postArr = array('action'=>'edit', 'tag'=>$message['tags'], 'id'=>$apNewPostID);  
            $postinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/post', $postArr, 'POST'); 
          }              
        } $code = $tum_oauth->http_code;
      }
      
      
      if (!empty($apNewPostID)) {         
         return array('postID'=>$apNewPostID, 'isPosted'=>1, 'postURL'=>$apNewPostURL, 'pDate'=>date('Y-m-d H:i:s'));          
      } else { $badOut['Error'] .= print_r($postinfo, true)." Code:".$tum_oauth->http_code; 
        return $badOut;
      }
      return $badOut;
    }  
    
}}
?>