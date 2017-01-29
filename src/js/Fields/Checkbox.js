/**
 * Single checkbox
 */
var ioFormFieldCheckbox = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldCheckbox, ioFormField );
ioFormFieldCheckbox.prototype.SetValue = function( value ){
    this.element.checked = value;
    this.trigger( 'ioform:setvalue' );
};
ioFormFieldCheckbox.prototype.GetValue = function( raw ){
    if( typeof raw !== 'undefined' && raw ){
        return ((this.element.checked)?1:0);
    }

    return this.element.checked;
};
