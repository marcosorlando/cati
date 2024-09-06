<!-- Preloader -->
<div class="loader-mask">
	<div class="loader">
		<div></div>
		<div></div>
	</div>
</div>
<!-- Preloader -->
<!-- HEADER SECTION START HERE -->
<div class="header-main-con w-100 float-left" id="menu">
	<div class="container-fluid">
		<nav class="navbar navbar-expand-lg navbar-light">
			<a class="navbar-brand" href="<?= BASE; ?>">
				<figure class="mb-0">
					<img id="logo-header" src="<?=INCLUDE_PATH?>/assets/images/header-logo.png" alt="Catiane Zanotto
					para Vereadora
					11112" loading="lazy">
				</figure>
			</a>
			<button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
			        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
			        aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				<span class="navbar-toggler-icon"></span>
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link p-0 " href="<?= BASE; ?>">
							<i class="fas fa-home"></i> HOME
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link p-0 " href="<?= BASE; ?>/sobre">SOBRE MIM</a>
					</li>
					<li class="nav-item">
						<a class="nav-link p-0 " href="<?= BASE; ?>/artigos/minhas-propostas">PROPOSTAS</a>
					</li>
					<li class="nav-item">
						<a class="nav-link p-0" href="<?= BASE; ?>/contato">CONTATO</a>
					</li>

					<?php
						$Read->ExeRead(DB_CATEGORIES,
							"WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM " . DB_POSTS . " WHERE post_status <> 0 AND post_date <= NOW()) ORDER BY category_title ASC");

						if ($Read->getResult()) {
							foreach ($Read->getResult() as $Cat) {
								echo "<li class='nav-item dropdown'>";
								echo "<a class='nav-link dropdown-toggle p-0' id='navbarDropdown5' title=' " .
									SITE_NAME . " | {$Cat['category_title']}' href='" . BASE . "/artigos/{$Cat['category_name']}' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> {$Cat['category_title']} </a>";

								$Read->ExeRead(DB_CATEGORIES,
									"WHERE category_parent = :ct ORDER BY category_name ASC",
									"ct={$Cat['category_id']}");
								if ($Read->getResult()) {
									echo "<div class='dropdown-menu' aria-labelledby='navbarDropdown5'>";
									echo "<ul class='list-unstyled mb-0'>";
									foreach ($Read->getResult() as $SubCat) {
										echo "<li><a class='dropdown-item' title='{$Cat['category_title']} | {$SubCat['category_title']}' href='" . BASE . "/artigos/{$SubCat['category_name']}'>{$SubCat['category_title']}</a></li>";
									}
									echo "</ul>";
									echo "</div>";
								}
								echo "</li>";
							}
						}
					?>
				</ul>
			</div>
			<div class="nav-btns d-flex align-items-center">
				<ul class="list-unstyled mb-0 d-flex">
					<li><a target="_blank" href="https://www.facebook.com/<?= SITE_SOCIAL_FB_PAGE ?>"><i class="fab
					fa-facebook-f"></i></a></li>
					<li><a target="_blank" href="https://instagram.com/<?= SITE_SOCIAL_INSTAGRAM ?>"><i class="fab
					fa-instagram"></i></a></li>
					<li><a target="_blank" href="https://www.linkedin.com/in/<?= SITE_SOCIAL_LINKEDIN?>"><i class="fab
					fa-linkedin"></i></a></li>
				</ul>
				<div class="donate-btn">
					<a href="javascript:alert('Vaquinha')">
						<i class="fas fa-hand-holding-usd"></i> DOAÇÃO
					</a>
				</div>
				<div class="header-contact-btn">
					<a target="_blank" title="Envie uma Mensagem para Cati" href="<?=Check::WhatsMessage(SITE_ADDR_WHATS, 'Olá Catiane,...')?>"><i class="fab fa-whatsapp"></i> <?= SITE_ADDR_WHATS ?></a>
				</div>
			</div>
		</nav>
	</div>
</div>
<!-- HEADER SECTION END HERE -->
