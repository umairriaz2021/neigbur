/*Global variable*/
var interval='';

$(function () {
	$('#cardNumber').mask('9999 9999 9999 9999');
	$('#cardExpiry').mask('99/99');
	$('#cardCVC').mask('999');

	$("#donation_receipt").click(function () {
		if ($(this).is(":checked")){ $("#charitable-yes").show(); }else{ $("#charitable-yes").hide(); }
	});
});

jQuery(document).on('click', '#buytickets', function (e) {
    e.preventDefault();
    
	var site_url = jQuery('#Site_Url').val();

	$('#modal_loader_text').text('Processing...');
	$('#loadingModal').show();

	if (parseFloat($('#TotalAmount').val()) == 0)
	{
	    
	    
		clearInterval(interval);
		jQuery.ajax({
				url: site_url + '/wp-content/themes/Divi Child/ajax/createtickets.php',
				type: "post",
				data : $("#GetTicketForm").serialize(),
				success: function (result){
					$('#loadingModal').hide();
					console.log(result);
					//alert('Done you are redirecting to thanks you page.');
					//window.location.href= site_url+"/thank-you/";
				}
		});
	}
	else
	{


		jQuery.ajax({
			/* url: site_url + '/wp-content/themes/Divi Child/ajax/createtickets.php', */
			url: site_url + '/bambora/src/Beanstream/pay.php',
			type: "post",
			//async: false,
			data : $("#GetTicketForm").serialize(),
			dataType : 'json',
			success: function (result){

				if(result.success){
					$('#bambora_transaction_id').val(result.data.id);
					clearInterval(interval);
					jQuery.ajax({
							url: site_url + '/wp-content/themes/Divi Child/ajax/createtickets.php',
							type: "post",
							data : $("#GetTicketForm").serialize(),
							success: function (result){
								$('#loadingModal').hide();
								console.log(result);
								//alert('Done you are redirecting to thanks you page.');
								window.location.href= site_url+"/thank-you/";
							}
					});
				}else{
					$('#loadingModal').hide();
					$('#PayMentError').html('<p>'+result.message +'</p>');

				}
			}
		});
	}
});



function getTicket(){
	var site_url = jQuery('#Site_Url').val();
	var datastring = jQuery("#GetTicketForm").serialize();
	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/ajax/gettickets.php',
			type: 'post',
			dataType: 'json',
			data : datastring,
			success: function (result){
			    	console.log(result);
				//console.log(result[0][0]);
			
				//for (var i = 0; i < result.length; i++) {
                 // result[i] ;
             //	jQuery(".thold-div").text(result[i]);
                // }
                 
                  $.each(result, function(index, el) {
        // index is your 0-based array index
        // el is your value

        // for example
        $('.thold-'+index).val(el);
       var a= $('.thold-'+index).val();
       console.log('hold'+a);
       var b= $('.tlimit-'+index).val();
       console.log('remaining'+b);
       var value = b - a;
       console.log('total remaining'+ value);
         $('.left-'+index).val(value);
        console.log("element at " + index + ": " + el); // will alert each value
    });
			}
	});
}


jQuery(document).ready(function(){
setInterval(getTicket,5000);
    if(jQuery('.thold').val() === ""){
        jQuery('.thold').val(0);
    }
       jQuery( ".abc" ).each(function( index ) {
        // index is your 0-based array index
        // el is your value
         var data = jQuery(this).attr('data');
         console.log(data);
         var value =  jQuery(this).val();
         console.log(value);
         var b = jQuery('.thold-'+data).val(value);
         console.log(b);
         var d = jQuery('.thold-'+data).val();
         var c= jQuery('.tlimit-'+data).val();
       console.log('remaining'+c);
       var total = c - d;
        jQuery('.left-'+data).val(total);
    });
	
});

jQuery(document).on("change keyup", ".tqty", function() {
   console.log("changed");   
    getTicket();
    if(jQuery('.sub-promo input').val() !==""){
        applyPromo();
    }
    	/*jQuery('.tkt-info').each(function(index){
                  //do stuff
                  if((jQuery('.tqty-'+index).val()) > (jQuery('.tremaining-'+index).val())) {
                      alert('tickets are not available...');
                      
                  }
                  //else{ ticketpaymentNext(1,2);}
                  console.log('index is '+index);
                });*/
});

