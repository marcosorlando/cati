<div class="col-xl-6 col-md-6 col-lg-6 blog_mash">
    <div class="singleBlog">
        <div class="sbThumb">
            <a href="<?= BASE ."/artigo/{$post_name}"; ?>" title="<?= $post_title; ?>">
                <img src="<?= BASE . "/tim.php?src=uploads/{$post_cover}&w=360&h=188"; ?>"
                     alt="<?= $post_title; ?>" title="<?= $post_title; ?>"/>
            </a>
        </div>
        <div class="sbDetails">
            <h4 class="sb_cats">
                <a href="<?= BASE . "/artigo/{$post_name}"; ?>"><?= $post_title; ?></a>
            </h4>
            <p>
                <?= Check::Chars($post_subtitle, 70); ?>
                <a href="<?= BASE . "/artigo/{$post_name}"; ?>" title="<?= $post_title; ?>">Continue Lendo <i class="fa fa-angle-right"></i></a>
            </p>
        </div>
        <div class="sb_footer">
            <span><i class="fal fa-clock"></i>
                <time datetime="<?= date('Y-m-d', strtotime($Post['post_date'])); ?>" pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y", strtotime($Post['post_date']))); ?></time>
            </span>
            <span><i class="fal fa-user"></i><?= $AuthorName; ?></span>
        </div>
    </div>
</div>