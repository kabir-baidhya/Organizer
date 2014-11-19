<?php 

namespace Gckabir\Organizer\Prv;

class Helper {
	
	public static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}

	public static function hasWildcards($string) {
		return (bool) preg_match('/(\*|\?|\[|\])/i', $string);
	}
}
