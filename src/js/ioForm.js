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
