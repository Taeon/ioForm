/**
 * Select field
 */
var ioFormFieldSelect = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldSelect, ioFormField );
ioFormFieldSelect.prototype.GetValue = function(){
    var selected = this.element.querySelectorAll( 'option:checked' );
    // Select multiple fields return an array of values
    if ( typeof this.element.getAttribute( 'multiple' ) != 'undefined' ) {
        var value = [];
        for( var i = 0; i < selected.length; i++ ){
            if ( selected[ i ].hasAttribute( 'value' ) ) {
                value.push( selected[ i ].getAttribute( 'value' ) );
            }
        }
    } else {
        var value = '';
        if ( selected.length > 0 ) {
            var option = selected.pop();
            if ( option.hasAttribute( 'value' ) ) {
                var value = option.getAttribute( 'value' );
            }
        }
    }

    return value;
}
ioFormFieldSelect.prototype.SetValue = function( value ){
    if ( typeof this.element.getAttribute( 'multiple' ) != 'undefined' && this.element.getAttribute( 'multiple' ) != null ) {
        this.ClearOptions();
        // Convert string to array
        if ( Object.prototype.toString.call( value ) !== '[object Array]' ) {
            value = [value];
        }
        for( var i = 0; i < value.length; i++ ){
            var options = this.element.querySelectorAll( 'option[value="' + value[ i ].toString()  + '"]' );
            for( var o = 0; o < options.length; o++ ){
                options[ o ].selected = true;
            }
        }
    } else {
        var options = this.element.querySelectorAll( 'option[value="' + value.toString()  + '"]' );
        for( var o = 0; o < options.length; o++ ){
            options[ o ].selected = true;
        }
    }
    this.trigger( 'change' );
}
ioFormFieldSelect.prototype.ClearOptions = function(){
    var options = this.element.querySelectorAll( 'option' );
    for( var o = 0; o < options.length; o++ ){
        options[ o ].selected = false;
    }
}