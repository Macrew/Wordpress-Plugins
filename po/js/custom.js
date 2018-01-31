 jQuery(document).ready(function($) {
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
	 
}); 
 