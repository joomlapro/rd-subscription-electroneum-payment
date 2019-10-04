// JavaScript Document

function openpay_direct()
{
	 code = "";
	 paymentid = jQuery("#paymentid").val();
	 outlet = jQuery("#outlet").val();
	 total = jQuery("#etn").val();
	 code = "etn-it-"+outlet+"/"+paymentid+'/'+total;
	 window.open("https://link.electroneum.com/jWEpM5HcxP?vendor=" + code);
}

jQuery(document).ready(function(e) {
    jQuery("#qrimage").click(function(){
			openpay_direct();
			
			
	 });
	  setTimeout(function(){
		 check_rdsubs_electroneumresponse(1);
	 }, 5000);
});



function check_rdsubs_electroneumresponse()
{

	sitebaseurl = jQuery("#sitebaseurl").val();


	
	 jQuery.ajax({
		type: "POST",
		cache: false,
		dataType: "json",
		url: sitebaseurl,
		data : jQuery("#electronium_payform :input").serialize(),
	 }).done(
	 function (data, textStatus){
		 
		 if(data.success == 0)
		 {
			 errorstring = '<div class="uk-alert" uk-alert><a href="" class="uk-alert-close uk-close"></a><p>'+data.message+'</p></div>';
			 
			  setTimeout(function(){
				check_rdsubs_electroneumresponse(1);
			 }, 5000);
			 
		 }
		 if(data.success == 1)
		 {
			
			 jQuery("#firstdiv").hide();
			 jQuery("#thirddiv").show();
			 jQuery("#checkmark_svg").show();
			 
			 setTimeout(function(){
				 jQuery("#ajaxtask").val('thankspage');
				 showthankspage();
			 }, 3000);
		 }
		 
	 });

	
}
function showthankspage()
{
	total = jQuery("#etn").val();
	returnurl = jQuery("#returnurl").val()+total;
	alert(returnurl);
	location.href = returnurl;
}