jQuery(document).ready(function() {  nxs_doTabs();
  
  jQuery('#nxs_ntType').ddslick({ width: 200, imagePosition: "left", selectText: "Select network", onSelected: function (data) { doShowFillBlockX(data.selectedData.value);}});      
  
    
  if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) { //## Chrome Autofill is evil
    jQuery(window).load(function(){
        jQuery('input:-webkit-autofill').each(function(){ var text =jQuery(this).val(); var name = jQuery(this).attr('name'); jQuery(this).after(this.outerHTML).remove(); jQuery('input[name=' + name + ']').val(text);});
    });
  }  
  //jQuery( "input[onchange^='nxs_doShowWarning']" ).prop("indeterminate", true).css( "-webkit-appearance", "checkbox" );  
  //jQuery( "input[onchange^='nxs_doShowWarning']" ).prop("indeterminate", true).css("background", "#D0D0D0").css("border-color", "#999");    
  //## Submit Serialized Form - avoid Max.Vars limit.
  jQuery('#nsStFormMisc').submit(function() { var dataA = jQuery('#nsStForm').serialize(); jQuery('#nxsMainFromElementAccts').val(dataA); });
  jQuery('#nsStForm').submit(function() { jQuery('#nsStFormMisc').submit(); return false; });  
  var nxs_isPrevirew = false;   
  jQuery('#post-preview').click(function(event) { nxs_isPrevirew = true; });  
  jQuery('#post').submit(function(event) { if (nxs_isPrevirew == true) return; if (jQuery("#NXS_MetaFieldsIN").length==0) return;  jQuery('body').append('<form id="nxs_tempForm"></form>'); jQuery("#NXS_MetaFieldsIN").appendTo("#nxs_tempForm");  
      var nxsmf = jQuery('#nxs_tempForm').serialize();  jQuery( "#NXS_MetaFieldsIN" ).remove(); jQuery('#nxs_snapPostOptions').val(nxsmf); //alert(nxsmf);  alert(jQuery('#nxs_snapPostOptions').val()); return false; 
  });  
  
  //jQuery( "#HGTHT" ).on( "click", function(e) { e.preventDefault();  alert( jQuery( this ).text() ); });
  
  //## Show/Hide Metabx Blocks
  nxs_showHideMetaBoxBlocks();  jQuery('.nxs_acctcb').on("change", function(e) { jQuery('.nxstbl'+e.target.id).toggle(); });  
  
  //## +- in the post edit  
  jQuery('.nsx_iconedTitle').on("click", function(e) { var divid = jQuery('.nxstb'+jQuery(this).prop('id')); if (divid.length>0) { jQuery(this).find('span').html(jQuery(this).find('span').html() == '[-]' ? '[+]' : '[-]');  divid.toggle(); } });    
  
  jQuery('.nxs_Cancel_Q').on("click", function(e) { e.preventDefault();  var curr = jQuery(e.target);  var cID = curr[0].id.split("Q_"); cID = cID[1]; //console.log( cID) ;  
    jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"cancQ", cid: cID, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
         jQuery("#nxs_QU_"+cID).hide(); 
    }, "html")
  });
  
  
  jQuery('.nxs_acctcb').iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'});
  jQuery('.nxs_acctcb').iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'});
  
  jQuery('.nxs_acctcb').on('ifChanged', function(event){  nxs_showHideMetaBoxBlocks(); });
  //nxs_acctcb
  jQuery('.nxs_acctcb').on('ifClicked', function(event){ if (jQuery(this).attr('type')=='radio') {  nxs_hidePopUpInfo('popShAttFLT'); jQuery(this).attr('type', 'checkbox'); jQuery(this).val('1'); jQuery(this).iCheck('destroy'); 
    jQuery(this).iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'}); 
    jQuery(this).attr('id', jQuery(this).attr('id').replace('rbtn','do'));
    jQuery('.nxs_acctcb').on('ifChanged', function(event){ nxs_showHideMetaBoxBlocks(); }); 
  } });
    
  //## Handle radiobutton for filters
  jQuery('.iradio_minimal-blue').on('ifChanged', function(event){  nxs_showHideMetaBoxBlocks(); });
  jQuery( ".iCheck-helper" ).mouseover(function() { 
    if (jQuery(this).parent().hasClass('iradio_minimal-blue')) nxs_showPopUpInfo('popShAttFLT', event, jQuery(this).parent().find("input").data('fltinfo'));
  });
  jQuery( ".iCheck-helper" ).mouseout(function() { nxs_hidePopUpInfo('popShAttFLT'); });
  
  jQuery( ".nxsShowQmark" ).mouseover(function() { nxs_showPopUpInfo(jQuery(this).attr('longdesc'), event);});
  jQuery( ".nxsShowQmark" ).mouseout(function() { nxs_hidePopUpInfo(jQuery(this).attr('longdesc')); });
  
  
  
  jQuery( ".rpstrTimes" ).change(function() {
    switch(jQuery(this).val()) {
        case 'A' :
            jQuery( "#rpstPostEveryOptions" ).show(); jQuery( "#rpstPostSpTimesOptions" ).hide();
            break;
        case 'S' :
            jQuery( "#rpstPostEveryOptions" ).hide(); jQuery( "#rpstPostSpTimesOptions" ).show();
            break;
    }            
  });
  
    
    jQuery( ".bClose" ).on( "click",function(e) { jQuery.pgwModal('close'); });      
        
    jQuery( ".manualPostBtn" ).on( "click", nxs_doManPost);      
    jQuery( ".nxsPostNowBtn" ).on( "click", nxs_doManPost);
    jQuery( ".manualAllPostBtn" ).on( "click", nxs_doAllManPost);
    
    jQuery('.nxsImgCtrlCb').on("change", function(e) {  var curr = jQuery(e.target); if (curr.val()=='I') jQuery('#altFormatIMG'+curr.data('nt')+curr.data('ii')).show(); else jQuery('#altFormatIMG'+curr.data('nt')+curr.data('ii')).hide(); });  
    
    jQuery('.nxsEdNTLineHeader').on("click", function(e) {  var curr = jQuery(e.target); jQuery('#nxsNTSetDiv'+curr.data('nt').toUpperCase()+curr.data('ii')).toggle(); });
    jQuery('.nxsEdNTLineShowSettings').on("click", function(e) { e.preventDefault;  var curr = jQuery(e.target); jQuery('#nxsNTSetDiv'+curr.data('nt').toUpperCase()+curr.data('ii')).toggle(); return false; });
    
    
    /* ### Reset Stats ## */
    jQuery('#nxs_rstRPStats').on('click', function(e /*, params */) { e.preventDefault();  var rid = jQuery(e.target).data('rid');  jQuery("#nxs_resetStats").val('1'); nxs_svRep(rid); });
    
    
    var kkk = new jBox("Tooltip",{attach:".showListOfPostsXX",preventDefault:true,closeOnMouseleave:true,animation:"zoomIn",
      position: { x: 'left', y: 'center' },
      outside: 'x',      
      getContent: 'data-qid',      
      adjustDistance:{top:55,right:5,bottom:5,left:5},zIndex:4e3}
    );    
    
    
    jQuery('.riTo_button').click(function() { var ii = jQuery(this).data('ii'); var nt = jQuery(this).data('nt'); var pid = jQuery(this).data('pid'); 
      jQuery('#nxs_gPopupContent').html("<p>Getting Replies from "+nt+" ....</p>" + "<p></p>");
      //jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, positionStyle: 'fixed'});        
      jQuery.pgwModal({ target: '#nxs_gPopupCntWrap', title: 'Comments Import', maxWidth: 800, closeOnBackgroundClick : false});      
      jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"importComments", nt:nt, pid:pid, ii:ii, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
          jQuery('#nxs_gPopupContent').html('<p> ' + j + '</p>' +'<input type="button" onclick="jQuery.pgwModal(\'close\');" class="bClose" value="Close">');     
      }, "html");          
    }); 

  
  //## End of ready.function
});

window.onclick = function(event) { if (!event.target.matches('.nxs_dropbtn')) {
    var dropdowns = document.getElementsByClassName("nxs_dropdown-content");  var i;
    for (i = 0; i < dropdowns.length; i++) { var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('nxs_show')) { openDropdown.classList.remove('nxs_show'); }
    }
  }
}

function nxs_showTimeDialog(ntii,dmode){ if (dmode=='C') dmode = 0;
    jQuery('#'+ntii+'timeToRunWM').val(dmode); jQuery('#nxs_timeID').val(ntii);  var dd = jQuery('#'+ntii+'timeToRun').val();  
    if (!!dd) { 
      if ( dd.indexOf('|')>0 ) { dd = dd.split('|'); if (dmode=='A') dd = dd[dd.length-1]; else dd = dd[dmode]; } 
      if (dd=='i') {var dC=new Date(); var dd = (dC.getTime() / 1000); } 
      nxs_fillTime(dd*1000);   
    } jQuery.pgwModal({ target: '#showSetTimeInt', title: 'Post', maxWidth: 800, closeOnBackgroundClick : true}); 
}

function nxs_getItFromNT(nt,ii,outElemID,fName,params){ jQuery("#"+nt+"LoadingImg"+ii).show();
  jQuery.post(ajaxurl,{nt:nt,ii:ii,fName:fName,params:params, nxs_mqTest:"'", action: 'nxs_snap_aj', "nxsact":"getItFromNT", id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    jQuery("#"+outElemID).html(j); jQuery("#"+nt+"LoadingImg"+ii).hide();
  }, "html")
}

function nxs_svSetAdv(nt,ii,divIn,divOut,loc,isModal){ jQuery(':focus').blur();
    //## jQuery clone fix for empty textareas
    (function (original) { jQuery.fn.clone = function () { var result = original.apply(this, arguments),
      my_textareas = this.find('textarea').add(this.filter('textarea')), result_textareas = result.find('textarea').add(result.filter('textarea')), 
      my_selects = this.find('select').add(this.filter('select')), result_selects = result.find('select').add(result.filter('select'));
      for (var i = 0, l = my_textareas.length; i < l; ++i) jQuery(result_textareas[i]).val( jQuery(my_textareas[i]).val() );    
      for (var i = 0, l = my_selects.length; i < l; ++i) for (var j = 0, m = my_selects[i].options.length; j < m; ++j) if (my_selects[i].options[j].selected === true) result_selects[i].options[j].selected = true;    
      return result;
    }; }) (jQuery.fn.clone); 
    //## /END jQuery clone fix for empty textareas
    if (isModal=='1') { jQuery("#"+divIn).addClass("loading");  jQuery("#nxsSaveLoadingImg"+nt+ii).show(); } else { jQuery("#"+nt+ii+"ldImg").show(); jQuery("#"+nt+ii+"rfrshImg").hide();   }
    if (divIn=='nxsAllAccntsDiv' && jQuery("#nxsAllAccntsDiv").length && jQuery("#nxsSettingsDiv").length) jQuery("#nxsSettingsDiv").appendTo("#nxsAllAccntsDiv");
    
    var isOut=''; if (typeof(divOut)!='undefined' && divOut!='') isOut = '<input type="hidden" name="isOut" value="1" />';
    jQuery('#svSetRef').val(jQuery("input[name='_wp_http_referer']").val()); jQuery('#svSetNounce').val(jQuery('#nxsSsPageWPN_wpnonce').val());
    frmTxt = '<div id="nxs_tmpDiv_'+nt+ii+'" style="display:none;"><form id="nxs_tmpFrm_'+nt+ii+'"><input name="action" value="nxs_snap_aj" type="hidden" /><input name="nxsact" value="setNTS" type="hidden" /><input name="nxs_mqTest" value="\'" type="hidden" /><input type="hidden" name="_wp_http_referer" value="'+jQuery("input[name='_wp_http_referer']").val()+'" /><input type="hidden" name="_wpnonce" value="'+jQuery('#nxsSsPageWPN_wpnonce').val()+'" />'+isOut+'</form></div>';
    jQuery("body").append(frmTxt); jQuery("#"+divIn).clone(true).appendTo("#nxs_tmpFrm_"+nt+ii); 
    var serTxt = jQuery("#nxs_tmpFrm_"+nt+ii).serialize(); jQuery("#nxs_tmpDiv_"+nt+ii).remove();// alert(serTxt);
    jQuery.ajax({ type: "POST", url: ajaxurl, data: serTxt, 
      success: function(data){ if (isModal=='1') jQuery("#nxsAllAccntsDiv").removeClass("loading"); else {  jQuery("#"+nt+ii+"rfrshImg").show(); jQuery("#"+nt+ii+"ldImg").hide(); }
        if(typeof(divOut)!='undefined' && divOut!='' && data!='OK') jQuery('#'+divOut).html(data); 
        if (isModal=='1') {  jQuery("#nxsSaveLoadingImg"+nt+ii).hide(); jQuery("#doneMsg"+nt+ii).show(); jQuery("#doneMsg"+nt+ii).delay(600).fadeOut(3200); } 
        if (loc!='') { if (loc!='r') window.location = loc; else { window.location = jQuery(location).attr('href').split('#')[0]; } return false; } 
      }
    });    
}

