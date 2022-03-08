<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  




add_action( 'wp_enqueue_scripts', 'pasal_ecommerce_child_style' );
				function pasal_ecommerce_child_style() {
					wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
					wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
				}
				
		
/**
 * Your code goes below.
 */
						

        require_once (dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php');
		require_once (ABSPATH . 'wp-includes/wp-db.php');
		require_once (ABSPATH . 'wp-admin/includes/taxonomy.php');
		require_once (dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');
//action for custom filter leftbar





// function html2wp_theme_setup(){
   

	


//     register_nav_menus( array(
// 		'footer' => __('Mobile Menu', 'html2wp'),
// 		'secondary menu' => __('secondary Menu', 'html2wp')
//     ) );
	

// };




add_action('custom_filter_leftbar','custom_filter_keys',10,1);
function custom_filter_keys($main_cat_id){
					global $wpdb;
					$all_default_product_data=$wpdb->get_results("SELECT object_id from ef_term_relationships WHERE term_taxonomy_id=".$main_cat_id."",ARRAY_A);
					
					$default_product_ids=array();

				foreach($all_default_product_data as $val){
					$default_product_ids[]=$val['object_id'];
				}
	
	$args = array(
       'hierarchical' => 1,
       'show_option_none' => '',
       'hide_empty' => 0,
       'parent' => 23,
	   'orderby' => 'ID',
	   'order' => 'ASC', 
       'taxonomy' => 'product_cat'
    );
  $product_filters = get_categories($args);

		  echo '<div class="main-filters" style="top:20%">';
			echo '<div class="dropdown">';				
foreach ( $product_filters as $product_filter ) {
	
	static $i=0;
	$i++;
	
	
 if($i<=2){
	 echo '<div class="grey-bg filterbtn dropdown-toggle" type="button">';
	
	 echo $product_filter->name;
	 echo '</div>';
	 
				echo '<ul class="filter-dropdown-menu list-unstyled menu-lk">';

	$args = array(
       'hierarchical' => 1,
       'show_option_none' => '',
       'hide_empty' => 0,
       'parent' => $product_filter->term_id,
       'taxonomy' => 'product_cat'
    );
  $product_sub_filters = get_categories($args);

foreach ( $product_sub_filters as $product_sub_filter ) {
	
				$thumbnail_id = get_term_meta( $product_sub_filter->term_id, 'thumbnail_id', true ); 
				$image = wp_get_attachment_url( $thumbnail_id );
				if($image==null){
					$image="https://eyefoster.in/wp-content/uploads/2021/01/ezgif-2-5406af6daeb3.jpg";
				}				
				
	
					echo '<input type="hidden" value="'.$product_filter->name.'" id="parentOf'.$product_sub_filter->term_id.'">';
					echo '<input type="hidden" value="'.$product_sub_filter->name.'" id="childName'.$product_sub_filter->term_id.'">';
					echo '<li class="list-image">';
						echo '<div class="listItem" id="'.$product_sub_filter->term_id.'" onclick="initialPP('.$product_sub_filter->term_id.')">';
							echo '<span title="'.$product_sub_filter->name.'">';
								echo '<img class="innerImg" src="'.$image.'">';
								echo '<span>';
							  echo $product_sub_filter->name;
							  echo '</span>';
							echo '</span>';
						echo '</div>';
					echo '</li>';
}
echo '</ul>';
 }elseif($i==3){
	 echo '<div class="grey-bg filterbtn dropdown-toggle" type="button">';
	
	 echo $product_filter->name;
	 echo '</div>';
	 
				echo '<ul class="filter-dropdown-menu list-unstyled menu-lk">';

	$args = array(
       'hierarchical' => 1,
       'show_option_none' => '',
       'hide_empty' => 0,
       'parent' => $product_filter->term_id,
       'taxonomy' => 'product_cat'
    );
  $product_sub_filters = get_categories($args);

foreach ( $product_sub_filters as $product_sub_filter ) {
	
				$current_id_product=$wpdb->get_results("SELECT object_id from ef_term_relationships WHERE term_taxonomy_id=".$product_sub_filter->term_id."",ARRAY_A);
			
					$curren_product_ids=array();

				foreach($current_id_product as $val){
					if(in_array($val['object_id'],$default_product_ids)){
					$curren_product_ids[]=$val['object_id'];
					}
				}

				echo '<input type="hidden" value="'.$product_filter->name.'" id="parentOf'.$product_sub_filter->term_id.'">';
				echo '<input type="hidden" value="'.$product_sub_filter->name.'" id="childName'.$product_sub_filter->term_id.'">';
					echo '<li>';
					echo '<div class="listItem">';
					echo '<label>';
						echo '<input type="checkbox" id="'.$product_sub_filter->term_id.'" onclick="fetchData('.$product_sub_filter->term_id.')">';	
						echo '<span>';
					    echo $product_sub_filter->name.' ('.count($curren_product_ids).')';
						echo '</span>';
						 echo '</label>' ;
					echo '</div>';
					echo '</li>';
	
}
echo '</ul>';
	 
 }else{
	 echo '<div class="dropdown second-filter-dropdown ">';
	 echo '<div class="white-bg btn btn dropdown-toggle"  type="button" onclick=showFilterKeyList("list'.$i.'")>';
	  echo $product_filter->name;
	 echo '</div>';
	 echo '<div class="customFilterCopy">';
	 echo '<ul class="filter-dropdown-menu list-unstyled not-click-container menu-lk " id="list'.$i.'" style="display:none">';
	 $args = array(
       'hierarchical' => 1,
       'show_option_none' => '',
       'hide_empty' => 0,
       'parent' => $product_filter->term_id,
       'taxonomy' => 'product_cat'
    );
  $product_sub_filters = get_categories($args);

foreach ( $product_sub_filters as $product_sub_filter ) { 
					$current_id_product=$wpdb->get_results("SELECT object_id from ef_term_relationships WHERE term_taxonomy_id=".$product_sub_filter->term_id."",ARRAY_A);
			
					$curren_product_ids=array();

				foreach($current_id_product as $val){
					if(in_array($val['object_id'],$default_product_ids)){
					$curren_product_ids[]=$val['object_id'];
					}
				}

	echo '<input type="hidden" value="'.$product_filter->name.'" id="parentOf'.$product_sub_filter->term_id.'">';
	echo '<input type="hidden" value="'.$product_sub_filter->name.'" id="childName'.$product_sub_filter->term_id.'">';
	 echo '<li class="list-checkbox">';
	 echo '<div class="listItem">';
	 echo '<label>';
	 echo '<input type="checkbox" id="'.$product_sub_filter->term_id.'" onchange="fetchData('.$product_sub_filter->term_id.')">';
	 echo '<span>';
	 echo $product_sub_filter->name.' ('.count($curren_product_ids).')';
	 echo '</span>' ;
	 echo '</label>' ;
	 echo '</div>' ;
	 echo '</li>' ;
 }
 echo '</ul>' ;
 	 echo '</div>' ;
 	 echo '</div>' ;
}
}			

echo '<input type="hidden" id="totalFilterHeading" value="'.$i.'">';			

echo '</div>';   
echo '</div>';  

}
//hook to return wc product by ajax

 add_action('wp_ajax_get_refund_data', 'get_wc_products');
 add_action('wp_ajax_nopriv_get_refund_data','get_wc_products');
		
		
		function array_make_group_with_repeatation(array $array){
			// echo $flag+1;exit;
			// return array_unique(array_diff_assoc($array, array_unique($array)));
			return(array_count_values($array));
		}
		
		   function matched_cart_items( $search_products ) {
					$count = 0;
					if ( ! WC()->cart->is_empty() ) {
						foreach(WC()->cart->get_cart() as $cart_item ) {
							$cart_item_ids = array($cart_item['product_id'], $cart_item['variation_id']);
							if( ( is_array($search_products) && array_intersect($search_products, cart_item_ids) ) 
							|| ( !is_array($search_products) && in_array($search_products, $cart_item_ids))){
								$count++;
							}
						}
					}
					return $count; 
				}
		
		
		
			
		
   function get_wc_products() {
	    
	   global $wpdb;
		$html="";
		$category=$_POST['category_id'];	
		$main_cat_id=$_POST['main_category'];	
		$term=array();
		foreach($category as $cat){	
			static $i=0;	
			$term[] = get_term_by( 'id', $cat, 'product_cat', 'ARRAY_A' );	   
			$i++;
		}
        function sortByParent($a, $b) {
			return $a['parent'] - $b['parent'];
		};
       usort($term, 'sortByParent');
			$query_part="SELECT object_id from ef_term_relationships WHERE term_taxonomy_id IN (";
			 foreach($term as $term_key){
				 static $previus_parent_key=0;
				 static $filter_flag=0;
				 $current_parent_key=$term_key['parent'];
				 if($current_parent_key==$previus_parent_key){
				   $query_part.=",".$term_key['term_id'];
				  }else{
						if($previus_parent_key==0){
							 
							$query_part.=$term_key['term_id'];
						}else{
								$filter_flag++;
								$query_part.=",".$term_key['term_id'];
					}
			}
			$previus_parent_key=$current_parent_key;
			}
		// query for limited products
			$query_part.=")";
			$result_data= $wpdb->get_results($query_part,ARRAY_A);
			$all_default_product_data=$wpdb->get_results("SELECT object_id from ef_term_relationships WHERE term_taxonomy_id=".$main_cat_id."",ARRAY_A);
			
			$default_product_ids=array();

				foreach($all_default_product_data as $val){
					$default_product_ids[]=$val['object_id'];
				}
		//end  query for limited products
		
		//query for count all products according applied filter
		    // $Applied_filter_query_without_limit=$query_part.")";
			// $total_possible_result_data= $wpdb->get_results($Applied_filter_query_without_limit,ARRAY_A);
		   // $count_possible_product_ids=array();
		   // foreach($total_possible_result_data as $count_result){
				// $count_possible_product_ids[]=$count_result['object_id'];
			// }
			
		
		//end  query for limited products

	


$product_ids=array();

foreach($result_data as $result){
	if(in_array($result['object_id'],$default_product_ids)){
		$product_ids[]=$result['object_id'];
	}
}
		if($filter_flag==0){
			$after_filter_product_ids=array_unique($product_ids);
			// $total_possible_count=$total_possible_result_data;
		}else{
			$counting_filter = array_make_group_with_repeatation($product_ids);
			foreach($counting_filter as $key=>$val){
				if($val==($filter_flag+1)){
					$after_filter_product_ids[]=$key;
				}
			}
			// $total_possible_count= array_duplicates($count_possible_product_ids);
		}
	
		$html.='<div class="row allproducts2" id="allproducts2">';
		$html.='<input type="hidden" value="'.count($after_filter_product_ids).'" id="tota_possible_elemnts">';
		// echo "<pre>";print_r($after_filter_product_ids);exit;
		// echo "<pre>".count($after_filter_product_ids);exit;
            foreach($after_filter_product_ids as $p_ids=>$val) {
				
				static $count_total_products=0;
				$count_total_products++;
				if($count_total_products>=50){
					continue;
				}
				$id=(int)$val;
				 $item_exists_in_cart=matched_cart_items($id);
						 if ($item_exists_in_cart != 0) {
							  $heart_class="fa-heart";
						 }else{
							$heart_class="fa-heart-o"; 
						 }
						 
						 $cat_ids=wp_get_post_terms($id,'product_cat',array('fields'=>'ids'));
							if(in_array(64,$cat_ids)){
								$size="Small";
							}elseif(in_array(65,$cat_ids)){
								$size="Medium";
							}elseif(in_array(66,$cat_ids)){
								$size="Large";
							}else{
								$size="N/A";
							}
				
				$product_data = wc_get_product( $id );
				$saving=$product_data->regular_price-$product_data->sale_price;
				$html.='<div class="col-md-4 oneProduct" data-price="'.(int)$product_data->sale_price.'" data-publish-date="'.get_the_date('d-m-Y', $id).'" data-saving="'.$saving.'">';
					$html.='<a href="'.get_permalink($id).'?glasses_main_cat_name='.get_the_category_by_ID($main_cat_id).'">';
						 $product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
						 $html.='<div class="customProduct">';
							 $html.= '<img src="'.$product_image[0].'" alt="Placeholder" style="height:184px; width:310px;">';
							 $html.='<div class="caption">';
								 $html.= '<span class="name_fl">'.$product_data->name.'</span>';
								 $html.='<div class="ProductRating">';
									 $html.= '<span class="avg_fl">'.$product_data->average_rating;
										 $html.= '<span class="fa fa-star star_fl"></span>';
									 $html.= '</span>';
								 $html.='</div>';
								 $html.= '<span class="price_fl">Rs '.$product_data->sale_price.'</span>';
								 $html.= '<span class="productsize size_fl"><p>Size </p>'.$size.'</span>';
									 
								 
							 $html.='</div>';
						 $html.='</div>';
					$html.='</a>';
					$html.='<div class="wishlist pos-abs right-5 top-15">';
						$html.=do_shortcode("[ti_wishlists_addtowishlist loop=yes]");
						$html.='<span id="like'.$id.'" onclick="AddToFavourite('.$id.')" class="wishlist-icon cursor-pointer text-center fa fs20 '.$heart_class.' text-color-red" >';
						$html.='<p class="tooltiptext1">';
						$html.='Add To Shortlist';
						$html.='</p>';
						$html.='<p class="tooltiptext2">';
						$html.='Remove From Shortlist';
						$html.='</p>';			 
						$html.='</span>';
				    $html.='</div>';
              $html.='</div>';      
                 
            }
		$html.='</div>';
		echo $html; exit;          
    } 

add_action('wp_ajax_add_to_cart', 'add_items_in_cart');
 add_action('wp_ajax_nopriv_add_to_cart','add_items_in_cart');	
 function add_items_in_cart(){
	 $product_id=$_POST['id'];
	 global $woocommerce;
     $woocommerce->cart->add_to_cart( $product_id );
	 echo WC()->cart->get_cart_contents_count();
	 exit;
 }
 add_action('wp_ajax_remove_cart_item', 'remove_from_cart');
 add_action('wp_ajax_nopriv_remove_cart_item','remove_from_cart');	
 function remove_from_cart(){
	 $product_id=$_POST['id'];
	 global $woocommerce;
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
     if ( $cart_item['product_id'] == $product_id ) {
          WC()->cart->remove_cart_item( $cart_item_key );
     }
}
	 echo WC()->cart->get_cart_contents_count();
	 exit;
 }
add_action( 'specific_default_items', 'filter_default_items', 10, 3);
function filter_default_items($category_name){
	$args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => $category_name, 'orderby' => 'ID',  'order'=> 'ASC');
        $loop = new WP_Query( $args );
	$html='<div class="row allproducts1" id="allproducts1">';
	
		$counter=0;
        while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
		$counter++;
		if($counter>=50){
			continue;
		}
		$id=$loop->post->ID;
		$cat_ids=wp_get_post_terms($id,'product_cat',array('fields'=>'ids'));
		if(in_array(64,$cat_ids)){
			$size="Small";
		}elseif(in_array(65,$cat_ids)){
			$size="Medium";
		}elseif(in_array(66,$cat_ids)){
			$size="Large";
		}else{
			$size="N/A";
		} 
		 $item_exists_in_cart=matched_cart_items($id);
						 if ($item_exists_in_cart != 0) {
							  $heart_class="fa-heart";
						 }else{
							$heart_class="fa-heart-o"; 
						 }
				$product_data = wc_get_product( $id );
				$saving=$product_data->regular_price-$product_data->sale_price;
				$html.='<div class="col-md-4 oneProduct" data-price="'.(int)$product_data->sale_price.'" data-publish-date="'.get_the_date('d-m-Y', $id ).'"  data-saving="'.$saving.'">';
					$html.='<a href="'.get_permalink($id).'?glasses_main_cat_name='.$category_name.'" style="text-decoration:none;">';
						 $product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
						 $html.='<div class="customProduct">';
							    $html.= '<img src="'.$product_image[0].'" alt="Placeholder" style="height:184px; width:310px;">';
								$html.='<div class="caption">';
									 $html.= '<span class="name_fl">'.$product_data->name.'</span>';
									 $html.='<div class="ProductRating">';	
										 $html.= '<span class="avg_fl">'.$product_data->average_rating;
											$html.= '<span class="fa fa-star star_fl"></span>';
										 $html.= '</span>';
									 $html.='</div>';
									 $html.= '<span class="price_fl">Rs '.$product_data->sale_price.'</span>';
									 $html.= '<span class="productsize size_fl"><p>Size </p>'.$size.'</span>';
								
							$html.='</div>';
						$html.='</div>';
						 
					 $html.='</a>';
					 $html.='<div class="wishlist pos-abs right-5 top-15">';
					 $html.=do_shortcode("[ti_wishlists_addtowishlist loop=yes]");
					 $html.='<span id="defaultlikeitem'.$id.'" onclick="AddToFavouriteDefaultItems('.$id.')" class="wishlist-icon cursor-pointer text-center fa fs20 '.$heart_class.' text-color-red" >';
					 $html.='<p class="tooltiptext1">';
					 $html.='Add To Shortlist';
					 $html.='</p>';
					 $html.='<p class="tooltiptext2">';
					 $html.='Remove From Shortlist';
					 $html.='</p>';			 
					 $html.='</span>';
					  $html.='</div>';      
              $html.='</div>';
             endwhile;
			 $html.='<input type="hidden" value="'.($counter).'" id="tota_possible_elemnts">';
			 $html.='<script>  document.getElementById("show_count").innerHTML="Showing "+ (jQuery(".allproducts1").children(".oneProduct")).length + " of ";</script>';
			 $html.='<script>document.getElementById("show_count").innerHTML+=document.getElementById("tota_possible_elemnts").value</script>';
			 
		 $html.='</div>';
    wp_reset_query();
	
	echo $html;
}



