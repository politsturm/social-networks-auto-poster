<?php   
//## NXS/OWS Common Functions.
if (!function_exists('prr')){ function prr($str,$id='') { echo $id."<pre>"; print_r($str); echo "</pre>\r\n"; }}
if (!function_exists('nsx_stripSlashes')){ function nsx_stripSlashes(&$value){$value = stripslashes($value);}}
if (!function_exists('nsx_fixSlashes')){ function nsx_fixSlashes(&$value){ while (strpos($value, '\\\\')!==false) $value = str_replace('\\\\','\\',$value);
   if (strpos($value, "\\'")!==false) $value = str_replace("\\'","'",$value); if (strpos($value, '\\"')!==false) $value = str_replace('\\"','"',$value);
}}
if (!function_exists('CutFromTo')){ function CutFromTo($string, $from, $to){$fstart = stripos($string, $from); $tmp = substr($string,$fstart+strlen($from)); $flen = stripos($tmp, $to);  return substr($tmp,0, $flen);}}
if (!function_exists('nsx_doEncode')){ function nsx_doEncode($string,$key='NSX') { $key = sha1($key); $strLen = strlen($string);$keyLen = strlen($key); $j = 0; $hash = '';
  for ($i = 0; $i < $strLen; $i++) { $ordStr = ord(substr($string,$i,1)); if ($j == $keyLen) $j = 0; $ordKey = ord(substr($key,$j,1)); $j++; $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));} return $hash;
}}
if (!function_exists('nsx_doDecode')){ function nsx_doDecode($string,$key='NSX') { $key = sha1($key); $keyLen = strlen($key); $hash = ''; $sX = str_split($string, 2560);
  foreach($sX as $ss){$j=0; $sA=str_split($ss, 2); foreach($sA as $oS){$oS=hexdec(base_convert(strrev($oS),36,16)); if ($j==$keyLen) $j=0; $oK=ord(substr($key,$j,1)); $j++; $hash.=chr($oS-$oK);}} return $hash;
}}
if (!function_exists('nxs_decodeEntitiesFull')){ function nxs_decodeEntitiesFull($string, $quotes = ENT_COMPAT, $charset = 'utf-8') {
  return html_entity_decode(preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'nxs_convertEntity', $string), $quotes, $charset); 
}}
if (!function_exists('nxs_substr')){ function nxs_substr($str, $start){ preg_match_all("/./su", $str, $ar);
   if(func_num_args() >= 3) { $end = func_get_arg(2); return join("",array_slice($ar[0],$start,$end)); } else return join("",array_slice($ar[0],$start));
}}
if (!function_exists('nxs_strLen')){ function nxs_strLen($str) { return count(str_split(utf8_decode($str))); }}
if (!function_exists('nxs_convertEntity')){ function nxs_convertEntity($matches, $destroy = true) {
  static $table = array('quot' => '&#34;','amp' => '&#38;','lt' => '&#60;','gt' => '&#62;','apos' => '&#39;','OElig' => '&#338;','oelig' => '&#339;','Scaron' => '&#352;','scaron' => '&#353;','Yuml' => '&#376;','circ' => '&#710;','tilde' => '&#732;','ensp' => '&#8194;','emsp' => '&#8195;','thinsp' => '&#8201;','zwnj' => '&#8204;','zwj' => '&#8205;','lrm' => '&#8206;','rlm' => '&#8207;','ndash' => '&#8211;','mdash' => '&#8212;','lsquo' => '&#8216;','rsquo' => '&#8217;','sbquo' => '&#8218;','ldquo' => '&#8220;','rdquo' => '&#8221;','bdquo' => '&#8222;','dagger' => '&#8224;','Dagger' => '&#8225;','permil' => '&#8240;','lsaquo' => '&#8249;','rsaquo' => '&#8250;','euro' => '&#8364;','fnof' => '&#402;','Alpha' => '&#913;','Beta' => '&#914;','Gamma' => '&#915;','Delta' => '&#916;','Epsilon' => '&#917;','Zeta' => '&#918;','Eta' => '&#919;','Theta' => '&#920;','Iota' => '&#921;','Kappa' => '&#922;','Lambda' => '&#923;','Mu' => '&#924;','Nu' => '&#925;','Xi' => '&#926;','Omicron' => '&#927;','Pi' => '&#928;','Rho' => '&#929;','Sigma' => '&#931;','Tau' => '&#932;','Upsilon' => '&#933;','Phi' => '&#934;','Chi' => '&#935;','Psi' => '&#936;','Omega' => '&#937;','alpha' => '&#945;','beta' => '&#946;','gamma' => '&#947;','delta' => '&#948;','epsilon' => '&#949;','zeta' => '&#950;','eta' => '&#951;','theta' => '&#952;','iota' => '&#953;','kappa' => '&#954;','lambda' => '&#955;','mu' => '&#956;','nu' => '&#957;','xi' => '&#958;','omicron' => '&#959;','pi' => '&#960;','rho' => '&#961;','sigmaf' => '&#962;','sigma' => '&#963;','tau' => '&#964;','upsilon' => '&#965;','phi' => '&#966;','chi' => '&#967;','psi' => '&#968;','omega' => '&#969;','thetasym' => '&#977;','upsih' => '&#978;','piv' => '&#982;','bull' => '&#8226;','hellip' => '&#8230;','prime' => '&#8242;','Prime' => '&#8243;','oline' => '&#8254;','frasl' => '&#8260;','weierp' => '&#8472;','image' => '&#8465;','real' => '&#8476;','trade' => '&#8482;','alefsym' => '&#8501;','larr' => '&#8592;','uarr' => '&#8593;','rarr' => '&#8594;','darr' => '&#8595;','harr' => '&#8596;','crarr' => '&#8629;','lArr' => '&#8656;','uArr' => '&#8657;','rArr' => '&#8658;','dArr' => '&#8659;','hArr' => '&#8660;','forall' => '&#8704;','part' => '&#8706;','exist' => '&#8707;','empty' => '&#8709;','nabla' => '&#8711;','isin' => '&#8712;','notin' => '&#8713;','ni' => '&#8715;','prod' => '&#8719;','sum' => '&#8721;','minus' => '&#8722;','lowast' => '&#8727;','radic' => '&#8730;','prop' => '&#8733;','infin' => '&#8734;','ang' => '&#8736;','and' => '&#8743;','or' => '&#8744;','cap' => '&#8745;','cup' => '&#8746;','int' => '&#8747;','there4' => '&#8756;','sim' => '&#8764;','cong' => '&#8773;','asymp' => '&#8776;','ne' => '&#8800;','equiv' => '&#8801;','le' => '&#8804;','ge' => '&#8805;','sub' => '&#8834;','sup' => '&#8835;','nsub' => '&#8836;','sube' => '&#8838;','supe' => '&#8839;','oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;','sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;','lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;','rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;','clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;','nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;','pound' => '&#163;','curren' => '&#164;','yen' => '&#165;','brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;','copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;','not' => '&#172;','shy' => '&#173;','reg' => '&#174;','macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;','sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;','micro' => '&#181;','para' => '&#182;','middot' => '&#183;','cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;','raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;','frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;','Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;','Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;','Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;','Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;','Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;','ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;','Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;','Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;','Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;','Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;','szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;','acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;','aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;','egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;','euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;','icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;','ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;','ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;','divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;','uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;','yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;');
  if (isset($table[$matches[1]])) return $table[$matches[1]];
  // else 
  return $destroy ? '' : $matches[0];
}}
if (!function_exists('nsTrnc')){ function nsTrnc($string, $limit, $break=" ", $pad=" ...") { if(nxs_strLen($string) <= $limit) return $string; if(nxs_strLen($pad) >= $limit) return ''; $string = nxs_substr($string, 0, $limit-nxs_strLen($pad)); 
  $brLoc = strripos($string, $break);  if ($brLoc===false) return $string.$pad; else return nxs_substr($string, 0, $brLoc).$pad; 
}}
//##=============================
if (!function_exists("NXS_doSetArrRecursive")) { function NXS_doSetArrRecursive(&$array, $path, $value){ $key = array_shift($path); //prr($path); prr($key); echo "|-"; prr($array); echo "-|";
  if (empty($path)) if (trim($key)=='')  $array[] = $value;  else  $array[$key] = $value; else { if (!isset($array[$key]) || !is_array($array[$key])) $array[$key] = array(); NXS_doSetArrRecursive($array[$key], $path, $value); }
}}
if (!function_exists("NXS_parseQueryStr")) { function NXS_parseQueryStr($url){ $tokens = explode("&", $url); $urlVars = array();
  foreach ($tokens as $token) { $token = urldecode($token); $value = NXS_parseEQStr($token, "=", ""); 
    if (preg_match('/^([^\[]*)(\[.*\])$/', $token, $matches)) { if (preg_match_all('/\[([^\]]*)\]/', $matches[2], $matches2)) $gg = $matches2[1]; array_unshift($gg, $matches[1]); NXS_doSetArrRecursive($urlVars, $gg, $value);} 
      else $urlVars[$token] = $value;
  } return $urlVars;
}}
if (!function_exists("NXS_parseEQStr")) { function NXS_parseEQStr(&$a, $delim='.', $default=false){ $n = strpos($a, $delim); if ($n === false) return $default; $result = substr($a, $n+strlen($delim)); $a = substr($a, 0, $n); return $result;}}
if (!function_exists("nxs_chArrVar")) { function nxs_chArrVar($arr, $varN, $varV){ return (isset($arr) && is_array($arr) && isset($arr[$varN]) && $arr[$varN]==$varV); }}

