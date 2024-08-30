<div class="singleBlog">
    <div class="sbThumb">
        <a href="<?= BASE . "/artigo/{$post_name}"; ?>" title="<?= $post_title . " - Clique para continuar a leitura..."; ?>">
            <img src="<?= BASE . "/tim.php?src=uploads/{$post_cover}&w=" . IMAGE_W / 2 . "&h=" . IMAGE_H / 2; ?>"
             alt="<?= $post_title; ?>" title="<?= $post_title; ?>"/>
        </a>
    </div>
    <div class="sbDetails">
        <h4 class="sb_cats">
            <a href="<?= BASE . "/artigo/{$post_name}"; ?>"><?= $post_title; ?></a>
        </h4>
        <p>
            <?= Check::Chars($post_subtitle, 70); ?>
            <a href="<?= BASE . "/artigo/{$post_name}"; ?>"><i class='fa fa-eye'></i> Continue lendo...</a>
        </p>
    </div>
    <div class="sb_footer">
        <span><i class="fal fa-folder-open"></i><?= $category_title; ?></span>
        <span><i class="fal fa-user"></i><?= $AuthorName; ?></span>
        <span><i class="fal fa-eye"></i><?= $post_views; ?></span>
    </div>
</div>
