<?php
/*
 * Created on May 25, 2009
 *
 */
 
  /* 
 * Class ModHex
 * Encapsulates encoding/decoding text with the ModHex encoding from Yubico.
 * ModHex::Decode decodes a ModHex string
 * ModHex::Encode encodes a regular string into ModHex
 *
 */
class ModHex {

	static $TRANSKEY = "cbdefghijklnrtuv"; // translation key used to ModHex a string
	
	// ModHex encodes the string $src
	static function encode($src) {
		$encoded = "";
		$i = 0;
		$srcLen = strlen($src);
		for ($i = 0; $i < $srcLen; $i++) {
			$bin = (ord($src[$i]));
			$encoded .= ModHex::$TRANSKEY[((int)$bin >> 4) & 0xf];
			$encoded .= ModHex::$TRANSKEY[ (int)$bin & 0xf];
		}
		return $encoded;		
	}
	
	// ModHex decodes the string $token.  Returns the decoded string if successful,
	// or zero if an encoding error was found.
	static function decode($token) {
		$tokLen = strlen($token);	// length of the token
		$decoded = "";				// decoded string to be returned
		
		// strings must have an even length
		if ( $tokLen % 2 != 0 ) { return FALSE; }
		
		for ($i = 0; $i < $tokLen; $i=$i+2 ) {
			$high = strpos(ModHex::$TRANSKEY, $token[$i]);
			$low = strpos(ModHex::$TRANSKEY, $token[$i+1]);

			// if there's an invalid character in the encoded $token, fail here.
			if ( $high === FALSE || $low === FALSE ) 
				return FALSE;

			$decoded .= chr(($high << 4) | $low);
		}
		return $decoded;
	}
	

}

function modhexToB64($modhex_str) {
	$s = ModHex::Decode($modhex_str);
	return base64_encode($s);
}

function b64ToModhex($b64_str) {
	$s = base64_decode($b64_str);
	return ModHex::Encode($s);
}

function b64ToHex($b64_str) {
	$s = '';
	$tid = base64_decode($b64_str);
	$a = str_split($tid);
	for ($i=0; $i < count($a); $i++) {
		$s .= dechex(ord($a[$i]));
		//echo ' '.strval($s);
	}
	return $s;
}

function hexToB64($hex_str) {
	$s = '';
	$a = str_split($hex_str, 2);
	for ($i=0; $i < count($a); $i++) {
		if($a[$i] == '00') {
			$s .= chr(hexdec($a[$i]));
			$s .= chr(hexdec($a[$i]));
		} else {
			$s .= chr(hexdec($a[$i]));	
		}
		//echo '? '.strval($s).' :: '.$a[$i];
	}
	return base64_encode($s);
}

?>
