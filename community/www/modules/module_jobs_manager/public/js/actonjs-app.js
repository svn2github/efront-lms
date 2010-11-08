/**
 * Actonjs Library v. 1.0
 * Author: Actonbit.gr < sales@actonbit.gr >
 * Copyrights: Actonbit.gr < sales@actonbit.gr >
 */

Actonjs.namespace("Actonjs.app");

Actonjs.app = function () {

 var server_name;
 var form, form_container, form_mask;
 var sliding;
 var i18n_thanks, i18n_please_wait, i18n_submit, i18n_all_fields, i18n_valid_email;

 function reset() {
  server_name = window.location.protocol + '//' + window.location.hostname;
  sliding = false;
  initElements();
  addEvents();
 }

 function initElements() {
  form = jQuery('.form');
  form_container = jQuery('.form-container');
  form_mask = jQuery('.form-mask');
 }

 function addEvents() {
  jQuery('#form-trigger-image').click(function (){
   if (!sliding) {
    toggleForm();
   }
  });
 }

 function toggleForm() {
  if (form.css('height')=='0px') {
   sliding = true;
   form.animate({height:'420px'},'slow',function() {jQuery('.form-content').fadeIn('fast',function(){}); sliding = false;});
  }
  else {
   sliding = true;
   jQuery('.form-content').fadeOut('fast',function(){form.animate({height:'0px'},'slow',function() {sliding = false;});});
  }
 }

 function displayJob(id) {
  var id = (id ? id : false);
  if (id>0) {
   var data = {'id':id};
   $.ajax({
      url: 'fetch-job.php',
    type: 'POST',
      dataType: 'json',
      data: data,
      success: function(response) {
     i18n_thanks = response.i18n.thanks;
     i18n_please_wait = response.i18n.please_wait;
     i18n_submit = response.i18n.submit;
     i18n_all_fields = response.i18n.all_fields;
     i18n_valid_email = response.i18n.valid_email;
     if (response.success) {
      populateJob(response.job_data);
     }
     else {
      alert(response.error_msg);
     }
    },
    error: function() {
     alert('Some error occured while trying to get the description of this job, please try again later.');
    }
   });
  }
  else {
   window.location = '';
   hideFormContainer();
  }
 }

 function populateJob(data) {
  resetForm();
  jQuery('#mod_jam_form_job_id').val(data.job_id);
  jQuery('#job_code_form').html(data.apply_for_job+': '+data.title+' ['+data.code+']');
  var html = '';
  html += '<table cellpadding="0" cellspacing="0" style="width:100%;">';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.title_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.code_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.code;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.date_added_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.date_added;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.type_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.type;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.experience_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.experience;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.remuneration_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  if (data.remuneration) {
   html += data.remuneration;
  }
  else {
   html += '-';
  }
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.functions_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.functions;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.desc_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.desc;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.skills_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.skills;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:14px;padding-bottom:2px;padding-top:10px;font-weight:bold;">';
  html += data.company_desc_title;
  html += '		</td>';
  html += '	</tr>';
  html += '	<tr>';
  html += '		<td style="font-size:12px;">';
  html += data.company_desc;
  html += '		</td>';
  html += '	</tr>';
  html += '<table>';
  jQuery('#main_content_div').html(html);
  showFormContainer();
 }

 function resetForm() {
  jQuery('#mod_jam_form_name').val('');
  jQuery('#mod_jam_form_email').val('');
  jQuery('#mod_jam_form_phone').val('');
  jQuery('#mod_jam_form_city').val('');
  jQuery('#mod_jam_form_country').val('');
  jQuery('#mod_jam_form_cover').val('');
  jQuery('#mod_jam_form_file').val('');
 }

 function checkApplication(form) {
  disableBtn('mod_jam_form_btn',i18n_please_wait);
  showFormMask();
  var validation_error = false;
  var invalid_email = false;
  // Validating form
  var name = jQuery.trim(jQuery('#mod_jam_form_name').val());
  var email = jQuery.trim(jQuery('#mod_jam_form_email').val());
  var phone = jQuery.trim(jQuery('#mod_jam_form_phone').val());
  var city = jQuery.trim(jQuery('#mod_jam_form_city').val());
  var country = jQuery.trim(jQuery('#mod_jam_form_country').val());
  var cover = jQuery.trim(jQuery('#mod_jam_form_cover').val());
  var file = jQuery.trim(jQuery('#mod_jam_form_file').val());
  if (!name || !city || !country || !cover || !file) {
   validation_error = true;
  }
  if (!isEmail(email)) {
   invalid_email = true;
  }

  if (validation_error) {
   alert(i18n_all_fields);
   enableBtn('mod_jam_form_btn',i18n_submit);
   hideFormMask();
  }
  else if (invalid_email) {
   alert(i18n_valid_email);
   enableBtn('mod_jam_form_btn',i18n_submit);
   hideFormMask();
  }
  else {
   submitApplication(form);
  }
 }

 function isEmail(val) {var result=false; result=(/^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\.[\_|a-z|0-9|\-]+)*@[a-z|0-9]+([a-z|0-9|\-]+)*(\.[a-z|0-9|\-]+)*(\.[a-z]{2,4})$/).test(val); return result;}

 function submitApplication(form) {
  disableBtn('mod_jam_form_btn',i18n_please_wait);
  showFormMask();
  var iframe = document.createElement("iframe");
     iframe.setAttribute("id","new_application_upload_iframe");
     iframe.setAttribute("name","new_application_upload_iframe");
     iframe.setAttribute("width","0");
     iframe.setAttribute("height","0");
     iframe.setAttribute("border","0");
     iframe.setAttribute("style","width: 0; height: 0; border: none;");

     // Add to document...
     form.parentNode.appendChild(iframe);
     window.frames['new_application_upload_iframe'].name="new_application_upload_iframe";

  var eventHandler = function () {
   jQuery("#new_application_upload_iframe").unbind('load', eventHandler);
   try {
    var response = jQuery('#new_application_upload_iframe').contents().find('body').html();
    console.log(response);
    eval('var response='+response);
    if (response.success) {
     alert(i18n_thanks);
    }
    else {
     alert(response.error);
    }
   }
   catch (e){
    alert('Some error occured while trying to upload this photo, please try again later.');
   }
   enableBtn('mod_jam_form_btn',i18n_submit);
   hideFormMask();
   resetForm();
   toggleForm();
   Actonjs.app.removeUploadFrame('new_application_upload_iframe');
  }

  // Attach event listener
  $('#new_application_upload_iframe').load(eventHandler);

     // Set properties of form...
     form.setAttribute("target","new_application_upload_iframe");
     form.setAttribute("action", "save-application.php");
     form.setAttribute("method","post");
     form.setAttribute("enctype","multipart/form-data");
     form.setAttribute("encoding","multipart/form-data");

     // Submit the form...
     form.submit();
 }

 function removeUploadFrame(id) {
  var el = jQuery('#'+id).get();
  if (el && el.parentNode && el.parentNode.removeChild) {
   el.parentNode.removeChild(el);
  }
 }

 function cancelForm() {
  resetForm();
  toggleForm();
 }

 function showFormContainer() {
  if (form_container.css('display') == 'none') {
   form_container.fadeIn('fast',function (){});
  }
 }

 function hideFormContainer() {
  if (form_container.css('display') == 'inline') {
   form_container.fadeOut('fast',function (){});
  }
 }

 function showFormMask() {
  if (form_mask.css('display') == 'none') {
   form_mask.css('display','inline');
  }
 }

 function hideFormMask() {
  if (form_mask.css('display') == 'inline') {
   form_mask.css('display','none');
  }
 }

 function enableBtn(id, msg) {
  var btn_el = jQuery('#'+id);
  btn_el.val(msg);
  btn_el.attr('disabled', '');
 }

 function disableBtn(id, msg) {
  var btn_el = jQuery('#'+id);
  btn_el.val(msg);
  btn_el.attr('disabled', true);
 }

 return {
  init: function() {
   reset();
  },
  toggleForm: function() {
   toggleForm();
  },
  displayJob: function(id) {
   displayJob(id);
  },
  checkApplication: function(form) {
   checkApplication(form);
  },
  cancelForm: function() {
   cancelForm();
  },
  removeUploadFrame: function(id) {
   removeUploadFrame(id);
  }
 }
}();
$(document).ready(function() {Actonjs.app.init();});
