<?php
/**
 * Plugin Name: Proposals
 * Description: This plugin allows to manage all proposals created by logged in user & seconders
 * Version: 1.0.0
 * Author: Macrew Technologies
 * License: GPL2
 */
add_action('admin_menu', 'my_plugin_menu');
function my_plugin_menu() {
    add_menu_page('Proposals', 'Proposals', 'administrator', 'proposals', 'list_proposals', 'dashicons-admin-generic');
}
function list_proposals(){
    
        wp_enqueue_style( 'proposal-bootstrap-css', plugins_url('css/bootstrap.min.css', __FILE__  ));
        wp_enqueue_style( 'proposal-datatable-css', plugins_url('css/jquery.datatables.css', __FILE__  ));
		wp_enqueue_script( 'proposal-bootstrap-js' , plugins_url( 'js/bootstrap.min.js', __FILE__ ) );
		wp_enqueue_script( 'proposal-datatable-js' , plugins_url( 'js/jquery.datatables.min.js', __FILE__ ) );
		$proposal_arr = array();
		global $wpdb;
		$proposals = $wpdb->prefix.'proposals';
		$proposal_meta = $wpdb->prefix.'proposal_meta';
		$seconders = $wpdb->prefix.'seconders';
		
		/* echo '<pre>';
		print_r($proposal_arr); */
		
		$page_url  = admin_url().'admin.php?page=proposals';
		
	
	?>
	<style>
	.mtop{
		margin-top:20px
	}
	.prop_anchor{
		color:#EE3930;
	}
	a.prop_anchor:hover{color:#EE3930;}
	.slash{
		color:#EE3930;	
	}
	.pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus{ background-color: #333 !important;
    border-color: #333 !important;}
	ul.pagination li a{color: #333} 
	</style>
<div class="wrapper container-fluid col-md-10 mtop">
<div class="row">
<section class="col-md-17 m-t-15">
<?php if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'responser') { 
  $proposal_id = $_REQUEST['proposal_id'];
  $responser_id = $_REQUEST['responser_id'];


?>
 <a href="<?php echo $page_url; ?>">Back</a><br/><br/>
 <div class="panel panel-default">
	<div class="panel-heading">	
		<h3 class="panel-title" style="display:inline">View Resonser Detail</h3>
	</div>
	
   <div class="panel-body table-responsive">
		<div class="table-responsive">
			<table class="table table-hover">
			  <tbody>
			   <tr>
					<th>Full Name of Debutante Candidate</th>
					<td><?php echo get_proposal_meta($proposal_id,'debutante_candidate_name'); ?></td>
				<tr>
					<th>Submitted by Board Member</th>
					<td><?php echo get_proposal_meta($proposal_id,'submitted_by_board_member'); ?>	</td>
				</tr>
				<tr>	
				<?php
			$receiver1_arr = get_user_by( 'ID', $responser_id );
			$receiver1_display_name = $receiver1_arr->display_name;
					?>	
					<th>Letter Writer’s Name</th>
					<td><?php echo $receiver1_display_name; ?></td>
				</tr>
				<tr>	
					<th>Date</th>
					<td><?php echo get_seconder_meta($proposal_id,$responser_id,'date'); ?></td>
				</tr>
				<tr>
					<th>Body of letter</th>
					<td><?php echo get_seconder_meta($proposal_id,$responser_id,'body_of_letter'); ?></td>
				</tr>

			  </tbody>          
		   </table>
		</div>
	</div><!-- /.box-body -->
 </div>
 
<?php } elseif(isset($_REQUEST['view']) && $_REQUEST['view'] == 'proposer'){  
$proposal_id = $_REQUEST['proposal_id'];
 ?>
 <a href="<?php echo $page_url; ?>">Back</a><br/><br/>
 <div class="panel panel-default">
	    <div class="panel-heading">	
	<h3 class="panel-title" style="display:inline">View Proposer Detail </h3>
	</div>
	
	   <div class="panel-body table-responsive">
                                    <div class="table-responsive">
										<table class="table table-hover">
										  <tbody>
										   <tr>
												<th>Proposal Name</th>
												<td><?php echo get_proposal_meta($proposal_id,'proposal_name'); ?></td>
											<tr>	
												<th>Proposal Description</th>
												<td><?php echo get_proposal_meta($proposal_id,'propsal_desc'); ?></td>
											</tr>
											<tr>		
												<th>First Receiver name</th>
												<?php
				$responser_id1 = get_proposal_meta($proposal_id,'receiver_name1');
				$receiver1_arr = get_user_by( 'ID', $responser_id1 );
				$receiver1_display_name = $receiver1_arr->display_name;
		?>
												<td><?php echo $receiver1_display_name ?></td>
											</tr>
											<tr>		
												<th>Second Receiver name</th>
													<?php
				$responser_id2 = get_proposal_meta($proposal_id,'receiver_name2');
				$receiver2_arr = get_user_by( 'ID', $responser_id2 );
				$receiver2_display_name = $receiver2_arr->display_name;
		?>
												<td><?php echo $receiver2_display_name; ?></td>
											</tr>
											<tr>	
												<th>Submitted by Board Member</th>
												<td><?php echo get_proposal_meta($proposal_id,'submitted_by_board_member'); ?></td>
											</tr>
											<tr>
												<th>Date</th>
												<td><?php echo get_proposal_meta($proposal_id,'date'); ?></td>
											</tr>
											<tr>	
												<th>Would you like to make the phone call to invite the Candidate</th>
												<td><?php echo get_proposal_meta($proposal_id,'make_phone_calls'); ?></td>
											</tr>
											<tr>
												<th>Form Title</th>
												<td><?php echo get_proposal_meta($proposal_id,'form_title'); ?></td>
											</tr>
											<tr>
												<th>Full Name of Debutante Candidate</th>
												<td><?php echo get_proposal_meta($proposal_id,'debutante_candidate_name'); ?></td>
											</tr>
											<tr>
												<th>Year of Birth</th>
												<td><?php echo get_proposal_meta($proposal_id,'year_of_birth'); ?></td>
											</tr>
											<tr>
												<th>Name of Parent(s) with whom girl resides</th>
												<td><?php echo get_proposal_meta($proposal_id,'name_of_parents_with_girl_resides'); ?></td>
											</tr>
											<tr>
												<th>Address</th>
												<td><?php echo get_proposal_meta($proposal_id,'address'); ?></td>
											</tr>
											<tr>
												<th>Phone</th>
												<td><?php echo get_proposal_meta($proposal_id,'phone'); ?></td>
											</tr>
										    <tr>
												<th>Siblings and ages</th>
												<td><?php echo get_proposal_meta($proposal_id,'siblings_ages'); ?></td>
											</tr>
											<tr>
												<th>Candidate’s cell phone</th>
												<td><?php echo get_proposal_meta($proposal_id,'candidates_cell_phone'); ?></td>
											</tr>
											<tr>
												<th>Mother’s first and maiden names</th>
												<td><?php echo get_proposal_meta($proposal_id,'mother_first_maiden_name'); ?></td>
											</tr>
											<tr>
												<th>Mother’s office number, if applicable and cell phone</th>
												<td><?php echo get_proposal_meta($proposal_id,'mother_office_phone_number'); ?></td>
											</tr>
											<tr>
												<th>Father’s office number and cell phone</th>
												<td><?php echo get_proposal_meta($proposal_id,'father_office_phone_number'); ?></td>
											</tr>
											<tr>
												<th>Father’s phone</th>
												<td><?php echo get_proposal_meta($proposal_id,'father_phone_number'); ?></td>
											</tr>
											<tr>
												<th>Is this candidate a legacy?</th>
												<td><?php echo get_proposal_meta($proposal_id,'candidate_legacy'); ?></td>
											</tr>
											<tr>
												<th>How: <br/>
												Daughter of Cotillion Deb (year, if known)</th>
												<td><?php echo get_proposal_meta($proposal_id,'daughter_of_cotillion_deb'); ?></td>
											</tr>
											<tr>
												<th>Daughter of Board Member</th>
												<td><?php echo get_proposal_meta($proposal_id,'daughter_of_board_member'); ?></td>
											</tr>
											<tr>
												<th>Sister of Cotillion Deb (year, if known)</th>
												<td><?php echo get_proposal_meta($proposal_id,'sister_of_cotillion_deb'); ?></td>
											</tr>
											<tr>
												<th>Relative associated with NMH or Cotillion</th>
												<td><?php echo get_proposal_meta($proposal_id,'relative_Associated_with_nmh'); ?></td>
											</tr>
											<tr>
												<th>Other (describe)</th>
												<td><?php echo get_proposal_meta($proposal_id,'other'); ?></td>
											</tr>
											<tr>
												<th>Who asked you to propose this candidate? Please provide name: <br/> Other (describe)</th>
												<td><?php echo get_proposal_meta($proposal_id,'family_of_candidate'); ?></td>
											</tr>
											<tr>
												<th>Friend of Candidate</th>
												<td><?php echo get_proposal_meta($proposal_id,'friend_of_candidate'); ?></td>
											</tr>
											<tr>
												<th>Board Member</th>
												<td><?php echo get_proposal_meta($proposal_id,'board_member'); ?></td>
											</tr>
											<tr>
												<th>Myself</th>
												<td><?php echo get_proposal_meta($proposal_id,'myself'); ?></td>
											</tr>
											<tr>
												<th>Other</th>
												<td><?php echo get_proposal_meta($proposal_id,'other_propose_candidate'); ?></td>
											</tr>
											<tr>
												<th>Has an older brother participated on the Floor Committee?  <br/> If so, name and year</th>
												<td><?php echo get_proposal_meta($proposal_id,'older_brother_name_year'); ?></td>
											</tr>
											<tr>
												<th>Is the candidate or her family affiliated with NMH?</th>
												<td><?php echo get_proposal_meta($proposal_id,'candidate_her_family_with_nmh'); ?></td>
											</tr>
											<tr>
												<th>How?</th>
												<td><?php echo get_proposal_meta($proposal_id,'how_affiliated_with_nmh'); ?></td>
											</tr>
											<tr>
												<th>Step-parents’ names and address, if applicable</th>
												<td><?php echo get_proposal_meta($proposal_id,'step_parents_name_address'); ?></td>
											</tr>
											<tr>
												<th>Schools attended by the candidate</th>
												<td><?php echo get_proposal_meta($proposal_id,'school_attended_by_candidate'); ?></td>
											</tr>
											<tr>
												<th>Current school and grade</th>
												<td><?php echo get_proposal_meta($proposal_id,'current_school_grade'); ?></td>
											</tr>
											<tr>
												<th>Hobbies</th>
												<td><?php echo get_proposal_meta($proposal_id,'hobbies'); ?></td>
											</tr>
											<tr>
												<th>Interests</th>
												<td><?php echo get_proposal_meta($proposal_id,'interests'); ?></td>
											</tr>
											<tr>
												<th>Notable accomplishments</th>
												<td><?php echo get_proposal_meta($proposal_id,'notable_accomplishments'); ?></td>
											</tr>
											<tr>
												<th>What is your relation to the candidate and her family? How long and how close is your acquaintance with one another? <br/> Do you know the girl as well as her parents? </th>
												<td><?php echo get_proposal_meta($proposal_id,'relation_to_candidate'); ?></td>
											</tr>
											<tr>
												<th>Additional personal comments</th>
												<td><?php echo get_proposal_meta($proposal_id,'personal_comments'); ?></td>
											</tr>

										  </tbody>          
									   </table>
									</div>
                                    
                                   
                                </div><!-- /.box-body -->
	
 </div>

<?php }	elseif(isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'proposer') {
$proposal_id = $_REQUEST['proposal_id'];

if(isset($_REQUEST['UpdateProposal'])) {
		
		unset($_POST['UpdateProposal']);
	/* 	echo '<pre>';
		print_r($_POST); */
		$post_arr = $_POST;
		global $wpdb;
		$table_proposal_meta = $wpdb->prefix.'proposal_meta';	
		foreach($post_arr as $key=>$post_val){
			
			$get_val = get_proposal_meta_arr($proposal_id,$key);
			if(!empty($get_val)){
				$wpdb->update($table_proposal_meta,array('meta_value'=>$post_val),array('proposal_id' => $proposal_id,'meta_key'=>$key));
			} else {
				$secd_arr = array(
						'proposal_id'=>$proposal_id,
						'meta_key' => $key,
						'meta_value' => $post_val
						);
				$wpdb->insert($table_proposal_meta,$secd_arr);
			}
			
		}
		echo ' <div class="alert alert-success"  data-dismiss="alert">
                                 	<button type="button" class="close close-sm">
                                                            <i class="fa fa-times"></i>
                                                        </button>Your proposal has been updated.</div>';
		
}

	?>


 <a href="<?php echo $page_url; ?>">Back</a><br/><br/>
 <div class="panel panel-default">
	    <div class="panel-heading">	
	<h3 class="panel-title" style="display:inline">Edit Proposal Detail</h3>
	</div>
	
	 <div class="panel-body">
	<form class="form-horizontal" name="proposal-form" method="post" onsubmit="return validate_proposal_form()">
	  <div class="form-group">
		<label class="control-label col-sm-2" >Proposal Name:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="proposal_name" value="<?php echo get_proposal_meta($proposal_id,'proposal_name'); ?>" placeholder="Enter Proposal Name">
		</div>
	  </div>
	  <div class="form-group">
		<label class="control-label col-sm-2" >Proposal Description:</label>
		<div class="col-sm-10">
		  <textarea  class="form-control" name="propsal_desc" placeholder="Enter Proposal description"><?php echo get_proposal_meta($proposal_id,'propsal_desc'); ?></textarea>
		</div>
	  </div>
	 <div class="form-group">
		<label class="control-label col-sm-2">First Receiver:</label>
		<div class="col-sm-10">
		<?php $args = array(
				'role'         => 'Subscriber',
				'fields'       => array( 'ID' ,'display_name'),
		); 
		$users = get_users( $args ); ?>
		<select class="form-control" name="receiver_name1">
		<?php  foreach ( $users as $user_val ) {
			$receiver_name1 = get_proposal_meta($proposal_id,'receiver_name1');
		  $user_id = $user_val->ID;			
           $display_name = $user_val->display_name;  
          ?>
		<option value="<?php echo $user_id; ?>" <?php if($receiver_name1 == $user_id){ echo 'selected'; } ?>><?php echo $display_name; ?></option>
		<?php } ?>
		</select>
		</div>
	  </div>
	  <div class="form-group">
		<label class="control-label col-sm-2" >Second Receiver:</label>
		<div class="col-sm-10">
		  	<?php $args = array(
				'role'         => 'Subscriber',
				'fields'       => array( 'ID' ,'display_name'),
		); 
		$users = get_users( $args ); ?>
		<select class="form-control" name="receiver_name2">
		<?php  foreach ( $users as $user_val ) {
			$receiver_name2 = get_proposal_meta($proposal_id,'receiver_name2');
		  $user_id = $user_val->ID;			
           $display_name = $user_val->display_name;  
          ?>
		<option value="<?php echo $user_id; ?>" <?php if($receiver_name2 == $user_id){ echo 'selected'; } ?>><?php echo $display_name; ?></option>
		<?php } ?>
		</select>
		</div>
	  </div>
	   <div class="form-group">
		<label class="control-label col-sm-2">Submitted by Board Member:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="submitted_by_board_member" value="<?php echo get_proposal_meta($proposal_id,'submitted_by_board_member');  ?>" placeholder="Submitted by Board Member">
		</div>
	  </div>
	   <div class="form-group">
		<label class="control-label col-sm-2">Date:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" value="<?php echo get_proposal_meta($proposal_id,'date');  ?>" name="date" placeholder="Date">
		</div>
	  </div>
	  <div class="form-group">
		<label class="control-label col-sm-2" >Would you like to make the phone call to invite the Candidate:</label>
		<div class="col-sm-10">
		<?php $make_phone_calls = get_proposal_meta($proposal_id,'make_phone_calls');  ?>
		    <label class="radio-inline">
				<input type="radio" name="make_phone_calls" value="Yes" <?php if($make_phone_calls == 'Yes') { echo 'checked' ; } ?>>Yes
			</label>
			<label class="radio-inline">
			  <input type="radio" name="make_phone_calls" value="No" <?php if($make_phone_calls == 'No') { echo 'checked' ; } ?>>No
			</label>
		</div>
	  </div>
	  
	    <div class="form-group">
		<label class="control-label col-sm-2" >Form Title:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="form_title" value="<?php echo get_proposal_meta($proposal_id,'form_title');  ?>" placeholder="Form Title">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Full Name of Debutante Candidate:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="debutante_candidate_name" value="<?php echo get_proposal_meta($proposal_id,'debutante_candidate_name');  ?>" placeholder="Full Name of Debutante Candidate">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Year of Birth:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="year_of_birth" value="<?php echo get_proposal_meta($proposal_id,'year_of_birth');  ?>" placeholder="Year of Birth">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Name of Parent(s) with whom girl resides:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="name_of_parents_with_girl_resides" value="<?php echo get_proposal_meta($proposal_id,'name_of_parents_with_girl_resides');  ?>"  placeholder="Name of Parent(s) with whom girl resides">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Address:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="address" value="<?php echo get_proposal_meta($proposal_id,'address');  ?>" placeholder="Address">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Phone:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="phone" value="<?php echo get_proposal_meta($proposal_id,'phone');  ?>" placeholder="Phone">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2">Siblings and ages:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="siblings_ages" value="<?php echo get_proposal_meta($proposal_id,'siblings_ages');  ?>" placeholder="Siblings and ages">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Candidate’s cell phone:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="candidates_cell_phone" value="<?php echo get_proposal_meta($proposal_id,'candidates_cell_phone');  ?>" placeholder="Candidate’s cell phone">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Mother’s first and maiden names:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="mother_first_maiden_name" value="<?php echo get_proposal_meta($proposal_id,'mother_first_maiden_name');  ?>" placeholder="Mother’s first and maiden names">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Mother’s office number, if applicable and cell phone:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="mother_office_phone_number" value="<?php echo get_proposal_meta($proposal_id,'mother_office_phone_number');  ?>" placeholder="Mother’s office number, if applicable and cell phone">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Father’s office number and cell phone:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="father_office_phone_number" value="<?php echo get_proposal_meta($proposal_id,'father_office_phone_number');  ?>" placeholder="Father’s office number and cell phone">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Father’s phone:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="father_phone_number" value="<?php echo get_proposal_meta($proposal_id,'father_phone_number');  ?>" placeholder="Father’s phone">
		</div>
	  </div>
	   <div class="form-group">
		<label class="control-label col-sm-2" >Is this candidate a legacy?:</label>
		<div class="col-sm-10">
		 <label class="radio-inline">
		<?php $candidate_legacy = get_proposal_meta($proposal_id,'candidate_legacy');  ?>
				<input type="radio" name="candidate_legacy" <?php if($candidate_legacy == "Yes"){ echo 'checked';  } ?> value="Yes">Yes
			</label>
			<label class="radio-inline">
			  <input type="radio" name="candidate_legacy" <?php if($candidate_legacy == "No"){ echo 'checked';  } ?> value="No">No
			</label>
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" > Daughter of Cotillion Deb (year, if known):</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="daughter_of_cotillion_deb" value="<?php echo get_proposal_meta($proposal_id,'daughter_of_cotillion_deb');  ?>" placeholder="Daughter of Cotillion Deb (year, if known)">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" > Daughter of Board Member:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="daughter_of_board_member" value="<?php echo get_proposal_meta($proposal_id,'daughter_of_board_member');  ?>" placeholder="Daughter of Board Member">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Sister of Cotillion Deb (year, if known):</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="sister_of_cotillion_deb" value="<?php echo get_proposal_meta($proposal_id,'sister_of_cotillion_deb');  ?>" placeholder="Sister of Cotillion Deb (year, if known)">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Relative associated with NMH or Cotillion:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" value="<?php echo get_proposal_meta($proposal_id,'relative_Associated_with_nmh');  ?>"  name="relative_Associated_with_nmh" placeholder="Relative associated with NMH or Cotillion">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Other (describe):</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" value="<?php echo get_proposal_meta($proposal_id,'other');  ?>" name="other" placeholder="Other (describe)">
		</div>
	  </div>
	  
	   <div class="form-group">
	   <p>Who asked you to propose this candidate? Please provide name</p>
		<label class="control-label col-sm-2" >Family of Candidate :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" value="<?php echo get_proposal_meta($proposal_id,'family_of_candidate');  ?>" name="family_of_candidate" placeholder="Family of Candidate">
		</div>
	  </div>
	  
	    <div class="form-group">
		<label class="control-label col-sm-2" >Friend of Candidate:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="friend_of_candidate" value="<?php echo get_proposal_meta($proposal_id,'friend_of_candidate');  ?>" placeholder="Friend of Candidate">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" > Board Member :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="board_member" value="<?php echo get_proposal_meta($proposal_id,'board_member');  ?>" placeholder="Board Member">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" > Myself :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="myself"  value="<?php echo get_proposal_meta($proposal_id,'myself');  ?>" placeholder="Myself">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" > Other :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="other_propose_candidate" value="<?php echo get_proposal_meta($proposal_id,'other_propose_candidate');  ?>" placeholder="Other">
		</div>
	  </div>
	  
	  <div class="form-group">
	  <p>Has an older brother participated on the Floor Committee?</p>
		<label class="control-label col-sm-2" >If so, name and year :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="older_brother_name_year" value="<?php echo get_proposal_meta($proposal_id,'older_brother_name_year');  ?>" placeholder="If so, name and year">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Is the candidate or her family affiliated with NMH? :</label>
		<div class="col-sm-10">
		<label class="radio-inline">
		<?php  $candidate_her_family_with_nmh = get_proposal_meta($proposal_id,'candidate_her_family_with_nmh');  ?>
				<input type="radio" name="candidate_her_family_with_nmh" <?php if($candidate_her_family_with_nmh == 'Yes'){ echo 'checked'; } ?>  value="Yes">Yes
			</label>
			<label class="radio-inline">
			  <input type="radio" name="candidate_her_family_with_nmh"  <?php if($candidate_her_family_with_nmh == 'No'){ echo 'checked'; } ?> value="No">No
			</label>
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >How? :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="how_affiliated_with_nmh" value="<?php echo get_proposal_meta($proposal_id,'how_affiliated_with_nmh');  ?>" placeholder="How?" value="">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Step-parents’ names and address, if applicable :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="step_parents_name_address" value="<?php echo get_proposal_meta($proposal_id,'step_parents_name_address');  ?>" placeholder="Step-parents’ names and address, if applicable">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Schools attended by the candidate :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="school_attended_by_candidate" value="<?php echo get_proposal_meta($proposal_id,'school_attended_by_candidate');  ?>" placeholder="Schools attended by the candidate">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Current school and grade :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="current_school_grade" value="<?php echo get_proposal_meta($proposal_id,'current_school_grade');  ?>" placeholder="Current school and grade">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label class="control-label col-sm-2" >Hobbies :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="hobbies" value="<?php echo get_proposal_meta($proposal_id,'hobbies');  ?>" placeholder="Hobbies">
		</div>
	  </div>
	  
	    <div class="form-group">
		<label class="control-label col-sm-2" >Interests :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="interests" value="<?php echo get_proposal_meta($proposal_id,'interests');  ?>" placeholder="Interests">
		</div>
	  </div>

	  <div class="form-group">
		<label class="control-label col-sm-2" >Notable accomplishments :</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="notable_accomplishments" value="<?php echo get_proposal_meta($proposal_id,'notable_accomplishments');  ?>" placeholder="Notable accomplishments">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >What is your relation to the candidate and her family? How long and how close is your acquaintance with one another? Do you know the girl as well as her parents?  :</label>
		<div class="col-sm-10">
		  <textarea  class="form-control" name="relation_to_candidate"  placeholder="Notable accomplishments" rows="8"><?php echo get_proposal_meta($proposal_id,'relation_to_candidate');  ?></textarea>
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Additional personal comments  :</label>
		<div class="col-sm-10">
		  <textarea  class="form-control" name="personal_comments" placeholder="Additional personal comments"><?php echo get_proposal_meta($proposal_id,'personal_comments');  ?></textarea>
		</div>
	  </div>
	  
	  <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default" name="UpdateProposal">Submit</button>
		</div>
	  </div>
	</form> 
	</div>
	
 </div>

 <script>
  function validate_proposal_form(){
	if( jQuery("input[name='proposal_name']").val() == ""){
		alert("Please enter proposal name.");
		return false;
	}
	if( jQuery("textarea[name='propsal_desc']").val() == ""){
		alert("Please enter proposal desciption.");
		return false;
	}
	
	if( jQuery("input[name='submitted_by_board_member']").val() == ""){
		alert("Please enter Submitted by Board Member.");
		return false;
	}
	
	if( jQuery("input[name='date']").val() == ""){
		alert("Please enter Date.");
		return false;
	}
	
	if( jQuery("input[name='date']").val() == ""){
		alert("Please enter Date.");
		return false;
	}
	
	if(jQuery("input[name='make_phone_calls']").is(':checked') == false){
	    alert("Would you like to make the phone call to invite the Candidate field is required.");
		return false;
	}
	
	if(jQuery("input[name='form_title']").val() == ""){
		alert("Please enter Form Title.");
		return false;
	}
	
	if(jQuery("input[name='debutante_candidate_name']").val() == ""){
		alert("Please enter Full Name of Debutante Candidate.");
		return false;
	}
	if(jQuery("input[name='year_of_birth']").val() == ""){
		alert("Please enter Year of Birth.");
		return false;
	}
	if(jQuery("input[name='name_of_parents_with_girl_resides']").val() == ""){
		alert("Please enter Name of Parent(s) with whom girl resides.");
		return false;
	}
	
	if(jQuery("input[name='address']").val() == ""){
		alert("Please enter address.");
		return false;
	}
	if(jQuery("input[name='phone']").val() == ""){
		alert("Please enter phone number.");
		return false;
	}
	if(jQuery("input[name='siblings_ages']").val() == ""){
		alert("Please enter siblings and ages.");
		return false;
	}
	if(jQuery("input[name='candidates_cell_phone']").val() == ""){
		alert("Please enter Candidate’s cell phone.");
		return false;
	}
	if(jQuery("input[name='mother_first_maiden_name']").val() == ""){
		alert("Please enter Mother’s first and maiden names.");
		return false;
	}
	if(jQuery("input[name='mother_office_phone_number']").val() == ""){
		alert("Please enter Mother’s first and maiden names.");
		return false;
	}
	if(jQuery("input[name='father_office_phone_number']").val() == ""){
		alert("Please enter Father’s office number and cell phone.");
		return false;
	}
	if(jQuery("input[name='father_phone_number']").val() == ""){
		alert("Please enter Father’s phone.");
		return false;
	}
	if(jQuery("input[name='candidate_legacy']").is(':checked') == false){
		alert("Is this candidate a legacy? is required.");
		return false;
	}
	if(jQuery("input[name='daughter_of_cotillion_deb']").val() == ""){
		alert("Daughter of Cotillion Deb (year, if known) is required.");
		return false;
	}
	if(jQuery("input[name='daughter_of_board_member']").val() == ""){
		alert("Daughter of Board Member is required.");
		return false;
	}
	if(jQuery("input[name='sister_of_cotillion_deb']").val() == ""){
		alert("Sister of Cotillion Deb (year, if known) is required.");
		return false;
	}
	if(jQuery("input[name='relative_Associated_with_nmh']").val() == ""){
		alert("Relative associated with NMH or Cotillion is required.");
		return false;
	}
	if(jQuery("input[name='other']").val() == ""){
		alert("Other (describe) is required.");
		return false;
	}
	if(jQuery("input[name='family_of_candidate']").val() == ""){
		alert("Family of Candidate is required.");
		return false;
	}
	if(jQuery("input[name='friend_of_candidate']").val() == ""){
		alert("Friend of Candidate is required.");
		return false;
	}
	if(jQuery("input[name='board_member']").val() == ""){
		alert("Board Member is required.");
		return false;
	}
	if(jQuery("input[name='myself']").val() == ""){
		alert("Myself field is required.");
		return false;
	}
	if(jQuery("input[name='other_propose_candidate']").val() == ""){
		alert("Other field is required.");
		return false;
	}
	
	if(jQuery("input[name='older_brother_name_year']").val() == ""){
		alert("If so, name and year field is required.");
		return false;
	}
	
	if(jQuery("input[name='older_brother_name_year']").val() == ""){
		alert("If so, name and year field is required.");
		return false;
	}
	if(jQuery("input[name='candidate_her_family_with_nmh']").is(":checked") == false){
		alert("Is the candidate or her family affiliated with NMH? field is required.");
		return false;
	}
	
	if(jQuery("input[name='how_affiliated_with_nmh']").val() == ""){
		alert("How? field is required.");
		return false;
	}
	
	if(jQuery("input[name='step_parents_name_address']").val() == ""){
		alert("Step-parents’ names and address, if applicable field is required.");
		return false;
	}
	
	if(jQuery("input[name='school_attended_by_candidate']").val() == ""){
		alert("Schools attended by the candidate field is required.");
		return false;
	}
	
	if(jQuery("input[name='current_school_grade']").val() == ""){
		alert("Current school and grade field is required.");
		return false;
	}
	if(jQuery("input[name='hobbies']").val() == ""){
		alert("Hobbies field is required.");
		return false;
	}
	if(jQuery("input[name='interests']").val() == ""){
		alert("Interests field is required.");
		return false;
	}
	if(jQuery("input[name='notable_accomplishments']").val() == ""){
		alert("Notable accomplishments field is required.");
		return false;
	}
	if(jQuery("textarea[name='relation_to_candidate']").val() == ""){
		alert("What is your relation to the candidate and her family? How long and how close is your acquaintance with one another? Do you know the girl as well as her parents? field is required.");
		return false;
	}
	if(jQuery("textarea[name='personal_comments']").val() == ""){
		alert("Additional personal comments field is required.");
		return false;
	}
	
 }
 
 </script>


<?php } elseif(isset($_REQUEST['change_status']) && $_REQUEST['change_status'] == 'complete') { 
$proposal_id = $_REQUEST['proposal_id'];
$responser_id = $_REQUEST['responser_id'];
 global $wpdb;
$table_seconders = $wpdb->prefix.'seconders';	
//update status
$wpdb->update($table_seconders,array('meta_value'=>'Complete'),array('proposal_id' => $proposal_id,'seconder_id'=>$responser_id,'meta_key'=>'status'));


?>
 <a href="<?php echo $page_url; ?>">Back</a><br/><br/>
 <div class="panel panel-default">
	
	
	 <div class="panel-body">
	<div class="alert alert-success">
     <button type="button" class="close close-sm"><i class="fa fa-times"></i></button>Responser status has been changed to COMPLETE.</div>
	 </div>
	</div>

<?php } elseif(isset($_REQUEST['resend']) && $_REQUEST['resend'] == 'reminder') { 
$proposal_id = $_REQUEST['proposal_id'];
$responser_id = $_REQUEST['responser_id'];
 global $wpdb;
	$proposal = $wpdb->prefix.'proposals';
	$proposal_arr = $wpdb->get_row("select * from $proposal where id='$proposal_id'",'ARRAY_A');
	$proposer_id = $proposal_arr['proposer_id'];
 $proposer_arr = get_user_by( 'ID', $proposer_id );
  $proposal_name = get_proposal_meta($proposal_id,'proposal_name');
    $debutante_candidate_name = get_proposal_meta($proposal_id,'debutante_candidate_name');
	$receiver1_arr = get_user_by( 'ID', $responser_id );
	$receiver1_email = $receiver1_arr->user_email;
	$receiver1_display_name = $receiver1_arr->display_name;
	$to = $receiver1_email;
	$subject = 'Proposal Invitation';
	 $pid = base64_encode($proposal_id);
	 $sid = base64_encode($responser_id);
   $html = '<p>Dear <b>'.$receiver1_display_name.'</b>,</p><p>This is the reminder email.Please complete the your proposal form. <b>'.$proposer_arr->display_name.'</b> is submitting a proposal for debutante candidate '.$debutante_candidate_name.'. Please find below the link to the seconding letter form to complete this proposal process. Please use this online form to submit your seconding letter for this proposal.<br/><a href="'.get_site_url().'/seconder/?pid='.$pid.'&sid='.$sid.'">'.get_site_url().'/seconder/?pid='.$pid.'&sid='.$sid.'</a><br/><br/>  Thanks</p>';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = 'From: Proposal Invitation <info@wbnorthwestern.org>';
	 
	wp_mail( $to, $subject, $html, $headers );

 

?>
 <a href="<?php echo $page_url; ?>">Back</a><br/><br/>
 <div class="panel panel-default">
	
	
	 <div class="panel-body">
	<div class="alert alert-success">
     <button type="button" class="close close-sm"><i class="fa fa-times"></i></button>Reminder has been sent to responser.</div>
	 </div>
	</div>

<?php }	elseif(isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'responser') {
$proposal_id = $_REQUEST['proposal_id'];
$responser_id = $_REQUEST['responser_id'];

if(isset($_REQUEST['UpdateResponser'])) {
		
		unset($_POST['UpdateResponser']);
	/* 	echo '<pre>';
		print_r($_POST); */
		$post_arr = $_POST;
		global $wpdb;
		$table_seconder = $wpdb->prefix.'seconders';	
		foreach($post_arr as $key=>$post_val){
			
			$get_val = get_seconder_meta_arr($proposal_id,$responser_id,$key);
			if(!empty($get_val)){
				$wpdb->update($table_seconder,array('meta_value'=>$post_val),array('proposal_id' => $proposal_id,'seconder_id'=>$responser_id,'meta_key'=>$key));
			} else {
				$secd_arr = array(
						'proposal_id' => $proposal_id, 
						'seconder_id'=>$responser_id,
						'meta_key' => $key,
						'meta_value' => $post_val
						);
					$wpdb->insert($table_seconder,$secd_arr);
			}
			
		}
		//update status
		$wpdb->update($table_seconder,array('meta_value'=>'Complete'),array('proposal_id' => $proposal_id,'seconder_id'=>$responser_id,'meta_key'=>'status'));
		echo ' <div class="alert alert-success"  data-dismiss="alert">
                                 	<button type="button" class="close close-sm">
                                                            <i class="fa fa-times"></i>
                                                        </button>Your Responser form has been updated.</div>';
		
}

?>

 <a href="<?php echo $page_url; ?>">Back</a><br/><br/>
 <div class="panel panel-default">
	    <div class="panel-heading">	
	<h3 class="panel-title" style="display:inline">Edit Responser Detail</h3>
	</div>
	
	 <div class="panel-body">
	<form class="form-horizontal" name="responser-form" method="post" onsubmit="return validate_responser_form()">
	  <div class="form-group">
		<label class="control-label col-sm-2" >Full Name of Debutante Candidate:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="debutante_candidate_name" value="<?php echo get_proposal_meta($proposal_id,'debutante_candidate_name'); ?>"  disabled>
		   <input type="hidden" class="form-control" name="debutante_candidate_name" value="<?php echo get_proposal_meta($proposal_id,'debutante_candidate_name'); ?>" >
		</div>
	  </div>
	    <div class="form-group">
		<label class="control-label col-sm-2" >Submitted by Board Member:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="submitted_by_board_member" value="<?php echo get_proposal_meta($proposal_id,'submitted_by_board_member'); ?>" disabled>
		  <input type="hidden" class="form-control" name="submitted_by_board_member" value="<?php echo get_proposal_meta($proposal_id,'submitted_by_board_member'); ?>">
		</div>
	  </div>
	  <div class="form-group">
		<label class="control-label col-sm-2" >Letter Writer’s Name:</label>
		<div class="col-sm-10">
		<?php
		
				$receiver1_arr = get_user_by( 'ID', $responser_id );
				$receiver1_display_name = $receiver1_arr->display_name;
		?>
		  <input type="text" class="form-control" name="letter_writer_name" value="<?php echo $receiver1_display_name; ?>" disabled>
		   <input type="hidden" class="form-control" name="letter_writer_name" value="<?php echo $receiver1_display_name; ?>">
		</div>
	  </div>
	   <div class="form-group">
		<label class="control-label col-sm-2" >Date:</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" name="date" value="<?php echo get_seconder_meta($proposal_id,$responser_id,'date'); ?>">
		</div>
	  </div>
	  
	   <div class="form-group">
		<label class="control-label col-sm-2" >Body of letter:</label>
		<div class="col-sm-10">
		  <textarea class="form-control" name="body_of_letter"><?php echo get_seconder_meta($proposal_id,$responser_id,'body_of_letter'); ?></textarea>
		</div>
	  </div>
	  
	  
	   <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default" name="UpdateResponser">Submit</button>
		</div>
	  </div>
	  
	  </form>
	  </div>
	 </div>
	 
	 
	 <script>
  function validate_responser_form(){
	if( jQuery("input[name='date']").val() == ""){
		alert("Please enter date.");
		return false;
	}
	if( jQuery("textarea[name='body_of_letter']").val() == ""){
		alert("Please enter Body of letter.");
		return false;
	}
  }
</script>

<?php  } elseif(isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'proposel') {
$proposal_id = $_REQUEST['proposal_id']; 
global $wpdb;
$table_seconder = $wpdb->prefix.'seconders';    
$proposal = $wpdb->prefix.'proposals';
$proposal_meta = $wpdb->prefix.'proposal_meta';
$wpdb->delete( $proposal, array( 'ID' => $proposal_id ) );
$wpdb->delete( $table_seconder, array( 'ID' => $proposal_id ) );
$wpdb->delete( $proposal_meta, array( 'ID' => $proposal_id ) );
echo ' <a href="'.$page_url.'">Back</a><br/><br/> <div class="alert alert-success"  data-dismiss="alert">
                                 	<button type="button" class="close close-sm">
                                                            <i class="fa fa-times"></i>
                                                        </button>Proposal has been deleted.</div>';

} else { 

$sql = "select * from $proposals as P inner join $proposal_meta as PM on P.id=PM.proposal_id where PM.meta_key='proposal_name'";
$count_proposal_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 


$page	=	"" ;
$lastpage	=	"" ;
$numrows  = "" ;
$total_prop = count($count_proposal_arr);
if(isset($_REQUEST['pageid']))
{
	$page = $_REQUEST['pageid'];
}
else
{
	$page	=	1 ;
}

$numrows	=	$total_prop ;
$rows_per_page	=	10;

// Calculate number of $lastpage
$lastpage = ceil($numrows/$rows_per_page);

// validate/limit requested $pageno
$page = (int)$page;
if ($page > $lastpage) {
	$page = $lastpage;
}
if ($page < 1) {
		$page = 1;
	}
$currentpage = !empty($page) ? (integer)$page : 1;
$start = ($page - 1) * $rows_per_page;
$end = $start + $rows_per_page -1;

if($end > $numrows - 1){
	$end = $numrows - 1;
}

 $sql1 = "select * from $proposals as P inner join $proposal_meta as PM on P.id=PM.proposal_id where PM.meta_key='proposal_name' limit $start,$rows_per_page";
$proposal_arr = $wpdb->get_results( $sql1, 'ARRAY_A' ); 


 ?>
     <div class="panel panel-default">
	    <div class="panel-heading">	
	<h3 class="panel-title" style="display:inline">Proposals - Total <?php echo count($proposal_arr); ?> </h3>
	</div>
	
    
	        <div class="panel-body table-responsive">
                                    <div class="table-responsive">
										<table class="table table-hover">
										  <thead>
											 <tr>
												
												<th>#id</th>
												<th>Proposal Name</th>
												<th>Submitted By</th>
												<th>Date</th>
												<th></th>
												
												<th class="nosort">&nbsp;</th>
											 </tr>
										  </thead>  
										  <tbody>
										  <?php if(!empty($proposal_arr)) { $page_url  = admin_url().'admin.php?page=proposals' ;  $i=1; foreach($proposal_arr as $proposal_val) { $responser_arr ="" ; $responser_arr =  get_responsers($proposal_val['id']);  ?>
										  <tr>
										  <td><?php echo $i; ?></td>
										  <td><?php echo 'Proposal For: '.$proposal_val['meta_value']; ?> <a href="<?php echo $page_url; ?>&view=proposer&proposal_id=<?php echo $proposal_val['id']; ?>" class="prop_anchor">view</a> <span class="slash">/</span> <a href="<?php echo $page_url; ?>&edit=proposer&proposal_id=<?php echo $proposal_val['id']; ?>" class="prop_anchor">edit</a><br/> 
										<?php   if(!empty($responser_arr)){  foreach($responser_arr as $key=>$responser_val){ ?>
										  -  Response <?php echo $key+1 ; ?> | Status:<?php echo $responser_val['status'];  ?> <a href="<?php echo $page_url; ?>&view=responser&proposal_id=<?php echo $proposal_val['id']; ?>&responser_id=<?php echo $responser_val['responser_id']; ?>" class="prop_anchor">view</a> <span class="slash">/</span> <a href="<?php echo $page_url; ?>&edit=responser&proposal_id=<?php echo $proposal_val['id']; ?>&responser_id=<?php echo $responser_val['responser_id']; ?>" class="prop_anchor">edit</a><?php  if($responser_val['status'] == "Waiting") { ?> <span class="slash">|</span> <a href="<?php echo $page_url; ?>&resend=reminder&proposal_id=<?php echo $proposal_val['id']; ?>&responser_id=<?php echo $responser_val['responser_id']; ?>" class="prop_anchor">resend reminder</a> <span class="slash">|</span> <a href="<?php echo $page_url; ?>&change_status=complete&proposal_id=<?php echo $proposal_val['id']; ?>&responser_id=<?php echo $responser_val['responser_id']; ?>" class="prop_anchor">mark complete</a><?php } ?><br/>
										<?php } }  ?>
										  </td>
										  <td><?php  $user_arr = get_user_by( 'ID', $proposal_val['proposer_id'] ) ;
										  echo $user_arr->display_name; ?><br/><?php   if(!empty($responser_arr)){  foreach($responser_arr as $key=>$responser_val){  echo $responser_val['responser_name'];  ?> <br/><?php } } ?></td>
										  <td><?php echo date('Y/m/d',strtotime($proposal_val['created_at'])); ?><br/><?php   if(!empty($responser_arr)){  foreach($responser_arr as $key=>$responser_val){  echo $responser_val['date'];  ?> <br/><?php } } ?></td>
										   <td><a href="<?php echo admin_url( 'admin-post.php?action=print.csv&proposal_id='.$proposal_val['id'] ); ?>" class="prop_anchor">export this proposal(csv)</a>  <br/> 
    									   <a href="<?php echo $page_url; ?>&delete=proposel&proposal_id=<?php echo $proposal_val['id']; ?>" onclick="return confirm('Are you sure you want to delete this proposal?');" class="prop_anchor" class="prop_anchor">Delete proposal</a> </td>
										  </tr>
										  <?php $i++;  } } ?>
										  </tbody>          
									   </table>
									</div>
									
									<ul class="pagination">
		<?php 


$ptemp = $page_url;
		 $pages = '';
//echo $whereStr;
	if ($currentpage != 1) 
{ //GOING BACK FROM PAGE 1 SHOULD NOT BET ALLOWED
 $previous_page = $currentpage - 1;
 //$previous = '<a href="'.$ptemp.'?pageno='.$previous_page.'"> </a> '; 
$previous = '&lt;Previous' ;
 $pages .= '<li><a href="'.$ptemp.'&pageid='.$previous_page.'">'. $previous .'</a></li>'; 
}    
$adjacents = 2;
  $pmin = ($currentpage > $adjacents) ? ($currentpage - $adjacents) : 1;
    $pmax = ($currentpage < ($lastpage - $adjacents)) ? ($currentpage + $adjacents) : $lastpage;
    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $currentpage) {
            $pages.= "<li  class=\"active\"><a href='#'>" . $i . "</a></li>\n";
        } elseif ($i == 1) {
            $pages.= "<li><a  href=\"" . $ptemp ."&pageid=".$i. "\"  rel=".$i.">" . $i . "</a>\n</li>";
        } else {
            $pages.= "<li><a  href=\"" . $ptemp . "&pageid=" . $i . "\"  rel=".$i." >" . $i . "</a>\n</li>";
        }
    }
    

if($currentpage != $lastpage) 
{

 //GOING AHEAD OF LAST PAGE SHOULD NOT BE ALLOWED
 $next_page = $currentpage + 1;
 $next = 'Next&gt;';
 $pages .= '<li><a href="'.$ptemp.'&pageid='.$next_page.$whereStr1.'">'. $next .'</a></li>';

}

if(!empty($numrows)) {
echo   $pages ; //PAGINATION LINKS
}

		?>
	</ul>	
                                    
                                   
                                </div><!-- /.box-body -->
		</div>
<?php } ?>
		
</section>
</div>
</div>
<?php 
}

