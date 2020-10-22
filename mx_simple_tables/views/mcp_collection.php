<div>
	<form method="post" action="<?=$form_post_url?>">
	<div>
			<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>"/>
	</div>
  </form>
  <?php
    $columns = '';
    $colHeaders = '';
    $rowHeaders = '';
    $select_array = array();
    if (count($settings['entries']) > 0) {
      foreach (array_keys($settings['entries'][0]) as $key => $value)
      {
        $colHeaders .= '"'.trim($value).'",';
        $columns  .= '{data :"'.trim($value).'"'.( $value == 'entry_id' ? ", readOnly: true"  : '').'},';
        $select_array[trim($value)] = trim($value);
      }
    }

  ?>

  <div class="rightNav">
    <div style="float: left; width: 100%;">
        <span class="button"><a title="<?=lang('upload_data')?>" class="submit" href="<?=$base_url.AMP.'method=upload_data'.AMP.'collection_id='.$settings['collection_id']?>"><?=lang('upload_data')?></a></span>
     </div>

  </div>

  <div class="clear"></div>
  <?php if(count($settings['entries']) > 0): ?>
  <div style="background:#ecf1f4; padding:0px 0 20px;">

    <form method="post" action="<?=$form_collection_filter?>">
      <div>
        <input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>"/>
        <input type="hidden" name="filters" value="collection"/>
      </div>
      <ul class="search-table" style="margin-bottom: 20px;">

      </ul>
      <div class="clear"></div>
      <div class="tableSubmit" ><input type="submit" value="Search" class="submit"></div>
      <div class="clear"></div>
    </form>

  </div>
<?php endif; ?>

<?php if(count($settings['entries']) == 0): ?>
   <div class="" style="text-align: left;">
      No Results
   </div>
<?php endif; ?>

  <?php if(count($settings['entries'])>0) : ?>
  <div class="shun wide_content" >
    <div id="mx_simple_tables_id" class="handsontable">

    </div>
  </div>
    <?php
        $output     = '';
        $data       = '';
        foreach ($settings['entries'] as $index => $row)
        {
            $row_line = '';

            foreach ($row as $key => $value) {
                $row_line  .= $key . ' : "'.trim($value).'",';
            }
            $data   .=  '{'.rtrim($row_line,',').'},';
          }
    ?>

    <?php
      if($settings['pagination']) {
       echo $settings['pagination'];
      }
    ?>
    <div class="tableSubmit">
        <form method="post" action="<?=($form_collection_filter.(isset($offset) ? AMP.'rownum='.$offset : '' ))?>" id="mx_simple_table_form">
         <?php if(count($settings['entries']) > 0): ?>
          <input type="hidden" name="csrf_token" value="{csrf_token}">
          <input type="hidden" name="offset" value="<?=(isset($offset) ? $offset: 0 )?>">
          <textarea id="mx_table_save" name="sample_table_json" style="display: none"></textarea>
          <input type="submit" value="Save" class="submit" id="save_data">
         <?php endif; ?>
        </form>

    </div>
    <?php endif; ?>
</div>



<script type="text/javascript">
  var data = [<?=$data?>];
  var f_index = 0;
  var query, tpl;

  $(document).ready(function () {

      container = document.getElementById('mx_simple_tables_id'),
      settings1 = {
         data: data,
        minSpareRows: 0,
        colHeaders: true,
        contextMenu: false,
        manualColumnResize: true,
        colHeaders : [<?=$colHeaders?>],
        persistentState: true,
        fixedColumnsLeft:1,
        columns: [<?=rtrim($columns,',')?>],
        afterChange: function (change, source) {
          if (source === 'loadData') {
            return;
          }
          console.log(change.length);
          console.log(change);
        },
        afterColumnResize: function() {
        }
      },
      simple_table = new Handsontable(container,settings1);
      simple_table.render();

    $("#save_data").click(function(e){
      e.preventDefault();
      $("#mx_table_save").html(JSON.stringify(simple_table.getData())).parents("form:first").submit();
      console.log('save data');
    });



  $('.search-table').on('click','.search-minus', function (e) {
      $(this).parents("li:first").remove();
  })

  $('.search-table').on('click','.search-plus', function (e) {
     do_tpl();
  })

  function do_tpl() {
    tpl = $('script.search_field').clone();
     tpl = tpl.text().replace(/%f_index%/g, f_index);
     $('.search-table').append(tpl);
     f_index++;
  }

  do_tpl();

  });
</script>

<script type="text/template" class="search_field">
  <li>
    <?=form_dropdown('search[%f_index%][column]',$select_array)?>
          is
          <select name="search[%f_index%][condition]">
            <option value="0">equal</option>
            <option value="1">starts with</option>
            <option value="2">ends with</option>
            <option value="3">contain</option>
            <option value="4">does not contain</option>
          </select>
    <input type="text" name="search[%f_index%][text]" style="width:400px;"/>

    <div class="table-right">
      <a href="#" class="search-minus"></a>
      <a href="#" class="search-plus"></a>
    </div>
  </li>
</script>

