/***
 * Number field
 * https://github.com/chemerisuk/better-dateinput-polyfill?
 */
var ioFormFieldNumber = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldNumber, ioFormField );
/**
 * Return a number field's value, as a numeric value (instead of string)
 *
 * @return      Date
 */
ioFormFieldNumber.prototype.GetValue = function( raw ){
    if( typeof raw !== 'undefined' && raw ){
        return this.element.value;
    }

    // No value has been entered, so just return an empty string
    // Otherwise it'll return 0 [zero]
    if( this.element.value === '' ){
        return '';
    }

    return +this.element.value; // Convert string to number
}