//## API Specific Functions
//## GP
      function nxs_gpGetAllInfo(ii,force){ var u = jQuery('#apGPUName'+ii).val(); var p = jQuery('#apGPPass'+ii).val(); var pstAs = jQuery('#gpPostAs'+ii).val(); var pg = jQuery('#gpWhToPost'+ii).val(); jQuery("#gpPostAs"+ii).focus();
            jQuery('#gp'+ii+'rfrshImg').hide();  jQuery('#gp'+ii+'ldImg').show();
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPages", nt:"GP", u:u, p:p, ii:ii, pg:pg, pstAs:pstAs, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) { jQuery("#gpPostAs"+ii).html(j);  nxs_gpGetWhereToPost(ii,force); 
                   if (jQuery('#gpPostAs'+ii).val()!='a' && jQuery('#gpPostAs'+ii).val()!='p') jQuery('#gpPostAsNm'+ii).val(jQuery('#gpPostAs'+ii+' :selected').text()); else jQuery('#gpPostAsNm'+ii).val('p');
                 } else { jQuery("#nxsGPMsgDiv"+ii).html(j);   jQuery('#gp'+ii+'ldImg').hide();  jQuery('#gp'+ii+'rfrshImg').show(); }
            }, "html")          
      }
      function nxs_gpGetWhereToPost(ii,force){ var u = jQuery('#apGPUName'+ii).val(); var p = jQuery('#apGPPass'+ii).val(); var pstAs = jQuery('#gpPostAs'+ii).val(); var pg = jQuery('#gpWhToPost'+ii).val(); var pstAsNm = jQuery('#gpPostAs'+ii+' :selected').text();
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListWhereToPost", nt:"GP", u:u, p:p, ii:ii, pg:pg, pstAs:pstAs, pstAsNm:pstAsNm, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 jQuery("#gpWhToPost"+ii).html(j);  nxs_gpGetCommCats(ii,force);
            }, "html")          
      }
      function nxs_gpGetCommCats(ii,force){ var u = jQuery('#apGPUName'+ii).val(); var p = jQuery('#apGPPass'+ii).val(); var pstAs = jQuery('#gpPostAs'+ii).val(); var pg = jQuery('#gpWhToPost'+ii).val();
         if (pg.charAt(0)=='c'){ 
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGPCommInfo", nt:"GP", u:u, p:p, ii:ii, pg:pg, pstAs:pstAs, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 jQuery("#gpCommCat"+ii).html(j); jQuery('#nxsGPInfoDivComm'+ii).show();   jQuery('#gp'+ii+'ldImg').hide();  jQuery('#gp'+ii+'rfrshImg').show(); jQuery("#nxsGPMsgDiv"+ii).html("&nbsp;"); 
            }, "html")          
         } else { jQuery('#gp'+ii+'ldImg').hide();  jQuery('#gp'+ii+'rfrshImg').show(); jQuery("#nxsGPMsgDiv"+ii).html("&nbsp;");  }
      }
      function nxs_gpPostAsChange(ii, sObj){  if (sObj.val()!='a' && sObj.val()!='p') jQuery('#gpPostAsNm'+ii).val(jQuery('#gpPostAs'+ii+' :selected').text()); else jQuery('#gpPostAsNm'+ii).val('p');
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#gpPstAsCst'+ii).show(); } 
            else { jQuery('#gp'+ii+'ldImg').show(); jQuery('#gp'+ii+'rfrshImg').hide(); nxs_gpGetWhereToPost(ii,0); }
      }      
      function nxs_gpWhToPostChange(ii, sObj){  jQuery('#nxsGPInfoDivComm'+ii).hide(); if (jQuery('#gpPostAs'+ii).val()!='a' && jQuery('#gpPostAs'+ii).val()!='p') jQuery('#gpPostAsNm'+ii).val(jQuery('#gpPostAs'+ii+' :selected').text()); else jQuery('#gpPostAsNm'+ii).val('p');
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#gpPgIDcst'+ii).show(); }
          if (sObj.val().charAt(0)=='c'){ jQuery('#gp'+ii+'ldImg').show(); jQuery('#gp'+ii+'rfrshImg').hide();  var pg = sObj.val(); nxs_gpGetCommCats(ii,0); }
      }
//## RD
      function nxs_rdGetSRs(ii,force){ var u = jQuery('#apRDUName'+ii).val(); var p = jQuery('#apRDPass'+ii).val(); var rdSR = jQuery('#rdSubReddit'+ii).val(); jQuery("#rdSubReddit"+ii).focus();
            jQuery('#rd'+ii+'rfrshImg').hide();  jQuery('#rd'+ii+'ldImg').show(); jQuery("#nxsRDMsgDiv"+ii).html("&nbsp;"); jQuery("#rdSubReddit"+ii).html("<option value=\"\">Getting SubReddits.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfSubReddits", nt:"RD", u:u, p:p, ii:ii, rdSR:rdSR, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#rdSubReddit"+ii).html(j); else jQuery("#nxsRDMsgDiv"+ii).html(j); jQuery('#rd'+ii+'ldImg').hide(); jQuery('#rd'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_rdSRChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#rdSRIDCst'+ii).show(); } 
      }                  
//## PN
      function nxs_pnGetBoards(ii,force){ var u = jQuery('#apPNUName'+ii).val(); var p = jQuery('#apPNPass'+ii).val(); var pnBoard = jQuery('#pnBoard'+ii).val(); jQuery("#pnBoard"+ii).focus();
            jQuery('#pn'+ii+'rfrshImg').hide();  jQuery('#pn'+ii+'ldImg').show(); jQuery("#nxsPNMsgDiv"+ii).html("&nbsp;"); jQuery("#pnBoard"+ii).html("<option value=\"\">Getting boards.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPNBoards", nt:"PN", u:u, p:p, ii:ii, pnBoard:pnBoard, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#pnBoard"+ii).html(j); else jQuery("#nxsPNMsgDiv"+ii).html(j); jQuery('#pn'+ii+'ldImg').hide(); jQuery('#pn'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_pnBoardChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#pnBRDIDCst'+ii).show(); } 
      }                  
//## TR
      function nxs_trGetBlogs(ii,force){ var u = jQuery('#trappKey'+ii).val(); var p = jQuery('#trAuthUser'+ii).val(); var cBlog = jQuery('#trpgID'+ii).val(); jQuery("#trpgID"+ii).focus();
            jQuery('#tr'+ii+'rfrshImg').hide();  jQuery('#tr'+ii+'ldImg').show(); jQuery("#nxsTRMsgDiv"+ii).html("&nbsp;"); jQuery("#trpgID"+ii).html("<option value=\"\">Getting Blogs.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfBlogs", nt:"TR", u:u, p:p, ii:ii, cBlog:cBlog, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#trpgID"+ii).html(j); else jQuery("#nxsTRMsgDiv"+ii).html(j); jQuery('#tr'+ii+'ldImg').hide(); jQuery('#tr'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_trBlogChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#trInpCst'+ii).show(); } 
      }     
//## LI
      function nxs_liGetPages(ii,force){ var u = jQuery('#liappKey'+ii).val(); var p = jQuery('#liAuthUser'+ii).val(); var cBlog = jQuery('#lipgID'+ii).val(); jQuery("#lipgID"+ii).focus();
            jQuery('#li'+ii+'rfrshImg').hide();  jQuery('#li'+ii+'ldImg').show(); jQuery("#nxsLIMsgDiv"+ii).html("&nbsp;"); jQuery("#lipgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPagesLIV2", nt:"LI", u:u, p:p, ii:ii, cBlog:cBlog, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#lipgID"+ii).html(j); else jQuery("#nxsLIMsgDiv"+ii).html(j); jQuery('#li'+ii+'ldImg').hide(); jQuery('#li'+ii+'rfrshImg').show();
            }, "html")          
      }      
      function nxs_liPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#liInpCst'+ii).show(); } 
      }
      function nxs_li2GetPages(ii,force){ var u = jQuery('#apLIUName'+ii).val(); var p = jQuery('#apLIPass'+ii).val(); jQuery('#nxsLI2InfoDiv'+ii).show(); var pgcID = jQuery('#li2pgID'+ii).val(); var pggID = jQuery('#li2GpgID'+ii).val(); jQuery("#li2pgID"+ii).focus();
            jQuery('#li'+ii+'2rfrshImg').hide();  jQuery('#li'+ii+'2ldImg').show();   jQuery('#li'+ii+'3ldImg').show(); jQuery("#nxsLI2MsgDiv"+ii).html("&nbsp;"); jQuery("#li2pgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPagesNXS", nt:"LI", u:u, p:p, ii:ii, pgcID:pgcID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
               if (j.indexOf('<option')>-1) jQuery("#li2pgID"+ii).html(j); else jQuery("#nxsLI2MsgDiv"+ii).html(j); jQuery('#li'+ii+'2ldImg').hide(); jQuery('#nxsLI2GInfoDiv'+ii).show(); jQuery("#li2GpgID"+ii).html("<option value=\"\">Getting Groups.......</option>");                 
               jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfGroupsNXS", nt:"LI", u:u, p:p, ii:ii, pggID:pggID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                  if (j.indexOf('<option')>-1) jQuery("#li2GpgID"+ii).html(j); else jQuery("#nxsLI2MsgDiv"+ii).html(j);  jQuery('#li'+ii+'3ldImg').hide(); jQuery('#li'+ii+'2rfrshImg').show();
               }, "html")                  
            }, "html")          
      }
      function nxs_li2PageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#li2InpCst'+ii).show(); jQuery("#li2InpCst"+ii).focus(); } 
      }     
      function nxs_li2GPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#li2GInpCst'+ii).show(); jQuery("#li2GInpCst"+ii).focus(); } 
      }      
//## XI
      function nxs_xi2GetPages(ii,force){ var u = jQuery('#apXIUName'+ii).val(); var p = jQuery('#apXIPass'+ii).val(); jQuery('#nxsXI2InfoDiv'+ii).show(); var pgcID = jQuery('#xi2pgID'+ii).val(); var pggID = jQuery('#xi2GpgID'+ii).val(); jQuery("#xi2pgID"+ii).focus();
            jQuery('#xi'+ii+'2rfrshImg').hide();  jQuery('#xi'+ii+'2ldImg').show();   jQuery('#xi'+ii+'3ldImg').show(); jQuery("#nxsxi2MsgDiv"+ii).html("&nbsp;"); jQuery("#xi2pgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getPgsList", nt:"XI", u:u, p:p, ii:ii, pgcID:pgcID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
               if (j.indexOf('<option')>-1) jQuery("#xi2pgID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j); jQuery('#xi'+ii+'2ldImg').hide(); jQuery('#nxsXI2GInfoDiv'+ii).show(); jQuery("#xi2GpgID"+ii).html("<option value=\"\">Getting Groups.......</option>");                 
               jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGrpList", nt:"XI", u:u, p:p, ii:ii, pggID:pggID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                  if (j.indexOf('<option')>-1) jQuery("#xi2GpgID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j); pggID = jQuery('#xi2GpgID'+ii).val();  var pgfID = jQuery('#xi2GfID'+ii).val(); 
                  
                  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGrpForums", nt:"XI", u:u, p:p, ii:ii, pggID:pggID, pgfID:pgfID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                     if (j.indexOf('<option')>-1) jQuery("#xi2GfID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j);  jQuery('#xi'+ii+'3ldImg').hide(); jQuery('#xi'+ii+'2rfrshImg').show();
                  }, "html")
                  
                  
               }, "html")
            }, "html")          
      }       
      function nxs_xi2PageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#xi2InpCst'+ii).show(); jQuery("#xi2InpCst"+ii).focus(); } 
      }     
      function nxs_xi2GPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#xi2GInpCst'+ii).show(); jQuery("#xi2GInpCst"+ii).focus(); } 
            else { jQuery('#xi'+ii+'3ldImg').show(); jQuery('#xi'+ii+'2rfrshImg').hide(); 
            
             var pggID = jQuery('#xi2GpgID'+ii).val();  var pgfID = jQuery('#xi2GfID'+ii).val();  var u = jQuery('#apXIUName'+ii).val(); var p = jQuery('#apXIPass'+ii).val();
                  
                  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGrpForums", nt:"XI", u:u, p:p, ii:ii, pggID:pggID, pgfID:pgfID, force:1, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                     if (j.indexOf('<option')>-1) jQuery("#xi2GfID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j);  jQuery('#xi'+ii+'3ldImg').hide(); jQuery('#xi'+ii+'2rfrshImg').show();
                  }, "html")
                  
            
            }
      }          
      function nxs_xi2GfChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#xi2GfInpCst'+ii).show(); jQuery("#xi2GfInpCst"+ii).focus(); } 
      }      