jQuery(document).on("change keyup input click", ".tqty", function() {

    var qty = jQuery(this).val();
	//console.log('========');
	//console.log(qty);
	var torderlimit = jQuery(this).parent().find('.torderlimit').val();
	//console.log(torderlimit);
	var tremaining = jQuery(this).parent().find('.tremaining').val();
	//console.log('========');
	if(qty > 0){
    if (parseInt(qty) == parseInt(tremaining)){
		console.log('===yes im in if q>l===');
		jQuery(this).val(tremaining);
		jQuery(this).parent().find('.limitMessage').show().text("Available ticket limit reached");
		jQuery(this).parent().find('.remainingMessage').hide();
		//jQuery(this).parent().find('.limitMessage').show();
		//qty=tremaining;
	}
	}
});



function holdTicket(){
	var site_url = jQuery('#Site_Url').val();
	var datastring = $("#GetTicketForm").serialize();
	$('#loadingModal').show();
	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/ajax/holdtickets.php',
			type: "post",
			data : datastring,
			success: function (result){
				$('#loadingModal').hide();
             
				console.log(result);
		
			   // ticketpaymentNext(1,2);
			    getTicket();
			   //if(result){
				jQuery('.tkt-info').each(function(index){
                  //do stuff
                  console.log('dsedf '+jQuery('.tqty-'+index).val());
                  console.log('rwer '+jQuery('.tremaining-'+index).val());
                 //if(parseInt(jQuery('.tqty-'+index).val()) > 0) {
                /*  if(parseInt(jQuery('.tqty-'+index).val()) > parseInt(jQuery('.tremaining-'+index).val())) {
                      alert(jQuery('.tqty-'+index).val()+' tickets are not available. Please select again');
                       return false;
                  }
                    else if(parseInt(jQuery('.tqty-'+index).val()) <= parseInt(jQuery('.tremaining-'+index).val())){  
                        console.log('tickets available...');  
                   return ticketpaymentNext(1,2);
                  }*/
                   
			//	}
                  console.log('index is '+index);
                });
 
                if (result.indexOf("error") >= 0){
                     console.log('tickets not available...');
                    alert('Your selection quantities have changed due to changes in inventory. Please review prior to proceeding.');
                    return false;
                   
                }
                else{
                    console.log('tickets available...');  
                    return ticketpaymentNext(1,2);
                }

  		   //}
               	//getTicket();
			}
       
	});
}


function holdTicketDelete(){
	var site_url = jQuery('#Site_Url').val();
	var datastring = $("#GetTicketForm").serialize();
	//$('#loadingModal').show();
	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/ajax/holdtickets_delete.php',
			type: "post",
			data : datastring,
			success: function (result){
				//$('#loadingModal').hide();
             
				console.log(result);
			//	ticketpaymentNext(1,2);
				//ticketpaymentBack(2,3);
				//getTicket();
			}
	});
}





function startTimer(dd){
	var timer2 = "4:59";
	interval = setInterval(function() {
	  var timer = timer2.split(':');

	  var minutes = parseInt(timer[0], 10);
	  //var minutes = parseInt(1);    
	  var seconds = parseInt(timer[1], 10);
	  --seconds;
	  minutes = (seconds < 0) ? --minutes : minutes;
	  if (minutes < 0){
			clearInterval(interval);
// 			alert('Your session has timed out and your tickets have been returned. Please try again.');
			
		  //  var newUrl = window.location.href.replace(window.location.search,'');
		  //  $('#sal_val1').attr('data',newUrl + "?single=1");
		   // holdTicketDelete();
		    sessionStorage.setItem('key',new Date().valueOf());
			window.location.href = window.location.href;
			//setTimeout("window.open(self.location, '_self');", 1000);
			//setTimeout("location.reload(true);", 1000);
			
	  }else{
		  seconds = (seconds < 0) ? 59 : seconds;
		  seconds = (seconds < 10) ? '0' + seconds : seconds;
		  /* $('.countdown'+dd).html(minutes + ':' + seconds); */
		  $('.countdown').empty().html(minutes + ':' + seconds);
		  timer2 = minutes + ':' + seconds;
	  }
	}, 1000);
}
if(sessionStorage.getItem('key')){
   var sessionInterval= setInterval(function() {
       if($('.session_expired').length>0){
           	clearInterval(sessionInterval);
             $('.session_expired').css('display','block');
            sessionStorage.removeItem('key')
       }
    }, 500);
    
}

