/***
 * Textarea
 */
var ioFormFieldTextarea = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldTextarea, ioFormField );
ioFormFieldTextarea.prototype.SetValue = function( value ){
    this.element.value = value;
}