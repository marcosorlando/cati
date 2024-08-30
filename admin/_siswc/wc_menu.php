<?php
    if (APP_PRODUCTS_TRAVI && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_PRODUCTS_TRAVI) {
        $wc_pdt_alerts = null;
        $Read->FullRead("SELECT count(pdt_id) as total FROM " . DB_PDT_TRAVI . " WHERE pdt_status != 1");
        if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
            $wc_pdt_alerts .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
        }
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'products/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-bullhorn" title="Produtos"
               href="dashboard.php?wc=products/home">Produtos <?= $wc_pdt_alerts; ?></a>
            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'products/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Ver Produtos" href="dashboard.php?wc=products/home">&raquo; Ver Produto</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput,
                    'products/home&opt=outsale') ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Fora de Estoque ou Inativos" href="dashboard.php?wc=products/home&opt=outsale">&raquo;
                        Indisponíveis <?= $wc_pdt_alerts; ?></a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput,
                    'products/categor') ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Categorias de Produtos" href="dashboard.php?wc=products/categories">&raquo; Categorias</a>
                </li>

                <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput,
                    'products/format') ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Processos de Produtos" href="dashboard.php?wc=products/formats">&raquo; Formatos</a>
                </li>

                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'products/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Novo Produto" href="dashboard.php?wc=products/create">&raquo; Novo Produto</a>
                </li>
            </ul>
        </li>
        <?php
    }
    if (APP_SERVICES && $Admin['user_level'] >= LEVEL_WC_SERVICES) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'services/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-hammer2" href="dashboard.php?wc=services/home">Processos</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'services/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=services/create">&raquo; Novo Processo</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'services/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=services/home">&raquo; Ver Processo</a>
                </li>
            </ul>

        </li>
        <?php
    }
    if (APP_SEGMENTS && $Admin['user_level'] >= LEVEL_WC_SEGMENTS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'segments/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-tree" href="dashboard.php?wc=segments/home">Segmentos</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'segments/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=segments/create">&raquo; Novo Segmentos</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'segments/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=segments/home">&raquo; Ver Segmentos</a>
                </li>
            </ul>

        </li>
        <?php
    }
    if (APP_CURIOSITIES && $Admin['user_level'] >= LEVEL_WC_CURIOSITIES) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'curiosities/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-newspaper" href="dashboard.php?wc=curiosities/create&id=1">Curiosidades</a>
        </li>
        <?php
    }
    if (APP_CV && $Admin['user_level'] >= LEVEL_WC_CV) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'curriculum/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-drawer" href="dashboard.php?wc=curriculos/home">Base de Currículos</a>
        </li>
        <?php
    }

	if (APP_OUVIDORIA && $Admin['user_level'] >= LEVEL_WC_OUVIDORIA) {
		?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'ouvidoria/home') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-bullhorn" href="dashboard.php?wc=ouvidoria/home">Ouvidoria</a>
		</li>
		<?php
	}
    if (APP_REPRESENTATIVES && $Admin['user_level'] >= LEVEL_WC_REPRESENTATIVES) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'representatives/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-user-tie" href="dashboard.php?wc=representatives/home">Representantes</a>
            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'representatives/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=representatives/create">&raquo; Novo Representante</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'representatives/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=representatives/home">&raquo; Representantes </a>
                </li>
            </ul>
        </li>
        <?php
    }
    if (APP_CERTIFICATIONS && $Admin['user_level'] >= LEVEL_WC_CERTIFICATIONS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'certifications/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-file-text" href="dashboard.php?wc=certifications/home">Certificações</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'certifications/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=certifications/create">&raquo; Nova Certificação</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'certifications/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=certifications/home">&raquo; Ver Certificações</a>
                </li>
            </ul>

        </li>
        <?php
    }
    if (APP_HELLO && $Admin['user_level'] >= LEVEL_WC_HELLO) {
        $wc_hellobars_alerts = null;
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'hello/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-bullhorn" title="Hellobar"
               href="dashboard.php?wc=hello/home">Hellobar <?= $wc_hellobars_alerts; ?></a>
        </li>
        <?php
    }
    if (APP_ALBUMS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'albums/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-camera" href="dashboard.php?wc=albums/home">Álbuns de Fotos</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'albums/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=albums/create">&raquo; Novo Álbum</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'albums/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=albums/home">&raquo; Ver Álbuns </a>
                </li>
            </ul>

        </li>
        <?php
    }
    if (APP_LEADS && $Admin['user_level'] >= LEVEL_WC_LEADS) {
        $wc_leads = null;
        $Read->FullRead("SELECT count(lead_id) as total FROM " . DB_LEADS . " WHERE lead_status != 1");
        if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
            $wc_leads .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
        }
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'leads/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-users" title="Leads" href="dashboard.php?wc=leads/home">Base de Leads <?= $wc_leads; ?></a>
        </li>
        <?php
    }
    if (APP_THANKYOU_PAGES && $Admin['user_level'] >= LEVEL_WC_THANKYOU_PAGES) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'thankyoupages/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-heart" href="dashboard.php?wc=thankyoupages/home">Thank You Pages</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'thankyoupages/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=thankyoupages/create">&raquo; Nova Thank You Page</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'thankyoupages/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=thankyoupages/home">&raquo; Ver Thank You Pages </a>
                </li>
            </ul>

        </li>
        <?php
    }
    if (APP_LANDING_PAGES && $Admin['user_level'] >= LEVEL_WC_LANDING_PAGES) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'landingpages/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-download" href="dashboard.php?wc=landingpages/home">Landing Pages</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'landingpages/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=landingpages/create">&raquo; Nova Landing Pages</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'landingpages/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=landingpages/home">&raquo; Ver Landing Pages </a>
                </li>
            </ul>

        </li>
        <?php
    }
    if (APP_MATERIALS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'custom/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-book" title="Materiais" href="dashboard.php?wc=materiais/home">Materiais</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'materiais/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Ver Materiais" href="dashboard.php?wc=materiais/home">&raquo; Ver Materials </a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput,
                    'materiais/categor') ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Categorias" href="dashboard.php?wc=materiais/categories">&raquo; Categorias</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'materiais/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a title="Novo Material" href="dashboard.php?wc=materiais/create">&raquo; Novo Material</a>
                </li>
            </ul>
        </li>
        <?php
    }

    if (APP_DEPOSITIONS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'depositions/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-man-woman" href="dashboard.php?wc=depositions/home">Depoimentos</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'depositions/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=depositions/create">&raquo; Novo Depoimento</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'depositions/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=depositions/home">&raquo; Depoimentos </a>
                </li>
            </ul>
        </li>
        <?php
    }
    if (APP_PARTNERS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'partners/home') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class="icon-users" href="dashboard.php?wc=partners/home">Parceiros</a>

            <ul class="dashboard_nav_menu_sub">
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'partners/create' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=partners/create">&raquo; Novo Parceiro</a>
                </li>
                <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'partners/home' ? 'dashboard_nav_menu_active' : ''; ?>">
                    <a href="dashboard.php?wc=partners/home">&raquo; Parceiros </a>
                </li>
            </ul>
        </li>
        <?php
    }

    if (APP_VIDEOS) {
        ?>
        <li class="dashboard_nav_menu_li <?= strstr($getViewInput,
            'custom/') ? 'dashboard_nav_menu_active' : ''; ?>">
            <a class='icon-youtube' title='Vídeos Youtube' href='dashboard.php?wc=videos/home'>Vídeos Youtube</a>
        </li>
        <?php
    }
?>
