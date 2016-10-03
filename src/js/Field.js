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
    GetValue:function(){
        return this.element.value;
    },
    SetValue:function( value ){
        this.element.value = value;
        this.trigger( 'change' );
    },
    Disable:function(){
        this.element.setAttribute( 'disabled', "" );
    },
    Enable:function(){
        this.element.removeAttribute( 'disabled' );
    },
}