//## FB      
      function nxs_fbGetPages(ii,force){ var u = jQuery('#fbAuthUser'+ii).val(); var p = jQuery('#fbappKey'+ii).val(); var pgID = jQuery('#fbpgID'+ii).val(); jQuery("#fbpgID"+ii).focus();
            jQuery('#fb'+ii+'rfrshImg').hide();  jQuery('#fb'+ii+'ldImg').show(); jQuery("#nxsFBMsgDiv"+ii).html("&nbsp;"); jQuery("#fbpgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPages", nt:"FB", u:u, p:p, ii:ii, pgID:pgID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#fbpgID"+ii).html(j); else jQuery("#nxsFBMsgDiv"+ii).html(j); jQuery('#fb'+ii+'ldImg').hide(); jQuery('#fb'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_fbPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#fbInpCst'+ii).show(); jQuery("#fbInpCst"+ii).focus(); } 
      }
      function nxs_fb2GetPages(ii,u,p,force){ var pgID = jQuery('#fbpgID'+ii).val(); jQuery("#fbpgID"+ii).focus();
            jQuery('#fb'+ii+'rfrshImg').hide();  jQuery('#fb'+ii+'ldImg').show(); jQuery("#nxsFBMsgDiv"+ii).html("&nbsp;"); jQuery("#fbpgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPagesNX", nt:"FB", u:u, p:p, ii:ii, pgID:pgID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#fbpgID"+ii).html(j); else jQuery("#nxsFBMsgDiv"+ii).html(j); jQuery('#fb'+ii+'ldImg').hide(); jQuery('#fb'+ii+'rfrshImg').show();
            }, "html")          
      }                 
      
//## Common Input to DropDown Function.             
      function nxs_InpToDDChange(tObj) { var sObj = jQuery('#'+tObj.data('tid')); sObj.prepend( jQuery("<option/>", { value: tObj.val(), text: tObj.val() })); tObj.hide(); sObj.prop("selectedIndex", 0).trigger('change'); sObj.show(); }            
      function nxs_InpToDDBlur(tObj) {  var sObj = jQuery('#'+tObj.data('tid')); tObj.hide(); sObj.prop("selectedIndex", 0).trigger('change'); sObj.show(); }
//## / API Specific Functions


/*## Functions ##*/

function nxs_doTabs(){
    jQuery(".nsx_tab_content").hide(); 
    jQuery("ul.nsx_tabs > li:first-child").addClass("active").show(); 
    jQuery(".nsx_tab_container > .nsx_tab_content:first-child").show();

    jQuery("ul.nsx_tabs li").click(function() {
      jQuery(this).parent().children("li").removeClass("active");
      jQuery(this).addClass("active"); 
      jQuery(this).parent().parent().children(".nsx_tab_container").children(".nsx_tab_content").hide(); 
      var activeTab = jQuery(this).find("a").attr("href"); 
      jQuery(activeTab).show(); 
      return false;
    });      
}   
   
function nxs_doTabsInd(iid){   
    //When page loads...
    jQuery(iid+" .nsx_tab_content").hide(); //Hide all content
    jQuery(iid+" ul.nsx_tabs > li:first-child").addClass("active").show(); //Activate first tab
    jQuery(iid+" .nsx_tab_container > .nsx_tab_content:first-child").show(); //Show first tab content

    //On Click Event
    jQuery(iid+" ul.nsx_tabs li").on('click', function() {  
      jQuery(this).parent().children("li").removeClass("active"); //Remove any "active" class
      jQuery(this).addClass("active"); //Add "active" class to selected tab
      jQuery(this).parent().parent().children(".nsx_tab_container").children(".nsx_tab_content").hide(); //Hide all tab content    
      var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
      jQuery(activeTab).show(); //Fade in the active ID content
      return false;
    });
      
}

/* Reset SNAP info in Posts */
function nxs_doResetPostSettings(pid){      
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"delPostSettings", pid: pid, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){ window.location = window.location.href.split("#")[0];}, "html")
}
  
//## What is that?????  
//function nxs_updateGetImgsX(e){ } jQuery(document).on('change', '#content', function( e ) { nxs_updateGetImgsX( e ); });  

function nxs_clPrvImgShow(tIdN){ jQuery("#isAutoImg-"+tIdN).trigger('click'); jQuery("#isAutoImg-"+tIdN).trigger('click');  }    
function nxs_getOriginalWidthOfImg(img_element) { var t = new Image();  t.src = (img_element.getAttribute ? img_element.getAttribute("src") : false) || img_element.src; /* alert(t.src+" | "+t.width); */ return t.width; }        
function nxs_clPrvImg(id, ii){ jQuery("#imgToUse-"+ii).val(jQuery("#"+id+" img").attr('src')).trigger('change'); jQuery(".nxs_prevIDiv"+ii+" .nxs_checkIcon").hide();
      jQuery(".nxs_prevIDiv"+ii).removeClass("nxs_chImg_selDiv"); jQuery(".nxs_prevIDiv"+ii+" img").removeClass("nxs_chImg_selImg"); 
      jQuery("#"+id+" img").addClass("nxs_chImg_selImg"); jQuery("#"+id).addClass("nxs_chImg_selDiv"); jQuery("#"+id+" .nxs_checkIcon").show();
}    
function nxs_updateGetImgs(e){ 
        var textOut=''; var text = '';
        var tId = e.target.id; 
        var tIdN = tId.replace("isAutoImg-", "");
        if ( tinymce.activeEditor ) text = tinymce.activeEditor.getContent();
        if ( text == '' ) text = jQuery('#content').val();                
        
        jQuery('#NS_SNAP_AddPostMetaTags').append('<div id="nxs_tempDivImgs" style="display: none;"></div>'); jQuery('#nxs_tempDivImgs').append(text);
        var textOutA = new Array(); var currSelImg =  jQuery("#imgToUse-"+tIdN).val();
                
        textOutA.push('http://gtln.us/img/nxs/noImgC.png');  
        
        var fImg = jQuery('#set-post-thumbnail > img').attr('src'); if (fImg!='' && fImg!=undefined) { textOutA.push(fImg); if (currSelImg=='') currSelImg = fImg; }        
        var fImg = jQuery('#yapbdiv img').attr('src'); if (fImg!='' && fImg!=undefined) { textOutA.push(fImg); if (currSelImg=='') currSelImg = fImg; }
        
        jQuery('#nxs_tempDivImgs img').each(function(){ var prWidth; prWidth = nxs_getOriginalWidthOfImg(this); if (prWidth!=1) textOutA.push(jQuery(this).attr('src'));  });                
        jQuery('#nxs_tempDivImgs').remove();
        var index;  for (index = 0; index < textOutA.length; ++index) { var isSel = currSelImg == textOutA[index] ? 'nxs_chImg_selImg' : ''; var isSelDisp = currSelImg == textOutA[index] ? 'style="display:block;"' : ''; 
          textOut = textOut + '<div class="nxs_prevIDiv'+tIdN+' nxs_prevImagesDiv" id="nxs_idiv'+tIdN+index+'" onclick="nxs_clPrvImg(\'nxs_idiv'+tIdN+index+'\', \''+tIdN+'\');"><img class="nxs_prevImages '+isSel+'" src="'+textOutA[index]+'"><div '+isSelDisp+' class="nxs_checkIcon"><div class="media-modal-icon"></div></div></div>'; 
        }
        jQuery('#imgPrevList-'+tIdN).html( textOut );
        if (jQuery('#'+tId).is(":checked")) jQuery('#imgPrevList-'+tIdN).hide(); else {  jQuery('#nxs_'+tIdN+'_idivD').hide(); jQuery('#imgPrevList-'+tIdN).show();  }
        
    }
jQuery(document).on('change', '.isAutoURL', function( e ) {    var tId = e.target.id; var tIdN = tId.replace("isAutoURL-", "");
   if (jQuery('#'+tId).is(":checked")) { jQuery('#isAutoURLFld-'+tIdN).hide(); jQuery('#URLToUse-'+tIdN).val(''); } else { jQuery('#isAutoURLFld-'+tIdN).show(); }
});    
jQuery(document).on('change', '.isAutoImg', function( e ) {
   nxs_updateGetImgs( e );
});


function nxs_showHideMetaBoxBlocks(){ 
    jQuery('.nxs_acctcb').each( function( i, e ) { if (jQuery(this).is(":checked")) { jQuery('.nxstbl'+e.id).show();  jQuery('#l'+e.id).find('span').html('[-]'); } else { jQuery('.nxstbl'+e.id).hide();  jQuery('#l'+e.id).find('span').html('[+]'); } });
}
function nxs_hideMetaBoxBlocks(){ 
    jQuery('.nxs_acctcb').each( function( i, e ) { jQuery('.nxstbl'+e.id).hide(); });
}

jQuery(document).on('change', '.nxsEdElem', function( e ) {  var nt = jQuery(e.target).data('nt'); var ii = jQuery(e.target).data('ii'); //var n =  jQuery(e.target).serialaize(); console.log(n);
    jQuery('#li'+ii+'ldImg').show();  var psst = jQuery('#original_post_status').val(); if (psst=='auto-draft') return; var pid = jQuery('#post_ID').val(); 
    var vall = ''; if (jQuery(e.target).is(":checkbox")) { if (jQuery(e.target).is(":checked")) vall = 1; else vall = 0; } else vall = e.target.value;
    jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"svEdFlds", pid:pid, "cID":e.target.id, "cname":e.target.name, "cval":vall, ii:ii, nt:nt, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
       jQuery('#li'+ii+'ldImg').hide();
    }, "html");

});


function nxs_TWCharsLeft(tweet) {
    var url, i, lenUrlArr;
    var virtualTweet = tweet;
    var filler = "01234567890123456789";
    var extractedUrls = twttr.txt.extractUrlsWithIndices(tweet);
    var remaining = 140;
    lenUrlArr = extractedUrls.length;
    if ( lenUrlArr > 0 ) {
        for (var i = 0; i < lenUrlArr; i++) {
            url = extractedUrls[i].url;
            virtualTweet = virtualTweet.replace(url,filler);
        }
    }
    remaining = remaining - virtualTweet.length;
    return remaining;
}
/* ### Save Reposter ## */
function nxs_svRep(id) {  jQuery("#nxsAllAccntsDiv").addClass("loading");  jQuery("#nxs_form_rep").append('<input type="hidden" name="_wpnonce" value="'+jQuery('#nxsSsPageWPN_wpnonce').val()+'" />');
    var serTxt = jQuery("#nxs_form_rep").serialize(); jQuery("#nxsSNAP_rpstrStats").html('Saving.....');  jQuery("#nxsSaveLoadingImg").show(); 
    jQuery.ajax({ type: "POST", url: ajaxurl, data: serTxt, 
      success: function(data){ jQuery("#nxsAllAccntsDiv").removeClass("loading"); if (data=='OK') { jQuery("#doneMsg").show(); jQuery("#nxsSaveLoadingImg").hide(); jQuery("#doneMsg").delay(600).fadeOut(3200); 
           window.location = jQuery(location).attr('href');           
        }
        jQuery("#nxsSaveLoadingImg").hide(); jQuery("#nxsSNAP_rpstrStats").html(data); 
      }
    });

}



function nxs_gpGetCommInfo(nt,ii,objj){ jQuery("#"+nt+ii+"ldImg").show(); jQuery("#"+nt+ii+"rfrshImg").hide();
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"gpGetCommInfo", cid: objj.val(), nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  jQuery("#"+nt+ii+"rfrshImg").show(); jQuery("#"+nt+ii+"ldImg").hide();  jQuery('#gp'+nt+ii+'CommInfo').html(j); }, "html")     
}

function nxs_savePluginSettings(wto){ jQuery(".nxsSvSettingsAjax").show(); 
    jQuery('#svSetRef').val(jQuery("input[name='_wp_http_referer']").val()); jQuery('#svSetNounce').val(jQuery('#nxsSsPageWPN_wpnonce').val()); if (typeof(wto) == 'undefined') wto='';
    var serTxt = jQuery("#nsStFormMisc").serialize();
    jQuery.ajax({ type: "POST", url: ajaxurl, data: serTxt, 
      success: function(data){ jQuery("#nxsAllAccntsDiv").removeClass("loading"); 
      if(typeof(divid)!='undefined') { jQuery('#'+divid).html(data); data = 'OK'; }
      if (data=='OK') { jQuery(".doneMsg").show(); jQuery(".nxsSvSettingsAjax").hide();  jQuery(".doneMsg").delay(600).fadeOut(3200); 
        if (wto!='') { if (wto!='r') window.location = wto; else window.location = jQuery(location).attr('href'); } 
      }}
    });    
}
function nxs_saveAllNetworks(wto){ jQuery('#svSetRef').val(jQuery("input[name='_wp_http_referer']").val()); jQuery('#svSetNounce').val(jQuery('#nxsSsPageWPN_wpnonce').val()); var wto = ''; jQuery("#nxsSaveLoadingImg").show(); if (typeof(wto) == 'undefined') wto='';
    var serTxt = jQuery("#nsStForm").serialize();
    jQuery.ajax({ type: "POST", url: ajaxurl, data: serTxt, 
      success: function(data){ jQuery("#nxsAllAccntsDiv").removeClass("loading"); 
      if(typeof(divid)!='undefined') { jQuery('#'+divid).html(data); data = 'OK'; }
      if (data=='OK') { jQuery("#doneMsg").show(); jQuery("#nxsSaveLoadingImg").hide(); jQuery("#doneMsg").delay(600).fadeOut(3200); 
        if (wto!='') { if (wto!='r') window.location = wto; else window.location = jQuery(location).attr('href'); } 
      }}
    });    
}

