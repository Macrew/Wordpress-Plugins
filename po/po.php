<?php
/**
* Plugin Name: PO plugin
* Plugin URI: http://www.memorabilia.expert/
* Description: A custom plugin to manage orders, update commision and transaction history.
* Version: 1.0.0
* Author: Macrew Technologies Pvt. Ltd.
* Author URI: http://www.macrew.net
* License: GPL2
*/

// Block direct requests
//ini_set("display_errors",0);
//if ( !defined('ABSPATH') )
	//die('Not Authorised'); 

ob_start();

class PO
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array( $this, 'init_orders_page' ));
		add_action( 'wp_ajax_get_order_list', array( $this,'get_order_list') );
		add_action( 'wp_ajax_update_commision', array( $this,'update_commision') );
		add_action( 'wp_ajax_update_commision_value', array( $this,'update_commision_value') );	
		add_action( 'wp_ajax_update_commision_value_dynamic', array( $this,'update_commision_value_dynamic') );	
		add_action( 'wp_ajax_save_payment_value', array( $this,'save_payment_value') );
		add_action( 'wp_ajax_save_payment_value_dynamic', array( $this,'save_payment_value_dynamic') );
		add_action( 'wp_ajax_delete_payment_value', array( $this,'delete_payment_value') );	
		add_action( 'wp_ajax_send_email', array( $this,'send_email') );	
		  
        //add_action( 'admin_init', array( $this, 'page_init' ) );
    }
	

    /**
     * Add orders page
     */
    public function init_orders_page()
    {
        add_menu_page('PO', 'PO Plugin', 'administrator', 'purchased-orders', array( $this, 'list_purchased_orders' ), 'dashicons-admin-generic');
    }

    /**
     * Options page callback
     */
    public function list_purchased_orders()
    {
		if($_REQUEST['page'] == 'purchased-orders' && $_REQUEST['view'] == "") {
		wp_enqueue_style( 'po-datatable-style1', plugins_url('css/bootstrap.min.css', __FILE__  ));
		//wp_enqueue_script( 'my-plugin-script1' , plugins_url( 'js/jquery-1.11.1.min.js', __FILE__ ) );
		//wp_enqueue_script( 'my-plugin-script2' , plugins_url( 'js/jquery-migrate-1.2.1.min.js', __FILE__ ) );
		wp_enqueue_script( 'my-plugin-script3' , plugins_url( 'fancybox/jquery.fancybox.js', __FILE__ ) );
		wp_enqueue_script( 'my-plugin-script4' , plugins_url( 'js/custom.js', __FILE__ ) );
		wp_enqueue_script( 'my-plugin-script5' , plugins_url( 'js/jquery.aCollapTable.js', __FILE__ ) );
		 wp_localize_script( 'custom', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
		
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo  plugins_url( 'fancybox/jquery.fancybox.css?v=2.1.5', __FILE__ ); ?>" media="screen" />
		  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		  <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<?php wp_enqueue_script( 'my-plugin-script2' , plugins_url( 'js/jquery-migrate-1.2.1.min.js', __FILE__ ) ); ?>
		<?php 
		
			$content = "" ;
			global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->user_table = "{$wpdb->prefix}users";
		
		if(isset($_REQUEST['search']))
		{

			
			if(!empty($_REQUEST['po_no'])  || !empty($_REQUEST['vendor_name'])  || !empty($_REQUEST['po_date']) || !empty($_REQUEST['sort_po']) ||   !empty($_REQUEST['product_id']))
			{
				$whereArr = "";
				 $po_no = !empty($_REQUEST['po_no']) ? $_REQUEST['po_no'] : "";
				 $product_id = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : "";
				 $vendor_name = !empty($_REQUEST['vendor_name']) ? $_REQUEST['vendor_name']:"";
			     $po_date = !empty($_REQUEST['po_date']) ? $_REQUEST['po_date']:"";
				 if($_REQUEST['sort_po'] == 'asc') {
					$sort_po1 = $_REQUEST['sort_po'];
				 } 
				 else {
					$sort_po1 = "desc";
				 }
				 
				$implodestring1  = "";
				if(!empty($vendor_name)) {
				 $vendor_q = "SELECT ID  FROM ".$wpdb->user_table." where ".$wpdb->user_table.".".display_name." LIKE '%".$vendor_name."%'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
				$users = $wpdb->get_results($vendor_q,ARRAY_A);
				$user_count = count($users);
				$user_arr = "";
				if($user_count > 0)
				{
					/* echo '<pre>';
					print_r($users);
					echo '</pre>'; */
					foreach($users as $user_val) {
						$user_arr[] = $user_val['ID'];
					}
					
				}
				  $implodestring1 = "'".implode("','",$user_arr)."'";
				$whereArr[] = "wp_pv_commission.vendor_id IN (".$implodestring1.")";
				}
				$newDate = "";
				if(!empty($po_date)) {
					 $originalDate = str_replace('/','-',$po_date);
				$arr = explode('-', $originalDate);
				 $newDate = $arr[2].'-'.$arr[0].'-'.$arr[1];
				$whereArr[] = "DATE(wp_pv_commission.time) = '".$newDate."'"; 
				}
				if(!empty($po_no))
				{
					$whereArr[] = "wp_pv_commission.order_id='".$po_no."'";
				}
				if(!empty($product_id))
				{
					$whereArr[] = "wp_pv_commission.product_id='".$product_id."'";
				}

				if(!empty($whereArr))
				{
					$whereStr = implode(' or ',$whereArr);
				}
				else {
					$whereStr = "";
				}
				if(!empty($whereStr))
				{
					$whereStr = " where ".$whereStr;
				}

				 $filter_q = "SELECT * FROM ".$wpdb->custom_table_name.$whereStr." ORDER BY ".$wpdb->custom_table_name.".".order_id." $sort_po1";
				
			}
		} 
		else {
		
		 $filter_q = "SELECT * FROM ".$wpdb->custom_table_name." ORDER BY ".$wpdb->custom_table_name.".".order_id." DESC";
		
		}
		//echo $filter_q;
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$order_arr = "";
	$filter_order = $wpdb->get_results($filter_q, OBJECT);  

	foreach($filter_order as $filter_order_value)
	{
	
		// check for bid price in wp_auction_log table
		 /*  $bid_query = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$filter_order_value->product_id."' order by id desc limit 1";
		$bid_arr = $wpdb->get_results($bid_query, OBJECT);   */
		
		
		//get regular/sale price of product
			
		/*  $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$filter_order_value->product_id."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);   */
		
		
		
		//get regular price of product
			
	/* 	$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$filter_order_value->product_id."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($bid_arr))
		{
			$order_arr[] = $filter_order_value->id;
		} 
		else if(!empty($post_arr))
		{
			$order_arr[] = $filter_order_value->id;
		}
		else if(!empty($postregular_arr))
		{
			$order_arr[] = $filter_order_value->id;
		}
		 */
		$order_arr[] = $filter_order_value->id;
		
				
		// woo commerce order 
		//$woo_query = "select * from (SELECT * FROM ".$wpdb->prefix.'woocommerce_order_items'." where order_id='".$filter_order_value->order_id."') as T inner join ".$wpdb->prefix.'woocommerce_order_itemmeta'."  as OI on OI.order_item_id=T.order_item_id where T.order_id='".$filter_order_value->order_id."' and OI.meta_key='_product_id' and OI.meta_value='".$filter_order_value->product_id."'"
		
		
	}
	
/* 	  echo '<pre>';
	print_r($order_arr);
	echo '</pre>'; */  
	//die;
	if($_REQUEST['sort_po'] == 'sort_due') {
	 foreach($order_arr as $order_val)
	{
		$id = $order_val;
		global $wpdb;
		$wpdb->custom_table_name12 = "{$wpdb->prefix}pv_commission";
		$querystr_count1 = "SELECT * FROM ".$wpdb->custom_table_name12." where id ='$id'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$orders_arr1 = $wpdb->get_results($querystr_count1, OBJECT);  


		 $order_id = $orders_arr1[0]->order_id;
		// echo '<br/>';
		 $product_id = $orders_arr1[0]->product_id;
		  //echo '<br/>';
		 $com_id = $orders_arr1[0]->id;
		 // echo '<br/>';
		 $po  = $this->calculate_po($order_id,$product_id);
		 $trans = $this->calculate_total_transaction($order_id,$product_id);
		 $bal = $this->calculate_balanace_due($po,$trans);
		//echo $bal;
		if($bal != '0.00')
		{
			//echo 'test';
			$order_arr5[] = $com_id;
		}
 
	} 
	} 
	
		/*   echo '<pre>';
	print_r($order_arr5);
	echo '</pre>';  
	die; */
	
	if($_REQUEST['sort_po'] == 'sort_due') {
	$order_arr =" ";
	$order_arr = $order_arr5;
	} 

	
	if(!empty($order_arr)) {
 $implodestring = "'" . implode("','", $order_arr) . "'";
$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		if($_REQUEST['sort_po'] == 'asc') {
					$sort_po1 = $_REQUEST['sort_po'];
				 } 
				 else {
					$sort_po1 = "desc";
				 }
		$querystr_count = "SELECT * FROM ".$wpdb->custom_table_name." where id IN($implodestring) ORDER BY ".$wpdb->custom_table_name.".".order_id." $sort_po1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$totalorders = $wpdb->get_results($querystr_count, OBJECT);  
			////////////////////////////////////////////////////////////////

		/////////////////////START OF ARRAY PAGINATION CODE/////////////////////
		 $pageno = $_REQUEST['pageno'];  
		 $read_url = '';
		// $read_url = site_url().'/article/'.$cat.'/'.$subcat;
		$ptemp  = admin_url().'admin.php?page=purchased-orders' ;
		$array=$totalorders; // REPLACE $KEY WITH YOUR ARRAY VARIABLE
		if(isset($_REQUEST['pageno'])) {
		$page = $_REQUEST['pageno'];
		} else {
		$page = "";
		}

		$currentpage = !empty($page) ? (integer)$page : 1;
		$numperpage = 10; //NUMBER OF RECORDS TO BE DISPLAYED PER PAGE

		$total = count($array);
		$numofpages = ceil($total / $numperpage); //TOTAL NUMBER OF PAGES

	 if (($currentpage > 0) && ($currentpage <= $numofpages))
	{
		//STARTING LOOP FOR ARRAY DATA
         $start = ($currentpage-1) * $numperpage;
		 $end  = $numperpage+$start-1 ;
		 
		 }
		 
		 
		 
	  $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where id IN($implodestring)  ORDER BY ".$wpdb->custom_table_name.".".order_id." $sort_po1 limit $start,$numperpage";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$orders = $wpdb->get_results($querystr, OBJECT);  

		}	

	
	?>
	
	<style>
	.searchpanel
	{
		padding:10px;
	}
	.act-more.act-expanded {
    font-size: 19px;
    font-weight: bold;
    padding-left: 10px;
    padding-right: 10px;
	outline:none;
	}
	.act-more.act-collapsed {
    padding-left: 10px;
    padding-right: 10px;
	font-size: 19px;
    font-weight: bold;
	outline:none;
	}
	.inline-items
	{
		background-color: #576099 !important;
		color: #fff !important;
	}
	
	.suggestions {
  background: #fff none repeat scroll 0 0;
  border: 1px solid #c3c3c3;
  height: 500px;
  margin-top: 5px;
  overflow: scroll;
  position: absolute;
  width: 70%;
  z-index: 999999;
}
.pro_list {
  border-bottom: 1px solid #c3c3c3;
  padding: 9px;
}
.pagination .disabled{
	display:none;
}
	</style>
	<script>
	 jQuery(document).ready(function(){	
	  $('.ref_id').on('click', function() { 
		if($(this).find("input[type=text]").length ==0 ){
			$("input[type=text]").remove();
		var $this = $(this);
		var trans_id = $(this).attr("id");
		var product_id = $(this).attr("data-id");
			var $input = $('<input>', {
			value: $this.text(),
			type: 'text',
			 
		}).appendTo( $this.html($input) ).focus();
		 	 
			 
			$("input[type=text]")
			  .focusout(function() {
				 if($("input[type=text]").val() !=""){
						$("input[type=text]").hide();
						 $(this).parent(".ref_id").append('<img class="loader" src="https://s3-us-west-2.amazonaws.com/mem-expert/wp-content/uploads/2016/09/18235628/dialogbox_loader.gif" alt="loading">');
						 
						 $.ajax({
						 type : "post",
						 url : "https://www.memorabilia.expert/wp-admin/admin-ajax.php ",
						 data : {action: "update_ref_id", 'trans_id' : trans_id , 'product_id' : product_id, 'ref_id' : $("input[type=text]").val()},
						 success: function(response) {
							 $(".loader").remove();
							 $(this).parent(".ref_id").html( $("input[type=text]").val());
							  alert(response);
							  location.reload(true);
							 
						 }
					  }); 
					 
				 }else{
					 alert("Please enter Value");
					   
				 } 
				
			  })
			/*   .blur(function() {
				  $("input[type=text]").hide();
				 $.ajax({
				 type : "post",
				 url : "http://www.memorabilia.expert/wp-admin/admin-ajax.php ",
				 data : {action: "update_ref_id", 'trans_id' : trans_id , 'product_id' : product_id, 'ref_id' : $("input[type=text]").val()},
				 success: function(response) {
					  alert(response);
					  location.reload(true);
					 
				 }
			  }); 
			  }); */
			
			$('.save').on('click',function(){
				 
				
			});
		}
		
	});
    $("#inputpodate").datepicker();
	  $('.collaptable').aCollapTable({ 
    startCollapsed: true,
    addColumn: false, 
    plusButton: '<span class="i">+</span>', 
    minusButton: '<span class="i">-</span>' 
  });
  
  	jQuery('.fancybox').on('click',function(e){
		e.preventDefault();
		var order_param =	$(this).attr('rel');
		var page_value = $('#page_value').val();
           // $('.fancybox').fancybox();
						$.fancybox.open({
					href: "<?php echo get_site_url();?>/wp-admin/admin-ajax.php",
					type: "ajax",
					ajax: {
					type: "POST",
					data: {action:'update_commision', order_param:order_param,page_value:page_value}, // this is my post data
					}
			});
			});
			jQuery('.commission_change').live('click',function(e){
			e.preventDefault();
			var rel = $(this).attr('rel');
				var datastring = $('#com_submit_'+rel).serializeArray();
				console.log(datastring);
				    $.ajax({
					"type": "POST",
					"url": "<?php echo get_site_url();?>/wp-admin/admin-ajax.php", //this is my URL
					"data": {action:'update_commision_value_dynamic',com_arr:datastring,rel:rel}, // this is my post data
					"success":function(data)  //currently i pass empty "", i don't require call back.
					{
					alert(data);
					location.reload();
						//$('#ajax-content').html(data);
					}
				});
				});
			jQuery('.save_payment').on('click',function(e){				  
				e.preventDefault();	
				var rel = $(this).attr('rel');
				var datastring = $('#po_payment_'+rel).serializeArray();
				var status = true ;
				//var z1 = /^[0-9]+$/;
				//var z1 = /^(?!0{5})\[0-9]+\.[0-9]+$/;
				var z1 =  /^([1-9](\.[0-9]+)?)|(0\.[0-9]*[1-9])*$/;
				var amount = $('#amount_'+rel).val() ;
				var pro_id = $('#pro_id_'+rel).val() ;
				if (!z1.test(amount)) { 
					alert('Please enter valid Amount');
					status = false;
				
				}
				if(pro_id == 'No Product found' || pro_id == 'Select Product') {
					alert('Please select product');
					status = false;
				}
				
				/* if($('input[name="amount"]').val() == "") {
					
				} */	
				if(status == true) {
				console.log(datastring);
				$.ajax({	
				"type": "POST",		
				"url": "<?php echo get_site_url();?>/wp-admin/admin-ajax.php", 	
				"data": {action:'save_payment_value_dynamic',pay_arr:datastring,rel:rel},	
				"success":function(data)  		
				{			
				alert(data);
			    var ref = 'ref_no_'+rel ;
				var amount = 'amount_'+rel ;
				var description = 'description_'+rel ;
				console.log(ref);
				console.log(amount);
				console.log(description);
				$("input[name='"+ref+"']").val('');
				$("input[name='"+amount+"']").val('');
				$("textarea[name='"+description+"']").val('');
				location.reload();		
				}			
				});
				}	
			});	
		//delete payments 
		jQuery('.delid').on('click',function(e){
			e.preventDefault();
			var datastring = $(e.currentTarget).attr("order-id");
			console.log(datastring);
			$.ajax({	
				"type": "POST",		
				"url": "<?php echo get_site_url();?>/wp-admin/admin-ajax.php", 	
				"data": {action:'delete_payment_value',del_arr:datastring,},	
				"success":function(data)  		
				{		
						
				alert(data);
				location.reload();
				}			
				}) 
		});
		$('.send-email').on("click",function(e){   
			var order_id = $(this).attr("id");
			var product_id = $(this).attr("data-id");
			var vendor_id = $(this).attr("data-title");
		 
			 $.ajax({
					 type : "post",
					 url : "<?php  echo  admin_url( 'admin-ajax.php' ) ; ?>",
					 data : {action: "send_email", 'order_id' : order_id , 'product_id' : product_id, 'vendor_id' : vendor_id},
					 success: function(response) {
						  
						   $('.success-msg').remove();
							$('.contentpanel').before("<p class='success-msg' style='font-weight:bold;'>Mail has been sent successfully..</p>");
						  $('html, body').animate({
							scrollTop: $("#wpbody-content").offset().top
						}, 1000);  
				  }
			  }); 
		});

		jQuery("#product_title").keyup(function(){
			var tb_val = $(this).val();
			 	$.ajax({
					 type : "post",
					 url : "<?php  echo admin_url('admin-ajax.php') ; ?>",
					 data : {action: "get_product_id", 'text' : tb_val },
					 success: function(response) {
						 $("ul.suggestions").css("display","block");
						 $("ul.suggestions").html(response);
						 $(document).on("click","ul.suggestions .pro_list",function(){
							$(".form-inline input[name=product_id]").attr("value",$(this).attr('id'));
							$(".form-inline #product_title").attr("value",$(this).html());
							$("ul.suggestions").css("display","none");
						 });
						 $(".form-inline #product_title")
					  .focusout(function() {
						 
						 $("ul.suggestions").css("display","none");
					  });
				  }
			  });
		 });
   
	});

	</script>

	
	<div class="contentpanel">
	<?php 
	if(!empty($_REQUEST['msg'])) {
	echo '<p style="font-weight:bold;">Mail has been sent successfully..</p>';
	} ?>
	<div class="searchpanel">
	
	<div class="row">
	
	<div class="col-xs-8">

    <form class="form-inline" action="<?php echo  admin_url().'admin.php' ; ?>" method="get">
	
	 <div class="form-group">

            <label class="sr-only" for="search">Search</label>
        </div>
<input type="hidden" name="page" value="purchased-orders"/>
        <div class="form-group">

            <label class="sr-only" for="inputpono">PO No.</label>

            <input type="text" class="form-control" id="inputpono" name="po_no" placeholder="Enter PO number">

        </div>
		
        <div class="form-group">

            <label class="sr-only" for="inputvendorname">Vendor Name</label>

            <input type="text" class="form-control" id="inputvendorname" name="vendor_name" placeholder="Enter Vendor Name">

        </div>
		
		 <div class="form-group">

            <label class="sr-only" for="inputpodate">PO Date</label>

            <input type="text" class="form-control" id="inputpodate" name="po_date" placeholder="Select PO date">

        </div>

        <div class="form-group">

            <label class="sr-only" for="inputvendorname">Title</label>
			<input type="hidden" name="product_id" />
            <input type="text" class="form-control" id="product_title" name="product_title" placeholder="Enter Product Title" autocomplete="off" >
				<ul class="suggestions" style="display:none"></ul>
        </div>		
		
		 <div class="form-group">

			<select class="form-control" name="sort_po">
			<option value="Select-Sort">Select Sort</option>
			<option value="asc">Sort by Lowest to Highest PO No</option>
			<option value="sort_due">Sort by Balance Due</option>
			</select> 

        </div>	

        <input type="submit" class="btn btn-primary" name="search" value="Search"/>

    </form>
	
	</div>
	
	</div>
	</div>
		
	  <input type="hidden" name="page_value" id="page_value" value="main_page" />
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Orders</h3>
  </div>
		<div class="table-responsive">
	  
		      <table class="table table-striped collaptable" id="table2">
              <thead>
                 <tr>
				<th>Product</th>
                   <th>PO No</th>
				   <th>Vendor</th>
                    <th>PO Date</th>
					<th>Trans ID</th>
                    <th>PO Total</th>
					<th>Trans Total</th>
					<th>Bal Due</th>
					<th>Status</th>
					<th>Action</th>
					<th>Invoice</th>
                 </tr>
              </thead>
			  <tbody>
			  <?php
			  if(!empty($orders)) {
			  	$i=1;
				$j=2;
	foreach($orders as $order_val) {
		/* echo "pre";
		print_r($order_val);
		echo "</pre>"; */
		
	// custom code here to fetch the totals
				global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";

		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_val->order_id." and product_id=".$order_val->product_id;
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);
	$bid_sum = 0;
	$com_sum = 0;
	$amt_sum = 0;
		foreach($products as $product_val) {
		//$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

		  $querystr1 = "SELECT * FROM ".$wpdb->prefix.'posts'." where post_type='product' and post_status='publish' and ID='".$product_val->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$product_arr = $wpdb->get_results($querystr1, OBJECT);
	if(!empty($product_arr)) {
	
	/* echo '<pre>';
	print_r($product_arr);
	echo '</pre>'; */
	
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$product_val->product_id."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	 if($product_val->commision_per != "0") {
	 $commision_per = $product_val->commision_per ;
	 }
	 else {
	 $comm_val = "";
		$comm_val  = $product_val->total_due ;
		$commision_per = ($comm_val/$price_arr[0]->bid)*100  ;
	 }
	   $querystr3 = "SELECT * FROM ".$wpdb->prefix.'usermeta'." where user_id='".$product_val->vendor_id."' and meta_key='_wcv_commission_percent' ";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$vendor_arr = $wpdb->get_results($querystr3, OBJECT);	
	 $product_commission = get_post_meta( $product_val->product_id, 'wcv_commission_percent', true );
	 $vendor_commission  = $vendor_arr[0]->meta_value;
	 if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}
		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}
	$total_amt = $price_arr[0]->bid - $product_val->total_due ;
	$bid_sum = $price_arr[0]->bid + $bid_sum ;
	$com_sum = $product_val->total_due + $com_sum ;
	$amt_sum = $total_amt + $amt_sum ;
		$table = $wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
		$tras_data  = "SELECT *,SUM(amount) as amount FROM ".$table." where ".$table.".".order_id."=".$order_val->order_id." and product_id=".$product_val->product_id;
		
		$tras_all = $wpdb->get_row($tras_data, OBJECT); 
	/* echo '<pre>';
	print_r($tras_all);
	echo "</pre>"; */
	
		}			}
	$title = "";
	$productq1 = "SELECT p.post_title from ".$wpdb->prefix."posts as p where  p.ID='".$order_val->product_id."' and p.post_type='product' and p.post_status='publish'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
			$productss = $wpdb->get_results($productq1, OBJECT);
			$title = $productss[0]->post_title;

	//custom code end here 	
	?>
	
		<?php 
			
				$check_ebay = get_post_meta($order_val->order_id,'_ebay_order_id',true);
			
			?>
			<tr <?php if(!empty($check_ebay)) { ?> style="font-weight:bold;" <?php } ?> data-id="<?php echo $i.'_parent'; ?>" data-parent="" >
			
			<td style="width: 30%;"><?php if(!empty($check_ebay)) { ?><img src="<?php echo plugins_url('po/fancybox/ebay-42x16.png');  ?>" ><?php } ?> <a href="<?php  echo admin_url() ; ?>admin.php?page=purchased-orders&view=order&order_id=<?php echo $order_val->order_id ; ?>&product_id=<?php echo $order_val->product_id ; ?>"><?php echo $title; ?></a></td>
			<td><a href="<?php  echo admin_url() ; ?>admin.php?page=purchased-orders&view=order&order_id=<?php echo $order_val->order_id ; ?>&product_id=<?php echo $order_val->product_id ; ?>"><?php echo $order_val->order_id ; ?></a></td>
			<td><?php echo $this->get_vendor_name($order_val->vendor_id); ?></td>
			<td><?php echo $order_val->time ; ?></td>
			<?php  $ref_query = "SELECT * FROM ".$wpdb->prefix.'po_transaction'." where order_id='".$order_val->order_id."' and product_id='".$order_val->product_id."'order by date_paid desc";
			
			$ref = $wpdb->get_results($ref_query);
			
			
			$ref_id = $ref[0]->ref_no;
			$trans_id = $ref[0]->trans_id;
			 ?>
			
			<td class="ref_id" id="<?php echo $order_val->order_id; ?>" data-id="<?php echo $order_val->product_id; ?>"><?php if(!empty($ref)){
				foreach($ref as $refData){
				 
					if($refData->ref_no !=""){
						echo $refNum = 	$refData->ref_no;
						break;
					}
					
				}
				
			} ?></td>
		<?php	 $po = $this->calculate_po($order_val->order_id,$order_val->product_id);  ?>
			<?php echo '<td id="row2">$'.number_format($po,2,'.',',').'</td>';?>
			<td><?php if($tras_all->amount!='') { echo '$'.$total_trans = number_format($tras_all->amount,2,'.',','); } else { echo '$'.$total_trans = '0.00'; } ?></td>
			 <?php $balance = $this->calculate_balanace_due($po,$total_trans); ?>
			<td><?php echo '$'.$balance ;?></td>
			<td><?php echo $order_val->status ;?></td>
			
			<td><a href="<?php  echo admin_url() ; ?>admin.php?page=purchased-orders&view=order&order_id=<?php echo $order_val->order_id ; ?>&product_id=<?php echo $order_val->product_id ; ?>">View Detail</a><br/><a href="javascript:void(0)" id="<?php echo $order_val->order_id ; ?>" data-id="<?php echo $order_val->product_id ; ?>" data-title="<?php echo $order_val->vendor_id ; ?>" class="send-email">Send Email</a></td>
			<td><a target="__blank" href="<?php  echo admin_url() ; ?>post.php?post=<?php echo $order_val->order_id ; ?>&action=edit"><?php echo $order_val->order_id ; ?></a></td>
			</tr> 
			
			 
			<?php $j++; ?>
			<tr data-id="<?php echo $j;  ?>" data-parent="<?php echo $i.'_parent'; ?>" >
			<td colspan="9" ><div class="contentpanel">
      
      <div class="panel panel-default">
        <div class="panel-heading inline-items">	
  <h3 style="display:inline" class="panel-title">Auction Items</h3>
  </div>
 
		<div class="table-responsive">
		      <table class="table table-striped">
              <thead>
                 <tr>
                   <!--<th>Date Paid</th>-->
                    <th>Name</th>
                    <th>Sku</th>
					<th>Vendor Name</th>
					<th>Vendor Cost</th>
                    <th>Price</th>
					<th>Pct</th>
					<th>Fee</th>
					<th>Amount</th>
                 </tr>
              </thead>
			  <tbody>
		    <?php

		
				global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";
	
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_val->order_id." and product_id='".$order_val->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);
	$bid_sum = 0;
	$com_sum = 0;
	$amt_sum = 0;
		foreach($products as $product_val) {
		//$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

		  $querystr1 = "SELECT * FROM ".$wpdb->prefix.'posts'." where post_type='product' and post_status='publish' and ID='".$product_val->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$product_arr = $wpdb->get_results($querystr1, OBJECT);
	/* if(!empty($product_arr)) { */
	
	/* echo '<pre>';
	print_r($product_arr);
	echo '</pre>'; */
	
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$product_val->product_id."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	 if($product_val->commision_per != "0") {
	 $commision_per = $product_val->commision_per ;
	 }
	 else {
	 $comm_val = "";
		$comm_val  = $product_val->total_due ;
		$commision_per = ($comm_val/$price_arr[0]->bid)*100  ;
	 }
	   $querystr3 = "SELECT * FROM ".$wpdb->prefix.'usermeta'." where user_id='".$product_val->vendor_id."' and meta_key='_wcv_commission_percent' ";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$vendor_arr = $wpdb->get_results($querystr3, OBJECT);	
	 $product_commission = get_post_meta( $product_val->product_id, 'wcv_commission_percent', true );
	 $vendor_commission  = $vendor_arr[0]->meta_value;
	 if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}
		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}

	//get regular/sale price of product
			
		 $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);  
		
		
		
		//get regular price of product
			
		$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($price_arr))
		{
			//$order_arr[] = $filter_order_value->order_id;
			$price = $price_arr[0]->bid;
		} 
		else if(!empty($post_arr))
		{
			$price = $post_arr[0]->meta_value;
		}
		else if(!empty($postregular_arr))
		{
			$price = $postregular_arr[0]->meta_value;
		}
		
		 $order = new WC_Order( $order_val->order_id );
		//$dues  = WCV_Vendors::get_vendor_dues_from_order( $order, false );
		
		/* echo '<pre>';
		print_r($dues); */
		/* echo '<pre>';
		print_r($order->get_items()); */
		foreach ( $order->get_items() as $key => $product ) {
		if($product[ 'product_id' ] == $product_val->product_id) {
		$price =  $product[ 'line_subtotal' ];
		}
		//$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
		//echo $commission_rate = WCV_Commission::get_commission_rate( $product_id );
		//$commission1      = $product_price * ( $commission_rate / 100 );
		// $commission1      = round( $commission, 2 );
			//echo $com1 = WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order );
		}
		$commission = WCV_Commission::get_commission_rate( $product_val->product_id );
		//$commission      = $product_price * ( $commission_rate / 100 );
		//$commission      = round( $commission, 2 );
			//$total_amt = $price_arr[0]->bid - $product_val->total_due ;
			$amt = $price * ($commission/100);
			$fee = $price - ($price * ($commission/100)) ;
	$bid_sum = $price + $bid_sum ;
	$com_sum = $product_val->total_due + $com_sum ;
	$amt_sum = $total_amt + $amt_sum ;
	$sku = "";
	$sku =  get_post_meta($product_val->product_id, '_sku', true );
	/* echo '<pre>';
	print_r($price_arr);
	 */
	$vendor_cost =  get_post_meta($product_val->product_id, 'vendor_cost', true ) ;
	//echo $vendor_cost;
	 
	echo '<tr>';
	//echo '<td></td>';
	echo '<td style="width:50%">' ;
	if(!empty($product_arr[0]->post_title)) {
	 
		echo "<a href='".admin_url('post.php?post='.$product_arr[0]->ID.'&action=edit')."' target='_blank'>".$product_arr[0]->post_title."</a>" ;

	} else {
	echo 'Product not exist.';
	}
	/* $jsa_service =  get_post_meta($product_val->product_id, 'jsa_auth_service', true ) ;
	if(!empty($jsa_service))
	{
		$jsa =  'Jsa Auction letter';
		echo '<br/><b>'.$jsa.'</b>';
	} 
	$jsa_100 =  get_post_meta($product_val->product_id, '100_auth_service', true ) ;
	if(!empty($jsa_100))
	{
		$jsa_hundrd = '100% Authentication team';
		echo '<br/><b>'.$jsa_hundrd.'</b>';
	}
	$auth_arr =  get_post_meta($product_val->product_id, 'authentication', false ) ;
	if(!empty($auth_arr))
	{
		echo '<br/><b>Authentication: '.implode(',',$auth_arr).'</b>';
	} */
	
	$attributes = get_the_terms( $product_val->product_id , 'pa_authentication');
		if(!empty($attributes)){ 
			echo '<br/><b>Authentication:';
			
			foreach( $attributes as $attrVal){
				
				echo  $attrVal->name.'</b>';
			}
		}else{
			
			
			  $jsa_auth_service  = "";
					$auth_service  = "";
					$authentication  = "";

				    $jsa_auth_service =  get_post_meta ( $product_val->product_id, 'jsa_auth_service' , true );
					$auth_service =  get_post_meta ( $product_val->product_id, '100_auth_service' , true );
					$authentication =  get_post_meta ( $product_id, 'authentication_status' , true );
					if(!empty(get_post_meta( $product_val->product_id, 'authentication'))){
				 
					$authentication_arr =	get_post_meta( $product_val->product_id , 'authentication');
				 	
					if(count($authentication_arr) > 1) {  
					$auth_arr = array_filter($authentication_arr);
				 
			 
						 $auth_string = implode(',',$auth_arr);
					} else { 
						 $auth_string = $authentication_arr[0];
					}
					}
					if(!empty($jsa_auth_service))
					{ 
						$j_auth_service =  ($jsa_auth_service == '1')  ? 'Jsa Auction letter' : '';
					} else {
						 
						$j_auth_service = '' ;
					}
					
					if(!empty($auth_service))
					{
						 
						$a_service =  ($auth_service == '1')  ? '100% Authentication team' : '';
					} else {
						 
						$a_service = '' ;
					}
					
					if(!empty($auth_string))
					{
						 
						$auth =  $auth_string;
					} else {
						 
						$auth = '' ;
					}
						if(!empty($j_auth_service)) {
							 
					$product_name .=	
						'<br/><br/>  <b>'.$j_auth_service.'</b> ';
						
						}
						if(!empty($a_service)) {
							 
						$product_name .=	'<br/><br/> <b>'.$a_service.'</b>';
						}
						if(!empty($auth)){
							echo $product_name .=	'<br/><br/> <b>Authentication: '.$auth.'</b><br/>';
						}
		 }
	
	echo '</td>';
	echo '<td>'.$sku.'</td>' ;
	echo '<td>'.$this->get_vendor_name($product_val->vendor_id).'</td>' ;
	echo '<td>'.$vendor_cost.'</td>' ;
	echo '<td> $'.number_format($price,2,'.',',').'</td>';
	echo '<td><a href="#" class="fancybox" rel="'.$product_val->product_id.'-'.$order_val->order_id.'">'.$commission.'%</a></td>';
	echo '<td>$'.number_format($fee,2,'.',',').'</td>';
	echo '<td>$'.number_format($amt,2,'.',',').'</td>';
	echo '</tr>';		/* } */			}
		?>
	<?php 	echo '<tr>';
	//echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td id="row2"><b>Total $'.number_format($bid_sum,2,'.',',').'</b> <input type="hidden" name="sum_of_bid"></td>';
	echo '<td></td>';
	echo '<td> <b> $'.number_format($fee,2,'.',',').' </b></td>';
	echo '<td> <b> $'.number_format($amt,2,'.',',').'</b></td>';
	echo '<tr>';	?>		</tbody>
           </table>
		   </div>
		 </div>
		 </div>
		 <div style="clear:both;"></div>
	 <div class="contentpanel">
         <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Payments and Adjustments</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped">
              <thead>
                 <tr>
                 <th>Action</th>
				   <th>Date Paid</th>
                    <th>Ref No.</th>
					<th>Description</th>
					<th>Amount</th>					</tr>
              </thead>
			  <tbody>	
			  <?php 
			  global $wpdb ;	
			  $table1 = $wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
			 $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_val->order_id." and product_id = ".$order_val->product_id." order by trans_id desc";
			 $sumqur ="SELECT SUM(amount) as amount FROM ".$table1."  WHERE ".$table1.".".order_id."=".$order_val->order_id." and product_id=".$order_val->product_id;
			 $product_arr = $wpdb->get_results($querystr); 
		     $product_sum = $wpdb->get_row($sumqur);
			 if(!empty($product_sum->amount))
			 {
				$total_trans = round($product_sum->amount,2) ;
			 } else {
			 $total_trans = "0.00" ;
			 }
		
			 echo '<input type="hidden" id="t_p" value='.$product_sum->amount.'>';
			  if(!empty($product_arr)) {
				   //echo '<pre>';	print_r($product_val); echo '</pre>';
				  	foreach($product_arr as $product_val) {
						 
						echo '<tr>';
						echo '<td class="delid" order-id='.$product_val->trans_id.'>X</td>';
						echo '<td>'.$product_val->date_paid.'</td>';
						echo '<td>'.$product_val->ref_no.'</td>';
						echo '<td>'.$product_val->description.'</td>';
						echo '<td>'.$product_val->amount.'</td>';
						echo '</tr>';
					}
					
				  } ?>
			  
			  <form action="#" method="post" id="po_payment_<?php echo $order_val->product_id; ?>">
			  <tr id="a">
			  <td></td>
			  <td><b>Add Trans</b></td>
			  <td><input type="text" name="ref_no_<?php echo $order_val->product_id; ?>" value=""/></td>
			  <td><textarea name="description_<?php echo $order_val->product_id; ?>" cols="31" rows="3"></textarea></td>
			  <td><input type="text" name="amount_<?php echo $order_val->product_id; ?>" value="" id="amount"/></td>			<td><input type="hidden" name="order_id_<?php echo $order_val->product_id; ?>" value="<?php echo $order_val->order_id; ?>"></td>
			  </tr>
			  <tr id="b">
			  <td></td>
			  <td></td>
			  <td></td>
			  <td><b>PO Total</b></td>
			  <?php 
				$po = $this->calculate_po($order_val->order_id,$order_val->product_id);
				
			  ?>
			  <input type="hidden" name="pro_id_<?php echo $order_val->product_id; ?>" id="pro_id_<?php echo $order_val->product_id; ?>" value="<?php echo $order_val->product_id;  ?>" />
			  <td><input id="po_total_<?php echo $order_val->product_id; ?>" type="text" name="po_total_<?php echo $order_val->product_id; ?>" style="font-weight:bold;" value="<?php echo '$'.number_format($po,2,'.',',');  ?>" disabled /></td>
			  </tr>
			  <tr id="c">
			  <td></td>
			  <td></td>
			  <td></td>
			  <td><b>Total Payments</b></td>
			  <td><input type="text" name="total_payments_<?php echo $order_val->product_id; ?>" style="font-weight:bold;" value="<?php echo '$'.$total_trans ;  ?>" disabled id="total_p"/></td>
			  </tr>
			  <tr id="d">
			  <td></td>
			  <td></td>
			  <td></td>
			  <td><b>Balance Due</b></td>
			  <?php
			  $balance = $this->calculate_balanace_due($po,$total_trans);
			  ?>
			  <td><input type="text" name="balance_<?php echo $order_val->product_id; ?>" style="font-weight:bold;" disabled id="bal_<?php echo $order_val->product_id; ?>" value="<?php echo '$'.$balance ; ?>"/></td>
			  </tr>
			  <tr id="e">
			  <td></td>
			  <td></td>
			  <td colspan="3">
			  <div class="row">
							<div class="col-sm-6 col-sm-offset-8">
                          <button type="submit" class="btn btn-primary save_payment" rel="<?php echo $order_val->product_id; ?>">Save PO and Payments</button>
						  </div>
						</div>  
			  </td>
			  </tr>
			  </form>
			          </tbody>
           </table>
		   </div>
		 </div>
		 </div>
		 
		 </td>
			</tr>
			  
	<?php 	$i++;	} } else { echo "<tr colspan='9'>No Order Found</tr>" ; } ?>
			  
		</tbody>
             
           </table>
		   </div>
		 </div>
		 </div>
		 <style>
		 .pagination .active {
			background-color: #337ab7;
    border-color: #337ab7;
    color: #fff;
    cursor: default;
    z-index: 2;
		 }
		 .pagination .active:hover {
			background-color: #337ab7;
    border-color: #337ab7;
    color: #fff;
    cursor: default;
    z-index: 2;
		 }
		 </style>
		   <ul class="pagination">
    
  <?php
		 $pages = '';
		 $po_no = "";
