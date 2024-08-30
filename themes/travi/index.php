<?php
	require '_cdn/widgets/slide/slide.wc.php';
	?>
<section class="commonSection" id="content">
	<div class="container">
		<div class="row">
			<div class="col-xl-6 col-lg-6 noPaddingRight">
				<div class="video_01 mrm15 text-right">
					<img src="<?= INCLUDE_PATH ?>/images/home/01-index.jpg" alt="Travi Plásticos Industriais"/>
					<div class="vp">
						<img src="<?= INCLUDE_PATH ?>/images/home/02-index.png" alt="Assista o Vídeo Institucional"/>
						<a class="videoPlayer" title="Assistir o Vídeo Institucional da Travi Plásticos"
						   href="https://www.youtube.com/watch?v=zrR0_uwwXRI"><i class="fa fa-play"></i></a>
					</div>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6">
				<div class="about_us_content">
					<h6 class="sub_title">Olá somos à</h6>
					<h2 class="sec_title">Travi Plásticos</h2>
					<p class="ind_lead">Empresa pioneira no Brasil a fabricar plásticos de engenharia de alto
						desempenho.
					</p>
					<p class="mb28">
						Disponibilizamos ao mercado a mais alta tecnologia nos processos de: injeção, extrusão,
						fundição, sinterização, usinagem e matrizaria. Processamos diversas matérias-primas tais como:
						nylon, poliacetal, poliuretano, polietileno de alto e ultra alto peso molecular (UHMW),
						policarbonato, PEEK, PPSU, PVDF, polipropileno, entre outros. </p>
					<img src="<?= INCLUDE_PATH ?>/images/sign.png" alt="Assinatura do CEO"/>
				</div>
			</div>
		</div>
	</div>
</section>

<section id="soluctions" class="commonSection graySection">
	<div class="container">
		<div class="row">
			<div class="col-xl-12 text-center">
				<h6 class="sub_title gray_sub_title">Solução em Plásticos Industrias</h6>
				<h2 class="sec_title with_bar">
					<span><i class="fal fa-user-hard-hat"></i><span>Nossas Soluções</span></span>
				</h2>
			</div>
		</div>

		<div class="row align-content-md-stretch">
			<?php
				$Read->FullRead("SELECT svc_title, svc_subtitle, svc_name, svc_cover, svc_icon FROM " . DB_SVC . " WHERE svc_status >= :st",
					"st=1");

				if (!$Read->getResult()) {
					Erro("Nenhum serviço cadastrado. Favor Volte mais tarde.");
				} else {
					foreach ($Read->getResult() as $Svc) {
						extract($Svc);
						$svc_cover = $svc_cover ? "uploads/{$svc_cover}" : 'admin/_img/no_image.jpg';
						$svc_icon = $svc_icon ? "uploads/{$svc_icon}" : 'admin/_img/no_image.jpg';
						?>
						<div class="col-lg-4 col-md-6">
							<a class="service-link" href="<?= BASE . "/servico/{$svc_name}"; ?>">
								<div class="icon_box_01 text-center">

									<img class="svc-icon" src="<?= BASE ?>/tim.php?src=<?= $svc_icon ?>" alt="<?=
										$svc_title;
									?>">
									<span></span>
									<h3><?= $svc_title; ?></h3>
									<p><?= $svc_subtitle; ?></p>
								</div>
							</a>
						</div>
						<?php
					}
				}
			?>
		</div>
	</div>
</section>

<?php
	//require REQUIRE_PATH. '/inc/cases.php';
	require REQUIRE_PATH. '/inc/curiosities.php';
	// ?>

