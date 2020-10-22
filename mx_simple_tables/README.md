# MX Simple Tables EE#


### Installation
* Download the latest version of MX Simple Tables and extract the .zip to your desktop.
* Copy system/expressionengine/third_party/mx_cp_notice to system/expressionengine/third_party/


### Activation
* Log into your control panel
* Browse to Addons > Modules
* Enable all the MX Simple Tables components


### Code Example

    {exp:mx_simple_tables:get_entries collection="zip_codes" limit="100" search:column_name="kittens" dynamic_parameters="search:column_0" group_by="column_1,column_2" backspace=""}
        {mx:collection_id}
        {column_1}

        {if column_4 == ""}
            empty :(
        {/if}

        {if mx_no_results}
            no results
        {/if}
    {/exp:mx_simple_tables:get_entries}

### Search Form Example

    <form action="/home/post/" method="post">
        <input type="hidden" name="csrf_token" value="{csrf_token}">
        <input type="text" name="search:column_0" value="34007">
        <input type="submit" name="Search">
    </form>