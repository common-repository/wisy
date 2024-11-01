document.addEventListener('DOMContentLoaded', function(e) {
	wisyCountdownTimer( document.querySelectorAll('.wisy-block.widget-countdown-timer') );
});

MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

var observer = new MutationObserver(function(mutations, observer) {
	// fired when a mutation occurs
	wisyCountdownTimer( document.querySelectorAll('.wisy-block.widget-countdown-timer') );
});

observer.observe(document, {
	subtree: true,
	attributes: true
});

function wisyCountdownTimer(elements) {
	elements.forEach(function (timerBlock) {

		var element = timerBlock.querySelector('.countdown');
		var endDate = element.getAttribute('data-date');
		let days, hours, minutes, seconds;

		endDate = new Date(endDate).getTime();

		if (isNaN(endDate)) {
			return;
		}

		setInterval( function () {
			var startDate = new Date().getTime();

			let timeRemaining = parseInt((endDate - startDate) / 1000);

			if (timeRemaining >= 0) {
				days = parseInt(timeRemaining / 86400);
				timeRemaining = (timeRemaining % 86400);

				hours = parseInt(timeRemaining / 3600);
				timeRemaining = (timeRemaining % 3600);

				minutes = parseInt(timeRemaining / 60);
				timeRemaining = (timeRemaining % 60);

				seconds = parseInt(timeRemaining);

				element.querySelector('.days>span').innerText = parseInt(days, 10);
				element.querySelector('.hours>span').innerText = hours < 10 ? "0" + hours : hours;
				element.querySelector('.minutes>span').innerText = minutes < 10 ? "0" + minutes : minutes;
				element.querySelector('.seconds>span').innerText = seconds < 10 ? "0" + seconds : seconds;
			} else {
				timerBlock.classList.add('hidden');
				return;
			}
		}, 1000 );

	});
}