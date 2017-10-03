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
        var value = this.element.getAttribute( 'value' );
        if( value === null ){
            // No value set on element so send 1 or 0
            value = ((this.element.checked)?1:0);
        } else {
            // Send value or blank if not checked
            value = ((this.element.checked)?value:'');
        }
        return value;
    }
    return this.element.checked;
};
