<?php
/*
Template Name: Ticket payment
*/

get_header(); ?>



<div id="main-content">
   <div class="outer-wrapper ">
      <div class="container container-home">
         <div class="getticket">
            <div class="head-h2">
               <h2>Order Details</h2>
               <button class="btn-return"><a href="<?php echo site_url(); ?>/ticket-order-details/"><i class="fa fa-toggle-left"></i> <span>Return to Event Setup</span></a></button>
               <ul class="progressbar">
                  <li class="active chk-mark">Select Tickets</li>
                  <li class="active chk-mark">Order Details</li>
                  <li class="active last-li">Payment</li>
               </ul>
            </div>
            <div class="time-sec">
               <p>Order Expires in: <span>05:00</span></p>
            </div>
            <div class="tkt-message-box"> All Tickets Are NON-REFUNDABLE</div>
            <!-- CREDIT CARD FORM STARTS HERE -->
            <div class="credit-card">
               <div class="creit-card-box">
                  <div class="card-head">
                     <h3>Neighbur TIX Event</h3>
                     <p> Event Title here</p>
                  </div>
                  <div class="form-group">
                     <label for="email"> <i class="fa fa-envelope"></i> </label>
                     <input type="text" id="email" name="email" placeholder="Email">
                  </div>
                  <div class="form-group">
                     <label for="cardNumber"> <i class="fa fa-credit-card"></i> </label>
                     <input  type="tel" class="form-control" name="cardNumber" placeholder="Card Number"  autocomplete="cc-number" required autofocus />
                  </div>
                  <div class="form-group">
                     <label for="cardExpiry"><i class="fa fa-calendar"></i></label>
                     <input  type="tel"  class="form-control"  name="cardExpiry" placeholder="MM / YY" autocomplete="cc-exp" required />
                  </div>
                  <div class="form-group">
                     <label for="cardCVC"> <i class="fa fa-lock"></i></label>
                     <input  type="tel"  class="form-control" name="cardCVC" placeholder="CVC" autocomplete="cc-csc" required/>
                  </div>
                  <p class="remember">
                     <span class="chkbox"><input type="checkbox" checked="checked" name="remember"><span class="checkmark"></span> &nbsp;  &nbsp; Remember me</span>
                  </p>
                  <p class="payout2"><button style="width: 80%;" type="submit" name="SignInUser" class="signupbtn">Buy Tickets $11.77(CAD)</button> </p>
               </div>
            </div>
            <!-- CREDIT CARD FORM ENDS HERE -->
            <div class="button_order_page">
               <div class="back-btn"><a href="<?php echo site_url(); ?>/ticket-order-details/">BACK</a></div>
            </div>
         </div>
      </div>
   </div>
   <!-- #content-area -->
</div>
<!-- #End Main -->





<?php get_footer(); ?>
