<?php
    ob_start();
    session_start();
    require '../_app/Config.inc.php';
    require '../_cdn/cronjob.php';

    if (isset($_SESSION['userLogin']) && isset($_SESSION['userLogin']['user_level']) && $_SESSION['userLogin']['user_level'] >= 6) {
        $Read = new Read;
        $Read->FullRead("SELECT user_level FROM " . DB_USERS . " WHERE user_id = :user", "user={$_SESSION['userLogin']['user_id']}");
        if (!$Read->getResult() || $Read->getResult()[0]['user_level'] < 6) {
            unset($_SESSION['userLogin']);
            header('Location: ./index.php');
            exit;
        } else {
            $Admin = $_SESSION['userLogin'];
            $Admin['user_thumb'] = (!empty($Admin['user_thumb']) && file_exists("../uploads/{$Admin['user_thumb']}") && !is_dir("../uploads/{$Admin['user_thumb']}") ? $Admin['user_thumb'] : '../admin/_img/no_avatar.jpg');
            $DashboardLogin = true;
        }
    } else {
        unset($_SESSION['userLogin']);
        header('Location: ./index.php');
        exit;
    }

    $AdminLogOff = filter_input(INPUT_GET, 'logoff', FILTER_VALIDATE_BOOLEAN);
    if ($AdminLogOff) {
        $_SESSION['trigger_login'] = Erro("<b>LOGOFF:</b> Olá {$Admin['user_name']}, você desconectou com sucesso do " . ADMIN_NAME . ", volte logo!");
        unset($_SESSION['userLogin']);
        header('Location: ./index.php');
        exit;
    }

    $getViewInput = filter_input(INPUT_GET, 'wc', FILTER_DEFAULT);
    $getView = ($getViewInput == 'home' ? 'home' . ADMIN_MODE : $getViewInput);

    //PARA SUA SEGURANÇA, NÃO REMOVA ESSA VALIDAÇÃO!
