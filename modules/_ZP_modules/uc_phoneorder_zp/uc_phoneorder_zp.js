// -*- js-var: set_line_item, basePath, getTax; -*-
// $Id$

var page;
var details;
var methods;
var label;


$(document).ready(

	function() { 

		$("#phoneorder_zp-pane").addClass('collapsed');

	}
);
 







function setPhoneorder_ZpCallbacks(funclabel){
	label = funclabel;



// my changes


// by client bia Internet


$("#edit-panes-phoneorder-zp-phoneorder-zp-0-wrapper---").click(function(){
//$("#edit-panes-phoneorder-zp-calculate-0-wrapper").click(function(){

  remove_line_item("phoneorder_zp");
  //set_line_item("phoneorder_zp", label, 0, 1);
  //alert('removed');
  return true;
});




// by operator by phone

$("div[id*=edit-panes-phoneorder-zp-phoneorder-zp-]").click(function(){
//$("#edit-panes-phoneorder-zp-phoneorder-zp-1-wrapper").click(function(){
//$("#edit-panes-phoneorder-zp-calculate-1-wrapper").click(function(){

 details = new Object();
 
 //details["phoneorder_zp"] = $("div[id*=).form-radio").val();
 details["phoneorder_zp"] = $(this).find(".form-radio").val();
 //details["phoneorder_zp"] = $("#edit-panes-phoneorder-zp-phoneorder-zp-1-wrapper .form-radio").val();
 //details["phoneorder_zp"] = 1;
 //details["phoneorder_zp"] = $("input[@name*=phoneorder_zp]").val();
 
 
 price = Number(details["phoneorder_zp"]).toFixed(2);
 if(price > 0)
  set_line_item("phoneorder_zp", label, price, 1);
 else
  remove_line_item("phoneorder_zp");
 
 //set_line_item("phoneorder_zp", label, Number(details["phoneorder_zp"]).toFixed(2), 1);
 
 //alert(details["phoneorder_zp"]);
 return true;
});






/*

  $("#phoneorder_zp-button").click(function(){
    Phoneorder_ZpCallback();
  });
  
  
  $("input[@name*=phoneorder_zp]").change(function(){
    //alert('here');
    Phoneorder_ZpCallback();
  });
  */
  
}

function Phoneorder_ZpCallback(){

	
	
// my changes 

  return;
	
/*	
  details = new Object();
	details["phoneorder_zp"] = $("input[@name*=phoneorder_zp]").val();
	//details["phoneorder"] = $("input[@name*=phoneorder_zp]").val();
	//alert( Number(details["phoneorder_zp"]).toFixed(2));
  set_line_item("phoneorder_zp", label, Number(details["phoneorder_zp"]).toFixed(2), 1);
  //set_line_item("phoneorder", label, Number(details["phoneorder"]).toFixed(2), 1);
  
  return false;
  
*/


}
