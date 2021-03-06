if( typeof extend !== 'function' ){
	if (typeof Object.create !== 'function') {
		Object.create = function (o) {function F() {}F.prototype = o;return new F();};
	}
	var extend = function(ch, p) {var cp = Object.create(p.prototype);cp.constructor = ch;ch.prototype = cp;};
}

var Events = {
	/**
	 * Register any object to be able to register/trigger custom events
	 */
	Register:function( obj, target ){
		// Create a DOM EventTarget object
		if ( typeof target == 'undefined' ) {
			target = document.createTextNode(null);
		}
        obj.ready_events = [];
        var _target = target;
		obj.on = function( event_type, func ){
            var target = _target;
            // We only want this to run once
			if ( event_type == 'ioform:ready' ){
                obj.one( event_type, func );
                if( obj.onReady ) {
    				obj.onReady();
                }
			} else {
                if ( Object.prototype.toString.call( target ) == '[object Array]' ) {
                    for ( var i = 0; i < target.length; i++ ){
                        target[ i ].addEventListener( event_type, func );
                    }
                    return;
                } else {
                    target.addEventListener( event_type, func );
                }
            }
		};

		obj.one = function( event_type, func ){
            var target = _target;
			if ( Object.prototype.toString.call( target ) == '[object Array]' ) {
				for ( var i = 0; i < target.length; i++ ){
					target[ i ].addEventListener( event_type, function(e) {
						obj.off(event_type, arguments.callee);
						return func(e);
					});
				}
				return;
			} else {
				target.addEventListener(event_type, function(e) {
					obj.off(event_type, arguments.callee);
					return func(e);
				});
			}
        };
		obj.off = function( event_type, func ){
            var target = _target;
			if ( Object.prototype.toString.call( target ) == '[object Array]' ) {
				for ( var i = 0; i < target.length; i++ ){
				target[ i ].removeEventListener.bind(target);				}
				return;
			} else {
				target.removeEventListener.bind(target);
			}
        };

		obj.trigger = function( event_type, params ){
            var target = _target;
			if ( event_type == 'ioform:ready' && !obj.ready ) {
				return;
			}

			var detail = {};
			var bubbles = true;
			var cancelable = true;
			for ( var index in params ) {
				if( params.hasOwnProperty( index ) ){
					switch ( index ) {
						case 'bubbles':{
							bubbles = params[ index ];
							break;
						}
						case 'cancelable':{
							cancelable = params[ index ];
							break;
						}
						default:{
							detail[ index ] = params[ index ];
							break;
						}
					}
				}
			}

			var evt = new CustomEvent(
				event_type,
				{
					bubbles: bubbles,
					cancelable: cancelable,
					detail:detail
				}
			);
            if ( Object.prototype.toString.call( target ) == '[object Array]' ) {
                target = target[0];
            }
            return target.dispatchEvent(evt);
		};
	}
};
// Explorer polyfill for events
(function () {

  if ( typeof window.CustomEvent === "function" ) return false;

  function CustomEvent ( event, custom_params ) {
	var params = { bubbles: false, cancelable: false, detail: undefined  };
	for( var index in custom_params ){
		if( custom_params.hasOwnProperty( index ) ){
			params[ index ] = custom_params[ index ];
		}
	}
	var evt = document.createEvent( 'CustomEvent' );
	evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
	return evt;
   }

  CustomEvent.prototype = window.Event.prototype;

  window.CustomEvent = CustomEvent;
})();
;
(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define([], factory);
    } else if (typeof exports === "object") {
        module.exports = factory();
    } else {
        root.ioForm = factory();
    }
}(
    this,
    function () {
        'use strict';
        /********
         * Handles form interaction
         */
        var ioForm = function( form ){

            // What sort of element/selector have we been given?
            if ( typeof form == 'string' ) {
                // String
                var items = document.querySelectorAll( form );
                if ( items.length === 0 ) {
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
                    if ( form.length === 0 ) {
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

            this.form = form;

            var field_name;

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
                        field_name = field_element.getAttribute( 'name' );
						if( field_element.getAttribute( 'data-ioform-field-name' ) !== null ){
							field_name = field_element.getAttribute( 'data-ioform-field-name' );
						}

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
                                        field_name = field_element.getAttribute( 'name' );
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
                                var type = 'select';
                                this.fields[ field_name ] = new ioFormFieldSelect( field_element );
                                break;
                            }
                            case 'textarea':{
                                var type = 'textarea';
                                this.fields[ field_name ] = new ioFormFieldDefault( field_element );
                                break;
                            }
                        }
                        this.fields[ field_name ].on( 'ioform:ready', this.onFieldReady.bind( this ) );
						this.fields[ field_name ].type = type;
                    }
                }
                for( field_name in radio_fields ){
                    if( radio_fields.hasOwnProperty( field_name ) ){
                        this.fields[ field_name ] = new ioFormFieldRadio( radio_fields[ field_name ] );
                        this.fields[ field_name ].on( 'ioform:ready', this.onFieldReady.bind( this ) );
                    }
                }
            }
            // Check if all fields are ready
            for( field_name in this.fields ){
                if( this.fields.hasOwnProperty( field_name ) ){
                    this.fields[ field_name ].onFormReady();
                }
            }
        };
        ioForm.prototype = {

            input_field_types:{
                checkbox: 'Checkbox',
                date: 'Date',
                number: 'Number',
                file: 'File',
            },
			/**
			 * Get the form's HTML element
			 *
			 * @return		HTMLELement
			 */
            GetElement:function(){
                return this.form;
            },
            onFieldReady:function(){
                // Check that all fields are ready and then trigger ready event
                for ( var field_name in this.fields ) {
                    if( this.fields.hasOwnProperty( field_name ) ){
                        if ( !this.fields[ field_name ].ready ) {
                            return;
                        }
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
			/**
			 * Check if a field is present in the form
			 *
			 * @param		string		field_name		The name of the field
			 *
			 * @return		boolean
			 */
            HasField: function( field_name ){
                return typeof this.fields[ field_name ] !== 'undefined';
            },
			/**
			 * Get an array of all field objects in this form
			 *
			 * @return		array of fields
			 */
            GetFields: function(){
                return this.fields;
            },
			/**
			 * Get a field object
			 * i.e. a Javascript object that represents the field
			 * ...not the field element itself (use [field].GetElement() for that)
			 *
			 * @param		string		field_name		The name of the field
			 *
			 * @return		ioFormField object
			 */
            GetField: function( field_name ){
                if ( this.HasField( field_name ) ) {
                    return this.fields[ field_name ];
                } else {
                    return null;
                }
            },
			/**
			 * Get the value of a field
			 *
			 * @param		string		field_name		The name of the field
			 * @param		boolean		raw				Return raw value e.g. string instead of Date object (for a date field)
			 *
			 * @return		mixed
			 */
            GetValue: function( field_name, raw ){
                if ( this.HasField( field_name ) ) {
                    return this.fields[ field_name ].GetValue( raw );
                }
                return null;
            },
			/**
			 * Get an array of all field values in this form
			 *
			 * @param		boolean		raw				Return raw values e.g. string instead of Date object (for a date field)
			 *
			 * @return		array of fields
			 */
            GetValues: function( raw ){
                var values = {};
                for( var field_name in this.fields ){
                    if( this.fields.hasOwnProperty( field_name ) ){
                        var field = this.fields[ field_name ];
                        // Don't return values for disabled fields
                        if ( !field.IsDisabled() ){
                            values[ field_name ] = field.GetValue( raw );
                        }
                    }
                }
                return values;
            },
			/**
			 * Set the value of a field
			 *
			 * @param		string		field_name		The name of the field
			 * @param		mixed		value
			 *
			 * @return		mixed
			 */
            SetValue: function( field_name, value ){
                if ( this.HasField( field_name ) ) {
                    this.fields[ field_name ].SetValue( value );
                }
            },
			/**
			 * Submit the form
			 */
            Submit:function(){
                this.trigger( 'submit' );
            },
			/**
			 * Reset the form
			 */
            Reset:function(){
                this.form.reset();
                for( var index in this.fields ){
                    if( this.fields.hasOwnProperty( index ) ){
                        this.fields[index].Reset();
                    }
                }
            }
        };
        return ioForm;
    }
));

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
;
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
        // Because radio fields are arrays
        var element = this.element;
        if( Array.isArray( element ) ){
            element = this.element[ 0 ];
        }
        return element.getAttribute( 'name' );
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
        // Because radio fields are arrays
        var element = this.element;
        if( Array.isArray( element ) ){
            element = this.element[ 0 ];
        }

        if( element.hasAttribute( 'data-ioform-field-name' ) ){
			return element.getAttribute( 'data-ioform-field-name' );
		}
        return element.getAttribute( 'name' );
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
;
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
;
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
ioFormFieldDate.prototype.GetValue = function( raw ){
    if( typeof raw !== 'undefined' && raw ){
        return this.element.value;
    }

    // Convert value to date object
    var value = new Date( Date.parse( this.GetValue( true ) ) );
    // Make sure it's valid
    if ( value.toString() != 'Invalid Date' ) {
        return value;
    } else {
        return null;
    }
};
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
;
/***
 * Number field
 */
