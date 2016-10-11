# Creating a simple form

So let's get started with a very basic form. Let's say you want to create a sign-up form for a newsletter: one field for the email address, plus a submit button. Something like this, perhaps:

----------

<form method="get" action="/newsletter-form.php">
	<label for="email">Your email:</label>
	<input type="email" id="email" name="email" tabindex="1"/>
	<input type="submit" value="Submit"/>
</form>

----------

## The easy way: using arrays

There is in fact more than one way to create a form in ioForm, but we'll start with the easiest because that's the one you're most likely to use. Here's how we'd reproduce the form above:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'elements' => array(
			array(
				'label' => 'Your email:',
				'type' => 'Email',
				'name' => 'email',
				'id' => 'email',
			)
        )
    )
);
echo( $form->Render() );
```

The HTML that's produced looks like this:

```html
<form method="get" action="/newsletter-form.php">
	<label for="email">Your email:</label>
	<input type="email" id="email" name="email" tabindex="1"/>
	<input type="submit" value="Submit"/>
</form>
```

*Please note that, for the sake of clarity, I've manually formatted the HTML code in the examples in this documentation with line breaks and indentation. However, ioForm does not break up or indent the HTML code that it produces -- in fact it the HTML it produces will be written as a single, long string.*

### Breaking it down

Let's look more closely at what we've just done. First of all, we instantiate a new object which will contain the definition of our form:

```php
$form = (new \ioForm\Form())
```
*Note that the above type of object instantiation requires PHP 5.4*

...and then we call the ```FromArray()``` method:

```php
->FromArray(
```
This method allows us to pass in a form definition as an array. We'll see later than this isn't the only way to define our form, but it is the simplest and quickest.

#### The form

The array we're passing in contains pretty much the absolute minimum we need to produce a working form. First off, we have the `action`, which is the path that the form will submit to. Then we have an array called `elements`, which itself contains one or more arrays that define the fields that will make up the form (it is possible to define other element types than form fields, as we'll find later).

```php
    array(
        'action' => '/newsletter-form.php',
        'elements' => array(
			...
        )
    )
```
#### The field

Now we come to the field definition:

```php
array(
	'label' => 'Your email:',
	'type' => 'Email',
	'name' => 'email',
	'id' => 'email',
)
```

Each element's definition is an array of key/value pairs. At minimum for a form field, you'll need specify the `type` (i.e. what type of field is it?) and `name`. Field types always start with a capital letter, for example *Text*, *Radio*, *Select*. You can find a reference list of all field types [here](./fields-reference). 

Note that, as we will find out later, there are other element types available besides fields (for example, layout elements like Fieldset). Internally, a type is specified as its group followed by its type -- for example, *Field:Text*, *Layout:Fieldset*. Since fields are the most commonly used sort of element, to save typing it's assumed that if the group isn't specified (e.g. *Text*, *Radio*) then it's of the *Field:* group.

In this case, we've also specified a `label` -- which is the text that appears alongside the field -- and also an `id`, because this allows the label element to be linked to the field (using `for="..."`). This is good practice for accessibility, and it also means that clicking on the label's text focuses on the linked field.  

Of course most forms will have more than one field: to do this, you would just have more field definitions in the `elements` array. See below for an example of this.

#### Getting the HTML

Finally, we use the form's `Render()` method to output the HTML:

```php
echo( $form->Render() );
```
#### Setting the method

You'll notice that while we didn't specify it, the form's method has been set to `get` by default. You can change this (and should, for a form like this that sends a value that will be saved) by passing a 'method' parameter in the form's definition array:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post', // That's better
        'elements' => array(
			array(
				'label' => 'Your email:',
				'type' => 'Email',
				'name' => 'email',
				'id' => 'email',
			)
        )
    )
);
echo( $form->Render() );
```
gives

```html
<form method="post" action="/newsletter-form.php">
	<label for="email">Your email:</label>
	<input type="email" id="email" name="email" tabindex="1"/>
	<input type="submit" value="Submit"/>
</form>
```

### Adding more fields

Adding more fields to our form just means adding more field definitions to our `elements` array. So let's add a couple more fields to our form:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post', // That's better
        'elements' => array(
			array(
				'label' => 'Your email:',
				'type' => 'Email',
				'name' => 'email',
				'id' => 'email',
			),
			array(
				'label' => 'Your name:',
				'type' => 'Text',
				'name' => 'name',
				'id' => 'name',
			),
			array(
				'label' => 'Email type:',
				'type' => 'Radio',
				'name' => 'email_type',
				'id' => 'email_type',
				'options' => array(
					array( 'value' => 'html', 'text' => 'HTML' ),
					array( 'value' => 'text', 'text' => 'Text' )
				)
			)
        )
    )
);
echo( $form->Render() );
``` 
...gives this code:

```html
<form method="post" action="/newsletter-form.php">
	<label for="email">Your email:</label>
	<input type="email" id="email" name="email"/>
	<label for="name">Your name:</label>
	<input type="text" id="name" name="name"/>
	<label>Email type:</label>
		<input type="radio" value="html" id="email_type-html" name="email_type"/>
		<label for="email_type-html">HTML</label>
		<input type="radio" value="text" id="email_type-text" name="email_type"/>
		<label for="email_type-text">Text</label>
	<input type="submit" value="Submit"/>
</form>

```

...which looks like this:

----------

<form method="post" action="/newsletter-form.php"><label for="email">Your email:</label><input type="email" id="email" name="email"/><label for="name">Your name:</label><input type="text" id="name" name="name"/><label>Email type:</label><input type="radio" value="html" id="email_type-html" name="email_type"/><label for="email_type-html">HTML</label><input type="radio" value="text" id="email_type-text" name="email_type"/><label for="email_type-text">Text</label><input type="submit" value="Submit"/></form>

----------

Yes, the formatting is terrible. We'll deal with how to customise your form's layout, later.

## The hard way: using objects

There's another way to build a form, and while it's not as convenient as using an array, it does give an insight into what's going on behind the scenes.

Let's start with the form itself:

```php
$form = new \ioForm\Form();
$form->action = '/newsletter-form.php';
$form->method = 'post';
```
Now we create the email field:

```php
// Create the email field
$field = new \ioForm\Core\Definition();
$field->type = 'Email';
$field->name = 'email';
$field->label = 'Your email:';
$field->id = 'email';
```
...and add the field to the form:
```php
$form->AddElement( $field );
```
And finally we render the result:

```php
$form->Render();
```
The resulting output is exactly the same as our original array-based example above. In truth, when you define a form using an array, internally it converts that array into `Definition` objects -- so in fact the two approaches are ultimately achieving the same thing.  