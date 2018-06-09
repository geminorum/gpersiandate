(function ($) {
  var m = {};

  m.wrapper = '#gpd-now';

  m.toPersianDigit = function (number) {
    var pzero = '۰'.charCodeAt(0);
    return number.toString().replace(/\d+/g, function (match) {
      return match.split('').map(function (number) {
        return String.fromCharCode(pzero + parseInt(number));
      }).join('');
    });
  };

  m.toEnglishDigit = function (number) {
    return number.toString().replace(/[۱۲۳۴۵۶۷۸۹۰]+/g, function (match) {
      return match.split('').map(function (number) {
        return number.charCodeAt(0) % 1776;
      }).join('');
    });
  };

  m.updateClock = function () {
    var wrapper = $(m.wrapper);
    var currentTime = new Date();
    var currentHours = currentTime.getHours();
    var currentMinutes = currentTime.getMinutes();
    var currentSeconds = currentTime.getSeconds();

    // pad the minutes and seconds with leading zeros, if required
    currentHours = (currentHours < 10 ? '0' : '') + currentHours;
    currentMinutes = (currentMinutes < 10 ? '0' : '') + currentMinutes;
    currentSeconds = (currentSeconds < 10 ? '0' : '') + currentSeconds;

    // choose either "AM" or "PM" as appropriate
    // var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

    // convert the hours component to 12-hour format if needed
    // currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

    // convert an hours component of "0" to "12"
    // currentHours = ( currentHours == 0 ) ? 12 : currentHours;

    // compose the string for display
    // var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
    // var currentTimeString = GPDfixNumbers( currentHours + ":" + currentMinutes, GPD_clock.local );
    var currentTimeString = currentHours + ':' + currentMinutes + ':' + currentSeconds;

    if (wrapper.data('locale') === 'fa_IR') {
      currentTimeString = m.toPersianDigit(currentTimeString);
    }

    wrapper.html(currentTimeString);
    // console.log(currentTimeString);
  };

  $(document).ready(function () {
    setInterval(m.updateClock, 1000);
    // setInterval(m.updateClock, 60*1000);
  });
}(jQuery));
