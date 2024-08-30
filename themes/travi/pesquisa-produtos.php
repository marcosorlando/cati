<?php

	$Search = urldecode($URL[1]);
	$SearchPage = urlencode($Search);

	if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'])):
		$Read->FullRead('SELECT search_id, search_count FROM ' . DB_SEARCH . ' WHERE search_key = :key',
			"key={$Search}");
		if ($Read->getResult()):
			$Update = new Update;
			$DataSearch = [
				'search_count' => $Read->getResult()[0]['search_count'] + 1,
				'search_origin' => 'PROD'
			];

			$Update->ExeUpdate(DB_SEARCH, $DataSearch, 'WHERE search_id = :id',
				"id={$Read->getResult()[0]['search_id']}");
		else:
			$Create = new Create;
			$DataSearch = [
				'search_key' => $Search,
				'search_count' => 1,
				'search_origin' => 'PROD',
				'search_date' => date('Y-m-d H:i:s'),
				'search_commit' => date('Y-m-d H:i:s')
			];
			$Create->ExeCreate(DB_SEARCH, $DataSearch);
		endif;
		$_SESSION['search'][] = $Search;
	endif;
?>
<!-- PAGE TITLE -->
<section class="blog-banner">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12">
				<h1 class="search-title">Pesquisa por <span><?= $Search; ?></span></h1>
			</div>
		</div>
	</div>
</section>
<!-- END PAGE TITLE -->

<section class="commonSection shopLoopPage">
	<div class="container">

		<div class="row">
			<div class="col-xl-8 col-md-12 col-lg-8">
				<div class="row">
					<?php
						$Page = (!empty($URL[2]) ? $URL[2] : 1);
						$Pager = new Pager(BASE . "/pesquisa-produtos/{$SearchPage}/",
							"<i class='fa fa-arrow-alt-left'></i>", "<i class='fa fa-arrow-alt-right'></i>",
							5);
						$Pager->ExePager($Page, 12);

						if (strpos($Search, '-')) {
							$SearchExplode = array_unique(explode('-', $Search), SORT_REGULAR);

							foreach ($SearchExplode as $Pattern => $Key) {
								$Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_TRAVI . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_color LIKE '%' :s '%') ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
									"limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Key}");
							}
						} else {
							$SearchExplode = null;
							$Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_TRAVI . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_title LIKE '%' :s '%' OR pdt_subtitle LIKE '%' :s '%' OR pdt_tags LIKE '%' :s '%' OR pdt_color LIKE '%' :s '%') ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
								"limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}");
						}

						if (!$Read->getResult()) {
							$Pager->ReturnPage();
							echo Erro("Não encontramos conteúdo para a palavra-chave <b class='text-extra-dark-gray'>( {$SearchPage} )</b>.",
								E_USER_NOTICE);

							$Read->FullRead("SELECT p.pdt_title, p.pdt_subtitle, p.pdt_name, p.pdt_cover, p.pdt_created, p.pdt_color, g.id, g.image FROM " . DB_PDT_TRAVI . " p, " . DB_PDT_GALLERY_TRAVI . " g WHERE pdt_status = 1 AND pdt_created<= NOW() ORDER BY pdt_created DESC LIMIT :limit",
								"limit=3");

							if (!$Read->getResult()) {
								echo Erro("Ainda não existem produtos cadastrados para esta pesquisa. Favor volte mais tarde.",
									E_USER_NOTICE);
							} else {
								foreach ($Read->getResult() as $Post) {
									extract($Post);
									require REQUIRE_PATH . '/inc/produto.php';
								}
							}
						} else {
							foreach ($Read->getResult() as $Post) {
								extract($Post);
								$BOX = 1;
								require REQUIRE_PATH . '/inc/produto.php';
							}
						}
					?>
				</div>

				<div class="row mt20">
					<div class="col-lg-12">
						<div class="ind_pagination text-center">
							<?php
								$Pager->ExePaginator(DB_PDT_TRAVI,
									"WHERE pdt_status = 1 AND pdt_created <= NOW() AND(pdt_title LIKE '%' :s '%' OR pdt_subtitle LIKE '%' :s '%' OR pdt_tags LIKE '%' :s '%' OR pdt_color LIKE '%' :s '%')",
									"s={$Search}");
								echo $Pager->getPaginator();
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
				require REQUIRE_PATH . '/inc/products-sidebar.php'; ?>
		</div>
	</div>
</section>
<!-- END PORTFOLIO -->
