/**
 * Radio buttons
 */
var ioFormFieldRadio = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldRadio, ioFormField );
ioFormFieldRadio.prototype.SetValue = function( value ){
    if( value === null ){
        return;
    }
    value = value.toString();
    for( var i = 0; i < this.element.length; i++ ){
        if ( this.element[ i ].value == value ) {
            this.element[ i ].checked = true;
            break;
        }
    }
    this.trigger( 'ioform:setvalue' );
};
ioFormFieldRadio.prototype.GetValue = function( raw ){
    for( var i = 0; i < this.element.length; i++ ){
        if ( this.element[ i ].checked ) {
            return this.element[ i ].value;
        }
    }
    return null;
};
ioFormFieldRadio.prototype.Disable = function(){
    for( var i = 0; i < this.element.length; i++){
        this.element[i].setAttribute( 'disabled', "disabled" );
    }
};
ioFormFieldRadio.prototype.Enable = function(){
    for( var i = 0; i < this.element.length; i++){
        this.element[ i ].removeAttribute( 'disabled' );
    }
};
ioFormFieldRadio.prototype.IsDisabled = function(){
    for( var i = 0; i < this.element.length; i++){
        if( this.element[i].hasAttribute( 'disabled' ) && this.element[i].getAttribute( 'disabled' ) ){
            return true;
        }
    }
    return false;
};
