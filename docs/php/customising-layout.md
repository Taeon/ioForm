# Customising form layout

The basic example form we created gives you an idea of how to build a very simple form, but of course few forms are ever really that simple. Having the fields listed one after another gives very little scope for creating an attractive form, or for breaking up a complex form into logical sections.

ioForm gives you complete control of the layout of your form, through a combination of simple but flexible templates and the use of structural elements.

## Structural elements

So far, we've only added field elements to our form. But there's nothing stopping you from creating a more complex layout, through the use of structural elements.

While you can use pretty much any markup you like within a form, if you're looking to follow best practice then in most cases, to break up a form you would use the `<fieldset>` element. In the case of layout elements, you just have to tell ioForm that it's a layout type of element, rather than a field.

So for a fieldset, you need to enter *'layout:fieldset'*.  

Just like the main `<form>` itself, a fieldset can accept a list of elements.

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post',
        'elements' => array(
			array(
				'type' => 'layout:fieldset', // Instead of a 'field', this is a 'layout' element
				'legend' => 'Details', // The title of our fieldset
				'elements' => array(
					array(
						'label' => 'Your email:',
						'type' => 'email',
						'name' => 'email',
						'id' => 'email',
					),
					array(
						'label' => 'Your name:',
						'type' => 'text',
						'name' => 'name',
						'id' => 'name',
					)
				)
			),
			array(
				'type' => 'layout:div',
				'legend' => 'Options',
				'elements' => array(
					array(
						'label' => 'Email type:',
						'type' => 'radio',
						'name' => 'email_type',
						'id' => 'email_type',
						'options' => array(
							array( 'value' => 'html', 'text' => 'HTML' ),
							array( 'value' => 'text', 'text' => 'Text' )
						)
					),
					array(
						'label' => 'I agree to the terms and conditions:',
						'type' => 'checkbox',
						'name' => 'agree_terms',
						'id' => 'agree_terms'
					)									
				)
			)
        )
    )
);
```
<p data-height="265" data-theme-id="dark" data-slug-hash="JRBQBR" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/JRBQBR/">ioForm: Customising form layout - structural elements</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>

Note that the `<fieldset>` can have a title, which is rendered as a `<legend>` element. Although in this definiton we've used `'legend'`, for simplicity/consistency you may also use `'label'` -- it will still render a `<legend>` tag.

Of course `fieldset` isn't always going to be appropriate, but that's OK because you can pass any HTML element type and it will render accordingly. So for example, you could just as easily specify `'layout:div'` or `'layout:section'` or whatever you need. ioForm doesn't validate this: if you entered `'layout:made_up_element'` it would render as

```html
<made_up_element>...</made_up_element>
```
And of course, you can nest as deep as you like. So you might have:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'method' => 'post',
        'elements' => array(
			array(
				'type' => 'layout:fieldset',
				'legend' => 'Details',
				'elements' => array(
					array(
						'label' => 'Your email:',
						'type' => 'email',
						'name' => 'email',
						'id' => 'email',
					),
					array(
						'label' => 'Your name:',
						'type' => 'text',
						'name' => 'name',
						'id' => 'name',
					),
					array(
						'type' => 'layout:fieldset',
						'elements' => array(
							array(
								'label' => 'Email type:',
								'type' => 'radio',
								'name' => 'email_type',
								'id' => 'email_type',
								'options' => array(
									array( 'value' => 'html', 'text' => 'HTML' ),
									array( 'value' => 'text', 'text' => 'Text' )
								)
							),
							array(
								'label' => 'I agree to the terms and conditions:',
								'type' => 'checkbox',
								'name' => 'agree_terms',
								'id' => 'agree_terms'
							)									
						)
					)
				)
			)
        )
    )
);
```
<p data-height="265" data-theme-id="dark" data-slug-hash="yaqdxm" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/yaqdxm/">ioForm: Customising form layout -- nested layout elements</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>

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
				'type' => 'email',
				'name' => 'email',
				'id' => 'email',
			),
			array(
				'label' => 'Your name:',
				'type' => 'text',
				'name' => 'name',
				'id' => 'name',
			),
			array(
				'label' => 'Email type:',
				'type' => 'radio',
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
			'default' => '<div><label></label><elements/></div>'
		)
    )
);
```

The *default* template is used (by...uh...default) by all fields (except radio button elements -- we'll explore that, later). So with the above code in place, our example form now renders like this:

<p data-height="265" data-theme-id="dark" data-slug-hash="LRBKXW" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/LRBKXW/">ioForm: Customising form layout - a simple template</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>

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
				'type' => 'text',
				'name' => 'name',
				'id' => 'name',
			),
			array(
				'label' => 'Enter price:',
				'type' => 'number',
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

Looks like this:

<p data-height="265" data-theme-id="dark" data-slug-hash="NRBkoo" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/NRBkoo/">ioForm: Customising form layout - field prefix and suffix</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>

Or how about the same form, but spruced up with a bit of Bootstrap?

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/product-form.php',
        'method' => 'post',
        'elements' => array(
            array(
                'label' => 'Enter product name:',
                'type' => 'text',
                'name' => 'name',
                'id' => 'name',
				'class' => 'form-control' // Pretty input
            ),
            array(
                'label' => 'Enter price:',
                'type' => 'number',
                'name' => 'price',
                'id' => 'price',
				'class' => 'form-control', // Pretty input
                'container_template' => 'price'
            )
        ),
        'templates' => array(
            'default' => '<div class="form-group"><label></label><elements/></div>',
            'price' => '<div class="form-group"><label></label><div class="input-group"><div class="input-group-addon">$</div><elements/><div class="input-group-addon">+ TAX</div></div></div>'  // Extra markup for prefix and suffix elements
        )
    )
);
```

<p data-height="265" data-theme-id="dark" data-slug-hash="PGBALB" data-default-tab="html,result" data-user="Taeon" data-embed-version="2" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/PGBALB/">ioForm: Customising form layout - templates and Bootstrap</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>
