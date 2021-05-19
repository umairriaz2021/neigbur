<?php 
/*
Template Name: Sales Report
*/
if(isset($_SESSION['userdata'])){
	$userdata = $_SESSION['userdata'];
}else{
	wp_redirect( site_url().'?page_id=187' );
	exit;
}

global $wpdb;
$token   =  $_SESSION['Api_token'];
$url = $_SERVER['REQUEST_URI'];      
$event = explode('/', $url);
$event_id = $event[2];

if (isset($event_id) && $event_id != '') {

   //$event_id = $_GET['event_id'];

   $ch   = curl_init(API_URL . '/events/' . $event_id);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: ' . $token
   ));
   $result = curl_exec($ch);
   curl_close($ch);
   $apirespons = json_decode($result);

   if ($apirespons->success) {

      $event_detail = $apirespons->event;
	  
	  /*fetch ticket data*/
      $ch      = curl_init(API_URL.'ticketTypes?eventId='.$event_id);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'Content-Type: application/json',
         'Authorization: ' . $token
      ));
      $tkt_response = curl_exec($ch);
      curl_close($ch);
      $tkt = json_decode($tkt_response);
	// echo 'ON Api call:- '.API_URL.'ticketTypes?eventId='.$event_id;
	 //echo "<pre>"; print_r($tkt); 
	 
	 /*fetch ticket Purchaser data */
	 $ch      = curl_init(API_URL.'/admin/orders?eventId='.$event_id);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'Content-Type: application/json',
         'Authorization: ' . $token
      ));
      $tkt_response = curl_exec($ch);
      curl_close($ch);
      $tktPurchaser = json_decode($tkt_response);
      $orders = $tktPurchaser->orders;
	 //echo 'ON Api call:- '.API_URL.'orders?eventId='.$event_id;
	 //echo "<pre>"; print_r($tktPurchaser); die;
		
      if($tkt->success && !empty($tkt->ticketType)) {
         $tickets = $tkt->ticketType;              
      }
      $metadata = unserialize($event_detail->metadata);


   }
}
get_header(); ?>
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/css/salesreport.css">
<div id="main-content">
       <div class="outer-wrapper">
           <div class="container container-home">
              <div class="edit-event sale-rep">
        <h3 class="h3-title sales-rep">Sales Report </h3>
		<div class="event-detail">
			<div class="upload-image-sale">
			<?php if (isset($event_detail) && count($event_detail->files) > 0 && $event_detail->files[0]->type == 'image') { ?>
                  <img src="https://storage.googleapis.com/<?php echo $event_detail->files[0]->bucket ?>/<?php echo $event_detail->files[0]->filename; ?>" style="max-height: 250px;">
              <?php }else{  ?>
                  <img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
               <?php } ?>
            	<h3><?php echo isset($event_detail) ? $event_detail->name : ''; ?></h3> 
				<b class="p-date"> <?php foreach ($event_detail->event_dates as $edate) {
                  if ($edate->start_date == $edate->end_date) { ?>
                     <b class="p-date"><?php echo date('M d, Y h:i a', strtotime($edate->start_date)); ?> to <?php echo date('h:i a', strtotime($edate->end_date)); ?></b>
                  <?php } else { ?>
                     <b class="p-date"><?php echo date('M d, Y h:i a', strtotime($edate->start_date)); ?> to <?php echo date('M d, Y h:i a', strtotime($edate->end_date)); ?></b>
                  <?php } 
				  } ?></b><br>
				<p class="sale-venue"><?php
				 $country = $wpdb->get_row("Select * from wp_countries where  id = $event_detail->country_id");
                  $state = $wpdb->get_row("select * from wp_states where id = $event_detail->province_id");
				echo isset($event_detail) ? !empty($event_detail->location) ? $event_detail->location.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? !empty($event_detail->address2) ? $event_detail->address2.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? $event_detail->city : '';?>, <?php echo isset($event_detail) ? $state->name : '';?>, <?php echo isset($event_detail) ? $country->name : '';?><br/>
                     <?php echo isset($event_detail) ? $event_detail->postalcode : '';?></p>
			</div>	
		</div>
		<?php 
	$tota_per = 0;
		$l = count($tickets);
		foreach($tickets as $key=>$val) { 
               $total_tkts = $val->order_limit;
               $sold_tkts = $val->ticket_allocation;    
               $available_tkts = $total_tkts - $sold_tkts;
               $persentage = ($sold_tkts/$total_tkts)*100; 
               $tota_per += $persentage;
               } 
               $totalsold = $tota_per / $l;
               //print_r($totalsold);
               ?>

		       <svg class="radial-progress" data-percentage="<?= $totalsold?>" viewBox="0 0 80 80">
		           <circle class="incomplete" cx="40" cy="40" r="35" style="stroke: #8e8e8e;">></circle>
		           <circle class="complete" cx="40" cy="40" r="35" style="stroke-dashoffset: 39.58406743523136;"></circle>
		           <p class="total">Total</p>
		           <text class="percentage" x="50%" y="57%" transform="matrix(0, 1, -1, 0, 80, 0)"><?= $totalsold?>%</text>
		           </svg> 
		
		</div>
	     <div class="breakdown">
	         <h4 class="sale-tkt">Ticket Type Breakdown</h4>
            <table class="tkt-summary sale_tkt_brk" style="width:100%">
               <tr>
                  <th>Ticket Type </th>
                  <th class="right-align">Price</th>
                  <th class="right-align">Sold</th>
                  <th class="right-align">Available</th>
                  <th class="right-align">Total</th>
                  <th class="right-align">%</th>
               </tr>
               <?php 
			  
			   $ti = 0; 
               foreach($tickets as $key=>$val) {
                   $ti++;
               $total_tkts = $val->order_limit;
               $sold_tkts = $val->ticket_allocation;    
               $available_tkts = $total_tkts - $sold_tkts;
               $persentage = ($sold_tkts/$total_tkts)*100;
               
               ?>
                <tr id="tlr_<?=$ti?>" class="tkt_list">
                  <td><?php echo $val->name;?></td>
                  <td class="right-align"><?php echo '$'.number_format($val->price,2);?></td>
                  <td class="right-align"><?php echo $val->ticket_allocation;?></td>
                  <td class="right-align"><?php echo $available_tkts; ?></td>
                  <td class="right-align"><?php echo $total_tkts;?></td>
                  <td class="right-align"><?php echo sprintf("%.2f", $persentage) ?></td>
                </tr>
               <?php } ?> 
            </table>
            <!--<p class="att-load-more"><button class="load-event loadmore_tkts">Load More</button></p>
            <p class="view-all"><a href="" class="load-event">View All</a></p>
            <div class="download-csv"><a href="#"><img width="80px" src="http://webdev.snapd.com/wp-content/uploads/2019/11/csv.jpg"></a></div>-->
