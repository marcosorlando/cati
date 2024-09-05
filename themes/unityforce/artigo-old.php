<?php

	if (!$Read) {
		$Read = new Read;
	}

	$Read->ExeRead(DB_POSTS, "WHERE post_name = :nm", "nm={$URL[1]}");
	if (!$Read->getResult()) {
		require REQUIRE_PATH . '/404.php';
		return;
	} else {
		//var_dump($Read->getResult());
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
<!-- PAGE TITLE -->
<section class="sub-banner-section w-100">
	<div class="container-fluid">
		<div class="sub-banner-inner-con">
			<div class="sub-banner-left-con">
				<h1 data-aos="fade-up" data-aos-duration="700">VOTE 11112</h1>
				<p data-aos="fade-up" data-aos-duration="700">Welcome To The Heart Of Democracy, Where Every Voice
					Resonates And Every Vote Shapes The Destiny Of Our Nation.</p>
				<nav aria-label="breadcrumb" data-aos="fade-up" data-aos-duration="700">
					<ol class="breadcrumb d-inline-block">
						<li class="breadcrumb-item d-inline-block"><a href="<?= BASE ?>">HOME</a></li>
						<li class="breadcrumb-item active text-white d-inline-block"
						    aria-current="page"><?= Check::getCapilalize($PostCategory['category_title']); ?></li>
					</ol>
				</nav>
			</div>
			<div class="sub-banner-right-con">
				<div class="banner2-right-top-txt">
					<span class="d-inline-block">“Stands For A Better, <br> United Tomorrow.”</span>
				</div>
				<figure class="mb-0">
					<img src="<?= INCLUDE_PATH ?>/assets/images/sub-banner-right-man-img.png"
					     alt="sub-banner-right-man-img">
				</figure>
			</div>
		</div>
	</div>
</section>


<!-- POST SECTION -->
<section class="singleblog-section blogpage-section">
	<div class="container">
		<div class="row mt-4">
			<div class="col-lg-8 col-md-12 col-sm-12 col-8">
				<div class="main-box">
					<?php
						if ($post_video) {
							echo "<div class='embed-container htmlchars'>";
							echo "<iframe id='mediaview' src='https://www.youtube.com/embed/{$post_video}?rel=0&amp;showinfo=0&autoplay=0&origin=" . BASE . "' allowfullscreen></iframe>";
							echo "</div>";
						} else {
							$post_cover = $post_cover ? "uploads/{$post_cover}" : 'admin/_img/no_image.jpg';
							echo "<figure class='image1' data-aos='fade-up' data-aos-duration='700'>";
							echo "<img title='{$post_title}' alt='{$post_title}' src=\"{$baseDir}/tim.php?src={$post_cover}&w=" . IMAGE_W . "&h=" . IMAGE_H . "\">";
							echo "</figure";
						}
					?>
					<div class="content1" data-aos="fade-up" data-aos-duration="700">
						<h4><?= $post_title; ?></h4>
						<i class="fa-solid fa-user"></i>
						<span class="text-size-14 text-mr">Por : <?= $AuthorName; ?></span>
						<i class="mb-0 calendar fa-solid fa-calendar-days"></i>
						<span class="mb-0 text-size-14"><time datetime="<?= date('Y-m-d', strtotime($post_date)); ?>"
						                                      pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y",
									strtotime($post_date))); ?></time></span>
						<span><i class="fa fa-tag"></i><?= $PostCategory['category_title']; ?></span>
						<span><i class="fa fa-user"></i><?= $AuthorName; ?></span>
						<span><i class="fa fa-laptop"></i><?= $post_views; ?> views</span>

					</div>

					<div class="newsDetails">

						<?php
							$WC_TITLE_LINK = $post_title;
							$WC_SHARE_HASH = "#CatianeVereadora";
							$WC_SHARE_LINK = BASE . "/artigo/{$post_name}";
							require './_cdn/widgets/share/share.wc.php';
						?>

						<div class="content2" data-aos="fade-up" data-aos-duration="700">
							<h2 class="ndTitle"><?= $post_subtitle; ?></h2>
						</div>


						<div class="content3">
							<div class="htmlchars"><?= $post_content; ?></div>
						</div>
						<div class="content4" data-aos="fade-up" data-aos-duration="700">
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
						</div>

						<div class="content5" data-aos="fade-up" data-aos-duration="700">
							<figure class="singleblog-review1 mb-0">
								<img src="<?= BASE . "/uploads/{$user_thumb}"; ?>"
								     alt="<?= $AuthorName ?>"
								     title="<?= $AuthorName ?>" class="img-fluid"
								     loading="lazy"/>

							</figure>
							<div class="content">
								<h4><?= $AuthorName ?></h4>
								<span class="text-size-16"><?= $user_profession; ?></span>
								<p class="text-size-14"><?= $user_description; ?></p>
							</div>
						</div>

						<!-- COMMENTS -->
						<div class="content6" data-aos="fade-up" data-aos-duration="700">
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
							/*$Read->ExeRead(DB_POSTS,
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
							}*/

						?>
						<!-- END RELATED POSTS -->
					</div>
				</div>

				<div class="col-lg-4 col-md-12 col-sm-12 col-12 column">

					<div class="box1" data-aos="fade-up" data-aos-duration="700">
						<h5>Pesquisar artigos</h5>

						<form class="searchForms" name="search" action="" method="post">
							<div class="form-row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<input type="search" name="s" id="search" class="form-control upper_layer"
									       placeholder="Pesquisar artigos...">
									<div class="input-group-append form-button">
										<button type="submit" class="btn search" name="btnsearch" id="searchbtn"><i
													class="fa-solid fa-magnifying-glass"></i></button>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="box1 box2" data-aos="fade-up" data-aos-duration="700">
						<h5>Categorias</h5>

						<?php
							$Read = new Read();
							$Read->ExeRead(DB_CATEGORIES,
								"WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM " . DB_POSTS . " WHERE post_status <> 0 AND post_date <= NOW()) ORDER BY category_title ASC");
							if (!$Read->getResult()) {
								echo Erro("Ainda não existem sessões cadastradas!", E_USER_NOTICE);
							} else {
								echo "<ul class='list-unstyled mb-0'>";
								foreach ($Read->getResult() as $Ses) {
									echo "<li class='text-size-16'><a title='artigos/{$Ses['category_name']}' href='" . BASE . "/artigos/{$Ses['category_name']}'></a><i class='fa fa-tag'></i> {$Ses['category_title']}";


									$Read->ExeRead(DB_CATEGORIES,
										"WHERE category_parent = :pr AND category_id IN(SELECT post_category_parent FROM " . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW()) ORDER BY category_title ASC",
										"pr={$Ses['category_id']}");
									if ($Read->getResult()) {
										echo "<ul class='list-unstyled mb-0'>";

										foreach ($Read->getResult() as $Cat) {
											echo "<li class='text-size-14'><a title='artigos/{$Cat['category_name']}' href='" .
												BASE . "/artigos/{$Cat['category_name']}'><i class='fal fa-tags'></i> {$Cat['category_title']}</a></li>";
										}
										echo "</ul>";
									}
									echo "</li>";
								}
								echo "</ul>";
							}
						?>
					</div>

					<div class="box1 box3" data-aos="fade-up" data-aos-duration="700">
						<h5>Siga-me</h5>
						<div class="social-icons">
							<ul class="mb-0 list-unstyled ">
								<li><a href="https://www.linkedin.in/<?= SITE_SOCIAL_LINKEDIN ?>"
								       class="text-decoration-none"><i
												class="fa-brands fa-linkedin-in social-networks"></i></a>
								</li>
								<li>
									<a href="https://instagram.com/<?= SITE_SOCIAL_INSTAGRAM ?>"
									   class="text-decoration-none"><i
												class="fa-brands fa-instagram social-networks"></i></a></li>
								<li><a href="https://www.facebook.com/<?= SITE_SOCIAL_FB_PAGE ?>"
								       class="text-decoration-none"><i
												class="fa-brands fa-facebook-f social-networks"></i></a>
								</li>
								<li>
									<a href="https://twitter.com/i/flow/login?input_flow_data=%7B%22requested_variant%22%3A%22eyJsYW5nIjoiZW4ifQ%3D%3D%22%7D"
									   class="text-decoration-none"><i
												class="fa-brands fa-twitter social-networks"></i></a></li>

							</ul>
						</div>
					</div>

					<div class="box1 box4" data-aos="fade-up" data-aos-duration="700">
						<h5>Tags</h5>
						<ul class="tag mb-0 list-unstyled">
							<li><a class="button text-decoration-none" href="about.html">Assistant</a></li>
							<li><a class="button button2 text-decoration-none" href="about.html">Advice</a></li>
							<li><a class="button button3 text-decoration-none" href="about.html">Virtual</a></li>
							<li><a class="button button4 text-decoration-none" href="about.html">Designer</a></li>
							<li><a class="button button5 text-decoration-none" href="about.html">Blog</a></li>
							<li><a class="button button6 text-decoration-none" href="about.html">Support</a></li>
							<li><a class="button button7 text-decoration-none" href="about.html">Finance</a></li>
							<li><a class="button button8 text-decoration-none" href="about.html">Projects</a></li>
						</ul>
					</div>

					<div class="box1 box5" data-aos="fade-up" data-aos-duration="700">
						<h5>Mais lidos</h5>

						<?php
							$Read->ExeRead(DB_POSTS,
								"WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_views DESC, post_date DESC LIMIT 5");
							if (!$Read->getResult()) {
								echo Erro("Não existem artigos cadastrados. Favor volte mais tarde :)", E_USER_NOTICE);
							} else {
								foreach ($Read->getResult() as $Post) {
									$Post['post_cover'] = $Post['post_cover'] ? "uploads/{$Post['post_cover']}" : 'admin/_img/no_image.jpg';

									echo "<div class='feed'><a href='" . BASE . "/artigo/{$Post['post_name']}'
                                   title='Ler mais sobre {$Post['post_title']}'>";
									echo "<figure class='feed-image mb-0' data-aos='fade-up'>";
									echo "<img src='" . BASE . "/tim.php?src={$Post['post_cover']}&w=115&h=62' alt='{$Post['post_title']}' class='img-fluid' loading='lazy' >";
									echo "</figure>";

									echo "<h6>{$Post['post_title']}</h6>";
									echo "</a></div>";
								}
							}
						?>

						<!--<div class="feed">
							<figure class='feed-image mb-0' data-aos='fade-up'>
								<img src="./assets/images/singleblog-feed1.jpg" alt="" class="img-fluid" loading="lazy">
							</figure>
							<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
								Company</a>
						</div>
						<div class="feed">
							<figure class="feed-image mb-0" data-aos="fade-up">
								<img src="./assets/images/singleblog-feed2.jpg" alt="" class="img-fluid" loading="lazy">
							</figure>
							<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
								Company</a>
						</div>
						<div class="feed">
							<figure class="feed-image mb-0" data-aos="fade-up">
								<img src="./assets/images/singleblog-feed3.jpg" alt="" class="img-fluid" loading="lazy">
							</figure>
							<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
								Company</a>
						</div>
						<div class="feed feed4">
							<figure class="feed-image mb-0" data-aos="fade-up">
								<img src="./assets/images/singleblog-feed4.jpg" alt="" class="img-fluid" loading="lazy">
							</figure>
							<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
								Company</a>
						</div>-->
					</div>

					<div class="box1 box6" data-aos="fade-up" data-aos-duration="700">
						<h5>Arquivo Mensal</h5>
						<ul>
							<?php
								$Read->FullRead("SELECT DISTINCT post_month FROM " . DB_POSTS . " WHERE post_status = :st AND post_date <= NOW() ORDER BY post_month ASC LIMIT 12",
									"st=1");

								if ($Read->getResult()) {
									foreach ($Read->getResult() as $MesAno) {
										$Pesquisa = BASE . '/pesquisa/' . $MesAno['post_month'];
										echo "<li><a href='{$Pesquisa}?month' title='Ver artigos publicados neste mês.' ><i class='fal fa-calendar'></i> " . getWcMonths($MesAno['post_month']) . "</a></li>";

									}
								}
							?>
						</ul>
					</div>

				</div>

			</div>
		</div>
</section>
