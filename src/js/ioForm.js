/********
 * Handles form interaction
 */
var ioForm = function( form ){

    // What sort of element/selector have we been given?    
    if ( typeof form == 'string' ) {
        // String
        var items = document.querySelectorAll( form );
        if ( items.length == 0 ) {
            throw new Error( 'ioForm error: Selector ["' + form + '"] returns no elements' );
        }
        if ( items.length > 1 ) {
            console.log( 'ioForm warning: Selector ["' + form + '"] returns more than one element. All but the first element will be ignored. Selected elements are:' );
            console.log( items );
        }
        form = items[0];
    } else if ( typeof form == 'object' && !(form instanceof HTMLElement) ) {
        // If it has length, it's probably an array-ish thing (e.g. jQuery collection)
        if ( typeof form.length !== 'undefined' ) {
            if ( form.length == 0 ) {
                throw new Error( 'ioForm error: Collection contains no elements' );
            }
            if ( form.length > 1 ) {
                console.log( 'ioForm warning: Collection contains more than one element. All but the first element will be ignored. Selected elements are:' );
                console.log( form );
            }
            form = form[0];
        }
    }

    // Make sure we've been passed a form element
    if ( !( form.tagName && form.tagName.toLowerCase() == 'form' ) ) {
        throw new Error( 'ioForm error: Element is not a form' );
    }
    
    // Already initialised?
    if ( form.ioForm ) {
        // Return current instance
        return form.ioForm;
    } else {
        this.ready = false;
        
        Events.Register( this, form );
        
        form.ioForm = this;
        
        this.fields = {};

        // Find all field elements (except radio buttons)
        var field_elements = form.querySelectorAll( 'input,select,textarea' );
        var radio_fields = {};
        for ( var i = 0; i < field_elements.length; i++ ) {
            var field_element = field_elements[ i ];

            if ( field_element.hasAttribute( 'name' ) ) {
                var field_name = field_element.getAttribute( 'name' );
                switch ( field_element.tagName.toLowerCase() ) {
                    case 'input':{
                        var type = field_element.getAttribute( 'type' ).toLowerCase();
                        if ( type != 'radio' ) {
                            var field_type = 'Default';
                            // Custom field type?
                            if ( typeof this.input_field_types[ type ] != 'undefined' ) {
                                field_type = this.input_field_types[ field_element.getAttribute( 'type' ) ];
                            }
                            this.fields[ field_name ] = new window[ 'ioFormField' + field_type ]( field_element );
                        } else {
                            // Radio fields
                            if ( field_element.hasAttribute( 'name' ) ) {
                                var field_name = field_element.getAttribute( 'name' );
                                if( typeof radio_fields[ field_name ] == 'undefined' ){
                                    radio_fields[ field_name ] = [];
                                }
                                // Group them for later
                                radio_fields[ field_name ].push( field_element );
                            }
                            continue;
                        }
                        break;
                    }
                    case 'select':{
                        this.fields[ field_name ] = new ioFormFieldSelect( field_element );
                        break;
                    }
                    case 'textarea':{
                        this.fields[ field_name ] = new ioFormFieldDefault( field_element );
                        break;
                    }
                }
                this.fields[ field_name ].on( 'ioform:ready', this.onFieldReady.bind( this ) );
            }
        }
        for( var field_name in radio_fields ){
            this.fields[ field_name ] = new ioFormFieldRadio( radio_fields[ field_name ] );
            this.fields[ field_name ].on( 'ioform:ready', this.onFieldReady.bind( this ) );
        }
    }
    // Check if all fields are ready 
    for( var field_name in this.fields ){
        this.fields[ field_name ].oioFormReady();
    }
}
ioForm.prototype = {
    
    input_field_types:{
        checkbox: 'Checkbox',
        date: 'Date',
    },
    
    onFieldReady:function( event ){
        // Check that all fields are ready and then trigger ready event
        for ( var field_name in this.fields ) {
            if ( !this.fields[ field_name ].ready ) {
                return;
            }
        }
        this.ready = true;
        this.onReady();
    },
    onReady:function(){
        if ( this.ready ) {
            this.trigger( 'ioform:ready', {form:this} );
        }
    },
    HasField: function( field_name ){
        return typeof this.fields[ field_name ] !== 'undefined';
    },
    GetField: function( field_name ){
        if ( this.HasField( field_name ) ) {
            return this.fields[ field_name ];
        } else {
            return null;
        }
    },
    GetValue: function( field_name ){
        if ( this.HasField( field_name ) ) {
            return this.fields[ field_name ].GetValue();
        }
        return null;
    },
    SetValue: function( field_name, value ){
        if ( this.HasField( field_name ) ) {
            this.fields[ field_name ].SetValue( value );
        }
    }
}
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
    oioFormReady:function(){
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

/***
 * Text field
 */
var ioFormFieldDefault = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldDefault, ioFormField );

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
    this.trigger( 'change' );
}
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
/**
 * Radio buttons
 */
var ioFormFieldRadio = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldRadio, ioFormField );
ioFormFieldRadio.prototype.SetValue = function( value ){
    value = value.toString();
    for( var i = 0; i < this.element.length; i++ ){
        if ( this.element[ i ].value == value ) {
            this.element[ i ].checked = true;
            break;
        }
    }
    this.trigger( 'change' );
}
ioFormFieldRadio.prototype.GetValue = function(){
    for( var i = 0; i < this.element.length; i++ ){
        if ( this.element[ i ].checked ) {
            return this.element[ i ].value;
        }
    }
    return null;
}
ioFormFieldRadio.prototype.Disable = function(){
    for( var i = 0; i < this.element.length; i++){
        this.element[i].setAttribute( 'disabled', "disabled" );
    }
}
ioFormFieldRadio.prototype.Enable = function(){
    for( var i = 0; i < this.element.length; i++){
        this.element[ i ].removeAttribute( 'disabled' );
    }
}

/**
 * Single checkbox
 */
var ioFormFieldCheckbox = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldCheckbox, ioFormField );
ioFormFieldCheckbox.prototype.SetValue = function( value ){
    this.element.checked = value;
    this.trigger( 'change' );
}
ioFormFieldCheckbox.prototype.GetValue = function(){
    return this.element.checked;
}

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
}