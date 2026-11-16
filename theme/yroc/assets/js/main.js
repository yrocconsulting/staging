/* Yroc theme interactions. No dependencies. */
(function () {
	'use strict';
	var doc = document.documentElement;
	doc.classList.add('yr-js');
	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var isDesktop = function () { return window.matchMedia('(min-width: 1141px)').matches; };
	var esc = function (s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
	var fmt = function (n, d) { return n.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); };

	/* ---------- Header ---------- */
	var header = $('[data-yr-header]');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 10);
			doc.style.setProperty('--yr-hh', header.getBoundingClientRect().bottom + 'px');
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);
	}
	var burger = $('[data-yr-burger]'), nav = $('#yr-nav');
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
		var set = function (open) { if (open) closeAll(btn); btn.setAttribute('aria-expanded', open); li.classList.toggle('is-open', open); };
		btn.addEventListener('click', function (e) { e.stopPropagation(); set(btn.getAttribute('aria-expanded') !== 'true'); });
		li.addEventListener('mouseenter', function () { if (isDesktop()) { clearTimeout(timer); set(true); } });
		li.addEventListener('mouseleave', function () { if (isDesktop()) { timer = setTimeout(function () { set(false); }, 160); } });
	});
	document.addEventListener('click', function (e) { if (!e.target.closest('.yr-nav__item')) closeAll(); });
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(); });

	/* ---------- Reveal + counters ---------- */
	var runCount = function (el) {
		var target = parseFloat(el.getAttribute('data-count')), d = parseInt(el.getAttribute('data-decimals') || el.getAttribute('data-dec') || '0', 10);
		if (isNaN(target)) return;
		if (reduce) { el.textContent = fmt(target, d); return; }
		var start = performance.now();
		(function tick(t) {
			var p = Math.min(1, (t - start) / 1800);
			el.textContent = fmt(target * (1 - Math.pow(1 - p, 4)), d);
			if (p < 1) requestAnimationFrame(tick);
		})(start);
	};
	var revealEls = $$('.yr-reveal, [data-reveal], .yr-card, .yr-faq__item, .yr-step');
	var counters = $$('[data-count]');
	if ('IntersectionObserver' in window && !reduce) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (!en.isIntersecting) return;
				var el = en.target, i = el.parentNode ? Array.prototype.indexOf.call(el.parentNode.children, el) : 0;
				el.style.transitionDelay = Math.min(i, 5) * 70 + 'ms';
				el.classList.add('is-visible');
				setTimeout(function () { el.style.transitionDelay = ''; }, 1200);
				io.unobserve(el);
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
		revealEls.forEach(function (el) { io.observe(el); });
		var co = new IntersectionObserver(function (en) {
			en.forEach(function (x) { if (x.isIntersecting) { runCount(x.target); co.unobserve(x.target); } });
		}, { threshold: 0.5 });
		counters.forEach(function (c) { c.textContent = '0'; co.observe(c); });
	} else {
		revealEls.forEach(function (el) { el.classList.add('is-visible'); });
	}

	/* ---------- Tabs ---------- */
	$$('[data-yr-tabs]').forEach(function (wrap) {
		var tabs = $$('[role="tab"]', wrap);
		var select = function (tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1;
				var p = document.getElementById(t.getAttribute('aria-controls')); if (p) p.hidden = !on;
			});
			if (focus) tab.focus();
		};
		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () { select(t); });
			t.addEventListener('keydown', function (e) {
				var n = null;
				if (e.key === 'ArrowDown' || e.key === 'ArrowRight') n = tabs[(i + 1) % tabs.length];
				if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') n = tabs[(i - 1 + tabs.length) % tabs.length];
				if (n) { e.preventDefault(); select(n, true); }
			});
		});
	});

	/* ---------- Hero video ---------- */
	var vid = $('[data-yr-video]'), pauseBtn = $('[data-yr-pause]');
	if (vid) {
		if (reduce) { vid.pause(); vid.removeAttribute('autoplay'); }
		if (pauseBtn) {
			if (reduce) pauseBtn.setAttribute('aria-pressed', 'true');
			pauseBtn.addEventListener('click', function () {
				var paused = pauseBtn.getAttribute('aria-pressed') === 'true';
				if (paused) { vid.play(); } else { vid.pause(); }
				pauseBtn.setAttribute('aria-pressed', paused ? 'false' : 'true');
				$('.yr-sr', pauseBtn).textContent = paused ? 'Pause background video' : 'Play background video';
			});
		}
		// Save battery/data: pause when the hero is off screen.
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) {
				if (pauseBtn && pauseBtn.getAttribute('aria-pressed') === 'true') return;
				if (en[0].isIntersecting) { var p = vid.play(); if (p && p.catch) p.catch(function () {}); } else { vid.pause(); }
			}).observe(vid);
		}
	}

	/* ---------- Hero: "See what AI says" ---------- */
	var check = $('[data-yr-check]'), card = $('[data-yr-aicard]');
	if (check && card) {
		var qEl = $('[data-q]', card), aEl = $('[data-a]', card), srcEl = $('[data-src]', card), go = $('[data-go]', card), tok = 0;
		var engines = ['ChatGPT', 'Gemini', 'Perplexity', 'Google AI Overviews'];
		check.addEventListener('submit', function (e) {
			e.preventDefault();
			var bizIn = check.elements.business, cityIn = check.elements.market;
			var biz = (bizIn.value || bizIn.placeholder).trim(), city = (cityIn.value || cityIn.placeholder).trim();
			var my = ++tok;
			card.classList.remove('is-done');
			srcEl.textContent = engines[Math.floor(Math.random() * engines.length)] + ' · answer preview';
			qEl.textContent = '› Who should I trust in ' + city + '? Is ' + biz + ' a good choice?';
			go.href = check.action + '?business=' + encodeURIComponent(bizIn.value) + '&market=' + encodeURIComponent(cityIn.value) + '#audit';
			var html = 'AI recommends businesses it can clearly identify and verify. Whether <b>' + esc(biz) + '</b> gets named in ' + esc(city) + ' depends on its entity signals, reviews and third-party mentions. A full audit shows exactly where you stand.';
			if (reduce) { aEl.innerHTML = html; card.classList.add('is-done'); return; }
			var parts = html.match(/<[^>]+>[^<]*<\/[^>]+>\s*|[^<\s]+\s*/g), i = 0, out = '';
			aEl.innerHTML = '<span class="yr-caret"></span>';
			if (window.innerWidth < 980) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
			setTimeout(function step() {
				if (my !== tok) return;
				if (i >= parts.length) { aEl.innerHTML = out; card.classList.add('is-done'); return; }
				out += parts[i++]; aEl.innerHTML = out + '<span class="yr-caret"></span>';
				setTimeout(step, 30 + Math.random() * 45);
			}, 450);
		});
	}

	/* ---------- The Yroc Method: entity graph ---------- */
	var method = $('[data-yr-method]');
	if (method) {
		var cv = $('[data-yr-graph]', method), ctx = cv.getContext('2d'), dpr = Math.min(window.devicePixelRatio || 1, 2), W = 0, H = 0;
		var meter = $('[data-meter]', method), conf = $('[data-conf]', method), steps = $$('.yr-mstep', method);
		var L = [['Google Business Profile', 0], ['Website', 0], ['Schema.org', 0], ['Wikidata', 0], ['LinkedIn', 0], ['Bing Places', 0], ['Apple Maps', 0], ['Founder bio', 0], ['NAP citations', 0],
			['Google reviews', 1], ['Yelp', 1], ['BBB', 1], ['Press mention', 1], ['Podcast', 1], ['YouTube', 1], ['Industry directory', 1], ['Case study', 1], ['Clutch', 1], ['Local news', 1],
			['FAQ answers', 2], ['Knowledge panel', 2], ['Expert article', 2], ['Comparison page', 2], ['Awards', 2]];
		var rc = [0, 0, 0], ri = L.map(function (l) { return rc[l[1]]++; });
		var nodes = L.map(function (l, i) { return { label: l[0], k: l[1], x: Math.random() * 500, y: Math.random() * 400, vx: 0, vy: 0, a: ri[i] / rc[l[1]] * Math.PI * 2 + l[1] * 0.35, sx: Math.random(), sy: Math.random(), seed: Math.random() * 99 }; });
		var drag = null, mouse = { x: -1e4, y: -1e4 }, prog = 0.05, tgt = 0.05, started = false, visible = false, timer, t = 0, thr = [0.2, 0.5, 0.85];
		var size = function () { var r = cv.getBoundingClientRect(); W = r.width; H = r.height; cv.width = W * dpr; cv.height = H * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0); };
		size(); window.addEventListener('resize', size);
		var pos = function (e) { var r = cv.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; };
		cv.addEventListener('pointerdown', function (e) { var p = pos(e), b = null, bd = 28; nodes.forEach(function (n) { var d = Math.hypot(n.x - p.x, n.y - p.y); if (d < bd) { bd = d; b = n; } }); drag = b; if (b) cv.setPointerCapture(e.pointerId); });
		cv.addEventListener('pointermove', function (e) { var p = pos(e); mouse = p; if (drag) { drag.x = p.x; drag.y = p.y; drag.vx = drag.vy = 0; } });
		cv.addEventListener('pointerup', function () { drag = null; });
		cv.addEventListener('pointerleave', function () { mouse = { x: -1e4, y: -1e4 }; });
		var setPhase = function (p, user) {
			tgt = [0.3, 0.6, 0.95][p];
			steps.forEach(function (s, i) { s.setAttribute('aria-selected', i === p); var b = $('.yr-bar', s); b.style.animation = 'none'; void b.offsetWidth; b.style.animation = ''; });
			clearTimeout(timer);
			if (!user && !reduce) timer = setTimeout(function () { setPhase((p + 1) % 3); }, 6000);
		};
		steps.forEach(function (s, i) { s.addEventListener('click', function () { setPhase(i, true); }); });
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) {
				visible = en[0].isIntersecting;
				if (visible && !started) { started = true; setPhase(0); }
				if (visible) requestAnimationFrame(frame);
			}, { threshold: 0.25 }).observe(cv);
		} else { started = visible = true; setPhase(0); }
		var frame = function () {
			if (!visible) return;
			t += 0.016; prog += (tgt - prog) * (reduce ? 1 : 0.03);
			var X = W * 0.46, Y = H / 2 - 10, R = Math.min(W * 0.62, H) * 0.56, connected = 0;
			ctx.clearRect(0, 0, W, H);
			nodes.forEach(function (n) {
				var on = prog > thr[n.k]; if (on) connected++;
				if (n === drag) return;
				var hx, hy;
				if (on) { var ring = [0.42, 0.74, 1][n.k], a = n.a + t * 0.04 * (n.k === 1 ? -1 : 1); hx = X + Math.cos(a) * R * ring * 1.05; hy = Y + Math.sin(a) * R * ring * 0.92; }
				else { hx = W * (0.05 + n.sx * 0.7) + Math.sin(t * 0.6 + n.seed) * 12; hy = H * (0.08 + n.sy * 0.78) + Math.cos(t * 0.5 + n.seed) * 12; }
				n.vx += (hx - n.x) * 0.012; n.vy += (hy - n.y) * 0.012;
				var dm = Math.hypot(n.x - mouse.x, n.y - mouse.y);
				if (dm < 90 && dm > 0) { n.vx += (n.x - mouse.x) / dm * 0.6; n.vy += (n.y - mouse.y) / dm * 0.6; }
				n.vx *= 0.86; n.vy *= 0.86; n.x += n.vx; n.y += n.vy;
			});
			nodes.forEach(function (n) {
				if (prog <= thr[n.k]) return;
				var k = Math.min(1, (prog - thr[n.k]) * 5);
				ctx.strokeStyle = 'rgba(126,217,87,' + (0.45 * k) + ')'; ctx.lineWidth = 1;
				ctx.beginPath(); ctx.moveTo(X, Y); ctx.lineTo(X + (n.x - X) * k, Y + (n.y - Y) * k); ctx.stroke();
				var pp = (t * 0.5 + n.seed) % 1;
				ctx.fillStyle = 'rgba(126,217,87,' + k + ')'; ctx.beginPath(); ctx.arc(n.x + (X - n.x) * pp, n.y + (Y - n.y) * pp, 1.6, 0, 7); ctx.fill();
			});
			ctx.font = '500 11.5px Inter, sans-serif'; ctx.textBaseline = 'middle';
			nodes.forEach(function (n) {
				var on = prog > thr[n.k];
				ctx.fillStyle = on ? (n.k === 2 ? '#0099FF' : '#7ED957') : 'rgba(84,117,144,.9)';
				ctx.beginPath(); ctx.arc(n.x, n.y, on ? 4 : 3, 0, 7); ctx.fill();
				ctx.fillStyle = on ? 'rgba(233,238,245,.88)' : 'rgba(233,238,245,.35)'; ctx.fillText(n.label, n.x + 9, n.y);
			});
			var c = connected / nodes.length, core = (14 + c * 26) * (1 + Math.sin(t * 2.4) * 0.04);
			var g = ctx.createRadialGradient(X, Y, 0, X, Y, core * 4);
			g.addColorStop(0, 'rgba(126,217,87,' + (0.12 + c * 0.35) + ')'); g.addColorStop(1, 'rgba(126,217,87,0)');
			ctx.fillStyle = g; ctx.beginPath(); ctx.arc(X, Y, core * 4, 0, 7); ctx.fill();
			ctx.fillStyle = c > 0.95 ? '#7ED957' : '#E9EEF5'; ctx.beginPath(); ctx.arc(X, Y, core, 0, 7); ctx.fill();
			ctx.fillStyle = '#0B1220'; ctx.textAlign = 'center'; ctx.font = '700 ' + Math.round(9 + c * 4) + 'px Inter, sans-serif';
			ctx.fillText(c > 0.95 ? 'RECOMMENDED' : 'YOUR BRAND', X, Y); ctx.textAlign = 'left';
			var sc = Math.round(8 + c * 88); meter.style.width = sc + '%'; conf.textContent = sc + '%';
			requestAnimationFrame(frame);
		};
	}

	/* ---------- Before / after slider ---------- */
	$$('[data-yr-cmp]').forEach(function (cmp) {
		var h = $('[data-handle]', cmp), dragging = false;
		var set = function (p) { p = Math.max(4, Math.min(96, p)); cmp.style.setProperty('--cut', p + '%'); h.setAttribute('aria-valuenow', Math.round(p)); };
		var from = function (e) { var r = cmp.getBoundingClientRect(); set((e.clientX - r.left) / r.width * 100); };
		cmp.addEventListener('pointerdown', function (e) { dragging = true; cmp.setPointerCapture(e.pointerId); from(e); });
		cmp.addEventListener('pointermove', function (e) { if (dragging) from(e); });
		cmp.addEventListener('pointerup', function () { dragging = false; });
		h.addEventListener('keydown', function (e) { var v = +h.getAttribute('aria-valuenow'); if (e.key === 'ArrowLeft') set(v - 5); if (e.key === 'ArrowRight') set(v + 5); });
		if (reduce || !('IntersectionObserver' in window)) { set(18); return; }
		new IntersectionObserver(function (en, o) {
			if (!en[0].isIntersecting) return; o.disconnect();
			var s = null;
			requestAnimationFrame(function sw(n) { if (!s) s = n; var p = Math.min(1, (n - s) / 1600); if (!dragging) set(85 - 67 * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(sw); });
		}, { threshold: 0.5 }).observe(cmp);
	});

	/* ---------- Case study filter ---------- */
	$$('[data-yr-filter]').forEach(function (bar) {
		var btns = $$('button', bar), cards = $$('[data-yr-cases] [data-cat]');
		var apply = function (cat) {
			btns.forEach(function (b) { b.setAttribute('aria-pressed', b.getAttribute('data-cat') === cat); });
			cards.forEach(function (c) { c.hidden = cat !== 'all' && c.getAttribute('data-cat') !== cat; if (!c.hidden) c.classList.add('is-visible'); });
		};
		btns.forEach(function (b) { b.addEventListener('click', function () { apply(b.getAttribute('data-cat')); }); });
		var hash = (location.hash || '').replace('#', '');
		if (hash && $('[data-cat="' + hash + '"]', bar)) apply(hash);
	});

	/* ---------- Learning Center filters ---------- */
	var kbFilter = function (input, selector, attr) {
		input.addEventListener('input', function () {
			var q = input.value.trim().toLowerCase();
			$$(selector).forEach(function (el) {
				var hay = ((el.getAttribute(attr) || '') + ' ' + el.textContent).toLowerCase();
				el.hidden = !!q && hay.indexOf(q) === -1;
				if (!el.hidden) el.classList.add('is-visible');
			});
		});
	};
	$$('[data-yr-kb-filter]').forEach(function (i) { kbFilter(i, '.yr-kbcard', 'data-kb'); });
	$$('[data-yr-gloss-filter]').forEach(function (i) { kbFilter(i, '.yr-gloss__item', 'data-term'); });

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
})();
