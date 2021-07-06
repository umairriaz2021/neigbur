<?php


/*

Template Name: Home Page

*/

$ch = curl_init(API_URL . 'categories?sort=ASC&sortType=name');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(

    'Content-Type: application/json',

    //'Authorization: ' . $token

));

$result2 = curl_exec($ch);

curl_close($ch);

$categoryList = json_decode($result2);

/* echo "<pre>"; print_r($_SESSION); die;  */

get_header(); ?>


<script>

    jQuery(document).ready(function () {

        jQuery('#owl-one').owlCarousel({

            margin: 5,

            nav: true,

            loop: true,

            slideBy: 4,

            responsiveClass: true,

            responsive: {

                0: {

                    items: 1

                },

                600: {

                    items: 3

                },

                1000: {

                    items: 4

                }

            }

        });


        jQuery('#owl-two').owlCarousel({

            margin: 4,

            nav: true,

            loop: false,

            navRewind: false,

            slideBy: 1,

            responsiveClass: true,

            responsive: {

                0: {

                    items: 1

                },

                600: {

                    items: 4

                },

                1000: {

                    items: 5

                },

                1300: {

                    items: 6

                }

            }

        });

    });

    jQuery("title").html("Neighbur");

</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.css"
      integrity="sha256-b88RdwbRJEzRx95nCuuva+hO5ExvXXnpX+78h8DjyOE=" crossorigin="anonymous"/>
<style>
    .row{
        margin-left:0;
        margin-right:0;
    }
    #owl-two button.owl-next, #owl-two button.owl-prev {
        color: #000000 !important;
        border: 2px solid #000 !important;
    }

    #owl-two button.owl-next.disabled, #owl-two button.owl-prev.disabled {
        color: #b7b9bb !important;
        border: 2px solid #b7b9bb !important;
    }

    #month_select {

        display: flex;
        flex-direction: row;
    }

    #datemonth_from, #datemonth_to {
        padding: 13px 0;
    }


</style>


<div id="main-content">

    <?php if (have_posts()) : while (have_posts()) : the_post();

        the_content();

    endwhile;
    else: ?>

        <p>Sorry, no posts matched your criteria.</p>

    <?php endif; ?>

    <div class="outer-wrapper grey">

        <div class="container container-home">

            <div id="content-area" class="clearfix">

			<form id="SearchForm" class="row align-items-center" action="<?php echo site_url() ?>/search-result/" method="POST">

				<div class="serach-form">

                        <div class="row no-gutters">

                            <div class="col">

                                <input type="text" class="form-control1 inSty"
                                       placeholder="Keyword (Optional)" name="search_event"
                                       id="search_event" value="<?php //echo @$_SESSION['sePra']['search_event']; ?>">

                            </div>

                            <div class="col">

                                <!--input type="text" class="form-control1 inSty loc" placeholder="Location" name="location"-->

                                <input id="autocomplete"

                                       name="location"

                                       placeholder="Location (Required)"

                                       onFocus="geolocate()"

                                       class="form-control1 inSty loc"

                                       value="<?php echo @$_SESSION['sePra']['location']; ?>"

                                       type="text"/>

                                <input type="hidden" name="loclat" id="locLat"
                                       value="<?php echo @$_SESSION['sePra']['loclat']; ?>">

                                <input type="hidden" name="loclong" id="locLong"
                                       value="<?php echo @$_SESSION['sePra']['loclong']; ?>">

                            </div>

                            <button class="d-inline-block w-100 read_btn btn_search" type="submit" name="btnSearch">
                                Search
                            </button>

                        </div>


                </div>

                <div class="filter-icon filterDiv" style="cursor: pointer;">

<?php
// check if filter was applied
function WasFilterApplied()
{
  if (isset($_SESSION['sePra']['filter_category']) && count($_SESSION['sePra']['filter_category']) > 0 && $_SESSION['sePra']['filter_category'][0] != "17")
  {
    return true;
  }
  if (isset($_SESSION['sePra']['radiusfilter']) && $_SESSION['sePra']['radiusfilter'] != '' && $_SESSION['sePra']['location'] != '' && $_SESSION['sePra']['radiusfilter'] != '50')
  {
    return true;
  }

  if (isset($_SESSION['sePra']['pricefilter']) && $_SESSION['sePra']['pricefilter'] != "1")
  {
    return true;
  }

  if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] != 'nw')
  {
    return true;
  }

  return false;

}

