<div>
	<form method="post" action="<?=$form_post_url?>">
	<div>
			<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>"/>
	</div>
  </form>

   <table class="mainTable" cellspacing="0" cellpadding="0">
   		<colgroup>
			<col class="name" />
			<col class="short_name" />
			<col class="entries" />
			<col class="actions" />
		</colgroup>
		<thead>
			<tr>
				<th><?=lang('name')?></th>
				<th><?=lang('short_name')?></th>
				<th><?=lang('entries')?></th>
				<th><?=lang('actions')?></th>
			</tr>
		</thead>
        <tbody>
        <?php
            $output = '';

            if(count($settings['collections']) > 0)
           {

              foreach ($settings['collections'] as $key => $value)
              {
                $output .= '<tr>';
                $output .= '<td><a href="'.$base_url.AMP.'method=collection'.AMP.'collection_id=' . $key . '">'.$value['name'].'</a></td>';
                $output .= '<td>'.$value['short_name'].'</td>';
                $output .= '<td>'.$value['entries'].'</td>';
                $output .= '<td><a href="'.$base_url.AMP.'method=delete'.AMP.'collection_id=' . $key . '">delete</a></td>';
                $output .= '</tr>';
              }

            } else {
                $output .= '<tr><td colspan="4" style="text-align:center">'.lang('no_collections').'</td></tr>';

            }

            print($output);
        ?>
        </tbody>
    </table>

</div>