function get_responsers($proposal_id){
	global $wpdb;
	$proposal_meta_arr = array();
	$responsers_arr = array();
	$proposal_meta = $wpdb->prefix.'proposal_meta';
	$seconders = $wpdb->prefix.'seconders';
	 $sql = "select * from $proposal_meta as P where (P.meta_key='receiver_name1' or P.meta_key='receiver_name2') and  P.proposal_id='$proposal_id'";
	$proposal_meta_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 

	if(!empty($proposal_meta_arr)){
		foreach($proposal_meta_arr as $key=>$proposal_val){
			$responsers_arr[$key]['responser_id'] = $proposal_val['meta_value'];
			 $seconder_id = $proposal_val['meta_value'];
			 $user_arr = get_user_by( 'ID', $proposal_val['meta_value'] ) ;
			$responsers_arr[$key]['responser_name'] = $user_arr->display_name;
			 $status_sql = "select * from $seconders as S where S.meta_key='status' and S.proposal_id='$proposal_id' and seconder_id='$seconder_id'";
			 
			$status_arr = $wpdb->get_row( $status_sql, 'ARRAY_A' ); 

			 $date_sql = "select * from $seconders as S where S.meta_key='date' and S.proposal_id='$proposal_id' and seconder_id='$seconder_id'";
			 
			$date_arr = $wpdb->get_row( $date_sql, 'ARRAY_A' ); 

			$responsers_arr[$key]['status'] = $status_arr['meta_value'];
			$responsers_arr[$key]['date'] = !empty($date_arr['meta_value'])?date('Y/m/d',strtotime($date_arr['meta_value'])):"N/A";
		}
	}

	return $responsers_arr;
	
}

