	window.onload = function () {
			var links = document.getElementsByTagName('a');
			for (var x=0; x < links.length; x++) {
				links[x].onclick = function () {
					var mydomain = new RegExp(document.domain, 'i');
					if(!mydomain.test(this.getAttribute('href'))) {
						pageTracker._trackPageview('/outgoing/' + this.getAttribute('href'));
					}
				};
			}
		};