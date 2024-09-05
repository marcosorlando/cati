<?php

	ini_set('allow_url_fopen', 'On');
	ob_start();
	session_start();

	setlocale(LC_ALL, "pt_BR", "pt_BR.utf-8", "portuguese");
	date_default_timezone_set('America/Sao_Paulo');

	require './_app/Config.inc.php';

	//CHANCE THEME IN SESSION
	$WC_THEME = filter_input(INPUT_GET, "wctheme", FILTER_DEFAULT);
	if ($WC_THEME && $WC_THEME != 'null') {
		$_SESSION['WC_THEME'] = $WC_THEME;
		header("Location: " . BASE);
		exit;
	} elseif ($WC_THEME && $WC_THEME == 'null') {
		unset($_SESSION['WC_THEME']);
		header("Location: " . BASE);
		exit;
	}

	//READ CLASS AUTO INSTANCE
	if (empty($Read)) {
		$Read = new Read;
	}

	$Sesssion = new Session(SIS_CACHE_TIME);

	//USER SESSION VALIDATION
	if (!empty($_SESSION['userLogin']) && !empty($_SESSION['userLogin']['user_id'])) {
		if (empty($Read)) {
			$Read = new Read;
		}
		$Read->ExeRead(DB_USERS, "WHERE user_id = :user_id", "user_id={$_SESSION['userLogin']['user_id']}");
		if ($Read->getResult()) {
			$_SESSION['userLogin'] = $Read->getResult()[0];
		} else {
			unset($_SESSION['userLogin']);
		}
	}

	//GET PARAMETER URL
	$getURL = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
	$setURL = (empty($getURL) ? 'index' : $getURL);
	$URL = explode('/', $setURL);
	$SEO = new Seo($setURL);

	//CHECK IF THIS POST TABLE TO AMP
	if (APP_POSTS_AMP && (!empty($URL[0]) && $URL[0] == 'artigo') && file_exists(REQUIRE_PATH . '/amp.php')) {
		$Read->ExeRead(DB_POSTS, "WHERE post_name = :name", "name={$URL[1]}");
		$PostAmp = ($Read->getResult()[0]['post_amp'] == 1 ? true : false);
	}

	//INSTANCE AMP (valid single article only)
	if (APP_POSTS_AMP && (!empty($URL[0]) && $URL[0] == 'artigo') && file_exists(REQUIRE_PATH . '/amp.php') && (!empty($URL[2]) && $URL[2] == 'amp') && (!empty($PostAmp) && $PostAmp == true)) {
		require REQUIRE_PATH . '/amp.php';
	} else {
		?>
		<!DOCTYPE html>
		<html lang="pt-br" itemscope itemtype="https://schema.org/<?= $SEO->getSchema(); ?>">
		<head>
			<meta charset="UTF-8">
			<meta name="mit" content="2017-11-16T11:05:36-02:00+24186">
			<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
			<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0,user-scalable=0">
			<meta property="fb:pages" content="#"/>
			<title><?= $SEO->getTitle(); ?></title>
			<meta name="description" content="<?= $SEO->getDescription(); ?>"/>
			<meta name="robots" content="index, follow"/>
			<link rel="base" href="<?= BASE; ?>"/>
			<link rel="paththeme" href="<?= INCLUDE_PATH; ?>"/>
			<link rel="canonical" href="<?= BASE; ?>/<?= $getURL; ?>"/>
			<?php

				if (APP_POSTS_AMP && (!empty($URL[0]) && $URL[0] == 'artigo') && file_exists(REQUIRE_PATH . '/amp.php') && (!empty($PostAmp) && $PostAmp == true)) {
					echo '<link rel="amphtml" href="' . BASE . '/' . $getURL . '/amp" />' . "\r\n";
				}
			?>
			<link rel="alternate" type="application/rss+xml" href="<?= BASE; ?>/rss.php"/>
			<link rel="sitemap" type="application/xml" href="<?= BASE; ?>/sitemap.xml"/>
			<meta itemprop="name" content="<?= $SEO->getTitle(); ?>"/>
			<meta itemprop="description" content="<?= $SEO->getDescription(); ?>"/>
			<meta itemprop="image" content="<?= $SEO->getImage(); ?>"/>
			<meta itemprop="url" content="<?= BASE; ?>/<?= $getURL; ?>"/>
			<meta property="og:type" content="article"/>
			<meta property="og:title" content="<?= $SEO->getTitle(); ?>"/>
			<meta property="og:description" content="<?= $SEO->getDescription(); ?>"/>
			<meta property="og:image" content="<?= $SEO->getImage(); ?>"/>
			<meta property="og:url" content="<?= BASE; ?>/<?= $getURL; ?>"/>
			<meta property="og:site_name" content="<?= SITE_NAME; ?>"/>
			<meta property="og:locale" content="pt_BR"/>
			<meta name="facebook-domain-verification" content="wjmri0pyl9je1o7uib842pgyaleoz6"/>
			<?php

				if (SITE_SOCIAL_FB) {
					echo '<meta property="article:author" content="https://www.facebook.com/' . SITE_SOCIAL_FB_AUTHOR . '" />' . "\r\n";
					echo '<meta property="article:publisher" content="https://www.facebook.com/' . SITE_SOCIAL_FB_PAGE . '" />' . "\r\n";

					if (SITE_SOCIAL_FB_APP) {
						echo '<meta property="og:app_id" content="' . SITE_SOCIAL_FB_APP . '" />' . "\r\n";
					}

					if (SEGMENT_FB_PAGE_ID) {
						echo '<meta property="fb:pages" content="' . SEGMENT_FB_PAGE_ID . '" />' . "\r\n";
					}
				}
			?>

			<meta property="twitter:card" content="summary_large_image"/>
			<?php

				if (SITE_SOCIAL_TWITTER) {
					echo '<meta property="twitter:site" content="@' . SITE_SOCIAL_TWITTER . '" />' . "\r\n";
				}
			?>
			<meta property="twitter:domain" content="<?= BASE; ?>"/>
			<meta property="twitter:title" content="<?= $SEO->getTitle(); ?>"/>
			<meta property="twitter:description" content="<?= $SEO->getDescription(); ?>"/>
			<meta property="twitter:image" content="<?= $SEO->getImage(); ?>"/>
			<meta property="twitter:url" content="<?= BASE; ?>/<?= $getURL; ?>"/>

			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Maven+Pro:wght@400..900&display=swap" rel="stylesheet">

			<!-- favicon -->
			<link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>//assets/images/favicon/favicon.png">
			<link rel="apple-touch-icon" sizes="57x57" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-57x57.png">
			<link rel="apple-touch-icon" sizes="60x60" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-60x60.png">
			<link rel="apple-touch-icon" sizes="72x72" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-72x72.png">
			<link rel="apple-touch-icon" sizes="76x76" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-76x76.png">
			<link rel="apple-touch-icon" sizes="114x114" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-114x114.png">
			<link rel="apple-touch-icon" sizes="120x120" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-120x120.png">
			<link rel="apple-touch-icon" sizes="144x144" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-144x144.png">
			<link rel="apple-touch-icon" sizes="152x152" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-152x152.png">
			<link rel="apple-touch-icon" sizes="180x180" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/apple-icon-180x180.png">
			<link rel="icon" type="image/png" sizes="192x192" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/android-icon-192x192.png">
			<link rel="icon" type="image/png" sizes="32x32" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/favicon-32x32.png">
			<link rel="icon" type="image/png" sizes="96x96" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/favicon-96x96.png">
			<link rel="icon" type="image/png" sizes="16x16" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/favicon-16x16.png">
			<link rel="manifest" href="<?= INCLUDE_PATH; ?>/assets/images/favicon/manifest.json">
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-TileImage" content="<?= INCLUDE_PATH; ?>/assets/images/favicon/ms-icon-144x144.png">
			<meta name="theme-color" content="#ffffff">

			<link href="<?= INCLUDE_PATH; ?>/assets/bootstrap/bootstrap.min.css" rel="stylesheet">
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/aos.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/custom-style.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/special-classes.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/responsive.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/custom.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/mobile.css">
			<link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/assets/css/owl.carousel.css">
			<link rel="stylesheet" href="<?= BASE; ?>/_cdn/bootcss/fonticon.css">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css">

			<script src="<?= BASE; ?>/_cdn/jquery.js"></script>
			<script src="<?= BASE; ?>/_cdn/workcontrol.js"></script>

			<!-- Facebook Pixel Code -->
			<script>
                !function (f, b, e, v, n, t, s) {
                    if (f.fbq) return;
                    n = f.fbq = function () {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq) f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
                }(window, document, 'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '<?=SEGMENT_FB_PIXEL_ID?>');
                fbq('track', 'PageView');
			</script>
			<noscript>
				<img height="1" width="1" style="display:none"
				     src="https://www.facebook.com/tr?id=pixel_facebook&ev=PageView&noscript=1"/>
			</noscript>
			<!-- End Facebook Pixel Code -->

			<?php
				//GOOGLE ANALYTICS GA4 WITH DEFINE IN CONFIG
				if (!empty(SEGMENT_GL_ANALYTICS)) {
					//Global site tag (gtag.js) - Google Analytics
					echo "<script async src='https://www.googletagmanager.com/gtag/js?id=" . SEGMENT_GL_ANALYTICS . "'></script>";
					echo "<script>
					window.dataLayer = window.dataLayer || [];
						function gtag() { dataLayer.push(arguments); }
                            gtag('js', new Date());
                            gtag('config', '" . SEGMENT_GL_ANALYTICS . "'); " .
						(SEGMENT_GL_ADWORDS_ID ? "gtag('config', '" . SEGMENT_GL_ADWORDS_ID . "');" : '') .
						"</script>";
				}

				if (!empty(SEGMENT_GL_TAGMANAGER)) {
					#<!-- Google Tag Manager -->
					echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','" . SEGMENT_GL_TAGMANAGER . "');</script>";
					#<!-- End Google Tag Manager -->
				}
			?>
		</head>
		<body>
		<?php
			if (!empty(SEGMENT_GL_TAGMANAGER)) {
				#<!-- Google Tag Manager (noscript) -->
				echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . SEGMENT_GL_TAGMANAGER . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
				#<!-- End Google Tag Manager (noscript) -->
			}
		?>

		<div id="texto">
			<?php
				// MESSAGE MAINTENANCE FOR ADMIN
				if (ADMIN_MAINTENANCE && !empty($_SESSION['userLogin']['user_level']) && $_SESSION['userLogin']['user_level'] >= 6) {
					echo "<div class='workcontrol_maintenance'>&#x267A; O MODO de manutenção está ativo. Somente administradores podem ver o site assim &#x267A;</div>";
				}

				// REDIRECT PUBLIC TO MAINTENANCE
				if (ADMIN_MAINTENANCE && (empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < 6)) {
					require 'maintenance.php';
				} else {
					// PESQUISA PRODUTOS
					$Search = filter_input_array(INPUT_POST, FILTER_DEFAULT);
					if ($Search && !empty($Search['p'])) {
						$Search = urlencode(strip_tags(trim($Search['p'])));
						header('Location: ' . BASE . '/pesquisa-produtos/' . $Search);
						exit;
					}

					// PESQUISA
					$Search = filter_input_array(INPUT_POST, FILTER_DEFAULT);
					if ($Search && !empty($Search['s'])) {
						$Search = urlencode(strip_tags(trim($Search['s'])));
						header('Location: ' . BASE . '/pesquisa/' . $Search);
						exit;
					}

					//LANDING_PAGES MODULE
					//LP
					$Customers = [];
					$Read->FullRead('SELECT page_name FROM ' . DB_LANDING_PAGES);
					if ($Read->getResult()) {
						foreach ($Read->getResult() as $SinglePage) {
							$Customers[] = $SinglePage['page_name'];
						}
					}

					//TP
					$Tps = [];
					$Read->FullRead('SELECT page_name FROM ' . DB_THANKYOU_PAGES);
					if ($Read->getResult()) {
						foreach ($Read->getResult() as $SinglePage) {
							$Tps[] = $SinglePage['page_name'];
						}
					}

					if (in_array($URL[0], $Tps) && file_exists(REQUIRE_PATH . '/thankyou-page.php')) {
						if (file_exists(REQUIRE_PATH . "/page-{$URL[0]}.php")) {
							require REQUIRE_PATH . "/page-{$URL[0]}.php";
						} else {
							require REQUIRE_PATH . '/thankyou-page.php';
						}
					} elseif (in_array($URL[0], $Customers) && file_exists(REQUIRE_PATH . '/landing-page.php')) {
						if (file_exists(REQUIRE_PATH . "/page-{$URL[0]}.php")) {
							require REQUIRE_PATH . "/page-{$URL[0]}.php";
						} else {
							require REQUIRE_PATH . '/landing-page.php';
						}
					} else {
						//END LANDING_PAGES MODULE

						// HEADER
						if (file_exists(REQUIRE_PATH . "/inc/header.inc.php")) {
							require REQUIRE_PATH . "/inc/header.inc.php";
						} else {
							trigger_error('Crie um arquivo /inc/header.inc.php na pasta do tema!');
						}

						// CONTENT
						echo "<main class='main-wrapper oh'>";
						$URL[1] = (empty($URL[1]) ? null : $URL[1]);

						if ($URL[0] == 'rss' || $URL[0] == 'feed' || $URL[0] == 'rss.xml') {
							header("Location: " . BASE . "/rss.php");
							exit;
						}

						$Pages = [];
						$Read->FullRead("SELECT page_name FROM " . DB_PAGES);
						if ($Read->getResult()) {
							foreach ($Read->getResult() as $SinglePage) {
								$Pages[] = $SinglePage['page_name'];
							}
						}

						if (in_array($URL[0], $Pages) && file_exists(REQUIRE_PATH . '/pagina.php')) {
							if (file_exists(REQUIRE_PATH . "/page-{$URL[0]}.php")) {
								require REQUIRE_PATH . "/page-{$URL[0]}.php";
							} else {
								require REQUIRE_PATH . '/pagina.php';
							}
						} elseif (file_exists(REQUIRE_PATH . '/' . $URL[0] . '.php')) {
							if ($URL[0] == 'artigos' && file_exists(REQUIRE_PATH . "/cat-{$URL[1]}.php")) {
								require REQUIRE_PATH . "/cat-{$URL[1]}.php";
							} else {
								require REQUIRE_PATH . '/' . $URL[0] . '.php';
							}
						} elseif (file_exists(REQUIRE_PATH . '/' . $URL[0] . '/' . $URL[1] . '.php')) {
							require REQUIRE_PATH . '/' . $URL[0] . '/' . $URL[1] . '.php';
						} else {
							if (file_exists(REQUIRE_PATH . "/404.php")) {
								require REQUIRE_PATH . '/404.php';
							} else {
								trigger_error("Não foi possível incluir o arquivo themes/" . THEME . "/{$getURL}.php <b>(O arquivo 404 também não existe!)</b>");
							}
						}
						echo "</main>";

						// FOOTER
						if (file_exists(REQUIRE_PATH . "/inc/footer.inc.php")) {
							require REQUIRE_PATH . "/inc/footer.inc.php";
						} else {
							trigger_error('Crie um arquivo /inc/footer.inc.php na pasta do tema!');
						}
					}
				}

				// WC CODES
				$Read->ExeRead(DB_WC_CODE);
				if ($Read->getResult()) {
					if (empty($Update)) {
						$Update = new Update;
					}

					$ActiveCodes = filter_input(INPUT_GET, 'url', FILTER_DEFAULT);
					echo "\r\n\r\n\r\n<!--WorkControl Codes-->\r\n";
					foreach ($Read->getResult() as $HomeCodes) {
						if (empty($HomeCodes['code_condition'])) {
							echo $HomeCodes['code_script'];
							$UpdateCodes = ['code_views' => $HomeCodes['code_views'] + 1];
							$Update->ExeUpdate(DB_WC_CODE, $UpdateCodes, "WHERE code_id = :id",
								"id={$HomeCodes['code_id']}");
						} elseif (preg_match("/" . str_replace("/", "\/", $HomeCodes['code_condition']) . "/",
							$ActiveCodes)) {
							echo $HomeCodes['code_script'];
							$UpdateCodes = ['code_views' => $HomeCodes['code_views'] + 1];
							$Update->ExeUpdate(DB_WC_CODE, $UpdateCodes, "WHERE code_id = :id",
								"id={$HomeCodes['code_id']}");
						}
					}
					echo "\r\n<!--/WorkControl Codes-->\r\n\r\n\r\n";
				}
				/*if (!empty(SEGMENT_FB_PIXEL_ID)) {
					require '_cdn/wc_track.php';
				}*/
			?>

			<a id="button"></a>
			<!-- js start -->
<!--			<script src="<?php /*= INCLUDE_PATH; */?>/assets/js/jquery.min.js"></script>
-->			<script src="<?= INCLUDE_PATH; ?>/assets/js/popper.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/assets/js/bootstrap.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/assets/js/owl.carousel.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/assets/js/aos.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/assets/js/custom.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/assets/js/ajax-form.js"></script>
		<!--	<script src="<php /*= INCLUDE_PATH; */?>/assets/js/contact-form.js"></script>
			<script src="<php /*= INCLUDE_PATH; */?>/assets/js/jquery.validate.js"></script>-->

			<script>
                AOS.init();
			</script>
			<script>
                $(window).on('load', function () {
                    // Preloader
                    $('.loader').fadeOut();
                    $('.loader-mask').delay(350).fadeOut('slow');
                });
			</script>
			<script>
                var btn = $('#button');

                $(window).scroll(function () {
                    if ($(window).scrollTop() > 300) {
                        btn.addClass('show');
                    }
                    else {
                        btn.removeClass('show');
                    }
                });
                btn.on('click', function (e) {
                    e.preventDefault();
                    $('html, body').animate({ scrollTop: 0 }, '300');
                });
			</script>
			<script>
                window.document.onkeydown = function (e) {
                    if (!e) {
                        e = event;
                    }
                    if (e.keyCode == 27) {
                        lightbox_close();
                    }
                }

                function lightbox_open() {
                    var lightBoxVideo = document.getElementById("VisaChipCardVideo");
                    document.getElementById('light').style.display = 'block';
                    document.getElementById('fade').style.display = 'block';
                    lightBoxVideo.play();
                }

                function lightbox_close() {
                    var lightBoxVideo = document.getElementById("VisaChipCardVideo");
                    document.getElementById('light').style.display = 'none';
                    document.getElementById('fade').style.display = 'none';
                    lightBoxVideo.pause();
                }
			</script>
			<script>
                $(document).ready(function ($) {
                    $(".owl-carousel").owlCarousel({
                        loop: false,
                        margin: 0,
                        dots: false,
                        nav: true,
                        items: 1
                    });
                    var owl = $(".owl-carousel");
                    owl.owlCarousel();
                    $(".next-btn").click(function () {
                        owl.trigger("next.owl.carousel");
                    });
                    $(".prev-btn").click(function () {
                        owl.trigger("prev.owl.carousel");
                    });
                    $(".prev-btn").addClass("disabled");
                    $(owl).on("translated.owl.carousel", function (event) {
                        if ($(".owl-prev").hasClass("disabled")) {
                            $(".prev-btn").addClass("disabled");
                        } else {
                            $(".prev-btn").removeClass("disabled");
                        }
                        if ($(".owl-next").hasClass("disabled")) {
                            $(".next-btn").addClass("disabled");
                        } else {
                            $(".next-btn").removeClass("disabled");
                        }
                    });
                });
			</script>

		</div>
		<!--ACCESS-->
		<?php require './_cdn/widgets/accessibility/accessibility.inc.php'; ?>

		</body>
		</html>
		<?php
	}
	ob_end_flush();

	if (!file_exists('.htaccess')) {
		$htaccesswrite = "RewriteEngine On\r\nOptions All -Indexes\r\n\r\n# WC WWW Redirect.\r\n#RewriteCond %{HTTP_HOST} !^www\. [NC]\r\n#RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n\r\n# WC HTTPS Redirect\r\nRewriteCond %{HTTP:X-Forwarded-Proto} !https\r\nRewriteCond %{HTTPS} off\r\nRewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n\r\n# WC URL Rewrite\r\nRewriteCond %{SCRIPT_FILENAME} !-f\r\nRewriteCond %{SCRIPT_FILENAME} !-d\r\nRewriteRule ^(.*)$ index.php?url=$1";
		$htaccess = fopen('.htaccess', "w");
		fwrite($htaccess, str_replace("'", '"', $htaccesswrite));
		fclose($htaccess);
	}

?>