//send user registration Otp
 add_action('wp_ajax_send_registration_otp', 'send_registration_otp');
 add_action('wp_ajax_nopriv_send_registration_otp','send_registration_otp');	
 function send_registration_otp(){
			session_start();
			$action=$_POST["bt_value"];
			
			switch ($action) {
			
			
					case "send_otp":
					  
							$mobile_number=$_POST["user_mobile"];
							$apiKey = urlencode('YOUR_API_KEY');
							$Textlocal = new Textlocal(false, false, $apiKey);
							
							$numbers = array(
								$mobile_number
							);
							$sender = 'PHPPOT';
							$otp = rand(100000, 999999);
							$message = "Your One Time Password is " . $otp;
							
							$response = $Textlocal->sendSms($numbers, $message, $sender);
							   
							if($response){
								$_SESSION['session_otp'] = $otp;
								echo "Otp Has been sent To Mobile No".$mobile_number;
								exit;
							}
							 break;
				 
				 
					case "veryfy_otp":
							 $entered_Otp=$_POST['enteredOtp'];
							 if($_SESSION['session_otp']==$entered_Otp){
								 echo "Your otp veryied sucessfully";
								 session_destroy();
								 exit;
							 }else{
								 echo 0;
								exit; 
							 }
							 break;
	 
	    // $user_mail=$_POST['user_email'];
		// $genrated_otp=rand(100000,999999);
		// $to =  $user_mail;
		// $subject = "OTP";
		// $txt = "Your eyefoster OTP is ".$genrated_otp." Please do not share with anyone";
		// $headers = "From: eyefoster@gmail.com" . "\r\n" .
		// "CC: ".$user_mail."";

			}	
		
 }
//custom registration for wc 
 add_action('wp_ajax_custom_customer_registration_for_wc', 'custom_customer_registration_for_wc');
 add_action('wp_ajax_nopriv_custom_customer_registration_for_wc','custom_customer_registration_for_wc');	
function custom_customer_registration_for_wc(){
	$Username = $_POST['username'];
	$Password = $_POST['pass'];
	$email = $_POST['email'];
	$ext_phone = $_POST['ext_phone'];
	if ( !username_exists( $Username )  && !email_exists( $email ) ) {
	$user_id = wp_create_user( $Username, $Password, $email);
	add_user_meta($user_id, 'billing_phone', $ext_phone);
	$user = new WP_User( $user_id );
	$user->set_role( 'customer' );
		wp_clear_auth_cookie();
		wp_set_current_user ( $user_id );
		wp_set_auth_cookie  ( $user_id );
		
	echo "ok";exit;
	}else{
		echo "no";exit;
	}    
}


//add a custom shortcode for quick donate form
add_shortcode('custom_newslatter', 'custom_newslatter');
function custom_newslatter(){
   $html="";
   $html.='<section class="available_frames">';
		$html.='<div class="row">';
			$html.='<h4 class="secondary-header" style="font-size:22px!important; ">';
			$html.='Please make your contribution count';
			$html.='</h4>';
			$html.='<div class="available_prod_cont">';
				$html.='<ul>';
					$html.='<form class="ng-pristine ng-valid">';
					$html.='<li>';
					$html.='<input type="text" name="firstName" placeholder="First Name">';
					$html.='</li>';
					$html.='<li>';
					$html.='<input type="text" name="email" placeholder="Email">';
					$html.='</li>';
					$html.='<li>';
					$html.='<input type="text" name="phone" placeholder="Phone">';
					$html.='</li>';
					$html.='<li>';
					$html.='<input type="text" name="amount" placeholder="Amount">';
					$html.='</li>';
					$html.='<li>';
					$html.='<button type="submit" class="button" style="margin:0; border-radius:0; cursor: pointer;" name="firstName">';
					$html.='Donate Now';
					$html.='</button>';
					$html.='</li>';
				$html.='</ul>';
		$html.= '</div>';
	$html.='</div>';
$html.='</section>';
return $html;
}

//add a custom shortcode for Eyefoster cash table
add_shortcode('custom_eyefosterCash_table', 'eyefosterCash_table');
function eyefosterCash_table(){
   $html="";
   $html.='<div class="custom-toggle-panel">';
		$html.='<table>';
			$html.='<thead>';
				$html.='<tr>';
					$html.='<th>'.'Eligible Platform/s'.'</th>';
					$html.='<th>'.'Valid For'.'</th>';
					$html.='<th>'.'Minimum Order Value'.'</th>';
					$html.='<th>'.'Usable EyefosterCash'.'</th>';
				$html.='</tr>';
			$html.='</thead>';
			$html.='<tbody>';
				$html.='<tr>';
					$html.='<td rowspan="4">'.'Eyefoster Desktop/Msite & App (Android & iOS)'.'</td>';
				$html.='</tr>';
				$html.='<tr>';
					$html.='<td>'.'All Users'.'</td>';
					$html.='<td>'.'Rs. 2001 & above'.'</td>';
					$html.='<td>'.'60% (max up to Rs.3500)'.'</td>';
				$html.='</tr>';
				$html.='<tr>';
					$html.='<td>'.'All Users'.'</td>';
					$html.='<td>'.'Rs. 501 - Rs. 2000'.'</td>';
					$html.='<td>'.'20%'.'</td>';
				$html.='</tr>';
				$html.='<tr>';
					$html.='<td>'.'All Users'.'</td>';
					$html.='<td>'.'Rs. 0 - Rs. 500'.'</td>';
					$html.='<td>'.'0%'.'</td>';
				$html.='</tr>';
			$html.='</tbody>';
				$html.='<caption>';
				$html.='1 EyeFoster = 1 INR';
				$html.='</caption>';
		$html.='</table>';
	$html.='<div class="cl_btn_wrap">';					
	$html.='<ul>';					
		$html.='<li>';					
		$html.='<a class="btn" href="https://eyefoster.in/customfilter/?cat=ECONOMY%20EYEGLASSES">';					
		$html.='Shop Eyeglasses';					
		$html.='</a>';					
		$html.='</li>';		
		$html.='<li>';					
		$html.='<a class="btn" href="href="https://eyefoster.in/customfilter/?cat=SUN%20GLASSES%20SHAPES"">';					
		$html.='Shop Sunglasses';					
		$html.='</a>';					
		$html.='</li>';
	$html.='</ul>';
$html.='</div>';					
return $html;
}


//add a custom shortcode for Eyefoster cash terms conditions
add_shortcode('custom_eyefosterCash_terms_conditions', 'eyefosterCash_terms_conditions');
function eyefosterCash_terms_conditions(){
   $content = include_once( __DIR__ .'/custom-page-shortcode/eyefosterCash_eyefosterCash_terms.php'); 
   	
return $html;
}


//add a custom shortcode for Eyefoster cash dynamic toogle section
add_shortcode('custom_eyefosterCash_dynamic_toogle_section', 'eyefosterCash_dynamic_toogle_section');
function eyefosterCash_dynamic_toogle_section(){
  $content = include_once( __DIR__ .'/custom-page-shortcode/eyefosterCash_dynamic_toogle_section.php'); 
  return $content;
  die();
  exit;
						
return $html;
}