if (!function_exists('nxs_snap_fbpgcmp')){ function nxs_snap_fbpgcmp($a, $b){ return strcmp($a["t"], $b["t"]);}}

//## Cookie functions
if (!function_exists('nxs_getCKVal')){ function nxs_getCKVal($name, $ck) { foreach ($ck as $c) if ($c->name==$name) return($c->value); return false; } }
if (!function_exists('nxsClnCookies')){ function nxsClnCookies($ck) { $ckOut = array(); $t =time(); foreach ($ck as $c) { if ($c->value!='deleted' && $c->value!='deleteMe' && $c->value!='delete me' && $c->value!='"delete me"' && (empty($c->expires) || $c->expires>$t)) $ckOut[] = $c; } return $ckOut; }}
if (!function_exists('nxsLeaveOnlyCookies')){ function nxsLeaveOnlyCookies($ck, $lv) { $ckOut = array(); foreach ($ck as $c) { if (in_array($c->name, $lv)) $ckOut[] = $c; } return $ckOut; }}
if (!function_exists('nxsDelCookie')){ function nxsDelCookie($ck, $dc) { $ckOut = array(); foreach ($ck as $c) if ($c->name!=$dc) $ckOut[] = $c; return $ckOut; }}
if (!function_exists('nxsMergeArraysOV')){function nxsMergeArraysOV($Arr1, $Arr2){ if (empty($Arr1)) $Arr1 = array(); if (empty($Arr2)) $Arr2 = array();
  foreach($Arr2 as $key => $value) { if(array_key_exists($key, $Arr1) && is_array($value)) $Arr1[$key] = nxsMergeArraysOV($Arr1[$key], $Arr2[$key]); else $Arr1[$key] = $value;} return $Arr1;
}}
if (!function_exists('nxs_MergeCookieArr')){function nxs_MergeCookieArr($ArrO, $ArrN){ if (empty($ArrO)) $ArrO = array(); if (empty($ArrN)) $ArrN = array(); $namesArr = array(); foreach($ArrO as $key => $value) { if (is_object($value)) $namesArr[$key] = $value->name; }             
  foreach($ArrN as $key => $value) { if (is_object($value) && $value->value!='deleted') { $isEx = array_search($value->name, $namesArr); if ($isEx===false) $ArrO[] = $value; else $ArrO[$isEx] = $value;}} return $ArrO;
}}

