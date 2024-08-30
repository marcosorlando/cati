<?php

    $Search = urldecode($URL[1]);
    $SearchPage = urlencode($Search);

    if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'])) {
        $Read->FullRead("SELECT search_id, search_count FROM " . DB_SEARCH . " WHERE search_key = :key",
            "key={$Search}");

		if ($Read->getResult()) {
            $Update = new Update;
            $DataSearch = ['search_count' => $Read->getResult()[0]['search_count'] + 1];
            $Update->ExeUpdate(DB_SEARCH, $DataSearch, "WHERE search_id = :id",
                "id={$Read->getResult()[0]['search_id']}");
        } else {
            $Create = new Create;
            $DataSearch = [
                'search_key' => $Search,
                'search_count' => 1,
                'search_date' => date('Y-m-d H:i:s'),
                'search_commit' => date('Y-m-d H:i:s')
            ];
            $Create->ExeCreate(DB_SEARCH, $DataSearch);
        }
        $_SESSION['search'][] = $Search;
    }
?>

<!-- PAGE TITLE -->
<section class="blog-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <h2>
                    <a href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a>
                    / Pesquisa por
                    <span title="<?= SITE_NAME; ?>"><?= $Search; ?></span>
                </h2>
            </div>
        </div>
    </div>
</section>
<!-- END PAGE TITLE -->

<!-- POSTS -->
<section class="commonSection newslistpage">
    <div class="container">
        <?php
            $Page = (!empty($URL[2]) ? $URL[2] : 1);
            $Pager = new Pager(BASE . "/pesquisa/{$SearchPage}/", "<i class='fa fa-arrow-alt-left'></i>", "<i class='fa fa-arrow-alt-right'></i>", 5);
            $Pager->ExePager($Page, 12);

            $Read->FullRead("SELECT p.post_title, p.post_name,p.post_tags, p.post_cover, p.post_date, p.post_author, p.post_views, p.post_subtitle, u
    .user_name, u.user_lastname, c.category_title FROM "
                . DB_POSTS . " p, " . DB_USERS . " u, " . DB_CATEGORIES . " c WHERE post_status = 1 AND post_category = category_id AND post_date <= NOW() AND post_author = user_id AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE
                         '%' :s '%' OR post_tags LIKE
                         '%' :s '%'  OR MONTH(post_date) = :s) ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
                "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}");

            if (!$Read->getResult()) {
                $Pager->ReturnPage();
                echo Erro("Não encontramos conteúdo para a palavra-chave <b class='text-extra-dark-gray'>( {$Search} )</b>.",
                    E_USER_NOTICE);

                $Read->FullRead("SELECT p.post_title, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, p.post_subtitle, u.user_lastname, c.category_title FROM "
                    . DB_POSTS . " p, " . DB_USERS . " u, " . DB_CATEGORIES . " c WHERE post_status = 1 AND post_category = category_id AND post_date <= NOW() AND post_author = user_id ORDER BY post_date DESC LIMIT :limit",
                    "limit=3");


                if (!$Read->getResult()) {
                    echo Erro("Ainda Não existe posts cadastrados nesta seção. Favor volte mais tarde.",
                        E_USER_NOTICE);
                } else {
                    echo "<h3>Posts Mais Vistos</h3>";
                    echo "<div class='row'>";
                    foreach ($Read->getResult() as $Post) {
                        extract($Post);
                        $AuthorName = "{$user_name} {$user_lastname}";
                        echo "<div class='col-xl-4 col-md-6 col-lg-4 mb51'>";
                        require REQUIRE_PATH . '/inc/post-index.php';
                        echo "</div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<div class='row'>";
                foreach ($Read->getResult() as $Post) {
                    extract($Post);
                    $Read->FullRead("SELECT user_name, user_lastname FROM " . DB_USERS . " WHERE user_id = :user",
                        "user={$post_author}");
                    $AuthorName = "{$Read->getResult()[0]['user_name']} {$Read->getResult()[0]['user_lastname']}";

                    echo "<div class='col-xl-4 col-md-6 col-lg-4 mb51'>";
                    require REQUIRE_PATH . '/inc/post-index.php';
                    echo "</div>";
                }
                echo "</div>";
            }

        ?>

        <div class="row mt10">
            <div class="col-lg-12">
                <div class="ind_pagination text-center">
                    <?php
                        $Pager->ExePaginator(DB_POSTS, "WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%' OR post_tags LIKE '%' :s '%' OR MONTH(post_date) = :s)", "s={$Search}");
                        echo $Pager->getPaginator();
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END POSTS -->
