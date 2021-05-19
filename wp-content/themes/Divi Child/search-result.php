<?php
/*
Template Name: Search Result Page
*/

require(__DIR__.'/inc/beforesearch_result.php');

get_header(); ?>





<div id="main-content">
    <div class="outer-wrapper ">
        <div class="container container-home">
            <?php echo $searchrequesturl;  ?>
			<div class="alert alert-success" style="display: none;" id="success_msg">
				<p style="text-align: center;"><?php echo @$success; ?></p>
			</div>
			<?php if (@$success != '') { ?>
				<script>
				jQuery('#success_msg').show();
				setTimeout(function () {
				jQuery('#success_msg').hide();
				}, 5000);
				</script>
			<?php } ?>
			<form id="SearchForm" class="row align-items-center" action="<?php echo site_url() ?>/search-result/" method="POST">
            <div style="margin-top:30px;" class="serach-form result-page">

                    <div class="row no-gutters">
                        <div class="col">
                            <input type="text" class="form-control1 inSty" value="<?php echo @$_POST['search_event'] ?>"
                                   placeholder="Keyword (Optional)" name="search_event" id="search_event">
                        </div>
                        <div class="col">
                            <input id="autocomplete"
                                   name="location"
                                   value="<?php echo @$_POST['location'] ?>"
                                   placeholder="Location (Required)"
                                   onFocus="geolocate()"
                                   class="form-control1 inSty loc"
                                   type="text"/>
                            <input type="hidden" name="loclat" id="locLat" value="<?php echo @$_POST['loclat'] ?>">
                            <input type="hidden" name="loclong" id="locLong" value="<?php echo @$_POST['loclong'] ?>">
                            <input type="hidden" name="loclocation" id="locLocation" value="">
                        </div>
                        <button class="d-inline-block w-100 read_btn btn_search" type="submit" name="btnSearch">Search
                        </button>
                    </div>

            </div>

<?php

// check if filter was applied
function WasFilterApplied()
{
  if (isset($_POST['filter_category']) && count($_POST['filter_category']) > 0 && $_POST['filter_category'][0] != "17")
  {
    return true;
  }
  if (isset($_POST['radiusfilter']) && $_POST['radiusfilter'] != '' && $_POST['location'] != '' && $_POST['radiusfilter'] != '50')
  {
    return true;
  }

  if (isset($_POST['pricefilter']) && $_POST['pricefilter'] != "1")
  {
    return true;
  }

  if (isset($_POST['datefilter']) && $_POST['datefilter'] != 'nw')
  {
    return true;
  }

  return false;

}

