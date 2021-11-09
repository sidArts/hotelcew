<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php //print_r($stores); exit;?>
<div class="panel">
	<div class="panel-heading">
		Hotel Images - <?= $hotel_details['name']?>
		<span class="page-buttons">
			<a href="javascript:void(0)" class="header-button" id="add_hotel_image_button"><i class="fa fa-plus-circle"></i> Add New</a>
		</span>
	</div>
	<div class="panel-body">
		<div class="row">
			<?php
			foreach ($room_images as $key => $value) {
				?>
				<div class="col-md-3">
					<div class="gallery-item">
						<img src="<?=base_url().'public/uploads/room/'.$value['image'] ?>">
						<button type='button' class='btn btn-danger' id='del-<?= $value["id"]?>' onclick='delete_hotel_image("<?=$value['id']?>")'><i class='fa fa-trash'></i></button>
						<!-- <input type="radio" name="main_image"> -->
					</div>
					
				</div>
				<?php
			}

			?>
		</div>
		
	</div>
	
</div>

<div id="add_hotel_image_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Images</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="<?= ADMIN_URL . 'user/submit_hotel_image/' ?>" id="store" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-12">
            <input type="hidden" name="hotel_id" value="<?= $hotel_details['id']?>">
            <input type="file" name="files[]" multiple="">
            
          </div>
          <div class="col-md-3">
            <br>
            <button type="submit" class="btn btn-primary btn-block submit-btn"><i class="fa fa-save"></i> Save <i class="fa fa-refresh fa-spin spin-loader" style="display: none;"></i></button>
          </div>
        </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
function delete_hotel_image(id){

	var flag = confirm("Are you sure want to delete this image");

     if(flag == true){
         

          $.ajax({
            url:"<?=ADMIN_URL.'user/delete_hotel_image/'?>",
            type: 'post',
            data:{id:id},
            datatype: 'json'
        })
        .done(function (data) { 
         
            location.reload(true)
         })
        .fail(function (jqXHR, textStatus, errorThrown) { 
         console.log('error');
         });

     }
	
}

function deletemultiple(id){
	

          $.ajax({
            url:"<?=ADMIN_URL.'user/delete_user/'?>",
            type: 'post',
            data:{id:id},
            datatype: 'json'
        })
        .done(function (data) { 
         
            location.reload(true)
         })
        .fail(function (jqXHR, textStatus, errorThrown) { 
         console.log('error');
         });

     
	
}

$("#checkAll").click(function(){
    $('.check').not(this).prop('checked', this.checked);
    
   
   
});
$(".deleteselected").click(function(){
 // var flag = confirm("Are you sure want to delete this store");

    // if(flag == true){
   $('.check:checked').each(function(index,item){
		  let ids = $(item).val();
		  deletemultiple(ids);
		     
  });
//}
});


</script>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#add_hotel_image_button').click(function()
		{
			$('#add_hotel_image_modal').modal('show');
		})
	})
</script>
<script>
  $(document).ready(function() {
    //let qrcode = $("#store_id").val();
    $("#store").validate({

      rules: {
        
      },
      messages: {
        //name: "Name field is required",
        //size: "Size field is required",
        // size: "Size field is required",
        // size: "Size field is required",
        // size: "Size field is required",
        // size: "Size field is required",
        
      },
      submitHandler: function(form) {
        $(".submit-btn").attr("disabled", true);
        $(".spin-loader").show();
        $.ajax({
          url: form.action,
          type: form.method,
          data: new FormData(form),
          contentType: false,
          cache: false,
          processData: false,
          success: function(response) {
            console.log(response);
            let obj = JSON.parse(response);
            if (obj.stat == 'success') {
              toastr.success(obj.msg);
              location.reload();
              //$(location).attr('href', "<?= ADMIN_URL . 'gallery/all' ?>")
            } else if (obj.stat == 'error') {
              toastr.error(obj.msg);
            }
          }
        });


      }
    });
  })
</script>
