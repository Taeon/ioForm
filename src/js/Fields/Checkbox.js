/**
 * Single checkbox
 */
var ioFormFieldCheckbox = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldCheckbox, ioFormField );
/**
 * Checkbox is really boolean, so setting value is just setting the 'checked' attribute
 *
 * @param		boolean		value
 */
ioFormFieldCheckbox.prototype.SetValue = function( value ){
    this.element.checked = value;
    this.trigger( 'ioform:setvalue' );
};
/**
 * Checkbox is really boolean, so return true or false depending on the  'checked' attribute
 *
 * @return		boolean
 */
ioFormFieldCheckbox.prototype.GetValue = function( raw ){
    if( typeof raw !== 'undefined' && raw ){
        return ((this.element.checked)?1:0);
    }

    return this.element.checked;
};