//## Upload image as file from URL to remote server
if (!function_exists('nxs_altCurlUploadImg')){ function nxs_altCurlUploadImg( $ch, $r ){ $pstArray = unserialize($r['headers']['nxsPstArr']); $tmp = $r['headers']['nxsUplFile']; $fld = $r['headers']['nxsPstField'];
    unset($r['headers']['nxsPstArr']); unset($r['headers']['nxsUplFile']); unset($r['headers']['nxsPstField']);    
    if (function_exists('curl_file_create')) $file  = curl_file_create($tmp); else $file = '@'.$tmp;  $pstArray[$fld] = $file; $r['body'] = http_build_query($pstArray);
    if ( !empty( $r['headers'] ) ) { $headers = array(); foreach ( $r['headers'] as $name => $value ) if ($name!=='Content-Length')  $headers[] = "{$name}: $value"; curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );}
    curl_setopt($ch, CURLOPT_POST, TRUE); curl_setopt($ch, CURLOPT_POSTFIELDS, $pstArray); return array('ch'=>$ch, 'r'=>$r);
}}
if (!function_exists('nxs_curlUploadImg')){ function nxs_curlUploadImg($imgURL, $uplURL, $pstArray, $pstField, $ck='') { $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; 
  $imgType = substr(  $remImgURL, strrpos( $remImgURL , '.' )+1 ); if (stripos($imgType,'?')!==false) $imgType = @reset((explode('?', $imgType))); $ia = array("jpg", "png", "gif", "jpeg"); if (!in_array($imgType, $ia)) $imgType = 'jpg'; 
  $hdrsArr = array('User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36', 'Referer'=>$remImgURL); 
  $advSet = nxs_mkRemOptsArr($hdrsArr);  $imgData = nxs_remote_get($remImgURL, $advSet);// prr($remImgURL);  // prr($imgData);
  if(is_nxs_error($imgData) || empty($imgData['body']) || (!empty($imgData['headers']['content-length']) && (int)$imgData['headers']['content-length']<200) || 
    $imgData['headers']['content-type'] == 'text/html' ||  $imgData['response']['code'] == '403' ) return array('err'=>print_r($imgData, true)); else $imgData = $imgData['body'];  
  $tmpX=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile()))); 
  if (!is_writable($tmpX)) { $msg = "Can't upload image. Your temporary folder or file (file - ".$tmpX.") is not writable.";
    if (function_exists('wp_upload_dir')) { $uDir = wp_upload_dir(); $tmpX = tempnam($uDir['path'], "nx"); if (!is_writable($tmpX)) return $msg." Your UPLOADS folder or file (file - ".$tmpX.") is not writable. ";} else return $msg;
  } rename($tmpX, $tmpX.='.'.$imgType); if (version_compare(PHP_VERSION, '7.0.0', '<')) register_shutdown_function(create_function('', "@unlink('{$tmpX}');")); else register_shutdown_function(function ($tmpX) { @unlink($tmpX); }, $tmpX); file_put_contents($tmpX, $imgData);  
  $hdrsArr['Content-type'] = 'multipart/form-data'; $hdrsArr['nxsUplFile'] = $tmpX; $hdrsArr['nxsPstArr'] = serialize($pstArray); $hdrsArr['nxsPstField'] = $pstField;  $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $pstArray); $advSet['postAsArray'] = 1; //prr($advSet);
  $rep = nxs_remote_post($uplURL, $advSet);  @unlink($tmpX); if(is_nxs_error($rep)) return array('err'=>print_r($rep, true)); else return $rep;  
}}
if (!function_exists("nxs_add_array_sort")) {  function nxs_add_array_sort($a, $b){ $c = array('Social Networks'=>1, 'Blogs/Publishing Platforms'=>2, 'Link Sharing/Boormarks'=>3, 'Email Marketing'=>4, 'Messengers'=>5,  'Image Sharing'=>6,  'Forums'=>7, 'Other'=>8);
    return $c[$a]>$c[$b] ? 1: -1;
}}
//## Filters
if (!function_exists('nxs_adjFilters')){ function nxs_adjFilters($pval, $o) { 
      //## Filters
      if (isset($pval['fltrsOn'])) $o['fltrsOn'] = trim($pval['fltrsOn']); else $o['fltrsOn'] = 0;  //prr($o);
      if (isset($pval['fltrAfter'])) $o['fltrAfter'] = trim($pval['fltrAfter']); else if (isset($o['fltrAfter'])) unset($o['fltrAfter']); $o['fltrs'] = array(); 
      //## Image Selection      
      if (isset($pval['wpImgSize']))   $o['wpImgSize'] = trim($pval['wpImgSize']);
      //## Proxy
      if (isset($pval['proxyOn'])) $o['proxyOn'] = trim($pval['proxyOn']); else $o['proxyOn'] = 0;  //prr($o);
      if (isset($pval['proxy']))   $o['proxy']['proxy'] = trim($pval['proxy']); 
      if (isset($pval['proxyup'])) $o['proxy']['up'] = trim($pval['proxyup']);      
      //## Tags
      if (!empty($pval['nxs_ie_tags_names'])) $o['fltrs']['nxs_ie_tags_names'] = $pval['nxs_ie_tags_names'];
      if (isset($pval['nxs_tags_names'])) { foreach ($pval['nxs_tags_names'] as $jj=>$tag) if (empty($tag)) unset($pval['nxs_tags_names'][$jj]); else { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'post_tag'); else $exT = term_exists($tag, 'post_tag'); 
          if (empty($exT)) $exT = wp_insert_term($tag, 'post_tag'); $pval['nxs_tags_names'][$jj]= $exT['term_id'];
        } $o['fltrs']['nxs_tags_names'] = $pval['nxs_tags_names'];
      }      
      //## Cats
      if (!empty($pval['nxs_ie_cats_names'])) $o['fltrs']['nxs_ie_cats_names'] = $pval['nxs_ie_cats_names'];      
      if (isset($pval['nxs_cats_names'])) {  foreach ($pval['nxs_cats_names'] as $jj=>$tag) if (empty($tag)) unset($pval['nxs_cats_names'][$jj]); else { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'category'); else $exT = term_exists($tag, 'category');  
          if (empty($exT)) $exT = wp_insert_term($tag, 'category'); $pval['nxs_cats_names'][$jj]= $exT['term_id'];
        } $o['fltrs']['nxs_cats_names'] = $pval['nxs_cats_names'];
      }     
      //$o = nxs_FltrsV3toV4($o);
      if (isset($pval['nxs_post_status'])) $o['fltrs']['nxs_post_status'] = $pval['nxs_post_status'];
      if (!empty($pval['nxs_ie_posttypes'])) $o['fltrs']['nxs_ie_posttypes'] = $pval['nxs_ie_posttypes'];
      if (isset($pval['nxs_post_type'])) $o['fltrs']['nxs_post_type'] = $pval['nxs_post_type'];
      if (isset($pval['nxs_post_formats'])) $o['fltrs']['nxs_post_formats'] = $pval['nxs_post_formats'];
      if (isset($pval['nxs_user_names'])) $o['fltrs']['nxs_user_names'] = $pval['nxs_user_names'];
      if (isset($pval['nxs_langs'])) $o['fltrs']['nxs_langs'] = $pval['nxs_langs'];      
      if (!empty($pval['nxs_search_keywords'])) $o['fltrs']['nxs_search_keywords'] = $pval['nxs_search_keywords'];
      
      if (!empty($pval['nxs_count_meta_compares'])) $o['fltrs']['nxs_count_meta_compares'] = $pval['nxs_count_meta_compares'];      
      if (!empty($pval['nxs_meta_key'])) {       
        $o['fltrs']['post_meta'][0]['operator'] = (isset($pval['nxs_meta_operator']))?$pval['nxs_meta_operator']:'';
        $o['fltrs']['post_meta'][0]['key'] = (isset($pval['nxs_meta_key']))?$pval['nxs_meta_key']:'';
        $o['fltrs']['post_meta'][0]['value'] = (isset($pval['nxs_meta_value']))?$pval['nxs_meta_value']:'';
        $o['fltrs']['post_meta'][0]['relation'] = (isset($pval['nxs_meta_relation']))?$pval['nxs_meta_relation']:''; 
      } $jjj = 0;//prr($pval['nxs_count_term_compares']);
      if (!empty($pval['nxs_count_meta_compares']) && (int)$pval['nxs_count_meta_compares']>1) for( $jj = 2; $jj <= $pval['nxs_count_meta_compares']; $jj++ ) { if (!empty($pval['nxs_meta_key_'.$jj])) { $jjj++;
          $o['fltrs']['post_meta'][$jjj]['operator'] = (isset($pval['nxs_meta_operator_'.$jj]))?$pval['nxs_meta_operator_'.$jj]:'';
          $o['fltrs']['post_meta'][$jjj]['key'] = (isset($pval['nxs_meta_key_'.$jj]))?$pval['nxs_meta_key_'.$jj]:'';
          $o['fltrs']['post_meta'][$jjj]['value'] = (isset($pval['nxs_meta_value_'.$jj]))?$pval['nxs_meta_value_'.$jj]:'';         
          $o['fltrs']['post_meta'][$jjj]['relation'] = (isset($pval['nxs_meta_relation_'.$jj]))?$pval['nxs_meta_relation_'.$jj]:''; 
        }
      }
      
      if (!empty($pval['nxs_count_term_compares'])) $o['fltrs']['nxs_count_term_compares'] = $pval['nxs_count_term_compares'];
      if (!empty($pval['nxs_term_names'])) {
        $o['fltrs']['nxs_tax_names'] = (isset($pval['nxs_tax_names']))?$pval['nxs_tax_names']:'';  
        //## Check/insert missing terms
        if (!empty($pval['nxs_term_names']) && !empty($pval['nxs_tax_names']) && is_array($pval['nxs_term_names']) ) { $outT = array();
            foreach ($pval['nxs_term_names'] as $g) { $term = get_term( $g, $pval['nxs_tax_names'] ); if (!is_object($term)) { $t = wp_insert_term( $g, $pval['nxs_tax_names']);  $outT[] = $t['term_id']; } else  $outT[] = $g; }
            $pval['nxs_term_names'] = $outT;
        } $o['fltrs']['nxs_term_names'] = (isset($pval['nxs_term_names']))?$pval['nxs_term_names']:'';
        $o['fltrs']['nxs_term_operator'] = (isset($pval['nxs_term_operator']))?$pval['nxs_term_operator']:'';
        $o['fltrs']['nxs_term_children'] = (isset($pval['nxs_term_children']))?$pval['nxs_term_children']:'';
        $o['fltrs']['nxs_term_relation'] = (isset($pval['nxs_term_relation']))?$pval['nxs_term_relation']:'';
      } 
      if (!empty($pval['nxs_count_term_compares']) && $pval['nxs_count_term_compares']>1) for( $jj = 2; $jj <= $pval['nxs_count_term_compares']; $jj++ ) {
        $o['fltrs']['nxs_tax_names_'.$jj] = (isset($pval['nxs_tax_names_'.$jj]))?$pval['nxs_tax_names_'.$jj]:''; 
        //## Check/insert missing terms
        if (!empty($pval['nxs_term_names_' .$jj]) && !empty($pval['nxs_tax_names_' .$jj]) && is_array($pval['nxs_term_names_' .$jj]) ) { $outT = array();
          foreach ($pval['nxs_term_names_' .$jj] as $g) { $term = get_term( $g, $pval['nxs_tax_names_' .$jj] ); if (!is_object($term)) { $t = wp_insert_term( $g, $pval['nxs_tax_names_' .$jj]); $outT[] = $t['term_id']; } else  $outT[] = $g; }
          $pval['nxs_term_names_' .$jj] = $outT;
        } $o['fltrs']['nxs_term_names_'.$jj] = (isset($pval['nxs_term_names_'.$jj]))?$pval['nxs_term_names_'.$jj]:'';
        $o['fltrs']['nxs_term_operator_'.$jj] = (isset($pval['nxs_term_operator_'.$jj]))?$pval['nxs_term_operator_'.$jj]:'';
        $o['fltrs']['nxs_term_children_'.$jj] = (isset($pval['nxs_term_children_'.$jj]))?$pval['nxs_term_children_'.$jj]:'';
        $o['fltrs']['nxs_term_relation_'.$jj] = (isset($pval['nxs_term_relation_'.$jj]))?$pval['nxs_term_relation_'.$jj]:'';        
      } return $o;   
 }}
