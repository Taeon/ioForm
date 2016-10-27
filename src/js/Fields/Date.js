/***
 * Date field
 * https://github.com/chemerisuk/better-dateinput-polyfill?
 */
var ioFormFieldDate = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldDate, ioFormField );
/**
 * Set a date field's value
 *
 * @param       date        Date as a date-formatted string or Date object
 *                          Strings will be parsed into a date object (using Date.Parse()) and then the value will be set as yyyy-mm-dd (because of Chrome)
 *                              Accepted values are anything understood by Date.Parse()
 *                          The displayed value will depend on the browser
 */
ioFormFieldDate.prototype.SetValue = function( date ){
    
    if( date !== null ){
        // Convert string to Date
        if ( typeof date == 'string' ) {
            date = new Date( Date.parse(date) );
        }
        if ( date.toString() != 'Invalid Date' ) {
            // For Chrome's sake, we need to convert to yyyy-MM-dd
            this.element.value = date.getFullYear() + '-' + ioFormUtility.ZeroPad( date.getMonth() + 1, 2 ) + '-' + ioFormUtility.ZeroPad( date.getDate(), 2 );
            //code
        } else {
            this.element.value = '';
        }
    } else {
        this.element.value = '';        
    }
    
    this.trigger( 'ioform:setvalue' );
};
/**
 * Return a date field's value, as a date object
 *
 * @return      Date
 */
ioFormFieldDate.prototype.GetValue = function(){
    // Convert value to date object
    var value = new Date( Date.parse(this.element.value) );
    // Make sure it's valid
    if ( value.toString() != 'Invalid Date' ) {
        return value;
    } else {
        return null;
    }
}