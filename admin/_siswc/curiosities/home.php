<?php
    $AdminLevel = LEVEL_WC_SEGMENTS;
    if (!APP_SEGMENTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    }

    //AUTO DELETE SEGMENT TRASH
    if (DB_AUTO_TRASH) {
        $Delete = new Delete;
        $Delete->ExeDelete(DB_SEG, "WHERE seg_title IS NULL AND seg_description IS NULL and seg_status = :st", "st=0");

        //AUTO TRASH IMAGES
        $Read->FullRead("SELECT image FROM " . DB_SEG_IMAGE . " WHERE segment_id NOT IN(SELECT seg_id FROM " . DB_SEG . ")");
        if ($Read->getResult()) {
            $Delete->ExeDelete(DB_SEG_IMAGE,
                "WHERE id >= :id AND segment_id NOT IN(SELECT seg_id FROM " . DB_SEG . ")", "id=1");
            foreach ($Read->getResult() as $ImageRemove) {
                if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")) {
                    unlink("../uploads/{$ImageRemove['image']}");
                }
            }
        }
    }

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)) {
        $Read = new Read;
    }

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)) {
        $Create = new Create;
    }

    $S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
    $O = filter_input(INPUT_GET, "opt", FILTER_DEFAULT);

    $WhereString = (!empty($S) ? " AND (seg_title LIKE '%{$S}%' OR seg_description LIKE '%{$S}%'" : "");
    $WhereOpt = ((!empty($O)) ? " AND (seg_status != 1) " : "");

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode($Search['s']);
        $O = urlencode($Search['opt']);
        header("Location: dashboard.php?wc=segments/home&opt={$O}&s={$S}");
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-newspaper">Curiosidades</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Curiosidades
        </p>
    </div>
</header>

<div class="dashboard_content">

	<style>
        .funfactSection .container .row{
            display: flex;
            justify-content: flex-start;
            align-items: stretch;

        }
        .funfactSection .container .row .col-xl-6{
            flex-basis: 50%;
            border: 1px dashed red;
        }

        .funfactSection .container .bg-image{
            height: 500px;
        }
        .funfactSection .container .curiosities{}

	</style>

	<section class="funfactSection">
		<div class="container">
			<div class="row">

				<div class="col-xl-6 bg-image" style="background: url('<?=INCLUDE_PATH?>/images/about/a-travi-page952X1033.jpg') no-repeat center center /
						contain">

				</div>

				<div class="col-xl-6 curiosities">
					<input class="h6" type="text" value="" placeholder="Curiosidades">
					<input class="h2" type="text" value="" placeholder="Curiosidades">

					<div class="row">
						<div class="col-xl-6 col-md-6 col-lg-6">

							<div class="icon_box_02">
								<i class="fal fa-users"></i>
								<h3>
									<span><span data-counter="1" class="timer">1</span>M+</span> Colaboradores Felizes
								</h3>
								<p>Colaboradores felizes trabalham em Empresas de Sucesso! Faça o colaborador se sentir
									parte do todo!</p>
							</div>

							<div class="icon_box_02">
								<i class="fab fa-trade-federation"></i>
								<h3>
                                <span><span data-counter="<?= (date('Y') - 1972); ?>" class="timer"></span>
                                    Anos+</span> de Fundação
								</h3>
								<p>O sucesso de uma empresa é o resultado da dedicação e comprometimento de uma grande
									equipe </p>
							</div>

						</div>
						<div class="col-xl-6 col-md-6 col-lg-6">
							<div class="icon_box_02 ">
								<i class="fal fa-rocket"></i>
								<h3>
									<span><span data-counter="2" class="timer">2</span>M+</span> Projetos Entregues
								</h3>
								<p>Fazer o melhor sempre! Não importa se o projeto é grande e demorado. Nosso cliente ficará
									feliz e irá se sentir especial!</p>
							</div>
							<div class="icon_box_02">
								<i class="fal fa-leaf" style="color: #0B8D4D"></i>
								<h3>
									<span><span data-counter="50" class="timer"></span>T+</span> Resíduos desviados do
									aterro
								</h3>
								<p>Um orgulho para a Empresa! O meio ambiente agradece! </p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

</div>