function get_seconder_meta($proposal_id,$seconder_id,$meta_key){
	global $wpdb;
	$seconders = $wpdb->prefix.'seconders';
	    $sql = "select S.meta_value from $seconders as S where S.proposal_id='$proposal_id' and S.seconder_id='$seconder_id' and S.meta_key='$meta_key'";
		$responser_arr = $wpdb->get_row( $sql, 'ARRAY_A' ); 
		return $responser_arr['meta_value'];
}

function get_seconder_meta_arr($proposal_id,$seconder_id,$meta_key){
	global $wpdb;
	$seconders = $wpdb->prefix.'seconders';
	    $sql = "select S.meta_value from $seconders as S where S.proposal_id='$proposal_id' and S.seconder_id='$seconder_id' and S.meta_key='$meta_key'";
		$responser_arr = $wpdb->get_row( $sql, 'ARRAY_A' ); 
		return $responser_arr;
}

function get_proposal_meta($proposal_id,$meta_key){
	global $wpdb;
	$proposal_meta = $wpdb->prefix.'proposal_meta';
	     $sql = "select P.meta_value from $proposal_meta as P where P.proposal_id='$proposal_id' and P.meta_key='$meta_key'";
		$proposal_arr = $wpdb->get_row( $sql, 'ARRAY_A' ); 
		return $proposal_arr['meta_value'];
}