$filterEvents = "Filter Events";
if (WasFilterApplied())
  $filterEvents = "Filter will be applied";

?>

				<span onclick="showfilter()"><img
                            src="<?php echo site_url(); ?>/wp-content/uploads/2019/09/Filter-Icon-1.png">

					<span id="filtertext"><?php echo $filterEvents; ?></span>

				</span>

                    <span id="filter_open" style="display:none;"> <button class="reset deselectFilter" type="button"
                                                                          name="btnReset">Reset</button></span>

                </div>

                <div class="search-main-section filterDiv" id="filter_div" style="display:none;">

                    <div class="Search-radius">

                        <h2>Search Radius</h2>

                        <div class="range-wrap">

                            <input type="range" class="range slider" name="radiusfilter" min="1" max="100"
                                   value="<?php echo (isset($_SESSION['sePra']['radiusfilter'])) ? $_SESSION['sePra']['radiusfilter'] : 50; ?>"
                                   id="myRange">

                            <output class="bubble"></output>
                            <span class="start-range">1km</span>
							<span class="end-range">100Km</span>

                            <h6 style="margin-top: 5px;font-style: italic;">*Used if location is defined</h6>

                        </div>

                        <div class="category-sec-2">

                            <h2>Date</h2>

                            <ul class="list-checkbox">

                                <li>

                                    <label class="rad-style">

                                        <input type="radio" value="lm"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'lm') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Past 30 days</label>

                                </li>

                                <li>

                                    <label class="rad-style">

                                        <input type="radio" value="tod"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'tod') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Today</label>

                                </li>
<!--

-->
                                <li>

                                    <label class="rad-style">

                                        <input type="radio" value="nw"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) &&($_SESSION['sePra']['datefilter'] == 'nw')) {
                                                echo 'checked';
                                            } else {
                                              if (isset($_SESSION['sePra']['datefilter']) == false)
                                                echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>In the Next Week</label>

                                </li>

                                <li>

                                    <label class="rad-style">

                                        <input type="radio" value="nm"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'nm') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Next 30 days</label>

                                </li>
								<li>

                                    <label class="rad-style">

                                        <input type="radio" value="ad"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'ad') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Any date</label>

                                </li>