</div>
	     <div class="breakdown">
	       <div class="sale-outer"> <h4 class="sale-tkt">Ticket Purchase Breakdown</h4> 
	       <form role="search" method="get" class="edit-search" action="<?php echo site_url(); ?>">
				     	  <span class="e-search">  
				     	<input type="text" value="" placeholder="Search..." name="s" id="s">
				    	<input type="submit" id="searchsubmit" value="Search"> <i class="fa fa-search"></i></span>
			
			        </form>	</div>
            <table class="tkt-sumtkt_listmary sale_prch_brk get_summery" style="width:100%">
               <tr>
                  <th>Date Purchased</th>
                  <th class="right-align">Order No.</th>
                  <th class="right-align">Purchaser</th>
                  <th class="right-align">Ticket Type</th>
                  <th class="right-align">Tickets Total</th>
                  <th class="right-align">Your Payout</th>
               </tr>
               <?php 	 $t = 0;
	 $pur_break = array();
	 foreach($orders as $order_item){
       foreach($order_item->ticket_order_item as $tks){
         foreach($tks->tickets as $stkt){
            if($stkt->firstname !='' && $stkt->lastname != '' && $stkt->email != ''){
                $booking_date = date('d M, Y', strtotime($order_item->create_date));
                $booking_time = date('h:i a', strtotime($order_item->create_date));
                 $price= $tks->ticket_type->price;
       
       
       
       
       
       
	$Havingtax= empty($tickets[0]->tax_profile) ? '0' : '1' ;

	$taxrate= $tickets[0]->tax_profile->tax_rate_aggregate * 100;
	if ($Havingtax == 0) {
		$taxrate=13.00;
	}
	$ConvenienceFees= $tks->ticket_type->fee_percentage;
	$taxInc = $tks->ticket_type->tax_inclusion;
		$taxAmount=($price*$taxrate)/100;

	if ($taxInc > 0) {
		$taxAmount=($price*$taxrate)/100;
	}
	else{
		$taxAmount=($price*$taxrate)/100;		
	}
	
	$BCF1a=$BCF1b=$ECF2=$BCFTax=0;

	if ($ConvenienceFees != '0') {
		$BCF= ($price*$ConvenienceFees)/100;
		$BCFTax=( $BCF*$taxrate)/100;
		$ECF2=( ($BCF+$BCFTax+$price)*3.30 )/100;
		$TCF=$ECF2+$BCF+$BCFTax;
		$ECF2Tax=($ECF2*$taxrate)/100;
	}
	else{

		if ($price < 5.01) {
			$BCF1a=0.50;
			$BCF1b= 0.00;
		}
		else if($price > 5.01 && $price <9.50){
			$BCF1a=0.00;
			$BCF1b=($price*10)/100;
		}
		else if ($price >9.49) {
			$BCF1a=0.95;
			$BCF1b=($price*2.50)/100;
		}

		$BCFTax=( ($BCF1a+$BCF1b)*$taxrate)/100;
		$ECF2=( ($BCF1a+$BCF1b+$BCFTax+$price)*3.30 )/100;

		$TCF=$ECF2+$BCF1a+$BCF1b+$BCFTax;
		$ECF2Tax=($ECF2*$taxrate)/100;

	}
	
	$SubTotalPrice=$price+$TCF;
	$CustTktPrice=$CustTktTax=$CustCF=$CustCFTax=0;
	$OrgTktPrice=$OrgTktTax=$OrgCF=$OrgCFTax=0;

	if($taxInc > 0){
		$CustTktPrice=$OrgTktPrice=$price-$taxAmount;
		$CustTktTax=$OrgTktTax=$taxAmount;
		$CustCF=$OrgCF=$ECF2+$BCF1a+$BCF1b;
		$CustCFTax=$OrgCFTax=$BCFTax+$ECF2Tax;
		$CustTktTotal=$price+$TCF+$ECF2Tax;
		$OrgTktTotal=$price-$TCF-$ECF2Tax;
	}
	else{
		$CustTktPrice=$OrgTktPrice=$price;	
		$CustTktTax=$OrgTktTax=$taxAmount;
		$CustCF=$OrgCF=$ECF2+$BCF1a+$BCF1b;
		$CustCFTax=$OrgCFTax=$BCFTax+$ECF2Tax;
		$CustTktTotal=$price+$TCF+$ECF2Tax;
		$OrgTktTotal=$price-$TCF-$ECF2Tax;

	}
                
                
       $pur_break[$t] = array(
           "date"=>$booking_date,
           "time"=>$booking_time,
           "order_id"=>$tks->ticket_order_id,
           "holder_name"=>$stkt->firstname,
           "ticket_type"=>$tks->ticket_type->name,
           "tkt_total"=>$CustTktTotal,
           "your_payout"=>$OrgTktTotal
           );
       
       $t++;
            }
         }
	 }
	 }
     //echo "<pre>"; print_r($pur_break);
     for($b=0;$b<count($pur_break);$b++){
     ?>
                <tr id="tbr_<?= $b+1 ?>" class="tkt_brkdwn">
                  <td><span class="p-date"><?php echo $pur_break[$b][date]?></span><span class="p-time"><?php echo ' '.$pur_break[$b][time]?></span></td>
                  <td class="right-align"><?php echo $pur_break[$b][order_id]?></td>
                  <td class="right-align"><?php echo $pur_break[$b][holder_name]?></td>
                   <td class="right-align"><?php echo $pur_break[$b][ticket_type]?></td>
                   <td class="right-align"><?php echo "$".number_format($pur_break[$b][tkt_total],2);?></td>
                   <td class="right-align"><?php echo "$".number_format($pur_break[$b][your_payout],2);?></td>
                  </tr>
<?php } ?>

                  
           
                       
               <tr class="buyer_total">
                   <td></td>
                   <td></td>
                   <td></td>
                  <td>Overall Totals</td>
                  <?php $total_tkt=$your_payout=0; 
                  for($tt=0;$tt<count($pur_break);$tt++){ 
                  $total_tkt+= $pur_break[$tt][tkt_total]; 
                  $your_payout+=$pur_break[$tt][your_payout];
                  }?>
                  <td class="right-align"><?php echo "$".number_format($total_tkt,2); ?></td>
                  <td class="right-align"><?php echo "$".number_format($your_payout,2); ?></td>
               </tr></table>
              <div class="load-sales"> <p class="att-load-more"><button class="load-event loadmore_brkdns">Load More</button></p>
               <p class="view-all"><a href="#" class="load-event viewall_brkdns">View All</a></p></div>
          <!--  <div class="download-csv"><a href="#"><img width="80px" src="http://webdev.snapd.com/wp-content/uploads/2019/11/csv.jpg"></a>
            </div>-->
      </div>
      	     <div class="breakdown">   
         <h4 class="sale-tkt">Ticket Purchaser Detailed Summary</h4>
         <div class="download-csv"><a href="" id="get_csv" download=""><img width="80px" src="http://webdev.snapd.com/wp-content/uploads/2019/11/csv.jpg"></a></div>
         </div>
      </div>   <!-- # outer-wrapper-->
    </div> <!-- #main content --> 

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-circle-progress/1.2.0/circle-progress.min.js'></script>
<script>$(function(){

    // Remove svg.radial-progress .complete inline styling
    $('svg.radial-progress').each(function( index, value ) { 
        $(this).find($('circle.complete')).removeAttr( 'style' );
    });

    // Activate progress animation on scroll
    $(window).scroll(function(){
        $('svg.radial-progress').each(function( index, value ) { 
            // If svg.radial-progress is approximately 25% vertically into the window when scrolling from the top or the bottom
            if ( 
                $(window).scrollTop() > $(this).offset().top - ($(window).height() * 0.75) &&
                $(window).scrollTop() < $(this).offset().top + $(this).height() - ($(window).height() * 0.25)
            ) {
                // Get percentage of progress
                percent = $(value).data('percentage');
                // Get radius of the svg's circle.complete
                radius = $(this).find($('circle.complete')).attr('r');
                // Get circumference (2πr)
                circumference = 2 * Math.PI * radius;
                // Get stroke-dashoffset value based on the percentage of the circumference
                strokeDashOffset = circumference - ((percent * circumference) / 100);
                // Transition progress for 1.25 seconds
                $(this).find($('circle.complete')).animate({'stroke-dashoffset': strokeDashOffset}, 1250);
            }
        });
    }).trigger('scroll');

});
/*
  $('.tkt_list').hide();
  var count_tkt = $('.tkt_list').length
  if(count_tkt <= 3){
     $('.loadmore_tkts').hide();
     $('.viewall_tkts').hide();
     }
     else{
         var c = count_tkt;
         var last,nl,e;
         $('.tkt-summary tr').each(function(c){
             if(nl <= 3){
                 $('#tlr_'+nl).show()
                 last = nl;
             }
             })
           $('.loadmore_tkts').click(function(){
                 last += 3;
                 $('.tkt-summary tr').each(function(c){
                     nl = c + 1;
                     if(nl <= last){
                         $('#tlr_'+nl).show()
                     }
                 })
              last = nl;
          })
        }
        */

