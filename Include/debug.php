<?php

function TOP_DEBUG( $str ) {
	global $top_debug;
	
	$top_debug->add_to_log( $str );
}

function top_is_debug_enabled() {
	global $top_debug;
	
	return $top_debug->is_enabled();
}

class TOPDebug {
	var $debug_file;
	var $log_messages;

	function TOPDebug() {
		$this->debug_file = false;
	}
	
	function is_enabled() {
		return ( $this->debug_file );	
	}

	function enable( $enable_or_disable ) {
		if ( $enable_or_disable ) {
			$this->debug_file = @fopen( WP_CONTENT_DIR . '/plugins/Tweet-Old-Post/log/log.txt', 'a+t' );
			$this->log_messages = 0;
		} else if ( $this->debug_file ) {
			fclose( $this->debug_file );
			$this->debug_file = false;		
		}
	}

	function add_to_log( $str ) {
		if ( $this->debug_file ) {
			
			$log_string = $str;
			
			// Write the data to the log file
			fwrite( $this->debug_file, sprintf( "%12s %s\n", time(), $log_string ) );
			fflush( $this->debug_file );
			
			$this->log_messages++;
		}
	}
}

global $top_debug;
$top_debug = &new TOPDebug;


?>
