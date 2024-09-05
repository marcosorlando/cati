<div class="col-xl-6 col-lg-6 col-md-12" data-aos="fade-up" data-aos-duration="700">
	<div class="blog-box twocolumn-blog float-left w-100 post-item mb-4">
		<div class="post-item-wrap position-relative">
			<div class="post-image">
				<a href="<?= BASE . "/artigo/{$post_name}"; ?>" title="<?= $post_title . " - Clique para continuar a leitura..."; ?>">
					<img src="<?= BASE . "/tim.php?src=uploads/{$post_cover}&w=" . IMAGE_W / 2 . "&h=" . IMAGE_H / 2;
					?>" alt="<?= $post_title; ?>" title="<?= $post_title; ?> loading="lazy"">
				</a>
			</div>
			<div class="lower-portion">
				<i class="fas fa-user"></i>
				<span class="text-size-14 text-mr"><?= $user_name; ?></span>
				<i class="tag-mb fas fa-user-astronaut"></i>
				<span class="text-size-14"><?= $user_profession; ?></span>
				<i class="tag-mb fas fa-tag"></i>
				<span class="text-size-14"><?= $category_title; ?></span>

				<p class="mb-0 text-size-16"><?= $post_title; ?></p>
			</div>
			<div class="button-portion loadone_twocol">
				<div class="date">
					<i class="mb-0 calendar-ml fas fa-calendar-alt"></i>
					<span class="mb-0 text-size-14"><time datetime="<?= date('Y-m-d', strtotime($post_date)); ?>"
					                                      pubdate="pubdate"><?= utf8_encode(strftime(" %d de %b de %Y",
								strtotime($post_date))); ?></time></span>
				</div>
				<div class="button">
					<a class="btn btn-info" href="<?= BASE . "/artigo/{$post_name}"; ?>" title="<?= $post_title . " - Clique para continuar a leitura..."; ?>"> Leia Mais <i class="far fa-eye"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>
