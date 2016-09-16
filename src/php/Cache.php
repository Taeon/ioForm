<?php

namespace ioForm;

class Cache{
	static $cache;
	public static function Store( $id, $data ){
		if( empty( self::$cache ) ){
			self::$cache = array();
		}
		self::$cache[ $id ] = serialize( $data );
	}
	public static function Retrieve( $id ){
		if( is_array( self::$cache ) && isset( self::$cache[ $id ] ) ){
			return unserialize( self::$cache[ $id ] );
		}
		return false;
	}
}