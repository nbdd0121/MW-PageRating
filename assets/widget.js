'use strict';

function loadResult(data) {
	setTooltipEnabled('rated' in data);
	if ('rated' in data) {
		setTooltipText(mw.msg('pagerating-ui-already-rated', data.myscore));
	}
	setResult(data.numrating, data.averagescore);
	setAverageScore(data.averagescore);
	setIcon('');
}

function loadRating() {
	setEnabled(false);
	setIcon('loading');
	new mw.Api().get({
		action: 'getrating',
		pageid: mw.config.get('wgArticleId')
	}).done(function(data) {
		loadResult(data.getrating);
		setEnabled(true);
	}).fail(function(error) {
		setIcon('error');
		// setText(error);
		setText(mw.msg('pagerating-ui-error-loading'));
	});
}

function rate(score) {
	setEnabled(false);
	setIcon('loading');
	new mw.Api().get({
		action: 'rate',
		pageid: mw.config.get('wgArticleId'),
		score: score
	}).done(function(data) {
		setTimeout(function() {
			loadResult(data.rate);
			setEnabled(true);
		}, 2000);
		setIcon('success');
		setText(mw.msg('pagerating-ui-rated'));
	}).fail(function(error) {
		setIcon('error');
		// alert(error);
		setText(mw.msg('pagerating-ui-error-loading'));
	});
}

function constructWidget() {
	var container = $('<div/>').addClass('rating-widget');
	container.append($('<span/>').text(mw.msg('pagerating-ui-rating-legend')));
	var starContainer = $('<div/>').addClass('rating-star-container');
	for (var i = 1; i <= 5; i++) {
		var star = $('<div/>').addClass('rating-star').addClass('rating-star-' + i);
		star.append($('<div/>'));
		starContainer.append(star);
		star.click(function(i) {
			return function() {
				if (container.attr('enabled') != null)
					rate(i);
			}
		}(i));
	}
	starContainer.append($('<div/>').addClass('rating-result-glasspane'));

	var tooltip = $('<div/>').addClass('rating-tooltip');
	starContainer.append(tooltip);

	container.append(starContainer);
	container.append('<div class="rating-result"><div class="rating-icon"/><div class="rating-text"/></div>');

	return container;
}

function setIcon(icon) {
	$('.rating-icon').attr('icon', icon);
}

function setAverageScore(score) {
	$('.rating-result-glasspane').width(score * 30);
}

function setTooltipEnabled(enabled) {
	if (enabled) {
		$('.rating-tooltip').attr('enabled', '');
	} else {
		$('.rating-tooltip').removeAttr('enabled');
	}
}

function setResult(num, score) {
	$('.rating-text').html(mw.msg('pagerating-ui-rating', num, score));
}

function setText(html) {
	$('.rating-text').html(html);
}

function setTooltipText(text) {
	$('.rating-tooltip').text(text);
}

function setEnabled(enabled) {
	if (enabled) {
		$('.rating-widget').attr('enabled', '');
	} else {
		$('.rating-widget').removeAttr('enabled');
	}
}

$('#bodyContent>.visualClear').before(
	constructWidget()
);

loadRating();