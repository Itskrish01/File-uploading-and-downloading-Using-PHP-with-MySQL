
<?php 


add_action( 'wp_ajax_nopriv_form_data', 'form_data' );
add_action( 'wp_ajax_form_data', 'form_data' );

function form_data() {
	global $wpdb;
	$fname = $_POST['new_fname'];
	$lname = $_POST['new_lname'];
	$email = $_POST['new_email'];
	$num = $_POST['new_num'];
	$msg = $_POST['new_msg'];
	

	$table_name = $wpdb->base_prefix.'contact_form';
	$charset_collate = $wpdb->get_charset_collate();
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		$wpdb->insert( $table_name, array(
			'first name' => $fname,
			'last name' => $lname,
			'email' => $email,
			'number' => $num,
			'message' => $msg
		) );
		wp_send_json_success("Data Submitted");
		}else{
			$sql= "CREATE TABLE $table_name ( `id` INT(200) NOT NULL AUTO_INCREMENT , `first name` VARCHAR(200) NOT NULL , `last name` VARCHAR(200) NOT NULL , `email` VARCHAR(200) NOT NULL , `number` VARCHAR(200) NOT NULL , `message` VARCHAR(200) NOT NULL , PRIMARY KEY (`id`)) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			$wpdb->insert( $table_name, array(
				'first name' => $fname,
				'last name' => $lname,
				'email' => $email,
				'number' => $num,
				'message' => $msg
			) );
			wp_send_json_success("not exist");
		}
	
}

// delete data 

$id = 0815;
    $table = 'eLearning_progress';
    $wpdb->delete( $table, array( 'id' => $id ) );


//show data on admin side 
add_action( 'admin_menu', 'my_admin_menu' );
		function my_admin_menu() {
			add_menu_page('Form Data', 'Form Data', 'manage_options', 'myplugin/View_Customer_Details.php', 'customerview_admin_page', 'dashicons-phone', 6  );
		}
		function customerview_admin_page(){
			?>
				<div style="width:90%; margin:0 auto;">
					<h1 style="text-align:center; margin-top:2rem;">Contact Deatils</h1>
					<div style="margin-top:2rem;">
					<?php
					global $wpdb;
					$table_name = $wpdb->base_prefix.'contact_form';
					$customers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ID DESC;");
					?>
					<table class="admin_contact_form" style="width:100%; border: 1px solid gray; box-shadow: 1px 1px 1px 1px gray;">
						<tr style="background-color: #379DD6; color:white;">
							<th style=" border: 1px solid #dddddd;text-align: left; padding: 8px;">S.no</th>
							<th style=" border: 1px solid #dddddd;text-align: left; padding: 8px;">First Name</th>
							<th style=" border: 1px solid #dddddd;text-align: left; padding: 8px;">Last Name</th>
							<th style=" border: 1px solid #dddddd;text-align: left; padding: 8px;">Email</th>
							<th style=" border: 1px solid #dddddd;text-align: left; padding: 8px;">Moblie No.</th>
							<th style=" border: 1px solid #dddddd;text-align: left; padding: 8px; width:18rem;" >Message</th>
							<th  style=" border: 1px solid #dddddd;text-align: left; padding: 8px;">Action</th>
							
						</tr>
						<?php $sno = 1;
						foreach($customers as $customer){ ?>
						<tr>
							<td style=" border: 1px solid #dddddd;text-align: left; padding: 8px;"><?php  echo $sno;?></td>
							<td style=" border: 1px solid #dddddd;text-align: left; padding: 8px; "><?php  echo $customer->first_name;?></td>
							<td style=" border: 1px solid #dddddd;text-align: left; padding: 8px;"><?php  echo $customer->last_name;?></td>
							<td style=" border: 1px solid #dddddd;text-align: left; padding: 8px;"><?php  echo $customer->email;?></td>
							<td style=" border: 1px solid #dddddd;text-align: left; padding: 8px;"><?php  echo $customer->number;?></td>
							<td style=" border: 1px solid #dddddd;text-align: left; padding: 8px; max-width:18rem; word-wrap: break-word;"><?php  echo $customer->message;?></td>
							<td class="delete_data" style=" border: 1px solid #dddddd;text-align: left; padding: 8px; text-decoration:underline; cursor:pointer;">Delete</td>
						</tr>
						<?php $sno++;}?>
					</table>
				</div>
				</div>

		<?php
		}