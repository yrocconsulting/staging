/* Yroc theme interactions — no dependencies. */
(function () {
	'use strict';
	var doc = document.documentElement;
	doc.classList.add('yr-js');
	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var isDesktop = function () { return window.matchMedia('(min-width: 1081px)').matches; };

	/* ---------- Header ---------- */
	var header = $('[data-yr-header]');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 10); };
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
		var setHH = function () { doc.style.setProperty('--yr-hh', header.getBoundingClientRect().bottom + 'px'); };
		setHH();
		window.addEventListener('resize', setHH);
		window.addEventListener('scroll', setHH, { passive: true });
	}

	var burger = $('[data-yr-burger]');
	var nav = $('#yr-nav');
	if (burger && nav) {
		burger.addEventListener('click', function () {
			var open = burger.getAttribute('aria-expanded') !== 'true';
			burger.setAttribute('aria-expanded', open);
			nav.classList.toggle('is-open', open);
			document.body.style.overflow = open ? 'hidden' : '';
		});
	}

	var closeAll = function (except) {
		$$('[data-yr-drop]').forEach(function (b) {
			if (b === except) return;
			b.setAttribute('aria-expanded', 'false');
			b.parentNode.classList.remove('is-open');
		});
	};
	$$('[data-yr-drop]').forEach(function (btn) {
		var li = btn.parentNode, timer;
		var set = function (open) {
			if (open) closeAll(btn);
			btn.setAttribute('aria-expanded', open);
			li.classList.toggle('is-open', open);
		};
		btn.addEventListener('click', function (e) { e.stopPropagation(); set(btn.getAttribute('aria-expanded') !== 'true'); });
		li.addEventListener('mouseenter', function () { if (isDesktop()) { clearTimeout(timer); set(true); } });
		li.addEventListener('mouseleave', function () { if (isDesktop()) { timer = setTimeout(function () { set(false); }, 160); } });
	});
	document.addEventListener('click', function (e) { if (!e.target.closest('.yr-nav__item')) closeAll(); });
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(); });

	/* ---------- Reveal on scroll ---------- */
	var revealEls = $$('[data-reveal], .yr-card, .yr-section h2, .yr-tabs, .yr-faq__item');
	if ('IntersectionObserver' in window && !reduce) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (!en.isIntersecting) return;
				var el = en.target, sibs = el.parentNode ? Array.prototype.indexOf.call(el.parentNode.children, el) : 0;
				el.style.transitionDelay = Math.min(sibs, 5) * 80 + 'ms';
				el.classList.add('is-visible');
				setTimeout(function () { el.style.transitionDelay = ''; }, 1200);
				io.unobserve(el);
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
		revealEls.forEach(function (el) { io.observe(el); });
	} else {
		revealEls.forEach(function (el) { el.classList.add('is-visible'); });
	}

	/* ---------- Counters ---------- */
	var fmt = function (n, d) { return n.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); };
	var runCount = function (el) {
		var target = parseFloat(el.getAttribute('data-count')), d = parseInt(el.getAttribute('data-decimals') || '0', 10);
		if (reduce) { el.textContent = fmt(target, d); return; }
		var start = performance.now(), dur = 1800;
		var tick = function (t) {
			var p = Math.min(1, (t - start) / dur), e = 1 - Math.pow(1 - p, 4);
			el.textContent = fmt(target * e, d);
			if (p < 1) requestAnimationFrame(tick);
		};
		requestAnimationFrame(tick);
	};
	var counters = $$('[data-count]');
	if ('IntersectionObserver' in window) {
		var co = new IntersectionObserver(function (en) {
			en.forEach(function (x) { if (x.isIntersecting) { runCount(x.target); co.unobserve(x.target); } });
		}, { threshold: 0.6 });
		counters.forEach(function (c) { c.textContent = '0'; co.observe(c); });
	}

	/* ---------- Card spotlight ---------- */
	$$('.yr-card--glow').forEach(function (card) {
		card.addEventListener('pointermove', function (e) {
			var r = card.getBoundingClientRect();
			card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
			card.style.setProperty('--my', (e.clientY - r.top) + 'px');
		});
	});

	/* ---------- Tabs ---------- */
	$$('[data-yr-tabs]').forEach(function (wrap) {
		var tabs = $$('[role="tab"]', wrap);
		var select = function (tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute('aria-selected', on);
				t.tabIndex = on ? 0 : -1;
				var p = document.getElementById(t.getAttribute('aria-controls'));
				if (p) p.hidden = !on;
			});
			if (focus) tab.focus();
		};
		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () { select(t); });
			t.addEventListener('keydown', function (e) {
				var k = e.key, n = null;
				if (k === 'ArrowDown' || k === 'ArrowRight') n = tabs[(i + 1) % tabs.length];
				if (k === 'ArrowUp' || k === 'ArrowLeft') n = tabs[(i - 1 + tabs.length) % tabs.length];
				if (n) { e.preventDefault(); select(n, true); }
			});
		});
	});

	/* ---------- AI answer demo ---------- */
	var chat = $('[data-yr-chat]');
	if (chat) {
		var scenes = [
			{ q: 'Who is the most trusted roofing company near me?', intro: 'Based on reviews, local mentions and expertise, here are the top-recommended roofers:', you: 'Your Roofing Co.', why: '4.9★ · 312 reviews · cited by local news', others: [['Summit Roofing', '4.4★ · 88 reviews'], ['A1 Roof Repair', '4.1★ · 41 reviews']] },
			{ q: 'Which tax resolution firm can help me with an IRS problem?', intro: 'These firms are frequently recommended for IRS debt and tax resolution:', you: 'Your Tax Firm', why: 'Licensed EAs · featured expert · 5.0★', others: [['National Tax Relief', 'Mixed reviews'], ['TaxFix Pros', 'Limited info']] },
			{ q: "What's the best med spa for Botox in my area?", intro: 'For Botox and aesthetics, people consistently recommend:', you: 'Your Med Spa', why: 'Board-certified · 4.9★ · before/after gallery', others: [['Glow Aesthetics', '4.3★'], ['Radiance Clinic', '4.0★']] },
			{ q: 'Who should I call for termites and pest control?', intro: 'Highly rated pest control companies serving your area:', you: 'Your Pest Control', why: 'Same-day service · 4.8★ · 15 years local', others: [['BugAway', '4.2★'], ['PestPro Services', '3.9★']] }
		];
		var qEl = $('[data-yr-q]', chat), aEl = $('[data-yr-a]', chat), tabs = $$('.yr-chat__tabs button', chat);
		var token = 0, auto = true, idx = 0, autoTimer;
		var esc = function (s) { return s.replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
		var wait = function (ms, tk) { return new Promise(function (res, rej) { setTimeout(function () { tk === token ? res() : rej(); }, ms); }); };
		var play = function (i) {
			var tk = ++token, s = scenes[i];
			clearTimeout(autoTimer);
			tabs.forEach(function (b, j) { b.setAttribute('aria-selected', j === i); });
			aEl.innerHTML = '';
			var list = '<p class="yr-chat__intro">' + esc(s.intro) + '</p><ol class="yr-chat__list">' +
				'<li class="is-you"><span class="yr-chat__rank">1</span><span><b>' + esc(s.you) + '</b><small>' + esc(s.why) + '</small></span><span class="yr-chat__chip">Recommended</span></li>' +
				s.others.map(function (o, k) { return '<li><span class="yr-chat__rank">' + (k + 2) + '</span><span><b>' + esc(o[0]) + '</b><small>' + esc(o[1]) + '</small></span></li>'; }).join('') + '</ol>';
			if (reduce) { qEl.textContent = s.q; aEl.innerHTML = list; $$('li', aEl).forEach(function (l) { l.classList.add('is-in'); }); return; }
			qEl.innerHTML = '<span data-t></span><span class="yr-chat__caret"></span>';
			var t = $('[data-t]', qEl), n = 0;
			var type = function () {
				if (tk !== token) return Promise.reject();
				if (n > s.q.length) return Promise.resolve();
				t.textContent = s.q.slice(0, n++);
				return wait(28, tk).then(type);
			};
			type().then(function () { return wait(450, tk); }).then(function () {
				$('.yr-chat__caret', qEl) && $('.yr-chat__caret', qEl).remove();
				aEl.innerHTML = list;
				var items = $$('li', aEl);
				return items.reduce(function (p, li) { return p.then(function () { li.classList.add('is-in'); return wait(380, tk); }); }, wait(250, tk));
			}).then(function () {
				if (auto) autoTimer = setTimeout(function () { if (tk === token) { idx = (i + 1) % scenes.length; play(idx); } }, 4200);
			}).catch(function () {});
		};
		tabs.forEach(function (b, j) { b.addEventListener('click', function () { auto = false; idx = j; play(j); }); });
		if ('IntersectionObserver' in window) {
			var started = false;
			new IntersectionObserver(function (en) { if (en[0].isIntersecting && !started) { started = true; play(0); } }).observe(chat);
		} else { play(0); }
	}

	/* ---------- Authority simulator ---------- */
	var sim = $('[data-yr-sim]');
	if (sim) {
		var arc = $('[data-yr-arc]', sim), scoreEl = $('[data-yr-score]', sim), verdict = $('[data-yr-verdict]', sim);
		var boxes = $$('input[type="checkbox"]', sim), base = 12, current = base;
		var verdicts = [[25, 'AI barely knows you exist'], [45, 'AI knows you — but doesn’t trust you yet'], [65, 'You’re in the conversation'], [85, 'AI is starting to recommend you'], [101, 'You’re the answer AI recommends']];
		var update = function () {
			var target = base + boxes.reduce(function (s, b) { return s + (b.checked ? parseInt(b.getAttribute('data-w'), 10) : 0); }, 0);
			target = Math.min(target, 98);
			arc.setAttribute('stroke-dashoffset', 100 - target);
			for (var v = 0; v < verdicts.length; v++) { if (target < verdicts[v][0]) { verdict.textContent = verdicts[v][1]; break; } }
			var from = current, start = performance.now();
			current = target;
			var step = function (t) {
				var p = reduce ? 1 : Math.min(1, (t - start) / 700);
				scoreEl.textContent = Math.round(from + (target - from) * (1 - Math.pow(1 - p, 3)));
				if (p < 1) requestAnimationFrame(step);
			};
			requestAnimationFrame(step);
		};
		boxes.forEach(function (b) { b.addEventListener('change', update); });
	}

	/* ---------- Forms ---------- */
	$$('[data-yr-form]').forEach(function (form) {
		var msg = $('.yr-form__msg', form), btn = $('button[type="submit"]', form);
		var emailRe = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
		var setErr = function (name, text) {
			var el = form.elements[name]; if (!el) return;
			var field = el.closest('.yr-field'); field.classList.toggle('has-error', !!text);
			$('.yr-field__err', field).textContent = text || '';
			el.setAttribute('aria-invalid', text ? 'true' : 'false');
		};
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var bad = null;
			$$('[required]', form).forEach(function (el) {
				var v = el.value.trim(), err = '';
				if (!v) err = 'This field is required.';
				else if (el.type === 'email' && !emailRe.test(v)) err = 'Please enter a valid email.';
				setErr(el.name, err);
				if (err && !bad) bad = el;
			});
			if (bad) { bad.focus(); return; }
			btn.disabled = true; form.classList.add('is-sending');
			fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, credentials: 'same-origin' })
				.then(function (r) { return r.json().catch(function () { return { ok: false, message: 'Something went wrong. Please call 903.300.LEADS.' }; }); })
				.then(function (res) {
					if (res.errors) Object.keys(res.errors).forEach(function (k) { setErr(k, res.errors[k]); });
					msg.hidden = false; msg.textContent = res.message || '';
					msg.classList.toggle('is-error', !res.ok);
					if (res.ok) { form.classList.add('is-sent'); form.reset(); }
				})
				.catch(function () { msg.hidden = false; msg.classList.add('is-error'); msg.textContent = 'Network error. Please try again or call 903.300.LEADS.'; })
				.then(function () { btn.disabled = false; form.classList.remove('is-sending'); });
		});
		$$('input, textarea', form).forEach(function (el) { el.addEventListener('input', function () { if (el.closest('.has-error')) setErr(el.name, ''); }); });
	});

	/* ---------- Hero entity network ---------- */
	var cv = $('[data-yr-net]');
	if (cv && cv.getContext && !reduce) {
		var ctx = cv.getContext('2d'), pts = [], W = 0, H = 0, dpr = Math.min(window.devicePixelRatio || 1, 2), mouse = { x: -999, y: -999 }, running = true;
		var resize = function () {
			var r = cv.getBoundingClientRect();
			W = r.width; H = r.height;
			cv.width = W * dpr; cv.height = H * dpr;
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			var count = Math.round(Math.min(90, (W * H) / 14000));
			pts = [];
			for (var i = 0; i < count; i++) {
				pts.push({ x: Math.random() * W, y: Math.random() * H, vx: (Math.random() - 0.5) * 0.35, vy: (Math.random() - 0.5) * 0.35, r: Math.random() * 1.8 + 0.6, g: Math.random() < 0.18 });
			}
		};
		var hero = cv.closest('.yr-hero') || cv.parentNode;
		hero.addEventListener('pointermove', function (e) { var r = cv.getBoundingClientRect(); mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; });
		hero.addEventListener('pointerleave', function () { mouse.x = mouse.y = -999; });
		var draw = function () {
			if (!running) return;
			ctx.clearRect(0, 0, W, H);
			var max = 130;
			for (var i = 0; i < pts.length; i++) {
				var p = pts[i];
				p.x += p.vx; p.y += p.vy;
				if (p.x < 0 || p.x > W) p.vx *= -1;
				if (p.y < 0 || p.y > H) p.vy *= -1;
				var dm = Math.hypot(p.x - mouse.x, p.y - mouse.y);
				for (var j = i + 1; j < pts.length; j++) {
					var q = pts[j], d = Math.hypot(p.x - q.x, p.y - q.y);
					if (d < max) {
						var near = dm < 180;
						ctx.strokeStyle = near ? 'rgba(126,217,87,' + (1 - d / max) * 0.55 + ')' : 'rgba(140,170,200,' + (1 - d / max) * 0.18 + ')';
						ctx.lineWidth = near ? 1 : 0.6;
						ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
					}
				}
				ctx.fillStyle = p.g || dm < 140 ? '#7ED957' : 'rgba(180,200,220,.6)';
				ctx.beginPath(); ctx.arc(p.x, p.y, p.g ? p.r + 1 : p.r, 0, Math.PI * 2); ctx.fill();
			}
			requestAnimationFrame(draw);
		};
		resize();
		window.addEventListener('resize', resize);
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) { var was = running; running = en[0].isIntersecting; if (running && !was) draw(); }).observe(cv);
		}
		draw();
	}
})();
