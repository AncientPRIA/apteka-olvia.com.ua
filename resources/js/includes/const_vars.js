module.exports = {
	window_width: window.innerWidth,
	cancelAnimationFrame: window.cancelAnimationFrame || window.mozCancelAnimationFrame,
	requestAnimationFrame: window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame,
	date_timezone: function date_timezone_change(date, timezone_in_hours){
						var offset = (date.getTimezoneOffset() + (timezone_in_hours * 60)) * 60 * 1000;
						date.setTime(date.getTime() + offset);
						return date;
	}, //date_timezone_change(new Date(),3)
}
