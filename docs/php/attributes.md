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



