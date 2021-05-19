<?php
/*
Template Name: Get tickets order detail
*/
unset($_GET['single']);
get_header(); ?>
<?php
"<script>
 $('#holdTicketBtn').click(function(){
     '".add_option('myopt',1)."'
 });
</script>";

print_r(get_option('myopt'));die;
?>
 
<?php echo get_option('s_val');?>
<div id="main-content">
   <div class="outer-wrapper ">
      <div class="container container-home">
         <div class="getticket">
            <div class="head-h2">
               <h2>Order Details</h2>
               <button class="btn-return"><a href="<?php echo site_url(); ?>/get-ticket/"><i class="fa fa-toggle-left"></i> <span>Return to Event Setup</span></a></button>
               <ul class="progressbar">
                  <li class="active chk-mark">Select Tickets</li>
                  <li class="active">Order Details</li>
                  <li class="last-li">Payment</li>
               </ul>
            </div>
            <div class="time-sec">
               <p>Order Expires in: <span>05:00</span></p>
            </div>
            <div class="message-box">
               <p>Your ticket selection has been confirmed and will be held for the time shown.</p>
            </div>
            <div class="special-request">
               <h2>Special Requests
               </h2>
               <p>If the ticket holder(s) have any special request (i.e dietary needs, wheelchair accessibility, medical conditions) pease contact the event organizer</p>
               <div class="sec1-li">
                  <ul>
                     <li>Organization: </li>
                     <li>Name: </li>
                     <li>Phone: </li>
                     <li>Website:</li>
                     <li>Email: </li>
                  </ul>
               </div>
               <div class="sec2-li">
                  <ul>
                     <li>ABC Company</li>
                     <li>John smith</li>
                     <li>416-222-444</li>
                     <li><a href="#">https://www.abc.com</a></li>
                     <li><a href="#">jsmith@abc.com</a></li>
                  </ul>
               </div>
            </div>
            <div class="charitable-receipt">
               <h2>Charitable Donation Receipt</h2>
               <span class="chkbox">
               <input type="checkbox" name="" id="donation_receipt">
               <span class="checkmark"></span>
               If you would like to receive a Charitable donation receipt  for you purchase, please check this box and provide the required name and address information when prompted. Please note, this receipt will be sent by the organization hosting the event, not by <b>Neighbur inc.</b> </span>
               <div class="charitable-yes" id="charitable-yes" style="display:none">
                  <div class="one-half">  <label> First Name<em>*</em> <input type="text" placeholder="First" name="" id="" required="" title="Please enter your first name" value=""> </label></div>
                  <div class="one-half"><label> Last Name<em>*</em> <input type="text" placeholder="Last" name="" id="" required="" title="Please enter your last Name" value=""> </label></div>
                  <div class="one-full"><label> Address<em>*</em> <input type="text" placeholder="Address" name="" id="" required="" title="Please enter your Address" value=""> </label></div>
                  <div class="one-half">
                     <label>Country<em>*</em></label>
                     <select name="country">
                        <option value="1">Canada</option>
                        <option value="2">United States</option>
                     </select>
                  </div>
                  <div class="one-half">
                     <label>Province<em>*</em></label>
                     <select name="country">
                        <option value="1">Select...</option>
                        <option value="2">Option1</option>
                        <option value="3">option2</option>
                     </select>
                  </div>
                  <div class="one-half"> <label> City<em>*</em><input type="text" placeholder="City" name="" id="" required="" title="Please enter your City" value=""> </label></div>
                  <div class="one-half"><label> Postal code<em>*</em> <input type="text" placeholder="Postal code" name="" id="" required="" title="Please enter your Postal code" value=""> </label></div>
               </div>
            </div>
            <div class="ticketholders">
               <h2>Ticketholders</h2>
               <p><strong>It looks like you are purchasing multiple tickets!</strong> If these tickets aren't all for you, let us know which of your friends will be joining you and we will email them their assigned tickets.</p>
               <div class="ticket_type_div">Ticket Type: <span>Adult</span></div>
               <table style="width:100%" class="table_ordering_ticket_list">
                  <tr>
                     <th></th>
                     <th>First Name</th>
                     <th>Last Name</th>
                     <th>Email</th>
                  </tr>
                  <tr>
                     <td id="order_tab">1</td>
                     <td><input type="text" name="lastname" placeholder="FIRST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="LAST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
                  </tr>
                  <tr>
                     <td id="order_tab">2</td>
                     <td><input type="text" name="lastname" placeholder="FIRST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="LAST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
                  </tr>
                  <tr>
                     <td id="order_tab">3</td>
                     <td><input type="text" name="lastname"  placeholder="FIRST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname"  placeholder="LAST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
                  </tr>
               </table>
               <div class="ticket_type_div">Ticket Type: <span>Senior</span></div>
               <table style="width:100%" class="table_ordering_ticket_list">
                  <tr>
                     <th></th>
                     <th>First Name</th>
                     <th>Last Name</th>
                     <th>Email</th>
                  </tr>
                  <tr>
                     <td id="order_tab">1</td>
                     <td><input type="text" name="lastname" placeholder="FIRST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="LAST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
                  </tr>
                  <tr>
                     <td id="order_tab">2</td>
                     <td><input type="text" name="lastname" placeholder="FIRST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="LAST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
                  </tr>
                  <tr>
                     <td id="order_tab">3</td>
                     <td><input type="text" name="lastname"  placeholder="FIRST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname"  placeholder="LAST" class="frm_ordering_page"></td>
                     <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
                  </tr>
               </table>
            </div>
            <div class="button_order_page">
               <div class="back-btn"><a href="<?php echo site_url(); ?>/get-tickets/">BACK</a></div>
               <button class="next-btn" type="submit" name="btnSubmit"><a href="<?php echo site_url(); ?>/ticket-payment">NEXT</a></button>
            </div>
         </div>
      </div>
   </div>
   <!-- #content-area -->
</div>
<!-- #End Main -->



<script type="text/javascript">
    $(function () {
        $("#donation_receipt").click(function () {
            if ($(this).is(":checked")) {
                $("#charitable-yes").show();
            } else {
                $("#charitable-yes").hide();
            }
        });
    });
    
    $('#holdTicketBtn').click(function(){
        $('#s_val').value(1);
    });

</script>


<?php get_footer(); ?>
