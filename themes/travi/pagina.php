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

<section class='product-banner'>
	<div class='container'>
		<div class='row'>
			<h1><?= $page_title; ?></h1>
		</div>
	</div>
</section>

<section class='commonSection pdb80 padding-top-50px'>
	<div class='container'>
		<div class='row htmlchars'>
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
</section>