$vendor_name = "";
$po_date = "";
$whereStr = "";
$whereStr1 = "";
//echo $_REQUEST['po_no'];
	if($_REQUEST['po_no'] != "") $whereArr1[] = 'po_no='.$_REQUEST['po_no'];
	if($_REQUEST['vendor_name'] != "") $whereArr1[] = 'vendor_name='.$_REQUEST['vendor_name'];
	if($_REQUEST['po_date'] != "") $whereArr1[] = 'po_date='.$_REQUEST['po_date'];
	if(!empty($whereArr1)) {
 $whereStr1 = implode("&", $whereArr1);

if(!empty($whereStr1))
{
	$whereStr1 ='&'.$whereStr1.'&search=search';
} else {
	$whereStr1 = "";
}
}
//echo $whereStr;
	if ($currentpage != 1) 
{ //GOING BACK FROM PAGE 1 SHOULD NOT BET ALLOWED
 $previous_page = $currentpage - 1;
 //$previous = '<a href="'.$ptemp.'?pageno='.$previous_page.'"> </a> '; 
$previous = '&lt;Previous' ;

 $pages .= '<li><a href="'.$ptemp.'&pageno='.$previous_page.$whereStr1.'">'. $previous .'</a></li>'; 
}    


 
for ($a=1; $a<=$numofpages; $a++)
{ 
 
	if ($a == $currentpage){
		$pages .= '<li><a href="#" class="active">'. $a .'</a></li>';
	} 

	else{
		$pages .= '<li><a href="'.$ptemp.'&pageno='.$a.$whereStr1.'">'. $a .'</a></li>';
	} 
	
	 
}


       