//hook for load more products by lazzy loder
 add_action('wp_ajax_load_more_products', 'custom_lazzy_loder');
 add_action('wp_ajax_nopriv_load_more_products','custom_lazzy_loder');
 
 function custom_lazzy_loder() {
	    
	   global $wpdb;
	   $ofset=$_POST['limit'];
	   $req_products=$_POST['req_products'];
		$html="";
		$category=$_POST['category_id'];
		$main_cat_id=$_POST['main_category'];	
  	
		$term=array();
		foreach($category as $cat){	
			static $i=0;	
			$term[] = get_term_by( 'id', $cat, 'product_cat', 'ARRAY_A' );	   
			$i++;
		}
        function sortByParent($a, $b) {
			return $a['parent'] - $b['parent'];
		};
       usort($term, 'sortByParent');
			$query_part="SELECT object_id from ef_term_relationships WHERE term_taxonomy_id IN (";
			 foreach($term as $term_key){
				 static $previus_parent_key=0;
				 static $filter_flag=0;
				 $current_parent_key=$term_key['parent'];
				 if($current_parent_key==$previus_parent_key){
				   $query_part.=",".$term_key['term_id'];
				  }else{
						if($previus_parent_key==0){
							 
							$query_part.=$term_key['term_id'];
						}else{
								$filter_flag++;
								$query_part.=",".$term_key['term_id'];
					}
			}
			$previus_parent_key=$current_parent_key;
			}
		// query for limited products
			$query_part.=")";
			$result_data= $wpdb->get_results($query_part,ARRAY_A);
			
			$all_default_product_data=$wpdb->get_results("SELECT object_id from ef_term_relationships WHERE term_taxonomy_id=".$main_cat_id."",ARRAY_A);
			
			$default_product_ids=array();

				foreach($all_default_product_data as $val){
					$default_product_ids[]=$val['object_id'];
				}
			
			
			if(count($result_data)==0){
			 echo 0; exit;
			}

		//end query query for limited products
		    // $Applied_filter_query_without_limit=$query_part.")";
			// $total_possible_result_data= $wpdb->get_results($Applied_filter_query_without_limit,ARRAY_A);
		   // $total_possible_count= array_duplicates($total_possible_result_data);
		
		//query for count all products according applied filter

		$product_ids=array();
		foreach($result_data as $result){
			if(in_array($result['object_id'],$default_product_ids)){
				$product_ids[]=$result['object_id'];
			}
		}

				if($filter_flag==0){
					$after_filter_product_ids=array_unique($product_ids);
					// $total_possible_count=$total_possible_result_data;
				}else{
					$counting_filter = array_make_group_with_repeatation($product_ids);
					foreach($counting_filter as $key=>$val){
						if($val==($filter_flag+1)){
							$after_filter_product_ids[]=$key;
						}
					}
					// $total_possible_count= array_duplicates($count_possible_product_ids);
				}	
						//echo "<pre>";print_r($after_filter_product_ids);exit;
					foreach($after_filter_product_ids as $p_ids=>$val) {
						static $new_ofset=0;
						$new_ofset++;
						if($new_ofset<=$ofset){
							continue;
						}
						static $counter=0;
						$counter++;
						if($counter>=$req_products){
							exit;
						}
						$id=(int)$val;
						 $item_exists_in_cart=matched_cart_items($id);
								 if ($item_exists_in_cart != 0) {
									  $heart_class="fa-heart";
								 }else{
									$heart_class="fa-heart-o"; 
								 }
								 
								 $cat_ids=wp_get_post_terms($id,'product_cat',array('fields'=>'ids'));
									if(in_array(64,$cat_ids)){
										$size="Small";
									}elseif(in_array(65,$cat_ids)){
										$size="Medium";
									}elseif(in_array(66,$cat_ids)){
										$size="Large";
									}else{
										$size="N/A";
									}
						
						$product_data = wc_get_product( $id );
						$saving=$product_data->regular_price-$product_data->sale_price;
						$html.='<div class="col-md-4 oneProduct" data-price="'.(int)$product_data->sale_price.'" data-publish-date="'.get_the_date('d-m-Y', $id).'" data-saving="'.$saving.'">';
						$html.='<a href="'.get_permalink($id).'?glasses_main_cat_name='.get_the_category_by_ID($main_cat_id).'">';
							 $product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
							 $html.='<div class="customProduct">';
								 $html.= '<img src="'.$product_image[0].'" alt="Placeholder" style="height=154px; width:309px;">';
								 $html.='<div class="caption">';
									 $html.= '<span class="name_fl">'.$product_data->name.'</span>';
										 $html.='<div class="ProductRating">';
											 $html.= '<span class="avg_fl">'.$product_data->average_rating;
												$html.= '<span class="fa fa-star star_fl"></span>';
											 $html.= '</span>';
										 $html.='</div>';
									 $html.= '<span class="price_fl">Rs '.$product_data->sale_price.'</span>';
									 $html.= '<span class="productsize size_fl"><p>Size </p>'.$size.'</span>';
// 						             $html.= '<span class="inc_fl"><p>Size </p>'.$size.'</span>';
								 $html.='</div>';
							 $html.='</div>';
						 $html.='</a>';
						 $html.='<div class="wishlist pos-abs right-5 top-15">';
						 $html.=do_shortcode("[ti_wishlists_addtowishlist loop=yes]");
						 $html.='<span id="like'.$id.'" onclick="AddToFavourite('.$id.')" class="wishlist-icon cursor-pointer text-center fa fs20 '.$heart_class.' text-color-red" >';
						 $html.='<p class="tooltiptext1">';
						 $html.='Add To Shortlist';
						 $html.='</p>';
						 $html.='<p class="tooltiptext2">';
						 $html.='Remove From Shortlist';
						 $html.='</p>';			 
						 $html.='</span>';
						 $html.='</div>';     
					  $html.='</div>';   
					     
					}
				echo $html; exit;          
			} 
			
			
			
			
			
//lazzy loder for default products 

 add_action('wp_ajax_load_more_dafault_products', 'custom_dafault_products');
 add_action('wp_ajax_nopriv_load_more_dafault_products','custom_dafault_products');
function custom_dafault_products(){
	$category_name=$_POST['category_id'];
	$req_products=$_POST['req_products'];
	$main_cat_id=$_POST['main_category'];
	$ofset=$_POST['limit'];
   $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => $category_name, 'orderby' => 'ID',  'order'=> 'ASC');
        $loop = new WP_Query( $args );
	// $html='<div class="row allproducts1" id="allproducts1">';
	$html='';
		
        while ( $loop->have_posts() ) : $loop->the_post(); global $product;	
		static $i=0;
		$i++;
		if($i<=$ofset){
			continue;
		}
		static $counter=0;
		$counter++;
		if($counter>=$req_products){
			exit;
		}
		
		$id=$loop->post->ID;
		$cat_ids=wp_get_post_terms($id,'product_cat',array('fields'=>'ids'));
		if(in_array(64,$cat_ids)){
			$size="Small";
		}elseif(in_array(65,$cat_ids)){
			$size="Medium";
		}elseif(in_array(66,$cat_ids)){
			$size="Large";
		}else{
			$size="N/A";
		} 
		 $item_exists_in_cart=matched_cart_items($id);
						 if ($item_exists_in_cart != 0) {
							  $heart_class="fa-heart";
						 }else{
							$heart_class="fa-heart-o"; 
						 }
				$product_data = wc_get_product( $id );
				$saving=$product_data->regular_price-$product_data->sale_price;
				$html.='<div class="col-md-4 oneProduct" data-price="'.(int)$product_data->sale_price.'" data-publish-date="'.get_the_date('d-m-Y', $id ).'"  data-saving="'.$saving.'">';
				$html.='<a href="'.get_permalink($id).'?glasses_main_cat_name='.get_the_category_by_ID($main_cat_id).'">';
				 $product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
				 $html.='<div class="customProduct">';
                 $html.= '<img src="'.$product_image[0].'" alt="Placeholder" style="height:184px; width:310px;">';
				 $html.='<div class="caption">';
                 $html.= '<span class="name_fl">'.$product_data->name.'</span>';
				 $html.='<div class="ProductRating">';
                 $html.= '<span class="avg_fl">'.$product_data->average_rating;
                 $html.= '<span class="fa fa-star star_fl"></span>';
				 $html.='</div>';
                 $html.= '<span class="price_fl">Rs '.$product_data->sale_price.'</span>';
                 $html.= '<span class="productsize size_fl"><p>Size </p>'.$size.'</span></span>';
				 $html.='</div>';
				 $html.='</a>';
				 $html.='<div class="wishlist pos-abs right-5 top-15">';
				 $html.=do_shortcode("[ti_wishlists_addtowishlist loop=yes]");
			     $html.='<span id="defaultlikeitem'.$id.'" onclick="AddToFavouriteDefaultItems('.$id.')" class="wishlist-icon cursor-pointer text-center fa fs20 '.$heart_class.' text-color-red" >';
				 $html.='<p class="tooltiptext1">';
				 $html.='Add To Shortlist';
				 $html.='</p>';
				 $html.='<p class="tooltiptext2">';
				 $html.='Remove From Shortlist';
				 $html.='</p>';			 
				 $html.='</span>';
				 $html.='</div>';
              $html.='</div>';      
              $html.='</div>';
             endwhile;
			 
		 // $html.='</div>';
    wp_reset_query();
	
	echo $html;exit;
}


// change position of short description value
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta', 10);

// add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 10 );
// add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );

// change position of sku value
// add_action( 'woocommerce_before_shop_loop_item_title', 'custom_before_title' );
// function custom_before_title() {

    // global $product;

    // if ( $product->get_sku() ) {
        // echo $product->get_sku();
    // }

// }









			//add custom option in produt admin pannel
			function create_custom_lens_width_field_in_genral() {
			$arg1 = array(
			'id' => 'Lens_width',
			'label' => __( 'Lens width', 'cfwc' ),
			'class' => 'cfwc-custom-field',
			'desc_tip' => true,
			'description' => __( 'Lens width for Size chart.', 'ctwc' ),
			);
			woocommerce_wp_text_input( $arg1 );
			}
			add_action( 'woocommerce_product_options_general_product_data', 'create_custom_lens_width_field_in_genral' );
			
			
			function create_custom_field_Bridge_in_genral() {
			$arg2 = array(
			'id' => 'Bridge',
			'label' => __( 'Bridge', 'cfwc' ),
			'class' => 'cfwc-custom-field',
			'desc_tip' => true,
			'description' => __( 'Lens width for Size chart.', 'ctwc' ),
			);
			woocommerce_wp_text_input( $arg2 );
			}
			add_action( 'woocommerce_product_options_general_product_data', 'create_custom_field_Bridge_in_genral' );
			
			
			function create_custom_field_temple_in_genral() {
			$arg3 = array(
			'id' => 'Temple',
			'label' => __( 'Temple', 'cfwc' ),
			'class' => 'cfwc-custom-field',
			'desc_tip' => true,
			'description' => __( 'Temple for Size chart.', 'ctwc' ),
			);
			woocommerce_wp_text_input( $arg3 );
			}
			add_action( 'woocommerce_product_options_general_product_data', 'create_custom_field_temple_in_genral' );
			/**
			* Saves the custom field data to product meta data
			*/
			function save_custom_field_bridge( $post_id ) {
			$product = wc_get_product( $post_id );
			$title = isset( $_POST['Bridge'] ) ? $_POST['Bridge'] : '';
			$product->update_meta_data( 'Bridge', sanitize_text_field( $title ) );
			$product->save();
			}
			add_action( 'woocommerce_process_product_meta', 'save_custom_field_bridge' );
			
			
			function save_custom_field_lens_width( $post_id ) {
			$product = wc_get_product( $post_id );
			$title = isset( $_POST['Lens_width'] ) ? $_POST['Lens_width'] : '';
			$product->update_meta_data( 'Lens_width', sanitize_text_field( $title ) );
			$product->save();
			}
			add_action( 'woocommerce_process_product_meta', 'save_custom_field_lens_width' );
			
			
			
			function save_custom_field_temple( $post_id ) {
			$product = wc_get_product( $post_id );
			$title = isset( $_POST['Temple'] ) ? $_POST['Temple'] : '';
			$product->update_meta_data( 'Temple', sanitize_text_field( $title ) );
			$product->save();
			}
			add_action( 'woocommerce_process_product_meta', 'save_custom_field_temple' );
			
			
			
			
			
			function cfwc_display_custom_field() {
				global $post;
				// Check for the custom field value
				$product = wc_get_product( $post->ID );
				$title1 = $product->get_meta( 'Lens_width' );
				$title2 = $product->get_meta( 'Bridge' );
				$title3 = $product->get_meta( 'Temple' );
				if( $title1 || $title2 || $title3) {
					$html='<div class="size-frame">';
						$html.='<div class="clr_nomore">';
							$html.='<a href="/eyefoster/size-guide/">Know Your Size</a>';
						$html.='</div>';
						$html.='<div class="frame_size">';
							$html.='<ul>';
								$html.='<li>';
									$html.='<span class="frame">';
										$html.='<img src="https://eyefoster.in/wp-content/uploads/2021/02/left-frame_25oct17.jpg">';
									$html.='</span>';
									$html.='<div class="size-frame">';
										$html.='<span class="frame1">'.get_post_meta($post->ID, 'Lens_width', true).'</span>';
										$html.='<span class="portion">Lens Width</span>';
									$html.='</div>';
								$html.='</li>';
								
								$html.='<li>';
									$html.='<span class="frame">';
										$html.='<img src="https://eyefoster.in/wp-content/uploads/2021/02/mid-frame_25oct17.jpg">';
									$html.='</span>';
									$html.='<div class="size-frame">';
										$html.='<span class="frame1">'.get_post_meta($post->ID, 'Bridge', true).'</span>';
										$html.='<span class="portion">Bridge</span>';
									$html.='</div>';
								$html.='</li>';
								 
								$html.='<li>';
									$html.='<span class="frame">';
										$html.='<img src="https://eyefoster.in/wp-content/uploads/2021/02/last-frame_25oct17.jpg">';
									$html.='</span>';
									$html.='<div class="size-frame">';
										$html.='<span class="frame1">'.get_post_meta($post->ID, 'Temple', true).'</span>';
										$html.='<span class="portion">Temple</span>';
									$html.='</div>';
								$html.='</li>';
							$html.='</ul>';
						$html.='</div>';
					$html.='</div>';
				}
				echo $html;
				}
			add_action( 'woocommerce_before_add_to_cart_button', 'cfwc_display_custom_field' );




