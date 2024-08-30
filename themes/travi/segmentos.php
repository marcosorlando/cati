<?php
    if (!$Read) {
        $Read = new Read;
    }

    $Read->FullRead("SELECT seg_title, seg_subtitle, seg_name, seg_cover FROM " . DB_SEG . " WHERE seg_status >= :st", "st=1");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    }
?>
<section class="product-banner">
    <div class="container">
        <div class="row">
            <h1>Segmentos</h1>
        </div>
    </div>
</section>

<section class="commonSection pdb80 padding-top-50px">
    <div class="container">
        <div class="row">
            <?php
                foreach ($Read->getResult() as $Service) {
                    extract($Service);
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= BASE . "/segmento/{$seg_name}" ?>">
                            <div class="singleService">
                                <div class="serviceThumb">
                                    <img src="<?= BASE . "/tim.php?src=uploads/{$seg_cover}"; ?>&w=1200/2&h=628/2"
                                         alt="<?= $seg_title; ?>" title="<?= $seg_title; ?>">
                                </div>
                                <div class="serviceDetails">
                                    <h2><?= $seg_title; ?></h2>
                                    <p><?= $seg_subtitle; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            ?>
        </div>
    </div>
</section>
