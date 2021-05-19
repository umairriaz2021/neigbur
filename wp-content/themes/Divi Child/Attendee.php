<?php 
/*
Template Name: Attendee Report
*/
get_header(); ?>

    <div id="main-content">


       <div class="outer-wrapper">
           <div class="container container-home">
              <div class="edit-event sale-rep">
        <h3 class="h3-title sales-rep">Attendees Report </h3>
		<div class="event-detail">
			<div class="upload-image-sale">
			<img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
            	<h3>The Market Jazz Festival</h3> 
				<b class="p-date">May 22,9019 8:am to 5:00pm</b><br>
				<p class="sale-venue"><b>ABC Venue</b><br>
				123 SomewhereSl,<br>
				New Market, Canada	<br>	
				Ontaria<br>
				A1a 1A1</p>
			</div>	
		</div>
		
		<div id="scanned" data-percent="25"> 
	     	<div class="tkt-sold">
		      <span>114</span>
		       Tickets Sold
		       </div>
	     	<div class="tkt-scanned">
		      <span>85</span>
		       Scanned or Checked-in
		       </div>
		  
		<span class="scan-date-time">LAST SCAN 05/11/2019 8:45pm</span>
		</div> </div>

	     <div class="breakdown">
	       <div class="sale-outer">  
	       <form role="search" method="get" class="edit-search" action="<?php echo site_url(); ?>">
				     	  <span class="e-search">  
				     	<input type="text" value="" placeholder="Search..." name="s" id="s">
				    	<input type="submit" id="searchsubmit" value="Search"> <i class="fa fa-search"></i></span>
			
			        </form>	</div>
            <table class="tkt-summary" style="width:100%">
               <tr>
                  <th>Contact</th>
                  <th class="right-align">Ticket Type</th>
                  <th class="right-align">Ticket#</th>
                  <th class="right-align">Order#</th>
                  <th class="right-align">Purchase Date</th>
                  <th class="right-align">Check-in</th>
               </tr>
                <tr>
                  <td><span class="tname">Deo Jane</span> <span class="temail">janedeo@gmail.com</span></td>
                  <td class="right-align">General Admission</td>
                  <td class="right-align">5d5c8gh11d5c8g</td>
                   <td class="right-align">G5d5c8gh11d5c8</td>
                   <td class="right-align">30/08/2019</td>
                   <td class="right-align"><span class="p-date">01/11/19</span><span class="p-time">3:04pm</span></td>
                  </tr>
               <tr>
                  <td><span class="tname">Deo Jane</span> <span class="temail">janedeo@gmail.com</span></td>
                  <td class="right-align">General Admission</td>
                  <td class="right-align">5d5c8gh11d5c8g</td>
                   <td class="right-align">G5d5c8gh11d5c8n</td>
                   <td class="right-align">30/08/2019</td>
                   <td class="right-align"><span class="p-date">01/11/19</span><span class="p-time">3:04pm</span></td>
                  </tr>
                  <tr>
                  <td><span class="tname">Deo Jane</span> <span class="temail">janedeo@gmail.com</span></td>
                  <td class="right-align">General Admission</td>
                  <td class="right-align">5d5c8gh11d5c8g</td>
                   <td class="right-align">G5d5c8gh11d5c8</td>
                   <td class="right-align">30/08/2019</td>
                   <td class="right-align"><span class="p-date">01/11/19</span><span class="p-time">3:04pm</span></td>
                  </tr>
            </table>
            <p class="att-load-more"><button class="load-event">Load More</button></p>
            <p class="view-all"><a href="#" class="load-event">View All</a></p>
            <div class="download-csv"><a href="#"><img width="80px" src="http://webdev.snapd.com/wp-content/uploads/2019/11/csv.jpg"></a>
            </div>
      </div>
      </div>   <!-- # outer-wrapper-->
    </div> <!-- #main content --> 

    <?php get_footer(); ?>