?>


            <div class="filter-icon filterDiv" style="cursor: pointer;">
				<span width="40px" onclick="showfilter()"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/09/Filter-Icon-1.png">
                    <?php if (WasFilterApplied() == true) { ?>
                        <span id="filtertext">Filter applied</span>
						<!-- span id="filter_open"> <button class="reset deselectFilter" type="button" name="btnReset">Reset</button></span -->
                    <?php } else { ?>
                        <span id="filtertext">Filter Events</span>
						<!--span id="filter_open" style="display:none;"> <button class="reset deselectFilter" type="button" name="btnReset">Reset</button></span -->
                    <?php } ?>
				</span>

            </div>

            <div class="search-main-section filterDiv" id="filter_div" style="display:none;">

                <div class="Search-radius">
                    <h2>Search Radius</h2>
                    <div class="range-wrap">
                        <input type="range" class="range slider" name="radiusfilter" min="1" max="100"
                               value="<?php if (isset($_POST['radiusfilter']) && $_POST['radiusfilter'] != '') {
                                   echo $_POST['radiusfilter'] = '10';
                               } else {
                                   echo '50';
                               } ?>" id="myRange">
                        <output class="bubble"></output>
                    </div>
                    <span class="start-range">10km</span>
                    <span class="end-range">100Km</span>
                    <h6 style="margin-top: 5px;font-style: italic;">*Used if location is defined</h6>

                    <div class="category-sec-2">
                        <h2>Date</h2>
                        <ul class="list-checkbox">
                            <li>
                                <label class="rad-style">
                                    <input type="radio" value="lm"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'lm') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Past 30 days</label>
                            </li>

                            <li>
                                <label class="rad-style">
                                    <input type="radio" value="tod"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'tod') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Today</label>
                            </li>


                            <li>
                                <label class="rad-style">
                                    <input type="radio" value="nw"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'nw') {
                                        echo 'checked';
                                    } else {
                                      if (isset($_POST['datefilter']) == false)
                                            echo 'checked';
                                        } ?>><span class="checkstyleradio"></span>In the Next Week</label>
                            </li>
                            <li>
                                <label class="rad-style">
                                    <input type="radio" value="nm"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'nm') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Next 30 days</label>
                            </li>
							<li>
                                <label class="rad-style">
                                    <input type="radio" value="ad"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'ad') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Any date</label>
                            </li>
							<!--
                            <li>
                                <label class="rad-style">
                                    <input type="radio" value="lw"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'lw') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>In the Last Week</label>
                            </li>
                            <li>
                                <label class="rad-style">
                                    <input type="radio" value="yst"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'yst') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Yesterday</label>
                            </li>
							<li>
                                <label class="rad-style">
                                    <input type="radio" value="tom"
                                           name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'tom') {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Tommorow</label>
                            </li>

                            <li>
							<label class="rad-style">
								<input type="radio" value="sm" id="datefilter_month_select"  name="datefilter" <?php if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'sm') {                                            echo 'checked';                                        } ?>><span class="checkstyleradio"></span>Select Month.
							</label>
							<select class="form-control" name="month_select" id="month_select" <?php //if (isset($_POST['datefilter']) && $_POST['datefilter'] == 'sm') {}else{ echo 'style="display: none"'; } ?>>
							<?php /*
							$start_month = date('m')+1;
							$end_month = date('m')-1;
							$start_year = date('Y');
							for($m=$start_month; $m<=12; ++$m){
							if($start_month == 12 && $m==12 && $end_month < 12) {
							$m = 0;
							$start_year = $start_year+1;
							}
							?>
							<option value="<?php echo date('Y-m', mktime(0, 0, 0, $m, 1, $start_year)).'-1'; ?>"												<?php if (isset($_POST['month_select']) && $_POST['month_select'] == date('Y-m', mktime(0, 0, 0, $m, 1, $start_year)).'-1') { echo 'selected'; } ?>>
							<?php echo date('F Y', mktime(0, 0, 0, $m, 1, $start_year)); if($m == $end_month) break; ?>
							</option>
							<?php } */ ?>
							</select>
							</li>
							-->
                        </ul>
                        <h2>Price</h2>
                        <ul class="list-checkbox">
                            <li>
                                <label class="rad-style">
                                    <input type="radio" name="pricefilter"
                                           value="1" <?php if (isset($_POST['pricefilter']) && $_POST['pricefilter'] == 1) {
                                        echo 'checked';
                                    } else {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Any Price</label>
                            </li>
                            <li>
                                <label class="rad-style">
                                    <input type="radio" name="pricefilter"
                                           value="0" <?php if (isset($_POST['pricefilter']) && $_POST['pricefilter'] == 0) {
                                        echo 'checked';
                                    } ?>><span class="checkstyleradio"></span>Free Events Only</label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="category-sec-1">
                    <h2>Category</h2>
                    <ul class="list-checkbox">
                        <?php /* $i=0; foreach($categoryList->categories as $row) { $i++; */ ?>
                        <?php
                        $categoryList = $wpdb->get_results("SELECT * FROM api_category order by title ASC");
                        $i = 0;
                        foreach ($categoryList as $row) {
                        $i++; ?>
                        <li>
                            <label class="maincheck">

                                <input type="checkbox"
                                       name="filter_category[]" <?php echo ($row->api_cat_id == 17) ? 'id="all_cats"' : ''; ?>
                                       value="<?php echo $row->api_cat_id; ?>"
                                    <?php echo (($row->api_cat_id == 17) && (empty($_SESSION['sePra']['filter_category']))) ? "checked" : "" ?>
                                    <?php echo (isset($_SESSION['sePra'])) ? (in_array($row->api_cat_id, $_SESSION['sePra']['filter_category']) ? 'checked' : '') : ""; ?> <?php echo (isset($_SESSION['sePra'])) ?  (($row->api_cat_id == 17 && count($_SESSION['sePra']['filter_category']) == 0) ? 'checked' : '') : ""; ?>><span
                                        class="checkmstyle"></span><?php echo $row->title; ?>

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
                    <button class="reset" type="button" id="div_close">Close</a>
                </div>

            </div>
            <div class="search-main-section" id="filter_open" style="display:none;">
                <a href="#" class="close-button" id="btn_open">Filters Enabled-RESET</a>
            </div>
			</form>
            <div class="search-result-outer">

				<?php if(isset($events) && count($events)> 0){ ?>
		
				<span class="count_data">0</span> Results found
				<?php }else{ ?>
				<img class="no-search" src="<?php echo site_url() ?>/wp-content/uploads/2019/09/find-img.jpg">
        <?php
          if (isset($_POST['location']) == false || $_POST['location'] == '')
          {
            ?>
            <h3 class="sorry-title">Sorry, we can’t seem to find any events.</h3>
    				<p class="range-filter">Please choose a search location, or allow location permissions.</p>
            <?php
          }
          else
          {
            ?>
            <h3 class="sorry-title">Sorry, we can’t seem to find any events in <span id="resultlocation"><?php echo $_POST['location'] ?></span> </h3>
    				<p class="range-filter">Please adjust your filters to change the date range, ticket types<br/> or search radius and try again.</p>
    				<button class="all-evnt" type="button" onClick="searchAllinThisloaction(1)" name="searchall">RESET FILTERS AND SEARCH AGAIN</button>
            <?php
          }
          ?>
				<?php } ?>
               
                <ul class="owl-theme" id="myEvents">
                    <?php $i = 0; ?>
                    
                    <?php if(count($events) > 0):?>
                     <?php $count = 0;?>
                    
                     <?php foreach ($events as $row):?>
                    
                      <div class="count_d" style="display:none;"><?php echo $row->counts->count;?></div> 
                     <!--Event dates foreach starts-->
                     
                     
                      
                     
                      <li class="item event-detail" 
                      <?php if ($i > 4):?>
                        style="display:none;"
                        <?php endif;?>>
                        <a href="<?php echo site_url() ?>/view-event/<?php echo $row->id; ?>">
                        <p class="evt-img">
                        <!--Image If condition-->
                        <?php if($row->files[0]->type == 'image'): ?>
                        <?php 
                        $cat_im = 'https://storage.googleapis.com/' . $row->files[0]->bucket . '/' . $row->files[0]->filename;
                        ?>
                        <?php else: $cat_im = ''; ?>
                                                 
                        
                        <?php endif; ?>
                        <!--Image if condition ends-->
                        <!--Another Image if condition Starts -->
                        <?php if($cat_im && $cat_im != ''):  $event_image = $cat_im; ?>
                        
                        <?php else: $image_resp = getFileById($row->categorys[0]->image_id);
                                            $event_image = site_url() . '/wp-content/uploads/2019/06/f1.jpg'; ?>
                                            
                        
                        <?php endif; ?>
                        <!--Another Image if condition ends-->
                        <!--Image Tag -->
                      
                        <img src="<?php echo $event_image; ?>">
                                    </p>
                         <h2><?php echo $row->name; ?></h2>
                        </a>            
                        <!--Image tag & p tag & a tag Ends-->
                    <!--date time for tickets starts-->
                    <div class="date-time">

                    <p class="loc-icon"><i class="fa fa-calendar-o"></i></p>
                    <?php if(count($row->ticketTypes) !=0 ):?>
                    <?php if(!empty($row->ticketTypes)): ?>
                     
                    <!--Date Variables-->
                    <?php 
                    $estart = strtotime($row->event_dates[0]->start_date);
                    $eend = strtotime($row->event_dates[0]->end_date);
                  
                    ?>
                    <!--Date Variables Ends-->
                    <?php endif; ?>
                    <?php if (date('Y-m-d',$row->event_dates[0]->start_date) == date('Y-m-d',$row->event_dates[0]->end_date)):?>
                     
                    <p class="r-date">
                                                 <span class="s-day"><?php echo date('F j', strtotime($row->event_dates[0]->start_date)) ?></span>
                                                <span class="s-date"><?php echo date('g:i a', strtotime($row->event_dates[0]->start_date)) ?></span>
                                              
                        </p>
                    <?php else: ?>
                    
                     <p class="r-date">
                     <span class="s-day"><?php echo date('F j', strtotime($row->event_dates[0]->start_date)) ?></span>
                                                <span class="s-date"><?php echo date('g:i a', strtotime($row->event_dates[0]->start_date)) ?></span>
                                            
                                            
                                            
                                            
                                            
                        </p><br/>
                    <?php endif; ?>
                    
                    <!--date time for tickets Ends-->
                    <?php endif; ?>
                    <!--Location Starts-->
                    </div>
                        <div class="s-location">
                    <p class="loc-icon"><i style="font-size: 21px; padding-right: 4px;" class="fa fa-map-marker"></i></p>
                    <p class="r-date">
                  
                    <?php
                                    echo $row->city . ", " . $row->province->province_code . " " . $row->country->country_name;
                                    ?>
                                  
                                    </p>
                                </div>
                    <!--Location Ends
                    
                    <!--Ticket Buttons Start-->
                    <?php if (count($row->ticketTypes) > 0): 
                     $url = site_url().'/view-event/'.$row->id;
                     add_query_arg( array('tid'=>$row->ticketTypes[0]->id,'date'=>$estart), $url);
                    ?>
                   
                    <?php if ($row->ticketTypes[0]->price == 0): ?>
                    <p class="btn-buy"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id;?>/?tid=<?php echo $row->ticketTypes[0]->id;?>&abc=<?php echo $edate->start_date;?>" class="ticket-paid" value="<?php echo $row->ticketTypes[0]->price;?>">Free Tickets</a></p>
                    <p style="text-align:center;"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id;?>/?tid=<?php echo $row->ticketTypes[0]->id;?>&abc=<?php echo $edate->start_date;?>" style="color:red;">Event Details</a></p>
                    <?php elseif($row->ticketTypes[0]->price > 0): ?>
                    <p class="btn-buy"><a href="<?php echo site_url() ?>/get-tickets/<?php echo $row->id;?>/?tid=<?php echo $row->ticketTypes[0]->id; ?>&abc=<?php echo $edate->start_date;?> " class="ticket-paid" value="<?php echo $row->ticketTypes[0]->price;?>">Get Tix</a></p>
                    <p style="text-align:center;"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id;?>/?tid=<?php echo $row->ticketTypes[0]->id;?>&abc=<?php echo $edate->start_date;?>" style="color:red;">Event Details</a></p>
                    
                    <?php endif; ?>
                    <!--Ticket Conditions Ends-->
                     <?php else: ?>
                    <p class="btn-buy"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id; ?>" class="ticket-paid">Details</a></p>
                    
                    </li>
                    
                 
                    <?php endif; ?>
                    
                    <!--Ticket Count Ends-->
                    <!--Ticket Buttons Ends-->
                   

                 
                     <!--Event Dates foreach ends-->
                     
                     <?php endforeach; ?>
                    
                    <?php endif; ?>
                    </ul>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    

                <?php if ($i > 8) { ?>
                    <div id="loadMore" data-total="<?php echo $i; ?>">Load more</div>
                <?php } ?>
            </div>
            <div style="display:none;" class="search-result-outer recommended-for">
                <h3>Recommended for You</h3>
                <ul class="owl-theme">
                    <?php if (isset($events)) {
                        $i = 1;
                        foreach ($events as $row) {
                            if ($i <= 4) { ?>
                                <?php /* echo "<pre>"; print_r($row); echo "</pre>"; */ ?>
                                <?php if (empty($row->photos)) { ?>
                                    <li class="item">
                                        <a href="<?php echo site_url() ?>/view-event?event=<?php echo $row->id; ?>">
                                            <img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
                                        </a>
                                    </li>
                                <?php } else { ?>
                                    <li class="item">
                                        <a href="<?php echo site_url() ?>/view-event?event=<?php echo $row->id; ?>">
                                            <img src="https://storage.googleapis.com/<?php echo $row->photos[0]->file->bucket . '/' . $row->photos[0]->file->filename; ?>">
                                        </a>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                            <?php $i++;
                        }
                    } ?>
                </ul>
            </div>
        </div>
        <!-- #content-area -->
    </div>
    <!-- #End Main -->
</div>

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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw&libraries=places&callback=initAutocomplete" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
<?php if(!isset($_POST['location']) || $_POST['location'] == ''){ ?>
<script> geolocate(); </script>
<?php } ?>
<script>
    var slider = document.getElementById("myRange");
    var output = document.getElementById("sliderValue");
    output.innerHTML = slider.value;
    slider.oninput = function () {
        output.innerHTML = this.value;
        jQuery('#filtertext').text('Filter changed. Search to apply.');
    }

    $(function () {
        $('.list-checkbox').on('change', function(e) {
          jQuery('#filtertext').text('Filter changed. Search to apply.');
        });

        var el = $('input:checkbox[name="filter_category[]"]');
        /* var el=$('input:checkbox[id="all_cats"]'); */
        el.on('change', function (e) {
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
                if ($(this).is(':checked'))
                    el.not($(this)).prop('checked', false);
            }
        });
    });
</script>
<style>

    .Search-radius {
        float: right;
        width: 25%;
    }
	recommended-for{
		margin-top: 80px;
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
    }

    .filter-icon {
        float: left;
        width: 100%;
        padding: 15px 14px 22px;
        font-size: 17px;
    }

    .filter-icon span {
        color: #BDBDBD;
        font-size: 20px;
    }

    .filter-icon img {
        width: 40px;
        position: relative;
        top: 11px;
        right: 10px;
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
        width: 66%;
        display: inline-block;
        text-align: center;
    }

    span.start-range {
        color: #9c9a9a;
    }

    .slidecontainer {
        width: 100%; /* Width of the outside container */
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


    /* Responsive */
    @media only screen and (min-width: 320px) and (max-width: 480px) {
        .category-sec-1 {
            width: 100%;
        }

        .Search-radius {
            width: 100%;
            margin-bottom: 11px;
        }

        .search_inputs input {
            width: 40%;
            padding: 13px 7px 12px 9px;
        }

        .search_inputs button {
            padding: 14px 0px;
        }

        .search_inputs .btn-search {
            font-size: 14px;
        }

        ul.list-checkbox {
            margin-bottom: 0px;
        }
    }

    @media only screen and (min-width: 768px) and (max-width: 1023px) {
        .category-sec-1 {
            width: 25%;
            float: left;
        }

        .Search-radius {
            width: 25%;
        }

        .maincheck {
            font-size: 15px;
        }

        .rad-style {
            font-size: 15px;
        }
    }

    label#search_event-error {
        top: 108 !important;
    }



    #month_select {

        display: flex;
        flex-direction: row;
    }

    #datemonth_from, #datemonth_to {
        padding: 13px 0;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.css"
      integrity="sha256-b88RdwbRJEzRx95nCuuva+hO5ExvXXnpX+78h8DjyOE=" crossorigin="anonymous"/>

<?php get_footer(); ?>
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
        bubble.innerHTML = val + ' km';

        bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
    }
    jQuery(document).ready(function(){
        var count_val = jQuery('.count_d').text();
 
        jQuery('.count_data').html(count_val);
    });
</script>
