<!-- RELATED PRODUCTS -->
<section class="section-wrap related-products pt-30">
    <div class="container">
        <h3 class="heading text-center relative heading-small uppercase bottom-line style-2 mb-50">Produtos
            Relacionados</h3>
        <div class="row">
            <div id="owl-related-works" class="owl-carousel owl-theme">
                
                <?php
                    $Read->FullRead("SELECT pdt_name, pdt_title, pdt_ref, pdt_cover, pdt_color FROM " . DB_PDT_TRAVI . " WHERE pdt_subcategory = :pc AND pdt_id != :pid ORDER BY pdt_title ASC",
                        "pc={$pdt_subcategory}&pid={$pdt_id}");
                    
                    if (!$Read->getResult()) {
                        echo ERRO("Ainda não existem produtos relacionados cadastrados. Favor volte mais tarde :",
                            E_USER_NOTICE);
                    } else {
                        foreach ($Read->getResult() as $Product) {
                            extract($Product);
                            ?>
                            <div class="work-item">
                                <a href="<?= BASE ?>/produto/<?= $pdt_name ?>"
                                   title="<?= $pdt_title; ?> | Clique para visitar a página do produto">
                                    <div class="work-container">
                                        <div class="work-img">
                                            <img src="<?= BASE; ?>/tim.php?src=uploads/<?= $pdt_cover; ?>&w=360&h=360"
                                                 alt="<?= $pdt_title; ?>">
                                            <div class="work-description">
                                                <h2><a><?= $pdt_title; ?> - <?= $pdt_color; ?></a></h2>
                                                <span><a>Ref. <?= $pdt_ref; ?></a></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div> <!-- end work-item -->
                            <?php
                        }
                    }
                ?>
            </div> <!-- end owl -->

        </div> <!-- end row -->
    </div> <!-- end container -->
</section><!-- END RELATED PRODUCTS -->