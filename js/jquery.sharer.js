(function ($) {

$.sharer = {
	"networks": {
		"facebook": {
			"name": "Facebook",
			"url": "http://www.facebook.com/share.php?u=%url%&text=%description%"
		},
		"twitter": {
			"name": "Twitter",
			"url": "https://twitter.com/share?&text=%description%"
		},
		"linkedin": {
			"name": "Mail Millicom",
			"url": "mailto:app.support@millicom.com"
		}
	},
	"options": {
		"networks": ["facebook", "twitter", "linkedin", "tumblr", "googleplus", "reddit", "pinterest", "stumbleupon", "taringa"],
		"template": $('<a class="sharer-icon" />'),
		"class": "sharer-icon-%network.id%",
		"label": "Share on %network.name%",
		"title": null,
		"description": 'Vive la LOCURA extrema del fútbol con la App EXCLUSIVA de Tigo Copa Mundial FIFA™. Descárgala YA',
		"url": null
	}
};

$.fn.sharer = function () {
	var options = $.extend({}, $.sharer.options, options);

	return this.each(function () {
		var container = $(this);

		for (var i = 0; i < options["networks"].length; i++) {
			var network = options["networks"][i],
				networkData = $.sharer.networks[network],
				button = options["template"].clone();

			button
				.data("network", networkData)
				.addClass(options["class"].replace("%network.id%", network))
				.attr("title", options["label"].replace("%network.name%", networkData["name"]))
				.click(function () {
					var networkData = $(this).data("network"),
						popup = networkData["url"]
							.replace("%title%", encodeURIComponent(options["title"] || document.title))
							.replace("%description%", encodeURIComponent(options["description"] || $("meta[name=description]").attr("content")))
							.replace("%url%", encodeURIComponent(options["url"] || location.href));

					window.open(popup, "sharer", "toolbar=0,resizable=1,status=0,width=640,height=528");
				})
				.appendTo(container);
		}
	});
};

}(jQuery));