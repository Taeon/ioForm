# ioForm - simplified form handling for PHP and JavaScript

## Why?

I work with forms a lot. I don't like working with forms. So I built ioForm, to take away the pain.

## What does it do?

The PHP side of ioForm allows you to define a form's structure as an PHP object, and then use that definition object to spit out a perfect HTML form. With a very simple (yet flexible) template system, it's possible to wrap fields in standard markup, meaning that you don't need to update every row if you decide to change the structure of your form.

The JavaScript side of ioForm aims to simplify interactions with forms on the client. So for example, getting and setting values for all field types uses the same GetValue/SetValue method. In addition, ioForm returns values in a more useful form that native Javascript -- date fields return a Date object, number fields return a number, multiple selects return an array.

## Is that all?

Yes and no. When combined with ioValidate, ioForm will handle server- and client- side validation of form data. So you can define a set of validation rules once, and have them instantly work in PHP and JavaScript.