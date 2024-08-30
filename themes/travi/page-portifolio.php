<?php
    if (!$Read) {
        $Read = new Read;
    }
    
    $BaseDir = BASE;
    $Read->FullRead("SELECT p.pdt_title, p.pdt_name, p.pdt_cover, p.pdt_ref, p.pdt_color, c.cat_title, c.cat_parent FROM " . DB_PDT_TRAVI . " p INNER JOIN " . DB_PDT_CATS_TRAVI . " c WHERE pdt_status = :st AND pdt_subcategory = cat_id",
        "st=1");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

    <!-- PAGE TITLE -->
    <section class="page-title style-3">
        <div class="container relative clearfix">
            <div class="title-holder">
                <div class="title-text">
                    <h1 class="uppercase">Portifólio</h1>
                </div>
            </div>
        </div>
    </section>
    <!-- END PAGE TITLE -->

    <!-- PORTFOLIO-->
    <section class="section-wrap bg-gray">
        <div class="container-fluid">

            <!-- filter -->
            <div class="row">
                <div class="col-md-12">
                    <div class="portfolio-filter style-gray">
                        <?php
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

            <div class="row">
                <div id="portfolio-container" class="works-grid grid-4-col small-gutter with-title">
                    
                    <?php
                        $Time = 0;
                        
                        foreach ($Read->getResult() as $Produtos) {
                            extract($Produtos);
                            $cat_title = Check::Name($cat_title);
                            $Time = $Time + 0.4;
                            ?>
                            <div class="work-item <?= $cat_title; ?> wow fateInUp" data-wow-delay="<?= $Time; ?>s">
                                <a href="<?= $BaseDir; ?>/produto/<?= $pdt_name; ?>" title="Clique para visitar a página do produto">
                                    <div class="work-container">
                                        <div class="work-img">
                                            <img
                                                src="<?= $BaseDir; ?>/tim.php?src=uploads/<?= $pdt_cover; ?>&w=600&h=600"
                                                alt="<?= $pdt_title; ?>" title="<?= $pdt_title; ?>">
                                        </div>
                                        <div class="work-description">
                                            <h2><a href="<?= $BaseDir; ?>/produto/<?= $pdt_name; ?>"
                                                   title="Clique para visitar a página do produto"><?= $pdt_title; ?>
                                                    - <?= $pdt_color; ?></a></h2>
                                            <span><a href="<?= $BaseDir; ?>/produtos/<?= $pdt_name; ?>"
                                                     title="Clique para visitar a página do produto">Ref. <?= $pdt_ref; ?></a></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    ?>

                </div>    <!-- end portfolio container -->
            </div> <!-- end row -->

            <div class="row mt-10">
                <div class="col-md-12 text-center">
                    <?php
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- END PORTFOLIO-->

<?php require REQUIRE_PATH . "/inc/banner-cta.php"; ?>