<?php
/*
Template Name: Preview Event
*/
get_header(); ?>

    <div id="main-content">
       <div class="outer-wrapper ">
           <div class="container container-home">
                 <h3 class="h3-title">Submit Your Next Big Event</h3>
				  <ul class="progressbar">
                   <li class="active">Page Design</li>
                   <li>Option and Submit</li>
                </ul>
         <p style="float:left;width:100%;text-align:center;margin-bottom:10px;font-size: 15px;">Complete each required section. Select NEXT to proceed.</p>
			<hr/>
			<div class="event-type">
		    	<form>
			    <span class="radio-chk">  <input type="radio" name="event" value="Single-day-Event" checked="checked"> <span class="checkmark1"> </span>Single day Event</span>
                 <span class="radio-chk"> <input type="radio" name="event" value="Multi-day-event"> <span class="checkmark1"> </span>Multi-day event</span>
				  <span class="sameday">This event will start and end on same date</span>
		    	</form>
				<span class="event-preview"><a href="<?php echo site_url(); ?>/preview-event/"> PREVIEW <i class="fa fa-eye"></i></a></span>
		   </div>
		<hr/>
		<div class="event-detail">
			<div class="upload-image">
			<img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
				<div class="image-gallery">
			<h2 style="text-align: left;padding-bottom: 0;font-size: 30px;padding-top: 11px;">Gallery</h2>
			<img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
			<img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
			</div>
			<div id="loadMore">Load more</div>
			</div>
			<form class="event-details">
				<h3>The Market Jazz Festival</h3>
				<b class="p-date">May 22,9019 8:am to 5:00pm</b>
				<p>ABC Venue
				 SomewhereSl,<br/>
				New Market, Canada	<br/>
				Ontaria<br/>
				A1a 1A1</p>
				<div class="p-description">
				<h3>Description</h3>
				<p>Curabitur ultrices ex quis dictum fringilla. Nam sagittis lectus ollicitudin.Curabitur ultrices ex quis dictum fringilla. Nam sagittis lectus eu enim fringilla, ac ornareeu enim fringilla, ac ornare libero sollicitudin.Curabitur ultrices ex quis dictum fringilla. Nam sagittis lectus eu enim fringilla, ac ornare libero ornare libero sollicitudin.Curabitur ultrices ex quis dictum fringilla. Nam sagittis lectus eu enim fringilla, ac ornare libero sollicitudin.</p>
					<div class="exp-more"> Read More <span> <img src="http://webdev.snapd.com/wp-content/uploads/2019/09/down-arrow.png"></span></div>
				</div>
				<div class="p-catg">Music</div>
				<h2 class="f-tkt">This is a free Event</h2>
				<div class="attachemnts">
				<div class="logo-details">
				<h3>Contact Details</h3>
				<p><b>Name:</b> John</p>
				<p><b>Phone:</b> 9876543210</p>
				<p><b>Website: </b><a href="#">www.snapd.com</a></p>
				<p><b>Email: </b><a href="mailto:">snapd@gmail.com</a></p>
				<p class="up-logo"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/09/logo.png"></p>
				<p class="up-attach">
				<h3>Attachment</h3>
				<span>Include a single PDF for all instructions, waivers, map etc...</span>
				<button class="btn-downlaod"><i class="fa fa-download"></i> Download</button></p>
				</div>
				<div class="map-details"><h3>Map Details</h3> <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d184551.80858184173!2d-79.51814199446795!3d43.718403811497105!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4cb90d7c63ba5%3A0x323555502ab4c477!2sToronto%2C%20ON%2C%20Canada!5e0!3m2!1sen!2sin!4v1568367410679!5m2!1sen!2sin" width="250" height="290" frameborder="0" style="border:0;" allowfullscreen=""></iframe></div>

				</div>
            </form>
		<!--	<div class="help-btn"><i class="fa fa-question-circle"></i> Need Help? <a href="#">Visit our support site for answers</a></div> -->
	     </div>
		 </div>	 <!-- #container -->
      </div>  <!-- #outer-wrapper -->
        </div> <!-- #main content -->

    <?php get_footer(); ?>
