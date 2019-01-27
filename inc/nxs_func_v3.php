<?php
//## Reposter
function nxs_adjRpst($optionsii, $pval)
{
    if (empty($optionsii['rpstDays'])) {
        $optionsii['rpstDays'] = 0;
    }
    if (empty($optionsii['rpstHrs'])) {
        $optionsii['rpstHrs'] = 0;
    }
    if (empty($optionsii['rpstMins'])) {
        $optionsii['rpstMins'] = 0;
    }
    
    $rpstEvrySecEx = $optionsii['rpstDays']*86400+$optionsii['rpstHrs']*3600+$optionsii['rpstMins']*60;
    $isRpstWasOn = isset($optionsii['rpstOn']) && $optionsii['rpstOn']=='1';
    
    if (isset($pval['rpstOn'])) {
        $optionsii['rpstOn'] = $pval['rpstOn'];
    } else {
        $optionsii['rpstOn'] = 0;
    }
    
    if (isset($pval['rpstDays'])) {
        $optionsii['rpstDays'] = trim($pval['rpstDays']);
    }
    if (isset($pval['rpstHrs'])) {
        $optionsii['rpstHrs'] = trim($pval['rpstHrs']);
    }
    if ((int)$optionsii['rpstHrs']>23) {
        $optionsii['rpstHrs'] = 23;
    }
    if (isset($pval['rpstMins'])) {
        $optionsii['rpstMins'] = trim($pval['rpstMins']);
    }
    if ((int)$optionsii['rpstMins']>59) {
        $optionsii['rpstMins'] = 59;
    }
    if (isset($pval['rpstRndMins'])) {
        $optionsii['rpstRndMins'] = trim($pval['rpstRndMins']);
    }
    if (isset($pval['rpstPostIncl'])) {
        $optionsii['rpstPostIncl'] = trim($pval['rpstPostIncl']);
    }
    
    if (isset($pval['rpstStop'])) {
        $optionsii['rpstStop'] = trim($pval['rpstStop']);
    } else {
        $optionsii['rpstStop'] = 'O';
    }
     
    
    $rpstEvrySecNew = $optionsii['rpstDays']*86400+$optionsii['rpstHrs']*3600+$optionsii['rpstMins']*60;
    $rpstRNDSecs = isset($optionsii['rpstRndMins'])?$optionsii['rpstRndMins']*60:0;
    if ($rpstRNDSecs>$rpstEvrySecNew) {
        $optionsii['rpstRndMins'] = 0;
    }
    
    if ($rpstEvrySecNew!=$rpstEvrySecEx || (!$isRpstWasOn && $optionsii['rpstOn']=='1')) {
        $currTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
        $optionsii['rpstNxTime'] = $currTime + $rpstEvrySecNew;
    }
    if (isset($pval['rpstType'])) {
        $optionsii['rpstType'] = trim($pval['rpstType']);
    }
    if (isset($pval['rpstTimeType'])) {
        $optionsii['rpstTimeType'] = trim($pval['rpstTimeType']);
    }
    if (isset($pval['rpstFromTime'])) {
        $optionsii['rpstFromTime'] = trim($pval['rpstFromTime']);
    }
    if (isset($pval['rpstToTime'])) {
        $optionsii['rpstToTime'] = trim($pval['rpstToTime']);
    }
    if (isset($pval['rpstOLDays'])) {
        $optionsii['rpstOLDays'] = trim($pval['rpstOLDays']);
    }
    if (isset($pval['rpstNWDays'])) {
        $optionsii['rpstNWDays'] = trim($pval['rpstNWDays']);
    }
    if (isset($pval['rpstOnlyPUP'])) {
        $optionsii['rpstOnlyPUP'] = trim($pval['rpstOnlyPUP']);
    } else {
        $optionsii['rpstOnlyPUP'] = 0;
    }
    
    if (isset($pval['nxsCPTSeld'])) {
        $optionsii['nxsCPTSeld'] = serialize($pval['nxsCPTSeld']);
    }
    
    if (isset($pval['fltrsOn'])) {
        $optionsii['fltrsOn'] = trim($pval['fltrsOn']);
    } else {
        $optionsii['fltrsOn'] = 0;
    }
    
    if (isset($pval['catSel'])) {
        $optionsii['catSel'] = trim($pval['catSel']);
    }
    if (!empty($optionsii['catSel']) && $optionsii['catSel']=='1' && trim($pval['catSelEd'])!='') {
        $optionsii['catSelEd'] = trim($pval['catSelEd']);
    } else {
        $optionsii['catSelEd'] = '';
    }
      

    if (isset($pval['tagsSel'])) {
        $optionsii['tagsSel'] = trim($pval['tagsSel']);
        $tagsSelX = array();
        $tggsSel = explode(',', $optionsii['tagsSel']);
        foreach ($tggsSel as $tggg) {
            $tggg = trim($tggg);
            $tagsSelX[] = $tggg;
            if (stripos($tggg, '|')!==false) {
                $tgArr =  explode('|', $tggg);
                $taxonomy = $tgArr[0];
                $tgggT = $tgArr[1];
            } else {
                $taxonomy = 'post_tag';
                $tgggT = $tggg;
            }
            $tgArr = get_term_by('slug', $tgggT, $taxonomy, ARRAY_A);
            if (is_array($tgArr)) {
                $tagsSelX[] = $tgArr['term_id'];
            }
        }
        $optionsii['tagsSelX'] = implode(',', $tagsSelX);
    }
    if (isset($pval['custTaxSel'])) {
        $optionsii['custTaxSel'] = trim($pval['custTaxSel']);
    }
        
    if (isset($pval['rpstBtwHrsType'])) {
        $optionsii['rpstBtwHrsType'] = trim($pval['rpstBtwHrsType']);
    }
    if (isset($pval['rpstBtwHrsT'])) {
        $optionsii['rpstBtwHrsT'] = trim($pval['rpstBtwHrsT']);
    }
    if (isset($optionsii['rpstBtwHrsT'])&&(int)$optionsii['rpstBtwHrsT']>23) {
        $optionsii['rpstBtwHrsT'] = 23;
    }
    if (isset($pval['rpstBtwHrsF'])) {
        $optionsii['rpstBtwHrsF'] = trim($pval['rpstBtwHrsF']);
    }
    if (isset($optionsii['rpstBtwHrsF'])&&(int)$optionsii['rpstBtwHrsF']>23) {
        $optionsii['rpstBtwHrsF'] = 23;
    }
    if (isset($pval['rpstBtwDays'])) {
        $optionsii['rpstBtwDays'] = $pval['rpstBtwDays'];
    } else {
        $optionsii['rpstBtwDays'] = array();
    }
    return $optionsii;
}
