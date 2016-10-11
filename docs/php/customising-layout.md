# Customising form layout

The basic example form we created gives you an idea of how to build a very simple form, but of course few forms are ever really that simple. Having the fields listed one after another gives very little scope for creating an attractive form, or for breaking up a complex form into logical sections.

ioForm gives you complete control of the layout of your form, through a combination of simple but flexible templates and the use of structural elements.

## Structural elements

So far, we've only added field elements to our form. But there's nothing stopping you from creating a more complex layout, through the use of structural elements.

While you can use pretty much any markup you like within a form, if you're looking to follow best practice then in most cases, to break up a form you would use the `<fieldset>` element. You might remember from when it was briefly mentioned before, all elements are represented internally by their group and their type. So a text field's type is represented as *Field:Text*, for example, but you don't need to enter the group part ('Field:') because ioForm assumes that when the group part is missing, it's a field. 

In the case of layout elements, you have to specify the group. So for a fieldset, you need to enter *'Layout:Fieldset'*.  

Just like the main `<form>` itself, a fieldset can accept a list of elements.

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post',
        'elements' => array(
			array(
				'type' => 'Layout:Fieldset', // Instead of a 'Field', this is a 'Layout' element
				'legend' => 'Details', // The title of our fieldset
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
					)
				)
			),
			array(
				'type' => 'Layout:Fieldset',
				'elements' => array(
					array(
						'label' => 'Email type:',
						'type' => 'Radio',
						'name' => 'email_type',
						'id' => 'email_type',
						'options' => array(
							array( 'value' => 'html', 'text' => 'HTML' ),
							array( 'value' => 'text', 'text' => 'Text' )
						)
					),
					array(
						'label' => 'I agree to the terms and conditions:',
						'type' => 'Checkbox',
						'name' => 'agree_terms',
						'id' => 'agree_terms'
					)									
				)
			)
        ),
		'templates' => array(
			'default' => '<div class="form-row"><label></label><elements/></div>' 
		)
    )
);
```
```html
<form method="post" action="/newsletter-form.php">
	<fieldset>
		<legend>Details</legend>
		<div class="form-row">
			<label for="email">Your email:</label>
			<input type="email" id="email" name="email"/>
		</div>
		<div class="form-row">
			<label for="name">Your name:</label>
			<input type="text" id="name" name="name"/>
		</div>
	</fieldset>
	<fieldset>
		<div class="form-row">
			<label>Email type:</label>
			<input type="radio" value="html" id="email_type-html" name="email_type"/>
			<label for="email_type-html">HTML</label>
			<input type="radio" value="text" id="email_type-text" name="email_type"/>
			<label for="email_type-text">Text</label>
		</div>
		<div class="form-row">
			<label for="agree_terms">I agree to the terms and conditions:</label>
			<input type="checkbox" id="agree_terms" name="agree_terms"/>
		</div>
	</fieldset>
	<input type="submit" value="Submit"/>