var ioFormFieldNumber = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldNumber, ioFormField );
/**
 * Return a number field's value, as a numeric value (instead of string)
 *
 * @return      Date
 */
ioFormFieldNumber.prototype.GetValue = function( raw ){
    if( typeof raw !== 'undefined' && raw ){
        return this.element.value;
    }

    // No value has been entered, so just return an empty string
    // Otherwise it'll return 0 [zero]
    if( this.element.value === '' ){
        return '';
    }

    return +this.element.value; // Convert string to number
}
;
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
;
/**
 * Select field
 */
var ioFormFieldSelect = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldSelect, ioFormField );
ioFormFieldSelect.prototype.GetValue = function( raw ){
    var selected = this.element.querySelectorAll( 'option:checked' );
    // Select multiple fields return an array of values
    if ( typeof this.element.getAttribute( 'multiple' ) != 'undefined' && this.element.getAttribute( 'multiple' ) != null ) {
        var value = [];
        for( var i = 0; i < selected.length; i++ ){
            if ( selected[ i ].hasAttribute( 'value' ) && selected[ i ].value.trim() != '' ) {
                value.push( selected[ i ].getAttribute( 'value' ) );
            }
        }
    } else {
        var value = '';
        if ( selected.length > 0 ) {
            var option = selected.item(0);
            if ( option.hasAttribute( 'value' ) ) {
                var value = option.getAttribute( 'value' );
            }
        }
    }

    return value;
};
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
    this.trigger( 'ioform:setvalue' );
};
ioFormFieldSelect.prototype.AddOption = function( value, text ){
    var opt = document.createElement( 'option' );
    opt.setAttribute( 'value', value );
    opt.innerText = text;
    this.element.appendChild( opt );
};
ioFormFieldSelect.prototype.RemoveOptions = function( value, text ){
    this.element.innerHTML = '';
};
ioFormFieldSelect.prototype.ClearOptions = function(){
    var options = this.element.querySelectorAll( 'option' );
    for( var o = 0; o < options.length; o++ ){
        options[ o ].selected = false;
    }
};
;
/***
 * Text field
 */
var ioFormFieldDefault = function( element ){
    ioFormField.call( this, element );
};
extend( ioFormFieldDefault, ioFormField );