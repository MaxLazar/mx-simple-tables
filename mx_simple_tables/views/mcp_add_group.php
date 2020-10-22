<div>
	<form method="post" action="<?=$form_post_url?>">
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>"/>

    <table class="mainTable" cellspacing="0" cellpadding="0">
        <thead>
            <tr >
                <th colspan="2"><?=lang('collection_settings')?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>New name</td>
                <td>
                    <input type="text" name="mx_simple_tables[new_name]" id="" placeholder="" value="<?=$settings['new_name']?>"/>
                </td>
            </tr>
            <tr>
                <td>Short name</td>
                <td>
                    <input type="text" name="mx_simple_tables[new_short_name]" id="" placeholder="" value="<?=$settings['new_short_name']?>"/>
                </td>
            </tr>
        </tbody>
    </table>

    <input type="submit" class="submit" name="submit" value="Save" />
    </form>
</div>