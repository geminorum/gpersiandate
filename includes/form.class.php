<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateForm extends gPersianDateModuleCore
{

	public static function selectFromLastMonths( $name = 'month', $limit = 12, $calendar = NULL )
	{
		echo '<select name="'.$name.'" class="month-dropdown month-lasts">';

		foreach ( gPersianDateStrings::lastMonths( $limit, $calendar ) as $key => $val )
			printf( '<option value="%s">%s</option>', $key, $val );

		echo '</select>';
	}

	public static function selectFromMonths( $name = 'month', $calendar = NULL )
	{
		echo '<select name="'.$name.'" class="month-dropdown">';

		foreach ( gPersianDateStrings::month( NULL, TRUE, $calendar ) as $key => $val )
			printf( '<option value="%1$s">%1$s-%2$s</option>', $key, $val );

		echo '</select>';
	}
}
