var countdown_woocommerce = document.querySelector("#cdw_countdown");

if(countdown_woocommerce) {
	var countdown_woocommerce_end_time = countdown_woocommerce.getAttribute("data-end-time");

	//Date format "YYYY-MM-DDTHH:mm:ss"
	var endDate = new Date(countdown_woocommerce_end_time).getTime();
	var countdown = setInterval(function () {
		var now = new Date().getTime();
		var distance = endDate - now;
		var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		countdown_woocommerce.innerHTML = "<span class='day-left'>" + days + "d</span> <span class='hours-left'>" + hours + "h</span> <span class='minutes-left'>" + minutes + "</span> <span class='seconds-left'>" + seconds + "s </span>";

		if (distance < 0) {
			clearInterval(countdown);
			countdown_woocommerce.innerHTML = "Expired";
		}
	}, 1000);
}
