<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateCal extends gPersianDateModuleCore
{
    
    public function setup_actions() {}
        
        
        
        
        
        
        
        
        
        
    
    // Keep English!!
	// ALSO : http://www.opal-creations.co.uk/blog/free-scripts-and-code/php-linear-year-view-calendar
	
	// http://css-tricks.com/snippets/php/build-a-calendar-table/
	/**
	
		$dateComponents = getdate();
		$month = $dateComponents['mon']; 			     
		$year = $dateComponents['year'];
		echo gPersianDate::buildCalendar( $month,$year,$dateComponents );

	**/	 
	public static function buildCalendar($month,$year,$dateArray) 
	{
	
		$today_date = date("d");
		$today_date = ltrim($today_date, '0');	

		 // Create array containing abbreviations of days of week.
		 $daysOfWeek = array('S','M','T','W','T','F','S');

		 // What is the first day of the month in question?
		 $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

		 // How many days does this month contain?
		 $numberDays = date('t',$firstDayOfMonth);

		 // Retrieve some information about the first day of the
		 // month in question.
		 $dateComponents = getdate($firstDayOfMonth);

		 // What is the name of the month in question?
		 $monthName = $dateComponents['month'];

		 // What is the index value (0-6) of the first day of the
		 // month in question.
		 $dayOfWeek = $dateComponents['wday'];

		 // Create the table tag opener and day headers

		 $calendar = "<table class='calendar'>";
		 $calendar .= "<caption>$monthName $year</caption>";
		 $calendar .= "<tr>";

		 // Create the calendar headers

		 foreach($daysOfWeek as $day) {
			  $calendar .= "<th class='header'>$day</th>";
		 } 

		 // Create the rest of the calendar

		 // Initiate the day counter, starting with the 1st.

		 $currentDay = 1;

		 $calendar .= "</tr><tr>";

		 // The variable $dayOfWeek is used to
		 // ensure that the calendar
		 // display consists of exactly 7 columns.

		 if ($dayOfWeek > 0) { 
			  $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>"; 
		 }
		 
		 $month = str_pad($month, 2, "0", STR_PAD_LEFT);
	  /**	 
		 while ($currentDay <= $numberDays) {

			  // Seventh column (Saturday) reached. Start a new row.

			  if ($dayOfWeek == 7) {

				   $dayOfWeek = 0;
				   $calendar .= "</tr><tr>";

			  }
			  
			  $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
			  
			  $date = "$year-$month-$currentDayRel";

			  $calendar .= "<td class='day' rel='$date'>$currentDay</td>";

			  // Increment counters
	 
			  $currentDay++;
			  $dayOfWeek++;

		 }
	**/	 
 while ($currentDay <= $numberDays) {

          // Seventh column (Saturday) reached. Start a new row.

          if ($dayOfWeek == 7) {

               $dayOfWeek = 0;
               $calendar .= "</tr><tr>";

          }

          $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);

          $date = "$year-$month-$currentDayRel";
		  
	  if($currentDayRel == $today_date ){  $calendar .= "<td class='day' id='today_date ' rel='$date'><b>$currentDay</b></td>"; } 

		  else { $calendar .= "<td class='day' rel='$date'>$currentDay</td>"; }

          // Increment counters

          $currentDay++;
          $dayOfWeek++;

     }

		 
		 

		 // Complete the row of the last week in month, if necessary

		 if ($dayOfWeek != 7) { 
		 
			  $remainingDays = 7 - $dayOfWeek;
			  $calendar .= "<td colspan='$remainingDays'>&nbsp;</td>"; 

		 }
		 
		 $calendar .= "</tr>";

		 $calendar .= "</table>";

		 return $calendar;

	}
	
	public static function yearCalendar()
	{
		$dateArray = getdate();
		/**
		//For Specific Month and Year
		$month = "3"; //Number of month (1-12)
		$year = "2010"; //Four digets
		echo build_calendar($month,$year,$dateArray);
		**/

		//For all Months in Specific Year
		$year = "2014";
		$i = 1;
		$month=1; //Numeric Value
		while($i <= 12){
			echo self::buildCalendar($month,$year,$dateArray);
			$month=$month+1;
			$i++;
		}	

	}    
}