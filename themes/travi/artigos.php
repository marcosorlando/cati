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
<!-- PAGE TITLE -->
<section class="blog-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <h2>
                    <a href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a>
                    <i class="fal fa-book"></i>
                    <a href="<?= BASE; ?>/artigos/<?= $category_name; ?>" title="Ver mais: <?= $category_title ?> em <?= SITE_NAME; ?>"><?= $category_title; ?></a>
                </h2>
            </div>
        </div>
    </div>
</section>
<!-- END PAGE TITLE -->

<section class="commonSection newslistpage">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-md-12 col-lg-8">
                <div class="row bloglistgrid">
                    <?php
                        $Page = (!empty($URL[2]) ? $URL[2] : 1);
                        $Pager = new Pager(BASE . "/artigos/{$category_name}/", "<", ">", 5);
                        $Pager->ExePager($Page, 10);

                        $Read->FullRead("SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u.user_lastname, u.user_genre FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent)) AND post_author = user_id ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
                            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$category_id}");

                        if (!$Read->getResult()) {
                            $Pager->ReturnPage();
                            echo Erro("Ainda Não existe posts cadastrados nesta secão. Favor volte mais tarde :)",
                                E_USER_NOTICE);
                        } else {
                            foreach ($Read->getResult() as $Post) {
                                extract($Post);
                                $BOX = 1;
                                $AuthorName = "{$user_name} {$user_lastname}";
                                require REQUIRE_PATH . '/inc/post.php';
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
            </div>
            <?php require REQUIRE_PATH . '/inc/sidebar.php'; ?>
        </div>
    </div>
</section>