function nxs_svSet(nt,ii,isNew,wto,divid) {  jQuery("#nxsAllAccntsDiv").addClass("loading");  if (isNew=='1' && wto=='') wto = 'r';
    //## jQuery clone fix for empty textareas
    (function (original) { jQuery.fn.clone = function () { var result = original.apply(this, arguments),
      my_textareas = this.find('textarea').add(this.filter('textarea')), result_textareas = result.find('textarea').add(result.filter('textarea')), 
      my_selects = this.find('select').add(this.filter('select')), result_selects = result.find('select').add(result.filter('select'));
      for (var i = 0, l = my_textareas.length; i < l; ++i) jQuery(result_textareas[i]).val( jQuery(my_textareas[i]).val() );    
      for (var i = 0, l = my_selects.length; i < l; ++i) for (var j = 0, m = my_selects[i].options.length; j < m; ++j) if (my_selects[i].options[j].selected === true) result_selects[i].options[j].selected = true;    
      return result;
    }; }) (jQuery.fn.clone);    
    
    var elID = ''; if (isNew=='1') elID = 'dom'+nt.toUpperCase()+ii+'Div'; else elID = 'nxsAllAccntsDiv'; jQuery("#nxsSaveLoadingImg"+nt+ii).show(); 
    if (jQuery("#nxsSettingsDiv").length) { jQuery("#nxsAllAccntsDiv").addClass("loading");  
      if (jQuery("#nxsAllAccntsDiv").length) jQuery("#nxsSettingsDiv").appendTo("#nxsAllAccntsDiv"); else  elID = 'nxsSettingsDiv'; 
    }  
    
    jQuery('#svSetRef').val(jQuery("input[name='_wp_http_referer']").val()); jQuery('#svSetNounce').val(jQuery('#nxsSsPageWPN_wpnonce').val());
    var frmTxt = '<div id="nxs_tmpDiv_'+nt+ii+'" style="display:none;"><form id="nxs_tmpFrm_'+nt+ii+'"><input name="action" value="nxs_snap_aj" type="hidden" /><input name="nxsact" value="setNTset" type="hidden" /><input name="nxs_mqTest" value="\'" type="hidden" /><input type="hidden" name="_wp_http_referer" value="'+jQuery("input[name='_wp_http_referer']").val()+'" /><input type="hidden" name="_wpnonce" value="'+jQuery('#nxsSsPageWPN_wpnonce').val()+'" /></form></div>';
    jQuery("body").append(frmTxt); jQuery("#"+elID).clone(true).appendTo("#nxs_tmpFrm_"+nt+ii); var serTxt = jQuery("#nxs_tmpFrm_"+nt+ii).serialize(); jQuery("#nxs_tmpDiv_"+nt+ii).remove();// alert(serTxt);
    jQuery.ajax({ type: "POST", url: ajaxurl, data: serTxt, 
      success: function(data){ jQuery("#nxsAllAccntsDiv").removeClass("loading"); 
      if(typeof(divid)!='undefined') { jQuery('#'+divid).html(data); data = 'OK'; }
      if (data=='OK') { jQuery("#doneMsg"+nt+ii).show(); jQuery("#nxsSaveLoadingImg"+nt+ii).hide(); jQuery("#doneMsg"+nt+ii).delay(600).fadeOut(3200); 
        if (wto!='') { if (wto!='r') window.location = wto; else window.location = jQuery(location).attr('href'); } 
      }}
    });
}

function nxs_setAllXS(obj)  { var nid = obj.data('nid');  var uid = obj.data('uid'); 
     jQuery.post(ajaxurl,{nid:nid, uid:uid, action: 'nxs_snap_aj',"nxsact":"setAllXS", id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){
           jQuery('#'+nid+'BT').html(j);
     }, "html")
}

jQuery(document).ready(function() { 
    //## Add New Network Click
    jQuery('#nxs_snapNewAcc').on('click', function(e /*, params */) {     e.preventDefault();       
         jQuery.pgwModal({ target: '#nxs_spPopupU', title: 'Add New Network', maxWidth: 800, closeOnBackgroundClick : false});              
    });                                     
    //## In-line Add New [Network] Click
    jQuery('.nxs_snapAddNew').bind('click', function(e) { e.preventDefault(); var tk = jQuery(this).data('nt'); var gk = jQuery('#nxs_ntType ul li').index( jQuery('#nxs_ntType ul li input[value="'+tk+'"]').parent().parent()); 
       jQuery('#nxs_ntType').ddslick('select', {index: gk }); jQuery('.clNewNTSets').hide(); jQuery('#do'+tk+'Div').show(); jQuery.pgwModal({ target: '#nxs_spPopupU', title: 'Add New Network', maxWidth: 800, closeOnBackgroundClick : false});     
    });
});


(function($) {
  $(function() {                        
     jQuery('.showLic').on('click', function(e /*, params */) {     e.preventDefault();       
         jQuery.pgwModal({ target: '#nxs_showLicForm', title: 'Enter your activation key', maxWidth: 800, closeOnBackgroundClick : false});              
     });     
     jQuery('#checkAPI2x').bind('click', function(e) { e.preventDefault(); jQuery("#checkAPI2xLoadingImg").show(); doLic(); });                                      
     
     jQuery('#nxs_resetSNAPInfoPosts').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPInfoPosts','','','Please wait....','')  });
     jQuery('#nxs_deleteAllSNAPInfo').bind('click', function(e) { e.preventDefault();  var r = confirm("Are you sure?"); if (r == true) { var mu = jQuery( this ).data('mu'); nxs_doAJXPopup('deleteAllSNAPInfo',mu,'','Please wait....',''); } });
     
     jQuery('#nxs_accsFltToAll').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('accsFltToAll','','','Please wait....','')  });
     
     jQuery('#nxs_resetSNAPQuery').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPQuery','','','Please wait....','')  });
     jQuery('#nxs_resetSNAPCron').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPCron','','','Please wait....','')  });
     jQuery('#nxs_resetSNAPCache').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPCache','','','Please wait....','')  });
     
     jQuery('#nxs_restBackup').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('restBackup','','','Please wait....','')  });
         
     jQuery( "#HGTHT" ).bind( "click", function(e) { e.preventDefault();  alert( jQuery( this ).text() ); });
     
     // New Reposter
     // jQuery('#nxsFltAddButton').bind('click', function(e) { e.preventDefault(); jQuery('#nxs_spFltPopup').bPopup({ modalClose: false, appendTo: '#wpbody-content', opacity: 0.6, follow: [false, false], position: [65, 50]}); });
     
     jQuery('#nxsFltAddButton').bind('click', function(e) { e.preventDefault(); jQuery.pgwModal({ target: '#nxs_spFltPopupU', title: 'Add New Reposter', maxWidth: 800, closeOnBackgroundClick : false}); });
     
     /* // Will move it here later for better compatibility
     jQuery('.button-primary[name="update_NS_SNAutoPoster_settings"]').bind('click', function(e) { var str = jQuery('input[name="post_category[]"]').serialize(); jQuery('div.categorydivInd').replaceWith('<input type="hidden" name="pcInd" value="" />'); 
       str = str.replace(/post_category/g, "pk"); jQuery('div.categorydiv').replaceWith('<input type="hidden" name="post_category" value="'+str+'" />');  
     });
     */
  });
})(jQuery);

function nxs_updtRdBtn(idd){
    jQuery('#rbtn'+idd).attr('type', 'checkbox'); 
}

//## Functions

function nxs_expSettings(onlyCh){ var chN = '';
  if (onlyCh==true) { chN = ''; var selected = [];
    jQuery('#nxsAllAccntsDiv input:checked').each(function() {
       selected.push(jQuery(this).data('nxsid'));
    }); chN = selected;
  } jQuery.generateFile({ filename: 'nx-snap-settings.txt', content: jQuery('input#nxsSsPageWPN_wpnonce').val(), chN: chN, script: 'admin-ajax.php'});
}
// AJAX Functions
function nxs_getBoards(u,p,ii,nt){ jQuery("#"+nt+"LoadingImg"+ii).show();
        jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":'getBoards', u:u,p:p,ii:ii,nt:nt, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
          jQuery("select#ap"+nt.toUpperCase()+"Board"+ii).html(j); jQuery("#"+nt+"LoadingImg"+ii).hide();
        }, "html")
}

function nxs_getPNBoards(u,p,ii){ jQuery("#pnLoadingImg"+ii).show();
  jQuery.post(ajaxurl,{u:u,p:p,ii:ii, nxs_mqTest:"'", action: 'getBoards', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    jQuery("select#apPNBoard"+ii).html(j); jQuery("#pnLoadingImg"+ii).hide();
  }, "html")
}
function getGPCats(u,p,ii,c){ jQuery("#gpLoadingImg"+ii).show();
  jQuery.post(ajaxurl,{u:u,p:p,c:c,ii:ii, nxs_mqTest:"'", action: 'getGPCats', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    jQuery("select#apGPCCats"+ii).html(j); jQuery("#gpLoadingImg"+ii).hide();
  }, "html")
}
function getWLBoards(u,p,ii){ jQuery("#wlLoadingImg"+ii).show();
  jQuery.post(ajaxurl,{u:u,p:p,ii:ii, nxs_mqTest:"'", action: 'getWLBoards', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    jQuery("select#apWLBoard"+ii).html(j); jQuery("#wlLoadingImg"+ii).hide();
  }, "html")
}
function nxs_getBrdsOrCats(u,p,ty,ii,fName){ jQuery("#"+ty+"LoadingImg"+ii).show();
  jQuery.post(ajaxurl,{u:u,p:p,ii:ii,ty:ty, nxs_mqTest:"'", action: 'nxs_getBrdsOrCats', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    jQuery("select#"+fName+ii).html(j); jQuery("#"+ty+"LoadingImg"+ii).hide();
  }, "html")
}


function nxs_setRpstAll(t,ed,ii){ jQuery("#nxsLoadingImg"+t+ii).show(); var lpid = jQuery('#'+t+ii+'SetLPID').val();
  jQuery.post(ajaxurl,{t:t,ed:ed,ii:ii, nxs_mqTest:"'", action: 'SetRpstAll', id: 0, lpid:lpid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    alert('OK. Done.'); jQuery("#nxsLoadingImg"+t+ii).hide();
  }, "html")
}

function nxs_fillTime(dd){ var d=new Date(dd); jQuery('#nxs_aa').val(d.getFullYear()); jQuery('#nxs_mm').val(d.getMonth()+1); jQuery('#nxs_jj').val(d.getDate()); jQuery('#nxs_hh').val(d.getHours()); jQuery('#nxs_mn').val(d.getMinutes()); }
function nxs_makeTimeTxt(){ var m=new Array();m[0]="January";m[1]="February";m[2]="March";m[3]="April";m[4]="May";m[5]="June";m[6]="July";m[7]="August";m[8]="September";m[9]="October";m[10]="November";m[11]="December";  
    return m[jQuery('#nxs_mm').val()-1]+', '+jQuery('#nxs_jj').val()+' '+jQuery('#nxs_aa').val()+' '+jQuery('#nxs_hh').val()+':'+jQuery('#nxs_mn').val()+':00'; 
}
function nxs_makeTimeTxt2(tid){ var tmTxt = nxs_makeTimeTxt(); var d=new Date(tmTxt); tm = (d.getTime() / 1000); 
  var tmOut = ''; var tmOutText = ''; var exTm = jQuery('#'+tid+'timeToRun').val(); var dMode = jQuery('#'+tid+'timeToRunWM').val(); 
  if (!!exTm) {          
    var my_date_format = function(d){ var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; var date = d.getDate() + " " + month[d.getMonth()] + ", " + d.getFullYear();
      var time = d.toLocaleTimeString().toLowerCase().replace(/([\d]+:[\d]+):[\d]+(\s\w+)/g, "$1$2"); return (date + " " + time);  
    }; tmArr = exTm.split('|');    
    if (dMode!='A' && tm!='') tmArr[dMode] = tm.toString(); else tmArr.push(tm.toString());      
    tmArr.sort(function (a,b){if (a==b) return 0; if (a=='i' || parseInt(a)<parseInt(b)) return -1; else return 1;}); var isImm = false;   
    //## Remove Allready passed dates
    var dC=new Date(); var curDt = (dC.getTime() / 1000);
    if(tmArr[0]=='i' || (parseInt(tmArr[0])<parseInt(curDt))) isImm = true; tmArr = tmArr.filter(function(dt) { return dt > curDt; });
    if (isImm) tmArr.unshift('i');
    for (var i = 0, len = tmArr.length; i < len; i++) { 
      if (tmArr[i].trim()=='i') tm = 'Immediately'; else { tm = new Date(tmArr[i] * 1000); tm = my_date_format(tm); } 
      tmOutText = tmOutText + ' | ' + tm + '&nbsp;<a href="#" data-n="'+i+'" onclick="jQuery(\'#'+tid+'timeToRunWM\').val(\''+i+'\'); jQuery(\'#nxs_timeID\').val(\''+tid+'\'); jQuery(\'#nxs_timeID_ED\').val(0); var dd = jQuery(\'#'+tid+'timeToRun\').val(); if (!!dd) { dd = dd.split(\'|\');  dd = dd['+i+']; if (dd==\'i\') {var dC=new Date(); var dd = (dC.getTime() / 1000); }   nxs_fillTime(dd*1000); } jQuery.pgwModal({ target: \'#showSetTimeInt\', title: \'Post\', maxWidth: 800, closeOnBackgroundClick : true});  return false;">[Change]</a>';
    } tmOut = tmArr.join('|');
  } else { tmOutText = tmTxt; tmOut = tm; }
  jQuery('#'+tid+'timeToRun').val(tmOut); return tmOutText;
}