if(sessionStorage.getItem('key')){
    $('.next-btn').click(function(){
        var sessionInterval= setInterval(function() {
       if($('.session_expired').length>0){
           	clearInterval(sessionInterval);
             $('.session_expired').css('display','block');
            sessionStorage.removeItem('key')
       }
    }, 500);

});
}
   
 jQuery(window).bind('beforeunload', function(){
       console.log('refreshed');
       holdTicketDelete();
 });
 
  /*jQuery(window).bind('unload', function(){
       console.log('unload');
       holdTicketDelete();
 });*/
 
function ticketpaymentBack(id1,id2){
	jQuery('#step'+id1).show();
	jQuery('#step'+id2).hide();
	 holdTicketDelete();
	document.body.scrollTop = document.documentElement.scrollTop = 0;
	
}
function ticketpaymentNext(id1,id2){
    var a =jQuery('#step2 .countdown').text();
    console.log("jhk"+a);
	if(id1==1 && a=="05:00") {
	    //clearInterval(interval);
	    startTimer(id1);
	}
	jQuery('#step'+id1).hide();
	jQuery('#step'+id2).show();
	document.body.scrollTop = document.documentElement.scrollTop = 0;
}



jQuery(document).on('keyup', '.tqty', function () {
	if (!jQuery(this).val()){
	   // jQuery(this).val('0');
	    jQuery(this).parent().parent().find('.ttoltxt').val('0.00');
	    jQuery('.tkttotal').empty().append('$0.00');
	     jQuery('.tktFee').empty().append('$0.00');
	      jQuery('.tktTax').empty().append('$0.00');
	       jQuery('.Total').empty().append('$0.00');
	}
});


jQuery(document).on("change", ".tqty", function() {
  var t = jQuery(this).val();
  jQuery(this).val(parseFloat(t,10));
});


