/**
// http://jqueryui.com/demos/datepicker/
// http://hasheminezhad.com/datepicker
// http://fgelinas.com/code/timepicker/
// http://davidwalsh.name/jquery-datepicker-disable-days
// 
// http://stackoverflow.com/questions/1014554/jquery-datepicker-to-trigger-a-post
// http://mikemurko.com/general/jquery-ui-datepicker-form-submission-onselect/
// http://seesparkbox.com/foundry/validating_the_jquery_ui_datepicker
// http://stackoverflow.com/questions/6667149/get-datepicker-value-using-jqueryui
*/
jQuery(document).ready(function() {

    //jQuery('select[name="m"]').hide();
	
    jQuery('#start_date_gp').datepicker({
		dateFormat:'yy/mm/dd',
        regional:'fa',
        showOn: 'button',
        buttonImage: GPD_Edit['fromButtonImage'],
        buttonImageOnly: true,
        onSelect: function(dateText, inst) {
            jQuery('#end_date_gp').datepicker( 'option', 'minDate', new JalaliDate(inst['selectedYear'], inst['selectedMonth'], inst['selectedDay']));
        }
    });
    jQuery('#end_date_gp').datepicker({
        dateFormat:'yy/mm/dd',
        regional:'fa',
        showOn: 'button',
        buttonImage: GPD_Edit['toButtonImage'],
        buttonImageOnly: true
    });
       
});