<!--
								<li>

                                    <label class="rad-style">

                                        <input type="radio" value="lw"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'lw') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>In the Last Week</label>

                                </li>


                                <li>

                                    <label class="rad-style">

                                        <input type="radio" value="yst"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'yst') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Yesterday</label>

                                </li>
								<li>

                                    <label class="rad-style">

                                        <input type="radio" value="tom"
                                               name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'tom') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Tommorow</label>

                                </li>
                                <li>

                                    <label class="rad-style">

                                        <input type="radio" value="sm" id="datefilter_month_select"  name="datefilter" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'sm') {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Select Month.</label>

										<select class="form-control" name="month_select" id="month_select" <?php if (isset($_SESSION['sePra']['datefilter']) && $_SESSION['sePra']['datefilter'] == 'sm') {}else{ echo 'style="display: none"'; } ?>>
										<?php
										$start_month = date('m')+1;
										$end_month = date('m')-1;
										$start_year = date('Y');
										for($m=$start_month; $m<=12; ++$m){
											if($start_month == 12 && $m==12 && $end_month < 12) {
											$m = 0;
											$start_year = $start_year+1;
											}
										?>
												<option value="<?php echo date('Y-m', mktime(0, 0, 0, $m, 1, $start_year)).'-1'; ?>"
												<?php if (isset($_POST['month_select']) && $_POST['month_select'] == date('Y-m', mktime(0, 0, 0, $m, 1, $start_year)).'-1') { echo 'selected'; } ?>
												>
													<?php echo date('F Y', mktime(0, 0, 0, $m, 1, $start_year)); if($m == $end_month) break; ?>
												</option>
											<?php } ?>
										</select>

                                </li>
-->
                            </ul>

                            <h2>Price</h2>

                            <ul class="list-checkbox">

                                <li>

                                    <label class="rad-style">

                                        <input type="radio" name="pricefilter"
                                               value="1" <?php if (isset($_SESSION['sePra']['pricefilter']) && $_SESSION['sePra']['pricefilter'] == 1) {
                                            echo 'checked';
                                        } else {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Any Price</label>

                                </li>

                                <li>

                                    <label class="rad-style">

                                        <input type="radio" name="pricefilter"
                                               value="0" <?php if (isset($_SESSION['sePra']['pricefilter']) && $_SESSION['sePra']['pricefilter'] == 0) {
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>Free Events Only</label>

                                </li>

                            </ul>

                        </div>

                    </div>

                    <div class="category-sec-1">

                        <h2>Category</h2>

                        <ul class="list-checkbox">

                            <?php


                            $categoryList = $wpdb->get_results("SELECT * FROM api_category order by title ASC");

                            $i = 0;
                            foreach ($categoryList as $row) {
                            $i++; ?>

                            <li>

                                <label class="maincheck">

                                    <input type="checkbox"
											class="homecat_<?php echo $row->api_cat_id; ?>"
                                           name="filter_category[]" <?php echo ($row->api_cat_id == 17) ? 'id="all_cats"' : ''; ?>
                                           value="<?php echo $row->api_cat_id; ?>"
                                           <?php echo (($row->api_cat_id == 17) && (empty($_SESSION['sePra']['filter_category']))) ? "checked" : "" ?>
                                            <?php echo (isset($_SESSION['sePra'])) ? (in_array($row->api_cat_id, $_SESSION['sePra']['filter_category']) ? 'checked' : '') : ""; ?> <?php echo (isset($_SESSION['sePra'])) ? (($row->api_cat_id == 17 && count($_SESSION['sePra']['filter_category']) == 0) ? 'checked' : '') : ""; ?>>
                                    <span class="checkmstyle"></span><?php echo $row->title; ?>

                                </label>

                            </li>

                            <?php if ($i % 13 == 0 && $i != count($categoryList)){ ?>

                        </ul>

                    </div>

                    <div class="category-sec-1">

                        <h2>&nbsp;</h2>

                        <ul class="list-checkbox">

                            <?php } ?>

                            <?php } ?>

                        </ul>

                    </div>


                    <div class="btn-filter-section">

                        <button class="reset deselectFilter" type="button" name="btnReset">Reset</button>
                        <button class="reset" type="button" id="div_close"
                           onclick="closefilter()">Close</button>
                    </div>



                </div>
				</form>

            </div>

            <!-- #content-area -->

            <div class="row ms-0 me-0 home-slider">

                <!-- Category slider start -->

                <div class="search-catgory">

                    <h1>LOVE WHERE YOU’RE AT</h1>

                    <p>Explore Events by Category <img
                                src="<?php echo site_url(); ?>/wp-content/uploads/2019/06/play.png"></p>

                </div>

                <div class="slider_catgory">

                    <ul id="owl-one" class="owl-carousel owl-theme home-catg">

                        <?php foreach ($categoryList as $row) { ?>

                            <li class="item">

                                <a onClick="performCatSearch(<?php echo $row->api_cat_id; ?>)" href="javascript:void(0);">

                     <span>

					 <!--img src="<?php //echo ($row->image) ? 'https://storage.cloud.google.com/'.$row->image->bucket.'/'.$row->image->filename : site_url().'/wp-content/uploads/2019/06/f1.jpg';?>"-->

					 <img src="<?php echo site_url(); ?>/wp-content/plugins/category-manage/inc/uploads/<?php echo $row->image_id ?>">

					 </span>

                                    <h3 class="catg-title"><?php echo $row->title; ?></h3>

                                    <p class="catg-text" style="color:#000;"><?php echo $row->description; ?></p>

                                </a>

                            </li>

                        <?php } ?>

                    </ul>

                </div>
            </div>

            <!-- Category slider  end -->

        </div>

    </div>

    <div class="outer-wrapper">

        <div class="container container-home">

            <div class="row">
            
                <!-- Post Grid start -->

                <div id="gridpost">

                    <h2>In the Community</h2>

                    <?php $catquery = new WP_Query('posts_per_page=3'); ?>

                    <?php while ($catquery->have_posts()) : $catquery->the_post(); ?>

                        <div class="col-md-4 col-sm-6 col-12 d-block">
                            <div class="post_item">

                            <span class="entry-date"><?php echo get_the_date('M d'); ?></span>

                            <div class="postimage">

                                <a href="<?php the_permalink(); ?>"
                                   title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('category-thumbnail'); ?></a>

                            </div>

                            <h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>

                            <div class="post-exerpt"><?php the_excerpt(); ?></div>

                            <div class="post-btn"><a href="<?php the_permalink() ?>" rel="bookmark">Read More <img
                                            src="<?php echo site_url(); ?>/wp-content/uploads/2019/09/right-arrrow-1.png"></a>
                            </div>

                        </div>
                        </div>

                    <?php endwhile; ?>

                    <?php wp_reset_postdata(); ?>

                </div>
             </div>
            </div>

            <!-- Post Grid end -->

        </div>

    </div>

    <div class="outer-wrapper grey">

        <div class="container container-home">

            <div class="row1">

                <?php dynamic_sidebar('Welcome To Neighbur '); ?>


                <div class="bottom-icon">

                    <?php //dynamic_sidebar('Bottom Icons'); ?>

                    <ul id="owl-two" class="owl-carousel owl-theme">

                        <?php $services = new WP_Query('post_type=service&posts_per_page=100'); ?>

                        <?php while ($services->have_posts()) : $services->the_post(); ?>

                            <li class="item"><a
                                        href="<?php the_permalink(); ?>"><?php the_post_thumbnail('category-thumbnail'); ?></a>

                                <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>

                            </li>

                        <?php endwhile; ?>

                        <?php wp_reset_postdata(); ?>

                    </ul>

                </div>

            </div>

        </div>

    </div>

    <!-- .container -->

</div>

<!-- #main-content -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js"
        integrity="sha256-/7FLTdzP6CfC1VBAj/rsp3Rinuuu9leMRGd354hvk0k=" crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function ($) {

        $(document).on('change', '#filter_div input[type="checkbox"]', function (e) {
          jQuery('#filtertext').text('Filter changed. Search to apply.');
            if ($(this).attr('id') != 'all_cats') {
                if ($(this).is(':checked'))
                    $('#all_cats').prop('checked', false);
                else {
                    var l = $(':checkbox[name="filter_category[]"]')
                        .filter(':checked')
                        .not('#all_cats').length;

                    if (l == 0)
                        $('#all_cats').prop('checked', true);
                }
            } else {
                if ($(this).is(':checked')) {
                    $('#filter_div input:checkbox').each(function (index, element) {
                        if ($(element).attr('id') !== 'all_cats') {
                            $(element).prop('checked', false);
                        }
                    });
                }
            }

        });

        // select month
        $(document).on('click', '#filter_div input[name="datefilter"]', function (e) {
          jQuery('#filtertext').text('Filter changed. Search to apply.');

            if ($(this).attr('id') == 'datefilter_month_select') {

                $(this).parent().next().show();
                $('#month_select').attr('disabled', false); // enable to post value
            } else {

                $('#month_select').hide();
                $('#month_select').attr('disabled', true); // prevent to post value
            }
        });

        $(document).on('click', '#filter_div input[name="pricefilter"]', function (e) {
            jQuery('#filtertext').text('Filter changed. Search to apply.');
          });


    });
</script>

<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/searchmapautofil.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw&libraries=places&callback=initAutocomplete"
        async defer></script>
        

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>


<?php

get_footer(); ?>


<script>


    var slider = document.getElementById("myRange");

    var output = document.getElementById("sliderValue");

    output.innerHTML = slider.value;

    slider.oninput = function () {

        output.innerHTML = this.value;

    }

    jQuery(function ($) {

        /* var el=$('input:checkbox[name="filter_category[]"]'); */

        var el = $('input:checkbox[id="all_cats"]');

    });

</script>

<style>

    .filter-icon {

        float: left;

        width: 100%;

        padding: 10px 14px 0px;

        font-size: 17px;

        margin-top: 35px;

    }

    .filter-icon span {

        color: #BDBDBD;

        font-size: 20px;
    }
    .filter-icon img {

        width: 35px;
        position: relative;
       top: 4px;
    right: 2px;
    }
    .Search-radius {
        float: right;
        width: 25%;

    }

    .search_inputs {

        float: left;

        width: 100%;

    }

    .search_inputs input {

        width: 40%;

        padding: 15px 11px 12px 11px;

    }

    .search_inputs button {

        width: 20%;

        padding: 12px 0px;

        background-color: #f56d3a;

        border: 0px;

    }

    .search_inputs .btn-search {

        color: white;

        text-decoration: none;

        font-size: 19px;

        letter-spacing: 1px;

    }

    ::placeholder {

        font-size: 16px;

    }

    .search-main-section {

        float: left;

        padding: 20px;

        box-sizing: border-box;

        width: 100%;

        border: 3px solid #e6e8ee;

        margin-top: 30px;

    }

    .category-sec-1 {

        width: 25%;

        float: left;

    }

    ul.list-checkbox {

        padding-left: 0px;

        margin-top: 0px;

        float: left;

        width: 100%;

        margin-bottom: 15px;

    }

    .Search-radius h4 {

        text-transform: capitalize;

        color: #988c8c !important;

        font-weight: normal;

        padding-bottom: 20px !important;

        padding-top: 0;

        text-align: left;

    }

    .btn-filter-section {

        width: 100%;

        float: left;
        margin-top: -10px;

    }

    button.applyfilters {

        padding: 10px 25px;

        background-color: #f56d3a;

        border: 0px;

        font-size: 21px;

        color: #fff;

        border-radius: 3px;

    }

    button.reset {

        background-color: transparent !important;

        border: 0px;

        font-size: 20px;

        color: #f56d3a;

        padding: 0 16px;

    }

    button.close-button {

        background-color: transparent !important;

        border: 0px;

        font-size: 20px;

        color: #333;

    }

    .search-main-section h1, .search-main-section h2 {

        font-size: 20px;

        color: #333333b5 !important;

        font-weight: bold;

        width: 100%;

        text-align: left;

    }

    span.end-range {

        float: right;

        color: #9c9a9a;

    }

    span.mid-range {

        width: 65%;

        display: inline-block;

        text-align: center;

    }

    span.start-range {

        color: #9c9a9a;

    }

    .slidecontainer {

        width: 100%;

        margin-bottom: -7px; /* Width of the outside container */

    }


    /* css for checkbox styling */

    .maincheck {

        display: block;

        position: relative;

        padding-left: 24px;

        margin-bottom: 5px;

        cursor: pointer;

        font-size: 16px;

        -webkit-user-select: none;

        -moz-user-select: none;

        -ms-user-select: none;

        user-select: none;

    }

    /* Hide the browser's default checkbox */

    .maincheck input {

        position: absolute;

        opacity: 0;

        cursor: pointer;

        height: 0;

        width: 0;

    }

    /* Create a custom checkbox */

    .checkmstyle {

        position: absolute;

        top: 4px;

        left: -2px;

        height: 19px;

        width: 19px;

        background-color: #ffffff;

        border: 2px solid #80808080;

        border-radius: 2px;

    }

    /* When the checkbox is checked, add a blue background */

    .maincheck input:checked ~ .checkmstyle {

        background-color: #f56d3a;

        border-color: #f56d3a;

    }

    /* Create the checkmark/indicator (hidden when not checked) */

    .checkmstyle:after {

        content: "";

        position: absolute;

        display: none;

    }

    /* Show the checkmark when checked */

    .maincheck input:checked ~ .checkmstyle:after {

        display: block;

    }

    /* Style the checkmark/indicator */

    .maincheck .checkmstyle:after {

        left: 4px;

        top: 0px;

        width: 4px;

        height: 10px;

        border: solid white;

        border-width: 0 2px 2px 0;

        -webkit-transform: rotate(45deg);

        -ms-transform: rotate(45deg);

        transform: rotate(45deg);

    }

    /* for radio button style */

    .rad-style {

        display: block;

        position: relative;

        padding-left: 28px;

        margin-bottom: 2px;

        cursor: pointer;

        font-size: 16px;

        -webkit-user-select: none;

        -moz-user-select: none;

        -ms-user-select: none;

        user-select: none;

    }

    /* Hide the browser's default radio button */

    .rad-style input {

        position: absolute;

        opacity: 0;

        cursor: pointer;

    }

    /* Create a custom radio button */

    .checkstyleradio {

        position: absolute;

        top: 3px;

        left: 0;

        height: 21px;

        width: 21px;

        background-color: #fff;

        border-radius: 50%;

        border: 2px solid #bfbfbf;

    }

    /* When the radio button is checked, add a blue background */

    .rad-style input:checked ~ .checkstyleradio {

        background-color: #ffffff;

        border-color: #f56d3a;

    }

    /* Create the indicator (the dot/circle - hidden when not checked) */

    .checkstyleradio:after {

        content: "";

        position: absolute;

        display: none;

    }

    /* Show the indicator (dot/circle) when checked */

    .rad-style input:checked ~ .checkstyleradio:after {

        display: block;

    }

    /* Style the indicator (dot/circle) */

    .rad-style .checkstyleradio:after {

        top: 2px;

        left: 2px;

        width: 13px;

        height: 13px;

        border-radius: 50%;

        background: #f56d3a;

    }


    /* --------------------------- Range slider ----------------------- */

    .range-wrap {

        position: relative;
        margin-top: 22px;

    }

    .range {

        width: 100%;

    }

    .bubble {

        top: -24px;

        position: absolute;

        left: 50%;

        transform: translateX(-50%);

    }


    input[type=range] {

        -webkit-appearance: none;

        width: 100%;

        background: transparent;

        border: 0;

        margin: 0 !important;

        padding: 0 !important;

    }

    input[type=range]:focus {

        outline: none;

    }

    input[type=range]::-webkit-slider-runnable-track {

        width: 100%;

        height: 12px;

        cursor: pointer;

        animate: 0.2s;

        background: #f56d3a;

        border-radius: 6px;

    }

    input[type=range]::-webkit-slider-thumb {

        height: 25px;

        width: 25px;

        border-radius: 25px;

        background: #e25d2b;

        cursor: pointer;

        -webkit-appearance: none;

        margin-top: -8px;

    }

    input[type=range]:focus::-webkit-slider-runnable-track {

        background: #ef6835;

    }

    input[type=range]::-moz-range-track {

        width: 100%;

        height: 12px;

        cursor: pointer;

        animate: 0.2s;

        background: #f56d3a;

        border-radius: 6px;

    }

    input[type=range]::-moz-range-thumb {

        height: 25px;

        width: 25px;

        border-radius: 25px;

        background: #e25d2b;

        cursor: pointer;


    }

    input[type=range]::-ms-track {

        width: 100%;

        height: 12px;

        cursor: pointer;

        animate: 0.2s;

        background: transparent;

        border-color: transparent;

        border-width: 16px 0;

        color: transparent;

    }

    input[type=range]::-ms-thumb {

        height: 25px;

        width: 25px;

        border-radius: 25px;

        background: #e25d2b;

        cursor: pointer;

        margin-top: -1px;

    }

    input[type=range]::-ms-fill-lower {

        background: #ef6835;

        border-radius: 2.6px;

    }

    input[type=range]::-ms-fill-upper {

        background: #ef6835;

        border-radius: 2.6px;

    }

    input[type=range]:focus::-ms-fill-lower {

        background: #ef6835;

    }

    input[type=range]:focus::-ms-fill-upper {

        background: #ef6835;

    }


    @supports (-ms-ime-align: auto) {


        #myRange {

            margin-bottom: -20px !important;

        }

        .bubble {

            top: -10px;

            width: 70px;

            text-align: center;

        }

        .range-wrap {

            position: relative;

            margin-top: 0px;

        }

    }


</style>

<script type="text/javascript">

    const allRanges = document.querySelectorAll(".range-wrap");

    allRanges.forEach(wrap => {

        const range = wrap.querySelector(".range");

        const bubble = wrap.querySelector(".bubble");


        range.addEventListener("input", () => {
            jQuery('#filtertext').text('Filter changed. Search to apply.');
            setBubble(range, bubble);

        });

        setBubble(range, bubble);

    });


    function setBubble(range, bubble) {

        const val = range.value;

        const min = range.min ? range.min : 0;

        const max = range.max ? range.max : 100;

        const newVal = Number(((val - min) * 100) / (max - min));

        bubble.innerHTML = val + ' Km';


        bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;

    }

</script>
