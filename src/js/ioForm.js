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
        };
        return ioForm;
    }
));