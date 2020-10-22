<div id='calendar'>

    <form method="post" action="<?=$form_upload_url?>"  enctype="multipart/form-data">
    <div>
	   <input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>"/>
       <input type="hidden" name="collection_id" value="<?=$settings['collection_id']?>"/>
    </div>
       <table class="mainTable" cellspacing="0" cellpadding="0">

        <thead>
            <tr>
                <th colspan="2"><?=lang('upload_data')?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?=form_upload(array('id'=>'file_upload','name'=> 'userfile','size'=>15,'class'=>'field'))?></td>
                <td><input type="submit" class="submit" value="<?= lang('submit_file') ?>"  /></td>
            </tr>
        </tbody>
   	 
  	
    </table>


    <?=form_close()?>

</div>