function get_proposal_meta_arr($proposal_id,$meta_key){
	global $wpdb;
	$proposal_meta = $wpdb->prefix.'proposal_meta';
	     $sql = "select P.meta_value from $proposal_meta as P where P.proposal_id='$proposal_id' and P.meta_key='$meta_key'";
		$proposal_arr = $wpdb->get_row( $sql, 'ARRAY_A' ); 
		return $proposal_arr;
}

function get_user_name($user_id){
	$user_arr = get_user_by( 'ID', $user_id );
	return $user_arr->display_name;
}

add_action( 'admin_post_print.csv', 'print_csv' );

function print_csv()
{
	$proposal_id = $_REQUEST['proposal_id'];
   $_SERVER['DOCUMENT_ROOT']."/bobby_proposal_plugin/";
	 $txt = "";
	 // $tmpfile = sys_get_temp_dir().'/export_proposal.csv';
	 // $tmpfile = $_SERVER['DOCUMENT_ROOT']."/bobby_proposal_plugin/export_proposal.csv";
	  $tmpfile = sys_get_temp_dir().'/export_proposal.csv';
	
	 $myfile = fopen($tmpfile, "w") or die("Unable to open file 1!");
	global $wpdb;
	$proposals	= $wpdb->prefix.'proposals';
	$sql = "select * from $proposals";
	$proposal_arr = $wpdb->get_row( $sql, 'ARRAY_A' ); 
	 $txt.= "Proposal Detail";
	 $txt.= PHP_EOL ;
	
	 fwrite($myfile, $txt);
	 $content = "";
	 $proposer_id = $proposal_arr['proposer_id'];
	 $proposal_created_by =  get_user_name($proposer_id);
	 $proposer_created_at = $proposal_arr['created_at'];
	  $content.= "Proposal Created By,";
	 $content.= '"'.$proposal_created_by.'"' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 $content = "";
	 $content.= "Proposal Created At,";
	 $content.= '"'.$proposer_created_at.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $proposal_name = get_proposal_meta($proposal_id,'proposal_name');
	 $content = "";
	 $content.= "Proposal name,";
	 $content.= '"'.$proposal_name.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $propsal_desc = get_proposal_meta($proposal_id,'propsal_desc');
	 $content = "";
	 $content.= "Proposal Description,";
	 $content.= '"'.$propsal_desc.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	
	 $receiver_name1 = get_proposal_meta($proposal_id,'receiver_name1');
	 $content = "";
	 $content.= "First Receiver,";
	 $receiver_name1_val =  get_user_name($receiver_name1);
	 $content.= '"'.$receiver_name1_val.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 
	 $receiver_name2 = get_proposal_meta($proposal_id,'receiver_name2');
	 $content = "";
	 $content.= "Second Receiver,";
	 $receiver_name2_val =  get_user_name($receiver_name2);
	 $content.= '"'.$receiver_name2_val.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $submitted_by_board_member = get_proposal_meta($proposal_id,'submitted_by_board_member');
	 $content = "";
	 $content.= "Submitted by Board Member,";
	 $content.= '"'.$submitted_by_board_member.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $make_phone_calls = get_proposal_meta($proposal_id,'make_phone_calls');
	 $content = "";
	 $content.= "Would you like to make the phone call to invite the Candidate,";
	 $content.= '"'.$make_phone_calls.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $form_title = get_proposal_meta($proposal_id,'form_title');
	 $content = "";
	 $content.= "Form Title,";
	 $content.= '"'.$form_title.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $debutante_candidate_name = get_proposal_meta($proposal_id,'debutante_candidate_name');
	 $content = "";
	 $content.= "Full Name of Debutante Candidate,";
	 $content.= '"'.$debutante_candidate_name.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $year_of_birth = get_proposal_meta($proposal_id,'year_of_birth');
	 $content = "";
	 $content.= "Year of Birth,";
	 $content.= '"'.$year_of_birth.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $name_of_parents_with_girl_resides = get_proposal_meta($proposal_id,'name_of_parents_with_girl_resides');
	 $content = "";
	 $content.= "Name of Parent(s) with whom girl resides,";
	 $content.= '"'.$name_of_parents_with_girl_resides.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $address = get_proposal_meta($proposal_id,'address');
	 $content = "";
	 $content.= "Address,";
	 $content.= '"'.$address.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $phone = get_proposal_meta($proposal_id,'phone');
	 $content = "";
	 $content.= "Phone,";
	 $content.= '"'.$phone.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $siblings_ages = get_proposal_meta($proposal_id,'siblings_ages');
	 $content = "";
	 $content.= "Siblings and ages,";
	 $content.= '"'.$siblings_ages.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $siblings_ages = get_proposal_meta($proposal_id,'siblings_ages');
	 $content = "";
	 $content.= "Siblings and ages,";
	 $content.= '"'.$siblings_ages.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $candidates_cell_phone = get_proposal_meta($proposal_id,'candidates_cell_phone');
	 $content = "";
	 $content.= "Candidate’s cell phone,";
	 $content.= '"'.$candidates_cell_phone.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $mother_first_maiden_name = get_proposal_meta($proposal_id,'mother_first_maiden_name');
	 $content = "";
	 $content.= "Mother’s first and maiden names,";
	 $content.= '"'.$mother_first_maiden_name.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $mother_office_phone_number = get_proposal_meta($proposal_id,'mother_office_phone_number');
	 $content = "";
	 $content.= "Mother’s office number if applicable and cell phone,";
	 $content.= '"'.$mother_office_phone_number.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $father_office_phone_number = get_proposal_meta($proposal_id,'father_office_phone_number');
	 $content = "";
	 $content.= "Father’s office number and cell phone,";
	 $content.= '"'.$father_office_phone_number.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $father_phone_number = get_proposal_meta($proposal_id,'father_phone_number');
	 $content = "";
	 $content.= "Father’s phone,";
	 $content.= '"'.$father_phone_number.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $candidate_legacy = get_proposal_meta($proposal_id,'candidate_legacy');
	 $content = "";
	 $content.= "Is this candidate a legacy?,";
	 $content.= '"'.$candidate_legacy.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $daughter_of_cotillion_deb = get_proposal_meta($proposal_id,'daughter_of_cotillion_deb');
	 $content = "";
	 $content.= "Daughter of Cotillion Deb (year if known),";
	 $content.= '"'.$daughter_of_cotillion_deb.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $daughter_of_board_member = get_proposal_meta($proposal_id,'daughter_of_board_member');
	 $content = "";
	 $content.= "Daughter of Board Member,";
	 $content.= '"'.$daughter_of_board_member.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $sister_of_cotillion_deb = get_proposal_meta($proposal_id,'sister_of_cotillion_deb');
	 $content = "";
	 $content.= "Sister of Cotillion Deb (year if known),";
	 $content.= '"'.$sister_of_cotillion_deb.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $relative_Associated_with_nmh = get_proposal_meta($proposal_id,'relative_Associated_with_nmh');
	 $content = "";
	 $content.= "Relative associated with NMH or Cotillion,";
	 $content.= '"'.$relative_Associated_with_nmh.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $other = get_proposal_meta($proposal_id,'other');
	 $content = "";
	 $content.= "Other (describe),";
	 $content.= '"'.$relative_Associated_with_nmh.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $content = "";
	 $content.= "Who asked you to propose this candidate? Please provide name";
	 $content.= PHP_EOL ;
	  
	 $family_of_candidate = get_proposal_meta($proposal_id,'family_of_candidate');
	 $content = "";
	 $content.= "Family of Candidate,";
	 $content.= '"'.$family_of_candidate.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $friend_of_candidate = get_proposal_meta($proposal_id,'friend_of_candidate');
	 $content = "";
	 $content.= "Friend of Candidate,";
	 $content.= '"'.$friend_of_candidate.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $board_member = get_proposal_meta($proposal_id,'board_member');
	 $content = "";
	 $content.= "Board Member,";
	 $content.= '"'.$board_member.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $myself = get_proposal_meta($proposal_id,'myself');
	 $content = "";
	 $content.= "Myself,";
	 $content.= '"'.$myself.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $other_propose_candidate = get_proposal_meta($proposal_id,'other_propose_candidate');
	 $content = "";
	 $content.= "Other,";
	 $content.= '"'.$other_propose_candidate.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	

	 $content = "";
	 $content.= "Has an older brother participated on the Floor Committee? ";
	 $content.= PHP_EOL ;
	 
	 $older_brother_name_year = get_proposal_meta($proposal_id,'older_brother_name_year');
	 $content = "";
	 $content.= "If so name and year,";
	 $content.= '"'.$older_brother_name_year.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $candidate_her_family_with_nmh = get_proposal_meta($proposal_id,'candidate_her_family_with_nmh');
	 $content = "";
	 $content.= "Is the candidate or her family affiliated with NMH?,";
	 $content.= '"'.$candidate_her_family_with_nmh.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $how_affiliated_with_nmh = get_proposal_meta($proposal_id,'how_affiliated_with_nmh');
	 $content = "";
	 $content.= "How?,";
	 $content.= '"'.$how_affiliated_with_nmh.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $step_parents_name_address = get_proposal_meta($proposal_id,'step_parents_name_address');
	 $content = "";
	 $content.= "Step-parents’ names and address if applicable,";
	 $content.= '"'.$step_parents_name_address.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $school_attended_by_candidate = get_proposal_meta($proposal_id,'school_attended_by_candidate');
	 $content = "";
	 $content.= "Schools attended by the candidate,";
	 $content.= '"'.$school_attended_by_candidate.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $current_school_grade = get_proposal_meta($proposal_id,'current_school_grade');
	 $content = "";
	 $content.= "Current school and grade,";
	 $content.= '"'.$current_school_grade.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $hobbies = get_proposal_meta($proposal_id,'hobbies');
	 $content = "";
	 $content.= "Hobbies,";
	 $content.= '"'.$hobbies.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $interests = get_proposal_meta($proposal_id,'interests');
	 $content = "";
	 $content.= "Interests,";
	 $content.= '"'.$interests.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $notable_accomplishments = get_proposal_meta($proposal_id,'notable_accomplishments');
	 $content = "";
	 $content.= "Notable accomplishments,";
	 $content.= '"'.$notable_accomplishments.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $relation_to_candidate = get_proposal_meta($proposal_id,'relation_to_candidate');
	 $content = "";
	 $content.= " What is your relation to the candidate and her family? How long and how close is your acquaintance with one another? Do you know the girl as well as her parents? ,";
	 $content.= '"'.$relation_to_candidate.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $personal_comments = get_proposal_meta($proposal_id,'personal_comments');
	 $content = "";
	 $content.= "Additional personal comments ,";
	 $content.= '"'.$personal_comments.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $content = "";
	 $content.= "";
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $content = "";
	 $content.= "First Responser Detail";
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $content = "";
	 $content.= "";
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $debutante_candidate_name = get_proposal_meta($proposal_id,'debutante_candidate_name');
	 $content = "";
	 $content.= "Full Name of Debutante Candidate ,";
	 $content.= '"'.$debutante_candidate_name.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $submitted_by_board_member = get_proposal_meta($proposal_id,'submitted_by_board_member');
	 $content = "";
	 $content.= "Submitted by Board Member ,";
	 $content.= '"'.$submitted_by_board_member.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
		
	 $content = "";
	 $content.= "Letter Writer's Name ,";
	 $content.= '"'.$receiver_name1_val.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $date = get_seconder_meta($proposal_id,$receiver_name1,'date');
	 $content = "";
	 $content.= "Date ,";
	 $content.= '"'.$date.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $body_of_letter = get_seconder_meta($proposal_id,$receiver_name1,'body_of_letter');
	 $content = "";
	 $content.= "Body of letter ,";
	 $content.= '"'.$body_of_letter.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 
	 $content = "";
	 $content.= "";
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $content = "";
	 $content.= "Second Responser Detail";
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $content = "";
	 $content.= "";
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $debutante_candidate_name2 = get_proposal_meta($proposal_id,'debutante_candidate_name');
	 $content = "";
	 $content.= "Full Name of Debutante Candidate ,";
	 $content.= '"'.$debutante_candidate_name2.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 $submitted_by_board_member2 = get_proposal_meta($proposal_id,'submitted_by_board_member');
	 $content = "";
	 $content.= "Submitted by Board Member ,";
	 $content.= '"'.$submitted_by_board_member2.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
		
	 $content = "";
	 $content.= "Letter Writer's Name ,";
	 $content.= '"'.$receiver_name2_val.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $date2 = get_seconder_meta($proposal_id,$receiver_name2,'date');
	 $content = "";
	 $content.= "Date ,";
	 $content.= '"'.$date2.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);	
	 
	 $body_of_letter2 = get_seconder_meta($proposal_id,$receiver_name2,'body_of_letter');
	 $content = "";
	 $content.= "Body of letter ,";
	 $content.= '"'.$body_of_letter2.'",' ;
	 $content.= PHP_EOL ;
	 fwrite($myfile, $content);
	 
	 header("Content-type: text/csv");
	 header("Content-disposition: attachment; filename = export_proposal.csv");
	 readfile($tmpfile);

    // output the CSV data
}


