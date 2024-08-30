<div class="topbar">
    <div class="header-container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="topbar_right text-right">
                    <div class="topbar_element info_element col-md-3">
                        <i class="fa fa-envelope"></i>
                        <p>
                            <a class="color-white" href="mailto:<?= SITE_ADDR_EMAIL ?>"><?= SITE_ADDR_EMAIL ?></a>
                        </p>
                    </div>
                    <div class="topbar_element info_element col-md-3">
                        <i class="fa fa-phone"></i>
                        <p>
                            <a class="color-white" href="tel:<?= SITE_ADDR_PHONE_A ?>"><?= SITE_ADDR_PHONE_A ?></a>
                        </p>
                    </div>
	                <div class="topbar_element info_element col-md-3">
		                <i class="fa fa-headset"></i>
		                <p>
			                <a class="color-white" href="<?=BASE;?>/ouvidoria">Ouvidoria</a>
		                </p>
	                </div>
                    <div class="topbar_element search_element col-md-3">
                        <form method="post" action="">
                            <i class="fa fa-search"></i>
                            <input type="search" name="p" placeholder="Pesquisar por Produto..."/>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="nav_bar" id="fix_nav">
    <div class="header-container">
        <div class="row">
            <div class="col-xl-1 col-lg-1 col-md-1 noPadding">
                <div class="logo text-left">
                    <a href="<?= BASE; ?>" title="Voltar ao Início!">
                        <img src="<?= INCLUDE_PATH ?>/images/logo-travi-color.png"
                             alt="<?= SITE_ADDR_NAME ?> - Logotipo"/>
                    </a>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8">
                <div class="mobileMenuBar">
                    <a href="javascript: void(0);"><span>Menu</span><i class="fa fa-bars"></i></a>
                </div>

	            <nav class="mainmenu">
                    <h2 class="title-hidden">Plásticos de Engenharia de Alto Desempenho e com a Travi Plásticos</h2>
                    <ul>
                        <li class="current-menu-item menu-item-has-children">
                            <a href="<?= BASE ?>"><i class="fa fa-home"></i></a>
                        </li>
                        <li>
                            <a href="<?= BASE ?>/sobre">A Travi</a>
                            <ul class="sub_menu">
                                <li><a href="<?=BASE;?>/sobre">Sobre nós</a></li>
                                <li><a href="<?=BASE;?>/certificacoes">Certificações</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="<?= BASE ?>/produtos/produtos"></i>Produtos</a>
                            <?php
                                $Read->ExeRead(DB_PDT_CATS_TRAVI, 'WHERE cat_parent = :sector',
                                    "sector=1");
                                if ($Read->getResult()) {
                                    echo "<ul class='sub_menu'>";
                                    foreach ($Read->getResult() as $cat) {
                                        extract($cat);
                                        echo "<li><a href='" . BASE . "/produtos/$cat_name'>" . mb_convert_case
                                            ($cat_title,
                                                MB_CASE_TITLE) . "</a></li>";
                                    }
                                    echo '</ul>';
                                }
                            ?>

                        </li>
                        <?php
                            $Read->FullRead("SELECT svc_title, svc_name FROM " . DB_SVC . " WHERE svc_status = :st",
                                "st=1");
                            if ($Read->getResult()) {
                                echo "<li><a href='" . BASE . "/servicos'>Processos</a><ul class='sub_menu'>";
                                foreach ($Read->getResult() as $svc) {
                                    extract($svc);
                                    echo "<li><a href='" . BASE . "/servico/$svc_name'>$svc_title</a></li>";
                                }
                                echo "</ul></li>";
                            }
                        ?>

                        <?php
                            $Read->FullRead('SELECT seg_title, seg_name FROM ' . DB_SEG . ' WHERE seg_status = :st',
                                'st=1');
                            if ($Read->getResult()) {
                                echo "<li><a href='" . BASE . "/segmentos'>Segmentos</a>";
                                echo "<ul class='sub_menu'>";
                                foreach ($Read->getResult() as $seg) {
                                    extract($seg);
                                    echo "<li><a href='" . BASE . "/segmento/$seg_name'>$seg_title</a></li>";
                                }
                                echo '</ul>';
                                echo '</li>';
                            }
                        ?>

                        <?php
                            $Read->ExeRead(DB_CATEGORIES,
                                "WHERE category_parent IS NULL ORDER BY category_name ASC");
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Cat) {
                                    echo "<li class='menu-item-has-children'><a title=' " . SITE_NAME . " | {$Cat['category_title']}' href='" . BASE . "/artigos/{$Cat['category_name']}'>{$Cat['category_title']}</a>";
                                    $Read->ExeRead(DB_CATEGORIES,
                                        "WHERE category_parent = :ct ORDER BY category_name ASC",
                                        "ct={$Cat['category_id']}");
                                    if ($Read->getResult()) {
                                        echo "<ul class='sub_menu'>";
                                        foreach ($Read->getResult() as $SubCat) {
                                            echo "<li><a title='{$Cat['category_title']} | {$SubCat['category_title']}' href='" . BASE . "/artigos/{$SubCat['category_name']}'>{$SubCat['category_title']}</a></li>";
                                        }
                                        echo "</ul>";
                                    }
                                    echo "</li>";
                                }
                            }
                        ?>
                        <li>
                            <a href='<?= BASE ?>/representantes'>Representantes</a>
                        </li>
                        <li>
                            <a href='<?= BASE ?>/materiais'>Downloads</a>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="javascript:void(0);">Contatos</a>
                            <ul class="sub_menu">
                                <li>
                                    <a href="<?= BASE ?>/contato">Entrar em Contato</a>
                                </li>
                                <li>
                                    <a href="<?= BASE ?>/trabalhe-conosco">Trabalhe Conosco</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
		            <a class="cta_menu icon-newspaper" target="_blank" title="Clique! Nosso time esta aguardando seu contato."
		               href='https://marketing.travi.com.br/orcamento-site'>Orçamento</a>
                </nav>

            </div>
            <div class="col-xl-3 col-lg-3">
                <div class="top_social text-right">
	                <?php require '_cdn/gtranslate/gtranslate.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <span class="right_bgs"></span>
