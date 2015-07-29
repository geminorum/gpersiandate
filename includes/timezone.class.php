<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateTimeZone extends gPersianDateModuleCore
{

	public static function current()
	{
		$timezone = get_option( 'timezone_string' ); //default is 'UTC' // 'Asia/Tehran'

		if ( ! empty( $timezone ) )
			return $timezone;

		return self::fromOffset( get_option( 'gmt_offset', '0' ) );
	}

	public static function fromOffset( $offset )
	{
		$timezones = array(
			'-12'  => 'Pacific/Kwajalein',
			'-11'  => 'Pacific/Samoa',
			'-10'  => 'Pacific/Honolulu',
			'-9'   => 'America/Juneau',
			'-8'   => 'America/Los_Angeles',
			'-7'   => 'America/Denver',
			'-6'   => 'America/Mexico_City',
			'-5'   => 'America/New_York',
			'-4'   => 'America/Caracas',
			'-3.5' => 'America/St_Johns',
			'-3'   => 'America/Argentina/Buenos_Aires',
			'-2'   => 'Atlantic/Azores', // no cities here so just picking an hour ahead
			'-1'   => 'Atlantic/Azores',
			'0'    => 'Europe/London',
			'1'    => 'Europe/Paris',
			'2'    => 'Europe/Helsinki',
			'3'    => 'Europe/Moscow',
			'3.5'  => 'Asia/Tehran',
			'4'    => 'Asia/Baku',
			'4.5'  => 'Asia/Kabul',
			'5'    => 'Asia/Karachi',
			'5.5'  => 'Asia/Calcutta',
			'6'    => 'Asia/Colombo',
			'7'    => 'Asia/Bangkok',
			'8'    => 'Asia/Singapore',
			'9'    => 'Asia/Tokyo',
			'9.5'  => 'Australia/Darwin',
			'10'   => 'Pacific/Guam',
			'11'   => 'Asia/Magadan',
			'12'   => 'Asia/Kamchatka'
		);

		if ( isset( $timezones[$offset] ) )
			return $timezones[$offset];

		return '0';
	}

	// NOT USED YET
	// Originally from : http://wordpress.org/plugins/easy-digital-downloads/
	// using like : date_default_timezone_set( get_timezone_id() );
	public static function get_timezone_id()
	{
		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) )
			return $timezone;

		// get UTC offset, if it isn't set return UTC
		if ( ! ( $utc_offset = 3600 * get_option( 'gmt_offset', 0 ) ) )
			return 'UTC';

		// attempt to guess the timezone string from the UTC offset
		$timezone = timezone_name_from_abbr( '', $utc_offset );

		// last try, guess timezone string manually
		if ( $timezone === false ) {

			$is_dst = date('I');

			foreach ( timezone_abbreviations_list() as $abbr ) {
				foreach ( $abbr as $city ) {
					if ( $city['dst'] == $is_dst &&  $city['offset'] == $utc_offset )
						return $city['timezone_id'];
				}
			}
		}

		// fallback
		return 'UTC';
	}
}
