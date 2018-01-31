jQuery(document).ready(function(){	
	 
		jQuery('#proposals_lists').DataTable({
		"processing": true,
		"serverSide": true,
		"deferRender": true,
		"ajax": {url:jQuery("#base_url").val() +"admin/all_receipes/",type:"post",error:function(){jQuery("#project_table").append('<tbody class="employee-grid-error"><tr><th colspan="12">No data found in the server</th></tr></tbody>');}},
		"columns": [
			//{ data: "id"},
			{ data: "name","class":'read_only',orderable: true},            
			{ data: "prep_time","class":'read_only',orderable: true},
			{ data: "cook_time","class":'read_only',orderable: true},
			{ data: "status","class":'read_only',orderable: true},
			{ data: "description","class":'read_only',orderable: true},
			{ data: "id","class":'table-action',render: function ( data, type, full, meta ) {var edit_link = jQuery("#base_url").val()+'admin/edit_reciepe/'+data;var delete_link = jQuery("#base_url").val()+'admin/delete_receipe/'+data;return '<a href="'+edit_link+'"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'+delete_link+'" class="del" onclick="return confirm(\'Are you sure you want to delete this record?\');"><i class="fa fa-trash-o"></i></a>';}, sortable: false, searchable: false ,orderable: false}
		],
		"sPaginationType": "full_numbers"
	});
	
});