//jQuery(document).on('change, input, keyup', '.tqty', function () {
jQuery(document).on('input', '.tqty', function () {
	var qty = jQuery(this).val();
	//console.log('========');
	//console.log(qty);
	var torderlimit = jQuery(this).parent().find('.torderlimit').val();
	//console.log(torderlimit);
	var tremaining = jQuery(this).parent().find('.tremaining').val();
	//console.log('========');
    
   /* if(isNaN(qty)) {
      jQuery(this).val('0');
     }*/
     
     /*if (parseInt(qty) == parseInt(tremaining)){
		console.log('===yes im in if q>l===');
		jQuery(this).val(tremaining);
		jQuery(this).parent().find('.limitMessage').show().text("Available ticket limit reached");
		jQuery(this).parent().find('.remainingMessage').hide();
			jQuery(this).parent().find('.limitMessage').show();
		//qty=tremaining;
	}*/
	
      if(parseInt(tremaining) == parseInt(torderlimit) || parseInt(tremaining) < parseInt(torderlimit)){
	 if (parseInt(qty) > parseInt(tremaining)){
		//console.log('===yes im in if q>l===');
		jQuery(this).val(tremaining);
		jQuery(this).parent().find('.limitMessage').show().text("Available ticket limit reached");
		jQuery(this).parent().find('.remainingMessage').hide();
		qty=tremaining;
	}
	 }
     if(parseInt(qty) > parseInt(torderlimit)){
		//console.log('===yes im in if q>l===');
		jQuery(this).val(torderlimit);
		//jQuery(this).parent().find('.limitMessage').show().text("Maximum order limit is "+torderlimit);
		jQuery(this).parent().find('.limitMessage').show().text("Order limit reached");
		jQuery(this).parent().find('.remainingMessage').hide();
		qty=torderlimit;
	}
	
    if(parseInt(tremaining) == parseInt(torderlimit) || parseInt(tremaining) < parseInt(torderlimit)){
    	 if (parseInt(qty) < parseInt(tremaining)){
    	
    		jQuery(this).parent().find('.limitMessage').hide();
    		jQuery(this).parent().find('.remainingMessage').show();
    	}
    }

    if(parseInt(qty) < parseInt(torderlimit)){ 
        	jQuery(this).parent().find('.limitMessage').hide();
    		jQuery(this).parent().find('.remainingMessage').show();
    }
    
    
    

	var price = jQuery(this).parent().parent().find('.tprice').val();
	var tax = jQuery(this).parent().parent().find('.ttax').val();
	var fee = jQuery(this).parent().parent().find('.tfee').val();
	var code = jQuery(this).parent().parent().find('.tpromoMetric').val();
	var codevalue = jQuery(this).parent().parent().find('.tpromoValue').val();
	var dis = jQuery(this).parent().parent().find('.ttoltxt-discount').val();
	/* console.log(qty);
	console.log(price);
	console.log(tax);
	console.log(fee); */
	if(price!=0){
		var ttot = (parseFloat(price) + parseFloat((tax!=undefined)?tax:0) + parseFloat((fee!=undefined)?fee:0)) * parseFloat(qty);
		console.log(ttot);
		// jQuery(this).parent().parent().find('.ttotal').val(parseFloat(ttot).toFixed(2));
		jQuery(this).parent().parent().find('.ttoltxt').text(parseFloat(ttot).toFixed(2));
	}else{
		// jQuery(this).parent().parent().find('.ttotal').val(0.00);
		jQuery(this).parent().parent().find('.ttoltxt').text('0.00');
	}
	
	//custom
	if (jQuery(this).val() == 0){
	  jQuery(this).parent().parent().find('.ttoltxt').text('0.00');
	 var a = jQuery(this).parent().parent().find('.ttoltxt').text();
     console.log('hello' + a);
     if(a =='NaN'){
	    jQuery(this).parent().parent().find('.ttoltxt').text('0.00');
	    
	}
	}
  //custom
	getfulltot();
});


