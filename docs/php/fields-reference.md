# Field types reference

Below is a list of all field types supported by ioForm. For convenience, example definitions are given as arrays.

## text

A text input field.

Example:
```php
array(
	'type' => 'text',
	'name' => 'my_text',
	'label' => 'My text',
	'id' => 'field_my_text',
)
```
Result:

```html
<label for="my_text">My text:</label><input type="text" id="my_text" name="my_text"/>
```

----------

<label for="my_text">My text:</label><input type="text" id="my_text" name="my_text"/>

----------

## checkbox

A checkbox field.

Example:
```php
array(
	'type' => 'checkbox',
	'name' => 'my_checkbox',
	'label' => 'My checkbox',
	'id' => 'field_my_checkbox',
)
```

Result:

```html
<label for="field_my_checkbox">My checkbox</label><input type="checkbox" id="field_my_checkbox" name="my_checkbox"/>
```
----------

<label for="field_my_checkbox">My checkbox</label><input type="checkbox" id="field_my_checkbox" name="my_checkbox"/>

----------


## radio

A set of radio buttons. Options are specified as an array, `options`, with each option represented as an array with `value` and `text` fields.

Example:
```php
array(
	'type' => 'radio',
	'name' => 'my_radio_buttons',
	'label' => 'My radio buttons',
	'options' => array(
		array( 'value' => 1, 'text' => 'First' ),
		array( 'value' => 2, 'text' => 'Second' ),
		array( 'value' => 3, 'text' => 'Third' )
	)
)
```
Result:

```html
<label>My radio buttons</label>
	<input type="radio" value="1" id="my_radio_buttons-1" name="my_radio_buttons"/>
	<label for="my_radio_buttons-1">First</label>
	<input type="radio" value="2" id="my_radio_buttons-2" name="my_radio_buttons"/>
	<label for="my_radio_buttons-2">Second</label>
	<input type="radio" value="3" id="my_radio_buttons-3" name="my_radio_buttons"/>
	<label for="my_radio_buttons-3">Third</label>
```

----------

<label>My radio buttons</label><input type="radio" value="1" id="my_radio_buttons-1" name="my_radio_buttons" tabindex="3"/><label for="my_radio_buttons-1">First</label><input type="radio" value="2" id="my_radio_buttons-2" name="my_radio_buttons" tabindex="4"/><label for="my_radio_buttons-2">Second</label><input type="radio" value="3" id="my_radio_buttons-3" name="my_radio_buttons" tabindex="5"/><label for="my_radio_buttons-3">Third</label>

----------


## select

A select field (drop-down). As with radio fields options are specified as an array, `options`, with each option represented as an array with `value` and `text` fields.


Example:
```php
array(
	'type' => 'select',
	'name' => 'my_select_field',
	'label' => 'My select',
	'options' => array(
		array( 'value' => 1, 'text' => 'First' ),
		array( 'value' => 2, 'text' => 'Second' ),
		array( 'value' => 3, 'text' => 'Third' )
	)
)
```
Result:

```html
<label for="my_select_field">My select</label>
<select name="my_select_field" id="my_select_field">
	<option value="1">First</option>
	<option value="2">Second</option>
	<option value="3">Third</option>
</select>
```

----------

<label for="my_select_field">My select</label><select name="my_select_field" id="my_select_field"><option value="1">First</option><option value="2">Second</option><option value="3">Third</option></select>

----------

## select_multiple

A multiple select field. See the `select` field type.

Example:
```php
array(
	'type' => 'select_multiple',
	'name' => 'my_select_field',
	'label' => 'My select',
	'options' => array(
		array( 'value' => 1, 'text' => 'First' ),
		array( 'value' => 2, 'text' => 'Second' ),
		array( 'value' => 3, 'text' => 'Third' )
	)
)
```
Result:
```html
<label for="my_select_field">My multiple select</label>
<select multiple="multiple" id="my_select_field" name="my_select_field">
	<option value="1">First</option>
	<option value="2">Second</option>
	<option value="3">Third</option>
</select>
```


----------

<label for="my_select_field">My multiple select</label><select multiple="multiple" id="my_select_field" name="my_select_field"><option value="1">First</option><option value="2">Second</option><option value="3">Third</option></select>

----------

## file

A file upload field. Note that if your form includes a file element, ioForm will automatically set the form's `method` to *post* and its `enctype` to *multipart/form-data*.  

Example:
```php
array(
	'type' => 'file',
	'name' => 'my_file_field',
	'label' => 'My file upload field'
)
```
Result:
```html
<label for="my_file_field">My file upload field</label>
<input type="file" id="my_file_field" name="my_file_field"/>
```
----------

<label for="my_file_field">My file upload field</label><input type="file" id="my_file_field" name="my_file_field"/>

----------
# HTML5 elements

These are elements implemented in HTML5 only. Note that if you're intending to support older browsers (e.g. IE9) then you'll need to either avoid using these elements, or find a suitable polyfill.

## email

An email input field. HTML5 only. Will automatically validate input unless `novalidate` is specified.

Example:
```php
array(
	'type' => 'email',
	'name' => 'your_email',
	'label' => 'Enter your email'
)
```

```html
<label for="your_email" class="email">Enter your email</label>
<input type="email" id="your_email" name="your_email"/>
```

## date

A date input field. HTML5 only. 

**Beware** when using `date` fields: behaviour varies widely across browsers. All mobile browsers (that I'm aware of) have pretty pop-up datepickers. But while some desktop browsers do implement a datepicker, others don't. And (as of the time of writing) they're all pretty ugly. You should strongly consider using some kind of prettified datepicker for desktop browsers (but don't ask me which one...).   

Example:
```php
array(
	'label' => 'Enter date:',
	'type' => 'date',
	'name' => 'my_date'
)
```

```html
<label for="my_date" class="date">Enter date:</label>
<input type="date" id="my_date" name="my_date"/>
```