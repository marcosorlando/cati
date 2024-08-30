<?php
    require REQUIRE_PATH . '/inc/slider-revolution.php';
    require '_cdn/widgets/contact/contact.wc.php';
?>

<!-- ICON BOXES -->
<section class="section-wrap icon-boxes style-1 pb-50 pb-mdm-80">
    <div class="container">

        <div class="row flex-container flex-wrap">

            <div class="icon-flex wow fadeIn" data-wow-duration="2s" data-wow-delay="0.2s">
                <div class="service-item-box text-center icon-effect-1 icon-effect-1a">
                    <i class="hi-icon fa fa-hand-grab-o"></i>
                    <h3>Comprometimento</h3>
                </div>
            </div> <!-- end service item -->

            <div class="icon-flex wow fadeIn" data-wow-duration="2s" data-wow-delay="0.4s">
                <div class="service-item-box text-center icon-effect-1 icon-effect-1a">
                    <i class="hi-icon fa fa-car"></i>
                    <h3>Agilidade</h3>
                </div>
            </div> <!-- end service item -->

            <div class="icon-flex wow fadeIn" data-wow-duration="2s" data-wow-delay="0.6s">
                <div class="service-item-box text-center icon-effect-1 icon-effect-1a">
                    <i class="hi-icon fa fa-bank"></i>
                    <h3>Economia</h3>
                </div>
            </div> <!-- end service item -->

            <div class="icon-flex wow fadeIn" data-wow-duration="2s" data-wow-delay="0.6s">
                <div class="service-item-box text-center icon-effect-1 icon-effect-1a">
                    <i class="hi-icon fa fa-check"></i>
                    <h3>Qualidade</h3>
                </div>
            </div> <!-- end service item -->

        </div>
    </div>
</section>
<!-- END ICON BOXES -->

<!-- PORTFOLIO -->
<section class="section-wrap bg-dark">
    <div class="container-fluid nopadding">

        <h2 class="color-white text-center mb-50 mb-mdm-30">Nossos Produtos</h2>

        <!-- filter -->
        <div class="row">
            <div class="col-md-12">
                <div class="portfolio-filter dark">
                    <?php
                        $BaseDir = BASE;
                        $Read->FullRead("SELECT p.pdt_title, p.pdt_name, p.pdt_cover, p.pdt_ref, p.pdt_color, c.cat_title FROM "
                            . DB_PDT_TRAVI . " p INNER JOIN " . DB_PDT_CATS_TRAVI . " c WHERE pdt_status = :st AND pdt_subcategory = cat_id LIMIT 16",
                            "st=1");
                        //                        var_dump($Read->getResult());
                        if ($Read->getResult()) {
                            $CatList = [];
                            foreach ($Read->getResult() as $Lista) {
                                $CatList[] = $Lista['cat_title'];
                            }

                            $CatList = array_unique($CatList, SORT_REGULAR);

                            echo "<a href='javascript:void(0);' class='filter active' data-filter='*'>Todos</a>";
                            foreach ($CatList as $List => $Key) {
                                $KeyClass = Check::Name($Key);

                                echo "<a href='javascript:void(0);' class='filter' data-filter='.{$KeyClass}'>{$Key}</a>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div> <!-- end filter -->


        <div id="portfolio-container" class="works-grid small-gutter masonry grid-4-col">

            <?php
                $Time = 0;
                foreach ($Read->getResult() as $Produtos) {
//                    var_dump($Produtos);
                    extract($Produtos);
                    $cat_title = Check::Name($cat_title);
                    $Time = $Time + 0.4;
                    ?>
                    <div class="work-item masonry-item <?= $cat_title; ?> wow fadeInUp" data-wow-delay="<?= $Time; ?>s">
                        <div class="work-container">
                            <div class="work-img">
                                <img src="<?= $BaseDir; ?>/tim.php?src=uploads/<?= $pdt_cover; ?>&w=600&h=600"
                                     alt="<?= $pdt_title; ?>" title="<?= $pdt_title; ?>">
                                <div class="work-overlay">
                                    <div class="project-icons">
                                        <a href="<?= $BaseDir; ?>/tim.php?src=uploads/<?= $pdt_cover; ?>&w=800&h=800"
                                           class="lightbox-gallery" title="<?= $pdt_title; ?>"><i
                                                class="fa fa-search"></i></a>
                                        <a href="<?= $BaseDir; ?>/produto/<?= $pdt_name; ?>" class="project-icon"
                                           title="<?= $pdt_title; ?>"><i class="fa fa-link"></i></a>
                                    </div>
                                </div>
                                <div class="work-description">
                                    <h2><a href="<?= $BaseDir; ?>/produto/<?= $pdt_name; ?>"
                                           title="Clique para visitar a página do produto"><?= $pdt_title; ?> - <?= $pdt_color; ?></a></h2>
                                    <span><a href="<?= $BaseDir; ?>/produto/<?= $pdt_name; ?>"
                                             title="Clique para visitar a página do produto">Ref. <?= $pdt_ref; ?></a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            ?>

        </div>    <!-- end portfolio container -->

        <div class="row mt-40">
            <div class="col-md-12 text-center">
                <a href="<?= BASE; ?>/portifolio" class="btn btn-lg btn-dark" id="load-more">Mais...</a>
            </div>
        </div>
    </div>
</section>
<!-- END PORTFOLIO -->

<?php require REQUIRE_PATH . "/inc/depositions.php"; ?>

<!-- PARTNERS -->
<section class="section-wrap bg-light partners-light pt-10 pb-10">
    <div class="container">
        <div class="row">

            <div id="owl-partners" class="owl-carousel owl-theme">

                <?php
                    $Read->FullRead("SELECT * FROM " . DB_PARTNERS);

                    if (!$Read->getResult()) {
                        Erro("Ainda não existem parceiros cadastrados. Favor volte mais trade:", E_USER_NOTICE);
                    } else {
                        foreach ($Read->getResult() as $Partner) {
                            extract($Partner);
                            ?>
                            <div class="item">
                                <a href="<?= $partner_page; ?>" title="<?= $partner_name; ?>">
                                    <img class="partners" src="<?= BASE; ?>/tim.php?src=uploads/<?= $partner_image; ?>&w=180&h=120"
                                         alt="<?= $partner_name;?>" title="Logo | <?= $partner_name; ?>">
                                </a>
                            </div>
                            <?php
                        }
                    }
                ?>

            </div> <!-- end carousel -->

        </div>
    </div>
</section>
<!-- END PARTNERS -->

<!-- CALL TO ACTION -->
<?php require REQUIRE_PATH . "/inc/banner-cta.php"; ?>
<!-- END CALL TO ACTION -->