function applyPromo(){
     jQuery('.ttoltxt').css('text-decoration','none');
     jQuery('.ttoltxt-discount').empty();
     jQuery('.tktDiscount').empty().append('$0.00' );
     jQuery(".ticket-details").each(function(){
    var promo = jQuery(this).find('.tpromoCode').val();
     var promo1 = jQuery(this).parent().parent().find('.tpromoCode').val();
     var metric = jQuery(this).find('.tpromoMetric').val();
     var hvalue = parseFloat(jQuery(this).find('.tpromoValue').val()).toFixed(2);
     //var  a = parseFloat(hvalue).toFixed(2);
     var totalvalue = jQuery(this).find('.sub_total_each').val();
     var value = jQuery('.sub-promo input').val();
     
     
     if (value ==''){  jQuery(".promo-msg").empty().append("Please enter Promo code ");}
        else if( promo1.toLowerCase() !== value.toLowerCase()){
       //console.log('promo and value is:' + promo1  +  value);
        jQuery(".promo-msg").empty().append("Promo code is incorrect" );
        jQuery(".clear-code").css('display','inline-block');

         }
         
        else{ jQuery(".promo-msg").empty();
          //console.log('qweqweqeqweeqwe');
            jQuery(".clear-code").css('display','inline-block');
        }
    
     console.log(value);
     console.log('promo is '+ promo);
     console.log(metric);
     console.log(hvalue);
     console.log('total value is ' + totalvalue);
        
        
        if(promo){
            if(promo.toLowerCase() === value.toLowerCase()){
         jQuery(".promo-msg").empty();
         if(metric =='dollar'){
         console.log('dollar done');
         
         if(parseInt(totalvalue) < parseInt(hvalue)){
             jQuery(this).find('.ttoltxt-discount').empty().append('please add more tickets to get discount');
             jQuery('.tktDiscount').empty().append('$0.00' );
         }
         
        else if(parseInt(totalvalue) > parseInt(hvalue) ){
         var total = (totalvalue - hvalue).toFixed(2);
         console.log('overalltotal ' + total);
         jQuery(this).find('.ttoltxt-discount').empty().append('$'
         + total);
          jQuery(this).find('.ttoltxt-discount').attr("value",hvalue);
         // jQuery(this).find('.ttoltxt-discount').attr("discount",hvalue);
         	jQuery(this).find('.ttoltxt').css('text-decoration','line-through');
         	
         	
         	  jQuery('.tktDiscount').empty().append('$'+ hvalue);
          jQuery('.tktDiscount').attr("value",hvalue);
         }
         
         
        /* if (hvalue){
          jQuery('.tktDiscount').empty().append('$'+ hvalue);
          jQuery('.tktDiscount').attr("value",hvalue);
          	//jQuery('.Total').text('$'+parseFloat(tt).toFixed(2));
          //	jQuery(this).find('.ttoltxt').css('text-decoration','line-through');
         }
         else{
              jQuery('.tktDiscount').empty().append('$0.00' );
         }*/
         
         }
          if(metric =='percentage'){
         console.log('per done');
         var percentage = ( totalvalue * hvalue / 100 ).toFixed(2);
         var total = (totalvalue -  ( totalvalue * hvalue / 100 )).toFixed(2);
         console.log('overalltotal ' + total);
         jQuery(this).find('.ttoltxt-discount').empty().append('$'
         + total);
           jQuery(this).find('.ttoltxt-discount').attr("value",percentage);
            
           // jQuery(this).find('.ttoltxt-discount').attr("discount",percentage);
         if(percentage > 0){
          jQuery('.tktDiscount').empty().append('$'+ percentage);
           jQuery('.tktDiscount').attr("value",percentage);
            jQuery(this).find('.ttoltxt').css('text-decoration','line-through');
         }
         else{  jQuery('.tktDiscount').empty().append('$0.00' );
             
         }
        
         }
        //jQuery('.sub-promo input').val('');
        //jQuery(".promo-msg").empty();
     }
     
   /*  else if (value ==''){  jQuery(".promo-msg").empty().append("Please enter promo code");}
   else if(promo !== value){
      
       jQuery(".promo-msg").empty().append("Promo code is incorrect" );
   }
   else{ jQuery(".promo-msg").empty();}*/
   
   
   
  
        }
});
 
getfulltot();
}


function clearcode(){
     jQuery(".ticket-details").each(function(){
         var discount11 = jQuery(this).find('.ttoltxt-discount').text();
         console.log('the code is'+ discount11);
         if(discount11){
                jQuery(this).find('.ttoltxt-discount').empty();
                 jQuery(this).find('.ttoltxt').css('text-decoration','none');
                 jQuery('.tktDiscount').attr("value","0");
                  jQuery('.tktDiscount').empty().append('$0.00' );
         }
          
     });
     jQuery('.sub-promo input').val('');
     jQuery(".clear-code").css('display','none');
     getfulltot();
}

