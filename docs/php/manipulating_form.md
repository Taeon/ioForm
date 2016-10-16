# Manipulating a form

So far we've seen how to create a form from scratch. But what if you want to take an existing form and change it? You *could* just make a copy of the form definition and make the necessary changes. But you might find yourself wanting to create a single form definition, and then add or remove fields -- or entire sections -- without having to create a whole new form. 

Fortunately, ioForm provides a full set of functions to allow you to do this.

# Showing/hiding a field element

Let's say we have a simple form with a few fields in it:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post',
        'elements' => array(
			array(
				'type' => 'layout:fieldset',
				'label' => 'Details',
				'elements' => array(
					array(
						'label' => 'Your name:',
						'type' => 'text',
						'name' => 'name',
						'id' => 'name',
					),
					array(
						'label' => 'Your address:',
						'type' => 'textarea',
						'name' => 'address'
					),
					array(
						'label' => 'Your shoe size:',
						'type' => 'number',
						'name' => 'shoe_size',
						'id' => 'shoe_size'
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
...we want to use the whole form in some places, but in others the 'shoe size' field isn't appropriate. No problem, just disable the field:

```php
$form->GetField( 'shoe_size' )->Disable();
```

Conversely, you could have a field that doesn't appear by default:
  
```php
array(
	'label' => 'Your shoe size:',
	'type' => 'number',
	'name' => 'shoe_size',
	'id' => 'shoe_size',
	'enabled' => false // Does not appear by default
)	
```

..and then show it when needed:

```php
$form->GetField( 'shoe_size' )->Enable();
```

## Maniuplating a non-field element

That's fine if we want to change a field's state, but what about other types of element? They work the same way (so you can use `Enable()` and `Disable()`, as well as the other methods we'll discuss later), but the difference lies in how you access them.

To be able to fetch a non-field element from a form, that element needs an `alias`. So for example, here we have two groups of elements, and we want to be able to be able to change the second group. So we assign it an alias -- in this case, `'clothing'`:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post',
        'elements' => array(
			array(
				'type' => 'layout:fieldset',
				'label' => 'Details',
				'elements' => array(
					array(
						'label' => 'Your name:',
						'type' => 'text',
						'name' => 'name',
						'id' => 'name',
					),
					array(
						'label' => 'Your address:',
						'type' => 'textarea',
						'name' => 'address'
					)
				)
			),
			array(
				'type' => 'layout:fieldset',
				'label' => 'Clothing',
				'alias' => 'clothing', // We use this to refer to this element
				'elements' => array(
					array(
						'label' => 'Your shoe size:',
						'type' => 'number',
						'name' => 'shoe_size',
						'id' => 'shoe_size'
					),
					array(
						'label' => 'Your hat size:',
						'type' => 'number',
						'name' => 'hat_size',
						'id' => 'hat_size'
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

...and now we can, for example, hide the `'clothing'` group with:

```php
$form->GetElementByAlias( 'clothing' )->Disable();
```

## Adding new elements

So we can show and hide existing elements, but what about adding completely new ones? As it happens, this is also quite simple.

First, we create a new definition -- in pretty much the same way as we created the original form:

```php
$field = (new \ioForm\Core\Definition())->FromArray(
	array(
		'type' => 'text',
		'name' => 'chest_size',
		'id' => 'chest_size',
		'label' => 'Chest measurement'
	)
);
```  
...and then we insert it wherever we want it in the form. For example, let's just add it to the `'clothing'` group from the previous example:

```php
$form->GetElementByAlias( 'clothing' )->Append( $field );
```
Simple. But what if we wanted to insert it between the `'shoe_size'` and `'hat_size'` fields? Also simple:

```php
$form->GetField( 'shoe_size' )->After( $field );
```

There are also `Prepend()` and `Before()` methods, whose functions you can probably guess. There's even a `ReplaceWith()` method -- so you can replace an existing element with something new.

## Creating new structures

Bear in mind that you can create any structure you like with this approach -- you don't have to limit yourself to creating a single element at a time. So we could create a whole new section:

```php
$colours_section = (new \ioForm\Core\Definition())->FromArray(
	array(
		'type' => 'layout:fieldset',
		'label' => 'Favourite colours',
		'elements' => array(
			array(
				'type' => 'select',
				'name' => 'colour',
				'id' => 'colour',
				'label' => "What's your favourite colour?",
				'options' => array(
					array( 'value' => '', 'text' => 'Select one' ),
					array( 'value' => 'red', 'text' => 'Red' ),
					array( 'value' => 'green', 'text' => 'Green' ),
					array( 'value' => 'blue', 'text' => 'Blue' )
				)
			),
			array(
				'type' => 'select',
				'name' => 'shade',
				'id' => 'shade',
				'label' => "What's your favourite shade?",
				'options' => array(
					array( 'value' => '', 'text' => 'Select one' ),
					array( 'value' => 'light', 'text' => 'Light' ),
					array( 'value' => 'dark', 'text' => 'Dark' ),
				)
			)
		)
	)
);
		  
$form->GetElementByAlias( 'clothing' )->After( $colours_section );
```
<p data-height="265" data-theme-id="light" data-slug-hash="BLPApx" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" data-preview="true" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/BLPApx/">ioForm: Adding a new fieldset to a form</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>