function nxs_showPopUpInfo(pid, e, info){ var exTxt = jQuery('div#'+pid).data('text'); if (typeof(exTxt)=='undefined') exTxt = '';
    if (typeof(info)!='undefined') jQuery('div#'+pid).html(exTxt+info);  if (!jQuery('div#'+pid).is(":visible")) jQuery('div#'+pid).show().css('top', e.pageY+5).css('left', e.pageX+25).appendTo('body'); 
}
function nxs_hidePopUpInfo(pid){ jQuery('div#'+pid).hide(); }

function showPopShAtt(imid, e){ if (!jQuery('div#popShAtt'+imid).is(":visible")) jQuery('div#popShAtt'+imid).show().css('top', e.pageY+5).css('left', e.pageX+25).appendTo('body'); }
function hidePopShAtt(imid){ jQuery('div#popShAtt'+imid).hide(); }
function doSwitchShAtt(att, idNum){
  if (att==1) { if (jQuery('#apFBAttch'+idNum).is(":checked")) {jQuery('#apFBAttchShare'+idNum).prop('checked', false);}} else {if( jQuery('#apFBAttchShare'+idNum).is(":checked")) jQuery('#apFBAttch'+idNum).prop('checked', false);}
}      
      
function doShowHideAltFormat(){ if (jQuery('#NS_SNAutoPosterAttachPost').is(':checked')) { 
  jQuery('#altFormat').css('margin-left', '20px'); jQuery('#altFormatText').html('Post Announce Text:'); } else {jQuery('#altFormat').css('margin-left', '0px'); jQuery('#altFormatText').html('Post Text Format:');}
}
function nxs_doShowWarning(blID, num, bl, ii){ var idnum = bl+ii; 
  if (blID.is(':checked')) { var cnf =  confirm("You have active filters. You have "+num+" categories or tags selected. \n\r This will reset all filters. \n\r Would you like to continue?");   
  if (cnf==true) { if (jQuery('#catSelA'+idnum).length) jQuery('#catSelA'+idnum).prop('checked', true); else {
      jQuery('#nsStForm').append('<input type="hidden" id="catSelA'+idnum+'" name="'+bl.toLowerCase()+'['+ii+'][catSel]" value="X" />');
  } } else { blID.prop('checked', false); }
}}
function nxs_ShowNTSetsPP(bl,ii){
  //[Future] Use to show settings in popup (maybe)
}


function doShowHideBlocks(blID){ /* alert('#do'+blID+'Div'); */ if (jQuery('#apDo'+blID).is(':checked')) jQuery('#do'+blID+'Div').show(); else jQuery('#do'+blID+'Div').hide();}
function doShowHideBlocks1(blID, shhd){ if (shhd==1) jQuery('#do'+blID+'Div').show(); else jQuery('#do'+blID+'Div').hide();}            
function doShowHideBlocks2(blID){ if (jQuery('#apDoS'+blID).val()=='0') { jQuery('#do'+blID+'Div').show(); jQuery('#do'+blID+'A').text('[Hide Settings]'); jQuery('#apDoS'+blID).val('1'); } 
  else { jQuery('#do'+blID+'Div').hide(); jQuery('#do'+blID+'A').text('[Show Settings]'); jQuery('#apDoS'+blID).val('0'); }
}

function doGetHideNTBlock(bl,ii){ if (jQuery('#apDoS'+bl+ii).length<1 || jQuery('#apDoS'+bl+ii).val()=='0') { 
    if (jQuery('#do'+bl+ii+'Div').length<1) {  jQuery("#"+bl+ii+"LoadingImg").show();
      jQuery.post(ajaxurl,{nxsact:'getNTset',nt:bl,ii:ii,action:'nxs_snap_aj', _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
        //## check is filters were reset
        //var filtersReset = jQuery('#catSelA'+bl+ii).length && jQuery('#catSelA'+bl+ii).val() == 'X'; if (filtersReset) jQuery('#catSelA'+bl+ii).remove();
        //## Show data
        jQuery('#nxsNTSetDiv'+bl+ii).html(j); nxs_doTabsInd('#nxsNTSetDiv'+bl+ii); nxs_V4_filter_mainJS(jQuery);
        jQuery("#"+bl+ii+"LoadingImg").hide(); jQuery('#do'+bl+ii+'Div').show(); jQuery('#do'+bl+ii+'AG').text('[Hide Settings]'); jQuery('#apDoS'+bl+ii).val('1');        
        if (jQuery('#rbtn'+bl.toLowerCase()+ii).attr('type') != 'checkbox') { jQuery('#rbtn'+bl.toLowerCase()+ii).attr('type', 'checkbox'); 
          //jQuery('#rbtn'+bl.toLowerCase()+ii).iCheck('update'); 
          jQuery('#rbtn'+bl.toLowerCase()+ii).iCheck('destroy'); jQuery('#rbtn'+bl.toLowerCase()+ii).iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'});}         
        // if (filtersReset) jQuery('#catSelA'+bl+ii).prop('checked', true);
        //## selectize filter controls
        //jQuery(".nxsSelIt").selectize( { create: true,  persist: false, plugins:  ['remove_button'] } );
      }, "html")
    } else { jQuery('#do'+bl+ii+'Div').detach().appendTo('#nxsNTSetDiv'+bl+ii); jQuery('#do'+bl+ii+'Div').show(); jQuery('#do'+bl+ii+'AG').text('[Hide Settings]'); jQuery('#apDoS'+bl+ii).val('1'); }
  } else { jQuery('#do'+bl+ii+'Div').hide(); jQuery('#do'+bl+ii+'AG').text('[Show Settings]'); jQuery('#apDoS'+bl+ii).val('0'); }
}

function nxs_showHideBlock(iid, iclass){jQuery('.'+iclass).hide(); jQuery('#'+iid).show();}
            
function doShowFillBlock(blIDTo, blIDFrm){ jQuery('#'+blIDTo).html(jQuery('#do'+blIDFrm+'Div').html());}
function doCleanFillBlock(blIDFrm){ jQuery('#do'+blIDFrm+'Div').html('');}
            
function doShowFillBlockX(blIDFrm){ jQuery('.clNewNTSets').hide(); jQuery('#do'+blIDFrm+'Div').show(); }

function doDuplAcct(nt, blID, blName){ var data = { action:'nxs_snap_aj', nxsact: 'nsDupl', id: 0, nt: nt, id: blID, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; 
  jQuery.post(ajaxurl, data, function(response) { window.location = window.location.href.split("#")[0]; });
}             

function doDelAcct(nt, blID, blName){  var answer = confirm("Remove "+blName+" account?");
  if (answer){ var data = {action: 'nxs_snap_aj',"nxsact":"delNTAcc", nt: nt, id: blID, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};     
    jQuery.post(ajaxurl, data, function(response) {  
        if (jQuery('#dom'+nt.toUpperCase()+blID+'Div').length){ jQuery('#dom'+nt.toUpperCase()+blID+'Div').hide(); var num = jQuery('#nxsNumOfAcc_'+nt).html(); num = num-1; jQuery('#nxsNumOfAcc_'+nt).html(num); }
          else  window.location = window.location.href.split("#")[0];  
    });
  }           
}             

function callAjSNAP(data, label) { 
  var style = "position: fixed; display: none; z-index: 1000; top: 50%; left: 50%; background-color: #E8E8E8; border: 1px solid #555; padding: 15px; width: 350px; min-height: 80px; margin-left: -175px; margin-top: -40px; text-align: center; vertical-align: middle;";
  jQuery('body').append("<div id='test_results' style='" + style + "'></div>");
  jQuery('#test_results').html("<p>Sending update to "+label+"</p>" + "<p><img src='http://gtln.us/img/misc/ajax-loader-med.gif' /></p>");
  jQuery('#test_results').show();            
  jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
    jQuery('#test_results').html('<p> ' + response + '</p>' +'<input type="button" class="button" name="results_ok_button" id="results_ok_button" value="OK" />');
    jQuery('#results_ok_button').click(remove_results);
  });            
}
function remove_results() { jQuery("#results_ok_button").unbind("click");jQuery("#test_results").remove();
  if (typeof document.body.style.maxHeight == "undefined") { jQuery("body","html").css({height: "auto", width: "auto"}); jQuery("html").css("overflow","");}
  document.onkeydown = "";document.onkeyup = "";  return false;
}

function nxs_showHideFrmtInfo(hid){
  if(!jQuery('#'+hid+'Hint').is(':visible')) nxs_showFrmtInfo(hid); else {jQuery('#'+hid+'Hint').hide(); jQuery('#'+hid+'HintInfo').html('Show format info');}
}
function nxs_showFrmtInfo(hid){
  jQuery('#'+hid+'Hint').show(); jQuery('#'+hid+'HintInfo').html('Hide format info'); 
}
function nxs_clLog(){
  jQuery.post(ajaxurl,{action: 'nxs_clLgo', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery("#nxslogDiv").html('');
  }, "html")
}
function nxs_rfLog(){ var prm = new Array(0,0,0,0,0); if (jQuery('#nxs_shLogSE').prop( "checked" )) prm[0] = 1;
  if (jQuery('#nxs_shLogSI').prop( "checked" )) prm[1] = 1;  if (jQuery('#nxs_shLogCE').prop( "checked" )) prm[2] = 1; 
  if (jQuery('#nxs_shLogCI').prop( "checked" )) prm[3] = 1; if (jQuery('#nxs_shLogSY').prop( "checked" )) prm[4] = 1;
  jQuery.post(ajaxurl,{action: 'nxs_rfLgo', id: 0, 'prm[]':prm, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery("#nxslogDiv").html(j);
  }, "html")
}
function nxs_prxTest(){  jQuery('#nxs_pchAjax').show();
  jQuery.post(ajaxurl,{action: 'nxs_prxTest', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery('#nxs_pchAjax').hide(); jQuery("#prxList").html(j);  
  }, "html")
}
function nxs_prxGet(){  jQuery('#nxs_pchAjax').show();
  jQuery.post(ajaxurl,{action: 'nxs_prxGet', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery('#nxs_pchAjax').hide(); jQuery("#prxList").html(j);  
  }, "html")
}
function nxs_TRSetEnable(ptype, ii){
  if (ptype=='I'){ jQuery('#apTRMsgTFrmt'+ii).attr('disabled', 'disabled'); jQuery('#apTRDefImg'+ii).removeAttr('disabled'); } 
    else { jQuery('#apTRDefImg'+ii).attr('disabled', 'disabled');  jQuery('#apTRMsgTFrmt'+ii).removeAttr('disabled'); }                
}
function nxsTRURLVal(ii){ var val = jQuery('#apTRURL'+ii).val(); var srch = val.toLowerCase().indexOf('http://www.tumblr.com/blog/');
  if (srch>-1) { jQuery('#apTRURL'+ii).css({"background-color":"#FFC0C0"}); jQuery('#apTRURLerr'+ii).html('<br/>Incorrect URL: Please note that URL of your Tumblr Blog should be your public URL. (i.e. like http://nextscripts.tumblr.com/, not http://www.tumblr.com/blog/nextscripts'); } else { jQuery('#apTRURL'+ii).css({"background-color":"#ffffff"}); jQuery('#apTRURLerr'+ii).text(''); }            
}

function nxs_hideTip(id){  
  jQuery.post(ajaxurl,{action: 'nxs_hideTip', id: id, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
     jQuery('#'+id).hide(); 
  }, "html")
}

function nxs_actDeActTurnOff(objId){ if (jQuery('#'+objId).val()!='1') { jQuery('#'+objId+'xd').show(); jQuery('#rpstPostWhenFinished').show(); jQuery('#'+objId+'nxsRUQ').hide(); } 
  else { jQuery('#'+objId+'xd').hide(); jQuery('#rpstPostWhenFinished').hide(); jQuery('#'+objId+'nxsRUQ').show(); } 
}



