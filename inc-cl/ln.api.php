<?php

//## NextScripts TG Connection Class
$nxs_snapAPINts[] = array('code'=>'LN', 'lcode'=>'ln', 'name'=>'Line');

if (!class_exists("nxs_class_SNAP_LN")) { class nxs_class_SNAP_LN {
    
    var $ntCode = 'LN';
    var $ntLCode = 'ln';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function nxs_getHeaders($ref, $post=false){ $hdrsArr = array(); 
      $hdrsArr['X-Requested-With']='XMLHttpRequest'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
      $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.22 Safari/537.11';
      if($post) $hdrsArr['Content-Type']='application/json'; $hdrsArr['Accept']='application/json, text/javascript, */*; q=0.01'; 
      $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; $hdrsArr['Accept-Language']='en-US,en;q=0.8'; $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; return $hdrsArr;
    }    
    function doPostToNT($options, $message){ global $nxs_gCookiesArr; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; } if (empty($options['botTkn'])) { $badOut['Error'] = 'Not Configured'; return $badOut; }
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); if (empty($options['imgSize'])) $options['imgSize'] = '';
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = ''; 
      $urlToGo = (!empty($message['url']))?$message['url']:'';  if (!isset($options['webPrev'])) $options['webPrev'] = 1; 
      
      if ($options['attchImg']=='1') {         
        $flds = json_encode(array('to' => $options['whToPost'], 'messages' => array(array('type'=>'image', 
        'originalContentUrl'=>'https://res.cloudinary.com/nextscripts/image/fetch/c_scale,w_1024/'.$imgURL, 'previewImageUrl'=>'https://res.cloudinary.com/nextscripts/image/fetch/c_scale,w_240/'.$imgURL))));         
        $url = 'https://api.line.me/v2/bot/message/push'; $hdrsArr = $this->nxs_getHeaders('https://api.line.me', true); $hdrsArr['Authorization']='Bearer '.$options['botTkn'];
        $advSet = nxs_mkRemOptsArr($hdrsArr, '', $flds); $ret = nxs_remote_post( $url, $advSet); if (is_nxs_error($ret)) {  $badOut = print_r($ret, true)." - ERROR"; return $badOut; } //  prr($flds);    prr($ret);
      } 
      $msg = str_ireplace('<strong>','<b>',str_ireplace('</strong>','</b>',str_ireplace('<em>','<i>',str_ireplace('</em>','</i>',$msg)))); $msg = nsTrnc(strip_tags($msg, '<b><i><a><code><pre>'), 3000);       
      $flds = json_encode(array('to' => $options['whToPost'], 'messages' => array(array('type'=>'text', 'text'=>$msg)))); // prr($flds);
      $url = 'https://api.line.me/v2/bot/message/push'; $hdrsArr = $this->nxs_getHeaders('https://api.line.me', true); $hdrsArr['Authorization']='Bearer '.$options['botTkn'];
      $advSet = nxs_mkRemOptsArr($hdrsArr, '', $flds); $ret = nxs_remote_post( $url, $advSet); if (is_nxs_error($ret)) {  $badOut = print_r($ret, true)." - ERROR"; return $badOut; }             
      $contents = $ret['body']; $resp = json_decode($contents, true);    //  prr($resp);
      if (is_array($resp) && empty($resp)) { 
          return array('postID'=>'line.me', 'isPosted'=>1, 'postURL'=>'http://line.me', 'pDate'=>date('Y-m-d H:i:s'));  
      } else $badOut['Error'] .= 'Something went wrong - '.print_r($ret, true).' | '.print_r($flds, true); 
      return $badOut;      
   }    
}}

?>