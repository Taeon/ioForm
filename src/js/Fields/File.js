/**
 * Single checkbox
 */
var ioFormFieldFile = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldFile, ioFormField );
ioFormFieldFile.prototype.GetValue = function( raw ){
    if( typeof raw !== 'undefined' && raw ){
        return this.element.value;
    }

    input = this.element;
    if (!input.files) {
         return this.element.value;
    }
    if (!input.files[0]) {
      return null;
    }

    return input.files;
};
