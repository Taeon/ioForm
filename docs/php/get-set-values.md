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

Note that a ```default``` value is not the same as ```placeholder```: when you set a default, it will be submitted as the field's value (unless it's deleted or changed), whereas a placeholder value is just a visual cue for the user and will never be submitted. If you want to set a placeholder then you can with the (surprise!) ```placeholder``` option:

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
			),
            array(
				'label' => 'Your email address:',
				'type' => 'email',
				'placeholder' => 'Enter your email address',
				'name' => 'your_email'
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
    <label for="your_email" class="email">Your email address:</label>
    <input type="email" id="your_email" name="your_email" placeholder="Enter your email address"/>
    <input type="submit" value="Submit"/>
</form>
```

<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" placeholder="Enter your name"/>
    <label for="your_email" class="email">Your email address:</label>
    <input type="email" id="your_email" name="your_email" placeholder="Enter your email address"/>
    <input type="submit" value="Submit"/>
</form>

## Setting values

If you want to set the value of one or more fields in a pre-defined form, then you _could_ overwrite the default value that's been set in the form definition (see above). However, that's a little clumsy. An easier approach is to use the ```SetValue()``` and ```SetValues()``` methods. These will override the default value (if set).

### Setting a single value

Easy -- just call ```ioForm::SetValue( [field], [value] );``` on your form. So for example, using the form above:

```php
$form->SetValue( 'your_name', 'Alice Smith' );
```

<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" placeholder="Enter your name" value="Alice Smith"/>
    <label for="your_email" class="email">Your email address:</label>
    <input type="email" id="your_email" name="your_email" placeholder="Enter your email address"/>
    <input type="submit" value="Submit"/>
</form>

Alternatively, you could use ```$form->GetField( 'your_name' )->SetValue( 'Alice Smith' );``` -- which does the same thing.

Note that with some fields, you can pass in a value that is appropriate to the field's type. So, for example, you can pass a DateTime object to a ```date```  field, or an array of values to a multiple ```select``` field.

### Setting a multiple values

You can pass multiple values using either an array or an object and ```ioForm::SetValues( [values] );```:

```php
$form->SetValues(
    array(
        'your_name' => 'Alice Smith',
        'your_email' => 'alice@smith.com'
    )
);
```

<form method="get" action="/newsletter-form.php">
    <label for="your_name" class="text">Your name:</label>
    <input type="text" id="your_name" name="your_name" placeholder="Enter your name" value="Alice Smith"/>
    <label for="your_email" class="email">Your email address:</label>
    <input type="email" id="your_email" name="your_email" placeholder="Enter your email address" value="alice@smith.com"/>
    <input type="submit" value="Submit"/>
</form>

## Getting values

If you want to know what value a given form field has been set to, then you can use the ```GetValue( [field] )``` method:

```php
echo( $form->GetValue( 'your_name' ) ); // 'Alice Smith'
```

Alternatively, you could use ```$form->GetField( 'your_name' )->GetValue();``` -- which does the same thing.

This will take into account a default value (if set), a value passed using ```SetValue()``` or ```SetValues()```, or -- if the form has been submitted -- the value entered into the form (i.e. the corresponding value in ```$_GET``` or ```$_POST```).

ioForm will, by default, return an appropriate value based on the field's type. So for example, a date field will return a DateTime object, a number field will return a number (as opposed to a string representation of a number). If you want to be sure to get the 'raw' value of a field, i.e. the value as it was passed in (generally a string), then pass a second parameter of ```true```:

```php
echo( $form->GetValue( 'my_date_field', true ) ); // Returns a string rather than a DateTime object
```

Note that if a value of ```null``` is returned then it means that this field's value has not been set.

### Getting multiple values

There's also a corresponding method ```GetValues()``` which will return all a form's values (as an associative array). Again, it will return processed values unless you pass in a parameter of ```true```, in which case raw values will be returned instead.