// add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_thumbnails', 20 );
function woocommerce_show_product_thumbnails()
{
	global $product;
	// echo '<div style="
	// position: absolute;
    // top: -0.2%;
    // right: 3%;
    // height: 32px;
    // width: 32px;">'.do_shortcode("[ti_wishlists_addtowishlist]").'</div>';
	
// echo '<img class="imgB1" src="https://placehold.it/100" style="
    
// ">';
?>
<div>
	<?php
	if(is_user_logged_in()){
		 ?>
		 <div class="mobile_wishlist" style="position:absolute; top: 1rem; right: 41%; height:32px; width:32px; z-index: 99; height: 2.5rem;width: 2.5rem;
			border-radius: 50%;
			box-shadow: 0 0 7px rgb(0 0 0 / 20%);
			display: flex;
			justify-content: center;
			align-items: center;
			padding: .5rem;">
		 <span class="mobile_wishlist_child" style="position:absolute; top: 12px; left:.9rem;">
		 <?php echo do_shortcode("[ti_wishlists_addtowishlist loop=yes]");?></span>
		 </div>
		 <?php

	}else{
			?>
			<div class="testting mobile_wishlist" style="font-size:20px; font-weight:bold; color:20px;position: absolute;
			top: 1rem;
			right: 41%;
			height: 2.5rem;width: 2.5rem;
			border-radius: 50%;
			box-shadow: 0 0 7px rgb(0 0 0 / 20%);
			display: flex;
			justify-content: center;
			align-items: center;
			padding: .5rem;z-index: 2;"><i class="fa fa-heart-o" aria-hidden="true"></i></div>
	<?php
	}
	?>
</div>
<?php
}




function sv_add_text_above_wc_shop_image() {
    
	global $product;
 // 	echo '<div style="
 // 	position: absolute;
 //     top: 6.8%;
 //     right: 1%;
 //     height: 32px;
 //     width: 32px;
 //     z-index: 99999;">'.do_shortcode("[ti_wishlists_addtowishlist loop=yes]").'</div>';
 // }
 ?>
 <div style="position:absolute; top:6.8%; right:1%; height:32px; width:32px; z-index: 99;">
	 <?php
	 if(is_user_logged_in()){
		  echo do_shortcode("[ti_wishlists_addtowishlist loop=yes]");
	 }else{
			 ?>
			 <div class="testting" style="font-size:20px; font-weight:bold; color:20px;"><i class="fa fa-heart-o" aria-hidden="true"></i></div>
			 <?php
	 }
	 ?>
 </div>
 <?php
 }
 add_action( 'woocommerce_before_shop_loop_item_title', 'sv_add_text_above_wc_shop_image', 9 );
 



add_shortcode('custom_help_toogle_content', 'help_toogle_content');
function help_toogle_content(){
	
	$content = include_once( __DIR__ .'/custom-page-shortcode/help-page-content.php');
	return $content;
}


add_action( 'woocommerce_order_status_changed','send_custom_review_link',99,4 );
function send_custom_review_link( $order_id, $old_status, $new_status, $order ){
    if( $new_status == "completed" ) {
      $to=$order->get_billing_email();
	  $from="eyefoster@gmail.com";
	  $subject="Review";
	  $content="Please Give A Site Review <br> Click on blow link   https://eyefoster.in/site-review-page/";
	  mail($to,$subject,$content);
    }
}

add_action( 'woocommerce_after_single_product' , 'bbloomer_add_below_prod_gallery', 5 );
  
function bbloomer_add_below_prod_gallery() {
	?><h3 class ="mobile text-uppercase ml-2 custom_review" style ="font-size:18px; color: #515151; font-weight:600; display:none;">Reviews</h3><?php
   echo '<div class="custom_single_product_review" id="custom-review-position" style="padding: 1em 2em; clear:both;width: 66%;  display: inline-block;">';
  echo do_shortcode("[site_reviews display='3']");
   echo '</div>';
   $script='<script>';
   $script.='document.getElementById("custom-review-position").innerHTML+="<a style=float:right;color:#193f96;margin-top:5%;margin-right:7%; href=/eyefoster/customer-review>Read more reviews</a>";';
   $script.='</script>';
   echo $script;
}

add_action('woocommerce_after_single_product','lens_selection_madal');
function lens_selection_madal(){
	include_once( __DIR__ .'/custom-page-shortcode/lens-selection-popup.php');
}

// <div class="prescription-field">
		  // <label>Attached your Prescription:</label><input type="file" id="prescription_'.$product->get_id().'" name="prescription" value="" />
		  // </div>
//function to change upload directory for prescription

function wpse_custom_upload_dir( $dir_data ) {
    // $dir_data already you might want to use
    $custom_dir = 'prescription';
    return [
        'path' => $dir_data[ 'basedir' ] . '/' . $custom_dir,
        'url' => $dir_data[ 'url' ] . '/' . $custom_dir,
        'subdir' => '/' . $custom_dir,
        'basedir' => $dir_data[ 'error' ],
        'error' => $dir_data[ 'error' ],
    ];
}
remove_action('woocommerce_after_single_product_summery','woocommerce_output_product_data_tabs', 10);





//========================================================================custom fields on single product page================================================//

add_action('woocommerce_after_add_to_cart_button', 'custom_data_hidden_fields');
function custom_data_hidden_fields() {
	global $product;?>
    <input type="hidden" id="frame_<?php echo $product->get_id();?>" name="frame" value="" />
    <input type="hidden" id="power_type_<?php echo $product->get_id();?>" name="power_type" value="" />
    <input type="hidden" id="lens_<?php echo $product->get_id();?>" name="lens_name" value="" />
    <input type="hidden" id="lens_price_<?php echo $product->get_id();?>" name="lens_price" value="" />
		  
    <br>
	<?php
	
}



//=========================================== Logic to Save products custom fields values into the cart item data ================================================//
add_filter( 'woocommerce_add_cart_item_data', 'save_custom_data_hidden_fields', 10, 3 );
function save_custom_data_hidden_fields( $cart_item_data, $product_id, $variation_id ) {
    $custom_data = array();

    if( isset( $_REQUEST['frame'] ) ) {

        $custom_data['frame'] = $_REQUEST['frame'];
    }

    if( isset( $_REQUEST['power_type'] ) ) {

        $custom_data['power_type'] = $_REQUEST['power_type'];
    }
	
	if( isset( $_REQUEST['lens_name'] ) ) {
		
			$custom_data['lens_name'] = $_REQUEST['lens_name'];
	}
	if( isset( $_REQUEST['lens_price'] ) ) {
			
			$custom_data['lens_price'] = $_REQUEST['lens_price'];
	}

   $cart_item_meta['custom_data'] = $custom_data;

    return $cart_item_meta;
}


add_action( 'woocommerce_before_calculate_totals', 'add_custom_item_price', 10 );
function add_custom_item_price( $cart_object ) {

    foreach ( $cart_object->get_cart() as $item_values ) {

        ##  Get cart item data
        $item_id = $item_values['data']->id; 
        $original_price = $item_values['data']->price; 
        $name = $item_values['data']->name; 

        ## Get your custom fields values
        $price1 = $item_values['custom_data']['lens_price'];
        $quantity1 = $item_values['custom_data']['frame'];
        $quantity2 = $item_values['custom_data']['power_type'];
        $quantity3 = $item_values['custom_data']['lens_name'];
        // $quantity4 = $item_values['custom_data']['prescription'];
		

        ## Make HERE your own calculation 
        $new_price = (int)$price1+(int)$original_price;

        ## Set the new item price in cart
        $item_values['data']->set_price($new_price);
        // $item_values['data']->set_name($frame_stye);
    }
}


add_filter( 'woocommerce_get_item_data', 'get_item_data' , 25, 2 );

function get_item_data ( $other_data, $cart_item ) {

	if ( isset( $cart_item [ 'custom_data' ] ) ) {
		$custom_data  = $cart_item [ 'custom_data' ];
			// if(!is_null($custom_data['frame'])):
			// 			$other_data[] = array( 'name' => 'Frame Style','display'  => $custom_data['frame'] );
			// endif;
			if(!is_null($custom_data['lens_name'])):			
						$other_data[] = array( 'name' => 'Lens Name','display'  => $custom_data['lens_name'] );
			endif;
			if(!is_null($custom_data['power_type'])):
						$other_data[] = array( 'name' => 'Power Type','display'  => $custom_data['power_type'] );
			endif;
			// if(!is_null($custom_data['prescription'])):
						// $other_data[] = array( 'name' => 'Prescription','display'  => $custom_data['prescription'] );
			// endif;		   
					   
	}
	return $other_data;
	
	
}



add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 2);

function add_order_item_meta ( $item_id, $values ) {
// echo "<pre>";print_r($values);exit;
	if ( isset( $values [ 'custom_data' ] ) ) {

		$custom_data  = $values [ 'custom_data' ];
		wc_add_order_item_meta( $item_id, 'Frame Style', $custom_data['frame'] );
		wc_add_order_item_meta( $item_id, 'Lens Name', $custom_data['lens_name'] );
		wc_add_order_item_meta( $item_id, 'Power Type', $custom_data['power_type'] );
		// wc_add_order_item_meta( $item_id, 'Prescription', $custom_data['prescription'] );
	}
}
//add an option for control lenses
// function admin_lenses_setting() {
    // add_menu_page(
        // 'lenses', 
        // 'Lenses Setting', 
        // 'manage_options', 
        // 'Lenses-Setting', 
        // 'my_callback',
		// '',
		// 3
    // ); 
// }
function admin_lenses_setting() {
    add_menu_page(
        'lenses seting', 
        'Lenses Setting', 
        'manage_options', 
        'Lenses-Setting', 
        'my_callback',
		'',
		3
    ); 
	
	add_submenu_page( 'Lenses-Setting', 'Lenses',  'Lenses', 'manage_options', 'lenses','lense_page');

}
function my_callback()
{
    include_once( __DIR__ .'/custom-page-shortcode/lenses_setting.php');
}
function lense_page()
{
   include_once( __DIR__ .'/custom-page-shortcode/lenses.php');
}
add_action('admin_menu', 'admin_lenses_setting'); 
function lens_chart_sign( $action ){
		if($action=="yes"):
			echo "active";
			endif;
		if($action=="no"):
			echo "inactive";
			endif;
	}
	
	
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
	wp_enqueue_style( 'admin_css', get_stylesheet_directory_uri() . '/custom-page-shortcode/custom-style.css', false, '1.0.0' );
	
}


//add a custom menu item on Wc my account page

function my_custom_endpoints() {
	add_rewrite_endpoint( 'my-prescription', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'my_custom_endpoints' );

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function my_custom_query_vars( $vars ) {
	$vars[] = 'my-prescription';

	return $vars;
}

add_filter( 'query_vars', 'my_custom_query_vars', 0 );



function my_custom_insert_after_helper( $items, $new_items, $after ) {
	// Search for the item position and +1 since is after the selected item key.
	$position = array_search( $after, array_keys( $items ) ) + 1;

	// Insert the new item.
	$array = array_slice( $items, 0, $position, true );
	$array += $new_items;
	$array += array_slice( $items, $position, count( $items ) - $position, true );

    return $array;
}

function my_custom_my_account_menu_items( $items ) {
	$new_items = array();
	$new_items['my-prescription'] = __( 'My prescription', 'woocommerce' );

	return my_custom_insert_after_helper( $items, $new_items, 'orders' );
}

add_filter( 'woocommerce_account_menu_items', 'my_custom_my_account_menu_items' );

function prescription_endpoint_content() {	
	// global $current_user;
    // if(is_user_logged_in()){ 
    //     get_currentuserinfo();
	// 	echo '<h3>My Prescription</h3>';
	// 	$user_prescription_id = get_user_meta($current_user->ID, 'user_prescription', true);
	// 	echo wp_get_attachment_link($user_prescription_id, 'thumbnail', false, false);
    // }
	// echo '<h5 style="font-size:1.5rem; text-align:center; font-weight:700; margin-top:2rem;">Your Prescription</h5>';
	$user = wp_get_current_user();
	$user_id = $user->ID;
	$image_url = get_user_meta($user_id, 'prescription_image_url');
	$image_file = get_user_meta($user_id, 'prescription_image_file');

	//echo '<pre>'; print_r($image_url); die;

	?><div class="row prescription_div mt-2"><?php
	foreach($image_url as $img){
		$img_name_arr = explode('/',$img);
		$img_name = end($img_name_arr);
		?>
		<a href="<?php echo $img; ?>" download ="<?php echo $img; ?>">
		<div class="col-lg-4 col-12">
		<img src="<?php echo $img; ?>" alt="Prescription image" style="border: 1px solid #dadada !important; display:block;" class ="prescription_image img-fluid mx-auto d-block"></a>

		
		<!-- <div class="d-flex justify-content-around mt-2 mb-2">
		<a href="<?php// echo $img; ?>" download ="<?php //echo $img; ?>" class="btn btn-outline-primary"> Download</a>
		<button type="button"  class="btn btn-outline-danger prescription_delete" value="<?php echo $img_name;?>" >Delete</button>
		</div> -->
		</div>
		<?php
	}
			?>		</div><?php
			
}

add_action( 'woocommerce_account_my-prescription_endpoint', 'prescription_endpoint_content' );

// add_action('show_user_profile', 'my_user_profile_edit_action');
// add_action('edit_user_profile', 'my_user_profile_edit_action');
// function my_user_profile_edit_action($user) {
//   echo '<label for="user_prescription">';
//   echo '<input type="hidden" name="action" value="user_prescription"/>';
//     echo '<input name="user_prescription" type="hidden" id="user_prescription" value='.$user->user_prescription.' required>';
//   echo '</label>';
// }


/**
 * Adds a new column to the "My Orders" table in the account.
 *
 * @param string[] $columns the columns in the orders table
 * @return string[] updated columns
 */
function custom_wc_add_my_account_orders_column( $columns ) {

    $new_columns = array();

    foreach ( $columns as $key => $name ) {

        $new_columns[ $key ] = $name;

        // add ship-to after order status column
        if ( 'order-status' === $key ) {
			 $user_prescription_name = get_user_meta($current_user->ID, 'user_prescription', true);
			if($user_prescription_name){
				$column_field_value="Edit Prescription";
			}
            $new_columns['prescription-status'] = __( 'Prescription', 'textdomain' );
        }
    }

    return $new_columns;
}
add_filter( 'woocommerce_my_account_my_orders_columns', 'custom_wc_add_my_account_orders_column' );


/**
 * Adds data to the custom "prescription" column in "My Account > Orders".
 *
 * @param \WC_Order $order the order object for the row
 */
function sv_wc_my_orders_ship_to_column( $order ) {
	$id=$order->get_id();
    global $current_user;
			if(is_user_logged_in){ 
				get_currentuserinfo();
					$user_prescription_id = get_user_meta($current_user->ID, 'user_prescription', true);
			}
    echo ! empty( $user_prescription_id ) ? wp_get_attachment_link($user_prescription_id, 'thumbnail', false, false) : '--';
}
add_action( 'woocommerce_my_account_my_orders_column_prescription-status', 'sv_wc_my_orders_ship_to_column' );



remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 20);





