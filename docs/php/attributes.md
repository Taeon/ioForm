# ID, classes, data and other attributes

## Setting an element's ID

It's very simple to set any element's ID attribute. In fact we've already seen it in action, in our first example form:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'elements' => array(
			array(
				'label' => 'Your email:',
				'type' => 'email',
				'name' => 'email',
				'id' => 'email', // Here it is
			)
        )
    )
);
```

...and you can set the ID for any element the same way (including the form itself, or any structural elements). As noted before, it's good practice to give a field an ID because then you can link the field's label to the field element -- not just good for usability, but also for accessibility. 

But ioForm makes things even easier: if you want, it will assign an ID for all fields for you automatically.

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
		'auto_field_id' => true, // Assign all fields an ID automatically
        'elements' => array(
			array(
				'label' => 'Your email:',
				'type' => 'email',
				'name' => 'email'
			)
        )
    )
);
```
...this will assign an ID to each field, based on its `name` attribute. In this case, it will produce the exact same code as the original example. 

<p data-height="265" data-theme-id="dark" data-slug-hash="yaxVAE" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/yaxVAE/">ioForm: ID, classes, data and other attributes - setting a field's ID</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>

However, this isn't always a good idea: in any given HTML page, an element's ID should be unique. What if you have two forms, each of which has an 'email' field? Simple: just assign each of the two forms a different ID, and ioForm will prefix field IDs with the ID of the form:


```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
		'id' => 'newsletter-form',
		'auto_field_id' => true, // Assign all fields an ID automatically
        'elements' => array(
			array(
				'label' => 'Your email:',
				'type' => 'email',
				'name' => 'email'
			)
        )
    )
);
```
<p data-height="265" data-theme-id="dark" data-slug-hash="ORobqp" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/ORobqp/">ioForm: ID, classes, data and other attributes - auto field ID with form ID prefix</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>

At any time, you can override a given field's automatically-generated ID with something else, if you need to: 

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
		'id' => 'newsletter-form',
		'auto_field_id' => true,
        'elements' => array(
			array(
				'label' => 'Your name:',
				'type' => 'text',
				'name' => 'name',
				'id' => 'my-name-field', // Set this field's ID explicitly
			),
			array(
				'label' => 'Your email:',
				'type' => 'email',
				'name' => 'email'
			)
        )
    )
);
```

<p data-height="265" data-theme-id="dark" data-slug-hash="pEONmK" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/pEONmK/">ioForm: ID, classes, data and other attributes - overriding automatic ID</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>

## Setting an element's CSS classes

Adding a class to any element is simple -- just set the class: 

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
		'id' => 'newsletter-form',
        'elements' => array(
			array(
				'label' => 'Your name:',
				'type' => 'text',
				'name' => 'name',
				'class' => 'my-custom-class', // Easy as that
			),
			array(
				'label' => 'Your email:',
				'type' => 'email',
				'name' => 'email'
			)
        )
    )
);
```

<p data-height="265" data-theme-id="dark" data-slug-hash="ORBoXE" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/ORBoXE/">ioForm: ID, classes, data and other attributes - Setting an element's CSS classes</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>

You can do this for fields, layout elements, or even the form itself.

But let's say you want to apply a custom CSS class to an element that's part of a container template. You can't access these elements directly, but you can assign classes to them using the `'classes'` directive.

This directive takes an array of arrays, each with two fields: `element` and `class`. The `element` is an identifier for the element you want to apply the class to. 

Let's say you have a template that looks like this:

```html
<div>
	<label></label>
	<elements/>
</div>
```

ioForm automatically refers the 'root' element(s) of a template as the *container* (usually you would only have a single root element -- in the above example that would be the `<div>` element -- but if there's more than one then they're all referred to as the *container*). So if we wanted to apply a class to the `<div>` in this example, we'd use the following syntax:



```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'id' => 'newsletter-form',
        'elements' => array(
            array(
                'label' => 'Your name:',
                'type' => 'text',
                'name' => 'name',
                'classes' => array(
					array( 
						'element' => 'container', // Apply classes to the 'container' 
						'class' => 'my-custom-class' // The class(es) to apply
					) 
				)
            ),
            array(
                'label' => 'Your email:',
                'type' => 'email',
                'name' => 'email'
            )
        ),
		'templates' => array(
			'default' => '<div><label></label><elements/></div>'
		)
    )
);
```

<p data-height="265" data-theme-id="dark" data-slug-hash="amRaWm" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/amRaWm/">ioForm: ID, classes, data and other attributes - custom CSS in templates</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>