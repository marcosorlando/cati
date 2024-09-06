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
		$baseDir = BASE;
		$Update = new Update;
		$UpdateView = ['post_views' => $post_views + 1, 'post_lastview' => date('Y-m-d H:i:s')];
		$Update->ExeUpdate(DB_POSTS, $UpdateView, "WHERE post_id = :id", "id={$post_id}");

		$Read->FullRead("SELECT category_title, category_name FROM " . DB_CATEGORIES . " WHERE category_id = :id",
			"id={$post_category_parent}");
		$PostCategory = $Read->getResult()[0];
		extract($PostCategory);

		$Read->FullRead("SELECT user_name, user_lastname, user_thumb, user_genre, user_facebook, user_youtube, user_instagram, user_description, user_profession FROM " . DB_USERS . " WHERE user_id = :user",
			"user={$post_author}");

		$AuthorName = "{$Read->getResult()[0]['user_name']} {$Read->getResult()[0]['user_lastname']}";
		extract($Read->getResult()[0]);
	}
?>
<?php require REQUIRE_PATH .'/inc/blog_banner.inc.php'; ?>
<!-- Single Blog -->
<section class="singleblog-section blogpage-section">
	<div class="container">
		<div class="row">
			<main class="col-lg-8 col-md-12 col-sm-12 col-12">
				<div class="main-box">
					<?php
						if ($post_video) {
							echo "<div class='embed-container htmlchars'>";
							echo "<iframe id='mediaview' src='https://www.youtube.com/embed/{$post_video}?rel=0&amp;showinfo=0&autoplay=0&origin=" . BASE . "' allowfullscreen></iframe>";
							echo "</div>";
						} else {
							$post_cover = $post_cover ? "uploads/{$post_cover}" : 'admin/_img/no_image.jpg';
							echo "<figure class='image1' data-aos='fade-up' data-aos-duration='700'>";
							echo "<img src='{$baseDir}/tim.php?src={$post_cover}&w=1200&h=628' class='img-fluid' loading='lazy'>";
							echo "</figure>";
						}
					?>

					<div class="content1" data-aos="fade-up" data-aos-duration="700">
						<h4><?= $post_title; ?></h4>
						<span class="text-size-14 text-mr">Por: <i class="fas fa-user"></i> <?= $AuthorName;
							?></span>
						<i class="mb-0 calendar fas fa-calendar"></i>
						<span class="mb-0 text-size-14"><time datetime="<?= date('Y-m-d', strtotime($post_date)); ?>"
						                                      pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y",
									strtotime($post_date))); ?></time></span>
						<span><i class="fa fa-tag"></i><?= $PostCategory['category_title']; ?></span>
						<span><i class="fa fa-eye"></i><?= $post_views; ?> views</span>
					</div>

					<div class="htmlchars" data-aos="fade-up" data-aos-duration="700">
						<?= $post_content; ?>
					</div>

					<div class="content4" data-aos="fade-up" data-aos-duration="700">
						<div class="row">
							<div class="col-12">
								<div class="tag">
									<h5>Palavras-chave relacionadas</h5>
									<ul class="mb-0 list-unstyled ">
										<?php
											$tags = explode(',', $post_tags);
											foreach ($tags as $tag) {
												echo "<li><a class='button text-decoration-none' title='Pesquisar por {$tag}' href='"
													. BASE . "/pesquisa/" . urlencode
													(trim($tag)) . "'>{$tag}</a></li>";
											}
										?>
									</ul>
								</div>
							</div>

							<div class="col-12 mt-3">
								<?php
									$WC_TITLE_LINK = $post_title;
									$WC_SHARE_HASH = SITE_SOCIAL_HASHTAG;
									$WC_SHARE_LINK = BASE . "/artigo/{$post_name}";
									require './_cdn/widgets/share/share.wc.php';
								?>
							</div>
						</div>
					</div>

					<div class="content5" data-aos="fade-up" data-aos-duration="700">
						<figure class="singleblog-review1 mb-0">
							<img src="<?= "{$baseDir}/tim.php?src=uploads/{$user_thumb}&w=132&h=132" ?>"
							     alt="<?= $AuthorName ?>"
							     class="img-fluid"
							     loading="lazy">
						</figure>
						<div class="content">
							<h4><?= $AuthorName ?></h4>
							<span class="text-size-16"><?= $user_profession ?></span>
							<p class="text-size-14"><?= $user_description ?></p>
						</div>
					</div>

					<div class="content6" data-aos="fade-up" data-aos-duration="700">
						<?php
							if (APP_COMMENTS && COMMENT_ON_POSTS) {
								$CommentKey = $post_id;
								$CommentType = 'post';
								require '_cdn/widgets/comments/comments.php';
							}
						?>
					</div>
					<?php
						$Read->ExeRead(DB_POSTS,
							" WHERE post_status = 1 AND post_date <= NOW() AND post_category_parent != :ct AND post_id != :id ORDER BY post_date DESC LIMIT 3",
							"ct={$post_category_parent}&id={$post_id}");

						if ($Read->getResult()) {
							echo "<div class='content7' data-aos='fade-up' data-aos-duration='700'> <h4><i class='fa fa-tags'></i> Artigos Relacionados</h4> 
<div class='row blog-boxes'>";

							foreach ($Read->getResult() as $Post) {
								extract($Post);
								require REQUIRE_PATH . '/inc/post-index.php';
							}
							echo "</div> </div>";
						}

					?>


				</div>
			</main>
			<?php require 'inc/sidebar.inc.php' ?>
		</div>
	</div>
</section>
