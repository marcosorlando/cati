<?php

	if (!$Read) {
		$Read = new Read;
	}

	$Read->ExeRead(DB_POSTS, "WHERE post_name = :nm", "nm={$URL[1]}");
	if (!$Read->getResult()) {
		require REQUIRE_PATH . '/404.php';
		return;
	} else {
		extract($Read->getResult()[0]);
		$Update = new Update;
		$UpdateView = ['post_views' => $post_views + 1, 'post_lastview' => date('Y-m-d H:i:s')];
		$Update->ExeUpdate(DB_POSTS, $UpdateView, "WHERE post_id = :id", "id={$post_id}");

		$Read->FullRead("SELECT category_title, category_name FROM " . DB_CATEGORIES . " WHERE category_id = :id",
			"id={$post_category_parent}");
		$PostCategory = $Read->getResult()[0];
		extract($PostCategory);

		$Read->FullRead("SELECT user_name, user_lastname, user_thumb, user_genre,user_twitter, user_youtube, user_google, user_description FROM " . DB_USERS . " WHERE user_id = :user",
			"user={$post_author}");
		$AuthorName = "{$Read->getResult()[0]['user_name']} {$Read->getResult()[0]['user_lastname']}";
	}
	extract($Read->getResult()[0]);
?>
<!-- PAGE TITLE -->
<section class="blog-banner">
	<div class="container">
		<div class="row align-content-center">
			<div class="col-sm-12 col-md-8">
				<h1><?= $post_title; ?></h1>
			</div>
			<div class="col-sm-12 col-md-4 breadcrumbs">
				<a href="<?= BASE . "/artigos/{$PostCategory['category_name']}"; ?>"
				   title="Ver mais: <?= $PostCategory['category_title']; ?> em <?= SITE_NAME; ?>"><?= Check::getCapilalize($PostCategory['category_title']); ?></a>
			</div>
		</div>
	</div>
</section>
<!-- END PAGE TITLE -->

<!-- POST SECTION -->
<section class="commonSection newsDetailsSection">
	<div class="container ">
		<div class="row">
			<div class="col-xl-8 col-md-12 col-lg-8">
				<div class="newsDetailsArea">
					<?php
						if ($post_video) {
							echo "<div class='embed-container htmlchars'>";
							echo "<iframe id='mediaview' src='https://www.youtube.com/embed/{$post_video}?rel=0&amp;showinfo=0&autoplay=0&origin=" . BASE . "' allowfullscreen></iframe>";
							echo "</div>";
						} else {
							$post_cover = $post_cover ? "uploads/{$post_cover}" : 'admin/_img/no_image.jpg';
							echo "<div class='newsThumb newsGall owl-carousel'>";
							echo "<div class='ntItem'>";
							echo "<img title='{$post_title}' alt='{$post_title}' src='" . BASE . "/tim.php?src={$post_cover}&w=" . IMAGE_W . "&h=" . IMAGE_H . "'>";
							echo "</div></div>";
						}
					?>
					<div class="newsDetails">
						<div class="ndMeta">
                            <span><i class="fa fa-calendar-check"></i>
                                <time datetime="<?= date('Y-m-d', strtotime($post_date)); ?>"
                                      pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y",
		                                strtotime($post_date))); ?></time>
                            </span>
							<span><i class="fa fa-tag"></i><?= $PostCategory['category_title']; ?></span>
							<span><i class="fa fa-user"></i><?= $AuthorName; ?></span>
							<span><i class="fa fa-laptop"></i><?= $post_views; ?> views</span>
						</div>
						<?php
							$WC_TITLE_LINK = $post_title;
							$WC_SHARE_HASH = "#TraviPlasticos";
							$WC_SHARE_LINK = BASE . "/artigo/{$post_name}";
							require './_cdn/widgets/share/share.wc.php';
						?>
						<h2 class="ndTitle"><?= $post_subtitle; ?></h2>
						<div class="nd_content">
							<div class="htmlchars"><?= $post_content; ?></div>
						</div>
						<div class="row mb50">
							<div class="col-xl-12 col-md-12 col-sm-12">
								<div class="ndTags text-left clearfix">
									<h5>Tags:</h5>
									<?php
										$tags = explode(',', $post_tags);
										foreach ($tags as $tag) {
											echo "<a title='{$tag}' href='" . BASE . "/pesquisa/" . urlencode(trim($tag)) . "'>{$tag}</a>";
										}
									?>
								</div>
							</div>
							<?php
								require './_cdn/widgets/share/share.wc.php';
							?>
						</div>
						<div class="clearfix mh1"></div>
						<div class="row mb49">
							<div class="col-lg-12">
								<div class="ndAuthor">
									<div class="ndAuthorInner text-center">
										<img src="<?= BASE . "/uploads/{$user_thumb}"; ?>"
										     alt="<?= $AuthorName ?>"
										     title="<?= $AuthorName ?>"/>
										<h3><?= $AuthorName ?></h3>
										<p><?= $user_description; ?></p>
									</div>
								</div>
							</div>
						</div>
						<!-- COMMENTS -->
						<div class="entry-comments mt-20">
							<h3 class="heading relative heading-small uppercase bottom-line style-2 left-align mb-40"></h3>

							<?php
								if (APP_COMMENTS && COMMENT_ON_POSTS) {
									$CommentKey = $post_id;
									$CommentType = 'post';
									require '_cdn/widgets/comments/comments.php';
								}
							?>
						</div>
						<!--  END COMMENTS -->

						<!-- RELATED POSTS -->
						<?php
							$Read->ExeRead(DB_POSTS,
								"WHERE post_status = 1 AND post_date <= NOW() AND post_category_parent != :ct AND post_id != :id ORDER BY post_date DESC LIMIT 3",
								"ct={$post_category_parent}&id={$post_id}");

							if ($Read->getResult()) {
								echo "<div class='related-posts mt-40'>";
								echo "<h3 class='heading relative heading-small uppercase bottom-line style-2 left-align mb-30'>
                                    Posts Relacionados</h3>";
								echo "<div class='row'>";
								foreach ($Read->getResult() as $Post) {
									extract($Post);
									require REQUIRE_PATH . '/inc/post.php';
								}

								echo '</div></div>';
							}
						?>
						<!-- END RELATED POSTS -->
					</div>
				</div>
			</div>
			<?php
				require REQUIRE_PATH . '/inc/sidebar.php'; ?>
		</div>
	</div>
</section>
