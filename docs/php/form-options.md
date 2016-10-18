# Form options

|Option|Format|Default||
|-|-|-|-|
|`action`|string|*None*|The path that the form submits to. If you don't specify an action, the form will submit to the current page|
|`method`|string|get|Form submit method. Usually *get* or *post*|
|`enctype`|string|*None*|Form encoding type. Generally this isn't specified (it will default to *application/x-www-form-urlencoded*) but when the form includes one or more file input elements, it must be set to *multipart/form-data*. ioForm will do this for you automatically|
|`id`|string|*None*|ID attribute of the form|
|`class`|string|*None*|Additional class(es) to apply to the the form|
|`auto_field_id`|boolean|false|Automatically set the `id` attribute of any field element to match its `name` attribute (unless the field's ID is already provided by its definition). Will prepend the field's ID with the form's ID, if set.|
|`auto_tabindex`|boolean|false|Automatically add `tabindex` attribute to all fields, to allow easy tabbing between fields|
|`tabindex_start`|number|1|Start index for auto tabindex. When you have more than one form in a page, you might want to number the forms sequentially|
