<?php
/**
 * Title: The Yroc Method (entity graph)
 * Slug: yroc/method
 * Categories: yroc
 */
?>
<!-- wp:html -->
<section class="yr-section yr-dark" id="method">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">The Yroc Method</p><h2>A proven system, <em>not guesswork.</em></h2></div>
			<p class="yr-lead">To AI, your business is a web of signals. Our three-phase method connects every one of them to your brand until AI is confident enough to recommend you. Drag the nodes, or step through the phases.</p>
		</div>
		<div class="yr-method" data-yr-method>
			<div class="yr-msteps" role="tablist" aria-label="The Yroc Method phases">
				<button class="yr-mstep" type="button" role="tab" aria-selected="true"><span class="yr-mstep__n">01</span><span><h3>Identify</h3><p>Entity optimization and structured data make AI certain who you are: your name, services, locations and leadership.</p></span><i class="yr-bar"></i></button>
				<button class="yr-mstep" type="button" role="tab" aria-selected="false"><span class="yr-mstep__n">02</span><span><h3>Verify</h3><p>Using our AI Authority Engine framework, we build the reviews, citations, press and expert content that independently confirm you.</p></span><i class="yr-bar"></i></button>
				<button class="yr-mstep" type="button" role="tab" aria-selected="false"><span class="yr-mstep__n">03</span><span><h3>Recommend</h3><p>Answer-first content, knowledge panels and reputation work turn a trusted entity into the answer AI gives.</p></span><i class="yr-bar"></i></button>
			</div>
			<div class="yr-graph">
				<canvas data-yr-graph aria-label="Interactive map of the signals AI uses to trust a business"></canvas>
				<span class="yr-graph__tip">click a signal to learn why it matters · drag to move</span>
				<div class="yr-graph__hud"><span>AI confidence</span><span class="yr-meter"><i data-meter></i></span><b data-conf>8%</b></div>
				<div class="yr-graph__pop" data-yr-pop role="dialog" aria-modal="false" aria-labelledby="yr-pop-h" hidden>
					<button type="button" class="yr-graph__close" data-yr-pop-close aria-label="Close">×</button>
					<p class="yr-graph__phase" data-yr-pop-phase></p>
					<h3 id="yr-pop-h" data-yr-pop-title></h3>
					<p data-yr-pop-body></p>
				</div>
			</div>
		</div>
		<div class="yr-signals">
			<p class="yr-signals__h">Explore the signals AI uses <span>Click any signal, in the graph or below, to see why it matters.</span></p>
			<?php $phases = array( 'Identify', 'Verify', 'Recommend' ); ?>
			<ul data-yr-signals>
				<?php foreach ( yroc_signals() as $label => $sig ) : ?>
					<li><button type="button" class="yr-signal yr-signal--<?php echo (int) $sig[0]; ?>" data-signal="<?php echo esc_attr( $label ); ?>" data-phase="<?php echo esc_attr( $phases[ $sig[0] ] ); ?>" data-why="<?php echo esc_attr( $sig[1] ); ?>"><?php echo esc_html( $label ); ?></button></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
<!-- /wp:html -->
