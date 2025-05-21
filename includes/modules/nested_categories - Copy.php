<?php 

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();
?>
<?php
	#################################################################
	//Get the data for the cPath
	$status='';
	$plan_id='';
	$status = $category['categories_status'];
	$plan_id = $category['plan_id'];
	$categories_content='';
	######################################################
	// set the variable
	$show_the_plan=0;

	$status_categories_query = tep_db_query("select categories_status from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cat. "'");
			while ($status_categories = tep_db_fetch_array($status_categories_query)) 
			{
			if ($status == 1) $show_the_plan=1;
			}
	
	//echo $show_the_plan;

	//if the category status is enabled
	if ($show_the_plan==1)
	{
	 //$the_plan=3;//sub category seat plans

		if(($plan_id==3)or($plan_id==5)or($plan_id==6)or($plan_id==7)or($plan_id==8))
		{
		//we run the seat plan class
		require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/seatplan.php');
		
		//}
		//echo '<br>';
		//do we allow some GA products?
		//require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/all_products_ga.php');
		}
		require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/products_ga.php');
		if (SHOW_LOGIN == 'yes') 
		{
		include(DIR_FS_CATALOG.DIR_WS_MODULES. '/double_boxes.php');
		}
		
	}else
	{
		if(SHOW_DISABLED_CATEGORIES=='true')
		{
		echo "<h4>" . EVENT_DISABLED_MESSAGE . "</h4>";
		}else{
		tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL'));
		
		//echo "<h4>" . EVENT_DISABLED_MESSAGE . "</h4>";	
		}
		//allow BO to see the plan even when it is disabled
		if (($_SESSION['BoxOffice']== 999)or($_SESSION['customer_country_id']==999))	
		{
		require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/seatplan.php');
		require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/products_ga.php');
	//	require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/all_products_ga.php');
		}else
		{
			//echo 'No Plan';
		}
	}
		

		
		 if ($plan_id==4)
		 {			
			$cat = $cPath;	
		 }
		 elseif ($plan_id==3)
		 {
			$cat = $parent_id;
		 }
		 
		
	$sql_order_by = FEATURED_CATEGORIES_ORDERBY;	
	//Now we are going to show the gallery
	$nested_categories_query = tep_db_query("select * from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status >'0' and c.parent_id = '" . (int)$cat . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$FSESSION->languages_id . "' order by c." .$sql_order_by . " ASC");
	
	$num_categories = tep_db_num_rows($nested_categories_query);

	if ($num_categories > 0) 
	{

		while ($nested_categories = tep_db_fetch_array($nested_categories_query)) 
		{
			//print_r($nested_categories);
			$categories_venue_id = $nested_categories['venue_id'];
			$categories_status = $nested_categories['categories_status'];
			$categories_name = $nested_categories['categories_name'];	
			$categories_description = $nested_categories['categories_description'];	
			//$categories_description = ltrim(substr($categories_description, 0, 75) . '...'); //Trims and Limits the desc
			$categories_heading_title = $nested_categories["categories_heading_title"];
			$concert_unix = $nested_categories['concert_date_unix'];
			$categories_venue = $nested_categories['concert_venue'];
			$categories_date = $nested_categories['concert_date'];
			$categories_time = $nested_categories['concert_time'];
			$cPath_new = tep_get_path($nested_categories['categories_id']);
			$cPath_end = $nested_categories['categories_id'];
			$columns = 'col-lg-6 col-md-6 col-sm-6';
			
			require(DIR_WS_FUNCTIONS.'/date_formats.php');
				
				//about images
				if (($nested_categories['categories_image'] == 'NULL') or ($nested_categories['categories_image'] == '')) 
				{
					if(USE_CINE=='yes')
					{
					$cat_image="coming_soon_poster.jpg";
					}else
					{//Theatre
					$cat_image="theatre.png";	
					}
				}else
				{
					$cat_image=$nested_categories['categories_image'];
				}
				//system real time and date
				$eventdate = date('Ymd', $concert_unix);
				$event = $eventdate.$digit_time;
				$system = date('Hi');
				$today = date('Ymd');//date("F j, Y, g:i a");
				$now=$today.$system;

				//Bootstrap Button
				if(USE_CIRCLE_BUTTONS=='yes')
				{
				$button="btn btn-category-circle";
				}else
				{
				$button="btn btn-primary";	
				}

			//if(NESTED_CATEGORY_BUTTONS=='yes')
			if($plan_id<5)		
			{
				$categories_content .= '
				<div class="col-lg-3 col-md-4 col-sm-6" style="padding:10px;text-align:center;display:true">
				<div class="portfolio-item">
				<div class="portfolio-overlay">
				<div class="portfolio-info">
				<a class="' . $button . '" href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">
				' . $categories_name . '</a>
				</div>
				</div>
				</div>
				</div>
				';	
			}
			elseif($plan_id==9)	
			{
				
				if(($plan_id==9)&&($concert_unix==0))
						{
						$heading_date='';
						$heading_time='';
						$heading_venue='<h4>'.$categories_venue.'</h4>';
						$select=IMAGE_BUTTON_BUY_NOW;
						}else
						{
						 $heading_date='<h4>'.TEXT_DATE.' '.$heading_date.'</h4>';
						 $heading_time='<h4>'.$heading_time.'</h4>';
						 $heading_venue='<h4>'.$categories_venue.'</h4>';
						 if ($categories_venue_id == 'SOLD') 
							{
							$select=SOLD_OUT;
							$url = '<button class="btn btn-primary pull-right disabled">'.SOLD_OUT.'</button>';
							
							}else{
							$select=TEXT_SELECT_TICKETS;
							$url = '<a class="pull-right" href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_template_image_button('', $select) . '</a>';							
							}
						}
				
				//menu
				$categories_content .= '
					<div id="sub_listing" class="col-lg-12 col-md-12">
					<div class="row p-3 effects">
					<div class="col-md-2">
					' . tep_image(DIR_WS_IMAGES . /* 'small/' .*/ $cat_image, '', '', '') . '
					</div>
					<div class="col-md-4">
				
					'.$heading_date.'
					'.$heading_time.'

					</div>
					<div class="col-md-4">
					<h5>'.$heading_venue.'</h5>

					</div>
					<div class="col-md-2">'.$url.'</div>
					</div>
					</div>
					';

			}
		}
	}
	

	if(DISABLE_OVERLAY =='yes')
	{
	?>
	<style>
	#featured .featured-overlay {
	opacity: 0;
	}
	</style>
	<?php
	}

	//if (SHOW_NESTED_CATEGORIES == 'true' || ($parent_id == 0 && SHOW_NESTED_CATEGORIES == 'false')) {
	?>
	<section id="featured" class="rbe_eventlist" data-aos="fade-up">
	<div class="container">
	<div class="row no-gutters">
	<?php 
	//we show the menu
	echo $categories_content; 
	?>
	</div>
	</div>
	</section>
	<?php 
	//}
	
	if(SHOW_FEATURED_CATEGORIES == 'true')
	{
	require_once(DIR_WS_MODULES  . FILENAME_FEATURED_CATEGORIES);
	}
	?>  
	<br>