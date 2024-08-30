<footer class="footer_01">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-md-6 col-lg-3 noPaddingRight">
                <aside class="widget">
                    <div class="about_widget">
                        <a href="<?= BASE; ?>"><img title="Ir para Página Inicial"
                                                    src="<?= INCLUDE_PATH; ?>/images/logo-travi-white@2x.png"
                                                    alt="<?= SITE_ADDR_NAME ?> - Logo"/></a>
                        <p>Possuímos uma diversidade de produtos e processos que nos permite atender com eficácia as
                            exigências de diversos mercados.</p>
                        <a href="tel:<?= str_replace(' ', '', SITE_ADDR_SAC); ?>" title="Ligar AGORA!">
                            <div class="caller">
                                <i class="fal fa-headphones"></i>
                                <span>Fale com nosso SAC</span>
                                <h3><?= SITE_ADDR_SAC; ?></h3>
                            </div>
                        </a>
	                    <br>
                        <a href="<?= BASE; ?>/ouvidoria" title="Ouvidoria">
                            <div class="caller">
                                <i class="fal fa-headset"></i>
                                <span>Nós escutamos você!</span>
                                <h3>Ouvidoria</h3>
                            </div>
                        </a>
                    </div>
                </aside>
            </div>
            <div class="col-xl-2 col-md-6 col-lg-2 pdl45 noPaddingRight">
                <aside class="widget">
                    <h3 class="widget_title">Links Rápidos<span>.</span>
                    </h3>
                    <ul>
                        <li>
                            <a href="<?= BASE; ?>/sobre">Sobre nós</a>
                        </li>
                        <li>
                            <a href="<?= BASE; ?>/segmentos">Áreas de Atuação</a>
                        </li>
                        <li>
                            <a href="<?= BASE; ?>/produtos/produtos">Produtos</a>
                        </li>
                        <li>
                            <a href="<?= BASE; ?>/servicos">Serviços</a>
                        </li>
                        <li>
                            <a href="<?= BASE; ?>/artigos/noticias">Últimas Notícias</a>
                        </li>
                        <li>
                            <a href="<?= BASE; ?>/contato">Entrar em Contato</a>
                        </li>
                        <li>
                            <a href="<?= BASE; ?>/representantes">Representantes</a>
                        </li>
                        <li>
                            <a href='<?= BASE; ?>/materiais'>Downloads</a>
                        </li>
	                    <li>
		                    <a href='<?= BASE; ?>/politica-de-protecao-de-dados'>LGPD</a>
	                    </li>
                    </ul>
                </aside>
            </div>
            <div class="col-xl-4 col-md-6 col-lg-4 pdl65">
                <aside class="widget">
                    <h3 class="widget_title">Novidades<span>.</span></h3>
                    <div class="recentServices">

                        <?php
                            $Read->FullRead('SELECT post_name, post_title, post_cover, post_date FROM ' . DB_POSTS . ' WHERE post_status = :st AND post_date < NOW() ORDER BY post_id DESC LIMIT :limit',
                                'st=1&limit=3');

                            if($Read->getResult()){

                                foreach ($Read->getResult() as $Post){
                                    extract($Post);
                                    $post_cover = $post_cover ? "uploads/{$post_cover}" : "admin/_img/no_image.jpg";
                                    echo "
                                        <div class='serviceItem clearfix'>
                                            <a href='" . BASE . "/artigo/{$post_name}' title='Ler artigo completo!'>
                                                <img class='float-left' src='" . BASE . "/tim.php?src={$post_cover}&w=1200&h=628'
                                                alt='{$post_title}'/></a>
                                                <h5><a href='" . BASE . "/artigo/{$post_name}' title='Ler artigo completo!'>{$post_title}</a></h5>
                                                <span>".utf8_encode(strftime(' %d de %B de %Y', strtotime($post_date)))."</span>
                                        </div>";
                                }

                            }else{
                                Erro("Não existem publicações no Blog. Volte mais tarde!", E_USER_NOTICE);
                            }
                        ?>
                    </div>
                </aside>
            </div>
            <div class="col-xl-3 col-md-6 col-lg-3">
                <aside class="widget subscribe_widget">
                    <h3 class="widget_title">Newsletter -  Inscreva-se<span>!</span>
                    </h3>
                    <div class="subscribForm">
                        <form class="j_formsubmit" method="post" enctype="multipart/form-data">
                            <input type="hidden" class="callback" name="callback" value="Leads">
                            <input type="hidden" class="callback-action" name="callback_action" value="news1">

                            <div class="callback_return trigger_ajax"></div>

                            <input type="email" name="email" autocomplete="email" placeholder="Entre com seu E-mail"/>
                            <button type="submit">
                                <img src="<?= INCLUDE_PATH; ?>/images/icons/load_white.gif" title="Enviando..."
                        class="none form_load">
                        Inscreva-se
                        </button>
                        </form>
                    </div>
                </aside>
                <aside class="widget footer_social">
                    <h3 class="widget_title">Siga-nos nas Redes Sociais<span>.</span>
                    </h3>
                    <div class="socials">
                        <?= SITE_SOCIAL_TWITTER ? "<a title='Siga-nos no Twitter' href='https://www.twitter.com/" . SITE_SOCIAL_TWITTER . "'  target='_blank'><i class='fab fa-twitter'></i></a>" : ""; ?>

                        <?= SITE_SOCIAL_FB_PAGE ? "<a title='Siga-nos no Facebook' href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "'  target='_blank'><i class='fab fa-facebook-f'></i></a>" : ""; ?>

                        <?= SITE_SOCIAL_INSTAGRAM ? "<a title='Siga-nos no Instagram' href='https://www.instagram.com/" . SITE_SOCIAL_INSTAGRAM . "'  target='_blank'><i class='fab fa-instagram'></i></a>" : ""; ?>

                        <?= SITE_SOCIAL_LINKEDIN ? "<a title='Siga-nos no Linkedin' href='https://www.linkedin.com/company/" . SITE_SOCIAL_LINKEDIN . "'  target='_blank'><i class='fab fa-linkedin'></i></a>" : ""; ?>

                        <?= SITE_SOCIAL_YOUTUBE ? "<a title='Inscreva-se em nosso canal no Youtube' href='https://www.youtube.com/@" . SITE_SOCIAL_YOUTUBE . "?sub_confirmation=1'  target='_blank'><i class='fab fa-youtube'></i></a>" : ""; ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</footer>
<section class="copyright_section">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="siteinfo">
                    Copyright &COPY; 1945 - <?= date('Y') ?> - Travi Plásticos - Desenvolvido por <a
                            href="https://zen.ppg.br" target="_blank">Zen
                        Agência Web</a> -
                </div>
            </div>
        </div>
    </div>
</section>

<script>
	$(function () {
        //HELLOBAR START
        $.getScript(BASE + "/_cdn/widgets/hellobar/hellobar.wc.js", function (data) {
            $("head").append("<link rel='stylesheet' href='" + BASE + "/_cdn/widgets/hellobar/hellobar.wc.css'/>");
        });
    });
</script>
