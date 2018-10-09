/*********
 * Base class for fields
 */
var ioFormField = function( element ){
    this.ready = false;
    this.element = element;

    // Radio buttons
    if ( false ){//Object.prototype.toString.call( element ) == '[object Array]' ) {
        Events.Register( this );
    } else {
        Events.Register( this, element );
    this.ready = true;
    }
};
ioFormField.prototype = {
    onFormReady:function(){
        // The form is ready, is the field ready?
        // If not, the field needs to trigger this event itself at some point
        if ( this.ready ) {
            this.trigger( 'ioform:ready', {field:this} );
        }
    },
	/**
	 * Get the field's HTML element
	 *
	 * @return		HTMLELement
	 */
    GetElement:function(){
        return this.element;
    },
	/**
	 * Get the field's name attribute
	 * This might have been changed by ioForm
	 * Some fields, e.g. multiple select, use a different format for their name
	 * ...in order to submit as an array e.g. myselect becomes myselect[]
	 *
	 * @return		string
	 */
    GetName:function(){
        return this.element.getAttribute( 'name' );
    },
	/**
	 * Get the field's original name.
	 * Some fields, e.g. multiple select, use a different format for their name
	 * ...in order to submit as an array e.g. myselect becomes myselect[]
	 * This method will return the original name attribute's value
	 *
	 * @return		string
	 */
	GetFieldName:function(){
		if( this.element.hasAttribute( 'data-ioform-field-name' ) ){
			return this.element.getAttribute( 'data-ioform-field-name' );
		}
        return this.element.getAttribute( 'name' );
    },
	/**
	 * Get the field's value
	 *
	 * @param		boolean		raw				Return raw value e.g. string instead of Date object (for a date field)
	 *
	 * @return		mixed
	 */
    GetValue:function( raw ){
        return this.element.value;
    },
	/**
	 * Set the field's value
	 *
	 * @param		mixed		value
	 *
	 * @return		mixed
	 */
    SetValue:function( value ){
        this.element.value = value;
        this.trigger( 'ioform:setvalue' );
    },
	/**
	 * Disable the field
	 */
    Disable:function(){
		this.trigger( 'ioform:disabled' );
		this.element.setAttribute( 'disabled', 'disabled' );
    },
	/**
	 * Enable the field
	 */
    Enable:function(){
        this.element.removeAttribute( 'disabled' );
		this.trigger( 'ioform:enabled' );
    },
    /**
	 * Enable the field
	 */
    IsDisabled:function(){
        return this.element.hasAttribute( 'disabled' );
    },
	/**
	 * Reset the field
	 */
    Reset:function(){
        // This is a placeholder function
    }
}
