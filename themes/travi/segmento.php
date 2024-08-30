<?php

	if (!$Read) {
		$Read = new Read;
	}

	$Read->ExeRead(DB_SEG, "WHERE seg_name = :nm AND seg_status = :st", "nm={$URL[1]}&st=1");

	if (!$Read->getResult()) {
		require REQUIRE_PATH . '/404.php';
		return;
	} else {
		extract($Read->getResult()[0]);
		$Update = new Update;
		$UpdateView = [
			'seg_views' => $seg_views + 1,
			'seg_lastview' => date('Y-m-d H:i:s')
		];
		$Update->ExeUpdate(DB_SEG, $UpdateView, "WHERE seg_id = :id", "id={$seg_id}");
	}
?>

<section class="product-banner">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<h2><?= $seg_title; ?></h2>
			</div>
			<div class="col-sm-12 col-md-6 breadcrumbs">
				<a href="<?= BASE; ?>">Home</a><i>|</i>
				<a href="<?= BASE . "/segmentos"; ?>" title="Segmentos da Indústria">Segmentos da Indústria</a>
			</div>
		</div>
	</div>
</section>

<section class="commonSection serviceDetailsSecions">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-12">
				<div class="service_details_area">
					<h2 class="entry_title">
						<?= $seg_title; ?>
					</h2>
					<div class="sda_gall">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="sda_gl">
									<img src="<?= ($seg_cover ? BASE . "/tim.php?src=uploads/{$seg_cover}&w=690&h=690" : BASE . "/tim.php?src=admin/_img/no_image.jpg") . "&w=690&h=690"; ?>"
									     alt="<?= $seg_title ?>" title="<?=
										$seg_title ?>"/>
								</div>
							</div>
						</div>
					</div>
					<div class="sda_content  htmlchars"><?= $seg_description; ?></div>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 sidebar_1">
				<aside class="widget">
					<h3 class="widget_title">Segmentos da Indústria</h3>
					<ul>
						<?php
							$Read->FullRead("SELECT seg_title, seg_name FROM " . DB_SEG . " WHERE seg_status >= :st",
								"st=1");
							if ($Read->getResult()) {
								foreach ($Read->getResult() as $svc) {
									extract($svc);
									echo "<li><a href='" . BASE . "/segmento/$seg_name'>{$seg_title}</a></li>";
								}
							}
						?>
					</ul>
				</aside>
				<?php
					$Read->FullRead("SELECT post_title, post_name, post_date, post_cover FROM " . DB_POSTS . " WHERE post_status = :ps ORDER BY post_date DESC LIMIT 3",
						"ps=1");
					if ($Read->getResult()) {
						echo "<aside class='widget last-news'>";
						echo "<h3 class='widget_title'>Últimas Notícias</h3>";

						foreach ($Read->getResult() as $Post) {
							extract($Post);

							$Post['post_cover'] = $Post['post_cover'] ? "uploads/{$Post['post_cover']}" : 'admin/_img/no_image.jpg';

							echo "<div class='allLatestWorks'>";
							echo "<div class='ltworks'>";
							echo "<a href='" . BASE . "/artigo/{$post_name}'><img class='res' alt='{$post_title}' src='" .
								BASE .
								"/tim.php?src={$Post['post_cover']}&w=128&h=62'></a>";
							echo "<h4><a href='" . BASE . "/artigo/{$post_name}'>{$post_title} </a></h4>";
							echo "<p><i class='fal fa-calendar-check'></i> " . date('d-m-Y',
									strtotime($post_date)) . "</p>";
							echo "</div>";
							echo "</div>";

						}
						echo "</aside>";
					}
				?>
				<aside class="widget havqueswidget">
					<h3 class="widget_title">Alguma dúvida?</h3>
					<div class="hqw_content">
						<p> Entre em contato conosco que responderemos em breve.</p>
						<span><i class="fa fa-envelope text-blue"></i> <a
									href="mailto:<?= SITE_ADDR_EMAIL; ?>"><?= SITE_ADDR_EMAIL; ?></a></span>

						<p> Ou ligue agora!</p>
						<span><i class="fa fa-phone text-blue"></i> <a
									href="tel:<?= SITE_ADDR_PHONE_A; ?>"><?= SITE_ADDR_PHONE_A; ?></a></span>

					</div>
				</aside>
			</div>
		</div>
	</div>
</section>
<?php
	include_once 'inc/cta.inc.php';
?>
