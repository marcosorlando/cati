<?php

    if (!$Read) {
        $Read = new Read;
    }
    $Email = new Email;
    $Read->ExeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';

        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

<section class="page_banner div_gray"
         style='background: url(<?= INCLUDE_PATH; ?>/images/bg/a-travi-bg-1920x370.jpg) no-repeat center center / cover;'>
    <div class="container">
        <div class="col-xl-12 text-center">
            <h2>Entre em Contato</h2>
            <div class="breadcrumbs">
                <a href="<?= BASE; ?>"><i class="fa fa-home"></i></a><i>|</i><span>Contate-nos</span>
            </div>
        </div>
    </div>
</section>

<section class="commonSection pdb90">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-md-6 col-lg-5">
                <div class="icon_box_05 ib5_left">
                    <i class="fal fa-map-marker-alt"></i>
                    <div class="ib5_inner">
                        <h3>Localização</h3>
                        <p><?= SITE_ADDR_ADDR ?>, <br><?= SITE_ADDR_DISTRICT ?>
                            <br/><?= SITE_ADDR_CITY ?>, <?= SITE_ADDR_COUNTRY ?>
                        </p>
                        <a href="#map" class="wc_goto">Encontre-nos no mapa</a>
                    </div>
                </div>
                <div class="icon_box_05 ib5_left">
                    <i class="fal fa-clock"></i>
                    <div class="ib5_inner">
                        <h3>Atendimento de:</h3>
                        <p>
                            Seg - Sex: 07:30 ás 17:18 hs
                        </p>
                        <a href="tel:<?= SITE_ADDR_PHONE_A ?>"><i class="fa fa-phone-office"></i> Contatar Comercial</a>
                    </div>
                </div>
                <div class="icon_box_05 ib5_left">
                    <i class="fal fa-headset"></i>
                    <div class="ib5_inner">
                        <h3>Contatos Rápidos</h3>
                        <p>
                            <a href="tel:<?= SITE_ADDR_SAC ?>"><i class="fal fa-headset"></i> <?= SITE_ADDR_SAC ?></a>
                            <br/>
                            <a href="mailto:<?=SITE_ADDR_EMAIL?>" title="Enviar e-mail agora!"><?=SITE_ADDR_EMAIL?></a>
                        </p>
                        <a href="tel:<?= str_replace([' ','+'], '', SITE_ADDR_PHONE_A); ?>"><i class="fal
                        fa-headset"></i> Fazer Chamada</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-md-6 col-lg-7 pdl45">
                <div class="row">
                    <div class="col-xl-12 text-left">
                        <h6 class="sub_title dark_sub_title ">Entrar em contato</h6>
                        <h2 class=" mb45">
                            <span>Preencha o Formulário</span>
                        </h2>
                    </div>
                </div>
                <div class="cotactForm light_form">
                    <form id="contactForm" class="row j_formsubmit" method="post" enctype="multipart/form-data">
                        <input type="hidden" class="callback" name="callback" value="Leads">
                        <input type="hidden" class="callback-action" name="callback_action" value="contato">

                        <div class="callback_return trigger_ajax"></div>

                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="iconInput">
                                <input class="required" type="text" name="nome" id="con_name"
                                       placeholder="DIGITE SEU NOME AQUI" tabindex="1" required/>
                                <i class="fal fa-user"></i>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="iconInput">
                                <input class="required" type="text" name="fone"
                                       placeholder="DIGITE SEU CELULAR AQUI" class="formPhone" tabindex="2" required/>
                                <i class="fal fa-phone"></i>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="iconInput">
                                <input class="required" type="email" name="email"
                                       placeholder="DIGITE SEU E-MAIL AQUI" tabindex="3" required/>
                                <i class="fal fa-envelope"></i>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="iconInput">
                                <input class="required" type="text" name="assunto"
                                       placeholder="DIGITE O ASSUNTO AQUI" tabindex="4" required/>
                                <i class="fal fa-edit"></i>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="iconInput">
                                <textarea class="required" name="mensagem"
                                          placeholder="ESCREVA AQUI SUA MENSAGEM..." tabindex="5" required></textarea>
                                <i class="fal fa-pencil-alt"></i>
                            </div>
                        </div>
                        <div class="col-xl-12 text-center">
                            <button type="submit" class="btn btn-outline-dark">
                                <img src="<?= INCLUDE_PATH; ?>/images/icons/load_white.gif" title="Enviando..." class="none form_load">
                                <i class="fa fa-envelope-open-text fa-1x"></i> Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GOOGLE MAPS -->
<?php require REQUIRE_PATH . "/inc/google-map.php"; ?>
<!-- GOOGLE MAPS -->
