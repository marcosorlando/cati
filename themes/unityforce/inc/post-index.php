<div class="blog-single-box">
	<div class="blog-img-con" data-aos="fade-up" data-aos-duration="700">
		<figure class="mb-0">
			<a href="<?= BASE . "/artigo/{$post_name}"; ?>" title="<?= $post_title . " - Clique para continuar a leitura..."; ?>">
			<img src="<?= BASE . "/tim.php?src=uploads/{$post_cover}&w=" . IMAGE_W / 2 . "&h=" . IMAGE_H / 2; ?>" alt="<?= $post_title; ?>" title="<?= $post_title; ?>">
			</a>
		</figure>
		<span class="d-inline-block"><?= $category_title; ?></span>
	</div>
	<div class="blog-txt-box" data-aos="fade-up" data-aos-duration="700">
		<span class="ds_block"><i class="far fa-calendar-alt"></i>
	<time datetime="<?= date('Y-m-d', strtotime($post_date)); ?>" pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y", strtotime($post_date))); ?></time>
		</span>
		<h4><a href="<?= BASE . "/artigo/{$post_name}"; ?>"><?= $post_title; ?></a></h4>
	</div>
</div>
