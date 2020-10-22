<div id='calendar'>

    <form method="post" action="<?=($base_url.AMP.'method=import_csv')?>"  enctype="multipart/form-data">
    <div>
	     <input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>"/>
       <input type="hidden" name="full_path" value="<?=$settings['file']['full_path']?>"/>
       <input type="hidden" name="collection_id" value="<?=$settings['collection_id']?>"/>
    </div>
       <table class="mainTable" cellspacing="0" cellpadding="0">

        <thead>
            <tr>
                <th colspan="" style="width:150px;"><?=lang('header_from_file')?></th>
                <th colspan=""><?=lang('column')?></th>
                 <th colspan=""><?=lang('url_title')?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
           <?php

            $output = '';

              foreach ($settings['file']['headers'][0] as $key => $value)
              {

                $output .= '<tr><td>'.$value.'</td>';
                $output .= '<td>'.(($settings['file']['columns']) ?
                  form_dropdown('column_header[\''.$value.'\']', $settings['file']['columns'], null, 'class="import_select"') :
                  lang('new_column').'<input type="hidden" name="columns[]" value="'.$key.'"/>').'</td>';
                $output .= '<td><input type="checkbox" name="url_title['.$key.']" value="'.$key.'"/></td>';
                $output .= '</tr>';
              }
                print($output);
           ?>
            </tr>
        </tbody>


    </table>
     <input type="submit" class="submit" value="<?= lang('submit_file') ?>"  />

    <?=form_close()?>

</div>
<script type="text/javascript">
  $( document ).ready(function() {
    $(document).on("change", ".import_select", function() {
        var user_choose = $(this).find(":selected").val();
        if (user_choose == 'new_column') {
          $(this).after('<input type="text" value="" name="" placeholder="keep empty for autoname" class="fullfield input " style="margin-left:20px;width:200px; ">');
        } else {
           $(this).next('input').remove();
        }
    });
  });
</script>