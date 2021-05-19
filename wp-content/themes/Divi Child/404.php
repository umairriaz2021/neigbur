<?php get_header(); ?>

<div id="main-content" class="not-found2">
	<div class="container">
		<div id="content-area" class="clearfix">
		    
		    
		  <?php 
global $et_no_results_heading_tag;
if ( empty( $et_no_results_heading_tag ) ){
	$et_no_results_heading_tag = 'h1';
}
?>
<div class="entry">
<!--If no results are found-->
	<<?php echo $et_no_results_heading_tag; ?> class="not-found-title"></<?php echo $et_no_results_heading_tag; ?>>
	<div class="opps"><b><?php esc_html_e('Oops!'); ?> </b><?php esc_html_e("Looks like you've found");?> <br/><?php esc_html_e(" yourself in a bad neighburhood.","Divi"); ?></div>
	<div class="may-be">Maybe you should</div>
	<div class="go-bck"><a href="<?php echo site_url() ?>">Go back Home</a></div>
</div>
<!--End if no results are found-->  
		    
		    

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php

get_footer();
