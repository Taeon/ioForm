# Field types reference

Below is a list of all field types supported by ioForm. For convenience, example definitions are given as arrays.

# Standard elements

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

## textarea

Box for entering multiline text.

```php
array(
	'label' => 'Enter some text',
	'type' => 'textarea',
	'name' => 'my_textarea'
)
```

Result:
```html
<label for="my_textarea" class="textarea">Enter some text</label>
<textarea rows="2" cols="20" id="my_textarea" name="my_textarea"></textarea>
```

----------

<label for="my_textarea" class="textarea">Enter some text</label>
<textarea rows="2" cols="20" id="my_textarea" name="my_textarea"></textarea>

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

## password

A password field.

Example:
```php
array(
	'label' => 'Enter password:',
	'type' => 'password',
	'name' => 'my_password'
)
```
Result:
```html
    <label for="my_password" class="password">Enter password:</label>
    <input type="password" id="my_password" name="my_password"/>
```
----------

<label for="my_password" class="password">Enter password:</label>
<input type="password" id="my_password" name="my_password"/>

----------

## hidden

A hidden field. Since it's not visible, it needs no label.

```php
array(
	'type' => 'hidden',
	'name' => 'my_hidden_value'
)
```
```html
 <input type="hidden" name="my_hidden_value"/>
```

----------

# HTML5 elements

These are elements implemented in HTML5 only. Note that if you're intending to support older browsers (e.g. IE9) then you'll need to either avoid using these elements, or find a suitable polyfill.

## email

An email input field. Will automatically validate input unless `novalidate` is specified.

Example:
```php
array(
	'type' => 'email',
	'name' => 'your_email',
	'label' => 'Enter your email'
)
```

Result:
```html
<label for="your_email">Enter your email</label>
<input type="email" id="your_email" name="your_email"/>
```
----------

<label for="your_email">Enter your email</label>
<input type="email" id="your_email" name="your_email"/>

----------

## number

A number field. Will automatically validate unless `novalidate` is specified.

Example:
```php
array(
	'label' => 'Enter number:',
	'type' => 'number',
	'name' => 'my_number'
)
```
Result:
```html
    <label for="my_number">Enter number:</label>
    <input type="number" id="my_number" name="my_number"/>
    <input type="submit" value="Submit"/>
```

----------

<label for="my_number">Enter number:</label>
<input type="number" id="my_number" name="my_number"/>

----------

## date

A date input field.

**Beware** when using `date` fields: behaviour varies widely across browsers. All mobile browsers (that I'm aware of) have pretty pop-up datepickers. But while some desktop browsers do implement a datepicker, others don't. And (as of the time of writing) they're all pretty ugly. You should strongly consider using some kind of prettified datepicker for desktop browsers (but don't ask me which one...).   

Example:
```php
array(
	'label' => 'Enter date:',
	'type' => 'date',
	'name' => 'my_date'
)
```

Result:
```html
<label for="my_date">Enter date:</label>
<input type="date" id="my_date" name="my_date"/>
```
----------

<label for="my_date">Enter date:</label>
<input type="date" id="my_date" name="my_date"/>

----------

## datetime_local

A field for entering date and time. As with the `date` field, use with caution as behaviour varies widely across browsers.

Example:
```php
array(
	'label' => 'Enter date/time:',
	'type' => 'datetime_local',
	'name' => 'my_datetime'
)
```

Result:
```html
<label for="my_datetime">Enter date/time:</label>
<input type="datetime-local" id="my_datetime" name="my_datetime"/>
```
----------

<label for="my_datetime">Enter date/time:</label>
<input type="datetime-local" id="my_datetime" name="my_datetime"/>

## month

A field for entering a month. Beware of variations in browser behaviour.

Example:
```php
array(
	'label' => 'Enter month:',
	'type' => 'month',
	'name' => 'my_month'
)
```

Result:
```html
<label for="my_month">Enter month:</label>
<input type="month" id="my_month" name="my_month"/>
```
----------

<label for="my_month">Enter month:</label>
<input type="month" id="my_month" name="my_month"/>

## week

A field for entering a week. Beware of variations in browser behaviour.

Example:
```php
array(
	'label' => 'Enter week:',
	'type' => 'week',
	'name' => 'my_week'
)
```

Result:
```html
<label for="my_week">Enter week:</label>
<input type="week" id="my_week" name="my_week"/>
```
----------

<label for="my_week">Enter week:</label>
<input type="week" id="my_week" name="my_week"/>

## time

A field for entering a time value. Beware of variations in browser behaviour.

Example:
```php
array(
	'label' => 'Enter time:',
	'type' => 'time',
	'name' => 'my_time'
)
```

Result:
```html
<label for="my_time">Enter time:</label>
<input type="time" id="my_time" name="my_time"/>
```
----------

<label for="my_time">Enter time:</label>
<input type="time" id="my_time" name="my_time"/>

## url

A field for entering a URL.

Example:
```php
array(
	'label' => 'Enter URL:',
	'type' => 'url',
	'name' => 'my_url'
)
```

Result:
```html
<label for="my_url">Enter URL:</label>
<input type="url" id="my_url" name="my_url"/>
```
----------

<label for="my_url">Enter URL:</label>
<input type="url" id="my_url" name="my_url"/>

## telephone

A field for entering a phone number. On mobile phones, this will (usually) open a numeric keypad.

Example:
```php
array(
	'label' => 'Enter phone number:',
	'type' => 'phone',
	'name' => 'my_phone'
)
```

Result:
```html
<label for="my_phone">Enter phone number:</label>
<input type="tel" id="my_phone" name="my_phone"/>
```
----------

<label for="my_phone" class="telephone">Enter phone number:</label>
<input type="tel" id="my_phone" name="my_phone"/>

## range

A field for selecting a value from a range (using a slider). Attributes `min`, `max` and `step` are all optional.

Example:
```php
array(
	'label' => 'Choose value:',
	'type' => 'range',
	'name' => 'my_range_value',
	'min' => 10, // Minimum value
	'max' => 20, // Maximum value
	'step' => 2 // Interval between values
)
```

Result:
```html
<label for="my_range_value">Choose value:</label>
<input type="range" id="my_range_value" name="my_range_value" min="10" max="20" step="2"/>
```
----------

<label for="my_range_value">Choose value:</label>
<input type="range" id="my_range_value" name="my_range_value" min="10" max="20" step="2"/>

## search

A field for entering a search query. From what I can tell, the effect of this is that when you use an on-screen keyboard (e.g. on a mobile phone or tablet) it will display a slightly different 'enter' key.

Example:
```php
array(
	'label' => 'Enter query:',
	'type' => 'search',
	'name' => 'my_search_query'
)
```

Result:
```html
<label for="my_search_query">Enter query:</label>
<input type="search" id="my_search_query" name="my_search_query"/>
```
----------

<label for="my_search_query">Enter query:</label>
<input type="search" id="my_search_query" name="my_search_query"/>

## color

A field for selecting a colour. Beware variations in browser behaviour.

Example:
```php
array(
	'label' => 'Select colour:',
	'type' => 'color',
	'name' => 'my_color'
)
```

Result:
```html
<label for="my_color">Select colour:</label>
<input type="color" id="my_color" name="my_color"/>
```
----------

<label for="my_color">Select colour:</label>
<input type="color" id="my_color" name="my_color"/>
