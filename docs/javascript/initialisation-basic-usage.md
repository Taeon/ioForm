# Initialisation and basic usage

## Initialising ioForm

To use ioForm's JavaScript form handling, you simply need to include the script file (see [Requirements and installation](/requirements-installation)), and then initialise the form with the following code:

```javascript
	var form = new ioForm( '#my-form' );
```

Note that the selector (in this case, '#my-form') can be any of the following:

 * An actual form element (e.g. the result of a call to ```document.getElementById()```)
 * Any jquery-like (or more accurately, in this case ```document.querySelector()```-like) selector that returns a form, for example an ID (```'#my-form'```) or a class ( ```'.formclass'``` ) or element ( ```'form'``` )
 * A jQuery object that contains a form element (i.e. the result of a jquery call such as ```$( '#my-form' )```)

However you should also be aware that if the submitted argument returns (or contains) more than one element, then all but the first element will be ignored and a warning will be issued via the console.

Once you've called this method on a form, the result is cached so that further calls to the same method will return the same object. So while it would usually make most sense to store the returned object if you're going to use it again later, there's very little penalty in terms of performance (and none in terms of functionality) to do something like this:

```javascript
	// First call
	var form = new ioForm( '#my-form' );

	// ... some stuff happens

	// Second call
	var form2 = new ioForm( '#my-form' ); // form and form2 are the same object
```

## Getting a value

The main purpose of ioForm is to make form interactions simpler, more logical, and more consistent. This is most obvious when getting (and setting, as we'll see later) a form's values.

In plain JavaScript, some field types, such as ```input type="text"``` fields, single ```select``` fields, and ```textarea``` fields, support ```element.value```:

```javascript
	console.log( document.getElementById( 'my_input' ).value );
```

But how do you get the selected value of a ```radio``` button? Or all selected values in a multiple ```select``` element? That requires some more complex logic.

With ioForm, getting and a value for _all_ field types uses the same method: ```GetValue()```. So for example, a radio button:

<p data-height="129" data-theme-id="0" data-slug-hash="QdOepq" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: Getting a radio button value" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/QdOepq/">ioForm: Getting a radio button value</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

A multiple select field will return an array:

<p data-height="142" data-theme-id="0" data-slug-hash="ygPmbK" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: Get multiple select value" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/ygPmbK/">ioForm: Get multiple select value</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

When using a date field, instead of a string you get a date object:

<p data-height="124" data-theme-id="0" data-slug-hash="QdOegq" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: Get date field value" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/QdOegq/">ioForm: Get date field value</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

If you pass a second parameter of ```true``` to ```GetValue()```, then the 'raw' value of the field will be returned -- so for example, a ```date``` field will return a string instead of a Date object.

## Setting a value

If you want to set a field's value the following will work (in pretty much all reasonably modern browsers) for some field types, such as ```input type="text"``` fields, single ```select``` fields, and ```textarea``` fields:

```js
	var my_input = document.getElementById( 'my_input' );
	input.value = 'Hello world';
```

If you want to set the value of a ```radio``` button, or pass an array of values to a multiple ```select``` element then you'd have to start fiddling around with finding individual elements, and setting  a particular attribute (```checked```, ```selected``` or whichever is appropriate). [Yes some browsers support setting a multiple select's value with .value, but not all].

Again, With ioForm setting values for _all_ field types uses the same method: ```SetValue()```. So for example, you can set the value of a radio button like this:

<p data-height="203" data-theme-id="0" data-slug-hash="vgWqWb" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: Setting a radio button's value" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/vgWqWb/">ioForm: Setting a radio button's value</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

Or you could pass an array of values to a multiple select field:

<p data-height="143" data-theme-id="0" data-slug-hash="ZLadjJ" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: Set multiple select value" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/ZLadjJ/">ioForm: Set multiple select value</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

Or what if you're using a date field, and you don't want to mess about converting a date object to a string? No problem:

<p data-height="142" data-theme-id="0" data-slug-hash="WRXqax" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: set date field value" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/WRXqax/">ioForm: set date field value</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

## Events

In addition to providing consistent methods for getting and setting values, ioForm also implements a simple system for listening for field events. Like jQuery's event handling, it uses the ```on()``` method.

For example, if you want to know when a radio field's value changes:

<p data-height="206" data-theme-id="0" data-slug-hash="vgWowy" data-default-tab="js,result" data-user="Taeon" data-embed-version="2" data-pen-title="ioForm: Listen for change event" class="codepen">See the Pen <a href="https://codepen.io/Taeon/pen/vgWowy/">ioForm: Listen for change event</a> by Taeon (<a href="http://codepen.io/Taeon">@Taeon</a>) on <a href="http://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>