//##
if (!function_exists("nxs_clean_string")) { function nxs_clean_string($string) { $s = trim($string); 
  if (function_exists("iconv")) $s = iconv("UTF-8", "UTF-8//IGNORE", $s); elseif (function_exists("mb_convert_encoding") && function_exists("mb_split")) $s = mb_convert_encoding($s, "UTF-8", mb_detect_encoding($s)); // drop all non utf-8 characters
  //## this is some bad utf-8 byte sequence that makes mysql complain - control and formatting i think
  $s = preg_replace('/(?>[\x00-\x1F]|\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);
  $s = preg_replace('/\s+/', ' ', $s); // reduce all multiple whitespace to a single space
  $s = preg_replace("/[^[:alnum:][:space:]]/u", '', $s); // Leave only letters and numbers (Unicode)
  return $s;
}}

//## Tests
function nxs_cURLTestCode($url){  
  $out = 'There is a problem with cURL. You need to contact your server admin or hosting provider. Here is the PHP code to reproduce the problem:<br/><pre style="color:#005800">&lt;?php '."\r\n".' $ch = curl_init(); '."\r\n".' curl_setopt($ch, CURLOPT_URL, "'.$url.'"); '."\r\n".' curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"); '."\r\n".' curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); '."\r\n".' curl_setopt($ch, CURLOPT_TIMEOUT, 10); '."\r\n".' curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); '."\r\n".' $response = curl_exec($ch); '."\r\n".' $errmsg = curl_error($ch); '."\r\n".' $cInfo = curl_getinfo($ch); '."\r\n".' curl_close($ch); '."\r\n".' print_r($errmsg); '."\r\n".' print_r($cInfo); '."\r\n".' print_r($response); '."\r\n".'?&gt;</pre>'; return $out; 
}
function nxs_cURLTest($url, $msg, $testText){ if ($testText=='getMyIP') echo 'Getting IP... <br/>'; else echo "<br/>--== Test Requested ... ".$url."<br/>";  $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.39 Safari/537.36"); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_TIMEOUT, 10); curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  $response = curl_exec($ch); $errmsg = curl_error($ch); $cInfo = curl_getinfo($ch); curl_close($ch);  
  if ($testText=='getMyIP') { //prr($response); 
  if (stripos($response, 'Your IP Address:')!==false) $ip = strip_tags(CutFromTo($response,'Your IP Address:', '</span>')); elseif (stripos($response, '<span>Your IP</span>:')!==false) $ip = strip_tags(CutFromTo($response,'<span>Your IP</span>:', '</span>')); 
    if (!empty($ip)) echo "Your Server IP:".$ip.'<br/>'; } else { echo "Testing ... ".$url." - ".$cInfo['url']."<br/>";
    if (stripos($response, $testText)!==false) echo "....".$msg." - OK<br/>"; else { echo "....<b style='color:red;'>".$msg." - Problem</b><br/>"; prr($response); prr($errmsg); prr($cInfo); echo nxs_cURLTestCode($url);  }
  }
}

if (!function_exists("nxs_html_to_utf8")){ function nxs_html_to_utf8($str){ $str = html_entity_decode($str, ENT_QUOTES, "utf-8"); return $str;} }

if (!function_exists('nsx_doEncode')){ function nsx_doEncode($string,$key='NSX') { $key = sha1($key); $strLen = strlen($string);$keyLen = strlen($key); $j = 0; $hash = '';
  for ($i = 0; $i < $strLen; $i++) { $ordStr = ord(substr($string,$i,1)); if ($j == $keyLen) $j = 0; $ordKey = ord(substr($key,$j,1)); $j++; $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));} return $hash;
}}
if (!function_exists('nsx_doDecode')){ function nsx_doDecode($string,$key='NSX') { $key = sha1($key); $keyLen = strlen($key); $hash = ''; $sX = str_split($string, 2560);
  foreach($sX as $ss){$j=0; $sA=str_split($ss, 2); foreach($sA as $oS){$oS=hexdec(base_convert(strrev($oS),36,16)); if ($j==$keyLen) $j=0; $oK=ord(substr($key,$j,1)); $j++; $hash.=chr($oS-$oK);}} return $hash;
}}

if (!function_exists("nxs_mbConvertCaseUTF8var")){ function nxs_mbConvertCaseUTF8var($s) { $arr = preg_split("//u", $s, -1, PREG_SPLIT_NO_EMPTY); $result = ""; $mode = false; 
  foreach ($arr as $char) { $res = preg_match('/\\p{Mn}|\\p{Me}|\\p{Cf}|\\p{Lm}|\\p{Sk}|\\p{Lu}|\\p{Ll}|\\p{Lt}|\\p{Sk}|\\p{Cs}/u', $char) == 1; 
    if ($mode) { if (!$res)$mode = false; } elseif ($res) { $mode = true; $char = mb_convert_case($char, MB_CASE_TITLE, "UTF-8"); } $result .= $char; 
  } return $result; 
}} 
if (!function_exists("nxs_ucwords")){ function nxs_ucwords($str) { if (function_exists("mb_convert_case")) return nxs_mbConvertCaseUTF8var($str); else return ucwords($str); }}

if (!function_exists('nxs_doProcessTags')){ function nxs_doProcessTags($tags){ $tagsA = array(); if (!is_array($tags)) { $tags = explode(',', $tags); 
  global $nxs_SNAP; $tagsExclFrmHT = $nxs_SNAP->nxs_options['tagsExclFrmHT']; $tagsExclFrmHT = explode(',',$tagsExclFrmHT); foreach ($tagsExclFrmHT as $i=>$t) $tagsExclFrmHT[$i] = trim(strtolower($t));
  foreach ($tags as $tg) $tagsA[] = trim($tg); } else $tagsA = $tags; $tagsA = array_unique($tagsA);  $tags = array(); 
  //foreach ($tagsA as $tg) { $tags['tagsA'][] = $tg; $tags['htagsA'][] = "#".trim(str_replace(' ', '', preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(ucwords(str_ireplace('&', '', str_ireplace('&amp;','',$tg))))))); } 
  foreach ($tagsA as $tg) if (!in_array(strtolower($tg), $tagsExclFrmHT)) { $tags['tagsA'][] = $tg; $tags['htagsA'][] = "#".trim(str_replace(' ', '', nxs_clean_string(trim(ucwords(str_ireplace('&', '', str_ireplace('&amp;','',$tg))))))); }   
  $tags['tags'] =  implode(', ', $tags['tagsA']); $tags['htags'] = implode(', ', $tags['htagsA']);
  return $tags;
}}

