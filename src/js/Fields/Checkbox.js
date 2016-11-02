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

ioFormUtility = {
    ZeroPad: function( num, numZeros ) {
        var n = Math.abs(num);
        var zeros = Math.max(0, numZeros - Math.floor(n).toString().length );
        var zeroString = Math.pow(10,zeros).toString().substr(1);
        if( num < 0 ) {
            zeroString = '-' + zeroString;
        }
    
        return zeroString+n;
    }
};