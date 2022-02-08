$(document).ready(function(){
	$('.checkAll, check').iCheck({
	    checkboxClass: 'icheckbox_square-blue',
	    radioClass: 'iradio_square-blue'
	});
});

$(document).ready(function(){
    $('input.checkAll').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('input.iCheck').iCheck('check');
        } else {
            $('input.iCheck').iCheck('uncheck');
        }
    });
});

$(document).ready(function(){
	$(document).on('click', '.confirm-delete-btn', function(){
		var table_const = data_drop = record_id = "";
		
		if(typeof($(this).data("constant")) != 'undefined'){
			table_const = $(this).data("constant");
		}else{
			console.log("Table constant not found");
			return false;
		}

		if(typeof($(this).data("id")) != 'undefined'){
			record_id = $(this).data("id");
		}else{
			console.log("Id not found");
			return false;
		}

		data_drop 	= (typeof($(this).data("drop")) != 'undefined') ? $(this).data("drop") : "";
		$.confirm({
	    	title : "Delete!",
	    	content : "Are you sure want to delete?",
		    buttons: {
		        delete: {
		        	text : '<i class="fa fa-check"></i>',
		        	btnClass: 'btn btn-success',
			        action: function(){
			            //location.href = this.$target.attr('href');
			            $.post(ADMIN_URL+"ajax/delete_record", {'table' : table_const, 'drop' : data_drop, 'record_id': record_id}, function(data,status){
			            	var obj = JSON.parse(data);
			            	if(obj.status == 1){
			            		toastr.success(obj.msg);
			            		table.draw();
			            	}else{
			            		toastr.error(obj.msg);
			            	}
			            });
			        }
			    }, 
		        cancel:{
		        	text : '<i class="fa fa-times"></i>',
		        	btnClass: 'btn btn-danger',
		        }
		    }
		});
	});

	$(document).on('click', '.confirm-status-change', function(){
		var table_const = status = record_id = "";
		
		if(typeof($(this).data("constant")) != 'undefined'){
			table_const = $(this).data("constant");
		}else{
			console.log("Table constant not found");
			return false;
		}

		if(typeof($(this).data("id")) != 'undefined'){
			record_id = $(this).data("id");
		}else{
			console.log("Id not found");
			return false;
		}

		status = (typeof($(this).data("status")) != 'undefined') ? $(this).data("status") : "";

		$.confirm({
	    	title : "Change Status!",
	    	content : "Are you sure want to change status?",
		    buttons: {
		        delete: {
		        	text : '<i class="fa fa-check"></i>',
		        	btnClass: 'btn btn-success',
			        action: function(){
			            //location.href = this.$target.attr('href');
			            $.post(ADMIN_URL+"ajax/status_change", {'table' : table_const, 'status' : status, 'record_id': record_id}, function(data,status){
			            	var obj = JSON.parse(data);
			            	if(obj.status == 1){
			            		toastr.success(obj.msg);
			            		table.draw();
			            	}else{
			            		toastr.error(obj.msg);
			            	}
			            });
			        }
			    }, 
		        cancel:{
		        	text : '<i class="fa fa-times"></i>',
		        	btnClass: 'btn btn-danger',
		        }
		    }
		});
	});
});


	function openImageModal(valcu)
	{
		$("#modelPlaceId").val(valcu);
		$("#myModalForUploadingImages").modal();
		
	}

	function getImages()
    {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
				
				if (this.readyState == 0) {
                    
                    document.getElementById("allImages").innerHTML="Please Wait...";
				}
				if (this.readyState == 1) {
                    
                    document.getElementById("allImages").innerHTML="Please Wait...";
				}
				if (this.readyState == 2) {
                    
                    document.getElementById("allImages").innerHTML="Please Wait...";
				}
				if (this.readyState == 3) {
                    
                    document.getElementById("allImages").innerHTML="Please Wait...";
                }
                if (this.readyState == 4 && this.status == 200) {
					
					document.getElementById("allImages").innerHTML="";
                    document.getElementById("allImages").innerHTML=this.responseText;
                }
            };
            xhttp.open("GET", ADMIN_URL+"image/getAllImage", true);
            xhttp.send();
	}
	
	

