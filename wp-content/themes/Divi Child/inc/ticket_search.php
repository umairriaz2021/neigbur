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
                    $estart = strtotime($edate->start_date);
                    $eend = strtotime($edate->end_date);
                    ?>
                    <!--Date Variables Ends-->
                    <?php endif; ?>
                    <?php if (date('Y-m-d',$estart) == date('Y-m-d',$eend)):?>
                    
                    <p class="r-date">
                                                <span class="s-day"><?php echo date('l', strtotime($edate->start_date)) ?></span>
                                                <span class="s-date"><?php echo date('F j, Y', strtotime($edate->start_date)) ?></span>
                                              
                        </p>
                    <?php else: ?>
                    
                     <p class="r-date">
                    <span class="s-day"><?php echo date('l', strtotime($edate->start_date)) ?></span>
                        <span class="s-date"><?php echo date('F j, Y', strtotime($edate->start_date)) ?></span>
                                            
                                            
                                            
                                            
                                            
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
                     add_query_arg( 'tid', $row->ticketTypes[0]->id, $url);
                    ?>
                   
                    <?php if ($row->ticketTypes[0]->price == 0): ?>
                    <p class="btn-buy"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id;?>/?tid=<?php echo $row->ticketTypes[0]->id;?>" class="ticket-paid" value="<?php echo $row->ticketTypes[0]->price;?>">Free Tickets</a></p>
                    <?php elseif($row->ticketTypes[0]->price > 0): ?>
                    <p class="btn-buy"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id;?>/?tid=<?php echo $row->ticketTypes[0]->id; ?> " class="ticket-paid" value="<?php echo $row->ticketTypes[0]->price;?>">Buy Tickets</a></p>
                   
                    <?php endif; ?>
                    <!--Ticket Conditions Ends-->
                     <?php else: ?>
                    <p class="btn-buy"><a href="<?php echo site_url() ?>/view-event/<?php echo $row->id; ?>" class="ticket-paid">Details</a></p>
                    </li>
                    
                 
                    <?php endif; ?>