</form> 
```

----------

<form method="post" action="/newsletter-form.php"><fieldset><legend>Details</legend><div class="form-row"><label for="email">Your email:</label><input type="email" id="email" name="email"/></div><div class="form-row"><label for="name">Your name:</label><input type="text" id="name" name="name"/></div></fieldset><fieldset><div class="form-row"><label>Email type:</label><input type="radio" value="html" id="email_type-html" name="email_type"/><label for="email_type-html">HTML</label><input type="radio" value="text" id="email_type-text" name="email_type"/><label for="email_type-text">Text</label></div><div class="form-row"><label for="agree_terms">I agree to the terms and conditions:</label><input type="checkbox" id="agree_terms" name="agree_terms"/></div></fieldset><input type="submit" value="Submit"/></form> 

----------



Note that the `<fieldset>` can have a title, which is rendered as a `<legend>` element. Although in this definiton we've used `'legend'`, for simplicity/consistency you may also use `'label'` -- it will still render a `<legend>` tag.

Of course `Fieldset` isn't always going to be appropriate, but that's OK because you can pass any HTML element type and it will render accordingly. So for example, you could just as easily specify `'Layout:Div'` or `'Layout:Section'` or whatever you need.

## Templates

Structural elements are all very well, but if you're looking to add consistent markup to lots of fields, it would quickly become very laborious indeed. Luckily, ioForm offers a template system that makes it very easy to wrap custom markup around field elements. 

It's might not be obvious from the example code you've seen so far, but when it renders a form ioForm automatically wraps each field element in a 'container template'. The default container template looks like this:

```html
<label></label><elements/>
```
...as you can see, not much going on there. Just a label element, followed by a special `<elements/>` marker that won't be rendered -- it's replaced by the field element. The label text is set automatically by ioForm.

So far, so dull.

But let's say we want to wrap all form fields in a `<div>`, so that we can use CSS to spruce up the layout. In our form definition, we can replace the default template string with whatever markup we want, making sure to include the `<elements/>` marker:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post',
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
        ),
		'templates' => array(
			// This is now our default field container
			'default' => '<div class="form-row"><label></label><elements/></div>' 
		)
    )
);
``` 

The *default* template is used (by...uh...default) by all fields (except radio button elements -- we'll explore that, later). So with the above code in place, our example form now renders like this:

```html
<form method="post" action="/newsletter-form.php">
	<div class="form-row">
		<label for="email">Your email:</label>
		<input type="email" id="email" name="email"/>
	</div>
	<div class="form-row">
		<label for="name">Your name:</label>
		<input type="text" id="name" name="name"/>
	</div>
	<div class="form-row">
		<label>Email type:</label>
			<input type="radio" value="html" id="email_type-html" name="email_type"/>
			<label for="email_type-html">HTML</label>
			<input type="radio" value="text" id="email_type-text" name="email_type"/>
			<label for="email_type-text">Text</label>
	</div>
	<input type="submit" value="Submit"/>
</form>
```

...and looks like this:

----------

<form method="post" action="/newsletter-form.php"><div class="form-row"><label for="email">Your email:</label><input type="email" id="email" name="email"/></div><div class="form-row"><label for="name">Your name:</label><input type="text" id="name" name="name"/></div><div class="form-row"><label>Email type:</label><input type="radio" value="html" id="email_type-html" name="email_type"/><label for="email_type-html">HTML</label><input type="radio" value="text" id="email_type-text" name="email_type"/><label for="email_type-text">Text</label></div><input type="submit" value="Submit"/></form> 

----------

...OK it's still not going to win any design awards, but you get the idea. 

You can do pretty much whatever you want in a template, as long as you include the `<elements/>` marker. So for example, with this form we have a field that requires a textual prefix/suffix:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/product-form.php',
        'method' => 'post',
        'elements' => array(
			array(
				'label' => 'Enter product name:',
				'type' => 'Text',
				'name' => 'name',
				'id' => 'name',
			),
			array(
				'label' => 'Enter price:',
				'type' => 'Number',
				'name' => 'price',
				'id' => 'price',
				'container_template' => 'price' // Use the new 'price' template for this field
			)
        ),
		'templates' => array(
			'default' => '<div class="form-row"><label></label><elements/></div>',
			// Our special template for price fields
			'price' => '<div class="form-row"><label></label> $<elements/> + TAX</div>' 
		)
    )
);
``` 

Renders as

```html
<form method="post" action="/product-form.php">
	<div class="form-row">
		<label for="name">Enter product name:</label><input type="text" id="name" name="name"/>
	</div>
	<div class="form-row">
		<label for="price">Enter price:</label> 
		$<input type="number" id="price" name="price"/> + TAX
	</div>
	<input type="submit" value="Submit"/>
</form> 
```


----------

<form method="post" action="/product-form.php"><div class="form-row"><label for="name">Enter product name:</label><input type="text" id="name" name="name"/></div><div class="form-row"><label for="price">Enter price:</label> $<input type="number" id="price" name="price"/> + TAX</div><input type="submit" value="Submit"/></form> 

----------