//$('.tkt_brkdwn').hide();


  var count_tkt = $('.tkt_brkdwn').length;
  $('.tkt_brkdwn').each(function(e){
                     all = e + 1;
                     if(all <= 10){
                         $('#tbr_'+all).show();
                     }else{
                         $('#tbr_'+all).hide();
                     }
                 });
  if(count_tkt <= 10){
     $('.loadmore_brkdns').hide();
     $('.viewall_brkdns').hide();
     }
    
     else{
         //var e = count_tkt;
         var last_bd,nb,e,c;
         $('.tkt_brkdwn').each(function(e){
             nb = e + 1;
             if(nb <= 10){
                 $('#tbr_'+nb).show();
                 last_bd = nb;
             }
             });
           $('.loadmore_brkdns').click(function(){
                 last_bd += 10;
                 $('.tkt_brkdwn').each(function(e){
                     nb = e + 1;
                     if(nb <= last_bd){
                         $('#tbr_'+nb).show();
                         c = nb;
                     }
                 });
                check_shown();  
              last_bd = c;
          });
        }


function check_shown(){
    $('.tkt_brkdwn').each(function(){
        var vis = $(this).css('display')
        if(vis != 'none'){$('.viewall_brkdns').text('Collaps All'),$('.loadmore_brkdns').prop('disabled', true).css('background','#b9b8b8')}
        else{$('.viewall_brkdns').text('View All'),$('.loadmore_brkdns').prop('disabled', false).css('background','#f56d3a')}
})
}