add_filter( 'woocommerce_product_tabs', 'woo_custom_product_tabs' );
function woo_custom_product_tabs( $tabs ) {



    // 2 Adding new tabs and set the right order

    //Attribute Description tab
    $tabs['attrib_desc_tab'] = array(
        'title'     => __( 'Features', 'woocommerce' ),
        'priority'  => 1,
        'callback'  => 'woo_attrib_desc_tab_content'
    );

   

    return $tabs;

}

// New Tab contents

function woo_attrib_desc_tab_content() {
	$feature = get_field('features');

    // The attribute description tab content
    echo '<h2>Description</h2>';
	echo '<pre style=" width:100%; overflow:hidden; word-wrap: break-word;">';
    echo $feature;
}

//============================================== average rating on shop page ======================================================================//

add_action('woocommerce_after_shop_loop_item_title', 'add_star_rating' );
function add_star_rating()
{
global $woocommerce, $product;
$average = $product->get_average_rating();

?><span class="desk_pdt_sub desktop"><?php echo $average;?><i class="fa fa-star ml-2" aria-hidden="true"></i></span><?php

}




//============================================= Exteral css & js================================================================================//

add_action('wp_enqueue_scripts', 'tutsplus_enqueue_custom_js');
function tutsplus_enqueue_custom_js() {
    wp_enqueue_script('custom', get_stylesheet_directory_uri().'/js/script.js', 
    array('jquery'), false, true);
	
	wp_enqueue_script('custom2', get_stylesheet_directory_uri().'/js/my.js', 
    array('jquery'), false, true);
	wp_localize_script( 'custom2', 'my_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script('custom3', get_stylesheet_directory_uri().'/js/swiped-events.js', 
    array('jquery'), false, true);
	wp_enqueue_script('custom4', get_stylesheet_directory_uri().'/js/sweet-alert/node_modules/sweetalert2/dist/sweetalert2.all.min.js', 
    array('jquery'), false, true);
}


add_action( 'wp_enqueue_scripts', 'child_enqueue_styles');

function child_enqueue_styles() {

wp_enqueue_style( 'reset-style', get_template_directory_uri() . '/css/newstyle.css', 'all');
}


//============================================== Allow SVG================================================================================

add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

  global $wp_version;
  if ( $wp_version !== '4.7.1' ) {
     return $data;
  }

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
      'ext'             => $filetype['ext'],
      'type'            => $filetype['type'],
      'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
  echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
             width: 100% !important;
             height: auto !important;
        }
        </style>';
}
add_action( 'admin_head', 'fix_svg' );
//============================================================================= Allow Webp===========================//
function webp_upload_mimes( $existing_mimes ) {
	// add webp to the list of mime types
	$existing_mimes['webp'] = 'image/webp';

	// return the array back to the function with our added mime type
	return $existing_mimes;
}
add_filter( 'mime_types', 'webp_upload_mimes' );

//** * Enable preview / thumbnail for webp image files.*/
function webp_is_displayable($result, $path) {
    if ($result === false) {
        $displayable_image_types = array( IMAGETYPE_WEBP );
        $info = @getimagesize( $path );

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}
add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);



//===========================================================Custom Registration and login===================================================
add_action('wp_ajax_send_register_user_front_end', 'send_register_user_front_end', 0);
add_action('wp_ajax_nopriv_send_register_user_front_end', 'send_register_user_front_end');
function send_register_user_front_end() {
	session_start();
	$new_reg_number_otp = $_POST['new_reg_number'];
	$otp = rand(100000, 999999);
						
	$auth_id = 'MANZY2ZTLKMDM1ZWY3M2';
	$auth_token = 'NWJhODEzODY4NzFkZjljZDFmOWIwYTEwZjc4NWJm';
	$src = '+919227060375';
	$dst = '+91'.$new_reg_number_otp;

	$text =  "Your One Time Password is " . $otp;

		$api = curl_init("https://api.plivo.com/v1/Account/$auth_id/Message/");
		curl_setopt_array($api, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST => 1,
			CURLOPT_HTTPHEADER => array("Authorization: Basic ".base64_encode($auth_id.':'.$auth_token)),
			CURLOPT_POSTFIELDS => array(
				'src' =>$src,
				'dst' => $dst,
				'text' => $text
			)
		));

		$resp = curl_exec($api);

		$resp = curl_exec($api);
		curl_close($api);

		// var_dump($resp);
		if($resp){
		$_SESSION['reg_otp'] = $otp;
		wp_send_json_success('Otp Has been sent To Mobile No');
		
	}else  {
		wp_send_json_success('Issue');
	}
}



add_action('wp_ajax_register_user_front_end', 'register_user_front_end', 0);
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');
function register_user_front_end() {
	  $new_reg_number = stripcslashes($_POST['new_reg_number']);
	  $new_reg_otp = $_POST['new_reg_otp'];
	  $new_reg_email = stripcslashes($_POST['new_reg_email']);
	  $reg_password = sanitize_text_field($_POST['new_reg_password']);
	  $reg_confirm_password = $_POST['new_reg_confirm_password'];
	  $new_reg_firstname = strtolower($_POST['new_reg_firstname']);
	  $new_reg_lastname = strtolower($_POST['new_reg_lastname']);
    $new_reg_username = $new_reg_firstname.$new_reg_lastname;

	if($_SESSION['reg_otp']==$new_reg_otp){
		session_destroy();

	if($reg_password==$reg_confirm_password){
	  $user_data = array(
	      'user_login' => $new_reg_number,
		  'first_name' => $new_reg_firstname,
		  'last_name' => $new_reg_lastname,
	      'user_email' => $new_reg_email,
	      'user_pass' => $reg_password,
        'nickname' =>$new_reg_number,
	      'display_name' => $new_reg_firstname,
	      'role' => 'customer'
	  	);
	  $user_id = wp_insert_user($user_data);
	  add_user_meta($user_id, 'billing_phone', $new_reg_number);
	  	if (!is_wp_error($user_id)) {
	      echo 'we have Created an account for you.';
				wp_clear_auth_cookie();
				wp_set_current_user ( $user_id );
				wp_set_auth_cookie  ( $user_id );
	  	} else {
	    	if (isset($user_id->errors['empty_user_login'])) {
	          $notice_key = 'User Name and Email are mandatory';
	          echo $notice_key;
	      	} elseif (isset($user_id->errors['existing_user_login'])) {
	          echo'Mobile Nubmer already exist.';
	      	} else {
	          echo'Email-id already exist.';
	      	}
	  	}
	}
	else{
		echo 'Password and  Confirm Password Field are not same.';
	}
}
else{
	echo 'You have entered an incorrect OTP.';
}
	die;

}

//===================================================================================desktop User check==========//

add_action('wp_ajax_email_user_check', 'email_user_check', 0);
add_action('wp_ajax_nopriv_email_user_check', 'email_user_check');

function email_user_check(){
	// wp_send_json_success("test");/
	global $wpdb; // this is how you get access to the database
	$reg_password = $_POST['valid_reg_number'];
	

		if(is_email($reg_password)){
			if(email_exists($reg_password)){
				wp_send_json_success($reg_password);
			}
			else{
				wp_send_json_error("registration");
				
			}
		}

		else{
			if(username_exists($reg_password)){
				wp_send_json_success($reg_password);
			}
			else{
				wp_send_json_error("registration");
				
			}
			
			}
			
}
/*
if (is_user_logged_in()) { ?>
	<a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>
<?php } else { get_template_part('ajax', 'auth'); ?>            	
	<a class="login_button" id="show_login" href="">Login</a>
	<a class="login_button" id="show_signup" href="">Signup</a>
<?php } */



add_action('wp_ajax_custom_user_login', 'custom_user_login', 0);
add_action('wp_ajax_nopriv_custom_user_login', 'custom_user_login');

function custom_user_login(){
	global $wpdb;
	
    $email     = $_POST['new_login_user'];
    $password  = $_POST['new_login_passowrd'];

	if(is_email($email)){

    $user_data     = get_user_by( 'email', $email );
    if(isset( $user_data ) && !empty( $user_data )) {
        $login_data    = array();
        $login_data['user_login']      = $email;
        $login_data['user_password']   = $password;
           
        $user_verify = wp_signon( $login_data, false ); 
        if ( is_wp_error($user_verify) ) {
            if( !empty( $user_data->user_login) ) {
              wp_send_json_error("Entered your password is wrong");
            }
            else {
               wp_send_json_error('Invalid Email id');
            }
        }
        else {
			wp_send_json_success('1');
        }
    }
    else {
       wp_send_json_error('Invalid Email id or Password');
    } //condition isset and is not empty.

	}
	else{
    $user_data     = get_user_by( 'login', $email );
	
    if(isset( $user_data ) && !empty( $user_data )) {
        $login_data    = array();
        $login_data['user_login']      = $email;
        $login_data['user_password']   = $password;
           
        $user_verify = wp_signon( $login_data, false ); 
        if ( is_wp_error($user_verify) ) {
            if( !empty( $user_data->user_login) ) {
              wp_send_json_error("Entered your password is wrong");
            }
            else {
               wp_send_json_error('Invalid Email id');
            }
        }
        else {
			wp_send_json_success('1');
        }
    }
    else {
       wp_send_json_error('Invalid Email id or Password');
    }
	}
 
}
//========================================== Desktop Forget Pssword ==========================================================//
add_action('wp_ajax_desktop_forget_password', 'desktop_forget_password');
add_action('wp_ajax_nopriv_desktop_forget_password','desktop_forget_password');	

function desktop_forget_password (){
	session_start();
	$checking = $_POST['new_button_check_value'];

	// wp_send_json_success($checking);
	

	switch ($checking) {
		
		case "send_otp":

			$email = $_POST['new_desktop_otp_data'];

			if(is_email($email)){
				
					if(email_exists($email)){
						$otp = rand(100000, 999999);
						$to = $email;
						$subject = "Forget Password Mail";
						$message = "Your One Time Password Is ".$otp."!";
						$headers = get_option('admin_email');
						$sent = mail($to, $subject, $message, $headers);

							if($sent) {
								$_SESSION['session_otp'] = $otp;
								wp_send_json_success('Otp has been sent to your Email id');
							}
							else  {
								wp_send_json_success('Issue');
							}


						


					}else{
						wp_send_json_success('User not exist with this name please create an account!!');
					}



			}else{
				if(username_exists($email)){
 							$mobile_number=$_POST["new_desktop_otp_data"];
							$otp = rand(100000, 999999);
						
							$auth_id = 'MANZY2ZTLKMDM1ZWY3M2';
							$auth_token = 'NWJhODEzODY4NzFkZjljZDFmOWIwYTEwZjc4NWJm';
							$src = '+919227060375';
							$dst = '+91'.$mobile_number;

							$text =  "Your One Time Password is " . $otp;

								$api = curl_init("https://api.plivo.com/v1/Account/$auth_id/Message/");
								curl_setopt_array($api, array(
									CURLOPT_RETURNTRANSFER => 1,
									CURLOPT_POST => 1,
									CURLOPT_HTTPHEADER => array("Authorization: Basic ".base64_encode($auth_id.':'.$auth_token)),
									CURLOPT_POSTFIELDS => array(
										'src' =>$src,
										'dst' => $dst,
										'text' => $text
									)
								));

								$resp = curl_exec($api);

								$resp = curl_exec($api);
								curl_close($api);

								// var_dump($resp);
								if($resp){
								$_SESSION['session_otp'] = $otp;
								wp_send_json_success('Otp Has been sent To Mobile No');
								
							}else  {
								wp_send_json_success('Issue');
							}

				}else{

					wp_send_json_success('User not exist with this number please create an account!!');
				}	
			}





			break;
		  case "veryfy_otp":

						$entered_Otp=$_POST['new_desktop_otp'];
						if($_SESSION['session_otp']==$entered_Otp){
							wp_send_json_success('Otp Matched');
							session_destroy();
							exit;
						}else{
							wp_send_json_success('You have entered an incorrect OTP.');
						exit; 
						}
						
						break;
		 


		  default:
			echo "click";




	}
	die();
}

