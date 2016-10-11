## Requirements

ioForm has no non-standard dependencies on either the PHP or JavaScript side. It does, however, require PHP >= 5.4. HTML5 form elements (e.g. date, number etc.) aren't supported by some older browsers such as IE9, so if that's a concern then you might need to look into finding a suitable polyfill.

## Installing ioForm...

### ...using composer

Simple enough, just use:

    composer require taeon/ioform

### ...from source

Alternatively, you can [download the source code as a compressed file](https://github.com/Taeon/ioForm/releases). 

## Loading ioForm

### PHP

If you're using composer, you don't need to do anything. If you're installing from source, it's still very easy: ioForm conforms to the [PSR-4 standard](http://www.php-fig.org/psr/psr-4/), so once you've included the autoloader file (autoload.php) somewhere in your PHP, it takes care of loading its dependencies itself.

### JavaScript

For now there's no compressed version, so you'll need to include the uncompressed source:

    <script src="[path-to-ioform]/src/js/ioForm.js"></script>

