document.addEventListener('DOMContentLoaded', function() {
	setInterval(function get_uptimerobot_widget_data() {
		var request = new XMLHttpRequest();
		request.open('GET', uptimerobot.rest_api + 'uptime-robot-widget/v1/status', true);
		request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
		request.onload = function() {
			if(this.status == 200) {
				document.querySelector('#uptimerobot').innerHTML = JSON.parse(this.responseText).data;
			}
			else {
				document.querySelector('#uptimerobot').innerHTML = uptimerobot.error;
			}
		};
		request.send();
		return get_uptimerobot_widget_data;
	}(), 60000);
});