// -*- js-var: set_line_item, basePath, getTax; -*-
// $Id$

var page;
var details;
var methods;
var label;

function setDonationCallbacks(funclabel){
	label = funclabel;



// my changes


$("#edit-panes-donation-calculate-0-wrapper").click(function(){

	alert('aaaa');
 details = new Object();
 details["donation"] = $("input[@name*=donation]").val();
 set_line_item("donation", label, Number(details["donation"]).toFixed(2), 1);
 return true;
});



$("#edit-panes-donation-calculate-1-wrapper").click(function(){
alert('ssss');
  remove_line_item("donation");
  return true;
});


















  $("#donation-button").click(function(){
    donationCallback();
  });
  $("input[@name*=donation]").change(function(){
    //alert('here');
    donationCallback();
  });
}

function donationCallback(){

  details = new Object();
	details["donation"] = $("input[@name*=donation]").val();
	//alert( Number(details["donation"]).toFixed(2));
  set_line_item("donation", label, Number(details["donation"]).toFixed(2), 1);
  
  return false;
}