function getfulltot(){
	var tottax=0;
	var totfee=0;
	var tottkt=0;
	var totqty=0;
	var totdis=0;
	jQuery('.tqty').each(function( index ) {
		var qty = jQuery(this).val();
		var tax = jQuery(this).parent().parent().find('.ttax').val();
		var price = jQuery(this).parent().parent().find('.tprice').val();
		var fee = jQuery(this).parent().parent().find('.tfee').val();
	    var dis = jQuery(this).parent().parent().find('.ttoltxt-discount').val();
		if(price!=0){
			tottkt +=  (parseFloat(price).toFixed(2)) * parseFloat(qty).toFixed(2);
			tottax +=  (parseFloat((tax!=undefined)?tax:0).toFixed(2)) * parseFloat(qty).toFixed(2);
			totfee +=  (parseFloat((fee!=undefined)?fee:0).toFixed(2)) * parseFloat(qty).toFixed(2);
		}
		totqty = parseInt(totqty) + parseInt(qty);
	});
	console.log(totqty);
	if(totqty>0){
		jQuery('#holdTicketBtn').attr('disabled',false);
			jQuery('.applybtn').attr('disabled',false);
	}else{
		jQuery('#holdTicketBtn').attr('disabled',true);
			jQuery('.applybtn').attr('disabled',true);
	
	}
	jQuery('.tkttotal').text('$'+parseFloat(tottkt).toFixed(2));
	jQuery('.tktTax').text('$'+parseFloat(tottax).toFixed(2));
	jQuery('.tktFee').text('$'+parseFloat(totfee).toFixed(2));
	var tt=parseFloat(tottkt) + parseFloat(tottax) + parseFloat(totfee);
	jQuery('#TotalAmount').val(parseFloat(tt).toFixed(2));
	//discount added
	var disvalue = jQuery('.tktDiscount').attr('value');
	console.log('value is ' + disvalue);
	if(disvalue){
	  var discount = tt - disvalue;
	  jQuery('.Total').text('$'+parseFloat(discount).toFixed(2));
	}
	else{
	jQuery('.Total').text('$'+parseFloat(tt).toFixed(2));
	}
	getticketholders();

	// update form if free or not
	if (parseFloat(tt) == 0)
	{
		$("#nameGroup").hide();
		$("#cardNumber").removeAttr("required");
		$("#cardNumberGroup").hide()
		$("#cardExpiry").removeAttr("required");
		$("#cardExpiryGroup").hide();
		$("#cardCVC").removeAttr("required");
		$("#cardCVCGroup").hide();
		//$("#rememberGroup").hide();
		$("#spacer").show();
	}
	else
	{
		$("#nameGroup").show();
		$("#cardNumber").attr('required', 'required');
		$("#cardNumberGroup").show();
		$("#cardExpiry").attr('required', 'required');
		$("#cardExpiryGroup").show();
		$("#cardCVC").attr('required', 'required');
		$("#cardCVCGroup").show();
		//$("#rememberGroup").show();
		$("#spacer").show();
	}
}

function getticketholders(){
	var tholder='';
	var _holdf = jQuery('#holderFirst').val();
	var _holdl = jQuery('#holderLast').val();
	var _holde = jQuery('#holderEmail').val();
	jQuery('#tickeTHoldersDiv').empty();

	jQuery('.tqty').each(function( index ) {

		var qty = jQuery(this).val();
		if(qty>0){
			var tname = jQuery(this).parent().parent().find('.tname').val();
			var tid = jQuery(this).parent().parent().find('.tid').val();
			tholder +='<div class="ticket_type_div">Ticket Type: <span>'+tname+'</span></div>'+
						   '<table style="width:100%" class="table_ordering_ticket_list">'+
							 '<tr><th></th><th>First Name</th><th>Last Name</th><th>Email</th></tr>';
			for(var i=1; i<=qty; i++){
				tholder +='<tr>'+
							 '<td id="order_tab">'+i+'</td>'+
							 '<td><input type="text" name="tktholder['+tid+']['+i+'][firstname]" placeholder="FIRST" value="'+_holdf+'" class="frm_ordering_page"></td>'+
							 '<td><input type="text" name="tktholder['+tid+']['+i+'][lastname]" placeholder="LAST" value="'+_holdl+'" class="frm_ordering_page"></td>'+
							 '<td><input type="text" name="tktholder['+tid+']['+i+'][customeremail]" value="'+_holde+'" placeholder="EMAIL" class="frm_ordering_page"></td>'+
						  '</tr>';
			}
			tholder +='</table>';
		}
	});
	jQuery('#tickeTHoldersDiv').html(tholder);
}

$(document).on('change', '#charitycountry', function () {
    var country_id = $(this).val();
    $.ajax({
        url: '/wp-content/themes/Divi Child/ajax/getstates.php?param=getstates&country_id='+country_id,
				processData: false,
				type: "GET",
        success: function (response) {
            if(response) {
                $('#charityregoin').html(response);
            }
        }
    });
});