if (!function_exists('nxs_gak')){ function nxs_gak($key){ if (!empty($key)) $key = (substr($key, 0, 5)=='x5g9a')?nsx_doDecode(substr($key, 5)):$key; return $key; }}
if (!function_exists('nxs_gas')){ function nxs_gas($sec){ if (!empty($sec)) $sec = (substr($sec, 0, 5)=='d3h0a')?nsx_doDecode(substr($sec, 5)):$sec; return $sec; }}

if (!function_exists('nxs_snapCleanHTML')){ function nxs_snapCleanHTML($html) { 
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html); $html = preg_replace('/<!--(.*)-->/Uis', "", $html); return $html;
}}

class NXS_HtmlFixer { public $dirtyhtml; public $fixedhtml; public $allowed_styles; private $matrix; public $debug; private $fixedhtmlDisplayCode;
    public function __construct() { $this->dirtyhtml = ""; $this->fixedhtml = ""; $this->debug = false; $this->fixedhtmlDisplayCode = ""; $this->allowed_styles = array();}
    public function getFixedHtml($dirtyhtml) { $c = 0; $this->dirtyhtml = $dirtyhtml; $this->fixedhtml = ""; $this->fixedhtmlDisplayCode = ""; if (is_array($this->matrix)) unset($this->matrix); $errorsFound=0;
      while ($c<10) { if ($c>0) $this->dirtyhtml = $this->fixedxhtml; $errorsFound = $this->charByCharJob(); if (!$errorsFound) $c=10;  $this->fixedxhtml=str_replace('<root>','',$this->fixedxhtml); 
        $this->fixedxhtml=str_replace('</root>','',$this->fixedxhtml); $this->fixedxhtml = $this->removeSpacesAndBadTags($this->fixedxhtml); $c++;
      } return $this->fixedxhtml;
    }
    private function fixStrToLower($m){ $right = strstr($m, '='); $left = str_replace($right,'',$m); return strtolower($left).$right;}
    private function fixQuotes($s){ $q = "\""; if (!stristr($s,"=")) return $s; $out = $s; preg_match_all("|=(.*)|",$s,$o,PREG_PATTERN_ORDER);
      for ($i = 0; $i< count ($o[1]); $i++) { $t = trim ( $o[1][$i] ) ; $lc=""; if ($t!="") { if ($t[strlen($t)-1]==">") { $lc= ($t[strlen($t)-2].$t[strlen($t)-1])=="/>"  ?  "/>"  :  ">" ; $t=substr($t,0,-1);}
        if (($t[0]!="\"")&&($t[0]!="'")) $out = str_replace( $t, "\"".$t,$out); else $q=$t[0]; if (($t[strlen($t)-1]!="\"")&&($t[strlen($t)-1]!="'")) $out = str_replace( $t.$lc, $t.$q.$lc,$out);
      }} return $out;
    }
    private function fixTag($t){  $t = preg_replace ( array( '/borderColor=([^ >])*/i', '/border=([^ >])*/i' ),  array('',''), $t);
        preg_match_all('/(?:"[^"]*"|\'[^\']*\'|[^"\'\s]+)+/', $t, $ar);  $ar = $ar[0];// prr($ar);
        $nt = ""; for ($i=0;$i<count($ar);$i++) { if (strpos($ar[$i], 'href=\\\\\\"')!==false) {$ar[$i] = str_replace('\\\\\\"','"',$ar[$i]);}
          if (strpos($ar[$i], 'href=\\"')!==false) {$ar[$i] = str_replace('\\"','"',$ar[$i]);} if (strpos($ar[$i], 'href=\"')!==false) {$ar[$i] = str_replace('\"','"',$ar[$i]);}
          $ar[$i]=$this->fixStrToLower($ar[$i]); if (stristr($ar[$i],"=")) $ar[$i] = $this->fixQuotes($ar[$i]); $nt.=$ar[$i]." ";   
        } $nt=preg_replace("/<( )*/i","<",$nt); $nt=preg_replace("/( )*>/i",">",$nt); return trim($nt);
    }
    private function extractChars($tag1,$tag2,$tutto) {  if (!stristr($tutto, $tag1)) return ''; $s=stristr($tutto,$tag1); $s=substr( $s,strlen($tag1)); if (!stristr($s,$tag2)) return '';
        $s1=stristr($s,$tag2); return substr($s,0,strlen($s)-strlen($s1));
    }
    private function mergeStyleAttributes($s) { $x = ""; $temp = ""; $c = 0;
        while(stristr($s,"style=\"")) {$temp = $this->extractChars("style=\"","\"",$s); if ($temp=="") { return preg_replace("/(\/)?>/i","\"\\1>",$s);}
            if ($c==0) $s = str_replace("style=\"".$temp."\"","##PUTITHERE##",$s); $s = str_replace("style=\"".$temp."\"","",$s); if (!preg_match("/;$/i",$temp)) $temp.=";"; $x.=$temp; $c++;
        }
        if (count($this->allowed_styles)>0) { $check=explode(';', $x); $x=""; foreach($check as $chk){ foreach($this->allowed_styles as $as) if(stripos($chk, $as) !== False) { $x.=$chk.';'; break; } }}
        if ($c>0) $s = str_replace("##PUTITHERE##","style=\"".$x."\"",$s);return $s;
    }
    private function fixAutoclosingTags($tag,$tipo=""){ if (in_array( $tipo, array ("img","input","br","hr")) ) { if (!stristr($tag,'/>')) $tag = str_replace('>','/>',$tag ); } return $tag; }
    private function getTypeOfTag($tag) { $tag = trim(preg_replace("/[\>\<\/]/i","",$tag)); $a = explode(" ",$tag); return $a[0];}
    private function checkTree() { $errorsCounter = 0; for ($i=1;$i<count($this->matrix);$i++) { $flag=false;
      if ($this->matrix[$i]["tagType"]=="div") { $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("p","b","i","font","u","small","strong","em"))) $flag=true; }
      if (in_array( $this->matrix[$i]["tagType"], array( "b", "strong" )) ) {  $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("b","strong"))) $flag=true; }
      if (in_array( $this->matrix[$i]["tagType"], array ( "i", "em") )) {  $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("i","em"))) $flag=true; }
      if ($this->matrix[$i]["tagType"]=="p") { $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("p","b","i","font","u","small","strong","em"))) $flag=true; }
      if ($this->matrix[$i]["tagType"]=="table") { $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("p","b","i","font","u","small","strong","em","tr","table"))) $flag=true; }
      if ($flag) { $errorsCounter++; if ($this->debug) echo "<div style='color:#ff0000'>Found a <b>".$this->matrix[$i]["tagType"]."</b> tag inside a <b>".htmlspecialchars($parentType)."</b> tag at node $i: MOVED</div>";                
        $swap = $this->matrix[$this->matrix[$i]["parentTag"]]["parentTag"]; if ($this->debug) echo "<div style='color:#ff0000'>Every node that has parent ".$this->matrix[$i]["parentTag"]." will have parent ".$swap."</div>";
        $this->matrix[$this->matrix[$i]["parentTag"]]["tag"]="<!-- T A G \"".$this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]."\" R E M O V E D -->"; $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]="";
        $hoSpostato=0;for ($j=count($this->matrix)-1;$j>=$i;$j--) { if ($this->matrix[$j]["parentTag"]==$this->matrix[$i]["parentTag"]) { $this->matrix[$j]["parentTag"] = $swap; $hoSpostato=1; }}
      }}return $errorsCounter;
    }
    private function findSonsOf($parentTag) { $out= "";
      for ($i=1;$i<count($this->matrix);$i++) { if ($this->matrix[$i]["parentTag"]==$parentTag) {
          if ($this->matrix[$i]["tag"]!="") { $out.=$this->matrix[$i]["pre"]; $out.=$this->matrix[$i]["tag"]; $out.=$this->matrix[$i]["post"]; } else { $out.=$this->matrix[$i]["pre"]; $out.=$this->matrix[$i]["post"];}
          if ($this->matrix[$i]["tag"]!="") { $out.=$this->findSonsOf($i); if ($this->matrix[$i]["tagType"]!="") { if (!in_array($this->matrix[$i]["tagType"], array ( "br","img","hr","input"))) $out.="</". $this->matrix[$i]["tagType"].">";}}
      }}return $out;
    }
    private function findSonsOfDisplayCode($parentTag) { $out= "";
        for ($i=1;$i<count($this->matrix);$i++) {
            if ($this->matrix[$i]["parentTag"]==$parentTag) { $out.= "<div style=\"padding-left:15\"><span style='float:left;background-color:#FFFF99;color:#000;'>{$i}:</span>";
                if ($this->matrix[$i]["tag"]!="") { if ($this->matrix[$i]["pre"]!="") $out.=htmlspecialchars($this->matrix[$i]["pre"])."<br>";
                    $out.="".htmlspecialchars($this->matrix[$i]["tag"])."<span style='background-color:red; color:white'>{$i} <em>".$this->matrix[$i]["tagType"]."</em></span>";
                    $out.=htmlspecialchars($this->matrix[$i]["post"]);
                } else { if ($this->matrix[$i]["pre"]!="") $out.=htmlspecialchars($this->matrix[$i]["pre"])."<br>"; $out.=htmlspecialchars($this->matrix[$i]["post"]);}
                if ($this->matrix[$i]["tag"]!="") { $out.="<div>".$this->findSonsOfDisplayCode($i)."</div>\n";
                    if ($this->matrix[$i]["tagType"]!="") {
                        if (($this->matrix[$i]["tagType"]!="br") && ($this->matrix[$i]["tagType"]!="img") && ($this->matrix[$i]["tagType"]!="hr")&& ($this->matrix[$i]["tagType"]!="input"))
                            $out.="<div style='color:red'>".htmlspecialchars("</". $this->matrix[$i]["tagType"].">")."{$i} <em>".$this->matrix[$i]["tagType"]."</em></div>";
                    }
                } $out.="</div>\n";
            }
        }return $out;
    }
    private function removeSpacesAndBadTags($s) { $i=0;
      while ($i<10) { $i++; $s = preg_replace (
        array( '/  /i', '/<p([^>])*>(&nbsp;)*\s*<\/p>/i', '/<span([^>])*>(&nbsp;)*\s*<\/span>/i', '/<strong([^>])*>(&nbsp;)*\s*<\/strong>/i', '/<em([^>])*>(&nbsp;)*\s*<\/em>/i',
          '/<font([^>])*>(&nbsp;)*\s*<\/font>/i', '/<small([^>])*>(&nbsp;)*\s*<\/small>/i', '/<\?xml:namespace([^>])*><\/\?xml:namespace>/i', '/<\?xml:namespace([^>])*\/>/i', '/class=\"MsoNormal\"/i',
          '/<o:p><\/o:p>/i', '/<!DOCTYPE([^>])*>/i', '/<!--(.|\s)*?-->/', '/<\?(.|\s)*?\?>/'), 
        array(' ', ' ', '', '', '', '', '', '', '', '', '', ' ', '', '' ) , trim($s));
      }return $s;
    }
    private function charByCharJob() { $s = $this->removeSpacesAndBadTags($this->dirtyhtml); if ($s=="") return; //echo "\r\n=!= ".$s." =!=\r\n<br/>\r\n";
        $s = "<root>".$s."</root>"; $contenuto = ""; $ns = ""; $i=0; $j=0; $ss=''; $indexparentTag=0; $padri=array(); array_push($padri,"0"); $this->matrix[$j]["tagType"]="";
        $this->matrix[$j]["tag"]=""; $this->matrix[$j]["parentTag"]="0"; $this->matrix[$j]["pre"]=""; $this->matrix[$j]["post"]=""; $tags=array();
        // echo "\r\n=#= ".$s." =#=\r\n<br/>\r\n";
        while($i<strlen($s)) {
            if ( $s[$i] =="<") { $contenuto = $ns; $ns = ""; $tag=""; while( $i<strlen($s) && $s[$i]!=">" ){ $tag.=$s[$i]; $i++;} $tag.=$s[$i]; if (stristr($tag,'<param') && stristr($tag,'/>')) $tag = str_replace('/>','></param>',$tag);
            $ss .= $tag;                 
        } else $ss .= $s[$i]; $i++; }
        $i=0; $s = $ss; //echo "\r\n== ".$s." ==\r\n<br/>\r\n";
        while($i<strlen($s)) {
            if ( $s[$i] =="<") { $contenuto = $ns; $ns = ""; $tag=""; while( $i<strlen($s) && $s[$i]!=">" ){ $tag.=$s[$i]; $i++;} $tag.=$s[$i];                
                if($s[$i]==">") { $tag = $this->fixTag($tag); $tagType = $this->getTypeOfTag($tag); $tag = $this->fixAutoclosingTags($tag,$tagType);
                    $tag = $this->mergeStyleAttributes($tag); if (!isset($tags[$tagType])) $tags[$tagType]=0; $tagok=true;
                    if (($tags[$tagType]==0)&&(stristr($tag,'/'.$tagType.'>'))&&(stristr($tag,'<'.$tagType)!==false)) { $tagok=false; if ($this->debug) echo "<div style='color:#ff0000'>Found a closing tag <b>".htmlspecialchars($tag)."</b> at char $i without open tag: REMOVED</div>";} else $tagok=true;
                }
                if ($tagok) { $j++; $this->matrix[$j]["pre"]=""; $this->matrix[$j]["post"]=""; $this->matrix[$j]["parentTag"]=""; $this->matrix[$j]["tag"]=""; $this->matrix[$j]["tagType"]="";
                    if (stristr($tag,'/'.$tagType.'>')) { $ind = array_pop($padri); $this->matrix[$j]["post"]=$contenuto; $this->matrix[$j]["parentTag"]=$ind; $tags[$tagType]--;
                    } else { if (@preg_match("/".$tagType."\/>$/i",$tag)||preg_match("/\/>/i",$tag)) { $this->matrix[$j]["tagType"]=$tagType; $this->matrix[$j]["tag"]=$tag;
                      $indexparentTag = array_pop($padri); array_push($padri,$indexparentTag); $this->matrix[$j]["parentTag"]=$indexparentTag; $this->matrix[$j]["pre"]=$contenuto; $this->matrix[$j]["post"]="";
                    } else { $tags[$tagType]++; $this->matrix[$j]["tagType"]=$tagType; $this->matrix[$j]["tag"]=$tag; $indexparentTag = array_pop($padri); array_push($padri,$indexparentTag);
                      array_push($padri,$j); $this->matrix[$j]["parentTag"]=$indexparentTag; $this->matrix[$j]["pre"]=$contenuto; $this->matrix[$j]["post"]=""; }
                    }
                }
            } else { $ns.=$s[$i]; } $i++;
        } for ($eli=$j+1;$eli<count($this->matrix);$eli++) { $this->matrix[$eli]["pre"]=""; $this->matrix[$eli]["post"]=""; $this->matrix[$eli]["parentTag"]=""; $this->matrix[$eli]["tag"]=""; $this->matrix[$eli]["tagType"]="";}
        $errorsCounter = $this->checkTree();  $this->fixedxhtml=$this->findSonsOf(0);return $errorsCounter;
    }
}