//## Export File
(function(jQuery){ jQuery.generateFile = function(options){ options = options || {};
        if(!options.script || !options.filename || !options.content){
            throw new Error("Please enter all the required config options!");
        }
        var iframe = jQuery('<iframe>',{ width:1, height:1, frameborder:0, css:{ display:'none' } }).appendTo('body');
        var addInfo = ''; if (options.chN != '' ) addInfo = '<input type="hidden" name="chN" value="'+options.chN+'" />';
        var formHTML = '<form action="" method="post"><input type="hidden" name="filename" /><input type="hidden" name="_wpnonce" /><input type="hidden" name="action" value="nxs_getExpSettings" />'+addInfo+'</form>';
        setTimeout(function(){
            var body = (iframe.prop('contentDocument') !== undefined) ? iframe.prop('contentDocument').body : iframe.prop('document').body;    // IE
            body = jQuery(body); body.html(formHTML); var form = body.find('form');
            form.attr('action',options.script);
            form.find('input[name=filename]').val(options.filename);            
            form.find('input[name=_wpnonce]').val(options.content);
            form.submit();
        },50);
    };
})(jQuery);

jQuery(document).ready( function($) {         /*
    wptuts_open_pointer(0);
    function wptuts_open_pointer(i) {
        pointer = wptutsPointer.pointers[i];
        options = $.extend( pointer.options, {
            close: function() {
                $.post( ajaxurl, {
                    pointer: pointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });
 
        $(pointer.target).pointer( options ).pointer('open');
    } */
});

