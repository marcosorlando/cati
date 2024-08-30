<div class="col-xl-4 col-md-12 col-lg-4">
    <div class="sidebar_2">
        <aside class="widget">
            <h3 class="widget_title">Pesquisa</h3>
            <form class="searchForms" name="search" action="" method="post" enctype="multipart/form-data">
                <input type="search" name="s" placeholder="Pesquisar artigos..." autocomplete="off">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </aside>

        <aside class="widget">
            <h3 class="widget_title">Sobre a Travi</h3>
            <p><?= SITE_DESC ?></p>
        </aside>

        <aside class="widget">
            <h3 class="widget_title">Siga nos</h3>
            <div class="socialLinks">
                <?= SITE_SOCIAL_FB_PAGE ? "<a href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "' title='" . SITE_NAME . " no Facebook' target='_blank'><i class='fab fa-facebook'></i></a>" : ""; ?>
                <?= SITE_SOCIAL_TWITTER ? "<a href='https://www.twitter.com/" . SITE_SOCIAL_TWITTER . "' title='" . SITE_NAME . " no Twitter' target='_blank'><i class='fab fa-twitter'></i></a>" : ""; ?>
                <?= SITE_SOCIAL_LINKEDIN ? "<a href='https://www.linkedin.com/company/" . SITE_SOCIAL_LINKEDIN . "' title='" . SITE_NAME . " no Linkedin' target='_blank'><i class='fab fa-linkedin'></i></a>" : ""; ?>
                <?= SITE_SOCIAL_INSTAGRAM ? "<a href='https://www.instagram.com/" . SITE_SOCIAL_INSTAGRAM . "' title='" . SITE_NAME . " no Instagram' target='_blank'><i class='fab fa-instagram'></i></a>" : ""; ?>
                <?= SITE_SOCIAL_YOUTUBE ? "<a href='https://www.youtube.com/user/" . SITE_SOCIAL_YOUTUBE . "' title='" . SITE_NAME . " no Youtube' target='_blank'><i class='fab fa-youtube'></i></a>" : ""; ?>
                <a href="<?= BASE; ?>/rss" class="social-rss" data-toggle="tooltip" data-placement="top"
                   title="Rss da <?= SITE_NAME; ?>" target="_blank"><i class="fa fa-rss"></i></a>
            </div>
        </aside>
        <aside class="widget">
            <h3 class="widget_title">Categorias</h3>
            <?php
                $Read->ExeRead(DB_CATEGORIES,
                    "WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM " . DB_POSTS . " WHERE post_status <> 0 AND post_date <= NOW()) ORDER BY category_title ASC");
                if (!$Read->getResult()) {
                    echo Erro("Ainda não existem sessões cadastradas!", E_USER_NOTICE);
                } else {
                    echo "<ul>";
                    foreach ($Read->getResult() as $Ses) {
                        echo "<li><a title='artigos/{$Ses['category_name']}' href='" . BASE . "/artigos/{$Ses['category_name']}'></a><i class='fa fa-tag'></i> {$Ses['category_title']}";


                        $Read->ExeRead(DB_CATEGORIES,
                            "WHERE category_parent = :pr AND category_id IN(SELECT post_category_parent FROM " . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW()) ORDER BY category_title ASC",
                            "pr={$Ses['category_id']}");
                        if ($Read->getResult()) {
                            echo "<ul>";

                            foreach ($Read->getResult() as $Cat) {
                                echo "<li><a title='artigos/{$Cat['category_name']}' href='" . BASE . "/artigos/{$Cat['category_name']}'><i class='fal fa-tags'></i> {$Cat['category_title']}</a></li>";
                            }
                            echo "</ul>";
                        }
                       echo "</li>";
                    }
                    echo "</ul>";
                }
            ?>
        </aside>
        <aside class="widget">
            <h3 class="widget_title">Mais vistos</h3>
            <div class="allfeeds">
                <?php
                    $Read->ExeRead(DB_POSTS,
                        "WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_views DESC, post_date DESC LIMIT 5");
                    if (!$Read->getResult()) {
                        echo Erro("Ainda não existe artigos cadastrados. Favor volte mais tarde :)", E_USER_NOTICE);
                    } else {
                        foreach ($Read->getResult() as $Post) {

                            $Post['post_cover'] = $Post['post_cover'] ? "uploads/{$Post['post_cover']}" : 'admin/_img/no_image.jpg';

                            echo "<div class='singlefeeds clearfix'><a href='" . BASE . "/artigo/{$Post['post_name']}'
                                   title='Ler mais sobre {$Post['post_title']}'>";
                            echo "<img src='" . BASE . "/tim.php?src={$Post['post_cover']}&w=115&h=62'>";
                            echo "<h6>{$Post['post_title']}</h6>";
                            echo "</a></div>";
                        }
                    }
                ?>
            </div>
        </aside>
        <aside class="widget">
            <h3 class="widget_title">Arquivo</h3>
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
        </aside>
        <!--<aside class="widget">
            <h3 class="widget_title">Instagram</h3>
            <div class="instafeeds clearfix">
                <a href="#"><img src="images/widget/inst/1.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/2.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/3.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/4.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/5.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/6.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/7.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/8.jpg" alt=""/></a>
                <a href="#"><img src="images/widget/inst/9.jpg" alt=""/></a>
            </div>
        </aside>-->
    </div>
</div>
