<?php
    if (!$Read) {
        $Read = new Read;
    }

    $Read->FullRead("SELECT cert_title, cert_subtitle, cert_name, cert_cover FROM " . DB_CERT . " WHERE cert_status >= :st", "st=1");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    }
?>
<section class="product-banner">
    <div class="container">
        <div class="row">
            <h1>Certificações</h1>
        </div>
    </div>
</section>

<section class="commonSection pdb80 padding-top-50px">
    <div class="container">
        <div class="row">
            <?php
                foreach ($Read->getResult() as $Certification) {
                    extract($Certification);
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= BASE . "/certificacao/{$cert_name}" ?>">
                            <div class="singleService">
                                <div class="serviceThumb">
                                    <img src="<?= BASE . "/tim.php?src=uploads/{$cert_cover}"; ?>&w=1200/2&h=628/2"
                                         alt="<?= $cert_title; ?>" title="<?= $cert_title; ?>">
                                </div>
                                <div class="serviceDetails">
                                    <h2><?= $cert_title; ?></h2>
                                    <p><?= $cert_subtitle; ?></p>
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