/*    if (!file_exists("dashboard.json")) {
        echo "<span class='wc_domain_license icon-key icon-notext wc_tooltip radius'></span>";
    }*/

    //SITEMAP GENERATE (1X DAY)
    $SiteMapCheck = fopen('sitemap.txt', "a+");
    $SiteMapCheckDate = fgets($SiteMapCheck);
    if ($SiteMapCheckDate != date('Y-m-d')) {
        $SiteMapCheck = fopen('sitemap.txt', "w");
        fwrite($SiteMapCheck, date('Y-m-d'));
        fclose($SiteMapCheck);

        $SiteMap = new Sitemap;
        $SiteMap->exeSitemap(DB_AUTO_PING);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= ADMIN_NAME; ?> - <?= SITE_NAME; ?></title>
    <meta name="description" content="<?= ADMIN_DESC; ?>"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
    <meta name="robots" content="noindex, nofollow"/>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Source+Code+Pro:300,500' rel='stylesheet' type='text/css'>
    <link rel="base" href="<?= BASE; ?>/admin/">
    <link rel="shortcut icon" href="_img/favicon.png" />

    <link rel="stylesheet" href="../_cdn/datepicker/datepicker.min.css"/>
    <link rel="stylesheet" href="_css/reset.css"/>
    <link rel="stylesheet" href="_css/workcontrol.css"/>
    <link rel="stylesheet" href="_css/workcontrol-860.css" media="screen and (max-width: 860px)"/>
    <link rel="stylesheet" href="_css/workcontrol-480.css" media="screen and (max-width: 480px)"/>
    <link rel="stylesheet" href="../_cdn/bootcss/fonticon.css"/>

    <script src="../_cdn/jquery.js"></script>
    <script src="../_cdn/jquery.form.js"></script>
    <script src="_js/workcontrol.js"></script>

    <script src="_js/tinymce/tinymce.min.js"></script>
    <script src="_js/maskinput.js"></script>
    <script src="_js/workplugins.js"></script>

    <script src="../_cdn/highcharts.js"></script>
    <script src="../_cdn/datepicker/datepicker.min.js"></script>
    <script src="../_cdn/datepicker/datepicker.pt-BR.js"></script>
</head>
<body class="dashboard_main">
<div class="workcontrol_upload workcontrol_loadmodal">
    <div class="workcontrol_upload_bar">
        <img class="m_botton" width="50" src="_img/load_w.gif" alt="Processando requisição!" title="Processando requisição!"/>
        <p><span class="workcontrol_upload_progrees">0%</span> - Processando requisição!</p>
    </div>
</div>

<div class="dashboard_fix">
    <?php
        if (isset($_SESSION['trigger_controll'])) {
            echo "<div class='trigger_modal' style='display: block'>";
            Erro("<span class='icon-warning'>{$_SESSION['trigger_controll']}</span>", E_USER_ERROR);
            echo "</div>";
            unset($_SESSION['trigger_controll']);
        }
    ?>

    <nav class="dashboard_nav">
        <div class="dashboard_nav_admin">
            <img class="dashboard_nav_admin_thumb rounded" alt="" title="" src="../tim.php?src=uploads/<?= $Admin['user_thumb']; ?>&w=76&h=76"/>
            <p><a href="dashboard.php?wc=users/create&id=<?= $Admin['user_id']; ?>" title="Meu Perfil"><?= $Admin['user_name']; ?> <?= $Admin['user_lastname']; ?></a></p>
        </div>
        <ul class="dashboard_nav_menu">
            <li class="dashboard_nav_menu_li <?= $getViewInput == 'home' ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-home" title="Dashboard" href="dashboard.php?wc=home">Dashboard</a></li>

            <?php
                if (APP_POSTS && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_POSTS) {
                    $wc_posts_alerts = null;
                    $Read->FullRead("SELECT count(post_id) as total FROM " . DB_POSTS . " WHERE post_status != 1");
                    if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
                        $wc_posts_alerts .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
                    }
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'posts/') ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-blog" title="Posts" href="dashboard.php?wc=posts/home">Posts <?= $wc_posts_alerts; ?></a>
                        <ul class="dashboard_nav_menu_sub">
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'posts/home' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Ver Posts" href="dashboard.php?wc=posts/home">&raquo; Ver Posts <?= $wc_posts_alerts; ?></a></li>
                            <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput, 'posts/categor') ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Categorias" href="dashboard.php?wc=posts/categories">&raquo; Categorias</a></li>
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'posts/create' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Novo Post" href="dashboard.php?wc=posts/create">&raquo; Novo Post</a></li>
                        </ul>
                    </li>
                    <?php
                }

                if (APP_COMMENTS && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_COMMENTS) {
                    $wc_comment_alerts = null;
                    $Read->FullRead("SELECT count(id) as total FROM " . DB_COMMENTS . " WHERE status != 1 AND alias_id IS NULL");
                    if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
                        $wc_comment_alerts .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
                    }
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'comments/') ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-bubbles2" title="Comentários" href="dashboard.php?wc=comments/home">Comentários<?= $wc_comment_alerts; ?></a></li>
                    <?php
                }

                //SISWC verifica personalizações!
                if (ADMIN_WC_CUSTOM && file_exists(__DIR__ . "/_siswc/wc_menu.php")) {
                    require __DIR__ . "/_siswc/wc_menu.php";
                }

                if (APP_SLIDE && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_SLIDES) {
                    $wc_slide_alerts = null;
                    $Read->FullRead("SELECT count(slide_id) as total FROM " . DB_SLIDES . " WHERE slide_end <= NOW()");
                    if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
                        $wc_slide_alerts .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
                    }
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'slide/') ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-images" title="Em destaque" href="dashboard.php?wc=slide/home">Slides<?= $wc_slide_alerts; ?></a>
                        <ul class="dashboard_nav_menu_sub">
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'slide/home' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Destaques ativos" href="dashboard.php?wc=slide/home">&raquo; Em Destaque</a></li>
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'slide/end' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Agendados ou Inativos" href="dashboard.php?wc=slide/end">&raquo; Slides Inativos<?= $wc_slide_alerts; ?></a></li>
                        </ul>
                    </li>
                    <?php
                }

                if (APP_PAGES && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_PAGES) {
                    $wc_pages_alerts = null;
                    $Read->FullRead("SELECT count(page_id) as total FROM " . DB_PAGES . " WHERE page_status != 1");
                    if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
                        $wc_pages_alerts .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
                    }
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'pages/') ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-pagebreak" title="Páginas" href="dashboard.php?wc=pages/home">Páginas<?= $wc_pages_alerts; ?></a></li>
                    <?php
                }

                if (APP_USERS && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_USERS) {
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'users/') ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-users" title="Usuários" href="dashboard.php?wc=users/home">Usuários</a>
                        <ul class="dashboard_nav_menu_sub">
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'users/home' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Ver Usuários" href="dashboard.php?wc=users/home">&raquo; Ver Usuários</a></li>
                            <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput, 'users/home&opt=customers') ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Clientes" href="dashboard.php?wc=users/home&opt=customers">&raquo; Clientes</a></li>
                            <li class="dashboard_nav_menu_sub_li <?= strstr($getViewInput, 'users/home&opt=team') ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Equipe" href="dashboard.php?wc=users/home&opt=team">&raquo; Equipe</a></li>
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'users/create' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Novo Usuário" href="dashboard.php?wc=users/create">&raquo; Novo Usuário</a></li>
                        </ul>
                    </li>
                    <?php
                }

                if ($_SESSION['userLogin']['user_level'] >= LEVEL_WC_REPORTS) {
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'report') ? 'dashboard_nav_menu_active' : ''; ?>"><a class="icon-pie-chart" title="Relatório" href="dashboard.php?wc=reports/home">Relatórios</a>
                        <ul class="dashboard_nav_menu_sub top">
                            <li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'reports/home' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Relatório de Acessos" href="dashboard.php?wc=reports/home">&raquo; Acessos</a></li>

                        </ul>
                    </li>
                    <?php
                }

                if ($Admin['user_level'] >= LEVEL_WC_CONFIG_MASTER || $Admin['user_level'] >= LEVEL_WC_CONFIG_API || $Admin['user_level'] >= LEVEL_WC_CONFIG_CODES) {
                    ?>
                    <li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'config/') ? 'dashboard_nav_menu_active' : ''; ?>"><a style="cursor: default;" onclick="return false;" class="icon-cogs" title="Configurações" href="#">Configurações</a>
                        <ul class="dashboard_nav_menu_sub top">
                            <?php if ($Admin['user_level'] >= LEVEL_WC_CONFIG_MASTER) { ?><li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'config/home' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Configurações Gerais" href="dashboard.php?wc=config/home">&raquo; Configurações Gerais</a></li><?php } ?>
                            <?php if ($Admin['user_level'] >= LEVEL_WC_CONFIG_MASTER) { ?><li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'config/license' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Licenciar Domínio" href="dashboard.php?wc=config/license">&raquo; Licenciar Domínio</a></li><?php } ?>
                            <?php if ($Admin['user_level'] >= LEVEL_WC_CONFIG_CODES) { ?><li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'config/codes' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="Gerenciar Pixels" href="dashboard.php?wc=config/codes">&raquo; Gerenciar Pixels</a></li><?php } ?>
                            <?php if ($Admin['user_level'] >= LEVEL_WC_CONFIG_API) { ?><li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'config/wcapi' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="WorkControl API" href="dashboard.php?wc=config/wcapi">&raquo; Zen Control® API</a></li><?php } ?>
                            <?php if ($Admin['user_level'] >= LEVEL_WC_CONFIG_MASTER) { ?><li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'config/sample' ? 'dashboard_nav_menu_active' : ''; ?>"><a title="WorkControl Samples" href="dashboard.php?wc=config/samples">&raquo; Zen Control® Samples</a></li><?php } ?>
                        </ul>
                    </li>
                    <?php
                }
            ?>
            <!--
                    <li class="dashboard_nav_menu_li"><a class="icon-lifebuoy" title="Suporte" href="dashboard.php?wc=home">Suporte</a></li>
                    -->
            <li class="dashboard_nav_menu_li"><a target="_blank" class="icon-forward" title="Ver Site" href="<?= BASE; ?>">Ver Site</a></li>
        </ul>
        <div class="dashboard_nav_normalize"></div>
    </nav>

    <div class="dashboard">
        <?php
            if (file_exists('../DATABASE.sql')) {
                echo "<div>";
                echo Erro("<span class='al_center'><b class='icon-warning'>IMPORTANTE:</b> Para sua segurança delete o arquivo DATABASE.sql da pasta do projeto! <a class='btn btn_yellow' href='dashboard.php?wc=home&database=true' title=''>Deletar Agora!</a></span>", E_USER_ERROR);
                echo "</div>";

                $DeleteDatabase = filter_input(INPUT_GET, 'database', FILTER_VALIDATE_BOOLEAN);
                if ($DeleteDatabase) {
                    unlink('../DATABASE.sql');
                    header('Location: dashboard.php?wc=home');
                    exit;
                }
            }

            if (!file_exists("../license.txt")) {
                echo "<div>";
                echo Erro("<span class='al_center'><b class='icon-warning'>ATENÇÃO:</b> O license.txt não está presente na raiz do projeto. Utilizar o Zen Control® sem esse arquivo caracteriza cópia não licenciada.", E_USER_ERROR);
                echo "</div>";
            }

            if (ADMIN_MAINTENANCE) {
                echo "<div>";
                echo Erro("<span class='al_center'><b class='icon-warning'>IMPORTANTE:</b> O modo de manutenção está ativo. Somente usuários administradores podem ver o site assim!</span>", E_USER_ERROR);
                echo "</div>";
            }

            //DB TEST
            //                $Read->FullRead("SELECT VERSION() as mysql_version");
            //                if ($Read->getResult()) {
            //                    $MysqlVersion = $Read->getResult()[0]['mysql_version'];
            //                    if (!stripos($MysqlVersion, "MariaDB")) {
            //                        echo "<div>";
            //                        echo Erro('<span class="al_center"><b class="icon-warning">ATENÇÃO:</b> O Zen Control® foi projetado com <b>banco de dados MariaDB superior a 10.1</b>, você está usando ' . $MysqlVersion . '!</span>', E_USER_ERROR);
            //                        echo "</div>";
            //                    }
            //                }

            //PHP TEST
            $PHPVersion = phpversion();
            if ($PHPVersion < '5.6') {
                echo "<div>";
                echo Erro('<span class="al_center"><b class="icon-warning">ATENÇÃO:</b> O Zen Control® foi projetado com <b>PHP 5.6 ou superior</b>, a versão do seu PHP é ' . $PHPVersion . '!</span>', E_USER_ERROR);
                echo "</div>";
            }
        ?>
        <div class="dashboard_sidebar">
            <span class="mobile_menu btn btn_blue icon-menu icon-notext"></span>
            <div class="fl_right">
                <span class="dashboard_sidebar_welcome m_right">Bem-vindo(a) ao <?= ADMIN_NAME; ?>, Hoje <?= date('d/m/y H\hi'); ?></span>
                <a class="icon-exit btn btn_red" title="Desconectar do <?= ADMIN_NAME; ?>!" href="dashboard.php?wc=home&logoff=true">Sair!</a>
            </div>
        </div>

        <?php
            //QUERY STRING
            if (!empty($getView)) {
                $includepatch = __DIR__ . '/_sis/' . strip_tags(trim($getView)) . '.php';
            } else {
                $includepatch = __DIR__ . '/_sis/' . 'dashboard.php';
            }

            if (file_exists(__DIR__ . "/_siswc/" . strip_tags(trim($getView)) . '.php')) {
                require_once __DIR__ . "/_siswc/" . strip_tags(trim($getView)) . '.php';
            } elseif (file_exists($includepatch)) {
                require_once($includepatch);
            } else {
                $_SESSION['trigger_controll'] = "<b>OPPSSS:</b> <span class='fontred'>_sis/{$getView}.php</span> ainda está em contrução!";
                header('Location: dashboard.php?wc=home');
                exit;
            }
        ?>
    </div>
</div>
</body>
</html>
<?php
    ob_end_flush();
