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
    GetElement:function(){
        return this.element;
    },
    GetName:function(){
        return this.element.getAttribute( 'name' );
    },
	GetFieldName:function(){
		if( this.element.hasAttribute( 'data-ioform-field-name' ) ){
			return this.element.getAttribute( 'data-ioform-field-name' );
		}
        return this.element.getAttribute( 'name' );
    },
    GetValue:function( raw ){
        return this.element.value;
    },
    SetValue:function( value ){
        this.element.value = value;
        this.trigger( 'ioform:setvalue' );
    },
    Disable:function(){
		this.trigger( 'ioform:disabled' );
		this.element.setAttribute( 'disabled', 'disabled' );
    },
    Enable:function(){
        this.element.removeAttribute( 'disabled' );
		this.trigger( 'ioform:enabled' );
    },
    Reset:function(){
        // This is a placeholder function
    }
}