$('.viewall_brkdns').click(function(btn){
    btn.preventDefault();
    var state = $(this).text();
    if(state == 'Collaps All'){
        $('.tkt_brkdwn').each(function(e){
                     nb = e + 1;
                     if(nb <= 10){
                         $('#tbr_'+nb).show();
                     }else{
                         $('#tbr_'+nb).hide();
                     }
                 });
               $('.viewall_brkdns').text('View All');
               $('.loadmore_brkdns').prop('disabled', false).css('background','#f56d3a')
               last_bd = 10;
        }else{
          $('.tkt_brkdwn').each(function(){
                     $(this).show();
                 });
               $('.viewall_brkdns').text('Collaps All');
               $('.loadmore_brkdns').prop('disabled', true).css('background','#b9b8b8')
        }
})

 $(document).ready(function () {
    $('.get_summery').each(function () {
        var table = $(this);

        var csv = table.table2CSV({
                delivery: 'value'
            });
            var link = $('#get_csv');
            
    link.attr('target', '_blank');
    link.attr('href', 'data:text/csv;charset=UTF-8,' + encodeURIComponent(csv));
    link.attr('download', 'Ticket_Purchaser_Detailed_Summary_event-<?= $event_id ?>_'+new Date().toLocaleDateString()+'.csv');
        
    });
});
    
   // $('#get_csv').click(function (fd) {
   //     fd.preventDefault();
   //     window.location.href = 'data:text/csv;charset=UTF-8,' + encodeURIComponent(csv_string);
   // });
    
    
    jQuery.fn.table2CSV = function(options) {
    var options = jQuery.extend({
        separator: ',',
        header: [],
        headerSelector: 'th',
        columnSelector: 'td',
        delivery: 'popup', // popup, value, download
        //filename: 'Ticket_Purchaser_Detailed_Summary_<?= $event_id ?>.csv', // filename to download
        transform_gt_lt: true // make &gt; and &lt; to > and <
    },
    options);

    var csvData = [];
    var headerArr = [];
    var el = this;

    //header
    var numCols = options.header.length;
    var tmpRow = []; // construct header avalible array

    if (numCols > 0) {
        for (var i = 0; i < numCols; i++) {
            tmpRow[tmpRow.length] = formatData(options.header[i]);
        }
    } else {
        $(el).find(options.headerSelector).each(function() {
            if ($(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData($(this).html());
        });
    }

    row2CSV(tmpRow);

    // actual data
    $(el).find('tr').each(function() {
        var tmpRow = [];
        $(this).find(options.columnSelector).each(function() {
            if ($(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData($(this).html());
        });
        row2CSV(tmpRow);
    });
    if (options.delivery == 'popup') {
        var mydata = csvData.join('\n');
        if(options.transform_gt_lt){
            mydata=sinri_recover_gt_and_lt(mydata);
        }
        return popup(mydata);
    }
    else if(options.delivery == 'download') {
        var mydata = csvData.join('\n');
        if(options.transform_gt_lt){
            mydata=sinri_recover_gt_and_lt(mydata);
        }
        var url='data:text/csv;charset=utf8,' + encodeURIComponent(mydata);
        window.open(url);
        return true;
    } 
    else {
        var mydata = csvData.join('\n');
        if(options.transform_gt_lt){
            mydata=sinri_recover_gt_and_lt(mydata);
        }
        return mydata;
    }

    function sinri_recover_gt_and_lt(input){
        var regexp=new RegExp(/&gt;/g);
        var input=input.replace(regexp,'>');
        var regexp=new RegExp(/&lt;/g);
        var input=input.replace(regexp,'<');
        return input;
    }

    function row2CSV(tmpRow) {
        var tmp = tmpRow.join('') // to remove any blank rows
        // alert(tmp);
        if (tmpRow.length > 0 && tmp != '') {
            var mystr = tmpRow.join(options.separator);
            csvData[csvData.length] = mystr;
        }
    }
    function formatData(input) {
        // double " according to rfc4180
        var regexp = new RegExp(/["]/g);
        var output = input.replace(regexp, '""');
        //HTML
        var regexp = new RegExp(/\<[^\<]+\>/g);
        var output = output.replace(regexp, "");
        output = output.replace(/&nbsp;/gi,' '); //replace &nbsp;
        if (output == "") return '';
        return '"' + output.trim() + '"';
    }
    function popup(data) {
        var generator = window.open('', 'csv', 'height=400,width=600');
        generator.document.write('<html><head><title>CSV</title>');
        generator.document.write('</head><body >');
        generator.document.write('<textArea cols=70 rows=15 wrap="off" >');
        generator.document.write(data);
        generator.document.write('</textArea>');
        generator.document.write('</body></html>');
        generator.document.close();
        return true;
    }
};
    
</script>
<?php get_footer(); ?>
<?php
// echo "<pre>"; print_r( $tickets); 
// echo 'ON Api call:- '.API_URL.'ticketTypes?eventId='.$event_id;
// echo "<pre>"; print_r($tkt);
// echo 'ON Api call:- '.API_URL.'admin/orders?eventId='.$event_id;
// echo "<pre>"; print_r($orders);
?>