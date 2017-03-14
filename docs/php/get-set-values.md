# Getting and setting values

You're going to want to be able to change, and retrieve, your form's values. Here's how.

## Setting a default value

If you want to give a field a pre-set value that will always be populated when the form is loaded, just use the ```default``` parameter:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'elements' => array(
			array(
				'label' => 'Your name:',
				'type' => 'text',
				'default' => 'Bob Smith',
				'name' => 'your_name'
			)
        )
    )
);
echo( $form->Render() );
```

```html
<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" value="Bob Smith"/>
    <input type="submit" value="Submit"/>
</form>
```

<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" value="Bob Smith"/>
    <input type="submit" value="Submit"/>
</form>

Note that a ```default``` value is not the same as ```placeholder```: when you set a default, it will be submitted as the field's value (unless it's deleted or changed), whereas a placeholder value is just a visual cue for the user and wiil never be submitted. If you want to set a placeholder then you can with the (surprise!) ```placeholder``` option:

```php
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'elements' => array(
			array(
				'label' => 'Your name:',
				'type' => 'text',
				'placeholder' => 'Enter your name',
				'name' => 'your_name'
			)
        )
    )
);
echo( $form->Render() );
```

```html
<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" placeholder="Enter your name"/>
    <input type="submit" value="Submit"/>
</form>
```

<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" placeholder="Enter your name"/>
    <input type="submit" value="Submit"/>
</form>

## Setting a value
