<?php

	$Post = new Read();
	$Post->FullRead(
		"SELECT p.post_date, p.post_name, p.post_title, p.post_cover, c.category_title FROM " . DB_POSTS . " p, " . DB_CATEGORIES . " c WHERE p.post_category = c.category_id AND p.post_status = :st AND p.post_date < NOW() ORDER BY p.post_date DESC LIMIT :limit",
		"st=1&limit=3"
	);

	if ($Post->getResult()) {
		?>
		<section class="latest-blogs-section w-100 float-left padding-top padding-bottom">
			<div class="container">
				<div class="blog-inner-con">
					<div class="generic-title text-center">
						<span class="small-txt d-block" data-aos="fade-up" data-aos-duration="700">NOTÍCIAS E ATUALIZAÇÕES</span>
						<h2 data-aos="fade-up" data-aos-duration="700">ÚLTIMAS NOTÍCIAS E ARTIGOS</h2>
					</div>
					<div class="blog-boxes">
						<?php
							foreach ($Post->getResult() as $Post) {
								extract($Post);
								require REQUIRE_PATH . '/inc/post-index.php';
							}
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	} ?>
