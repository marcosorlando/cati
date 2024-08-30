<?php

    if (!$Read) {
        $Read = new Read;
    }

    $Read->ExeRead(DB_PDT_CATS_TRAVI, "WHERE cat_name = :nm", "nm={$URL[1]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

<section class="product-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <h2>Produtos</h2>
            </div>
            <div class="col-sm-12 col-md-6 breadcrumbs">
                <a href="<?= BASE; ?>">Home</a><i>|</i>
                <a href="<?= BASE; ?>/produtos/<?= $cat_name; ?>" title="<?= $cat_title; ?>"><?= $cat_title; ?></a>
            </div>
        </div>
    </div>
</section>

<section class="commonSection shopLoopPage padding-top-50px">
    <div class="container">
        <div class="row">
            <div class="col-xl-9 col-md-12 col-lg-9">
                <div class="row htmlchars">
                    <h1 class="text-uppercase"><?= $cat_title; ?></h1>
                    <h2 class="font_medium"><?= $cat_subtitle; ?></h2>
                    <?= $cat_content; ?>
                </div>

                <div class="row products">
                    <header class="title">
                        <h3 class="uppercase">PRODUTOS DA FAMÍLIA <span class="text-red"><?= $cat_title ?></span></h3>
                    </header>

                    <?php
                        $Page = (!empty($URL[2]) ? $URL[2] : 1);
                        $Pager = new Pager(BASE . "/produtos/{$cat_name}/", "<<", ">>", 5);
                        $Pager->ExePager($Page, 10);

                        $Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_TRAVI . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_category = :ct OR FIND_IN_SET(:ct, pdt_subcategory)) ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
                            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$cat_id}");

                        if (!$Read->getResult()) {
                            $Pager->ReturnPage();
                            echo Erro("Não existem produtos cadastrados nesta seção. Por favor, volte mais tarde.",
                                E_USER_NOTICE);
                        } else {
                            foreach ($Read->getResult() as $Post) {
                                extract($Post);
                                $BOX = 1;
                                require REQUIRE_PATH . '/inc/produto.php';
                            }
                        }

                    ?>
                </div>

                <div class="row mt20">
                    <div class="col-lg-12">
                        <div class="ind_pagination text-center">
                            <?php
                                $Pager->ExePaginator(DB_PDT_TRAVI,
                                    "WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_category = :ct OR FIND_IN_SET(:ct, pdt_subcategory))",
                                    "ct={$cat_id}");

                                echo $Pager->getPaginator();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                require REQUIRE_PATH . '/inc/category-sidebar.php'; ?>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        $(".title h3:contains(PRODUTOS DA FAMÍLIA Produtos)").text("TODOS OS PRODUTOS");
    });

</script>
