# Form options

|Option|Format|Default||
|-|-|-|-|
|`action`|string|*None*|The path that the form submits to. If you don't specify an action, the form will submit to the current page|
|`method`|string|get|Form submit method. Usually *get* or *post*|
|`enctype`|string|*None*|Form encoding type. Generally this isn't specified (it will default to XXXXXXXXXX) but when the form includes one or more file input elements, it must be set to *multipart/form-data*. ioForm will do this for you automatically|
|`auto_tabindex`|boolean|false|Automatically add `tabindex` attribute to all fields, to allow easy tabbing between fields|
|`tabindex_start`|number|1|Start index for auto tabindex. When you have more than one form in a page, you might want to number the forms sequentially|