//==================================================chage forget password desktop=========================================================//

add_action('wp_ajax_desktop_change_forget_password', 'desktop_change_forget_password');
add_action('wp_ajax_nopriv_desktop_change_forget_password','desktop_change_forget_password');	


function desktop_change_forget_password(){
	
	$new_password_desktop = sanitize_text_field($_POST['new_desktop_new_password']);
	$new_username_desktop = $_POST['new_desktop_new_username'];
	if(is_email($new_username_desktop)){

	$current_user = get_user_by( 'email', $new_username_desktop );
	$userdata = array(
		'ID'        =>  $current_user->ID,
		'user_pass' =>  $new_password_desktop 
	); 
	$user_id = wp_update_user($userdata);

	if($user_id == $current_user->ID){
		update_user_meta($current_user->ID, 'ngp_changepass_status', 1);
		wp_send_json_success('Password Changed Succesfully !!');
	   
	} else {
		wp_send_json_success('Error occurred !!');
	}  
	}
	else{
		$current_user = get_user_by( 'login', $new_username_desktop );
		$userdata = array(
			'ID'        =>  $current_user->ID,
			'user_pass' =>  $new_password_desktop 
		); 
		$user_id = wp_update_user($userdata);
	
		if($user_id == $current_user->ID){
			update_user_meta($current_user->ID, 'ngp_changepass_status', 1);
			wp_send_json_success('Password Changed Succesfully !!');
		   
		} else {
			wp_send_json_success('Error occurred !!');
		}  
	}

exit();
}

//========================================desktop login with otp===================================================//

add_action('wp_ajax_desktop_otp_login_verify', 'desktop_otp_login_verify');
add_action('wp_ajax_nopriv_desktop_otp_login_verify','desktop_otp_login_verify');


function desktop_otp_login_verify(){
	session_start();
	$checking = $_POST['new_button_check_value'];
	switch ($checking) {
		
		case "send_otp": // regis
			$desktop_login_data = $_POST['new_desktop_otp_login_data'];
			if(is_email($desktop_login_data)){  ///send  email otp
				if(email_exists($desktop_login_data)){

							
					$otp = rand(100000, 999999);
						$to = $desktop_login_data;
						$subject = "Login Details";
						$message = "Your One Time Password Is ".$otp."!";
						$headers = get_option('admin_email');
						$sent = mail($to, $subject, $message, $headers);

							if($sent) {
								$_SESSION['session_otp'] = $otp;
								wp_send_json_success('Otp has been sent');
							}
							else  {
								wp_send_json_success('Issue');
							}


				}else{
						wp_send_json_success("Email is not exist! ");
				}
				
			}
			
			else{
				$desktop_login_data = $_POST['new_desktop_otp_login_data'];
				if(username_exists($desktop_login_data)){
	
					$otp = rand(100000, 999999);
						
					$auth_id = 'MANZY2ZTLKMDM1ZWY3M2';
					$auth_token = 'NWJhODEzODY4NzFkZjljZDFmOWIwYTEwZjc4NWJm';
					$src = '+919227060375';
					$dst = '+91'.$desktop_login_data;

					$text =  "Your One Time Password is " . $otp;

						$api = curl_init("https://api.plivo.com/v1/Account/$auth_id/Message/");
						curl_setopt_array($api, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_POST => 1,
							CURLOPT_HTTPHEADER => array("Authorization: Basic ".base64_encode($auth_id.':'.$auth_token)),
							CURLOPT_POSTFIELDS => array(
								'src' =>$src,
								'dst' => $dst,
								'text' => $text
							)
						));

						$resp = curl_exec($api);

						$resp = curl_exec($api);
						curl_close($api);

					   
					if($resp){
						$_SESSION['session_otp'] = $otp;
						wp_send_json_success('Otp has been sent');
						
					}else  {
						wp_send_json_success('Issue');
					}
				}
				else{
					wp_send_json_success("Mobile no. is not exist");
				}
			}

			break;
			








			case "login_in":{
				$desktop_login_data = $_POST['new_desktop_otp_login_data'];
				$desktop_login_otp = $_POST['new_desktop_login_otp'];
				if($_SESSION['session_otp']==$desktop_login_otp){
					session_destroy();
					if(is_email($desktop_login_data)){
					$current_user = get_user_by( 'email', $desktop_login_data );
						
								wp_clear_auth_cookie();
								wp_set_current_user ( $current_user->ID );
								wp_set_auth_cookie  ( $current_user->ID );

								wp_send_json_success("Login Successfully");
					
					exit;
					}
					else{
						$current_user = get_user_by( 'login', $desktop_login_data );
						
								wp_clear_auth_cookie();
								wp_set_current_user ( $current_user->ID );
								wp_set_auth_cookie  ( $current_user->ID );

								wp_send_json_success("Login Successfully");
					
					exit;
					}
				}else{
					wp_send_json_success('You have entered an incorrect OTP.');
				exit; 
				}
				
				break;
			}
		}
}

////==============================================logout==============================================///////// 
// function go_home(){
// 	wp_logout();
// 	wp_redirect( home_url() );
// 	ob_clean();
// 	exit();
// 	}
// 	add_action( 'wp_ajax_logout_user', 'go_home' );
// 	add_action( 'wp_ajax_nopriv_logout_user', 'go_home' );



//============================================================== Moblie login registration system====================================
//=============================================================== mobile user check ==================================================

add_action('wp_ajax_mob_email_user_check', 'mob_email_user_check', 0);
add_action('wp_ajax_nopriv_mob_email_user_check', 'mob_email_user_check');

function mob_email_user_check(){
	// wp_send_json_success("test");/
	global $wpdb; // this is how you get access to the database
	$mob_reg_password = $_POST['mob_valid_reg_number'];
	

		if(is_email($mob_reg_password)){
			if(email_exists($mob_reg_password)){
				wp_send_json_success($mob_reg_password);
			}
			else{
				wp_send_json_error("registration");
				
			}
		}

		else{
			if(username_exists($mob_reg_password)){
				wp_send_json_success($mob_reg_password);
			}
			else{
				wp_send_json_error("registration");
				
			}
			
			}
			
}

////================================================================= Mobile Registraion ==================================================//

add_action('wp_ajax_mob_send_register_user_front_end', 'mob_send_register_user_front_end', 0);
add_action('wp_ajax_nopriv_mob_send_register_user_front_end', 'mob_send_register_user_front_end');
function mob_send_register_user_front_end() {
	session_start();
	$new_reg_number_otp = $_POST['new_reg_number'];
	$otp = rand(100000, 999999);
						
	$auth_id = 'MANZY2ZTLKMDM1ZWY3M2';
	$auth_token = 'NWJhODEzODY4NzFkZjljZDFmOWIwYTEwZjc4NWJm';
	$src = '+919227060375';
	$dst = '+91'.$new_reg_number_otp;

	$text =  "Your One Time Password is " . $otp;

		$api = curl_init("https://api.plivo.com/v1/Account/$auth_id/Message/");
		curl_setopt_array($api, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST => 1,
			CURLOPT_HTTPHEADER => array("Authorization: Basic ".base64_encode($auth_id.':'.$auth_token)),
			CURLOPT_POSTFIELDS => array(
				'src' =>$src,
				'dst' => $dst,
				'text' => $text
			)
		));

		$resp = curl_exec($api);

		$resp = curl_exec($api);
		curl_close($api);

		// var_dump($resp);
		if($resp){
		$_SESSION['reg_otp'] = $otp;
		wp_send_json_success('Otp Has been sent To Mobile No');
		
	}else  {
		wp_send_json_success('Issue');
	}
}

add_action('wp_ajax_mob_register_user_front_end', 'mob_register_user_front_end', 0);
add_action('wp_ajax_nopriv_mob_register_user_front_end', 'mob_register_user_front_end');
function mob_register_user_front_end() {
	  $new_reg_number = stripcslashes($_POST['new_reg_number']);
	  $new_reg_otp = $_POST['new_reg_otp'];
	  $new_reg_email = stripcslashes($_POST['new_reg_email']);
	  $reg_password = sanitize_text_field($_POST['new_reg_password']);
	  $reg_confirm_password = $_POST['new_reg_confirm_password'];
	  $new_reg_firstname = strtolower($_POST['new_reg_firstname']);
	  $new_reg_lastname = strtolower($_POST['new_reg_lastname']);
    $new_reg_username = $new_reg_firstname.$new_reg_lastname;

	if($_SESSION['reg_otp']==$new_reg_otp){
		session_destroy();


	if($reg_password==$reg_confirm_password){
	  $user_data = array(
	      'user_login' => $new_reg_number,
		  'first_name' => $new_reg_firstname,
		  'last_name' => $new_reg_lastname,
	      'user_email' => $new_reg_email,
	      'user_pass' => $reg_password,
        'nickname' =>$new_reg_number,
	      'display_name' => $new_reg_firstname,
	      'role' => 'customer'
	  	);
	  $user_id = wp_insert_user($user_data);
	  add_user_meta($user_id, 'billing_phone', $new_reg_number);
	  	if (!is_wp_error($user_id)) {
	      echo 'we have Created an account for you.';
				wp_clear_auth_cookie();
				wp_set_current_user ( $user_id );
				wp_set_auth_cookie  ( $user_id );
	  	} else {
	    	if (isset($user_id->errors['empty_user_login'])) {
	          $notice_key = 'User Name and Email are mandatory';
	          echo $notice_key;
	      	} elseif (isset($user_id->errors['existing_user_login'])) {
	          echo'Mobile Nubmer already exist.';
	      	} else {
	          echo'Email-id already exist.';
	      	}
	  	}
	}
	else{
		echo 'Password and  Confirm Password Field are not same.';
	}
}
else{
	echo 'You have entered an incorrect OTP.';
}
	die;

}

//===================================================================================== Mobile Login ====================================================//



add_action('wp_ajax_mob_custom_user_login', 'mob_custom_user_login', 0);
add_action('wp_ajax_nopriv_mob_custom_user_login', 'mob_custom_user_login');

function mob_custom_user_login(){
	global $wpdb;
	
    $email     = $_POST['new_login_user'];
    $password  = $_POST['new_login_passowrd'];

	if(is_email($email)){

    $user_data     = get_user_by( 'email', $email );
    if(isset( $user_data ) && !empty( $user_data )) {
        $login_data    = array();
        $login_data['user_login']      = $email;
        $login_data['user_password']   = $password;
           
        $user_verify = wp_signon( $login_data, false ); 
        if ( is_wp_error($user_verify) ) {
            if( !empty( $user_data->user_login) ) {
              wp_send_json_error("Entered your password is wrong");
            }
            else {
               wp_send_json_error('Invalid Email id');
            }
        }
        else {
			wp_send_json_success('1');
        }
    }
    else {
       wp_send_json_error('Invalid Email id or Password');
    } //condition isset and is not empty.

	}
	else{
    $user_data     = get_user_by( 'login', $email );
	
    if(isset( $user_data ) && !empty( $user_data )) {
        $login_data    = array();
        $login_data['user_login']      = $email;
        $login_data['user_password']   = $password;
           
        $user_verify = wp_signon( $login_data, false ); 
        if ( is_wp_error($user_verify) ) {
            if( !empty( $user_data->user_login) ) {
              wp_send_json_error("Entered your password is wrong");
            }
            else {
               wp_send_json_error('Invalid Email id');
            }
        }
        else {
			wp_send_json_success('1');
        }
    }
    else {
       wp_send_json_error('Invalid Email id or Password');
    }
	}
 
}


//======================================================================= Mobile Login with otp===================================================


add_action('wp_ajax_mob_otp_login_verify', 'mob_otp_login_verify');
add_action('wp_ajax_nopriv_mob_otp_login_verify','mob_otp_login_verify');


