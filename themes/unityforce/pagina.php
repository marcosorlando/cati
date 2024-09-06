<?php

	if (!$Read) {
		$Read = new Read;
	}

	$Email = new Email;

	$Read->ExeRead(DB_PAGES, "WHERE page_name = :nm", "nm={$URL[0]}");
	if (!$Read->getResult()) {
		require REQUIRE_PATH . '/404.php';
		return;
	} else {
		extract($Read->getResult()[0]);
	}
?>

<section class="sub-banner-section w-100">
	<div class="container-fluid">
		<div class="sub-banner-inner-con">
			<div class="sub-banner-left-con">
				<h1 data-aos="fade-up" data-aos-duration="700">SOBRE MIM</h1>
				<p data-aos="fade-up" data-aos-duration="700">Eu Catiane Zanotto, estou comprometida em trabalhar incansavelmente por uma Caxias do Sul mais justa, inovadora e próspera. Conto com o seu apoio para construirmos juntos um futuro brilhante para todos os caxienses.</p>
				<button class="btn btn-info">VOTE 11112</button>
			</div>
			<div class="sub-banner-right-con">
				<div class="banner2-right-top-txt">
					<span class="d-inline-block">“#juntos, <br> por Caxias.”</span>
				</div>
				<figure class="mb-0">
					<img src="<?= INCLUDE_PATH ?>/assets/images/sub-banner-right-man-img.png"
					     alt="sub-banner-right-man-img">
				</figure>
			</div>
		</div>
	</div>
</section>
<!-- BANNER SECTION END HERE -->
<?php
?>
<!-- ABOUT SECTION START HERE -->
<section class="section-page">
	<div class="container">
		<div class="content">
			<div class='htmlchars'>
				<?= isset($page_cover) ? "<img class='cover' title='{$page_title}' alt='{$page_title}' src=' " . BASE . "/tim.php?src=uploads/{$page_cover}&w=" . IMAGE_W . '&h=' . IMAGE_H . "'/>" : ''; ?>

				<div>
					<?= $page_content; ?>
				</div>

			</div>
			<?php
				if (APP_COMMENTS && COMMENT_ON_PAGES) { ?>
					<div class="container" style="background: #fff; padding: 20px 0;">
						<div class="content">
							<?php
								$CommentKey = $page_id;
								$CommentType = 'page';
								require '_cdn/widgets/comments/comments.php';
							?>
							<div class="clear"></div>
						</div>
					</div>
					<?php
				} ?>
		</div>
	</div>
</section>
