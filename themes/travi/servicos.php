<?php
    if (!$Read) {
        $Read = new Read;
    }

    $Read->FullRead("SELECT svc_title, svc_subtitle, svc_name, svc_cover FROM " . DB_SVC . " WHERE svc_status >= :st", "st=1");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    }
?>
<section class="product-banner">
    <div class="container">
        <div class="row">
            <h1>Serviços</h1>
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
                        <a href="<?= BASE . "/servico/{$svc_name}" ?>">
                            <div class="singleService">
                                <div class="serviceThumb">
                                    <img src="<?= BASE . "/tim.php?src=uploads/{$svc_cover}"; ?>&w=1200/2&h=628/2"
                                         alt="<?= $svc_title; ?>" title="<?= $svc_title; ?>">
                                </div>
                                <div class="serviceDetails">
                                    <h2><?= $svc_title; ?></h2>
                                    <p><?= $svc_subtitle; ?></p>

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