function mob_otp_login_verify(){
	session_start();
	$checking = $_POST['new_button_check_value'];
	switch ($checking) {
		
		case "send_otp": // regis
			$desktop_login_data = $_POST['new_desktop_otp_login_data'];
			if(is_email($desktop_login_data)){  ///send  email otp
				if(email_exists($desktop_login_data)){

					// $current_user = get_user_by( 'email', $desktop_login_data );
					// 		if ( !is_wp_error( $current_user ) )
					// 		{
					// 			wp_clear_auth_cookie();
					// 			wp_set_current_user ( $current_user->ID );
					// 			wp_set_auth_cookie  ( $current_user->ID );

					// 			wp_send_json_success("1");
					// 		}
							
					$otp = rand(100000, 999999);
						$to = $desktop_login_data;
						$subject = "Login Details";
						$message = "Your One Time Password Is ".$otp."!";
						$headers = get_option('admin_email');
						$sent = mail($to, $subject, $message, $headers);

							if($sent) {
								$_SESSION['session_otp'] = $otp;
								wp_send_json_success('Otp has been sent');
							}
							else  {
								wp_send_json_success('Issue');
							}


				}else{
						wp_send_json_success("Email is not exist! ");
				}
				
			}
			
			else{
				$desktop_login_data = $_POST['new_desktop_otp_login_data'];
				if(username_exists($desktop_login_data)){
					$otp = rand(100000, 999999);
						
					$auth_id = 'MANZY2ZTLKMDM1ZWY3M2';
					$auth_token = 'NWJhODEzODY4NzFkZjljZDFmOWIwYTEwZjc4NWJm';
					$src = '+919227060375';
					$dst = '+91'.$desktop_login_data;

					$text =  "Your One Time Password is " . $otp;

						$api = curl_init("https://api.plivo.com/v1/Account/$auth_id/Message/");
						curl_setopt_array($api, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_POST => 1,
							CURLOPT_HTTPHEADER => array("Authorization: Basic ".base64_encode($auth_id.':'.$auth_token)),
							CURLOPT_POSTFIELDS => array(
								'src' =>$src,
								'dst' => $dst,
								'text' => $text
							)
						));

						$resp = curl_exec($api);

						$resp = curl_exec($api);
						curl_close($api);

					   
					if($resp){
						$_SESSION['session_otp'] = $otp;
						wp_send_json_success('Otp has been sent');
						
					}else  {
						wp_send_json_success('Issue');
					}
				}
				else{
					wp_send_json_success("Mobile no. is not exist");
				}
			}

			break;
			








			case "login_in":{
				$desktop_login_data = $_POST['new_desktop_otp_login_data'];
				$desktop_login_otp = $_POST['new_desktop_login_otp'];
				if($_SESSION['session_otp']==$desktop_login_otp){
					session_destroy();
					if(is_email($desktop_login_data)){
					$current_user = get_user_by( 'email', $desktop_login_data );
						
								wp_clear_auth_cookie();
								wp_set_current_user ( $current_user->ID );
								wp_set_auth_cookie  ( $current_user->ID );

								wp_send_json_success("Login Successfully");
					
					exit;
					}
					else{
						$current_user = get_user_by( 'login', $desktop_login_data );
						
								wp_clear_auth_cookie();
								wp_set_current_user ( $current_user->ID );
								wp_set_auth_cookie  ( $current_user->ID );

								wp_send_json_success("Login Successfully");
					
					exit;
					}
				}else{
					wp_send_json_success('You have entered an incorrect OTP.');
				exit; 
				}
				
				break;
			}
		}
}

//=============================================================== Mobile Forget Password =============================================

add_action('wp_ajax_mob_forget_password', 'mob_forget_password');
add_action('wp_ajax_nopriv_mob_forget_password','mob_forget_password');	

function mob_forget_password (){
	session_start();
	$checking = $_POST['new_button_check_value'];

	// wp_send_json_success($checking);
	

	switch ($checking) {
		
		case "send_otp":

			$email = $_POST['new_desktop_otp_data'];

			if(is_email($email)){
				
					if(email_exists($email)){
						$otp = rand(100000, 999999);
						$to = $email;
						$subject = "Forget Password Mail";
						$message = "Your One Time Password Is ".$otp."!";
						$headers = get_option('admin_email');
						$sent = mail($to, $subject, $message, $headers);

							if($sent) {
								$_SESSION['session_otp'] = $otp;
								wp_send_json_success('Otp has been sent to your Email id');
							}
							else  {
								wp_send_json_success('Issue');
							}


						


					}else{
						wp_send_json_success('User not exist with this name please create an account!!');
					}



			}else{
				if(username_exists($email)){
					$mobile_number=$_POST["new_desktop_otp_data"];
					$otp = rand(100000, 999999);
				
					$auth_id = 'MANZY2ZTLKMDM1ZWY3M2';
					$auth_token = 'NWJhODEzODY4NzFkZjljZDFmOWIwYTEwZjc4NWJm';
					$src = '+919227060375';
					$dst = '+91'.$mobile_number;

					$text =  "Your One Time Password is " . $otp;

						$api = curl_init("https://api.plivo.com/v1/Account/$auth_id/Message/");
						curl_setopt_array($api, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_POST => 1,
							CURLOPT_HTTPHEADER => array("Authorization: Basic ".base64_encode($auth_id.':'.$auth_token)),
							CURLOPT_POSTFIELDS => array(
								'src' =>$src,
								'dst' => $dst,
								'text' => $text
							)
						));

						$resp = curl_exec($api);

						$resp = curl_exec($api);
						curl_close($api);

						// var_dump($resp);
						if($resp){
						$_SESSION['session_otp'] = $otp;
						wp_send_json_success('Otp Has been sent To Mobile No');
						
					}else  {
						wp_send_json_success('Issue');
					}

				}else{

					wp_send_json_success('User not exist with this number please create an account!!');
				}	
			}





			break;
		  case "veryfy_otp":

						$entered_Otp=$_POST['new_desktop_otp'];
						if($_SESSION['session_otp']==$entered_Otp){
							wp_send_json_success('Otp Matched');
							session_destroy();
							exit;
						}else{
							wp_send_json_success('You have entered an incorrect OTP.');
						exit; 
						}
						
						break;
		 


		  default:
			echo "click";




	}
	die();
}

//==================================================chage forget password mobile =========================================================//

add_action('wp_ajax_mob_change_forget_password', 'mob_change_forget_password');
add_action('wp_ajax_nopriv_mob_change_forget_password','mob_change_forget_password');	


function mob_change_forget_password(){
	
	$new_password_desktop = sanitize_text_field($_POST['new_desktop_new_password']);
	$new_username_desktop = $_POST['new_desktop_new_username'];
	if(is_email($new_username_desktop)){

	$current_user = get_user_by( 'email', $new_username_desktop );
	$userdata = array(
		'ID'        =>  $current_user->ID,
		'user_pass' =>  $new_password_desktop 
	); 
	$user_id = wp_update_user($userdata);

	if($user_id == $current_user->ID){
		update_user_meta($current_user->ID, 'ngp_changepass_status', 1);
		wp_send_json_success('Password Changed Succesfully !!');
	   
	} else {
		wp_send_json_success('Error occurred !!');
	}  
	}
	else{
		$current_user = get_user_by( 'login', $new_username_desktop );
		$userdata = array(
			'ID'        =>  $current_user->ID,
			'user_pass' =>  $new_password_desktop 
		); 
		$user_id = wp_update_user($userdata);
	
		if($user_id == $current_user->ID){
			update_user_meta($current_user->ID, 'ngp_changepass_status', 1);
			wp_send_json_success('Password Changed Succesfully !!');
		   
		} else {
			wp_send_json_success('Error occurred !!');
		}  
	}

exit();
}



//======================================================================cart page===============================================/

function desktop_cart_after_total (){
	?><div class="cart_ex_footer" style=" background-color: #efefef;">
	<div class="px-3 py-2 d-flex mt-5" style="border-bottom: 1px solid #232735; background-color: #efefef;">
            <img src="https://eyefoster.in/wp-content/uploads/2021/10/footer1.svg" alt="" class="img-fluid"  style="width:48px;">
            <div>
              <p class="ml-4 mb-0 mt-3" style="font-size: 14px; font-weight: 600; color: #262626;"> 100% Genuine Product
              </p>
              <p class="ml-4" style="font-size: 12px; font-weight: 400; color: #262626;">Best Price Guaranteed</p>
            </div>
          </div>


          <div class="px-3 py-2 d-flex">
            <img src="https://eyefoster.in/wp-content/uploads/2021/10/footer2.svg" alt="" class="img-fluid" style="width:48px;">
            <div>
              <p class="ml-4 mb-0 mt-3" style="font-size: 14px; font-weight: 600; color: #262626;"> 14-Days Easy Returns
              </p>
              <p class="ml-4" style="font-size: 12px; font-weight: 400; color: #262626;">14 days no questions asked
                return policy</p>
            </div>
          </div>
		  </div>
	<?php
}

add_action('woocommerce_after_cart_totals', 'desktop_cart_after_total');


function desktop_coupon_popup (){
	$args = array(
		'posts_per_page'   => -1,
		'orderby'          => 'title',
		'order'            => 'desc',
		'post_type'        => 'shop_coupon',
		'post_status'      => 'publish',
		  'orderby'   => 'meta_value',
		'order' => 'DESC',
	);
	
	$coupons = get_posts( $args );
	$count =count($coupons);
	?>
<div class=" px-3 py-3 checkout_rg_1 cart_desktop_modal" style="height: auto; background-color: #ffffff; border-bottom: 10px solid #e5e5e5;">
            <div class="d-flex">
              <img src="https://lalitdesign.github.io/eyefoster-titled/images/cw-icon-offer.svg" class="img-fluid ml-2" alt="">
              <p class="d-inline-block mb-0" for="defaultCheck1"
                style="font-size: 14px; font-weight: 600; color: #262626; margin-left: .7rem;">
                Apply Coupon Code
              </p>
              <img src="https://lalitdesign.github.io/eyefoster-titled/images/cw-icon-arrow-right.svg" class="img-fluid ml-auto mt-2" alt="" style="cursor: pointer;">
            </div>
            <p class="mobile_cart_apply_coupn"
              style=" padding-left: 2.9rem; margin-bottom: 0; font-size: 12px; font-weight: 400; color: #262626; margin-top: -.4rem;">
              You
              Have <?php echo $count;?> Coupons to Apply <span class="text-danger">*</span></p>
          </div>
		  <?
		  
}

add_action('woocommerce_before_cart_totals', 'desktop_coupon_popup');

//================================================================================= My Account =================================================================================//

//  1. Remove the Addresses tab
add_filter( 'woocommerce_account_menu_items', 'QuadLayers_remove_acc_tab', 999 );
function QuadLayers_remove_acc_tab( $items ) {
unset($items['edit-address']);
unset($items['edit-account']);
return $items;
}
// -------------------------------
// 2. Insert the content of the Addresses tab into an existing tab (edit-account in this case)

add_action( 'woocommerce_account_dashboard', 'woocommerce_account_edit_account' );
add_action( 'woocommerce_account_dashboard', 'woocommerce_account_edit_address' );

add_filter( 'woocommerce_account_menu_items', 'QuadLayers_rename_acc_adress_tab', 9999 );
function QuadLayers_rename_acc_adress_tab( $items ) {
$items['dashboard'] = 'Profile';
return $items;
}


//=========================================================== Rename, re-order my account menu items =================================//
function fwuk_reorder_my_account_menu() {
    $neworder = array(
		// 'edit-account'       => __( 'Profile', 'woocommerce' ),
        'dashboard'          => __( 'Dashboard', 'woocommerce' ),
        'orders'             => __( 'Orders', 'woocommerce' ),
		'my-prescription'     => __( 'My Prescription', 'woocommerce' ),
		'downloads'          => __( 'Downloads', 'woocommerce' ),
        'wishlist'      => __( 'Wishlist', 'woocommerce' ),
        'recently-viewed'      => __( 'Recently Viewed', 'woocommerce' ),
        'customer-logout'    => __( 'Logout', 'woocommerce' ),
    );
    return $neworder;
}
add_filter ( 'woocommerce_account_menu_items', 'fwuk_reorder_my_account_menu' );


//=== Delete Downloads tab

add_filter( 'woocommerce_account_menu_items', 'QuadLayers_remove_acc_address', 9999 );
function QuadLayers_remove_acc_address( $items ) {
unset( $items['downloads'] );
return $items;
}



	
//==================================================================== My Account Wishlist & Recently ViewedOption ======================================================//
function QuadLayers_add_wishlist_endpoint() {
	add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'recently-viewed', EP_ROOT | EP_PAGES );
}  
add_action( 'init', 'QuadLayers_add_wishlist_endpoint' );  
// ------------------
// 2. Add new query
function QuadLayers_wishlist_query_vars( $vars ) {
	$vars[] = 'wishlist';
	$vars[] = 'recently-viewed';
	return $vars;
}  
add_filter( 'query_vars', 'QuadLayers_wishlist_query_vars', 0 );  
// ------------------
// 3. Insert the new endpoint 
function QuadLayers_add_wishlist_link_my_account( $items ) {
	$items['wishlist'] = 'Wishlist';
	$items['recently-viewed'] = 'recently-viewed';
	return $items;
}  
add_filter( 'woocommerce_account_menu_items', 'QuadLayers_add_wishlist_link_my_account' );
// ------------------
// 4. Add content to the new endpoint  
function QuadLayers_wishlist_content() {
echo do_shortcode( '[ti_wishlistsview]' );
}  
add_action( 'woocommerce_account_wishlist_endpoint', 'QuadLayers_wishlist_content' );



// remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);

add_filter( 'woocommerce_account_menu_items', 'QuadLayers_rename_acc_adress_tab2', 9999 );
function QuadLayers_rename_acc_adress_tab2( $items ) {
$items['wishlist'] = 'My Favourites';
$items['recently-viewed'] = 'Recently Viewed';
return $items;
}

