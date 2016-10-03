if (typeof Object.create !== 'function') {
    Object.create = function (o) {function F() {}F.prototype = o;return new F();};
}
function extend(ch, p) {var cp = Object.create(p.prototype);cp.constructor = ch;ch.prototype = cp;}

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
			var bubbles = false;
			var cancelable = false;
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