<section id="segments" class="commonSection">
	<div class="container">
		<div class="row">
			<div class="col-xl-12 text-left">
				<h6 class="sub_title ">A Travi Plásticos atua nos seguintes </h6>
				<h2 class="sec_title with_bar">
					<span>Segmentos</span>
				</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-xl-12">
				<div class="serviceSlider owl-carousel align-content-md-stretch">
					<?php
						$Read->FullRead("SELECT seg_title, seg_name, seg_subtitle, seg_cover, seg_icon FROM " . DB_SEG .
							" WHERE seg_status >= :st",
							"st=1");
						if (!$Read->getResult()) {
							Erro("Nenhum segmento cadastrado. Favor Volte mais tarde");
						} else {
							foreach ($Read->getResult() as $Seg) {
								extract($Seg);
								$Seg['seg_icon'] = $Seg['seg_icon'] ? "uploads/{$Seg['seg_icon']}" : 'admin/_img/no_image.jpg';

								echo "<div class='icon_box_03'>";
								echo "<a href='" . BASE . "/segmento/{$seg_name}'><img alt='{$seg_title}' src='" .
									BASE . "/tim.php?src={$Seg['seg_icon']}&w=62&h=62'></a>";
								echo "<h3><a href='" . BASE . "/segmento/{$seg_name}'>{$seg_title}</a></h3>";
								echo "<p>{$seg_subtitle}</p>";
								echo "</div>";
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="whyChooseUs">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xl-5 col-lg-6 noPadding">
				<div class="video_02 withBGImg div_gray">
					<a class="videoPlay1 videoPlayer" href="https://www.youtube.com/watch?v=HAgUotIjLVw"
					   title="Conheça os Processos da Travi Plásticos"><i class="fas fa-play"></i></a>

				</div>
			</div>
			<div class="col-xl-7 col-lg-6 noPaddingRight">
				<div class="whyChooseUsContent">
					<h6 class="sub_title">Porque escolher-nos</h6>
					<h2 class="sec_title dark_sec_title">
						<span>Oferecemos uma grande variedade de produtos e serviços</span>
					</h2>
					<p>Fornecedor
						<b>único</b> para várias demandas de Plásticos Técnicos.
					</p>
					<a id="cotacao_btn_index" href="https://marketing.travi.com.br/orcamento-site" target="_blank"
					   class="ind_btn"><span
								class="fa
					fa-arrow-alt-right"> Obter uma cotação</span></a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
	require_once __DIR__.'/inc/depositions.php';

	$Read->ExeRead(DB_PARTNERS, "ORDER BY partner_name ASC");
	if ($Read->getResult()) {
		?>
		<section class="commonSection clientSecion div_gray">
			<div class="container">
				<div class="row">
					<div class="col-xl-12 text-center">
						<h6 class="sub_title light_sub_title">Nossos Parceiros</h6>
						<h2 class="sec_title with_bar light_sec_title">
							<span>Quem confia?</span>
						</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="clientSlider owl-carousel">
							<?php
								foreach ($Read->getResult() as $Partners) {
									extract($Partners);
									?>
									<div class="csItem">
										<a href="<?= $partner_page; ?>" title="<?= $partner_name; ?>">
											<img src="tim.php?src=uploads/<?= $partner_image ?>&w=98&h=95"
											     alt="<?= $partner_name; ?> - Logotipo"/>
										</a>
									</div>
									<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
			</div>
		</section>
		<?php
	} ?>

<section class="commonSection newsSection">
	<div class="container">
		<div class="row">
			<div class="col-xl-12 text-center">
				<h6 class="sub_title">Quer ficar por dentro das Novidades do setor</h6>
				<h2 class="sec_title with_bar">
					<span>Já conhece nosso Blog?</span>
				</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-xl-7 col-md-7">
				<?php
					$Read->FullRead("SELECT p.*, a.user_name FROM " . DB_POSTS . " p INNER JOIN " . DB_USERS . " a ON p.post_author = a.user_id WHERE p.post_status = :st AND p.post_date < NOW() ORDER BY p.post_id DESC LIMIT :limit",
						"st=1&limit=1");

					if (!$Read->getResult()) {
						Erro("Ainda não temos artigos cadastrados. Volte mais tarde!", E_USER_NOTICE);
					} else {
						foreach ($Read->getResult() as $Post) {
							extract($Post);
							?>
							<div class="blogItem">
								<div class="bi_thumb">
									<img src="<?= BASE . "/tim.php?src=uploads/{$post_cover}&w=1200&h=628"; ?>"
									     alt="<?= $post_title; ?>" title="<?= $post_title; ?>"/>
								</div>
								<div class="bi_details">
									<div class="bi_meta">
                                        <span><i class="fal fa-calendar-alt"></i><a
			                                        href="<?= BASE . "/artigo/{$post_name}"; ?>"
			                                        title="<?= $post_title; ?>"><time
				                                        datetime="<?= date('Y-m-d', strtotime($post_date)); ?>"
				                                        pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y",
				                                        strtotime($post_date))); ?></time></a></span>
										<span><i class="fal fa-user"></i>Por <a
													href="<?= BASE . "/artigo/{$post_name}"; ?>"
													title="<?= $post_title; ?>"><?= $user_name; ?></a></span>
										<span><i class="fal fa-eye"></i><a href="<?= BASE . "/artigo/{$post_name}"; ?>"
										                                   title="<?= $post_title; ?>"><?= $post_views; ?></a></span>
									</div>
									<h3>
										<a href="<?= BASE . "/artigo/{$post_name}"; ?>"
										   title="<?= $post_title; ?>"><?= $post_title; ?></a>
									</h3>
									<p><?= Check::Chars($post_subtitle, 70); ?></p>
									<a href="<?= BASE . "/artigo/{$post_name}"; ?>" title="<?= $post_title; ?>"
									   class="read_more">Continue Lendo</a>
								</div>
							</div>
							<?php
						}
					}
				?>
			</div>
			<div class="col-xl-5 col-md-5">
				<?php
					$Read->FullRead("SELECT post_name, post_title, post_date, post_views FROM " . DB_POSTS . " WHERE post_status = :st AND post_date < NOW() AND post_id < (SELECT MAX(post_id) FROM " . DB_POSTS . ") ORDER BY post_id DESC LIMIT :limit",
						"st=1&limit=4");

					if ($Read->getResult()) {
						foreach ($Read->getResult() as $Post) {
							extract($Post);
							?>
							<div class="blogItem2">
								<div class="bi_meta">
                                    <span><i class="fal fa-calendar-alt"></i><a
			                                    href="<?= BASE . "/artigo/{$post_name}"; ?>"
			                                    title="<?= $post_title; ?>"><time
				                                    datetime="<?= date('Y-m-d', strtotime($post_date)); ?>"
				                                    pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y",
				                                    strtotime($post_date))); ?></time></a></span>
									<span><i class="fal fa-eye"></i><a href="#"><?= $post_views; ?></a></span>
								</div>
								<h3>
									<a href="<?= BASE . "/artigo/{$post_name}"; ?>"
									   title="<?= $post_title; ?>"><?= $post_title; ?></a>
								</h3>
							</div>
							<?php
						}
					}
				?>
			</div>
		</div>
	</div>
</section>