//$pages = substr($pages,0,-1); //REMOVING THE LAST COMMA (,)

if($currentpage != $numofpages) 
{

 //GOING AHEAD OF LAST PAGE SHOULD NOT BE ALLOWED
 $next_page = $currentpage + 1;
 $next = 'Next&gt;';
 $pages .= '<li><a href="'.$ptemp.'&pageno='.$next_page.$whereStr1.'">'. $next .'</a></li>';

}

  $page = (int) (!isset($_REQUEST['pageno']) ? 1 :$_REQUEST['pageno']);
$page = ($page == 0 ? 1 : $page);
$recordsPerPage = 10;
$start = ($page-1) * $recordsPerPage;
$adjacents = "2";
 
$prev = $page - 1;
$next = $page + 1;
$lastpage = ceil($total/$recordsPerPage);
$lpm1 = $lastpage - 1;   
$pagination = "";
if($lastpage > 1)
    {   
        
        if ($page > 1){
		$previous_page = $currentpage - 1;
			 //$previous = '<a href="'.$ptemp.'?pageno='.$previous_page.'"> </a> '; 
			$previous = '&lt;Previous' ;

            $pagination.= '<li><a href="'.$ptemp.'&pageno='.$previous_page.$whereStr1.'">'. $previous .'</a></li>'; 	
		}
			
        else
            $pagination.= "<span class='disabled'>&laquo; Previous&nbsp;&nbsp;</span>";   
        if ($lastpage < 7 + ($adjacents * 2))
        {   
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination.= '<li><a href="#" class="active">'. $counter .'</a></li>'; 
                else
                    $pagination.= '<li><a href="'.$ptemp.'&pageno='.$counter.$whereStr1.'">'. $counter .'</a></li>';
				     
 
            }
        }   
 
        elseif($lastpage > 5 + ($adjacents * 2))
        {
            if($page < 1 + ($adjacents * 2))
            {
                for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if($counter == $page)
                        $pagination.= '<li><a href="javascript:void(0)" class="active">'. $counter .'</a></li>'; 
                    else
                        $pagination.= '<li><a href="'.$ptemp.'&pageno='.$counter.$whereStr1.'">'. $counter .'</a></li>';     
                }
                $pagination.= "<li><a href='javascript:void(0)'>...</a></li>";
                $pagination.= '<li><a href="'.$ptemp.'&pageno='.$lpm1.$whereStr1.'">'. $lpm1 .'</a></li>';  
				
				 
                $pagination.= '<li><a href="'.$ptemp.'&pageno='.$lastpage.$whereStr1.'">'. $lastpage .'</a></li>';   
 
           }
           elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
           {
               $pagination.= '<li><a href="'.$ptemp.'&pageno=1'.$whereStr1.'">1</a></li>';
               $pagination.= '<li><a href="'.$ptemp.'&pageno=2'.$whereStr1.'">2</a></li>';  
               $pagination.= "<li><a href='javascript:void(0)'>...</a></li>";
               for($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
               {
                   if($counter == $page)
                       $pagination.= '<li><a href="javascript:void(0)" class="active">'. $counter .'</a></li>'; 
                   else
                       $pagination.= '<li><a href="'.$ptemp.'&pageno='.$counter.$whereStr1.'">'. $counter .'</a></li>';        
               }
               $pagination.= "<li><a href='javascript:void(0)'>..</a></li>";
               $pagination.= '<li><a href="'.$ptemp.'&pageno='.$lpm1.$whereStr1.'">'. $lpm1 .'</a></li>';  
				
				 
                $pagination.= '<li><a href="'.$ptemp.'&pageno='.$lastpage.$whereStr1.'">'. $lastpage .'</a></li>';   
           }
           else
           {
               $pagination.= '<li><a href="'.$ptemp.'&pageno=1'.$whereStr1.'">1</a></li>';
               $pagination.= '<li><a href="'.$ptemp.'&pageno=2'.$whereStr1.'">2</a></li>';  
               $pagination.= "<li>..</li>";
               for($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
               {
                   if($counter == $page)
                        $pagination.= '<li><a href="javascript:void(0)" class="active">'. $counter .'</a></li>'; 
                   else
                        $pagination.= '<li><a href="'.$ptemp.'&pageno='.$counter.$whereStr1.'">'. $counter .'</a></li>';       
               }
           }
        }
        if($page < $counter - 1){
			$next_page = $currentpage + 1;
			 $next1 = 'Next&gt;';
			 $pagination .= '<li><a href="'.$ptemp.'&pageno='.$next.$whereStr1.'">'. $next1 .'</a></li>';

		}
               
        else{
			$pagination.= "<span class='disabled'>Next &raquo;</span>";
 
		}
            
        $pagination.= "</div>";       
    }
	
 