//==================================================================== My Account recently viewed Option ======================================================//




function QuadLayers_recently_viewed() {
	echo do_shortcode( '[recently_viewed_products]' );
	}  
	add_action( 'woocommerce_account_recently-viewed_endpoint', 'QuadLayers_recently_viewed' );




//===================================================  add Shortcode recently_viewed_products===============================//

function custom_track_product_view() {
    if ( ! is_singular( 'product' ) ) {
        return;
    }

    global $post;

    if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
        $viewed_products = array();
    else
        $viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

    if ( ! in_array( $post->ID, $viewed_products ) ) {
        $viewed_products[] = $post->ID;
    }

    if ( sizeof( $viewed_products ) > 15 ) {
        array_shift( $viewed_products );
    }

    // Store for session only
    wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
}

add_action( 'template_redirect', 'custom_track_product_view', 20 );

add_shortcode( 'recently_viewed_products', 'bbloomer_recently_viewed_shortcode' );
 
function bbloomer_recently_viewed_shortcode() {
 
   $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
   $viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
 
   if ( empty( $viewed_products ) ) return;
    
   $title = '<h5 style="font-size:1rem; text-align:center; font-weight:700;">Recently Viewed Products</h5>';
   $product_ids = implode( ",", $viewed_products );
 
   return $title . do_shortcode("[products ids='$product_ids']");
   
}



//===================================================== Logout====//

add_action('wp_logout','njengah_homepage_logout_redirect');

function njengah_homepage_logout_redirect(){

    wp_redirect( home_url() );

    exit;

}


	
//==========================================================================chekout page ================================================================//



add_action( 'woocommerce_before_order_notes', 'woocommerce_order_review', 10 );

// add_action('woocommerce_before_order_notes','checkout_page_adrs');

function checkout_page_adrs (){
	$user_id  = get_current_user_id();
	// global $wpdb;
	// echo $user_id;
	// $customer = new WC_Customer( $user_id );
	// echo '<pre>';
	// print_r($customer->get_billing());
	// $tablename=$wpdb->prefix.'ocwma_billingadress';

    // $user = $wpdb->get_results( "SELECT * FROM {$tablename} WHERE type='billing' AND userid=".$user_id);
	// foreach($user as $row){ 
	// 	echo '<pre>';
	// 	print_r($row);
	//  }
	?>
	<div class="" style="width:100%; padding:0 .5rem;">
	<h3 style="font-weight:600;">Order Details</h3>
	<table class="shop_table woocommerce-checkout-review-order-table">
	<thead class="click_desktop_chekout_cart" style="cursor:pointer">
		<tr style="height:90px;">
			<th colspan="2" class="product-name"><?php esc_html_e( 'Your Cart', 'woocommerce' ); ?>&nbsp;<span class="text-primary">(<?php echo WC()->cart->get_cart_contents_count(); ?> Items)</span></th>
			<th class="product-total"  style="color:blue; text-decoration:underline;" ><?php esc_html_e( 'View Details', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody class="show_desktop_chekout_cart">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>" style="border-bottom:10px solid gray;">
				<td style="width:150px;"><?php echo  $_product->get_image(); ?></td>
					<td class="product-name">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) ); ?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<!-- <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> -->
					</td>
					<td class="product-total">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>

					
				</tr>
				
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal" style="border-bottom:1px solid gray !important;">
			<th colspan="2"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th colspan="2"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th colspan="2"><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th colspan="2"><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th colspan="2"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th colspan="2"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php //do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>


	</div>
	
	<?php
}

//=========================================================================================== Thankyou page ====================================================//
add_filter( 'woocommerce_order_item_name', 'ts_product_image_on_thankyou', 10, 3 );
  
function ts_product_image_on_thankyou( $name, $item, $visible ) {
 
    /* Return if not thankyou/order-received page */
    if ( ! is_order_received_page() ) {
        return $name;
    }
     
    /* Get product id */
    $product_id = $item->get_product_id();
      
    /* Get product object */
    $_product = wc_get_product( $product_id );
  
    /* Get product thumbnail */
    $thumbnail = $_product->get_image();
  
    /* Add wrapper to image and add some css */
    $image = '<div class="" style="width: 100%; height: 20%; display: inline-block; padding-right: 7px; vertical-align: middle;">'
                . $thumbnail .
            '</div>'; 
  
    /* Prepend image to name and return it */
    return $image . $name;
}



//=========================================================order page ===========================================================///////////
    // Display the product thumbnail in order view pages
    add_filter( 'woocommerce_order_item_name', 'display_product_image_in_order_item', 10, 3 );
    function display_product_image_in_order_item( $item_name, $item, $is_visible ) {
        // Targeting view order pages only
        if( is_wc_endpoint_url( 'view-order' ) ) {
            $product       = $item->get_product(); // Get the WC_Product object (from order item)
            $product_image = $product->get_image(array( 200, 200)); // Get the product thumbnail (from product object)
            $item_name     = '<span class="item-thumbnail ">' . $product_image . '</span>' . $item_name;
        }
        return $item_name;
    }



//================================================ add prescription on order page========//	
add_action('woocommerce_order_item_meta_end', 'action_order_details_after_order_table', 10, 4 );
function action_order_details_after_order_table( $order, $item) {
    // Only on "My Account" > "Order View"
   
		$user = wp_get_current_user();
		$user_id = $user->ID;
		$order_id = $item->get_order_id();
		$order_details = wc_get_order( $order_id );
		$order_status = $order_details->status;

		$product_id = $item->get_product_id();
		$porduct_details = get_product($product_id);
		$termss = get_the_terms( $product_id, 'product_cat' );
		$full_category_name = $termss[0]-> name;
		$category_name = explode(" ", $full_category_name);
		$category_name = strtolower(end($category_name));
		
		if($category_name =="eyeglasses" && $order_status=="processing"){
			?>
			<span>
				<form action="/upload-prescription" method="post">
			
			<input type="hidden" name="order_id" id="prescription_<?php echo $product_id; ?>" class="prescription_<?php echo $product_id; ?>" value="<?php echo $order_id; ?>">
			<?php
			if(!metadata_exists('user', $user_id, 'prescription_image_url')){
			?> 
			<button type="submit" name="pre_button" class="btn btn-primary ml-2 " value="<?php echo $product_id; ?>"> Upload Prescription</button>
			<?php
			}
			else{
				?>
			<span style="margin-left: 0.6rem !important; margin-top: -1.6rem !important;">
			<span style="font-weight:bold; ml-2">Your Prescription:</span> <span style="text-decoration:underline !important; cursor: pointer !Important;"  name="view_pre_button" class="prescription_popup" value="<?php echo $product_id; ?>">View</span>
			</span>
				<?php
			}?>
			
			</form>
		
			</span>
			
			<?php
		}


	
   
}

add_action('woocommerce_order_details_after_order_table', 'prescripion_offer', 10, 4 );


function prescripion_offer (){
	$user = wp_get_current_user();
	$user_id = $user->ID;
	$image_url = get_user_meta($user_id, 'prescription_image_url');
	$image_file = get_user_meta($user_id, 'prescription_image_file');

	//echo '<pre>'; print_r($image_url); die;

	?>
	<div class="prescription_popup_div" style="position:fixed; top:0; right:0; left:0; bottom:0;  z-index:1000000000; height:100vh; background-color: rgba(0,0,0,.5); display:none;"> 
	
		<div class="row" style="border: 1px solid #dadada !important; background:white; min-width:30%; position:absolute;top:50%;left:50%;transform:translate(-50%,-50%); padding: 2rem 0;">
		<div class="mb-3" style="position:relative;">
			<div class="text-center"><h4>Your Prescriptions</h4> </div>
			<div class="mr-4" style="cursor:pointer;position:absolute;right:0; top:0;"><span class="prescription_popup_cancel">X</span></div>
		</div>
		<?php
	foreach($image_url as $img){
		$img_name_arr = explode('/',$img);
		$img_name = end($img_name_arr);
		?>
		<a href="<?php echo $img; ?>" download ="<?php echo $img; ?>">
		<div class="col-6 mx-auto">
		<img src="<?php echo $img; ?>" alt="Prescription image" style="border: 1px solid #dadada !important; display:block;" class ="prescription_image img-fluid mx-auto d-block"></a>


		</div>
		<?php
	}
			?>	
		
	</div>
</div>
	<?php
}

add_action('wp_ajax_custom_prescription_upload', 'custom_prescription_upload');
add_action('wp_ajax_nopriv_custom_prescription_upload','custom_prescription_upload');

function custom_prescription_upload (){
	add_filter( 'upload_dir', 'wpse_custom_upload_dir' );
	$user = wp_get_current_user();
	$user_id = $user->ID;
	$prescription_hidden_product_id = $_POST['prescription_hidden_product_id'];
	$prescription_hidden_order_id = $_POST['prescription_hidden_order_id'];

	$arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');
    if (in_array($_FILES['file']['type'], $arr_img_ext)) {
		$ext = explode('/',$_FILES['file']['type'])[1];
		$filename = "Order-number_".$prescription_hidden_order_id.".".$ext;
        $upload = wp_upload_bits($filename, null, file_get_contents($_FILES["file"]["tmp_name"]));
        //$upload['url'] will gives you uploaded file path

		// delete_user_meta($user_id, 'prescription_image_url', $upload['url']);	
		// delete_user_meta($user_id, 'prescription_image_file', $upload['file']);	
		// if(!metadata_exists('user', $user_id, 'prescription_image_url')){
		// add_user_meta($user_id, 'prescription_image_url', $upload['url']);	
		// add_user_meta($user_id, 'prescription_image_file', $upload['file']);
		// }
		// else{
		// 	update_user_meta($user_id, 'prescription_image_url', $upload['url']);	
		// 	update_user_meta($user_id, 'prescription_image_file', $upload['file']);
		// }
		add_user_meta($user_id, 'prescription_image_url', $upload['url']);	
		add_user_meta($user_id, 'prescription_image_file', $upload['file']);

		wp_send_json_success("File uploaded successfully.");
		remove_filter( 'upload_dir', 'wpse_custom_upload_dir' );

					
    }else{
		wp_send_json_success("Please Upload Prescription On jpg/jpeg/png Format");
	}
    wp_die();


}

//======================== Delete prescription image===


// add_action('wp_ajax_delete_prescription_image', 'delete_prescription_image');
// add_action('wp_ajax_nopriv_delete_prescription_image','delete_prescription_image');


// 	function delete_prescription_image (){
// 		$selected_image_url = $_POST["new_image_path"];
// 		// $user = wp_get_current_user();
// 		// $user_id = $user->ID;
// 		// echo 'user_id'.$user_id; //die;
// 		//  $image_url = get_user_meta($user_id, 'prescription_image_url');
// 		//  echo '<pre>'; print_r($image_url);
// 		//  foreach($image_url as $key => $img){
// 		//  	$img_name_arr = explode('/',$img);
// 		// 	$img_name = end($img_name_arr);
// 		//  	echo $selected_image_url.'-'.$img_name.'-'.$key.'---'; //die;	
// 		//  	if($img_name == $selected_image_url){
				
// 		//  		unset($image_url[$key]);
	
// 		//  	}
	
// 		//  }
// 		//  echo '<pre>'; print_r($image_url); //die;
// 		// update_user_meta($user_id, 'prescription_image_url', $image_url);
// 		// echo '/home/eyefoste/public_html/wp-content/uploads/prescription/'.$selected_image_url;
// 		// unlink('/home/eyefoste/public_html/wp-content/uploads/prescription/'.$selected_image_url);
// 		wp_delete_file('/home/eyefoste/public_html/wp-content/uploads/prescription/'.$selected_image_url);
		
	
// 	}





//=================================== important 
// Add custom order meta data to make it accessible in Order preview template
add_filter( 'woocommerce_admin_order_preview_get_order_details', 'admin_order_preview_add_custom_meta_data', 10, 2 );
function admin_order_preview_add_custom_meta_data( $data, $order ) {
    $user_id = $order->user_id;
	$image_url = get_user_meta($user_id, 'prescription_image_url');
	if(metadata_exists('user', $user_id, 'prescription_image_url')){
		$data['Prescription_heading']= "Customer Prescription";
		$data['Prescription'] = $image_url; // <= Store the value in the data array.
	}
        

    return $data;
}

// Display custom values in Order preview
add_action( 'woocommerce_admin_order_preview_end', 'custom_display_order_data_in_admin' );
function custom_display_order_data_in_admin(){
    
	echo '<h4 style="text-align:center;">{{data.Prescription_heading}}</h4>';
echo '<div style="margin:10px;" ><a href="{{data.Prescription}}" download ="{{data.Prescription}}"><img src=" {{data.Prescription}}" alt="" style=""></a></div>';
	
	

}


add_action('woocommerce_before_shop_loop','custom_data_on_shop_page');
function custom_data_on_shop_page(){
	// echo 'hello';
	?>
	<p class="custom_data_shop" style="width:110%; font-size:13px; line-height:150%; text-align:right; margin-bottom: -1.2rem; margin-right:-1rem; position:absolute; top:0; right:-7%;">Problem in placing in order ? Give a missed call  <a class="font-weight-bold text-black" href="tel:084484 49249"> 084484 49249</a></p>
	<?php
}


