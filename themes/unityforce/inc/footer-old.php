        <footer class="footer_01">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-md-6 col-lg-3 noPaddingRight">
                        <aside class="widget">
                            <div class="about_widget">
                                <a href="index.html"><img src="images/logo_2.png" alt=""/></a>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                                <div class="caller">
                                    <i class="fal fa-headphones"></i>
                                    <span>Talk to Our Officers</span>
                                    <h3>+1 001-765-4321</h3>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <div class="col-xl-2 col-md-6 col-lg-2 pdl45 noPaddingRight">
                        <aside class="widget">
                            <h3 class="widget_title">Links Importantes<span>.</span></h3>
                            <ul>
                                <li><a href="<?= BASE; ?>">Sobre à Empresa</a></li>
                                <li><a href="<?= BASE; ?>">Últimos Projetos</a></li>
                                <li><a href="<?= BASE; ?>">Novidades no Blog</a></li>
                                <li><a href="<?= BASE; ?>">Nossos Testemunhos</a></li>
                                <li><a href="<?= BASE; ?>">Nossa Missão</a></li>
                                <li><a href="<?= BASE; ?>">Contate-nos</a></li>
                                
                            </ul>
                        </aside>
                    </div>
                    <div class="col-xl-4 col-md-6 col-lg-4 pdl65">
                        <aside class="widget">
                            <h3 class="widget_title">Nossos Serviços<span>.</span></h3>
                            <div class="recentServices">
                                <div class="serviceItem clearfix">
                                    <img class="float-left" src="images/widget/1.jpg" alt=""/>
                                    <h5><a href="#">Lorem ipsum dolor sit am et, consectetur.</a></h5>
                                    <span>14 Jnauary, 2019</span>
                                </div>
                                <div class="serviceItem clearfix">
                                    <img class="float-left" src="images/widget/2.jpg" alt=""/>
                                    <h5><a href="#">Lorem ipsum dolor sit am et, consectetur.</a></h5>
                                    <span>19 February, 2019</span>
                                </div>
                                <div class="serviceItem clearfix">
                                    <img class="float-left" src="images/widget/3.jpg" alt=""/>
                                    <h5><a href="#">Lorem ipsum dolor sit am et, consectetur.</a></h5>
                                    <span>14 July, 2018</span>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <div class="col-xl-3 col-md-6 col-lg-3">
                        <aside class="widget subscribe_widget">
                            <h3 class="widget_title">Inscreva-se agora!<span>.</span></h3>
                            <div class="subscribForm">
                                <form method="post" action="#">
                                    <input type="email" name="email" placeholder="Enter your email"/>
                                    <button type="submit">Submit Now</button>
                                </form>
                            </div>
                        </aside>
                        <aside class="widget footer_social">
                            <h3 class="widget_title">Get More Here<span>.</span></h3>
                            <div class="socials">
                                    <?= SITE_SOCIAL_TWITTER ? "<a href='https://www.twitter.com/" . SITE_SOCIAL_TWITTER . "' class='hover-twitter' target='_blank'><i class='fa fa-twitter'></i></a>" : ""; ?>
                        <?= SITE_SOCIAL_FB_PAGE ? "<a href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "' class='hover-facebook' target='_blank'><i class='fa fa-facebook'></i></a>" : ""; ?>
                        <?= SITE_SOCIAL_LINKEDIN ? "<a href='https://www.linkedin.com/company/" . SITE_SOCIAL_LINKEDIN . "' class='hover-linkedin' target='_blank'><i class='fa fa-linkedin'></i></a>" : ""; ?>
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
                            	© <?= Date('Y'); ?> Global Suprimentos  |  Desenvolvido por <a
                                    title="<?= AGENCY_NAME ?> - Marketing Digital"
                                    href="<?= AGENCY_URL; ?>"><?= AGENCY_NAME ?></a>
                           
                        </div>
                    </div>
                </div>
            </div>
        </section>