//## URL Shortener
if (!function_exists("nxs_mkShortURL")) { function nxs_mkShortURL($url, $postID=''){ $rurl = '';  global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $options = $nxs_SNAP->nxs_options; if (empty($options['nxsURLShrtnr'])) $options['nxsURLShrtnr'] = 'G'; $exSLinks = array();
    ///$ar = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,3); $ar = print_r($ar, true); nxs_LogIt('W','SURLX','','','SURLX',$ar); echo '<pre>'; echo $ar; echo '</pre>'; nxs_LogIt('W','SURL','','','SURL','NUI');     
    $murl =  md5($options['nxsURLShrtnr'].'-'.$url); $exSLinks = get_post_meta( $postID, '_nxs_slinks', true ); if (!empty($exSLinks) && is_array($exSLinks) && !empty($exSLinks[$murl])) return $exSLinks[$murl]; if (!is_array($exSLinks)) $exSLinks = array();
    if ($options['nxsURLShrtnr']=='B' && trim($options['bitlyUname']!='') && trim($options['bitlyAPIKey']!='')) {      
      $response  = nxs_remote_get('http://api-ssl.bitly.com/v3/shorten?login='.$options['bitlyUname'].'&apiKey='.$options['bitlyAPIKey'].'&longUrl='.urlencode($url), nxs_mkRemOptsArr('')); 
      if (is_nxs_error($response)) { nxs_LogIt('E', 'bit.ly', '', '', '-=ERROR=- '.print_r($response, true),'');  return $url; }
      $rtr = json_decode($response['body'],true);
      if ($rtr['status_code']=='200') $rurl = $rtr['data']['url']; else nxs_LogIt('E', '', 'bit.ly','','Error - bit.ly', print_r($rtr, true));
    } //echo "###".$rurl;
    if ($options['nxsURLShrtnr']=='A' && trim($options['adflyUname']!='') && trim($options['adflyAPIKey']!='')) {      
      $response  = nxs_remote_get('http://api.adf.ly/api.php?key='.$options['adflyAPIKey'].'&uid='.$options['adflyUname'].'&advert_type=int&domain='.$options['adflyDomain'].'&url='.urlencode($url), nxs_mkRemOptsArr(''));       
      if (is_nxs_error($response)) {   nxs_addToLogN('E', 'adf.ly', '', '-=ERROR=- '.print_r($response, true));  return $url; }     
      if ( $response['body']!='error')  $rurl = $response['body']; else {  nxs_addToLogN('E', 'adf.ly', '', '-=ERROR=- '.print_r($response, true)); return $url; }
    }
    if ($options['nxsURLShrtnr']=='C' && trim($options['clkimAPIKey']!='')) {    
      $response  = nxs_remote_get('http://api.clkim.com/?key='.$options['clkimAPIKey'].'&url='.urlencode($url), nxs_mkRemOptsArr(''));
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'clk.im', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = json_decode($response['body'], true); //prr($r); die();
      if (!is_array($r) || $r['error']!='0') { nxs_addToLogN('E', 'clk.im', '', '-=ERROR (JSON)=- '.print_r($response['body'], true)); return $url; } else $rurl = urldecode($r['short']);      
    }
    if ($options['nxsURLShrtnr']=='X' && trim($options['xcoAPIKey']!='')) {    
      $response  = nxs_remote_get('http://api.x.co/Squeeze.svc/text/'.$options['xcoAPIKey'].'?url='.urlencode($url), nxs_mkRemOptsArr('')); 
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'x.co', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = $response['body'];
      if (empty($r) || stripos($r, 'http://')===false) { nxs_addToLogN('E', 'x.co', '', '-=ERROR (RES)=- '.print_r($r, true)); return $url; } else $rurl = $r;
    }
    
    if ($options['nxsURLShrtnr']=='U') {    
      $flds = array('a'=>'add', 'url'=>$url); $response  = nxs_remote_post('http://u.to/', array('body' => $flds)); 
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'u.to', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = $response['body'];
      if (empty($r) || stripos($r, "#shurlout').val('")===false) { nxs_addToLogN('E', 'x.co', '', '-=ERROR (RES)=- '.print_r($r, true)); return $url; } else $rurl = CutFromTo($r,"#shurlout').val('","'");
    }
    
    if ($options['nxsURLShrtnr']=='R') { $urlR =   'https://api.rebrandly.com/v1/links/new?destination='.urlencode($url).'&domain[fullName]='.$options['rblyDomain']; 
      $hdrsArr = array('Content-Type'=>'application/json', 'apikey'=>$options['rblyAPIKey']); $advSet = nxs_mkRemOptsArr($hdrsArr); $response  = nxs_remote_get($urlR, $advSet);
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'Rebrandly', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $rtr = json_decode($response['body'],true); // prr($rtr);
      if (!is_array($rtr) || empty($rtr['shortUrl']) ) {   nxs_addToLogN('E', 'rebrandly', '', '-=ERROR=- '.print_r($response, true));  return $url; } $rurl = 'http://'.$rtr['shortUrl'];
    }
    
    if ($options['nxsURLShrtnr']=='P' && trim($options['postAPIKey']!='')) {      
      $response  = nxs_remote_get('http://po.st/api/shorten?longUrl='.urlencode($url).'&apiKey='.$options['postAPIKey'], nxs_mkRemOptsArr(''));       
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'po.st', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = json_decode($response['body'], true); 
      if (!is_array($r) || $r['status_txt']!='OK') { nxs_addToLogN('E', 'po.st', '', '-=ERROR (JSON)=- '.print_r($response['body'], true)); return $url; } else $rurl = $r['short_url'];
    }
    if ($options['nxsURLShrtnr']=='W' && function_exists('wp_get_shortlink')) { global $post; $post = get_post($postID);  $rurl = wp_get_shortlink($postID, 'post'); }
    if ($options['nxsURLShrtnr']=='Y' && trim($options['YOURLSKey']!='') && trim($options['YOURLSURL']!='')) { $timestamp = time(); $signature = md5( $timestamp . $options['YOURLSKey'] ); 
      $flds = array('signature'=>$signature, 'action' => 'shorturl', 'url'=>$url, 'format'=>'json', 'timestamp'=>$timestamp);  
      $response  = nxs_remote_post(($options['YOURLSURL']), array('body' => $flds)); 
      if (is_nxs_error($response)) {  nxs_addToLogN('E', 'YOURLS', '', '-=ERROR=- '.print_r($response, true)); return $url; } 
      $rtr = json_decode($response['body'],true);  if (!is_array($rtr) || !isset($rtr['shorturl']) ) {   nxs_addToLogN('E', 'YOURLS', '', '-=ERROR=- '.print_r($response, true));  return $url; }      
      $rurl = $rtr['shorturl'];
    
    }   
    if ($options['nxsURLShrtnr']=='O' || $options['nxsURLShrtnr']=='' || $options['nxsURLShrtnr']=='G') { //IS.GD/V.GD
      $advSet = nxs_mkRemOptsArr(nxs_makeHeaders()); $response  = nxs_remote_post('https://is.gd/create.php?format=simple&url='.urlencode($url), $advSet); //   nxs_addToLogN('E', 'is.gd', '', '-=URLS=- '.print_r($response['body'], true)); 
      if (is_nxs_error($response)) {   nxs_addToLogN('E', 'is.gd', '', '-=ERROR=- '.print_r($response, true));  return $url; } 
      $rtr = $response['body']; if (stripos($rtr, 'Error')!==false)  {   nxs_addToLogN('E', 'is.gd', '', '-=ERROR=- '.print_r($response, true));  return $url; }      
      $rurl = $rtr;
    }    
    //if ($rurl=='') { $response  = nxs_remote_get('http://gd.is/gtq/'.$url); if ((is_array($response) && ($response['response']['code']=='200'))) $rurl = $response['body']; }    
    if (!empty($rurl) && substr($rurl,0,4)=='http') { $rurl = substr($rurl,0,100); $url = $rurl; 
      if (!empty($postID) && is_numeric($postID)) { $exSLinks[$murl] = $rurl; update_post_meta( $postID, '_nxs_slinks', $exSLinks ); } 
    } return $url;
}}

