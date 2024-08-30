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
			<link href='https://fonts.googleapis.com/css?family=<?= SITE_FONT_NAME; ?>:<?= SITE_FONT_WHIGHT; ?>'
			      rel='stylesheet' type='text/css'>
			<style>* {
                    font-family: '<?= SITE_FONT_NAME; ?>', sans-serif;
                }</style>

			<!-- favicon -->
			<link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>/images/icons/favicon.png">
			<link rel="apple-touch-icon" href="<?= INCLUDE_PATH; ?>/images/icons/apple-touch-icon-57x57.png">
			<link rel="apple-touch-icon" sizes="72x72"
			      href="<?= INCLUDE_PATH; ?>/images/icons/apple-touch-icon-72x72.png">
			<link rel="apple-touch-icon" sizes="114x114"
			      href="<?= INCLUDE_PATH; ?>/images/icons/apple-touch-icon-114x114.png">

			<!--[if lt IE 9]>
            <script src="<?= BASE; ?>/_cdn/html5shiv.js"></script><![endif]-->

			<!-- Include All CSS here-->
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/bootstrap.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/fontawesome-all.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/line-awesome.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/icofont.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/owl.carousel.min.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/owl.theme.default.min.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/slick.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/animate.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/magnific-popup.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/preset.css"/>
			<!-- slider Revolution CSS -->
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/revolution/settings.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/revolution/navigation.css"/>

			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/theme.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/responsive.css"/>
			<link rel="stylesheet" type="text/css" href="<?= INCLUDE_PATH; ?>/css/presets/color2.css" id="colorChange"/>
			<!-- End Include All CSS -->

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

			<h1 class="title-hidden"><?= SITE_ADDR_NAME ?></h1>

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
						if (file_exists(REQUIRE_PATH . "/inc/header.php")) {
							require REQUIRE_PATH . "/inc/header.php";
						} else {
							trigger_error('Crie um arquivo /inc/header.php na pasta do tema!');
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
						if (file_exists(REQUIRE_PATH . "/inc/footer.php")) {
							require REQUIRE_PATH . "/inc/footer.php";
						} else {
							trigger_error('Crie um arquivo /inc/footer.php na pasta do tema!');
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

			<!-- Include All JS -->
			<script src="<?= INCLUDE_PATH; ?>/js/jquery.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/jquery-ui.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/bootstrap.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/jquery.appear.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/owl.carousel.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/slick.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/jquery.shuffle.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/jquery.magnific-popup.min.js"></script>

			<script src="<?= INCLUDE_PATH; ?>/js/modernizr.custom.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/dlmenu.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/jquery.easing.1.3.js"></script>

			<script src="<?= INCLUDE_PATH; ?>/js/jquery.themepunch.tools.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/jquery.themepunch.revolution.min.js"></script>

			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.actions.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.carousel.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.kenburn.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.layeranimation.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.migration.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.navigation.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.parallax.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.slideanims.min.js"></script>
			<script src="<?= INCLUDE_PATH; ?>/js/extensions/revolution.extension.video.min.js"></script>

			<script src='<?= BASE; ?>/_cdn/shadowbox/shadowbox.js'></script>
			<link rel='stylesheet' type='text/css' href='<?= BASE; ?>/_cdn/shadowbox/shadowbox.css'/>
			<script src="<?= INCLUDE_PATH; ?>/js/theme.js"></script>
			<!-- Include All JS -->

			<div class='testimony'>
				<div class='testimony_content'>
					<span class='testimony_close'>X</span>
					<h1>Assistir Vídeo:</h1>
					<div class='embed-container'></div>

					<div class='content_like box'>
						<div class='box_like box box2'>
							<p>Curta no Facebook</p>
							<div class='fb-like box_like_face fb_iframe_widget'
							     data-href='https://facebook.com/<?= SITE_SOCIAL_FB_PAGE ?>' data-layout='standard'
							     data-action='like' data-show-faces='false' data-share='true' data-width='170'
							     fb-xfbml-state='rendered'
							     fb-iframe-plugin-query='action=like&amp;app_id=626590460695980&amp;container_width=0&amp;href=https%3A%2F%2Ffacebook.com%2F<?= SITE_SOCIAL_FB_PAGE ?>&amp;layout=standard&amp;locale=pt_BR&amp;sdk=joey&amp;share=false&amp;show_faces=false&amp;width=170'>
								<span style='vertical-align: bottom; width: 225px; height: 28px;'><iframe
											name='f1f3cf1db2edf6c' width='170px' height='1000px'
											data-testid='fb:like Facebook Social Plugin'
											title='fb:like Facebook Social Plugin' frameborder='0'
											allowtransparency='true' allowfullscreen='true' scrolling='no'
											allow='encrypted-media'
											src='https://www.facebook.com/v2.6/plugins/like.php?action=like&amp;
											app_id=626590460695980&amp;channel=https%3A%2F%2Fstaticxx.facebook
											.com%2Fx%2Fconnect%2Fxd_arbiter%2F%3Fversion%3D46%23cb%3Df36bc7a3fd09cc
											%26domain%3D<?= SITE_ADDR_SITE
											?>%26is_canvas%3Dfalse%26origin%3Dhttps%253A%252F%252F<?= SITE_ADDR_SITE
											?>%252Ff3d2597aa04bd18%26relation%3Dparent.parent&amp;container_width=0&amp;href=https%3A%2F%2Ffacebook.com%2F<?= SITE_SOCIAL_FB_PAGE ?>&amp;layout=standard&amp;locale=pt_BR&amp;sdk=joey&amp;share=false&amp;show_faces=false&amp;width=170'
											class=''
											style='border: none; visibility: visible; width: 225px; height: 28px;'></iframe></span>
							</div>
						</div>
						<div class='box_like box box2'>
							<p>Inscreva-se no YouTube</p>
							<div id='___ytsubscribe_1'
							     style='text-indent: 0px; margin: 0px; padding: 0px; background: transparent; border-style: none; float: none; line-height: normal; font-size: 1px; vertical-align: baseline; display: inline-block; width: 450px; height: 24px;'>
								<iframe ng-non-bindable='' frameborder='0' hspace='0' marginheight='0' marginwidth='0'
								        scrolling='no'
								        style='position: static; top: 0px; width: 167px; margin: 0px; border-style: none; left: 0px; visibility: visible; height: 48px;'
								        tabindex='0' vspace='0' width='100%' id='I0_1699972094654'
								        name='I0_1699972094654'
								        src='https://www.youtube.com/subscribe_embed?usegapi=1&amp;channel=traviplasticos&amp;layout=full&amp;count=default&amp;origin=https%3A%2F%2Fwww.travi.com.br&amp;gsrc=3p&amp;jsh=m%3B%2F_%2Fscs%2Fabc-static%2F_%2Fjs%2Fk%3Dgapi.lb.pt_BR.QGnElhmNq_g.O%2Fd%3D1%2Frs%3DAHpOoo9sYmUA9Em_tDOz-9hNXbfp8V_wsQ%2Fm%3D__features__#_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart%2Concircled%2Cdrefresh%2Cerefresh%2Conload&amp;id=I0_1699972094654&amp;_gfid=I0_1699972094654&amp;parent=https%3A%2F%2Fwww.travi.com.br&amp;pfname=&amp;rpctoken=27351765'
								        data-gapiattached='true'></iframe>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--ACCESS-->
		<?php
			require './_cdn/widgets/accessibility/accessibility.inc.php'; ?>
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
