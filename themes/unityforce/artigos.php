<?php

	if (!$Read) {
		$Read = new Read;
	}

	$Read->ExeRead(DB_CATEGORIES, "WHERE category_name = :nm", "nm={$URL[1]}");
	if (!$Read->getResult()) {
		require REQUIRE_PATH . '/404.php';
		return;
	} else {
		extract($Read->getResult()[0]);
	}
?>
<?php require REQUIRE_PATH .'/inc/blog_banner.inc.php'; ?>

<div class="singleblog-section blogpage-section">
	<div class="container">
		<main class="row wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
			<main class="col-lg-8 col-md-12 col-sm-12 col-12">
				<div class="row">
					<?php
						$Page = (!empty($URL[2]) ? $URL[2] : 1);
						$Pager = new Pager(BASE . "/artigos/{$category_name}/", "<", ">", 5);
						$Pager->ExePager($Page, 10);

						$Read->FullRead("SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u.user_lastname, u.user_genre, u.user_profession FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE p.post_status = 1 AND p.post_date <= NOW() AND (p.post_category = :ct OR FIND_IN_SET(:ct, p.post_category_parent)) AND p.post_author = u.user_id ORDER BY p.post_date DESC LIMIT :limit OFFSET :offset",
							"limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$category_id}");

						if (!$Read->getResult()) {
							$Pager->ReturnPage();
							echo Erro("Ainda não existem posts cadastrados nesta seção. Favor volte mais tarde.",
								E_USER_NOTICE);
						} else {
							foreach ($Read->getResult() as $Post) {
								extract($Post);
								$AuthorName = "{$user_name} {$user_lastname}";
								require REQUIRE_PATH . '/inc/post-feed.php';
							}
						}
					?>
				</div>
				<div class="row mt3">
					<div class="col-lg-12">
						<div class="ind_pagination text-center">
							<?php
								$Pager->ExePaginator(DB_POSTS,
									"WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent))",
									"ct={$category_id}");
								echo $Pager->getPaginator();
							?>
						</div>
					</div>
				</div>
			</main>
			<?php
				require REQUIRE_PATH . '/inc/sidebar.inc.php'; ?>
		</div>
	</div>
</div>