echo   $pagination ; //PAGINATION LINKS
/////////////////////END OF ARRAY PAGINATION CODE/////////////////////
		 
		?> 
	</ul>	 
	
<?php
		
	}
	
	if(!empty($_REQUEST['view']) && $_REQUEST['view'] == 'order')
	{
	
		
	
			wp_enqueue_style( 'po-datatable-style1', plugins_url('css/bootstrap.min.css', __FILE__  ));
			
		wp_enqueue_script( 'my-plugin-script1' , plugins_url( 'js/jquery-1.11.1.min.js', __FILE__ ) );
	wp_enqueue_script( 'my-plugin-script2' , plugins_url( 'js/jquery-migrate-1.2.1.min.js', __FILE__ ) );
	//wp_enqueue_script( 'po-datatable-style2' , plugins_url( 'css/jquery.fancybox.css', __FILE__ ) );

			wp_enqueue_script( 'my-plugin-script3' , plugins_url( 'fancybox/jquery.fancybox.js', __FILE__ ) );
		wp_enqueue_script( 'my-plugin-script4' , plugins_url( 'js/custom.js', __FILE__ ) );
		
			global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

	
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id=".$_REQUEST['product_id'];
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$orders = $wpdb->get_results($querystr, OBJECT);
		?>
		<style>
		.panel-heading {
    background-color: #576099 !important;
    color: #fff !important;
}
</style>
			<link rel="stylesheet" type="text/css" href="<?php echo  plugins_url( 'fancybox/jquery.fancybox.css?v=2.1.5', __FILE__ ); ?>" media="screen" />
			<div class="row">
							<div class="col-sm-6">
                          <button id="back" class="btn btn-primary" type="button" style="margin-top:10px;margin-bottom:10px;" onclick="history.go(-1);">Back</button>
						  </div>
						</div>
		<div id="printableArea">
		<div class="contentpanel">
		<div class="row" style="padding:10px;"><div style="float:left;"><img width='300px' src="<?php echo get_option('woo_logo'); ?>"/></div><div style="float:right;padding-20px;"><p style="font-weight:bold;">BidAMI-Memorabilia.Expert <br/> 6490 W. Desert Inn <br/> Las Vegas NV 89146</p></div></div>
		</div>
		<div style="clear:both;padding-bottom:10px;"></div>
		<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
		<script>
		jQuery(document).ready(function(){  
			jQuery('.send-email').on("click",function(e){ 
			var order_id = jQuery(this).attr("id");
			var product_id =jQuery(this).attr("data-id");
			var vendor_id = jQuery(this).attr("data-title");
		 
			 jQuery.ajax({
					 type : "post",
					 url : "<?php  echo admin_url() ; ?>admin.php?page=purchased-orders",
					 data : {action: "send_email", 'order_id' : order_id , 'product_id' : product_id, 'vendor_id' : vendor_id},
					 success: function(response) {
						 jQuery('.success-msg').remove();
							jQuery('.contentpanel').before("<p class='success-msg' style='font-weight:bold;'>Mail has been sent successfully..</p>");
						  jQuery('html, body').animate({
							scrollTop: $("#wpbody-content").offset().top
						}, 1000);
				  }
			  }); 
		});
		});
		
		</script>
		<style>
		.po_send_mail {
		  display: block;
		  float: right;
		  position: relative;
		  top: -34px;
		  width: 8%;
		}
		.po_send_mail .send-email {
		  background: #3071a9 none repeat scroll 0 0;
		  color: #fff;
		  padding: 10px;
		}
		</style>
		<?php 
		foreach($orders as $order_val) { 
		?>
       <div class="po_send_mail"><a href="javascript:void(0)" id="<?php echo $order_val->order_id ; ?>" data-id="<?php echo $order_val->product_id ; ?>" data-title="<?php echo $order_val->vendor_id ; ?>" class="send-email">Send Email</a></div>
		<?php } ?>
		<div class="contentpanel">
		
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Consignor PO</h3>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
    function PrintElem(elem)
    {	 $(this).find('#po_payment').hide();
        Popup($(elem).html());
		
    }

    function Popup(data) 
    {
		
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Memorabilia Experts</title>');
        mywindow.document.write('<style type="text/css">#a, #e{display:none!important;}.panel-default {    border-color: #ddd;}.panel {    background-color: #fff;    border: 1px solid transparent;    border-radius: 4px;    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);    margin-bottom: 20px;}.panel-default > .panel-heading {    background-color: #f5f5f5;    border-color: #ddd;    color: #333;}.panel-heading {    border-bottom: 1px solid transparent;    border-top-left-radius: 3px;    border-top-right-radius: 3px;    padding: 10px 15px;}.panel-heading {    background-color: #576099 !important;    color: #fff !important;}.panel-title {    color: inherit;    font-size: 16px;    margin-bottom: 0;    margin-top: 0;}.panel > .table-responsive {    border: 0 none;    margin-bottom: 0;}.panel > .table:last-child, .panel > .table-responsive:last-child > .table:last-child {    border-bottom-left-radius: 3px;    border-bottom-right-radius: 3px;}.panel > .table, .panel > .table-responsive > .table, .panel > .panel-collapse > .table {    margin-bottom: 0;}.table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {    border-top: 0 none;}.table > thead > tr > th {    border-bottom: 2px solid #ddd;    vertical-align: bottom;}.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {    border-top: 1px solid #ddd;    line-height: 1.42857;    padding: 8px;    vertical-align: top;}.panel > .table:last-child > tbody:last-child > tr:last-child td:first-child, .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child td:first-child, .panel > .table:last-child > tfoot:last-child > tr:last-child td:first-child, .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child td:first-child, .panel > .table:last-child > tbody:last-child > tr:last-child th:first-child, .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child th:first-child, .panel > .table:last-child > tfoot:last-child > tr:last-child th:first-child, .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child th:first-child {    border-bottom-left-radius: 3px;}.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {    background-color: #f9f9f9;}.panel-default {    border-color: #ddd;}</style>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;
    }

</script>
		<div class="table-responsive">
		      <table class="table table-striped">
              <thead>
                 <tr>
                   <th>PO No</th>
				   <th>Vendor</th>
                    <th>PO Date</th>
                    <th>PO Total</th>
					<th>Trans Total</th>
					<th>Bal Due</th>	
					<th>Print</th>
                 </tr>
              </thead>
			  <tbody>
			  <?php
	
			foreach($orders as $order_val) { 
			echo '<tr>';
			echo '<td>'.$order_val->order_id.'</td>' ;
			echo '<td>'.$this->get_vendor_name($order_val->vendor_id).'</td>' ;
			echo '<td>'.$order_val->time.'</td>';
			$po = $this->calculate_po($order_val->order_id,$order_val->product_id);  
			echo '<td class="t_po_total">'.'$'.number_format($po,2,'.',',').'</td>';
			$total_trans = $this->calculate_total_transaction($order_val->order_id,$order_val->product_id);
			echo '<td id="trs_total_p">'.'$'.number_format($total_trans,2,'.',',').'</td>';
			  $balance = $this->calculate_balanace_due($po,$total_trans);
			echo '<td id="b_d">'.'$'.$balance.'</td>';?>
			<td><input type="button" value="Print" onclick="PrintElem('#printableArea')" /></td>
			<?php echo '</tr>';
			}
	
			?>
             </tbody>
           </table>
		   </div>
		 </div>
		 </div>
		 <input type="hidden" name="page_value" id="page_value" value="detail_page" />
		 <div style="clear:both;"></div>
		 
		  <?php
		 
		 global $wpdb ;
		 $wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		  $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id='".$_REQUEST['product_id']."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);

	 $vendor_id = $products[0]->vendor_id;
		 $user_meta_arr = get_user_meta($vendor_id);
		 $user_arr =  $this->user_info($vendor_id);
		 ?>
		  <div class="contentpanel">
         <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Details</h3>
  </div>
		<div class="table-responsive">
		 <table class="table table-striped">
			<thead>
			<tr>
			<th colspan="3">Account</th>
			<th>Email</th>
			<th>Phone no</th>	
			</tr>
			</thead>
			<tbody>
			<tr>
			<td colspan="3" ><?php echo $user_meta_arr['billing_address_1'][0].' '.$user_meta_arr['billing_city'][0].' '.$user_meta_arr['billing_postcode'][0].' '.$user_meta_arr['billing_state'][0].' '.$user_meta_arr['billing_country'][0] ; ?></td><td><?php echo $user_arr[0]->user_email ; ?></td><td><?php echo $user_meta_arr['billing_phone'][0] ; ?></td>
			</tr>
			</tbody>
		</table>
		  </div>
		 </div>
		 </div>	
		 
		 <div style="clear:both;"></div>	
		 
		 <div class="contentpanel">
      
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Auction Items</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped">
              <thead>
                 <tr>
                   <!--<th>Date Paid</th>-->
                    <th>Name</th>
                    <th>Sku</th>
					<th>Vendor Name</th>
					<th>Vendor Cost</th>
                    <th>Price</th>
					<th>Pct</th>
					<th>Fee</th>
					<th>Amount</th>
                 </tr>
              </thead>
			  <tbody>
		 
		 
		 <?php

		
				global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";
	
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id='".$_REQUEST['product_id']."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);
	$bid_sum = 0;
	$com_sum = 0;
	$amt_sum = 0;
		foreach($products as $product_val) {
		//$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

		  $querystr1 = "SELECT * FROM ".$wpdb->prefix.'posts'." where post_type='product' and post_status='publish' and ID='".$product_val->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$product_arr = $wpdb->get_results($querystr1, OBJECT);
	/* if(!empty($product_arr)) { */
	
	/* echo '<pre>';
	print_r($product_arr);
	echo '</pre>'; */
	
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$product_val->product_id."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	 if($product_val->commision_per != "0") {
	 $commision_per = $product_val->commision_per ;
	 }
	 else {
	 $comm_val = "";
		$comm_val  = $product_val->total_due ;
		$commision_per = ($comm_val/$price_arr[0]->bid)*100  ;
	 }
	   $querystr3 = "SELECT * FROM ".$wpdb->prefix.'usermeta'." where user_id='".$product_val->vendor_id."' and meta_key='_wcv_commission_percent' ";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$vendor_arr = $wpdb->get_results($querystr3, OBJECT);	
	 $product_commission = get_post_meta( $product_val->product_id, 'wcv_commission_percent', true );
	 $vendor_commission  = $vendor_arr[0]->meta_value;
	 if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}
		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}

	//get regular/sale price of product
			
		 $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);  
		
		
		
		//get regular price of product
			
		$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($price_arr))
		{
			//$order_arr[] = $filter_order_value->order_id;
			$price = $price_arr[0]->bid;
		} 
		else if(!empty($post_arr))
		{
			$price = $post_arr[0]->meta_value;
		}
		else if(!empty($postregular_arr))
		{
			$price = $postregular_arr[0]->meta_value;
		}
		
		 $order = new WC_Order( $_REQUEST['order_id'] );
		//$dues  = WCV_Vendors::get_vendor_dues_from_order( $order, false );
		
		/* echo '<pre>';
		print_r($dues); */
		/* echo '<pre>';
		print_r($order->get_items()); */
		$items = $order->get_items();
		 
		   $item['product_id'];
		foreach ( $order->get_items() as $key => $product ) {
			 $product_id .= $product[ 'product_id' ];
			 
		if($product[ 'product_id' ] == $product_val->product_id) {
		$price =  $product[ 'line_subtotal' ];
		}
		//$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
		//echo $commission_rate = WCV_Commission::get_commission_rate( $product_id );
		//$commission1      = $product_price * ( $commission_rate / 100 );
		// $commission1      = round( $commission, 2 );
			//echo $com1 = WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order );
		}
		$commission = WCV_Commission::get_commission_rate( $product_val->product_id );
		//$commission      = $product_price * ( $commission_rate / 100 );
		//$commission      = round( $commission, 2 );
			//$total_amt = $price_arr[0]->bid - $product_val->total_due ;
			$amt = $price * ($commission/100);
			$fee = $price - ($price * ($commission/100)) ;
	$bid_sum = $price + $bid_sum ;
	$com_sum = $product_val->total_due + $com_sum ;
	$amt_sum = $total_amt + $amt_sum ;
	$sku = "";
	$sku =  get_post_meta($product_val->product_id, '_sku', true );
	/* echo '<pre>';
	print_r($price_arr);
	 */
	$vendor_cost =  get_post_meta($product_val->product_id, 'vendor_cost', true ) ;
	//echo $vendor_cost;
	 
	echo '<tr>';
	//echo '<td></td>';
	echo '<td style="width:50%">' ;
	if(!empty($product_arr[0]->post_title)) {
	echo "<a href='".admin_url('post.php?post='.$product_arr[0]->ID.'&action=edit')."' target='_blank'>".$product_arr[0]->post_title."</a>" ;
	} else { 
	echo 'Product not exist.';
	}
	/* $jsa_service =  get_post_meta($product_val->product_id, 'jsa_auth_service', true ) ;
	if(!empty($jsa_service))
	{
		$jsa =  'Jsa Auction letter';
		echo '<br/><b>'.$jsa.'</b>';
	} 
	$jsa_100 =  get_post_meta($product_val->product_id, '100_auth_service', true ) ;
	if(!empty($jsa_100))
	{
		$jsa_hundrd = '100% Authentication team';
		echo '<br/><b>'.$jsa_hundrd.'</b>';
	}
	$auth_arr =  get_post_meta($product_val->product_id, 'authentication', false ) ;
	if(!empty($auth_arr))
	{
		echo '<br/><b>Authentication: '.implode(',',$auth_arr).'</b>';
	} */
	 
		 $attributes = get_the_terms( $product_val->product_id , 'pa_authentication');
		if(!empty($attributes)){ 
			echo '<br/><b>Authentication:';
			
			foreach( $attributes as $attrVal){
				
				echo  $attrVal->name.'</b>';
			}
		}else{
			 $jsa_auth_service  = "";
					$auth_service  = "";
					$authentication  = "";

				    $jsa_auth_service =  get_post_meta ( $product_val->product_id, 'jsa_auth_service' , true );
					$auth_service =  get_post_meta ( $product_val->product_id, '100_auth_service' , true );
					$authentication =  get_post_meta ( $product_id, 'authentication_status' , true );
					if(!empty(get_post_meta( $product_val->product_id, 'authentication'))){
				 
					$authentication_arr =	get_post_meta( $product_val->product_id , 'authentication');
				 	
					if(count($authentication_arr) > 1) {  
					$auth_arr = array_filter($authentication_arr);
				 
			 
						 $auth_string = implode(',',$auth_arr);
					} else { 
						 $auth_string = $authentication_arr[0];
					}
					}
					if(!empty($jsa_auth_service))
					{ 
						$j_auth_service =  ($jsa_auth_service == '1')  ? 'Jsa Auction letter' : '';
					} else {
						 
						$j_auth_service = '' ;
					}
					
					if(!empty($auth_service))
					{
						 
						$a_service =  ($auth_service == '1')  ? '100% Authentication team' : '';
					} else {
						 
						$a_service = '' ;
					}
					
					if(!empty($auth_string))
					{
						 
						$auth =  $auth_string;
					} else {
						 
						$auth = '' ;
					}
						if(!empty($j_auth_service)) {
							 
					$product_name .=	
						'<br/><br/>  <b>'.$j_auth_service.'</b> ';
						
						}
						if(!empty($a_service)) {
							 
						$product_name .=	'<br/><br/> <b>'.$a_service.'</b>';
						}
						if(!empty($auth)){
							echo $product_name .=	'<br/><br/> <b>Authentication: '.$auth.'</b><br/>';
						}
		}
	echo '</td>';
	echo '<td>'.$sku.'</td>' ;
	echo '<td>'.$this->get_vendor_name($product_val->vendor_id).'</td>' ;
	echo '<td>'.$vendor_cost.'</td>' ;
	echo '<td> $'.number_format($price,2,'.',',').'</td>';
	echo '<td><a href="#" class="fancybox" rel="'.$product_val->product_id.'-'.$_REQUEST['order_id'].'">'.$commission.'%</a></td>';
	echo '<td>$'.number_format($fee,2,'.',',').'</td>';
	echo '<td>$'.number_format($amt,2,'.',',').'</td>';
	echo '</tr>';		/* } */			}
		?>
	<?php 	echo '<tr>';
	//echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td id="row2"><b>Total $'.number_format($bid_sum,2,'.',',').'</b> <input type="hidden" name="sum_of_bid"></td>';
	echo '<td></td>';
	echo '<td> <b> $'.number_format($fee,2,'.',',').' </b></td>';
	echo '<td> <b> $'.number_format($amt,2,'.',',').'</b></td>';
	echo '<tr>';	?>		        </tbody>
           </table>
		   </div>
		 </div>
		 </div>		  <div style="clear:both;"></div>	
		
		 
		 <div class="contentpanel">
         <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Payments and Adjustments</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped">
              <thead>
                 <tr>
                 <th>Action</th>
				   <th>Date Paid</th>
                    <th>Ref No.</th>
					<th>Description</th>
					<th>Amount</th>					</tr>
              </thead>
			  <tbody>	
			  <?php 
			  global $wpdb ;	
			  $table1 = $wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
			 $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id = ".$_REQUEST['product_id'];
			 $sumqur ="SELECT SUM(amount) as amount FROM ".$table1."  WHERE ".$table1.".".order_id."=".$_REQUEST['order_id']." and product_id=".$_REQUEST['product_id'];
			 $product_arr = $wpdb->get_results($querystr); 
		     $product_sum = $wpdb->get_row($sumqur);
			 if(!empty($product_sum->amount))
			 {
				$total_trans = round($product_sum->amount,2) ;
			 } else {
			 $total_trans = "0.00" ;
			 }
		
			 echo '<input type="hidden" id="t_p" value='.$product_sum->amount.'>';
			  if(!empty($product_arr)) {
				   //echo '<pre>';	print_r($product_val); echo '</pre>';
				  	foreach($product_arr as $product_val) {
						echo '<tr>';
						echo '<td class="delid" order-id='.$product_val->trans_id.'>X</td>';
						echo '<td>'.$product_val->date_paid.'</td>';
						echo '<td>'.$product_val->ref_no.'</td>';
						echo '<td>'.$product_val->description.'</td>';
						echo '<td>'.$product_val->amount.'</td>';
						echo '</tr>';
					}
					
				  } ?>
			  
			  <form action="#" method="post" id="po_payment">
			  <tr id="a">
			  <td></td>
			  <td><b>Add Trans</b></td>
			  <td><input type="text" name="ref_no" value=""/></td>
			  <td><textarea name="description" cols="31" rows="3"></textarea></td>
			  <td><input type="text" name="amount" value="" id="amount"/></td>			<td><input type="hidden" name="order_id" value="<?php echo $_REQUEST['order_id']; ?>"></td>
			  </tr>
			  <tr id="b">
			  <td></td>
			  <td></td>
			  <td></td>
			  <td><b>PO Total</b></td>
			  <?php 
				$po = $this->calculate_po($_REQUEST['order_id'],$_REQUEST['product_id']);
				
			  ?>
			  <input type="hidden" name="pro_id" id="pro_id" value="<?php echo $_REQUEST['product_id'];  ?>" />
			  <td><input id="po_total" type="text" name="po_total" style="font-weight:bold;" value="<?php echo '$'.number_format($po,2,'.',',');  ?>" disabled /></td>
			  </tr>
			  <tr id="c">
			  <td></td>
			  <td></td>
			  <td></td>
			  <td><b>Total Payments</b></td>
			  <td><input type="text" name="total_payments" style="font-weight:bold;" value="<?php echo '$'.$total_trans ;  ?>" disabled id="total_p"/></td>
			  </tr>
			  <tr id="d">
			  <td></td>
			  <td></td>
			  <td></td>
			  <td><b>Balance Due</b></td>
			  <?php
			  $balance = $this->calculate_balanace_due($po,$total_trans);
			  ?>
			  <td><input type="text" name="balance" style="font-weight:bold;" disabled id="bal" value="<?php echo '$'.$balance ; ?>"/></td>
			  </tr>
			  <tr id="e">
			  <td></td>
			  <td></td>
			  <td colspan="2">
			  <div class="row">
							<div class="col-sm-6 col-sm-offset-8">
                          <button type="submit" class="btn btn-primary" id="save_payment">Save PO and Payments</button>
						  </div>
						</div>  
			  </td>
			  </tr>
			  </form>
			          </tbody>
           </table>
		   </div>
		 </div>
		 </div>
		 </div>
		 <script type="text/javascript">
		jQuery(document).ready(function(){	
 		//var r =$(".table-striped #row2").text();	
		//var res = r.replace("Total", ""); 
		//alert(res);
		//$('#po_total').val(res);
		
		//$('.t_po_total').html(r);
		
		//var t_p = $("#t_p").val();	
		//$('#total_p').val('$'+t_p);
		
		//$('#trs_total_p').html('$'+t_p);
		
		/* if($('#total_p').val()=='$'){
			$('#total_p').val('$0.00');
		} */
		/* if($('#po_total').val()=='$NaN'){
			$('#po_total').val('$0.00');
		} */

		
		//var po_total = r.replace ( /[^\d.]/g, '' );
		//var total_payment = t_p.replace ( /[^\d.]/g, '' );
		//total = parseFloat(po_total) - parseFloat(total_payment);
 		//$('#bal').val('$'+total.toFixed(2));
		//$('#b_d').html('$'+total.toFixed(2));
		jQuery('.fancybox').on('click',function(e){
		e.preventDefault();
		var order_param =	$(this).attr('rel');
		var page_value = $('#page_value').val();
           // $('.fancybox').fancybox();
						$.fancybox.open({
					href: "<?php echo get_site_url();?>/wp-admin/admin-ajax.php",
					type: "ajax",
					ajax: {
					type: "POST",
					data: {action:'update_commision', order_param:order_param,page_value:page_value}, // this is my post data
					}
			});
			});
			jQuery('#com_button').live('click',function(e){
			e.preventDefault();
				var datastring = $('#com_submit').serializeArray();
				console.log(datastring);
				   $.ajax({
					"type": "POST",
					"url": "<?php echo get_site_url();?>/wp-admin/admin-ajax.php", //this is my URL
					"data": {action:'update_commision_value',com_arr:datastring}, // this is my post data
					"success":function(data)  //currently i pass empty "", i don't require call back.
					{
					alert(data);
					location.reload();
						//$('#ajax-content').html(data);
					}
				});
			});
			jQuery('#save_payment').on('click',function(e){				  
				e.preventDefault();	
			 //alert('test');
				var datastring = $('#po_payment').serializeArray();
				var status = true ;
				//var z1 = /^[0-9]+$/;
				//var z1 = /^(?!0{5})\[0-9]+\.[0-9]+$/;
				var z1 =  /^([1-9](\.[0-9]+)?)|(0\.[0-9]*[1-9])*$/;
				var amount = $('#amount').val() ;
				
				var pro_id = $('#pro_id').val() ;
				if (!z1.test(amount)) { 
					alert('Please enter valid Amount');
					status = false;
				
				}
				if(pro_id == 'No Product found' || pro_id == 'Select Product') {
					alert('Please select product');
					status = false;
				}
				
				/* if($('input[name="amount"]').val() == "") {
					
				} */	
				if(status == true) {
				console.log(datastring);
				$.ajax({	
				"type": "POST",		
				"url": "<?php echo get_site_url();?>/wp-admin/admin-ajax.php", 	
				"data": {action:'save_payment_value',pay_arr:datastring},	
				"success":function(data)  		
				{			
				alert(data);
				$("input[name='ref_no']").val('');
				$("input[name='amount']").val('');
				$("textarea[name='description']").val('');
				location.reload();		
				}			
				});
				}	
			});	
		//delete payments 
		jQuery('.delid').on('click',function(e){
			e.preventDefault();
			var datastring = $(e.currentTarget).attr("order-id");
			console.log(datastring);
			$.ajax({	
				"type": "POST",		
				"url": "<?php echo get_site_url();?>/wp-admin/admin-ajax.php", 	
				"data": {action:'delete_payment_value',del_arr:datastring,},	
				"success":function(data)  		
				{		
						
				alert(data);
				location.reload();
				}			
				}) 
		});
		
		
   
	});
	
		</script>

		
		<?php
		}
		
		/* if($_REQUEST['action'] == 'send_email' && !empty($_REQUEST['order_id']) && !empty($_REQUEST['product_id']))
		{
			 
			$product_id = $_REQUEST['product_id'];
			$order_id = $_REQUEST['order_id'];
			$vendor_id = $_REQUEST['vendor_id'];
			$this->send_email($product_id,$order_id,$vendor_id);
			  
		} */
		
		
	}
	
	
	public function send_email($product_id,$order_id,$vendor_id)
	{ 
	

			global $wpdb ;
			
			
			 
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

	
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_id." and product_id=".$product_id;
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$orders = $wpdb->get_results($querystr, OBJECT);

	$msg = '';
	$msg.= '
	  <!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>'.get_bloginfo( 'name', 'display' ).'</title>
		<style>
		.panel-heading {
		background-color: #576099 !important;
		color: #fff !important;
		}
		
	</style>
	</head>
	<body>
	<div class="contentpanel">
		<div class="row" style="padding:10px;"><div style="float:left;"><img src="'.get_option("woo_logo").'"/></div><div style="float:right;padding-20px;"><p style="font-weight:bold;">BidAMI-Memorabilia.Expert <br/> 6490 W. Desert Inn <br/> Las Vegas NV 89146</p></div></div>
		</div>
		 <div style="clear:both;">
		<div class="contentpanel">
      <a href="<?php  echo admin_url() ; ?>admin.php?page=purchased-orders&view=order&order_id=<?php echo $order_val->order_id ; ?>&product_id=<?php echo $order_val->product_id ; ?>">View Detail</a><br/><a href="javascript:void(0)" id="<?php echo $order_val->order_id ; ?>" data-id="<?php echo $order_val->product_id ; ?>" data-title="<?php echo $order_val->vendor_id ; ?>" class="send-email">Send Email</a>
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Consignor PO</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped" cellpadding="20" cellspacing="2">
              <thead>
                 <tr>
                   <th>PO No</th>
				   <th>Vendor</th>
                    <th>PO Date</th>
                    <th>PO Total</th>
					<th>Trans Total</th>
					<th>Bal Due</th>	
                 </tr>
              </thead>
			  <tbody>';
			 
	
			foreach($orders as $order_val) { 
			$msg.=  '<tr>';
			$msg.= '<td>'.$order_val->order_id.'</td>' ;
			$msg.= '<td>'.$this->get_vendor_name($order_val->vendor_id).'</td>' ;
			$msg.= '<td>'.$order_val->time.'</td>';
			$po = $this->calculate_po($order_val->order_id,$order_val->product_id);  
			$msg.= '<td class="t_po_total">'.'$'.number_format($po,2,'.',',').'</td>';
			$total_trans = $this->calculate_total_transaction($order_val->order_id,$order_val->product_id);
			$msg.= '<td id="trs_total_p">'.'$'.$total_trans.'</td>';
			  $balance = $this->calculate_balanace_due($po,$total_trans);
			$msg.= '<td id="b_d">'.'$'.$balance.'</td>';
			$msg.= '</tr>';
			}
	
			
             $msg.= '</tbody>
           </table>
		   </div>
		 </div>
		 </div>
		 <div style="clear:both;"></div>';

		 global $wpdb ;
		 $wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		  $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id='".$_REQUEST['product_id']."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);

	 $vendor_id = $products[0]->vendor_id;
		 $user_meta_arr = get_user_meta($vendor_id);
		 $user_arr =  $this->user_info($vendor_id);

		  $msg.= '
		
		  <div class="contentpanel">
         <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Details</h3>
  </div>
		<div class="table-responsive">
		 <table class="table table-striped" cellpadding="20" cellspacing="2">
			<thead>
			<tr>
			<th colspan="3">Account</th>
			<th>Email</th>
			<th>Phone no</th>	
			</tr>
			</thead>
			<tbody>
			<tr>
			<td colspan="3" >'.$user_meta_arr['billing_address_1'][0].' '.$user_meta_arr['billing_city'][0].' '.$user_meta_arr['billing_postcode'][0].' '.$user_meta_arr['billing_state'][0].' '.$user_meta_arr['billing_country'][0].'</td><td>'.$user_arr[0]->user_email.'</td><td>'. $user_meta_arr['billing_phone'][0].'</td>
			</tr>
			</tbody>
		</table>
		  </div>
		 </div>
		 </div>	
		 
		 <div style="clear:both;"></div>	
		 
		 <div class="contentpanel">
      
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Auction Items</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped" cellpadding="20" cellspacing="2">
              <thead>
                 <tr>
                   <!--<th>Date Paid</th>-->
                    <th>Name</th>
                    <th>Sku</th>
					<th>Vendor Name</th>
					<!--<th>Vendor Cost</th>-->
                    <th>Price</th>
					<th>Pct</th>
					<th>Fee</th>
					<th>Amount</th>
                 </tr>
              </thead>
			  <tbody>';

		
				global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";
	
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id='".$_REQUEST['product_id']."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);
	$bid_sum = 0;
	$com_sum = 0;
	$amt_sum = 0;
		foreach($products as $product_val) {
		//$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

		  $querystr1 = "SELECT * FROM ".$wpdb->prefix.'posts'." where post_type='product' and post_status='publish' and ID='".$product_val->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$product_arr = $wpdb->get_results($querystr1, OBJECT);
	/* if(!empty($product_arr)) { */
	
	/* echo '<pre>';
	print_r($product_arr);
	echo '</pre>'; */
	
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$product_val->product_id."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	 if($product_val->commision_per != "0") {
	 $commision_per = $product_val->commision_per ;
	 }
	 else {
	 $comm_val = "";
		$comm_val  = $product_val->total_due ;
		$commision_per = ($comm_val/$price_arr[0]->bid)*100  ;
	 }
	   $querystr3 = "SELECT * FROM ".$wpdb->prefix.'usermeta'." where user_id='".$product_val->vendor_id."' and meta_key='_wcv_commission_percent' ";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$vendor_arr = $wpdb->get_results($querystr3, OBJECT);	
	 $product_commission = get_post_meta( $product_val->product_id, 'wcv_commission_percent', true );
	 $vendor_commission  = $vendor_arr[0]->meta_value;
	 if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}
		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}

	//get regular/sale price of product
			
		 $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);  
		
		
		
		//get regular price of product
			
		$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($price_arr))
		{
			//$order_arr[] = $filter_order_value->order_id;
			$price = $price_arr[0]->bid;
		} 
		else if(!empty($post_arr))
		{
			$price = $post_arr[0]->meta_value;
		}
		else if(!empty($postregular_arr))
		{
			$price = $postregular_arr[0]->meta_value;
		}
		
		 $order = new WC_Order( $_REQUEST['order_id'] );
		//$dues  = WCV_Vendors::get_vendor_dues_from_order( $order, false );
		
		/* echo '<pre>';
		print_r($dues); */
		/* echo '<pre>';
		print_r($order->get_items()); */
		foreach ( $order->get_items() as $key => $product ) {
		if($product[ 'product_id' ] == $product_val->product_id) {
		$price =  $product[ 'line_subtotal' ];
		}
		//$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
		//echo $commission_rate = WCV_Commission::get_commission_rate( $product_id );
		//$commission1      = $product_price * ( $commission_rate / 100 );
		// $commission1      = round( $commission, 2 );
			//echo $com1 = WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order );
		}
		$commission = WCV_Commission::get_commission_rate( $product_val->product_id );
		//$commission      = $product_price * ( $commission_rate / 100 );
		//$commission      = round( $commission, 2 );
			//$total_amt = $price_arr[0]->bid - $product_val->total_due ;
			$amt = $price * ($commission/100);
			$fee = $price - ($price * ($commission/100)) ;
	$bid_sum = $price + $bid_sum ;
	$com_sum = $product_val->total_due + $com_sum ;
	$amt_sum = $total_amt + $amt_sum ;
	$sku = "";
	$sku =  get_post_meta($product_val->product_id, '_sku', true );
	/* echo '<pre>';
	print_r($price_arr);
	 */
	$vendor_cost =  get_post_meta($product_val->product_id, 'vendor_cost', true ) ;
	//echo $vendor_cost;
	 
	$msg.= '<tr>';
	//echo '<td></td>';
	$msg.= '<td style="width:50%">' ;
	if(!empty($product_arr[0]->post_title)) {
	$msg.= $product_arr[0]->post_title ;
	} else {
	$msg.= 'Product not exist.';
	}
	$jsa_service =  get_post_meta($product_val->product_id, 'jsa_auth_service', true ) ;
	if(!empty($jsa_service))
	{
		$jsa =  'Jsa Auction letter';
		$msg.= '<br/><b>'.$jsa.'</b>';
	} 
	$jsa_100 =  get_post_meta($product_val->product_id, '100_auth_service', true ) ;
	if(!empty($jsa_100))
	{
		$jsa_hundrd = '100% Authentication team';
		$msg.= '<br/><b>'.$jsa_hundrd.'</b>';
	}
	$auth_arr =  get_post_meta($product_val->product_id, 'authentication', false ) ;
	if(!empty($auth_arr))
	{
		$msg.= '<br/><b>Authentication: '.implode(',',$auth_arr).'</b>';
	}
	
	$msg.= '</td>';
	$msg.= '<td>'.$sku.'</td>' ;
	$msg.= '<td>'.$this->get_vendor_name($product_val->vendor_id).'</td>' ;
	//$msg.= '<td>'.$vendor_cost.'</td>' ;
	$msg.= '<td> $'.number_format($price,2,'.',',').'</td>';
	$msg.= '<td><a href="#" class="fancybox" rel="'.$product_val->product_id.'-'.$_REQUEST['order_id'].'">'.$commission.'%</a></td>';
	$msg.= '<td>$'.number_format($fee,2,'.',',').'</td>';
	$msg.= '<td>$'.number_format($amt,2,'.',',').'</td>';
	$msg.= '</tr>';		/* } */			}
 	$msg.= '<tr>';
	//echo '<td></td>';
	$msg.= '<td></td>';
	$msg.= '<td></td>';
	$msg.= '<td></td>';
	$msg.= '<td id="row2"><b>Total $'.number_format($bid_sum,2,'.',',').'</b> <input type="hidden" name="sum_of_bid"></td>';
	$msg.= '<td></td>';
	$msg.= '<td> <b> $'.number_format($fee,2,'.',',').' </b></td>';
	$msg.= '<td> <b> $'.number_format($amt,2,'.',',').'</b></td>';
	$msg.= '<tr>		        </tbody>
           </table>
		   </div>
		 </div>
		 </div>		  <div style="clear:both;"></div>	
		
		 
		 <div class="contentpanel">
         <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Payments and Adjustments</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped" cellpadding="20" cellspacing="2">
              <thead>
                 <tr>
				   <th>Date Paid</th>
                    <th>Ref No.</th>
					<th>Description</th>
					<th>Amount</th>					</tr>
              </thead>
			  <tbody>';	
			  global $wpdb ;	
			  $table1 = $wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
			 $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$_REQUEST['order_id']." and product_id = ".$_REQUEST['product_id'];
			 $sumqur ="SELECT SUM(amount) as amount FROM ".$table1."  WHERE ".$table1.".".order_id."=".$_REQUEST['order_id']." and product_id=".$_REQUEST['product_id'];
			 $product_arr = $wpdb->get_results($querystr); 
		     $product_sum = $wpdb->get_row($sumqur);
			 if(!empty($product_sum->amount))
			 {
				$total_trans = round($product_sum->amount,2) ;
			 } else {
			 $total_trans = "0.00" ;
			 }
		
			  if(!empty($product_arr)) {
				   //echo '<pre>';	print_r($product_val); echo '</pre>';
				  	foreach($product_arr as $product_val) {
						$msg.= '<tr>';
						$msg.= '<td>'.$product_val->date_paid.'</td>';
						$msg.= '<td>'.$product_val->ref_no.'</td>';
						$msg.= '<td>'.$product_val->description.'</td>';
						$msg.= '<td>'.$product_val->amount.'</td>';
						$msg.= '</tr>';
					}
					
				  } else {
				   $msg.= '
			  <tr id="b">
			  <td colspan="3"><b>No Transaction Found</b></td>
			  </tr>
			  ';
				  }
			  
			 $msg.= '
			  <tr id="b">
			  <td></td>
			  <td></td>
			  <td><b>PO Total</b></td>';
			
				$po = $this->calculate_po($_REQUEST['order_id'],$_REQUEST['product_id']);
				
			 
			  $msg.= '
			  <td>$'.number_format($po,2,'.',',').'</td>
			  </tr>
			  <tr id="c">
			  <td></td>
			  <td></td>
			  <td><b>Total Payments</b></td>
			  <td>$'.$total_trans.'</td>
			  </tr>
			  <tr id="d">
			  <td></td>
			  <td></td>
			  <td><b>Balance Due</b></td>';
			  $balance = $this->calculate_balanace_due($po,$total_trans);
			  $msg.= '<td>$'.$balance.'</td>
			  </tr>
			          </tbody>
           </table>
		   </div>
		 </div>
		 </div>
		 </body>
		 </html>
		 ';
	
	
		$userdata = get_user_by('id',$vendor_id);
		 $to_mail = $userdata->data->user_email;
		// $to_mail = 'pallavi.v@macrew.net';
		//$vendor_id = 
		$to = $to_mail;
		$subject = '[Memorabilia.Expert] PO (Purchased Order Transaction)';

		$headers = array('Content-Type: text/html; charset=UTF-8');
 
		 wp_mail( $to, $subject, $msg, $headers );
			 
		
	die();
	
	}
	
	public function update_commision()
	{
		global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$explode_arr =  explode('-',$_REQUEST['order_param']);
		  $querystr1 = "SELECT * FROM ".$wpdb->prefix.'posts'." where post_type='product' and post_status='publish' and ID='".$explode_arr[0]."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$product_arr = $wpdb->get_results($querystr1, OBJECT);
	$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$explode_arr[1]." and product_id='".$explode_arr[0]."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
	$products = $wpdb->get_results($querystr, OBJECT);
	
	  $querystr3 = "SELECT * FROM ".$wpdb->prefix.'usermeta'." where user_id='".$products[0]->vendor_id."' and meta_key='_wcv_commission_percent' ";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$vendor_arr = $wpdb->get_results($querystr3, OBJECT);	
	 $product_commission = get_post_meta( $products[0]->product_id, 'wcv_commission_percent', true );
	 $vendor_commission  = $vendor_arr[0]->meta_value;
	 if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}
		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}
		$commission = WCV_Commission::get_commission_rate( $products[0]->product_id );
		if(!empty($product_arr[0]->post_title))
		{
			$product_title = $product_arr[0]->post_title;
		} else {
			$product_title = 'Product not exist.';
		}
		if(!empty($_REQUEST['page_value']) && $_REQUEST['page_value'] == 'detail_page')
	{
		echo  '<div class="contentpanel">
      <div class="panel panel-default">
            <div class="panel-heading inline-items">
			<h4 class="panel-title">Change Consignor Pct </h4>
            </div>
		<form class="form-horizontal" action="" method="post" style="margin-top:30px;" id="com_submit">
					 <div class="panel panel-default">
                                         
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="focusedInput">Auc No: </label>
                          <div class="col-sm-4">
                             <label class="col-sm-3 control-label" for="focusedInput">'.$explode_arr[0].'</label>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="focusedInput">Name</label>
                          <div class="col-sm-4">
                            <label class="col-sm-12 control-label" for="focusedInput">'.$product_title.'</label>
                          </div>
                        </div>
						 <div class="form-group">
                          <label class="col-sm-3 control-label" for="focusedInput">Consign Pct: </label>
                          <div class="col-sm-4">
                            <input class="form-control" id="focusedInput" type="text" value="'.$commission.'"  name="com_value">
                          </div>
                        </div>
						<input type="hidden" name="order_id" value="'.$explode_arr[1].'"/>
						<input type="hidden" name="product_id" value="'.$explode_arr[0].'"/>
						   <div class="panel-footer">
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3">
                          <button type="submit" class="btn btn-primary"  id="com_button">Save changes</button>
						  </div>
						</div>  
                        </div>
                      </div>
                   </form>
				   </div>
				   </div>';
				   
				   } else {
				   
					echo  '<div class="contentpanel">
      <div class="panel panel-default">
            <div class="panel-heading inline-items">
			<h4 class="panel-title">Change Consignor Pct </h4>
            </div>
		<form class="form-horizontal" action="" method="post" style="margin-top:30px;" id="com_submit_'.$explode_arr[0].'">
					 <div class="panel panel-default">
                                         
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="focusedInput">Auc No: </label>
                          <div class="col-sm-4">
                             <label class="col-sm-3 control-label" for="focusedInput">'.$explode_arr[0].'</label>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label" for="focusedInput">Name</label>
                          <div class="col-sm-4">
                            <label class="col-sm-12 control-label" for="focusedInput">'.$product_title.'</label>
                          </div>
                        </div>
						 <div class="form-group">
                          <label class="col-sm-3 control-label" for="focusedInput">Consign Pct: </label>
                          <div class="col-sm-4">
                            <input class="form-control" id="focusedInput" type="text" value="'.$commission.'"  name="com_value_'.$explode_arr[0].'">
                          </div>
                        </div>
						<input type="hidden" name="order_id_'.$explode_arr[0].'" value="'.$explode_arr[1].'"/>
						<input type="hidden" name="product_id_'.$explode_arr[0].'" value="'.$explode_arr[0].'"/>
						   <div class="panel-footer">
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3">
                          <button type="button" class="btn btn-primary commission_change" rel="'.$explode_arr[0].'">Save changes</button>
						  </div>
						</div> 
                        </div>
                      </div>
                   </form>
				   </div>
				   </div>';	
				   
				   
				   }
				   die;
	}	
	
	
		public function save_payment_value_dynamic()	{
		 $rel = $_REQUEST['rel'];
		global $wpdb ;	
		$com = array();
		$wpdb->custom_table_name1 = "{$wpdb->prefix}pv_commission";
		foreach($_REQUEST['pay_arr'] as $tt) {	
		$today = date("Y-m-d H:i:s");
		if($tt['name'] == 'ref_no_'.$rel) {
			$com['ref_no'] = $tt['value'];	
			}	
			if($tt['name'] == 'description_'.$rel) {	
			$com['description'] = $tt['value'];	
			}
			if($tt['name'] == 'amount_'.$rel) {	
			$com['amount'] = $tt['value'];	
			}		
			if($tt['name'] == 'order_id_'.$rel) {	
			$com['order_id'] = $tt['value'];	
			}
			
			if($tt['name'] == 'pro_id_'.$rel) {	
			$com['pro_id'] = $tt['value'];	
			}	
			}
			$po = $this->calculate_po($com['order_id'],$com['pro_id']);	
			/* if($com['amount'] > $po)
			{
				echo 'Your pay amount is greater than your total due.';
			} else { */
			$total_trans = $this->calculate_total_transaction($com['order_id'],$com['pro_id']);
			$sign = substr($com['amount'],0,1) ;
				if($total_trans != '0.00') {
					//echo $po ;
					//echo $total_trans ;
					//echo $com['amount'];
					 $bal_due = $po - ($total_trans + $com['amount']);
				} else {
				
					$bal_due = $po - $com['amount'];
				}
			
			
			$wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";	
			if($wpdb->insert( $wpdb->custom_table_name , array( 'ref_no' => $com['ref_no'], 'description' => $com['description'] , 'amount' => $com['amount'] , 'balance' => $bal_due , 'total_due'=> $po , 'order_id' => $com['order_id'],'product_id' => $com['pro_id'],'date_paid'=>$today ))){
			//echo $bal_due;
			$po  = $this->calculate_po($com['order_id'],$com['pro_id']);
			$trans = $this->calculate_total_transaction($com['order_id'],$com['pro_id']);
		$bal = $this->calculate_balanace_due($po,$trans);
		//echo $po ;
		//echo $bal ;
		/* echo $trans ;
		echo '<br/>';
		echo $bal ;
			 */
				 $querystr = "SELECT * FROM ".$wpdb->custom_table_name1." where order_id='".$com['order_id']."' and product_id='".$com['pro_id']."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
	$products = $wpdb->get_results($querystr, OBJECT);
	foreach($products as $pro_val)
	{ 
		$wpdb->update( $wpdb->custom_table_name1 , array( 'total_due' => $bal_due ), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		if($bal =='0.00') {
		if($pro_val->status == 'due') {
		//echo 'test';
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'paid'), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		}
		if($pro_val->status == 'reversed') {
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'reversed'), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		}
		} else {
		if($pro_val->status == 'paid') {
		//echo 'test';
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'due'), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		}
			
		}
		
	}
			
				echo 'payment has been saved.';
			
		/* } */
		die;
		}
	}
	
	public function save_payment_value()	{
		
		global $wpdb ;	
		$com = array();
		$wpdb->custom_table_name1 = "{$wpdb->prefix}pv_commission";
		foreach($_REQUEST['pay_arr'] as $tt) {	
		$today = date("Y-m-d H:i:s");
		if($tt['name'] == 'ref_no') {
			$com['ref_no'] = $tt['value'];	
			}	
			if($tt['name'] == 'description') {	
			$com['description'] = $tt['value'];	
			}
			if($tt['name'] == 'amount') {	
			$com['amount'] = $tt['value'];	
			}		
			if($tt['name'] == 'order_id') {	
			$com['order_id'] = $tt['value'];	
			}
			
			if($tt['name'] == 'pro_id') {	
			$com['pro_id'] = $tt['value'];	
			}	
			}
			$po = $this->calculate_po($com['order_id'],$com['pro_id']);	
			/* if($com['amount'] > $po)
			{
				echo 'Your pay amount is greater than your total due.';
			} else { */
			$total_trans = $this->calculate_total_transaction($com['order_id'],$com['pro_id']);
			$sign = substr($com['amount'],0,1) ;
				if($total_trans != '0.00') {
					//echo $po ;
					//echo $total_trans ;
					//echo $com['amount'];
					 $bal_due = $po - ($total_trans + $com['amount']);
				} else {
				
					$bal_due = $po - $com['amount'];
				}
			
			
			$wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";	
			if($wpdb->insert( $wpdb->custom_table_name , array( 'ref_no' => $com['ref_no'], 'description' => $com['description'] , 'amount' => $com['amount'] , 'balance' => $bal_due , 'total_due'=> $po , 'order_id' => $com['order_id'],'product_id' => $com['pro_id'],'date_paid'=>$today ))){
			//echo $bal_due;
			$po  = $this->calculate_po($com['order_id'],$com['pro_id']);
			$trans = $this->calculate_total_transaction($com['order_id'],$com['pro_id']);
		$bal = $this->calculate_balanace_due($po,$trans);
		//echo $po ;
		//echo $bal ;
		/* echo $trans ;
		echo '<br/>';
		echo $bal ;
			 */
				 $querystr = "SELECT * FROM ".$wpdb->custom_table_name1." where order_id='".$com['order_id']."' and product_id='".$com['pro_id']."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
	$products = $wpdb->get_results($querystr, OBJECT);
	foreach($products as $pro_val)
	{ 
		$wpdb->update( $wpdb->custom_table_name1 , array( 'total_due' => $bal_due ), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		if($bal =='0.00') {
		if($pro_val->status == 'due') {
		//echo 'test';
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'paid'), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		}
		if($pro_val->status == 'reversed') {
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'reversed'), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		}
		} else {
		if($pro_val->status == 'paid') {
		//echo 'test';
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'due'), array( 'order_id' => $com['order_id'],'product_id' => $com['pro_id'])) ;
		}
			
		}
		
	}
			
				echo 'payment has been saved.';
			
		/* } */
		die;
		}
	}
		
	public function delete_payment_value(){
		global $wpdb ;	
		$del = array();
		$wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
		$wpdb->custom_table_name1 = "{$wpdb->prefix}pv_commission";
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".trans_id."=".$_REQUEST['del_arr'];
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
		$trans = $wpdb->get_results($querystr, OBJECT);
		$order_id =  $trans[0]->order_id ;
		$product_id =  $trans[0]->product_id;
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'due'), array( 'order_id' => $trans[0]->order_id,'product_id'=>$trans[0]->product_id)) ;
		if( $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->custom_table_name." WHERE `trans_id`=%d",$_REQUEST['del_arr']))){
		$querystr1 = "SELECT * FROM ".$wpdb->custom_table_name1." where order_id='".$trans[0]->order_id."' and product_id='".$trans[0]->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
	$products = $wpdb->get_results($querystr1, OBJECT);
			$po  = $this->calculate_po($trans[0]->order_id,$trans[0]->product_id);
		$trans = $this->calculate_total_transaction($trans[0]->order_id,$trans[0]->product_id);
		$bal = $this->calculate_balanace_due($po,$trans);
		$bal_amt = $this->calculate_balanace($po,$trans);
		//echo $bal_amt;
		//echo $trans[0]->order_id ;
		//echo $trans[0]->product_id;
		$wpdb->update( $wpdb->custom_table_name1 , array( 'total_due' => $bal_amt), array( 'order_id' => $order_id,'product_id' => $product_id)) ;
		if($trans < $po) {
	foreach($products as $pro_val)
	{ 
		

		if($pro_val->status == 'paid') {
		//echo 'test';
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'due'), array( 'order_id' => $order_id,'product_id' => $pro_val->product_id)) ;
		}
		if($pro_val->status == 'reversed') {
		$wpdb->update( $wpdb->custom_table_name1 , array( 'status' => 'reversed'), array( 'order_id' => $order_id,'product_id' => $pro_val->product_id)) ;
		}
	}
	}
		
				echo 'payment has been deleted.';
		}
		die;
	}
	public function update_commision_value()
	{
		global $wpdb ;
		$com = array();
		foreach($_REQUEST['com_arr'] as $com_val) {
			if($com_val['name'] == 'com_value') {
			$com['total_due'] = $com_val['value'];
			}
			if($com_val['name'] == 'order_id') {
			$com['order_id'] = $com_val['value'];
			}
			if($com_val['name'] == 'product_id') {
			$com['product_id'] = $com_val['value'];
			}
		}
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";
		$wpdb->custom_table_postmeta = "{$wpdb->prefix}postmeta";
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$com['order_id']." and product_id='".$com['product_id']."'";
	$products = $wpdb->get_results($querystr, OBJECT);
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$com['product_id']."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
//$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	
	//get regular/sale price of product
			
		 $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$com['product_id']."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);  
		
		
		
		//get regular price of product
			
		$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$com['product_id']."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($price_arr))
		{
			//$order_arr[] = $filter_order_value->order_id;
			$price = $price_arr[0]->bid;
		} 
		else if(!empty($post_arr))
		{
			$price = $post_arr[0]->meta_value;
		}
		else if(!empty($postregular_arr))
		{
			$price = $postregular_arr[0]->meta_value;
		}
		
		 $order = new WC_Order( $com['order_id'] );
		//$dues  = WCV_Vendors::get_vendor_dues_from_order( $order, false );
		
		/* echo '<pre>';
		print_r($dues); */
		/* echo '<pre>';
		print_r($order->get_items()); */
		foreach ( $order->get_items() as $key => $product ) {
		if($product[ 'product_id' ] == $product_val->product_id) {
		$price =  $product[ 'line_subtotal' ];
		}
		//$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
		//echo $commission_rate = WCV_Commission::get_commission_rate( $product_id );
		//$commission1      = $product_price * ( $commission_rate / 100 );
		// $commission1      = round( $commission, 2 );
			//echo $com1 = WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order );
		}
		
		
		$product_id = $com['product_id'] ;
		
		
		$vendor_id = WCV_Vendors::get_vendor_from_product( $product_id );

		 $product_commission = get_post_meta( $product_id, 'wcv_commission_percent', true );
		$commission1 = WCV_Commission::get_commission_rate( $com['product_id'] );
		//echo $commission1;
		 $vendor_commission  = get_user_meta( $vendor_id, '_wcv_commission_percent', true );
		 $default_commission = WCVendors_Pro::get_option( 'commission_percent' );  

		$comm_val = "";
		 $comm_val  = $com['total_due'] ;
		
		if ( $product_commission != '' && $product_commission !== false ) {
			 $commission = $product_commission;
			$amt = $price * ($comm_val/100);
			$wpdb->update( $wpdb->custom_table_postmeta , array( 'meta_value' => $comm_val), array( 'post_id' => $com['product_id'] , 'meta_key'=>'wcv_commission_percent'));
			$wpdb->update( $wpdb->custom_table_name , array( 'total_due' => $amt), array( 'order_id' => $com['order_id'] , 'product_id' => $com['product_id'] ));
			echo 'Product Commision has been updated.';
		}

		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			//echo $commission1 = $vendor_commission;
			 $amt = $price * ($comm_val/100);
			//echo $products[0]->vendor_id;
			//echo $vendor_id;
			// update postmeta commision
			$wpdb->update( $wpdb->custom_table_usermeta , array( 'meta_value' => $comm_val), array( 'user_id' => $vendor_id , 'meta_key'=>'_wcv_commission_percent'));
			//echo $commission2  = WCV_Vendors::get_default_commission( $vendor_id );
			//echo $amt2 = $price * ($commission2/100);
			

			
			$wpdb->update( $wpdb->custom_table_name , array( 'total_due' => $amt), array( 'order_id' => $com['order_id'] , 'product_id' => $com['product_id'] ));
			//echo 'You can\'t update vendor\'s Profile Commissions';
			echo 'Vendor Commision has been updated.';
		}

		else if ( $default_commission != '' && $default_commission !== false ) {
			 $commission = $default_commission;
			echo 'You can\'t update wc vendor pro Default Commissions';
		}

	
		die;
	}
	
	
	public function update_commision_value_dynamic()
	{
		global $wpdb ;
		$com = array();
		$rel = $_REQUEST['rel'];
		foreach($_REQUEST['com_arr'] as $com_val) {
			if($com_val['name'] == 'com_value_'.$rel) {
			$com['total_due'] = $com_val['value'];
			}
			if($com_val['name'] == 'order_id_'.$rel) {
			$com['order_id'] = $com_val['value'];
			}
			if($com_val['name'] == 'product_id_'.$rel) {
			$com['product_id'] = $com_val['value'];
			}
		}
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";
		$wpdb->custom_table_postmeta = "{$wpdb->prefix}postmeta";
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$com['order_id']." and product_id='".$com['product_id']."'";
	$products = $wpdb->get_results($querystr, OBJECT);
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$com['product_id']."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
//$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	
	//get regular/sale price of product
			
		 $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$com['product_id']."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);  
		
		
		
		//get regular price of product
			
		$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$com['product_id']."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($price_arr))
		{
			//$order_arr[] = $filter_order_value->order_id;
			$price = $price_arr[0]->bid;
		} 
		else if(!empty($post_arr))
		{
			$price = $post_arr[0]->meta_value;
		}
		else if(!empty($postregular_arr))
		{
			$price = $postregular_arr[0]->meta_value;
		}
		
		 $order = new WC_Order( $com['order_id'] );
		//$dues  = WCV_Vendors::get_vendor_dues_from_order( $order, false );
		
		/* echo '<pre>';
		print_r($dues); */
		/* echo '<pre>';
		print_r($order->get_items()); */
		foreach ( $order->get_items() as $key => $product ) {
		if($product[ 'product_id' ] == $product_val->product_id) {
		$price =  $product[ 'line_subtotal' ];
		}
		//$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
		//echo $commission_rate = WCV_Commission::get_commission_rate( $product_id );
		//$commission1      = $product_price * ( $commission_rate / 100 );
		// $commission1      = round( $commission, 2 );
			//echo $com1 = WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order );
		}
		
		
		$product_id = $com['product_id'] ;
		
		$vendor_id = WCV_Vendors::get_vendor_from_product( $product_id );

		$product_commission = get_post_meta( $product_id, 'wcv_commission_percent', true );
		$commission1 = WCV_Commission::get_commission_rate( $com['product_id'] );
		//echo $commission1;
		$vendor_commission  = get_user_meta( $vendor_id, '_wcv_commission_percent', true );
		$default_commission = WCVendors_Pro::get_option( 'commission_percent' );  
		$comm_val = "";
		 $comm_val  = $com['total_due'] ;
		
		if ( $product_commission != '' && $product_commission !== false ) {
			 $commission = $product_commission;
			$amt = $price * ($comm_val/100);
			$wpdb->update( $wpdb->custom_table_postmeta , array( 'meta_value' => $comm_val), array( 'post_id' => $com['product_id'] , 'meta_key'=>'wcv_commission_percent'));
			$wpdb->update( $wpdb->custom_table_name , array( 'total_due' => $amt), array( 'order_id' => $com['order_id'] , 'product_id' => $com['product_id'] ));
			echo 'Product Commision has been updated.';
		}

		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			//echo $commission1 = $vendor_commission;
			 $amt = $price * ($comm_val/100);
			//echo $products[0]->vendor_id;
			//echo $vendor_id;
			// update postmeta commision
			$wpdb->update( $wpdb->custom_table_usermeta , array( 'meta_value' => $comm_val), array( 'user_id' => $vendor_id , 'meta_key'=>'_wcv_commission_percent'));

			$wpdb->update( $wpdb->custom_table_name , array( 'total_due' => $amt), array( 'order_id' => $com['order_id'] , 'product_id' => $com['product_id'] ));
			//echo 'You can\'t update vendor\'s Profile Commissions';
			echo 'Vendor Commision has been updated.';
		}

		else if ( $default_commission != '' && $default_commission !== false ) {
			 $commission = $default_commission;
			echo 'You can\'t update wc vendor pro Default Commissions';
		}

		
	
		die;
	}
	
	
	public function calculate_po($order_id,$product_id)
	{

			global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$wpdb->custom_table_usermeta = "{$wpdb->prefix}usermeta";
	
		 $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_id." and product_id=".$product_id;
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr, OBJECT);
	$bid_sum = 0;
	$com_sum = 0;
	$amt_sum = 0;
		foreach($products as $product_val) {
		//echo $product_val->total_due ;
		//$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

		  $querystr1 = "SELECT * FROM ".$wpdb->prefix.'posts'." where post_type='product' and post_status='publish' and ID='".$product_val->product_id."'";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$product_arr = $wpdb->get_results($querystr1, OBJECT);
	/* if(!empty($product_arr)) { */
	
	/* echo '<pre>';
	print_r($product_arr);
	echo '</pre>'; */
	//echo $product_val->total_due ;
		  $querystr2 = "SELECT * FROM ".$wpdb->prefix.'simple_auction_log'." where auction_id='".$product_val->product_id."' order by id desc limit 1";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$price_arr = $wpdb->get_results($querystr2, OBJECT);
	 if($product_val->commision_per != "0") {
	 $commision_per = $product_val->commision_per ;
	 }
	 else {
	 $comm_val = "";
		$comm_val  = $product_val->total_due ;
		$commision_per = ($comm_val/$price_arr[0]->bid)*100  ;
	 }
	   $querystr3 = "SELECT * FROM ".$wpdb->prefix.'usermeta'." where user_id='".$product_val->vendor_id."' and meta_key='_wcv_commission_percent' ";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
$commision_per = "";
	$vendor_arr = $wpdb->get_results($querystr3, OBJECT);	
	 $product_commission = get_post_meta( $product_val->product_id, 'wcv_commission_percent', true );
	 $vendor_commission  = $vendor_arr[0]->meta_value;
	 if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}
		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}
		//get regular/sale price of product
			
		 $post_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_sale_price' and meta_value != ''";
		$post_arr = $wpdb->get_results($post_query, OBJECT);  
		
		
		
		//get regular price of product
			
		$postregular_query = "select * from ".$wpdb->prefix.'postmeta'." where post_id='".$product_val->product_id."' and meta_key = '_regular_price' and meta_value != ''";
		$postregular_arr = $wpdb->get_results($postregular_query, OBJECT);  
		if(!empty($price_arr))
		{
			//$order_arr[] = $filter_order_value->order_id;
			$price = $price_arr[0]->bid;
		} 
		else if(!empty($post_arr))
		{
			$price = $post_arr[0]->meta_value;
		}
		else if(!empty($postregular_arr))
		{
			$price = $postregular_arr[0]->meta_value;
		}
			
		 $order = new WC_Order( $order_id );
		

	
		foreach ( $order->get_items() as $key => $product ) {
		if($product[ 'product_id' ] == $product_val->product_id) {
		//die;
		 $price =  $product[ 'line_subtotal' ];
		}
		//$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
		//echo $commission_rate = WCV_Commission::get_commission_rate( $product_id );
		//$commission1      = $product_price * ( $commission_rate / 100 );
		// $commission1      = round( $commission, 2 );
			//echo $com1 = WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order );
		}
		
		//echo $product_val->total_due ;
		  $commission = WCV_Commission::get_commission_rate( $product_val->product_id );

		 $amt = $price * ($commission/100);
		$fee = $price - ($price * ($commission/100)) ;
	$total_amt = $price_arr[0]->bid - $product_val->total_due ;
	$bid_sum = $price_arr[0]->bid + $bid_sum ;
	$com_sum = $product_val->total_due + $com_sum ;
	$amt_sum = $total_amt + $amt_sum ;
	/* } */
	
	 } 
	return $amt;
	
	}
	
	
	public function calculate_total_transaction($order_id,$product_id)
	{
		 global $wpdb ;	
			  $table1 = $wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
			 $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_id." and product_id=".$product_id ;
			 $sumqur ="SELECT SUM(amount) as amount FROM ".$table1."  WHERE ".$table1.".".order_id."=".$order_id." and product_id = ".$product_id;
			 $product_arr = $wpdb->get_results($querystr); 
		     $product_sum = $wpdb->get_row($sumqur);
			 if(!empty($product_sum->amount))
			 {
				 $total_trans = round($product_sum->amount,2) ;
			 } else {
			 $total_trans = "0.00" ;
			 }
			 return $total_trans;
	}
	
	public function calculate_balanace_due($total_order,$total_tran)
	{
		$balance_due = $total_order - $total_tran;
		return number_format($balance_due,2,'.',',');
	}
	
	public function calculate_balanace($total_order,$total_tran)
	{
		$balance = $total_order - $total_tran;
		return $balance;
	}
	
	public function get_vendor_name1($order_id)
	{
			global $wpdb ;
				$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";

	$querystr1 = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_id." group by vendor_id";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";

	$products = $wpdb->get_results($querystr1, OBJECT);
	foreach($products as $product_val)
	{
		$pro[] = $product_val->vendor_id;
	}
	if(!empty($pro))
	{
	$vendor_string = "'".implode("','",$pro)."'";
		$wpdb->user_table = "{$wpdb->prefix}users";
		 $querystr = "SELECT * FROM ".$wpdb->user_table." where ".$wpdb->user_table.".".ID." IN (".$vendor_string.")";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
		$users = $wpdb->get_results($querystr, OBJECT);
		if(!empty($users))
		{
			foreach($users as $userval)
			{
				$user[] = $userval->display_name;
			}
			$user_string = implode(',',$user);
			return $user_string;
		}
		else {
		return 'Vendor not exist';
		}
		
	}
		
		/* if(!empty($users)) 
		{
			return $users[0]->display_name;
		} else {
			return 'Vendor not exist';
		} */
	
	}
	
	
	public function get_vendor_name($vendor_id)
	{
			global $wpdb ;
		$wpdb->user_table = "{$wpdb->prefix}users";
		 $querystr = "SELECT * FROM ".$wpdb->user_table." where ".$wpdb->user_table.".".ID."=".$vendor_id;
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
		$users = $wpdb->get_results($querystr, OBJECT);
		if(!empty($users)) 
		{
			return $users[0]->display_name;
		} else {
			return 'Vendor not exist';
		}
	
	}
	
	public function user_info($vendor_id)
	{
			global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}users";
		$wpdb->custom_table_name1 = "{$wpdb->prefix}usermeta";
	
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".ID." = $vendor_id";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
		$users_arr = $wpdb->get_results($querystr, OBJECT);
		return $users_arr;
	}
	
	
	public function calculate_balanace_due1($total_order,$order_id)
	{
		 global $wpdb ;	
			  $table1 = $wpdb->custom_table_name = "{$wpdb->prefix}po_transaction";
			 $querystr = "SELECT * FROM ".$wpdb->custom_table_name." where ".$wpdb->custom_table_name.".".order_id."=".$order_id;
			 $trans_arr = $wpdb->get_results($querystr,OBJECT); 
			 $po = $this->calculate_po($order_id);
			 $total_trans = $this->calculate_total_transaction($order_id);
			 $bal = "0";
			 $bal_due = $po;
			 foreach($trans_arr as $trans_val) {
			 echo $trans_val->amount;
			 $sign = substr($trans_val->amount,0,1) ;
			if($sign == '-') {
				echo $bal_due = $bal_due + $trans_val->amount;
				/* if($total_trans != '0.00') {
					//echo $po ;
					//echo $total_trans ;
					//echo $com['amount'];
					 $bal_due = $po + ($total_trans + $trans_val->amount);
				} else {
				
					$bal_due = $po + $trans_val->amount;
				} */
			
			} else {
			$bal_due = $bal_due - $trans_val->amount;
			/* if($total_trans != '0.00') {
				//echo $po ;
				//echo $total_trans ;
				//echo $com['amount'];
				 $bal_due = $po - ($total_trans + $trans_val->amount);
			} else {
			
				$bal_due = $po - $trans_val->amount;
			} */
			}
			  //$bal = $bal + $bal_due;
				//$balance_due = $total_order - $total_tran;
				//echo $bal_due;
				}
				//echo $bal_due;
		return number_format($bal_due,2,'.',',');
	}
	
	
	public function get_order_list()
	{
	$content = "" ;
			global $wpdb ;
		$wpdb->custom_table_name = "{$wpdb->prefix}pv_commission";
		$querystr = "SELECT * FROM ".$wpdb->custom_table_name." GROUP BY order_id ORDER BY ".$wpdb->custom_table_name.".".order_id." ASC";
		//$querystr = "SELECT * FROM `wp_pv_commission` GROUP BY order_id ORDER BY `wp_pv_commission`.`order_id` ASC";
	$orders = $wpdb->get_results($querystr, OBJECT);
	$content.= '<div class="contentpanel">
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Orders</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped" id="table2">
              <thead>
                 <tr>
                   <th>PO No</th>
                    <th>PO Date</th> 
					 
                    <th>PO Total</th>
					<th>Trans Total</th>
					<th>Bal Due</th>
                 </tr>
              </thead>
			  <tbody>' ;
	foreach($orders as $order_val) {
			$content.=  '<tr>
			<td><a href="'.admin_url().'admin.php?page=purchased-orders&view=order&order_id='.$order_val->order_id.'">'.$order_val->order_id.'</a></td>
			<td>'.$order_val->time.'</td>
			<td></td>
			<td></td>
			<td></td>
			</tr>';
		}
		$content.= '</tbody></table>
		   </div>
		 </div>
		 </div>
		   <ul class="pagination">
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
  </ul>';
	echo $content ;
	//return $content ;
	}
	public function view_order()
	{
	wp_enqueue_style( 'po-datatable-style1', plugins_url('css/bootstrap.min.css', __FILE__  ));
		wp_enqueue_script( 'my-plugin-script1' , plugins_url( 'js/jquery-1.11.1.min.js', __FILE__ ) );
		wp_enqueue_script( 'my-plugin-script2' , plugins_url( 'js/jquery-migrate-1.2.1.min.js', __FILE__ ) );
		wp_enqueue_script( 'my-plugin-script4' , plugins_url( 'js/custom.js', __FILE__ ) );
		?>
		<div class="contentpanel">
      
      <div class="panel panel-default">
        <div class="panel-heading">	
  <h3 class="panel-title" style="display:inline">Orders</h3>
  </div>
		<div class="table-responsive">
		      <table class="table table-striped" id="table2">
              <thead>
                 <tr>
                   <th>PO No</th>
                    <th>PO Date</th>
                    <th>PO Total</th>
					<th>Trans Total</th>
					<th>Bal Due</th>
                 </tr>
              </thead>
           </table>
		   </div>
		 </div>
		 </div>
		 </div>
		<?php 
		}
	}
if( is_admin() )
    $po = new PO();	
?>