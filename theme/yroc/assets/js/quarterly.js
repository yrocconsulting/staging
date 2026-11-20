/* The Yroc Quarterly: page turning on wide screens, stacked pages on phones, plus the interactive
   bits (why-it-matters toggles, playbook checklist, quiz, count-up numbers). */
(function () {
	'use strict';
	var root = document.querySelector('.yq');
	if (!root) return;
	var book = root.querySelector('.yq-book');
	var pages = Array.prototype.slice.call(book.querySelectorAll('.yq-page'));
	var pos = root.querySelector('.yq-pos');
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var flip = null;

	function store(key, val) {
		try {
			if (val === undefined) return localStorage.getItem(key);
			localStorage.setItem(key, val);
		} catch (e) { /* storage blocked: still works, just not remembered */ }
		return null;
	}

	// Count-up numbers, run once per page when it comes into view.
	function countUp(page) {
		if (page.classList.contains('is-shown')) return;
		page.classList.add('is-shown');
		page.querySelectorAll('.yq-count').forEach(function (el) {
			var to = parseFloat(el.dataset.to);
			if (isNaN(to) || reduce) return;
			var dec = parseInt(el.dataset.dec || '0', 10), pre = el.dataset.pre || '', suf = el.dataset.suf || '';
			var start = null, dur = 1100;
			function fmt(v) {
				return pre + v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }) + suf;
			}
			function step(t) {
				if (start === null) start = t;
				var p = Math.min(1, (t - start) / dur);
				el.textContent = fmt(to * (1 - Math.pow(1 - p, 3)));
				if (p < 1) requestAnimationFrame(step);
			}
			el.textContent = fmt(0);
			requestAnimationFrame(step);
		});
	}

	function label(i) {
		var last = pages.length - 1;
		if (i <= 0) return 'Cover';
		if (i >= last) return 'Back cover';
		// Printed page numbers are the page index + 1 (the cover is unnumbered).
		if (i === last - 1) return 'Page ' + (i + 1) + ' of ' + last;
		return 'Pages ' + (i + 1) + '–' + (i + 2) + ' of ' + last;
	}
	function shown(i) {
		if (pos) pos.textContent = label(i);
		[i, i + 1].forEach(function (k) {
			if (pages[k]) countUp(pages[k]);
		});
	}

	var wide = window.innerWidth >= 960 && window.St && window.St.PageFlip;
	if (wide) {
		root.classList.add('yq-flip');
		// Fit the spread to the window height so the controls stay on screen.
		var header = document.querySelector('.yr-site-header');
		var top = header ? header.getBoundingClientRect().height : 0;
		var ph = Math.max(414, Math.min(856, window.innerHeight - top - 150));
		var pw = Math.floor((ph * 550) / 760);
		root.querySelector('.yq-stage').style.maxWidth = 2 * pw + 'px';
		flip = new window.St.PageFlip(book, {
			width: 550,
			height: 760,
			size: 'stretch',
			minWidth: 300,
			maxWidth: pw,
			minHeight: 414,
			maxHeight: ph,
			showCover: true,
			usePortrait: false,
			mobileScrollSupport: false,
			disableFlipByClick: true,
			maxShadowOpacity: 0.35,
			flippingTime: reduce ? 1 : 800,
		});
		flip.loadFromHTML(pages);
		flip.on('flip', function (e) {
			shown(e.data);
		});
		shown(0);
	} else {
		// Phones, small windows: stacked pages, numbers animate as each page scrolls in.
		root.classList.add('yq-static');
		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (en) {
					if (en.isIntersecting) {
						countUp(en.target);
						io.unobserve(en.target);
					}
				});
			}, { threshold: 0.25 });
			pages.forEach(function (p) { io.observe(p); });
		} else {
			pages.forEach(countUp);
		}
	}

	function go(i) {
		if (flip) {
			if (typeof flip.flip === 'function') flip.flip(i);
			else flip.turnToPage(i);
		} else if (pages[i]) {
			pages[i].scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
		}
	}

	// One click handler for everything (the page-flip library moves the page elements around).
	root.addEventListener('click', function (e) {
		var t = e.target.closest('button');
		if (!t || !root.contains(t)) return;
		if (t.classList.contains('yq-next') && flip) return flip.flipNext();
		if (t.classList.contains('yq-prev') && flip) return flip.flipPrev();
		if (t.classList.contains('yq-goto')) return go(parseInt(t.dataset.page, 10));
		if (t.classList.contains('yq-why-btn')) {
			var open = t.getAttribute('aria-expanded') !== 'true';
			t.setAttribute('aria-expanded', open ? 'true' : 'false');
			t.nextElementSibling.hidden = !open;
			return;
		}
		if (t.classList.contains('yq-step')) {
			var on = t.getAttribute('aria-pressed') !== 'true';
			t.setAttribute('aria-pressed', on ? 'true' : 'false');
			store('yq-step-' + t.dataset.step, on ? '1' : '0');
			return progress();
		}
		if (t.classList.contains('yq-ans')) return answer(t);
		if (t.classList.contains('yq-retake')) {
			picks = {};
			var quiz = t.closest('.yq-in');
			quiz.querySelectorAll('.yq-ans.is-on').forEach(function (b) { b.classList.remove('is-on'); });
			quiz.querySelector('.yq-qs').hidden = false;
			quiz.querySelector('.yq-result').hidden = true;
		}
	});

	document.addEventListener('keydown', function (e) {
		if (!flip || /input|textarea|select/i.test((e.target.tagName || ''))) return;
		if (e.key === 'ArrowRight') flip.flipNext();
		if (e.key === 'ArrowLeft') flip.flipPrev();
	});

	// Playbook checklist: remembered on this device.
	function progress() {
		var steps = root.querySelectorAll('.yq-step');
		var done = root.querySelectorAll('.yq-step[aria-pressed="true"]').length;
		root.querySelectorAll('.yq-progress-n').forEach(function (n) { n.textContent = done; });
		return steps.length;
	}
	root.querySelectorAll('.yq-step').forEach(function (s) {
		if (store('yq-step-' + s.dataset.step) === '1') s.setAttribute('aria-pressed', 'true');
	});
	progress();

	// Quiz.
	var picks = {};
	function answer(btn) {
		var q = btn.dataset.q;
		var wrap = btn.closest('.yq-qs');
		wrap.querySelectorAll('.yq-ans[data-q="' + q + '"]').forEach(function (b) { b.classList.toggle('is-on', b === btn); });
		picks[q] = parseInt(btn.dataset.points, 10) || 0;
		var total = wrap.querySelectorAll('.yq-q').length;
		if (Object.keys(picks).length < total) return;
		var score = 0;
		Object.keys(picks).forEach(function (k) { score += picks[k]; });
		var max = parseInt(wrap.dataset.max, 10) || total * 2;
		var pct = Math.round((score / max) * 100);
		var results = JSON.parse(wrap.dataset.results || '[]');
		var r = results[0];
		results.forEach(function (x) { if (pct >= x.min) r = x; });
		var box = wrap.parentNode.querySelector('.yq-result');
		box.querySelector('.yq-result-score').textContent = pct + '/100';
		box.querySelector('.yq-result-t').textContent = r.title;
		box.querySelector('.yq-result-p').textContent = r.text;
		box.hidden = false;
		wrap.hidden = true;
	}
})();