//## Format Message (API)
if (!function_exists('nxs_doFormatMsg')){ function nxs_doFormatMsg($format, $message, $addURLParams=''){ global $nxs_urlLen; $msg = nxs_doSpin($format);
  $msgDef = array('title'=>'','announce'=>'','text'=>'','url'=>'','surl'=>'','urlDescr'=>'','urlTitle'=>'','imageURL' => array(),'videoCode'=>'','videoURL'=>'','siteName'=>'','tags'=>'','cats'=>'','authorName'=>'','orID'=>''); $message = array_merge($msgDef, $message);
  if (preg_match('/%URL%/', $msg)) { $url = $message['url']; if($addURLParams!='') $url .= (strpos($url,'?')!==false?'&':'?').$addURLParams;  $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%URL%", $url, $msg);}
  if (preg_match('/%SURL%/', $msg)) { 
    if (isset($message['surl']) && $message['surl']!='') $url = $message['surl']; else { $url = $message['url']; if($addURLParams!='') $url .= (strpos($url,'?')!==false?'&':'?').$addURLParams; $url = nxs_mkShortURL($url); } 
    $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%SURL%", $url, $msg);
  }
  if (preg_match('/%IMG%/', $msg)) { if (isset($message['imgURL']) && is_array($message['imgURL'])) { $imgURL = trim($message['imgURL']['large']); if ($imgURL=='') $imgURL = trim($message['imgURL']['medium']);   
      if ($imgURL=='') $imgURL = trim($message['imgURL']['original']); if ($imgURL=='') $imgURL = trim($message['imgURL']['thumb']);
    } elseif (!empty($message['imgURL'])) $imgURL = $message['imgURL']; else $imgURL = '';    $msg = str_ireplace("%IMG%", $imgURL, $msg); 
  }
  if (preg_match('/%IMGLARGE%/', $msg)) $msg = str_ireplace("%IMG%", trim($message['imgURL']['large'], $msg));  
  if (preg_match('/%IMGMEDIUM%/', $msg)) $msg = str_ireplace("%IMGMEDIUM%", trim($message['imgURL']['medium'], $msg));  
  if (preg_match('/%IMGTHUMB%/', $msg)) $msg = str_ireplace("%IMGTHUMB%", trim($message['imgURL']['thumb'], $msg));  
  if (preg_match('/%IMGORIGINAL%/', $msg)) $msg = str_ireplace("%IMGORIGINAL%", trim($message['imgURL']['original'], $msg));  
  
  if (preg_match('/%ORID%/', $msg)) $msg = str_ireplace("%ORID%", $message['orID'], $msg);  
  if (preg_match('/%TITLE%/', $msg)) $msg = str_ireplace("%TITLE%", $message['title'], $msg);  
  if (preg_match('/%STITLE%/', $msg)) { $title = substr($message['title'], 0, 115); $msg = str_ireplace("%STITLE%", $title, $msg); }                    
  if (preg_match('/%AUTHORNAME%/', $msg)) $msg = str_ireplace("%AUTHORNAME%", $message['authorName'], $msg);
  if (preg_match('/%SITENAME%/', $msg)) $msg = str_ireplace("%SITENAME%", $message['siteName'], $msg); 
  
  if (preg_match('/%ANNOUNCE%/', $msg)) { $sText = (!empty($message['announce']))?$message['announce']:nsTrnc($message['text'], 300, " ", "...");  $msg = str_ireplace("%ANNOUNCE%", $sText, $msg); }
  if (preg_match('/%EXCERPT%/', $msg)) { $sText = (!empty($message['announce']))?$message['announce']:nsTrnc($message['text'], 300, " ", "...");  $msg = str_ireplace("%EXCERPT%", $sText, $msg); }
  if (preg_match('/%RAWEXCERPT%/', $msg)) { $sText = (!empty($message['announce']))?$message['announce']:nsTrnc($message['text'], 300, " ", "...");  $msg = str_ireplace("%RAWEXCERPT%", $sText, $msg); }
  
  if (preg_match('/%TEXT%/', $msg)) $msg = str_ireplace("%TEXT%", $message['text'], $msg);     
  if (preg_match('/%FULLTEXT%/', $msg)) $msg = str_ireplace("%FULLTEXT%", $message['text'], $msg);     
  if (preg_match('/%RAWTEXT%/', $msg)) $msg = str_ireplace("%RAWTEXT%", $message['text'], $msg);     
      
  
  if (preg_match('/%TAGS%/', $msg)) { $tags = nxs_doProcessTags($message['tags']);  $msg = str_ireplace("%TAGS%", $tags['tags'], $msg); }
  if (preg_match('/%HTAGS%/', $msg)) { $tags = nxs_doProcessTags($message['tags']);  $msg = str_ireplace("%HTAGS%", $tags['htags'], $msg); }
  if (preg_match('/%CATS%/', $msg)) { $tags = nxs_doProcessTags($message['cats']);  $msg = str_ireplace("%CATS%", $tags['cats'], $msg); }
  if (preg_match('/%HCATS%/', $msg)) { $tags = nxs_doProcessTags($message['hcats']);  $msg = str_ireplace("%HCATS%", $tags['hcats'], $msg); }
    
  if (preg_match('/%+CF-[a-zA-Z0-9-_]+%/', $msg)) { $msgA = explode('%CF', $msg); $mout = '';
    foreach ($msgA as $mms) { 
        if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false) { $mGr = CutFromTo($mms, '-', '%'); $cfItem = $message[$mGr]; $mms = str_ireplace("-".$mGr."%", $cfItem, $mms); } $mout .= $mms; 
    } $msg = $mout; 
  }  
  return trim($msg);
}}
//## Process Spin 
if (!function_exists("nxs_spinRecursion")) { function nxs_spinRecursion(&$txt, $startCh) { global $nxs_spin_lCh, $nxs_spin_rCh, $nxs_spin_splCh; $startPos = $startCh;
  while ($startCh++ < strlen($txt)) {
    if (substr($txt, $startCh, strlen($nxs_spin_lCh)) == $nxs_spin_lCh)  $txt = nxs_spinRecursion($txt, $startCh);
    elseif (substr($txt, $startCh, strlen($nxs_spin_rCh)) == $nxs_spin_rCh) {
      $tmpTxt = substr($txt, $startPos+strlen($nxs_spin_lCh), ($startCh - $startPos)-strlen($nxs_spin_rCh));
      $toRepl = nxs_spinReplace($tmpTxt); $txt = str_replace($nxs_spin_lCh.$tmpTxt.$nxs_spin_rCh, $toRepl, $txt);
    }
  } return $txt;
}}
if (!function_exists("nxs_spinReplace")) { function nxs_spinReplace($txt) { global $nxs_spin_splCh; $txt = explode($nxs_spin_splCh, $txt);  $out = $txt[mt_rand(0,count($txt)-1)]; return $out; }}
if (!function_exists("nxs_doSpin")) { function nxs_doSpin($msg){  global $nxs_spin_lCh, $nxs_spin_rCh, $nxs_spin_splCh;
    $nxs_spin_lCh = '{'; $nxs_spin_rCh='}'; $nxs_spin_splCh='|'; $msg = nxs_spinRecursion($msg, -1); return $msg;
}}
if (!function_exists("nxs_admin_notice__wrongProHelper")) { function nxs_admin_notice__wrongProHelper() { $class = 'notice notice-error'; $message = __( 'SNAP Pro Upgrade Helper Version', 'social-networks-auto-poster-facebook-twitter-g' ).' '.NextScripts_UPG_SNAP_Version.' '.__( 'is not compatible with SNAP v4. Please upgrade it to version 1.4.0 oh higher', 'social-networks-auto-poster-facebook-twitter-g' ); 
  printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}}              
if (!function_exists("nxs_isMobile")) { function nxs_isMobile() {
 return preg_match("/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i", $_SERVER["HTTP_USER_AGENT"]);
}}

?>