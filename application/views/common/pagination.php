<ul class="pagination pagination-sm pull-right">
  <li><a class='paginate_button previous<?= ($pagination['current_page'] > 1) ? '' : 'disabled' ?>' style='cursor:default' <?= ($pagination['current_page'] > 1) ? "onclick=\"changePage('".($pagination['current_page']-1)."','".$order['by']."','".$order['type']."')\""  : '' ?>> « Previous </a></li>
  <?php 
      if($pagination['count_start'] > 1)
      {	
          ?><li><a class='paginate_button' style='cursor:default' <?= ($pagination['current_page'] > 1) ? "onclick=\"changePage('1','".$order['by']."','".$order['type']."')\""  : '' ?>> 1 </a></li>
            <li><a>...</a></li><?php 
      }
  ?>
  <?php						
      for ($i = $pagination['count_start']; $i <= $pagination['count_end']; $i++) 
      { 
          ?><li><a class='paginate_button <?= ($i != $pagination['current_page']) ? '' : 'current1' ?>' style='cursor:default; <?= ($i != $pagination['current_page']) ? '' : 'background-color:#CCCCCC' ?>' <?= ($i != $pagination['current_page']) ? "onclick=\"changePage('".$i."','".$order['by']."','".$order['type']."')\"" : '' ?>> <?= $i ?> </a></li><?php
      }
  ?>
  <?php 
      if($pagination['count_end'] < $pagination['total_pages'])
      {	
          ?><li><a>...</a></li>
            <li><a class='paginate_button' style='cursor:default' <?= ($pagination['count_end'] < $pagination['total_pages']) ? "onclick=\"changePage('".$pagination['total_pages']."','".$order['by']."','".$order['type']."')\""  : '' ?>> <?= $pagination['total_pages'] ?> </a></li> <?php
      }
  ?>						
  <li><a class='paginate_button next <?= ($pagination['current_page'] < $pagination['total_pages']) ? '' : 'disabled' ?>' style='cursor:default' <?= ($pagination['current_page'] < $pagination['total_pages']) ? "onclick=\"changePage('".($pagination['current_page']+1)."','".$order['by']."','".$order['type']."')\""  : '' ?>> Next » </a></li>
</ul>