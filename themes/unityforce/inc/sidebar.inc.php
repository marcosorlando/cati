<div class="col-lg-4 col-md-12 col-sm-12 col-12 column">

	<div class="box1" data-aos="fade-up" data-aos-duration="700">
		<h5>Pesquisar artigos</h5>

		<form class="searchForms" name="search" action="" method="post">
			<div class="form-row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<input type="search" name="s" id="search" class="form-control upper_layer"
					       placeholder="Pesquisar artigos...">
					<div class="input-group-append form-button">
						<button type="submit" class="btn search" name="btnsearch" id="searchbtn"><i
									class="fas fa-search"></i></button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="box1 box2" data-aos="fade-up" data-aos-duration="700">
		<h5>Categorias</h5>
		<?php
			$Read = new Read();
			$Read->ExeRead(DB_CATEGORIES,
				"WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM " . DB_POSTS . " WHERE post_status <> 0 AND post_date <= NOW()) ORDER BY category_title ASC");
			if (!$Read->getResult()) {
				echo Erro("Ainda não existem sessões cadastradas!", E_USER_NOTICE);
			} else {
				foreach ($Read->getResult() as $Ses) {
					echo "<a class='mb-3 ds_block text-uppercase bold' title='artigos/{$Ses['category_name']}' href='" .
						BASE .
						"/artigos/{$Ses['category_name']}'>
								<i class='fa fa-tags'></i> {$Ses['category_title']}
							</a>
					";

					$Read->ExeRead(DB_CATEGORIES,
						"WHERE category_parent = :pr AND category_id IN(SELECT post_category_parent FROM " . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW()) ORDER BY category_title ASC",
						"pr={$Ses['category_id']}");
					if ($Read->getResult()) {
						echo "<ul class='list-unstyled mb-0 mt-3 ml-3'>";
						foreach ($Read->getResult() as $Cat) {
							echo "<li class='text-size-14'><a title='artigos/{$Cat['category_name']}' href='" .
								BASE . "/artigos/{$Cat['category_name']}'><i class='fa fa-tag'></i> {$Cat['category_title']}</a></li>";
						}
						echo "</ul>";
					}
				}
			}
		?>
	</div>

	<div class="box1 box3" data-aos="fade-up" data-aos-duration="700">
		<h5>Siga-me</h5>
		<div class="social-icons">
			<ul class="mb-0 list-unstyled">
				<li><a href="https://www.linkedin.in/<?= SITE_SOCIAL_LINKEDIN ?>" class="text-decoration-none"><i
								class="fab fa-linkedin-in social-networks"></i></a>
				</li>
				<li>
					<a href="https://instagram.com/<?= SITE_SOCIAL_INSTAGRAM ?>"
					   class="text-decoration-none"><i
								class="fab fa-instagram social-networks"></i></a></li>
				<li><a href="https://www.facebook.com/<?= SITE_SOCIAL_FB_PAGE ?>" class="text-decoration-none"><i
								class="fab fa-facebook-f social-networks"></i></a>
				</li>
			</ul>
		</div>
	</div>

	<!--<div class="box1 box4" data-aos="fade-up" data-aos-duration="700">
		<h5>Tags</h5>
		<ul class="tag mb-0 list-unstyled">
			<li><a class="button text-decoration-none" href="about.html">Assistant</a></li>
			<li><a class="button button2 text-decoration-none" href="about.html">Advice</a></li>
			<li><a class="button button3 text-decoration-none" href="about.html">Virtual</a></li>
			<li><a class="button button4 text-decoration-none" href="about.html">Designer</a></li>
			<li><a class="button button5 text-decoration-none" href="about.html">Blog</a></li>
			<li><a class="button button6 text-decoration-none" href="about.html">Support</a></li>
			<li><a class="button button7 text-decoration-none" href="about.html">Finance</a></li>
			<li><a class="button button8 text-decoration-none" href="about.html">Projects</a></li>
		</ul>
	</div>-->

	<div class="box1 box5" data-aos="fade-up" data-aos-duration="700">
		<h5><i class="fa fa-laptop"></i> Mais lidos</h5>

		<?php
			$Read->ExeRead(DB_POSTS,
				"WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_views DESC, post_date DESC LIMIT 5");
			if (!$Read->getResult()) {
				echo Erro("Não existem artigos cadastrados. Favor volte mais tarde :)", E_USER_NOTICE);
			} else {
				foreach ($Read->getResult() as $Post) {
					$Post['post_cover'] = $Post['post_cover'] ? "uploads/{$Post['post_cover']}" : 'admin/_img/no_image.jpg';

					echo "<div class='feed'><a href='" . BASE . "/artigo/{$Post['post_name']}'
                                   title='Ler mais sobre {$Post['post_title']}'>";
					echo "<figure class='feed-image mb-0' data-aos='fade-up'>";
					echo "<img src='" . BASE . "/tim.php?src={$Post['post_cover']}&w=600&h=314' alt='{$Post['post_title']}' class='img-fluid' loading='lazy' >";
					echo "</figure>";

					echo "<h6 class='mt-2'>{$Post['post_title']}</h6>";
					echo "</a></div>";
				}
			}
		?>

		<!--<div class="feed">
			<figure class='feed-image mb-0' data-aos='fade-up'>
				<img src="./assets/images/singleblog-feed1.jpg" alt="" class="img-fluid" loading="lazy">
			</figure>
			<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
				Company</a>
		</div>
		<div class="feed">
			<figure class="feed-image mb-0" data-aos="fade-up">
				<img src="./assets/images/singleblog-feed2.jpg" alt="" class="img-fluid" loading="lazy">
			</figure>
			<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
				Company</a>
		</div>
		<div class="feed">
			<figure class="feed-image mb-0" data-aos="fade-up">
				<img src="./assets/images/singleblog-feed3.jpg" alt="" class="img-fluid" loading="lazy">
			</figure>
			<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
				Company</a>
		</div>
		<div class="feed feed4">
			<figure class="feed-image mb-0" data-aos="fade-up">
				<img src="./assets/images/singleblog-feed4.jpg" alt="" class="img-fluid" loading="lazy">
			</figure>
			<a href="six-colum-full-wide.html" class="mb-0">Why You Need Virtual Assistant for Your
				Company</a>
		</div>-->
	</div>

	<div class="box1 box6" data-aos="fade-up" data-aos-duration="700">
		<h5><i class="far fa-calendar-alt"></i> Arquivo Mensal</h5>
		<ul class="list-unstyled">
			<?php
				$Read->FullRead("SELECT DISTINCT post_month FROM " . DB_POSTS . " WHERE post_status = :st AND post_date <= NOW() ORDER BY post_month ASC LIMIT 12",
					"st=1");

				if ($Read->getResult()) {
					foreach ($Read->getResult() as $MesAno) {
						$Pesquisa = BASE . '/pesquisa/' . $MesAno['post_month'];
						echo "<li><a href='{$Pesquisa}?month' title='Ver artigos publicados neste mês.' ><i class='far fa-calendar'></i> " . getWcMonths($MesAno['post_month']) . "</a></li>";

					}
				}
			?>
		</ul>
	</div>

</div>