<footer class="footer footer-type-1">
    <div class="container">
        <div class="footer-widgets">
            <div class="row">

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget footer-about-us">
                        <h5>Sobre a Global</h5>
                        <p class="mb-0">A Global Suprimentos Industriais atua fornecendo para as mais diversas cadeias
                            produtivas, com foco nos segmentos metal mecânico, moveleiro, naval, automotivo, calçadista
                            e construção civil.</p>
                    </div>
                </div> <!-- end about us -->

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget footer-get-in-touch">
                        <h5>Entre em Contato</h5>
                        <p class="footer-address"><?= SITE_ADDR_CITY; ?>, <?= SITE_ADDR_UF; ?>
                            , <?= SITE_ADDR_COUNTRY; ?>.
                            <br><?= SITE_ADDR_ADDR; ?>,<br> <?= SITE_ADDR_DISTRICT; ?></p>
                        <p><i class="fa fa-phone"></i><a
                                href="tel:<?= SITE_ADDR_PHONE_A; ?>"> <?= SITE_ADDR_PHONE_A; ?></a></p>
                        <p><i class="fa fa-envelope"></i><a
                                href="mailto:<?= SITE_ADDR_EMAIL; ?>"> <?= SITE_ADDR_EMAIL; ?></a></p>
                    </div>
                </div> <!-- end stay in touch -->

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget footer-posts">
                        <h5>Posts mais Recentes</h5>
                        <div class="footer-entry-list">
                            <ul class="posts-list no-top-pad">
                                <?php
                                    $Read->ExeRead(DB_POSTS,
                                        "WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_views DESC, post_date DESC LIMIT 5");
                                    if (!$Read->getResult()) {
                                        echo Erro("Ainda Não existe posts cadastrados. Favor volte mais tarde :)",
                                            E_USER_NOTICE);
                                    } else {
                                        foreach ($Read->getResult() as $Post) {
                                            ?>
                                            <li>
                                                <article class="post-small clearfix">
                                                    <figure class="entry-img hover-scale">
                                                        <a title="Ler mais sobre <?= $Post['post_title']; ?>"
                                                           href="<?= BASE; ?>/artigo/<?= $Post['post_name']; ?>"><img
                                                                title="<?= $Post['post_title']; ?>"
                                                                alt="<?= $Post['post_title']; ?>"
                                                                src="<?= BASE; ?>/tim.php?src=uploads/<?= $Post['post_cover']; ?>&w=<?= IMAGE_W / 2; ?>&h=<?= IMAGE_H / 2; ?>"/></a>
                                                    </figure>
                                                    <div class="entry">
                                                        <h3 title="Ler mais sobre <?= $Post['post_title']; ?>"
                                                            class="entry-title"><a
                                                                href="<?= BASE; ?>/artigo/<?= $Post['post_name']; ?>"><?= $Post['post_title']; ?></a>
                                                        </h3>
                                                        <ul class="entry-meta list-inline">
                                                            <li class="entry-date">
                                                                <time datetime="<?= date('Y-m-d',
                                                                    strtotime($Post['post_date'])); ?>"
                                                                      pubdate="pubdate"><?= utf8_encode(strftime(" %d / %m / %Y",
                                                                        strtotime($Post['post_date']))); ?></time>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </article>
                                            </li>
                                            <?php
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div> <!-- end latest posts -->

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget footer-links">
                        <h5>Links Úteis</h5>
                        <ul>
                            <li><a href="<? BASE; ?>" class="link-underline">A Global</a></li>
                            <li><a href="<? BASE; ?>/produtos" title="<?= BASE; ?> | Produtos">Produtos</a></li>
                            <li><a href="<? BASE; ?>/artigos" title="<?= BASE; ?> | Artigos">Blog</a></li>
                            <li><a href="<? BASE; ?>/contato" title="<?= BASE; ?> | Contato">Contato</a></li>
                            <li><a href="<? BASE; ?>/cotacao" title="<?= BASE; ?> | Solicite Cotação">Solicite Cotação</a></li>
                        </ul>

                    </div>
                </div> <!-- end useful links -->

            </div>
        </div>
    </div> <!-- end container -->

    <div class="bottom-footer">
        <div class="container">
            <div class="row">

                <div class="col-sm-6 copyright">
							<span>
								© <?= Date('Y'); ?> Global Suprimentos  |  Desenvolvido por <a
                                    title="<?= AGENCY_NAME ?> - Marketing Digital"
                                    href="<?= AGENCY_URL; ?>"><?= AGENCY_NAME ?></a>
							</span>
                </div>

                <div class="col-sm-4 col-sm-offset-2 footer-socials mt-mdm-10">
                    <div class="social-icons text-right">
                        <?= SITE_SOCIAL_TWITTER ? "<a href='https://www.twitter.com/" . SITE_SOCIAL_TWITTER . "' class='hover-twitter' target='_blank'><i class='fa fa-twitter'></i></a>" : ""; ?>
                        <?= SITE_SOCIAL_FB_PAGE ? "<a href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "' class='hover-facebook' target='_blank'><i class='fa fa-facebook'></i></a>" : ""; ?>
                        <?= SITE_SOCIAL_LINKEDIN ? "<a href='https://www.linkedin.com/company/" . SITE_SOCIAL_LINKEDIN . "' class='hover-linkedin' target='_blank'><i class='fa fa-linkedin'></i></a>" : ""; ?>
                    </div>
                </div>

            </div>
        </div>
    </div> <!-- end bottom footer -->
</footer> <!-- end footer -->