//########## FILTERS #############
function nxs_V4_filter_mainJS($){
    var selectized_terms;
    var selectized_metas;
    
    $.datepicker.regional['en'] = {
        closeText: 'Done',
        prevText: 'Prev',
        nextText: 'Next',
        currentText: 'Today',
        monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
        weekHeader: 'Wk',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['en']);
    
    
    jQuery('.datepicker').datepicker();
    
    
    
    // jQuery(".nxsSelIt").not(".selectized").selectize( { create: true,  persist: false, plugins:  ['remove_button'] } );
    
    //jQuery('select[class="nxsSelIt"]:not(".selectized")').selectize( { create: true,  persist: false, plugins:  ['remove_button'] } );
    
    
    jQuery('select[class="nxsSelItAjxAdd"]:not(".tokenize")').tokenize({ datas: "json.php", displayDropdownOnFocus: true });
    jQuery('select[class="nxsSelItAjx"]:not(".tokenize")').tokenize({ datas: "json.php", displayDropdownOnFocus: true, newElements:false });
    jQuery('select[class="nxsSelIt"]:not(".tokenize")').tokenize({displayDropdownOnFocus: true, newElements:false });
    /*
    jQuery('select[class="nxs_term_names"]:not(".selectized")').selectize( { create: true,  persist: false, valueField: 'value', labelField: 'title', sortField:  'title',  plugins:  ['remove_button'] } );
    jQuery('select[class="nxs_term_names"]:not(".selectized")').selectize( { create: true,  persist: false, valueField: 'value', labelField: 'title', sortField:  'title',  plugins:  ['remove_button'] } );
    jQuery('select[class="nxs_tax_names"]:not(".selectized")').selectize( { valueField: 'value', labelField: 'title', sortField:  'title', plugins:  ['remove_button'],
      onChange: function( item ) { console.log( "===" ); console.log( this.$input[0].id ); console.log( "=X="); filter_select( item, '#'+this.$input[0].id.replace("tax_names", "term_names") ); }
    });
    
    //console.log( jQuery('select[class^="nxs_tax_names"]').length );
    
    jQuery('select[class^="nxs_tax_names"]:not(".selinitialized")').each( function( i, e ) {  
        if (jQuery(e).prop('tagName')=='SELECT' && jQuery(e)[0].selectize.items.length) { jQuery(e).addClass('selinitialized'); //  console.log( "==**" ); console.log( jQuery(e).prop('id') ); console.log( "##==" ); 
           var termID = e.id.replace("tax_names", "term_names");   filter_select( jQuery(e)[0].selectize.items[0],  '#'+termID, true );
        }         
     });
    
      */
    
    //## Change values when taxonimy changed
    jQuery( '.nxs_tax_names' ).on( 'change', function() { var tgtID =  jQuery(this).attr('id').replace('_tax_','_term_'); jQuery('#'+tgtID).attr('data-type', jQuery(this).val()); jQuery('#'+tgtID).val(''); jQuery('#'+tgtID).tokenize().remap(); } );  
    //## Add new meta set
    jQuery( '.nxs_add_meta_compare' ).on( 'click', function(e) { e.preventDefault(); 
       
       var ii = jQuery( this ).attr( "data-ii" ); var nt = jQuery( this ).attr( "data-nt" ); jQuery('#nxs_meta_namesCond'+nt+ii).show(); 
       
       var count_compares = +jQuery( '#nxs_count_meta_'+nt+ii ).val( function( ii, val ) { return ( + val + 1 ); } ).val(); 
       
       var clone = jQuery('#nxs_meta_namesDiv'+nt+ii).clone(); 
              
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_meta_key").attr('name', 'nxs_meta_key'+'_'+count_compares); else jQuery(clone).find("#nxs"+nt+ii+"_meta_key").attr('name', nt+'['+ii+'][nxs_meta_key'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_meta_key").attr('id', 'nxs'+nt+ii+'_meta_key'+'_'+count_compares);
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_meta_operator").attr('name', 'nxs_meta_operator'+'_'+count_compares); else jQuery(clone).find("#nxs"+nt+ii+"_meta_operator").attr('name', nt+'['+ii+'][nxs_meta_operator'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_meta_operator").attr('id', 'nxs'+nt+ii+'_meta_operator'+'_'+count_compares);
       
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_meta_value").attr('name', 'nxs_meta_value'+'_'+count_compares+'[]'); else jQuery(clone).find("#nxs"+nt+ii+"_meta_value").attr('name', nt+'['+ii+'][nxs_meta_value'+'_'+count_compares+'][]');     
       jQuery(clone).find("#nxs"+nt+ii+"_meta_value").attr('id', 'nxs'+nt+ii+'_meta_value'+'_'+count_compares);    // tg[0][nxs_meta_names][]
       
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_meta_relation").attr('name', 'nxs_meta_relation'+'_'+count_compares); else jQuery(clone).find("#nxs"+nt+ii+"_meta_relation").attr('name', nt+'['+ii+'][nxs_meta_relation'+'_'+count_compares+']');
       jQuery(clone).find("#nxs"+nt+ii+"_meta_relation").attr('id', 'nxs'+nt+ii+'_meta_relation'+'_'+count_compares);       
              
       jQuery(clone).find("#nxs_meta_namesCond").remove();
       
       jQuery(clone).find("#nxs"+nt+ii+"_meta_names"+'_'+count_compares).find('option').remove().end();       
       clone.appendTo('#nxs_meta_namesTopDiv'+nt+ii);// jQuery('#nxs_meta_namesCond'+nt+ii).hide();        
       
       jQuery(clone).find(".nxs_metas_leftPanel").attr('style','display:inline-block;');       
       
        jQuery( '.nxs_remove_meta_compare' ).on( 'click', function() {
            jQuery(this).parent().parent().remove();
        } );
        
       return false;
        
    } );
    
    
    jQuery( '.nxs_remove_term_compare' ).on( 'click', function() {
        jQuery(this).parent().parent().remove();
    } );
    jQuery( '.nxs_remove_meta_compare' ).on( 'click', function() {
        jQuery(this).parent().parent().remove();
    } );
    
    jQuery( '.nxs_remove_date_period' ).on( 'click', function(e) { e.preventDefault();
        jQuery(this).parent().parent().remove();
    } );
    jQuery( '.nxs_remove_abs_period' ).on( 'click', function(e) { e.preventDefault();
        jQuery(this).parent().parent().remove();
    } );
    
    
    
    //## Filters - Taxonomies : Add More Button 
    jQuery( '.nxs_add_term_compare' ).on( 'click', function(e) { e.preventDefault(); var ii = jQuery( this ).attr( "data-ii" ); var nt = jQuery( this ).attr( "data-nt" );  
       var count_compares = +jQuery( '#nxs_count_term_'+nt+ii ).val( function( ii, val ) { return ( + val + 1 ); } ).val();    jQuery('#nxs_term_namesCond'+nt+ii).show(); 
       
       var clone = jQuery('#nxs_term_namesDiv'+nt+ii).clone();              
              
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_tax_names").attr('name', 'nxs_tax_names'+'_'+count_compares); else jQuery(clone).find("#nxs"+nt+ii+"_tax_names").attr('name', nt+'['+ii+'][nxs_tax_names'+'_'+count_compares+']');  
       jQuery(clone).find("#nxs"+nt+ii+"_tax_names").attr('id', 'nxs'+nt+ii+'_tax_names'+'_'+count_compares);
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_term_operator").attr('name', 'nxs_term_operator'+'_'+count_compares); else jQuery(clone).find("#nxs"+nt+ii+"_term_operator").attr('name', nt+'['+ii+'][nxs_term_operator'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_operator").attr('id', 'nxs'+nt+ii+'_term_operator'+'_'+count_compares);
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_term_children").attr('name', 'nxs_term_children'+'_'+count_compares); else jQuery(clone).find("#nxs"+nt+ii+"_term_children").attr('name', nt+'['+ii+'][nxs_term_children'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_children").attr('id', 'nxs'+nt+ii+'_term_children'+'_'+count_compares);       
       jQuery(clone).find(".Tokenize").remove();              
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_term_names").attr('name', 'nxs_term_names'+'_'+count_compares+'[]'); else jQuery(clone).find("#nxs"+nt+ii+"_term_names").attr('name', nt+'['+ii+'][nxs_term_names'+'_'+count_compares+'][]');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_names").attr('id', 'nxs'+nt+ii+'_term_names'+'_'+count_compares);    // tg[0][nxs_term_names][]
       
       if (nt=='') jQuery(clone).find("#nxs"+nt+ii+"_term_relation").attr('name', 'nxs_term_relation'+'_'+count_compares);  else jQuery(clone).find("#nxs"+nt+ii+"_term_relation").attr('name', nt+'['+ii+'][nxs_term_relation'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_relation").attr('id', 'nxs'+nt+ii+'_term_relation'+'_'+count_compares);      
       
       jQuery(clone).find("#nxs_term_namesCond").remove(); 
       
       jQuery(clone).find("#nxs"+nt+ii+"_term_names"+'_'+count_compares).find('option').remove().end();       
       clone.appendTo('#nxs_term_namesTopDiv'+nt+ii); jQuery('#nxs_term_namesCond'+nt+ii).show();        
       
       jQuery("#nxs"+nt+ii+"_term_names"+'_'+count_compares).tokenize({ datas: "json.php", displayDropdownOnFocus: true });
       
       jQuery(clone).find(".nxs_terms_leftPanel").attr('style','display:block;');       
       jQuery( '.nxs_tax_names' ).on( 'change', function() { var tgtID =  jQuery(this).attr('id').replace('_tax_','_term_'); jQuery('#'+tgtID).attr('data-type', jQuery(this).val()); jQuery('#'+tgtID).val(''); jQuery('#'+tgtID).tokenize().remap(); } );  
        
       return false;
        
    } );
    
    function nxs_runme_onClone(e) {
       jQuery( '.nxs_remove_date_period' ).on( 'click', function(e) { e.preventDefault(); jQuery(this).parent().parent().remove(); } );        
       jQuery( '.nxs_remove_abs_period' ).on( 'click', function(e) { e.preventDefault(); jQuery(this).parent().parent().remove(); } );        
       jQuery('body').on('focus',".datepicker", function(){ if( jQuery(this).hasClass('hasDatepicker') === false )  { jQuery(this).datepicker();} });
        
    }
    //## Add Date Period
    jQuery( '.nxs_add_date_period' ).on( 'click', function(e) { e.preventDefault(); var ii = jQuery( this ).attr( "data-ii" ); var nt = jQuery( this ).attr( "data-nt" );              
       var count_periods = +jQuery( '#nxsDivWrap #nxs_count_date_periods' ).val( function( i, val ) { return ( + val + 1 ); } ).val();
       var rel = 'nxs_date_period_' + count_periods;
       var clone = jQuery('#nxs_timeframe_Div'+nt+ii).clone(); clone.show();
       
       jQuery(clone).find("#nxs_starting_period").val('');       
       jQuery(clone).find("#nxs_starting_period").removeClass('hasDatepicker');
       jQuery(clone).find("#nxs_starting_period").attr('name', 'nxs_starting_period'+'_'+count_periods);     
       jQuery(clone).find("#nxs_starting_period").attr('id', 'nxs_starting_period'+'_'+count_periods);
                     
       jQuery(clone).find("#nxs_end_period").val('');
       jQuery(clone).find("#nxs_end_period").removeClass('hasDatepicker');
       jQuery(clone).find("#nxs_end_period").attr('name', 'nxs_end_period'+'_'+count_periods);     
       jQuery(clone).find("#nxs_end_period").attr('id', 'nxs_end_period'+'_'+count_periods);
              
       jQuery(clone).find("#nxs_timeframe_type").attr('name', 'nxs_timeframe_type'+'_'+count_periods);     
       jQuery(clone).find("#nxs_timeframe_type").attr('id', 'nxs_timeframe_type'+'_'+count_periods);
       
       jQuery(clone).find("#nxs_remove_date_period").attr('style', 'display:inline-block;');     
       jQuery(clone).find("#nxs_remove_date_period").attr('name', 'nxs_remove_date_period'+'_'+count_periods);     
       jQuery(clone).find("#nxs_remove_date_period").attr('id', 'nxs_remove_date_period'+'_'+count_periods);
              
       clone.appendTo('#nxs_timeframeTopDiv'+nt+ii); //jQuery('#nxs_term_namesCond'+nt+ii).show();        
       nxs_runme_onClone(e);       
       return false;        
    } );
    //## Add ABS Date Period
    jQuery( '#nxs_add_abs_period' ).on( 'click', function(e) {  e.preventDefault(); var ii = jQuery( this ).attr( "data-ii" ); var nt = jQuery( this ).attr( "data-nt" );                            
        var count_periods = +jQuery( '#nxsDivWrap #nxs_count_abs_periods' ).val( function( i, val ) { return ( + val + 1 ); } ).val();
        var rel = 'nxs_abs_period_' + count_periods;
        var clone = jQuery('#nxs_abstime_Div'+nt+ii).clone(); clone.show();
        
       jQuery(clone).find("#nxs_start_abs_period").val('');       
       jQuery(clone).find("#nxs_start_abs_period").attr('name', 'nxs_start_abs_period'+'_'+count_periods);     
       jQuery(clone).find("#nxs_start_abs_period").attr('id', 'nxs_start_abs_period'+'_'+count_periods);
                     
       jQuery(clone).find("#nxs_end_abs_period").val('');
       jQuery(clone).find("#nxs_end_abs_period").attr('name', 'nxs_end_abs_period'+'_'+count_periods);     
       jQuery(clone).find("#nxs_end_abs_period").attr('id', 'nxs_end_abs_period'+'_'+count_periods);
              
       jQuery(clone).find("#nxs_start_abs_period_type").attr('name', 'nxs_start_abs_period_type'+'_'+count_periods);     
       jQuery(clone).find("#nxs_start_abs_period_type").attr('id', 'nxs_start_abs_period_type'+'_'+count_periods);
       
       jQuery(clone).find("#nxs_end_abs_period_type").attr('name', 'nxs_end_abs_period_type'+'_'+count_periods);     
       jQuery(clone).find("#nxs_end_abs_period_type").attr('id', 'nxs_end_abs_period_type'+'_'+count_periods);
       
       jQuery(clone).find("#nxs_remove_abs_period").attr('style', 'display:inline-block;');     
       jQuery(clone).find("#nxs_remove_abs_period").attr('name', 'nxs_remove_abs_period'+'_'+count_periods);     
       jQuery(clone).find("#nxs_remove_abs_period").attr('id', 'nxs_remove_abs_period'+'_'+count_periods);
        
        clone.appendTo('#nxs_abstimeTopDiv'+nt+ii); //jQuery('#nxs_term_namesCond'+nt+ii).show();        
        nxs_runme_onClone(e);       
        return false;              
    } )
    
    jQuery( '.nxs_remove_meta_compare' ).on( 'click', function() {
        remove_compare( this );
    } );
        
    
    
    function remove_compare( button ) {
        var selector = '';
        switch( jQuery( button ).attr( 'class' ) ) {
            case 'nxs_remove_meta_compare': 
                selector = 'nxs_count_meta_compares';
                break;
            case 'nxs_remove_term_compare': 
                selector = 'nxs_count_term_compares';
                break;
            case 'nxs_remove_date_period': 
                selector = 'nxs_count_date_periods';
                break;
            case 'nxs_remove_date_abs_period': 
                selector = 'nxs_count_date_abs_periods';
                break;
        }
        
        jQuery( '#' + selector ).val( function( i, val ) {
            return ( + val - 1 );
        } )
        
        var rel = jQuery( button ).closest( 'div' ).attr( 'rel' );
        
        set_attributes_next_elements( button );    
        
        jQuery( button ).closest( 'div' ).prevAll( 'div[rel=' + rel + ']' ).remove();
        jQuery( button ).closest( 'div' ).prev( 'hr' ).remove();
        jQuery( button ).closest( 'div' ).remove();
        
        return false;
    }
    
    function get_select( name, count, nt, ii ){ //alert(name); console.log( JSON.stringify( selectized_terms ) );
        if( name == 'nxs_term_names' )
            var select = selectized_terms;
        else if( name == 'nxs_meta_value' )
            var select = selectized_metas;
        // else  var select = $( 'select#' + name ).tokenize().toArray();
        
        var current_name = name + '_' + count; 
        
        if(typeof(nt)!='undefined') current_nameX = nt+'['+ii+']['+name + '_' + count+']'; else current_nameX = current_name;
         
         //console.log( JSON.stringify( jQuery(this).prop('tagName') ) );
         
        var multiple     = ( jQuery( '#' + name ).attr( 'multiple' ) ? 'multiple="multiple"' : '' );
        
        var output = '<select name="' + ( multiple != '' ? current_nameX + '[]' : current_nameX ) + '" id="' + current_name + '" placeholder="Select from the list..." ' + multiple + '>';
        output += '<option value="">Select from the list...</option>';
        jQuery.each( select, function( i, e ) {
            output += '<option value="' + e.value + '">' + e.title + '</option>';
        } );     
        output += '</select>';
        
        return output;
    }
    
    function set_attributes_next_elements( elem ) {
        var selector = jQuery( elem ).hasClass( 'nxs_remove_date_period' ) ? 'div [rel^=nxs_date_period]' : 'div [rel]';
        
        jQuery( elem ).closest( 'div' ).nextAll( selector ).each( function( i, e ) {            
            set_attrribute( jQuery( e ), 'rel' );
            set_attrribute( jQuery( e ).children( 'input, select' ), 'id' );
            set_attrribute( jQuery( e ).children( 'input, select' ), 'name' );                                    
        } );    
    }
    
    function set_attrribute ( element, attribute ) {
        if( element.length > 0 ) {
            jQuery( element ).attr( attribute, function( index, value ) {
                var multiple = ~value.indexOf( '[]' ) ? '[]' : '';
                var position = value.lastIndexOf( '_' ) + 1;
                return ( value.slice( 0, position ) + ( parseInt( value.slice( position ) ) - 1 ) + multiple );
            } );
        }
    }
    
    function filter_select ( item, selector, saveItems ) {  
        var terms      = jQuery( selector )[0].selectize;  
        var items      = terms.items;  
        var newOptions = []; 
        
        if(typeof(selectized_terms)=='undefined' && typeof(jQuery( selector )[0])!='undefined') selectized_terms = jQuery( selector )[0].selectize.options; 
        if( ~selector.indexOf( '_term_names' ) ) var options = selectized_terms; else if( ~selector.indexOf( '_meta_value' ) ) var options = selectized_metas; else var options = terms.options;
        jQuery.each( options, function() { 
            var delimiter = this.value.indexOf( '||' );
            if( this.value.slice( 0, delimiter ) == item ) {
                newOptions.push( {  
                    value: this.value,
                    title: this.title
                } );
            }
        } );
        
        terms.clearOptions();
        terms.addOption( newOptions );
        if( saveItems ) {
            terms.addItems( items );
        }
    }
}

jQuery( document ).ready( function( $ ) {
    if (typeof($.datepicker)!='undefined') nxs_V4_filter_mainJS($);
} );
//########## END FILTERS #########

 /*! iCheck v1.0.2 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
(function(f){function A(a,b,d){var c=a[0],g=/er/.test(d)?_indeterminate:/bl/.test(d)?n:k,e=d==_update?{checked:c[k],disabled:c[n],indeterminate:"true"==a.attr(_indeterminate)||"false"==a.attr(_determinate)}:c[g];if(/^(ch|di|in)/.test(d)&&!e)x(a,g);else if(/^(un|en|de)/.test(d)&&e)q(a,g);else if(d==_update)for(var f in e)e[f]?x(a,f,!0):q(a,f,!0);else if(!b||"toggle"==d){if(!b)a[_callback]("ifClicked");e?c[_type]!==r&&q(a,g):x(a,g)}}function x(a,b,d){var c=a[0],g=a.parent(),e=b==k,u=b==_indeterminate,
v=b==n,s=u?_determinate:e?y:"enabled",F=l(a,s+t(c[_type])),B=l(a,b+t(c[_type]));if(!0!==c[b]){if(!d&&b==k&&c[_type]==r&&c.name){var w=a.closest("form"),p='input[name="'+c.name+'"]',p=w.length?w.find(p):f(p);p.each(function(){this!==c&&f(this).data(m)&&q(f(this),b)})}u?(c[b]=!0,c[k]&&q(a,k,"force")):(d||(c[b]=!0),e&&c[_indeterminate]&&q(a,_indeterminate,!1));D(a,e,b,d)}c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"default");g[_add](B||l(a,b)||"");g.attr("role")&&!u&&g.attr("aria-"+(v?n:k),"true");
g[_remove](F||l(a,s)||"")}function q(a,b,d){var c=a[0],g=a.parent(),e=b==k,f=b==_indeterminate,m=b==n,s=f?_determinate:e?y:"enabled",q=l(a,s+t(c[_type])),r=l(a,b+t(c[_type]));if(!1!==c[b]){if(f||!d||"force"==d)c[b]=!1;D(a,e,s,d)}!c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"pointer");g[_remove](r||l(a,b)||"");g.attr("role")&&!f&&g.attr("aria-"+(m?n:k),"false");g[_add](q||l(a,s)||"")}function E(a,b){if(a.data(m)){a.parent().html(a.attr("style",a.data(m).s||""));if(b)a[_callback](b);a.off(".i").unwrap();
f(_label+'[for="'+a[0].id+'"]').add(a.closest(_label)).off(".i")}}function l(a,b,f){if(a.data(m))return a.data(m).o[b+(f?"":"Class")]}function t(a){return a.charAt(0).toUpperCase()+a.slice(1)}function D(a,b,f,c){if(!c){if(b)a[_callback]("ifToggled");a[_callback]("ifChanged")[_callback]("if"+t(f))}}var m="iCheck",C=m+"-helper",r="radio",k="checked",y="un"+k,n="disabled";_determinate="determinate";_indeterminate="in"+_determinate;_update="update";_type="type";_click="click";_touch="touchbegin.i touchend.i";
_add="addClass";_remove="removeClass";_callback="trigger";_label="label";_cursor="cursor";_mobile=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);f.fn[m]=function(a,b){var d='input[type="checkbox"], input[type="'+r+'"]',c=f(),g=function(a){a.each(function(){var a=f(this);c=a.is(d)?c.add(a):c.add(a.find(d))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(a))return a=a.toLowerCase(),g(this),c.each(function(){var c=
f(this);"destroy"==a?E(c,"ifDestroyed"):A(c,!0,a);f.isFunction(b)&&b()});if("object"!=typeof a&&a)return this;var e=f.extend({checkedClass:k,disabledClass:n,indeterminateClass:_indeterminate,labelHover:!0},a),l=e.handle,v=e.hoverClass||"hover",s=e.focusClass||"focus",t=e.activeClass||"active",B=!!e.labelHover,w=e.labelHoverClass||"hover",p=(""+e.increaseArea).replace("%","")|0;if("checkbox"==l||l==r)d='input[type="'+l+'"]';-50>p&&(p=-50);g(this);return c.each(function(){var a=f(this);E(a);var c=this,
b=c.id,g=-p+"%",d=100+2*p+"%",d={position:"absolute",top:g,left:g,display:"block",width:d,height:d,margin:0,padding:0,background:"#fff",border:0,opacity:0},g=_mobile?{position:"absolute",visibility:"hidden"}:p?d:{position:"absolute",opacity:0},l="checkbox"==c[_type]?e.checkboxClass||"icheckbox":e.radioClass||"i"+r,z=f(_label+'[for="'+b+'"]').add(a.closest(_label)),u=!!e.aria,y=m+"-"+Math.random().toString(36).substr(2,6),h='<div class="'+l+'" '+(u?'role="'+c[_type]+'" ':"");u&&z.each(function(){h+=
'aria-labelledby="';this.id?h+=this.id:(this.id=y,h+=y);h+='"'});h=a.wrap(h+"/>")[_callback]("ifCreated").parent().append(e.insert);d=f('<ins class="'+C+'"/>').css(d).appendTo(h);a.data(m,{o:e,s:a.attr("style")}).css(g);e.inheritClass&&h[_add](c.className||"");e.inheritID&&b&&h.attr("id",m+"-"+b);"static"==h.css("position")&&h.css("position","relative");A(a,!0,_update);if(z.length)z.on(_click+".i mouseover.i mouseout.i "+_touch,function(b){var d=b[_type],e=f(this);if(!c[n]){if(d==_click){if(f(b.target).is("a"))return;
A(a,!1,!0)}else B&&(/ut|nd/.test(d)?(h[_remove](v),e[_remove](w)):(h[_add](v),e[_add](w)));if(_mobile)b.stopPropagation();else return!1}});a.on(_click+".i focus.i blur.i keyup.i keydown.i keypress.i",function(b){var d=b[_type];b=b.keyCode;if(d==_click)return!1;if("keydown"==d&&32==b)return c[_type]==r&&c[k]||(c[k]?q(a,k):x(a,k)),!1;if("keyup"==d&&c[_type]==r)!c[k]&&x(a,k);else if(/us|ur/.test(d))h["blur"==d?_remove:_add](s)});d.on(_click+" mousedown mouseup mouseover mouseout "+_touch,function(b){var d=
b[_type],e=/wn|up/.test(d)?t:v;if(!c[n]){if(d==_click)A(a,!1,!0);else{if(/wn|er|in/.test(d))h[_add](e);else h[_remove](e+" "+t);if(z.length&&B&&e==v)z[/ut|nd/.test(d)?_remove:_add](w)}if(_mobile)b.stopPropagation();else return!1}})})}})(window.jQuery||window.Zepto);

//Title: Custom DropDown plugin by PC
//Documentation: http://designwithpc.com/Plugins/ddslick
//Author: PC 
//Website: http://designwithpc.com
//Twitter: http://twitter.com/chaudharyp

(function ($) {

    $.fn.ddslick = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exists.');
        }
    };

    var methods = {},

    //Set defauls for the control
    defaults = {
        data: [],
        keepJSONItemsOnTop: false,
        width: 260,
        height: null,
        background: "#eee",
        selectText: "",
        defaultSelectedIndex: null,
        truncateDescription: true,
        imagePosition: "left",
        showSelectedHTML: true,
        clickOffToClose: true,
        onSelected: function () { }
    },

    ddSelectHtml = '<div class="dd-select"><input class="dd-selected-value" type="hidden" /><a class="dd-selected"></a><span class="dd-pointer dd-pointer-down"></span></div>',
    ddOptionsHtml = '<ul class="dd-options"></ul>',

    //CSS for ddSlick
    ddslickCSS = '<style id="css-ddslick" type="text/css">' +
                '.dd-select{ border-radius:2px; border:solid 1px #ccc; position:relative; cursor:pointer;}' +
                '.dd-desc { color:#aaa; display:block; overflow: hidden; font-weight:normal; line-height: 1.4em; }' +
                '.dd-selected{ overflow:hidden; display:block; padding:10px; font-weight:bold;}' +
                '.dd-pointer{ width:0; height:0; position:absolute; right:10px; top:50%; margin-top:-3px;}' +
                '.dd-pointer-down{ border:solid 5px transparent; border-top:solid 5px #000; }' +
                '.dd-pointer-up{border:solid 5px transparent !important; border-bottom:solid 5px #000 !important; margin-top:-8px;}' +
                '.dd-options{ border:solid 1px #ccc; border-top:none; list-style:none; box-shadow:0px 1px 5px #ddd; display:none; position:absolute; z-index:2000; margin:0; padding:0;background:#fff; overflow:auto;}' +
                '.dd-option, .dd-title{ padding:10px; display:block; border-bottom:solid 1px #ddd; overflow:hidden; text-decoration:none; color:#333; cursor:pointer;-webkit-transition: all 0.25s ease-in-out; -moz-transition: all 0.25s ease-in-out;-o-transition: all 0.25s ease-in-out;-ms-transition: all 0.25s ease-in-out; }' +
                '.dd-option { padding-left:20px} .dd-options > li:last-child > .dd-option{ border-bottom:none;}' +
                '.dd-option:hover{ background:#f3f3f3; color:#000;}' +
                '.dd-selected-description-truncated { text-overflow: ellipsis; white-space:nowrap; }' +
                '.dd-option-selected { background:#f6f6f6; }' +
                '.dd-option-image, .dd-selected-image { vertical-align:middle; float:left; margin-right:5px; max-width:64px;}' +
                '.dd-image-right { float:right; margin-right:15px; margin-left:5px;}' +
                '.dd-container{ position:relative;}? .dd-selected-text { font-weight:bold}?</style>';

    //CSS styles are only added once.
    if ($('#css-ddslick').length <= 0) {
        $(ddslickCSS).appendTo('head');
    }

    //Public methods 
    methods.init = function (options) {
        //Preserve the original defaults by passing an empty object as the target
        var options = $.extend({}, defaults, options);

        //Apply on all selected elements
        return this.each(function () {
            var obj = $(this),
                data = obj.data('ddslick');
            //If the plugin has not been initialized yet
            if (!data) {

                var ddSelect = [], ddJson = options.data;

                //Get data from HTML select options
                obj.find('option').each(function () {
                    var $this = $(this), thisData = $this.data(); 
                    ddSelect.push({
                        text: $.trim($this.text()),
                        value: $this.val(),
                        selected: $this.is(':selected'),
                        title: thisData.title,
                        description: thisData.description,
                        imageSrc: thisData.imagesrc //keep it lowercase for HTML5 data-attributes
                    });
                });

                //Update Plugin data merging both HTML select data and JSON data for the dropdown
                if (options.keepJSONItemsOnTop)
                    $.merge(options.data, ddSelect);
                else options.data = $.merge(ddSelect, options.data);

                //Replace HTML select with empty placeholder, keep the original
                var original = obj, placeholder = $('<div id="' + obj.attr('id') + '"></div>');
                obj.replaceWith(placeholder);
                obj = placeholder;

                //Add classes and append ddSelectHtml & ddOptionsHtml to the container
                obj.addClass('dd-container').append(ddSelectHtml).append(ddOptionsHtml);

                //Get newly created ddOptions and ddSelect to manipulate
                var ddSelect = obj.find('.dd-select'),
                    ddOptions = obj.find('.dd-options');

                //Set widths
                ddOptions.css({ width: options.width });
                ddSelect.css({ width: options.width, background: options.background });
                obj.css({ width: options.width });

                //Set height
                if (options.height != null)
                    ddOptions.css({ height: options.height, overflow: 'auto' });

                //Add ddOptions to the container. Replace with template engine later.
                
                
                
                $.each(options.data, function (index, item) {
                    if (item.selected) options.defaultSelectedIndex = index;
                    
                    if (item.title) ddOptions.append('<li class="dd-title"><strong>'+item.text+'</strong><ul>'); else 
                    
                    ddOptions.append('<li>' +
                        '<a class="dd-option">' +
                            (item.value ? ' <input class="dd-option-value" type="hidden" value="' + item.value + '" />' : '') +
                            (item.imageSrc ? ' <img class="dd-option-image' + (options.imagePosition == "right" ? ' dd-image-right' : '') + '" src="' + item.imageSrc + '" />' : '') +
                            (item.text ? ' <label class="dd-option-text">' + item.text + '</label>' : '') +
                            (item.description ? ' <small class="dd-option-description dd-desc">' + item.description + '</small>' : '') +
                        '</a>' +
                    '</li>');
                    
                    if (item.title) ddOptions.append('</ul></li>');
                });
                
                

                //Save plugin data.
                var pluginData = {
                    settings: options,
                    original: original,
                    selectedIndex: -1,
                    selectedItem: null,
                    selectedData: null
                }
                obj.data('ddslick', pluginData);

                //Check if needs to show the select text, otherwise show selected or default selection
                if (options.selectText.length > 0 && options.defaultSelectedIndex == null) {
                    obj.find('.dd-selected').html(options.selectText);
                }
                else {
                    var index = (options.defaultSelectedIndex != null && options.defaultSelectedIndex >= 0 && options.defaultSelectedIndex < options.data.length)
                                ? options.defaultSelectedIndex
                                : 0;
                    selectIndex(obj, index);
                }

                //EVENTS
                //Displaying options
                obj.find('.dd-select').on('click.ddslick', function () {
                    open(obj);
                });

                //Selecting an option
                obj.find('.dd-option').on('click.ddslick', function () {
                    selectIndex(obj, $(this).closest('li').index());
                });

                //Click anywhere to close
                if (options.clickOffToClose) {
                    ddOptions.addClass('dd-click-off-close');
                    obj.on('click.ddslick', function (e) { e.stopPropagation(); });
                    $('body').on('click', function () {
                        $('.dd-click-off-close').slideUp(50).siblings('.dd-select').find('.dd-pointer').removeClass('dd-pointer-up');
                    });
                }
            }
        });
    };

    //Public method to select an option by its index
    methods.select = function (options) {
        return this.each(function () {
            if (options.index)
                selectIndex($(this), options.index);
        });
    }

    //Public method to open drop down
    methods.open = function () {
        return this.each(function () {
            var $this = $(this),
                pluginData = $this.data('ddslick');

            //Check if plugin is initialized
            if (pluginData)
                open($this);
        });
    };

    //Public method to close drop down
    methods.close = function () {
        return this.each(function () {
            var $this = $(this),
                pluginData = $this.data('ddslick');

            //Check if plugin is initialized
            if (pluginData)
                close($this);
        });
    };

    //Public method to destroy. Unbind all events and restore the original Html select/options
    methods.destroy = function () {
        return this.each(function () {
            var $this = $(this),
                pluginData = $this.data('ddslick');

            //Check if already destroyed
            if (pluginData) {
                var originalElement = pluginData.original;
                $this.removeData('ddslick').unbind('.ddslick').replaceWith(originalElement);
            }
        });
    }

    //Private: Select index
    function selectIndex(obj, index) {

        //Get plugin data
        var pluginData = obj.data('ddslick');

        //Get required elements
        var ddSelected = obj.find('.dd-selected'),
            ddSelectedValue = ddSelected.siblings('.dd-selected-value'),
            ddOptions = obj.find('.dd-options'),
            ddPointer = ddSelected.siblings('.dd-pointer'),
            selectedOption = obj.find('.dd-option').eq(index),
            selectedLiItem = selectedOption.closest('li'),
            settings = pluginData.settings,
            selectedData = pluginData.settings.data[index];

        //Highlight selected option
        obj.find('.dd-option').removeClass('dd-option-selected');
        selectedOption.addClass('dd-option-selected');

        //Update or Set plugin data with new selection
        pluginData.selectedIndex = index;
        pluginData.selectedItem = selectedLiItem;
        pluginData.selectedData = selectedData;        

        //If set to display to full html, add html
        if (settings.showSelectedHTML) {
            ddSelected.html(
                    (selectedData.imageSrc ? '<img class="dd-selected-image' + (settings.imagePosition == "right" ? ' dd-image-right' : '') + '" src="' + selectedData.imageSrc + '" />' : '') +
                    (selectedData.text ? '<label class="dd-selected-text">' + selectedData.text + '</label>' : '') +
                    (selectedData.description ? '<small class="dd-selected-description dd-desc' + (settings.truncateDescription ? ' dd-selected-description-truncated' : '') + '" >' + selectedData.description + '</small>' : '')
                );

        }
        //Else only display text as selection
        else ddSelected.html(selectedData.text);

        //Updating selected option value
        ddSelectedValue.val(selectedData.value);

        //BONUS! Update the original element attribute with the new selection
        pluginData.original.val(selectedData.value);
        obj.data('ddslick', pluginData);

        //Close options on selection
        close(obj);

        //Adjust appearence for selected option
        adjustSelectedHeight(obj);

        //Callback function on selection
        if (typeof settings.onSelected == 'function') {
            settings.onSelected.call(this, pluginData);
        }
    }

    //Private: Close the drop down options
    function open(obj) {

        var $this = obj.find('.dd-select'),
            ddOptions = $this.siblings('.dd-options'),
            ddPointer = $this.find('.dd-pointer'),
            wasOpen = ddOptions.is(':visible');

        //Close all open options (multiple plugins) on the page
        $('.dd-click-off-close').not(ddOptions).slideUp(50);
        $('.dd-pointer').removeClass('dd-pointer-up');

        if (wasOpen) {
            ddOptions.slideUp('fast');
            ddPointer.removeClass('dd-pointer-up');
        }
        else {
            ddOptions.slideDown('fast');
            ddPointer.addClass('dd-pointer-up');
        }

        //Fix text height (i.e. display title in center), if there is no description
        adjustOptionsHeight(obj);
    }

    //Private: Close the drop down options
    function close(obj) {
        //Close drop down and adjust pointer direction
        obj.find('.dd-options').slideUp(50);
        obj.find('.dd-pointer').removeClass('dd-pointer-up').removeClass('dd-pointer-up');
    }

    //Private: Adjust appearence for selected option (move title to middle), when no desripction
    function adjustSelectedHeight(obj) {

        //Get height of dd-selected
        var lSHeight = obj.find('.dd-select').css('height');

        //Check if there is selected description
        var descriptionSelected = obj.find('.dd-selected-description');
        var imgSelected = obj.find('.dd-selected-image');
        if (descriptionSelected.length <= 0 && imgSelected.length > 0) {
           // obj.find('.dd-selected-text').css('lineHeight', lSHeight);
        }
    }

    //Private: Adjust appearence for drop down options (move title to middle), when no desripction
    function adjustOptionsHeight(obj) {
        obj.find('.dd-option').each(function () {
            var $this = $(this);
            var lOHeight = $this.css('height');
            var descriptionOption = $this.find('.dd-option-description');
            var imgOption = obj.find('.dd-option-image');
            if (descriptionOption.length <= 0 && imgOption.length > 0) {
                $this.find('.dd-option-text').css('lineHeight', lOHeight);
            }
        });
    }

})(jQuery);