</div>

<!-- Overlay Menu -->
<div class="popup popup__menu">
    <div class="header-container mobileContainer">
        <div class="row">
            <div class="col-lg-8 text-left">
                <div class="popup_logos">
                    <a href="<?= BASE; ?>">
                        <img src="<?= INCLUDE_PATH ?>/images/logo-travi-white.png"
                             alt="<?= SITE_ADDR_NAME ?> - Logotipo"/>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-right">
                <a href="" id="close-popup" class="close-popup"></a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="popup-inner">
                    <div class="dl-menu__wrap dl-menuwrapper">
                        <ul class="dl-menu dl-menuopen">
                            <li class="current-menu-item menu-item-has-children">
                                <a href="<?= BASE ?>"><i class="fa fa-home fa-2x"></i></a>
                            </li>
                            <li>
                                <a href="<?= BASE ?>/sobre">A Travi</a>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="javascript:void(0);">Contatos</a>
                                <ul class="dl-submenu">
                                    <li>
                                        <a href="<?= BASE ?>/contato">Entre em Contato</a>
                                    </li>
                                    <li>
                                        <a href="<?= BASE ?>/contato">Trabalhe Conosco</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="javascript:void(0);">Páginas</a>
                                <ul class="dl-submenu">
                                    <!--                                    <li>-->
                                    <!--                                        <a href="team.html">Team Page</a>-->
                                    <!--                                    </li>-->
                                    <li>
                                        <a href="<?= BASE; ?>404">404</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-6 col-xs-12 text-left">
                <ul class="footer__contacts">
                    <li>
                        <a href="tel:<?= SITE_ADDR_PHONE_A ?>" title="Ligar para Travi Plásticos"><i
                                    class="fa fa-phone"></i> Telefone: <?= SITE_ADDR_PHONE_A ?>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:<?= SITE_ADDR_EMAIL ?>" target="_blank" title="Envie-nos um E-mail..."><i
                                    class="fa fa-envelope"></i> Email: <?= SITE_ADDR_EMAIL ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://goo.gl/maps/mxwo37AoV7m86Jbt7" target="_blank"
                           title="Visite à Travi Plásticos"><i class="fa fa-home"></i> Endereço: <?= SITE_ADDR_ADDR ?>
                            , <?= SITE_ADDR_DISTRICT ?>, <?= SITE_ADDR_CITY ?>/<?= SITE_ADDR_UF ?>
                            - <?= SITE_ADDR_COUNTRY ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 col-sm-6 col-xs-12 col-xs-12">
                <div class="foo_social popUp_social text-right">

                    <?= SITE_SOCIAL_TWITTER ? "<a title='Siga-nos no Twitter' href='https://www.twitter.com/" . SITE_SOCIAL_TWITTER . "'  target='_blank'><i class='fab fa-twitter'></i></a>" : ""; ?>

                    <?= SITE_SOCIAL_FB_PAGE ? "<a title='Siga-nos no Facebook' href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "'  target='_blank'><i class='fab fa-facebook-f'></i></a>" : ""; ?>

                    <?= SITE_SOCIAL_INSTAGRAM ? "<a title='Siga-nos no Instagram' href='https://www.instagram.com/" . SITE_SOCIAL_INSTAGRAM . "'  target='_blank'><i class='fab fa-instagram'></i></a>" : ""; ?>

                    <?= SITE_SOCIAL_LINKEDIN ? "<a title='Siga-nos no Linkedin' href='https://www.linkedin.com/company/" . SITE_SOCIAL_LINKEDIN . "'  target='_blank'><i class='fab fa-linkedin'></i></a>" : ""; ?>

                    <?= SITE_SOCIAL_YOUTUBE ? "<a title='Inscreva-se em nosso canal no Youtube' href='https://www.youtube.com/user/" . SITE_SOCIAL_YOUTUBE . "?sub_confirmation=1'  target='_blank'><i class='fab fa-youtube'></i></a>" : ""; ?>

                </div>
            </div>
        </div>
    </div>
</div><!-- /Overlay Menu -->
