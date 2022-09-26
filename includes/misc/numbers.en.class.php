<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianNumbersEN extends gPersianDateBase
{

	// @REF: https://stackoverflow.com/a/15594551

	private static function singleDigit( $number )
	{
		switch ( $number ) {
			case 0: return 'zero';
			case 1: return 'one';
			case 2: return 'two';
			case 3: return 'three';
			case 4: return 'four';
			case 5: return 'five';
			case 6: return 'six';
			case 7: return 'seven';
			case 8: return 'eight';
			case 9: return 'nine';
		}

		return '';
	}

	private static function doubleDigitNumber( $number )
	{
		return 0 == $number ? '' : ( '-'.self::singleDigit( $number ) );
	}

	private static function doubleDigit( $number )
	{
		switch ( $number[0] ) {

			case 0: return self::doubleDigitNumber( $number[1] );

			case 1:
				switch ( $number[1] ) {
					case 0: return 'ten';
					case 1: return 'eleven';
					case 2: return 'twelve';
					case 3: return 'thirteen';
					case 4: return 'fourteen';
					case 5: return 'fifteen';
					case 6: return 'sixteen';
					case 7: return 'seventeen';
					case 8: return 'eighteen';
					case 9: return 'ninteen';
				}
				break;

			case 2: return 'twenty'. self::doubleDigitNumber( $number[1] );
			case 3: return 'thirty'. self::doubleDigitNumber( $number[1] );
			case 4: return 'forty'.  self::doubleDigitNumber( $number[1] );
			case 5: return 'fifty'.  self::doubleDigitNumber( $number[1] );
			case 6: return 'sixty'.  self::doubleDigitNumber( $number[1] );
			case 7: return 'seventy'.self::doubleDigitNumber( $number[1] );
			case 8: return 'eighty'. self::doubleDigitNumber( $number[1] );
			case 9: return 'ninety'. self::doubleDigitNumber( $number[1] );
		}

		return '';
	}

	private static function unitDigit( $numberlen, $number )
	{
		switch ( $numberlen ) {
			case 3:
			case 6:
			case 9:
			case 12: return 'hundred';
			case 4:
			case 5: return 'thousand';
			case 7:
			case 8: return 'million';
			case 10:
			case 11: return 'billion';
		}

		return '';
	}

	public static function numberToWords( $number )
	{
		$numberlength = strlen( $number );

		if ( 1 == $numberlength ) {

			return self::singleDigit( $number );

		} else if ( 2 == $numberlength ) {

			return self::doubleDigit( $number );

		} else {

			$word = '';

			switch ( $numberlength ) {

				case 5:
				case 8:
				case 11:

					if ( $number[0] > 0 ) {

						$unitdigit = self::unitDigit( $numberlength,$number[0] );
						$word      = self::doubleDigit( $number[0].$number[1] ) .' '.$unitdigit.' ';

						return $word.' '.self::numberToWords( substr( $number, 2 ) );

					} else {

						return $word.' '.self::numberToWords(substr($number,1));
					}

					break;

				default:

					if ( $number[0] > 0 ) {
						$unitdigit = self::unitDigit( $numberlength, $number[0] );
						$word      = self::singleDigit( $number[0] ) .' '.$unitdigit.' ';
					}

					return $word.' '.self::numberToWords( substr( $number, 1 ) );
			}
		}
	}
}