/*------------- View sent Proposals ----------*/
add_action('admin_menu', 'wpdocs_register_my_custom_submenu_page');
 
function wpdocs_register_my_custom_submenu_page() {
    add_submenu_page( 
        'proposals',   //or 'options.php'
        'Proposal Invitations',
        'Proposal Invitations',
        'manage_options',
        'list-proposal-invitation',
        'list_proposal_invitation'
    );
	
	add_submenu_page( 
        'proposals',   //or 'options.php'
        NULL,
        NULL,
        'manage_options',
        'add-proposal-invitation',
        'add_proposal_invitation'
    );
}

/******* List of all sent proposal invitations ******************/


function list_proposal_invitation()
{
	wp_enqueue_style( 'proposal-bootstrap-css', plugins_url('css/bootstrap.min.css', __FILE__  ));
	wp_enqueue_style( 'proposal-datatable-css', plugins_url('css/jquery.datatables.css', __FILE__  ));
	wp_enqueue_script( 'proposal-bootstrap-js' , plugins_url( 'js/bootstrap.min.js', __FILE__ ) );
	wp_enqueue_script( 'proposal-datatable-js' , plugins_url( 'js/jquery.datatables.min.js', __FILE__ ) );
	?>
	<script>
	jQuery(document).ready(function(){ //newly added
		var path = window.location.origin;
		jQuery(".inv_delete").live( "click", function() {
		var inv_id = jQuery(this).attr('id');
		var checkstr = confirm('Are you sure to delete this invitation?');	
			if(checkstr == true) {
				jQuery.ajax({
					type: 'post',
					url:  path+"/wp-admin/admin-ajax.php",
					dataType:'text',
					async:false,
					data:{action:'delete_invitation',id:inv_id},
					success: function(response) {	
						//alert(response);
						window.location.reload();
					}
				});
			}
		});
	});
	</script>

	<style>
	.success {background: #008000;color: #fff;display: block; margin: 10px 0; padding: 10px 0 10px 20px; width: 100%;}
	.error {background: #ee3930;color: #fff;display: block; margin: 10px 0; padding: 10px 0 10px 20px; width: 100%;}
	.prop_anchor {color: #ee3930;}
	.anch-link{ display: block; padding: 10px 0 10px 18px;}
	.form-control{width: 33%!important;}
	.acceess-error {display: block;font-size: 20px;padding: 0 0 10px 17px;}
	.table {border-bottom: 1px solid #ddd;}
	</style>
	<?php 
	session_start();
	$logged_in = get_current_user_id();
	$user = wp_get_current_user();
	if ( in_array( 'administrator', (array) $user->roles ) ) {
		$is_admin = 1;
		$created_by = '';
	}
	elseif( in_array( 'committee chair', (array) $user->roles )){
		$is_admin = 0;
		$created_by = ' WHERE created_by = '.$logged_in;
	}
	if(isset($_SESSION['message']) && $_SESSION['message']['type'] !='')
	{ 
		// echo  '<pre>'; print_r($_SESSION); echo '</pre>';
	?>
		<div class="message-bar">
			<?php
			if( $_SESSION['message']['type'] == 'success' ){ ?>
				<span class="success"><?php echo $_SESSION['message']['success']; ?></span>
			<?php
			}
			elseif($_SESSION['message']['type'] == 'error'){
			?>
			<span class="error"><?php echo $_SESSION['message']['error']; ?></span>
	  <?php } ?>
		</div>
	<?php
	unset($_SESSION['message']);
	}
	?>
	 <br>
	 <br>
    <div class="panel panel-default">
		<?php
			$page_url = $editUrl = admin_url().'admin.php?page=list-proposal-invitation';
			if(( isset($_REQUEST['proposal-id'] ) && $_REQUEST['proposal-id'] !='') && $_REQUEST['action'] == 'view')
			{
				$proposalId = $_REQUEST['proposal-id'];
				global $wpdb;
				$proposal_invitation = $wpdb->prefix.'proposal_invitation';
				$proposal_arr = $wpdb->get_row("select * from $proposal_invitation where id='$proposalId'",'ARRAY_A');
				$proposal_title = $proposal_arr['proposal_title'];
				$proposal_desc = $proposal_arr['proposal_desc'];
				$date_sent = $proposal_arr['date_sent'];
				$created_by = $proposal_arr['created_by'];
				$proposerId = $proposal_arr['proposer'];
				$committeeChairId = $proposal_arr['committee_chair'];
				$proposer_arr = get_user_by( 'ID', $proposerId );
				$proposer_email = $proposer_arr->user_email;
				$proposer_display_name = $proposer_arr->display_name;
				
				$committeeChair_arr = get_user_by( 'ID', $committeeChairId );
				
				$committeeChair_display_name = $committeeChair_arr->display_name;
				$statusVal = $proposal_arr['status'];
				if($statusVal == 0)
				{
					$status = 'Pending';
				}
				else{
					$status = 'Submitted';
				}
				if($logged_in == $created_by || $is_admin ==1)
				{
					$proposal_invitation_seconders = $wpdb->prefix.'proposal_invitation_seconders';
					$sql = "select * from $proposal_invitation_seconders where proposal_invitation_id = $proposalId";
					$seconders_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 
					
					foreach($seconders_arr as $seconders)
					{
						$seconder_id = $seconders['seconder_id'];
						$letterwriters = get_user_by( 'ID', $seconder_id );
						$letterwritersArr[] = $letterwriters->display_name;
					}
					
					
					
					?>
					<span class="anch-link"> <a href="<?php echo $page_url; ?>">Back</a></span>
					 <div class="panel panel-default">
						<div class="panel-heading">	
							<h3 class="panel-title" style="display:inline">View Sent Proposal Detail</h3>
						</div>
						
					   <div class="panel-body table-responsive">
							<div class="table-responsive">
								<table class="table table-hover">
								  <tbody>
								   <tr>
										<th>Debutante Candidate</th>
										<td><?php echo $proposal_title; ?></td>
									<tr>
										<th>Proposal Description</th>
										<td><?php echo $proposal_desc; ?>	</td>
									</tr>
									<tr>	
										<th>Committee Chair</th>
										<td><?php echo $committeeChair_display_name; ?></td>
									</tr>
									<tr>	
										<th>Proposer Name</th>
										<td><?php echo $proposer_display_name; ?></td>
									</tr>
									<tr>	
										<th>Letter Writers</th>
										<td><?php echo implode(', ', $letterwritersArr);; ?></td>
									</tr>
									<tr>	
										<th>Date</th>
										<td><?php echo $date_sent; ?></td>
									</tr>
									<tr>
										<th>Proposal Status</th>
										<td><?php echo $status; ?></td>
									</tr>

								  </tbody>          
							   </table>
							</div>
						</div><!-- /.box-body -->
					 </div>
				 <?php
				}
				else{ ?>
					<span class="anch-link"><a href="<?php echo $page_url; ?>">Back</a></span>
					<span class="acceess-error">Access denied.</span>
				<?php
				}
			}
			elseif(( isset($_REQUEST['proposal-id'] ) && $_REQUEST['proposal-id'] !='') && $_REQUEST['action'] == 'edit')
			{
				$proposalId = $_REQUEST['proposal-id'];
				global $wpdb;
				$proposal_invitation = $wpdb->prefix.'proposal_invitation';
				$proposal_arr = $wpdb->get_row("select * from $proposal_invitation where id='$proposalId'",'ARRAY_A');
				$proposal_title = $proposal_arr['proposal_title'];
				$proposal_desc = $proposal_arr['proposal_desc'];
				$date_sent = $proposal_arr['date_sent'];
				$created_by = $proposal_arr['created_by'];
				$proposerId = $proposal_arr['proposer'];
				$committeeChairId = $proposal_arr['committee_chair'];
				$form_id = $proposal_arr['form_id'];
				
				$proposal_invitation_seconders = $wpdb->prefix.'proposal_invitation_seconders';
				$sql = "select * from $proposal_invitation_seconders where proposal_invitation_id = $proposalId";
				$seconders_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 
				
				$secondersId_arr = array();
				foreach($seconders_arr as $seconders)
				{
					$secondersId_arr[] = $seconders['seconder_id'];
				}
				
				//Proposer users dropdown
				$pargs = array(
					'show_option_all'         => null, // string
					'show_option_none'        => null, // string
					'hide_if_only_one_author' => null, // string
					'orderby'                 => 'display_name',
					'order'                   => 'ASC',
					'include'                 => null, // string
					'exclude'                 => null, // string
					'multi'                   => false,
					'show'                    => 'display_name',
					'echo'                    => 0,
					'selected'                => $proposerId,
					'include_selected'        => false,
					'name'                    => 'send_proposal_proposer', // string
					'id'                      => null, // integer
					'class'                   => null, // string 
					'blog_id'                 => $GLOBALS['blog_id'],
					'who'                     => null, // string,
					'role'                    => null, // string|array,
					'role__in'                => null, // array    
					//'role__not_in'            => array('Subscriber','Administrator'), // array        
					'role__not_in'            => null, // array        
				);
				$pdropdown = wp_dropdown_users($pargs);
				//committee member  users dropdown
				$cargs = array(
					'show_option_all'         => null, // string
					'show_option_none'        => null, // string
					'hide_if_only_one_author' => null, // string
					'orderby'                 => 'display_name',
					'order'                   => 'ASC',
					'include'                 => null, // string
					'exclude'                 => null, // string
					'multi'                   => false,
					'show'                    => 'display_name',
					'echo'                    => 0,
					'selected'                => $committeeChairId,
					'include_selected'        => false,
					'name'                    => 'send_proposal_committee', // string
					'id'                      => null, // integer
					'class'                   => null, // string 
					'blog_id'                 => $GLOBALS['blog_id'],
					'who'                     => null, // string,
					'role'                    => 'committee chair', // string|array,
					'role__in'                => null, // array    
					//'role__not_in'          => array('Subscriber','Administrator'), // array        
					'role__not_in'            => null, // array        
				);
				$cdropdown = wp_dropdown_users($cargs);
				
				
				if($logged_in == $created_by  || $is_admin ==1)
				{
				
					?>
					<!--- Add/send new proposal invitation form --->
					 <span class="anch-link"><a href="<?php echo $page_url; ?>">Back</a></span>
					<div class="panel panel-default">
						<div class="panel-heading">	
							<h3 class="panel-title" style="display:inline">Update Proposal Invitation</h3>
						</div>

						<div class="panel-body">
							<form class="form-horizontal" name="send-invitation-form" method="post" action="<?php site_url();?>/wp-admin/admin.php?page=list-proposal-invitation&proposal-id=1&action=update-invitation">
								<div class="form-group">
									<label class="control-label col-sm-2" >Debutante Candidate </label>
									<div class="col-sm-10">
										<input type="text" class="form-control" name="send_proposal_title" value="<?php echo $proposal_title; ?>" required>
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-sm-2" >Proposal Description </label>
									<div class="col-sm-10">
										<textarea  rows="4" cols="53"  name="send_proposal_description" required><?php echo $proposal_desc; ?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-2" >Proposer</label>
									<div class="col-sm-10">
										<?php echo $pdropdown; ?>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-2" >Letter Writers</label>
									<div class="col-sm-10">
										<?php 
										 $args = array(
											'role' => 'subscriber'
										);
										$users = get_users($args);
										if( empty($users) )
										  return;
										echo'<select name ="send_proposal_seconders[]" multiple="multiple">';
										foreach( $users as $user ){
											if (in_array($user->data->ID, $secondersId_arr))
											{
												$selected = 'selected="selected"';
											}
											else
											{
												$selected = '';
											}
				
											echo '<option value="'.$user->data->ID.'" '.$selected.'>'.$user->data->display_name.'</option>';
										}
										echo'</select>';
										?>
										<p style="font-size:11px">Holding CTRL to select multiple letter writers</p>
									</div>
									
								</div>
								<div class="form-group">
									<label class="control-label col-sm-2" >Committee Chair</label>
									<div class="col-sm-10">
										<?php echo $cdropdown; ?>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
									<input type="hidden" name="proposalId" value="<?php echo $proposalId; ?>">
									<input type="hidden" name="proposal_prev_title" value="<?php echo $proposal_title; ?>">
									<input type="hidden" name="proposal_prev_form_id" value="<?php echo $form_id; ?>">
										<button type="submit" class="btn btn-primary" name="update_invitation" value="1">Update</button>
									</div>
								</div>
							</form>
						</div>
					 </div>
				 <?php
				}else{?>
					<span class="anch-link"><a href="<?php echo $page_url; ?>">Back</a></span>
					<span class="acceess-error">Access denied.</span>
				<?php
				}
			}
			elseif(( isset($_REQUEST['proposal-id'] ) && $_REQUEST['proposal-id'] !='') && $_REQUEST['action'] == 'update-invitation')
			{
				session_start();
				$logged_in = get_current_user_id();
				
				$send_proposal_title = $_POST['send_proposal_title'];
				$proposal_prev_title = $_POST['proposal_prev_title'];
				$proposal_prev_form_id = $_POST['proposal_prev_form_id'];
				$send_proposal_description = $_POST['send_proposal_description'];
				$send_proposal_proposer = $_POST['send_proposal_proposer'];
				$committeeChairId = $_POST['send_proposal_committee'];
				$proposalId = $_POST['proposalId'];
				$send_proposal_seconders = $_POST['send_proposal_seconders'];
				$now = new DateTime(); //string value use: %s
				$date_sent = $now->format('Y-m-d H:i:s'); //string value use: %s
				$form_id = generateRandomString(10); // for send in email with form URL
				
				 
				global $wpdb;
				$proposal_invitation = $wpdb->prefix . "proposal_invitation";
				$query = "UPDATE $proposal_invitation SET `proposal_title` = '$send_proposal_title',`proposal_desc` = '$send_proposal_description', `proposer` = $send_proposal_proposer, `committee_chair` = $committeeChairId where id = $proposalId";
				$wpdb->query($query);
				
				
				//save seconders data into another table
				//delete old record
				$proposal_invitation_seconders = $wpdb->prefix . "proposal_invitation_seconders";
				$querys = "DELETE FROM $proposal_invitation_seconders where proposal_invitation_id = $proposalId";
				$wpdb->query($querys);
				$allLetterWriters = array();
				foreach($send_proposal_seconders  as $row){
					$wpdb->insert($proposal_invitation_seconders, array(
					'id' => '',
					'proposal_invitation_id' => $proposalId,
					'seconder_id' => $row
					));
					$letterwriters = get_user_by( 'ID', $row );
					$letterwritersArr[] = $letterwriters->display_name;
				}
				$allLetterWriters  = implode(', ', $letterwritersArr);
				//send email with form link to proposer 
				$form_url = site_url().'/proposal?formid='.$proposal_prev_form_id;
				$proposer_arr = get_user_by( 'ID', $send_proposal_proposer );
				$proposer_email = $proposer_arr->user_email;
				$proposer_display_name = $proposer_arr->display_name;
				$to = $proposer_email;
				$subject = 'Proposal Invitation Updated';
								
				$html = '<p>Dear <b>'.$proposer_display_name.'</b>,</p><p>Proposal invitation '.$proposal_prev_title.' has been updated by admin.<br> The Debutante Selection Committee requests that you submit a Debutante Proposal for candidate, '.$send_proposal_title.' The seconding letter writer(s) are '.$allLetterWriters.'. <br>Here are notes, if any, regarding this candidate from the committee: '.$send_proposal_description.'. <br><br>Please submit this Debutante Proposal using this link <a href="'.$form_url.'">'.$form_url.'</a><br/>Thank you.<br>The Debutante Selection Committee</p>';

				$headers[] = 'Content-type: text/html; charset=iso-8859-1';
				$headers[] = 'From: Proposal Invitation <info@wbnorthwestern.org>';

				if(wp_mail( $to, $subject, $html, $headers ))
				{
					$_SESSION['message']['success'] = 'Proposal invitation updated successfully.';
					$_SESSION['message']['type'] = 'success';
					$url = site_url().'/wp-admin/admin.php?page=list-proposal-invitation';
					wp_redirect($url);
					exit;
				}
				else{
					$_SESSION['message']['error'] = 'Somthing went wrong. Please try again.';
					$_SESSION['message']['type'] = 'error';
					wp_redirect();
					exit;
				}
			}
			elseif(( isset($_REQUEST['proposal-id'] ) && $_REQUEST['proposal-id'] !='') && $_REQUEST['action'] == 'resend-reminder')
			{
				session_start();
				$proposalId = $_REQUEST['proposal-id'];
				global $wpdb;
				$proposal_invitation = $wpdb->prefix.'proposal_invitation';
				$proposal_arr = $wpdb->get_row("select * from $proposal_invitation where id='$proposalId'",'ARRAY_A');
				$proposal_title = $proposal_arr['proposal_title'];
				$proposal_desc = $proposal_arr['proposal_desc'];
				$date_sent = $proposal_arr['date_sent'];
				$created_by = $proposal_arr['created_by'];
				$proposerId = $proposal_arr['proposer'];
				$committeeChairId = $proposal_arr['committee_chair'];
				$form_id = $proposal_arr['form_id'];
				if($logged_in == $created_by)
				{
					$proposal_invitation_seconders = $wpdb->prefix.'proposal_invitation_seconders';
					$sql = "select * from $proposal_invitation_seconders where proposal_invitation_id = $proposalId";
					$seconders_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 
					
					$secondersId_arr = array();
					$letterwritersArr = array();
					foreach($seconders_arr as $seconders)
					{
						$secondersId_arr[] = $seconders['seconder_id'];
						$letterwriters = get_user_by( 'ID', $seconders['seconder_id'] );
						$letterwritersArr[] = $letterwriters->display_name;
					}
					$secondersIdName = implode(', ', $secondersId_arr);
					$secondersName = implode(', ', $letterwritersArr);
					//send email with form link to proposer 
					$form_url = site_url().'/proposal?formid='.$form_id;
					$proposer_arr = get_user_by( 'ID', $proposerId );
					$proposer_email = $proposer_arr->user_email;
					$proposer_display_name = $proposer_arr->display_name;
					
					$committeeChair_arr = get_user_by( 'ID', $committeeChairId );
					$committeeChair_arr_email = $committeeChair_arr->user_email;
					$committeeChair_arr_display_name = $committeeChair_arr->display_name;
					
					
					$to = $proposer_email;
					$subject = 'Proposal Invitation Reminder';
									
					$html = '<p>Dear <b>'.$proposer_display_name.'</b>,</p><p>The Debutante Selection Committee requests that you submit a Debutante Proposal for candidate, '.$proposal_title.' <br><br>The seconding letter writer(s) are '.$secondersName.'. <br>Here are notes, if any, regarding this candidate from the committee: '.$proposal_desc.'. <br><br>Please submit this Debutante Proposal using this link <a href="'.$form_url.'">'.$form_url.'</a><br/>You will receive a reminder each week until the proposal is submitted. <br/><br/> Thank you.<br>The Debutante Selection Committee</p>';
					
				
					$headers[] = 'Content-type: text/html; charset=iso-8859-1';
					$headers[] = 'From: Proposal Invitation <info@wbnorthwestern.org>';
					
					if(wp_mail( $to, $subject, $html, $headers ))
					{
						$_SESSION['message']['success'] = 'Email reminder sent for invitation successfully.';
						$_SESSION['message']['type'] = 'success';
						$url = site_url().'/wp-admin/admin.php?page=list-proposal-invitation';
						wp_redirect($url);
						exit;
					}
					else{
						$_SESSION['message']['error'] = 'Somthing went wrong. Please try again.';
						$_SESSION['message']['type'] = 'error';
						wp_redirect();
						exit;
					}
				}else{?>
					<span class="anch-link"><a href="<?php echo $page_url; ?>">Back</a></span>
					<span class="acceess-error">Access denied.</span>
				<?php
				}
			}
			else
			{
				global $wpdb;
				$page_url  = admin_url().'admin.php?page=list-proposal-invitation';
				$proposal_invitation_table = $wpdb->prefix.'proposal_invitation';  
				$sql = "select * from $proposal_invitation_table ".$created_by;
				$count_proposal_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 
				$page	=	"" ;
				$lastpage	=	"" ;
				$numrows  = "" ;
				$total_prop = count($count_proposal_arr);
				if(isset($_REQUEST['pageid']))
				{
					$page = $_REQUEST['pageid'];
				}
				else
				{
					$page	=	1 ;
				}

				$numrows	=	$total_prop ;
				$rows_per_page	=	10;

				// Calculate number of $lastpage
				$lastpage = ceil($numrows/$rows_per_page);

				// validate/limit requested $pageno
				$page = (int)$page;
				if ($page > $lastpage) {
					$page = $lastpage;
				}
				if ($page < 1) {
						$page = 1;
					}
				$currentpage = !empty($page) ? (integer)$page : 1;
				$start = ($page - 1) * $rows_per_page;
				$end = $start + $rows_per_page -1;

				if($end > $numrows - 1){
					$end = $numrows - 1;
				}
			
			
			?>
			

			<span class="anch-link"><a href="<?php echo admin_url();?>admin.php?page=add-proposal-invitation"> Add Proposal Invitation</a></span>
			<div class="panel-heading">	
				<h3 class="panel-title" style="display:inline">Proposals - Total <?php echo count($count_proposal_arr); ?> </h3>
			</div>
			<div class="panel-body table-responsive">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Debutante Candidate</th>
								<th>Proposal Description</th>
								<th>Committee Chair</th>
								<th>Proposer</th>
								<th>Letter Writers</th>
								<th>Added On</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>  
						<tbody>
							<?php
							
							$query = "select * from $proposal_invitation_table ".$created_by." ORDER BY id DESC limit $start,$rows_per_page";
							$proposal_arr = $wpdb->get_results( $query, 'ARRAY_A' ); 
							if(!empty($proposal_arr))
							{
								$i = 1;
								foreach($proposal_arr as $row)
								{
									$proposerInvitationId = $row['id'];
									$proposerId = $row['proposer'];
									$committeeChairId = $row['committee_chair'];
									$date_sent = $row['date_sent'];
									$proposer_arr = get_user_by( 'ID', $proposerId );
									$proposer_email = $proposer_arr->user_email;
									$proposer_display_name = $proposer_arr->display_name;
									
									$letterwritersArr = get_user_by( 'ID', $committeeChairId );
									$letterwritersName = $letterwritersArr->display_name;
									$statusVal = $row['status'];
									if($statusVal == 0)
									{
										$status = 'Pending';
										$resendUrl = admin_url().'admin.php?page=list-proposal-invitation&proposal-id='.$proposerInvitationId.'&action=resend-reminder';
										$resend = '<a  class="prop_anchor" href="'.$resendUrl.'">Resend Reminder</a>';
									}
									else{
										$status = 'Submitted';
										$resend = '';
									}
									$editUrl = admin_url().'admin.php?page=list-proposal-invitation&proposal-id='.$proposerInvitationId.'&action=edit';
							
									$viewUrl = admin_url().'admin.php?page=list-proposal-invitation&proposal-id='.$proposerInvitationId.'&action=view';
									
									//Letter writers
									$proposal_invitation_seconders = $wpdb->prefix.'proposal_invitation_seconders';
									$sql = "select * from $proposal_invitation_seconders where proposal_invitation_id = $proposerInvitationId";
									$seconders_arr = $wpdb->get_results( $sql, 'ARRAY_A' ); 
									$letterwritersArr = array();
									foreach($seconders_arr as $seconders)
									{
										$seconder_id = $seconders['seconder_id'];
										$letterwriters = get_user_by( 'ID', $seconder_id );
										$letterwritersArr[] = $letterwriters->display_name;
									}
								?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $row['proposal_title']; ?></td>
										<td><?php 
											if(strlen($row['proposal_desc'])<50) {
												echo $row['proposal_desc'];
											}
											else {
												echo substr(strip_tags($row['proposal_desc']), 0, 50).'...';
										}
										?></td>
										<td><?php echo $letterwritersName; ?></td>
										<td><?php echo $proposer_display_name; ?></td>
										<td><?php echo implode(', ', $letterwritersArr);; ?></td>
										<td><?php echo $date_sent; ?></td>
										<td><?php echo $status; ?><br><?php echo $resend; ?></td>
										<td><a  class="prop_anchor" href="<?php echo $viewUrl; ?>">View</a> / <a  class="prop_anchor" href="<?php echo $editUrl; ?>">Edit</a> / <a  class="prop_anchor inv_delete" href="javascript:void(0);" id="<?php echo $proposerInvitationId; ?>">Delete</a></td>
									</tr>
								<?php
								$i++;
								}
							}
							?>
						</tbody>
					</table>
					<ul class="pagination">
						<?php 
						$ptemp = $page_url;
						$pages = '';
						if ($currentpage != 1) 
						{ //GOING BACK FROM PAGE 1 SHOULD NOT BET ALLOWED
							$previous_page = $currentpage - 1;
							//$previous = '<a href="'.$ptemp.'?pageno='.$previous_page.'"> </a> '; 
							$previous = '&lt;Previous' ;
							$pages .= '<li><a href="'.$ptemp.'&pageid='.$previous_page.'">'. $previous .'</a></li>'; 
						}    
						$adjacents = 2;
						$pmin = ($currentpage > $adjacents) ? ($currentpage - $adjacents) : 1;
						$pmax = ($currentpage < ($lastpage - $adjacents)) ? ($currentpage + $adjacents) : $lastpage;
						for ($i = $pmin; $i <= $pmax; $i++) {
							if ($i == $currentpage) {
								$pages.= "<li  class=\"active\"><a href='#'>" . $i . "</a></li>\n";
							} elseif ($i == 1) {
								$pages.= "<li><a  href=\"" . $ptemp ."&pageid=".$i. "\"  rel=".$i.">" . $i . "</a>\n</li>";
							} else {
								$pages.= "<li><a  href=\"" . $ptemp . "&pageid=" . $i . "\"  rel=".$i." >" . $i . "</a>\n</li>";
							}
						}
						if($currentpage != $lastpage) 
						{
							//GOING AHEAD OF LAST PAGE SHOULD NOT BE ALLOWED
							$next_page = $currentpage + 1;
							$next = 'Next&gt;';
							$pages .= '<li><a href="'.$ptemp.'&pageid='.$next_page.$whereStr1.'">'. $next .'</a></li>';
						}

						if(!empty($numrows)) {
							echo   $pages ; //PAGINATION LINKS
						}
						?>
					</ul>	

				</div>
			</div>
	<?php } ?>
	</div>
	
	<?php
	
	
}

/*----- Send Proposal invitation to proposar -------------------*/

function add_proposal_invitation()
{
	wp_enqueue_style( 'proposal-bootstrap-css', plugins_url('css/bootstrap.min.css', __FILE__  ));
	wp_enqueue_style( 'proposal-datatable-css', plugins_url('css/jquery.datatables.css', __FILE__  ));
	wp_enqueue_script( 'proposal-bootstrap-js' , plugins_url( 'js/bootstrap.min.js', __FILE__ ) );
	wp_enqueue_script( 'proposal-datatable-js' , plugins_url( 'js/jquery.datatables.min.js', __FILE__ ) );
	//Proposer users dropdown
	$pargs = array(
		'show_option_all'         => null, // string
		'show_option_none'        => null, // string
		'hide_if_only_one_author' => null, // string
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => null, // string
		'exclude'                 => null, // string
		'multi'                   => false,
		'show'                    => 'display_name',
		'echo'                    => 0,
		'selected'                => false,
		'include_selected'        => false,
		'name'                    => 'send_proposal_proposer', // string
		'id'                      => null, // integer
		'class'                   => null, // string 
		'blog_id'                 => $GLOBALS['blog_id'],
		'who'                     => null, // string,
		'role'                    => null, // string|array,
		'role__in'                => null, // array    
		//'role__not_in'            => array('Subscriber','Administrator'), // array        
		'role__not_in'            => null, // array        
	);
	$pdropdown = wp_dropdown_users($pargs);

	//Seconders users dropdown
	$sargs = array(
		'show_option_all'         => null, // string
		'show_option_none'        => null, // string
		'hide_if_only_one_author' => null, // string
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => null, // string
		'exclude'                 => null, // string
		'multi'                   => false,
		'show'                    => 'display_name',
		'echo'                    => 0,
		'selected'                => false,
		'include_selected'        => false,
		'name'                    => 'send_proposal_seconders[]', // string
		'id'                      => null, // integer
		'class'                   => null, // string 
		'blog_id'                 => $GLOBALS['blog_id'],
		'who'                     => null, // string,
		'role'                    => null, // string|array,
		'role__in'                => 'Subscriber', // array    
		'role__not_in'            => 'Administrator', // array        
	);
	$sdropdown = wp_dropdown_users($sargs);
	$sdropdown = str_replace('id=', 'multiple="multiple" id=', $sdropdown);
	
	//committee member  users dropdown
	$cargs = array(
		'show_option_all'         => null, // string
		'show_option_none'        => null, // string
		'hide_if_only_one_author' => null, // string
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => null, // string
		'exclude'                 => null, // string
		'multi'                   => false,
		'show'                    => 'display_name',
		'echo'                    => 0,
		'selected'                => false,
		'include_selected'        => false,
		'name'                    => 'send_proposal_committee', // string
		'id'                      => null, // integer
		'class'                   => null, // string 
		'blog_id'                 => $GLOBALS['blog_id'],
		'who'                     => null, // string,
		'role'                    => 'committee chair', // string|array,
		'role__in'                => null, // array    
		//'role__not_in'          => array('Subscriber','Administrator'), // array        
		'role__not_in'            => null, // array        
	);
	$cdropdown = wp_dropdown_users($cargs);
	
?>
	<style>
		.success {background: #008000;color: #fff;display: block; margin: 10px 0; padding: 10px 0 10px 20px; width: 100%;}
		.error {background: #ee3930;color: #fff;display: block; margin: 10px 0; padding: 10px 0 10px 20px; width: 100%;}
		.prop_anchor {color: #ee3930;}
		.anch-link{ display: block; padding: 10px 0 10px 18px;}
		.form-control{width: 33%!important;}
		.table {border-bottom: 1px solid #ddd;}
	</style>
	<?php 
	session_start();
	if(isset($_SESSION['message']) && $_SESSION['message']['type'] !='')
	{ 
		// echo  '<pre>'; print_r($_SESSION); echo '</pre>';
	?>
		<div class="message-bar">
			<?php
			if( $_SESSION['message']['type'] == 'success' ){ ?>
				<span class="success"><?php echo $_SESSION['message']['success']; ?></span>
			<?php
			}
			elseif($_SESSION['message']['type'] == 'error'){
			?>
			<span class="error"><?php echo $_SESSION['message']['error']; ?></span>
	  <?php } ?>
		</div>
	<?php
	unset($_SESSION['message']);
	}
	?>
	<!--- Add/send new proposal invitation form --->
	<div class="panel panel-default">
		<div class="panel-heading">	
			<h3 class="panel-title" style="display:inline">Add New Proposal Invitation</h3>
		</div>

		<div class="panel-body">
			<form class="form-horizontal" name="send-invitation-form" method="post" action="<?php site_url();?>/wp-admin/admin.php?page=add-proposal-invitation&action=send-invitation">
				<div class="form-group">
					<label class="control-label col-sm-2" >Debutante Candidate </label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="send_proposal_title" value="" required>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-sm-2" >Proposal Description </label>
					<div class="col-sm-10">
						<textarea  rows="4" cols="53" name="send_proposal_description" required></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" >Proposer</label>
					<div class="col-sm-10">
						<?php echo $pdropdown; ?>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2" >Letter Writers</label>
					<div class="col-sm-10">
						<?php echo $sdropdown; ?>
						<p style="font-size:11px">Holding CTRL to select multiple letter writers</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" >Committee Chair</label>
					<div class="col-sm-10">
						<?php echo $cdropdown; ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-primary" name="send_invitation" value="1">Send</button>
					</div>
				</div>
			</form>
		</div>
	 </div>
	<?php
	//submit form data into database after form submit and send email
	if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'send-invitation' )
	{
		//echo '<pre>'; print_r($_POST);
		session_start();
		$logged_in = get_current_user_id();
		$send_proposal_title = $_POST['send_proposal_title'];
		$send_proposal_description = $_POST['send_proposal_description'];
		$send_proposal_proposer = $_POST['send_proposal_proposer'];
		$send_proposal_seconders = $_POST['send_proposal_seconders'];
		$send_proposal_committee = $_POST['send_proposal_committee'];
		$now = new DateTime(); //string value use: %s
		$date_sent = $now->format('Y-m-d H:i:s'); //string value use: %s
		$form_id = generateRandomString(10); // for send in email with form URL
		
		global $wpdb;
		$proposal_invitation = $wpdb->prefix . 'proposal_invitation';
		$wpdb->insert($proposal_invitation, array(
			'id' => '',
			'proposal_title' => $send_proposal_title,
			'proposal_desc' => $send_proposal_description,
			'proposer' => $send_proposal_proposer,
			'committee_chair' => $send_proposal_committee,
			'date_sent' => $date_sent,
			'form_id' => $form_id,
			'created_by' => $logged_in,
			'status' => 0
		));
		$lastid = $wpdb->insert_id;
		
		//save seconders data into another table
		$letterwritersArr = array();
		$proposal_invitation_seconders = $wpdb->prefix . 'proposal_invitation_seconders';
		foreach($send_proposal_seconders  as $row){
			$wpdb->insert($proposal_invitation_seconders, array(
			'id' => '',
			'proposal_invitation_id' => $lastid,
			'seconder_id' => $row
			));
			$letterwriters = get_user_by( 'ID', $row );
			$letterwritersArr[] = $letterwriters->display_name;
		}
		$allLetterWriters  = implode(', ', $letterwritersArr);
		//send email with form link to proposer 
		$form_url = site_url().'/proposal?formid='.$form_id;
		$proposer_arr = get_user_by( 'ID', $send_proposal_proposer );
		$proposer_email = $proposer_arr->user_email;
		$proposer_display_name = $proposer_arr->display_name;
		
		$committeeChair_arr = get_user_by( 'ID', $send_proposal_committee );
		$committeeChair_arr_email = $committeeChair_arr->user_email;
		$committeeChair_arr_display_name = $committeeChair_arr->display_name;
		
		$to = $proposer_email;
		$subject = 'Proposal Invitation';
		
		$html = '<p>Dear <b>'.$proposer_display_name.'</b>,</p><p>The Debutante Selection Committee requests that you submit a Debutante Proposal for candidate, '.$send_proposal_title.'. <br><br> The seconding letter writer(s) are '.$allLetterWriters.'. <br><br>Here are notes, if any, regarding this candidate from the committee: '.$send_proposal_description.'. <br><br>Please submit this Debutante Proposal using this link <a href="'.$form_url.'">'.$form_url.'</a><br/><br/> Thank you.<br>The Debutante Selection Committee</p>';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';
		$headers[] = 'From: Proposal Invitation <info@wbnorthwestern.org>';

		if(wp_mail( $to, $subject, $html, $headers ))
		{
			$_SESSION['message']['success'] = 'Proposal invitation added successfully';
			$_SESSION['message']['type'] = 'success';
			$url = site_url().'/wp-admin/admin.php?page=list-proposal-invitation';
			wp_redirect($url);
			exit;
		}
		else{
			$_SESSION['message']['error'] = 'Somthing went wrong. Please try again.';
			$_SESSION['message']['type'] = 'error';
			wp_redirect();
			exit;
		}
		
	}
	
}
/*--- Delete a Invitation */
add_action( 'wp_ajax_delete_invitation', 'delete_invitation');
function delete_invitation(){
	global $wpdb;
	if (isset($_POST['id'])){
		$id = $_POST['id'];	
		$table = $wpdb->prefix . "proposal_invitation";
		$table2 = $wpdb->prefix . "proposal_invitation_seconders";
		$qr="DELETE  FROM $table WHERE id = $id";
		$qrw="DELETE  FROM $table2 WHERE proposal_invitation_id = $id";
		$wpdb->get_var( $qrw );	
		$count = $wpdb->get_var( $qr );	
		if ($count == 1) {		
			echo '1';
		} 
		else{ 
			echo '0';
		}
		exit;
	}
}


function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}