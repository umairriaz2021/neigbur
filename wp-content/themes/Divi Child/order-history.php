<?php
/*
Template Name: Order History Page
*/

get_header(); ?>

<div id="main-content">
<div class="outer-wrapper ">
	<div class="container container-home">
		<div class="account-outer">
		<div class="login-form account-info"> <!--  Order History start --->
		 <div class="tab-1">
			 <div class="tab-header"> <h3> Order History </h3><span class="edit"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
			 <div class="tab-row1">
				 <p>View your Event Ticket History</p>
			 </div>
			 <div class="tab-row1">
				 <strong>Filter by Year</strong>
				 <p> <select name="Years">
					 <option value="2021" selected>2021</option>
					 <option value="2020">2020</option>
                     <option value="2019">2019</option>
                     <option value="2018">2018</option>
                     <option value="2017">2017</option>
                     <option value="2016">2016</option>
               </select></p>
			 </div>
		 </div>
      </div>  <!--   Order History end --->
		<div class="login-form account-info"> <!--   Upcoming start --->
		 <div class="tab-1">
			 <div class="tab-header"> <h3>Details</h3> </div>
			 <div class="tab-row1">
			   <h5>OVFH Dinner & Concert Series</h5>
				 <strong>TICKET ID : 5b9c339c344cf93s00da4</strong>
				 <p>Purchased on Friday September 14, 2019 at 8:40pm by Dan MacPerhson</p>
			 </div>
	     	<div class="tab-row1">
			   <h5>Qty:2      TOTAL COST: $200</h5>
				 <strong>Date & Time</strong>
				 <p>Saturday September 22nd, 2019 through Sunday September 23rd, 2019    6:00 PM to 1:00 AM </p>
			 </div>
			 	 <div class="tab-row1">
			   <h5>2019 Winter Jazz Festival</h5>
				 <strong>TICKET ID : 5b9c339c344cf93s00da4</strong>
				 <p>Purchased on Friday September 14, 2019 at 8:40pm by Dan MacPerhson</p>
			 </div>
	     	<div class="tab-row1">
			   <h5>Qty:2      TOTAL COST: $200</h5>
				 <strong>Date & Time</strong>
				 <p>Saturday September 22nd, 2019 through Sunday September 23rd, 2019    6:00 PM to 1:00 AM </p>
			 </div>
		 </div>
       </div>  <!--   Upcoming end --->

	 </div>
	</div> <!-- #content-area -->

   </div>  <!-- #End Main -->


<?php get_footer(); ?>
