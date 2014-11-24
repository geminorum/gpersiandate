function GPDfixNumbers(text, local) {
	if (text === null){
		return null;
		};
	if ( local === 'fa_IR' ) {
		text = text.replace(/0/g, '\u06F0');
		text = text.replace(/1/g, '\u06F1');
		text = text.replace(/2/g, '\u06F2');
		text = text.replace(/3/g, '\u06F3');
		text = text.replace(/4/g, '\u06F4');
		text = text.replace(/5/g, '\u06F5');
		text = text.replace(/6/g, '\u06F6');
		text = text.replace(/7/g, '\u06F7');
		text = text.replace(/8/g, '\u06F8');
		text = text.replace(/9/g, '\u06F9');
	};
	return text;
};
function GPDupdateClock() {
    var currentTime = new Date();
    var currentHours = currentTime.getHours();
    var currentMinutes = currentTime.getMinutes();
    //var currentSeconds = currentTime.getSeconds();

    // Pad the minutes and seconds with leading zeros, if required
    currentHours = ( currentHours < 10 ? "0" : "" ) + currentHours;
    currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
    //currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

    // Choose either "AM" or "PM" as appropriate
    //var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

    // Convert the hours component to 12-hour format if needed
    //currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

    // Convert an hours component of "0" to "12"
    //currentHours = ( currentHours == 0 ) ? 12 : currentHours;

    // Compose the string for display
    //var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
    var currentTimeString = GPDfixNumbers( currentHours + ":" + currentMinutes, GPD_clock['local'] );
	//console.log(currentTimeString);
    //jQuery("#wp-admin-bar-gpersiandate-now div").html(currentTimeString);
    jQuery("#gpd-now").html(currentTimeString);
};
jQuery(document).ready(function($) {
	//setInterval('GPDupdateClock()', 1000); 
	setInterval('GPDupdateClock()', 60*1000); 
});