/*!
* PersianDate - v0.6.3 - 2016-11-30
* https://github.com/brothersincode/persiandate
* Licensed: MIT
*/

(function(name, global, definition) {
	if (typeof module != 'undefined')
		module.exports = definition();
	else if (typeof define == 'function' && typeof define.amd == 'object')
		define(definition);
	else if (typeof window != 'undefined')
		window[name] = definition();
	else
		global[name] = definition();
	}
('PersianDate', this, function() {

	function PersianDate(year, month, date, hours, minutes, seconds, milliseconds) {

		this.gD; // Gregorian Date
		this.jD; // Jalali Date

		this.o = {}; // options
		this.def = { // defaults
			debug: !1,
			translate_numbers: !1,
		};

		if (!year) {

			this._init({});
			this.setFullDate();

		} else if (year instanceof Date) {

			this._init({});
			this.setFullDate(year);

		} else if (year instanceof Object) {

			this._init(year); // options
			this.setFullDate();

		} else if (typeof(year) == 'boolean') {

			this.isJalali = year;
			this._init({});
			this.setFullDate();

		} else if (typeof(year == 'number')) {

			var y = parseInt(year, 10);
			var m = parseInt(month, 10);
			var d = parseInt(date, 10);
			y += this.d(m, 12);
			m = this.m(m, 12);
			var g = this.jalali_to_gregorian([y, m, d]);

			this._init({});
			this.setFullDate(new Date(g[0], g[1], g[2]));

		} else if (year instanceof Array) {
			throw new "PersianDate(Array) is not implemented yet!";

		} else {

			this._init({});
			this.setFullDate(year);
		}
	};

	PersianDate.prototype.setDate = function(dayValue) {

		// FIXME: must check for more then a month days
		this.jD[2] = dayValue;

		var g = this.jalali_to_gregorian(this.jD);
		this.gD = new Date(g[0], g[1], g[2]);
		this.jD = this.gregorian_to_jalali([g[0], g[1], g[2]]);

		return this;
	};

	// dateObj.setHours(hoursValue[, minutesValue[, secondsValue[, msValue]]])
	PersianDate.prototype.setHours = function(hoursValue) {
		return this.gD.setHours(hoursValue);
	};

	// dateObj.setMinutes(minutesValue[, secondsValue[, msValue]])
	PersianDate.prototype.setMinutes = function(minutesValue) {
		return this.gD.setMinutes(minutesValue);
	};

	PersianDate.prototype.setSeconds = function(e) {
		return this.gD.setSeconds(e)
	};

	PersianDate.prototype.setMilliseconds = function(e) {
		return this.gD.setMilliseconds(e)
	};

	PersianDate.prototype.getFullYear = function() {
		return this.jD[0];
	};

	PersianDate.prototype.getMonth = function() {
		return this.jD[1];
	};

	PersianDate.prototype.getDate = function() {
		return this.jD[2];
	};

	PersianDate.prototype.toString = function() {
		return this.jD.join(',').toString();
	};

	PersianDate.prototype.getDay = function() {
		return this.gD.getDay();
	};

	PersianDate.prototype.getHours = function() {
		return this.gD.getHours();
	};

	PersianDate.prototype.getMinutes = function() {
		return this.gD.getMinutes();
	};

	PersianDate.prototype.getSeconds = function() {
		return this.gD.getSeconds();
	};

	PersianDate.prototype.getTime = function() {
		return this.gD.getTime();
	};

	PersianDate.prototype.getTimeZoneOffset = function() {
		return this.gD.getTimeZoneOffset();
	};

	PersianDate.prototype.getYear = function() {
		return this.jD[0] % 100;
	};

	////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////

	PersianDate.prototype.setFullDate = function(date) {

		if (date instanceof PersianDate) {
			// if (date && date.getGregorianDate) {
			date = date.getGregorianDate();
		}

		this.gD = new Date(date);

		// daylight Saving Adjust
		this.gD.setHours(this.gD.getHours() > 12
			? this.gD.getHours() + 2
			: 0);

		if (!this.gD || this.gD == 'Invalid Date' || isNaN(this.gD) || !this.gD.getDate()) {
			this.gD = new Date();
		}

		this.jD = this.gregorian_to_jalali([this.gD.getFullYear(), this.gD.getMonth(), this.gD.getDate()]);

		this.log(this.gD.toString());
		this.log(this.jD);

		return this;
	};

	PersianDate.prototype.getGregorianDate = function() {
		return this.gD;
	};

	// to use in date picker
	PersianDate.prototype.calculateWeek = function(date) {
		var checkDate = new this(date.getFullYear(), date.getMonth(), date.getDate() + (date.getDay() || 7) - 3);
		return this.d(Math.round((checkDate.getTime() - new this(checkDate.getFullYear(), 0, 1).getTime()) / 86400000), 7) + 1;
	};

	PersianDate.prototype.log = function() {
		if (this.o.debug)
			console.log.apply('', arguments);
	};

	PersianDate.prototype._init = function(options) {

		for (var i in options) {
			if (options.hasOwnProperty(i)) {
				this.def[i] = options[i];
			}
		}

		this.o = this.def;
	};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

	// http://stackoverflow.com/a/6198324/4864081

	// New browsers version support new Date("2010-11-30T08:32:22+0000")
	// (Chrome, FF4, IE9), but old browsers doesn't.

	// PersianDate.fromISOString('2011-06-01');
	// PersianDate.fromISOString('2011-06-01T00:00:00');
	// PersianDate.fromISOString('2011-06-01T00:00:00Z');
	// PersianDate.fromISOString('2011-06-01T00:00:00+30');
	// PersianDate.fromISOString('2011-06-01T00:00:00-30');
	// PersianDate.fromISOString('2011-06-01T00:00:00+0530');
	// PersianDate.fromISOString('2011-06-01T00:00:00-0530');
	// PersianDate.fromISOString('2011-06-01T00:00:00+05:30');
	// PersianDate.fromISOString('2011-06-01T00:00:00-05:30');
	// PersianDate.fromISOString("2010-11-30T08:32:22+0000"); // Your example valid as well.

	PersianDate.prototype.fromISOString = function(isoDateString) {

		var tzoffset = (new Date).getTimezoneOffset();
		var tz = isoDateString.substr(10).match(/([\-\+])(\d{1,2}):?(\d{1,2})?/) || 0;

		if (tz)
			tz = tzoffset + (tz[1] == '-'
				? -1
				: 1) * (tz[3] != null
				? + tz[2] * 60 + (+ tz[3])
				: + tz[2]);

		return this.fastDateParse.apply(tz || 0, isoDateString.split(/\D/));
	};

	PersianDate.prototype.fastDateParse = function(y, m, d, h, i, s, ms) { // this -> tz
		return new Date(y, m - 1, d, h || 0, + (i || 0) - this, s || 0, ms || 0);
	};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/*
 * Persian number conversion lib
 * by https://github.com/juvee
 */

	PersianDate.prototype.toPersianDigit = function(number) {
		var pzero = '۰'.charCodeAt(0);
		return number.toString().replace(/\d+/g, function(match) {
			return match.split('').map(function(number) {
				return String.fromCharCode(pzero + parseInt(number))
			}).join('');
		})
	};

	PersianDate.prototype.toEnglishDigit = function(number) {
		return number.toString().replace(/[۱۲۳۴۵۶۷۸۹۰]+/g, function(match) {
			return match.split('').map(function(number) {
				return number.charCodeAt(0) % 1776;
			}).join('');
		})
	};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

	PersianDate.prototype.jalali_to_gregorian = function(d) {
		var adjustDay = 0;
		if (d[1] < 0) {
			adjustDay = this.lP(d[0] - 1)
				? 30
				: 29;
			d[1]++;
		}
		var gregorian = this.jd2g(this.p2jd(d[0], d[1] + 1, d[2]) - adjustDay);
		gregorian[1]--;
		return gregorian;
	};

	PersianDate.prototype.gregorian_to_jalali = function(d) {
		var jalali = this.jd2p(this.g2jd(d[0], d[1] + 1, d[2]));
		jalali[1]--;
		return jalali;
	};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
 * JavaScript functions for the Fourmilab Calendar Converter
 * by John Walker  --  September, MIM
 * http://www.fourmilab.ch/documents/calendar/
 * This program is in the public domain.
 */

	// PersianDate.prototype.GREGORIAN_EPOCH = 1721425.5;
	// PersianDate.prototype.ISLAMIC_EPOCH = 1948439.5;
	// PersianDate.prototype.PERSIAN_EPOCH = 1948320.5;

	PersianDate.prototype.GE = 1721425.5; // GREGORIAN_EPOCH
	PersianDate.prototype.IE = 1948439.5; // ISLAMIC_EPOCH
	PersianDate.prototype.PE = 1948320.5; // PERSIAN_EPOCH

	// div
	PersianDate.prototype.d = function(a, b) {
		return Math.floor(a / b);
	};

	// mod
	PersianDate.prototype.m = function(a, b) {
		return a - this.d(a, b) * b;
	};

	// leap_gregorian
	PersianDate.prototype.lG = function(year) {
		return ((year % 4) == 0) && (!(((year % 100) == 0) && ((year % 400) != 0)));
	};

	// gregorian_to_jd
	PersianDate.prototype.g2jd = function(year, month, day) {
		return (this.GE - 1) + (365 * (year - 1)) + this.d((year - 1), 4) + (-this.d((year - 1), 100)) + this.d((year - 1), 400) + Math.floor((((367 * month) - 362) / 12) + ((month <= 2)
			? 0
			: (this.lG(year)
				? -1
				: -2)) + day);
	};

	// jd_to_gregorian
	PersianDate.prototype.jd2g = function(jd) {
		var wjd,
			depoch,
			quadricent,
			dqc,
			cent,
			dcent,
			quad,
			dquad,
			yindex,
			dyindex,
			year,
			yearday,
			leapadj;

		wjd = Math.floor(jd - 0.5) + 0.5;
		depoch = wjd - this.GE;
		quadricent = this.d(depoch, 146097);
		dqc = this.m(depoch, 146097);
		cent = this.d(dqc, 36524);
		dcent = this.m(dqc, 36524);
		quad = this.d(dcent, 1461);
		dquad = this.m(dcent, 1461);
		yindex = this.d(dquad, 365);
		year = (quadricent * 400) + (cent * 100) + (quad * 4) + yindex;
		if (!((cent == 4) || (yindex == 4))) {
			year++;
		}

		yearday = wjd - this.g2jd(year, 1, 1);
		leapadj = ((wjd < this.g2jd(year, 3, 1))
			? 0
			: (this.lG(year)
				? 1
				: 2));

		month = this.d((((yearday + leapadj) * 12) + 373), 367);
		day = (wjd - this.g2jd(year, month, 1)) + 1;

		// return new Array(year, month, day);
		return [year, month, day];
	};

	// leap_islamic
	PersianDate.prototype.lI = function(year) {
		return (((year * 11) + 14) % 30) < 11;
	};

	// islamic_to_jd
	PersianDate.prototype.i2jd = function(year, month, day) {
		return (day + Math.ceil(29.5 * (month - 1)) + (year - 1) * 354 + Math.floor((3 + (11 * year)) / 30) + this.ISLAMIC_EPOCH) - 1;
	};

	// jd_to_islamic
	PersianDate.prototype.jd2i = function(jd) {
		var year,
			month,
			day;

		jd = Math.floor(jd) + 0.5;
		year = this.d(((30 * (jd - ISLAMIC_EPOCH)) + 10646), 10631);
		month = Math.min(12, Math.ceil((jd - (29 + this.i2jd(year, 1, 1))) / 29.5) + 1);
		day = (jd - this.i2jd(year, month, 1)) + 1;

		// return new Array(year, month, day);
		return [year, month, day];
	};

	// leap_persian
	PersianDate.prototype.lP = function(year) {
		return ((((((year - ((year > 0)
			? 474
			: 473)) % 2820) + 474) + 38) * 682) % 2816) < 682;
	};

	// persian_to_jd
	PersianDate.prototype.p2jd = function(year, month, day) {
		var epbase,
			epyear;

		epbase = year - ((year >= 0)
			? 474
			: 473);
		epyear = 474 + this.m(epbase, 2820);

		return day + ((month <= 7)
			? ((month - 1) * 31)
			: (((month - 1) * 30) + 6)) + this.d(((epyear * 682) - 110), 2816) + (epyear - 1) * 365 + this.d(epbase, 2820) * 1029983 + (this.PE - 1);
	};

	// jd_to_persian
	PersianDate.prototype.jd2p = function(jd) {

		var year,
			month,
			day,
			depoch,
			cycle,
			cyear,
			ycycle,
			aux1,
			aux2,
			yday;

		jd = Math.floor(jd) + 0.5;

		depoch = jd - this.p2jd(475, 1, 1);
		cycle = this.d(depoch, 1029983);
		cyear = this.m(depoch, 1029983);

		if (cyear == 1029982) {
			ycycle = 2820;
		} else {
			aux1 = this.d(cyear, 366);
			aux2 = this.m(cyear, 366);
			ycycle = this.d(((2134 * aux1) + (2816 * aux2) + 2815), 1028522) + aux1 + 1;
		}

		year = ycycle + (2820 * cycle) + 474;

		if (year <= 0) {
			year--;
		}

		yday = (jd - this.p2jd(year, 1, 1)) + 1;
		month = (yday <= 186)
			? Math.ceil(yday / 31)
			: Math.ceil((yday - 6) / 30);
		day = (jd - this.p2jd(year, month, 1)) + 1;

		// return new Array(year, month, day);
		return [year, month, day];
	};

	return PersianDate
}));
