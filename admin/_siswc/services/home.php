<?php
    $AdminLevel = LEVEL_WC_SERVICES;
    if (!APP_SERVICES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    }

    //AUTO DELETE SERVICE TRASH
    if (DB_AUTO_TRASH) {
        $Delete = new Delete;
        $Delete->ExeDelete(DB_SVC, "WHERE svc_title IS NULL AND svc_description IS NULL and svc_status = :st", "st=0");

        //AUTO TRASH IMAGES
        $Read->FullRead("SELECT image FROM " . DB_SVC_IMAGE . " WHERE svc_id NOT IN(SELECT svc_id FROM " . DB_SVC . ")");
        if ($Read->getResult()) {
            $Delete->ExeDelete(DB_SVC_IMAGE,
                "WHERE id >= :id AND service_id NOT IN(SELECT svc_id FROM " . DB_SVC . ")", "id=1");

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

    $WhereString = (!empty($S) ? " AND (svc_title LIKE '%{$S}%' OR svc_description LIKE '%{$S}%'" : "");
    $WhereOpt = ((!empty($O)) ? " AND (svc_status != 1) " : "");

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode($Search['s']);
        $O = urlencode($Search['opt']);
        header("Location: dashboard.php?wc=services/home&opt={$O}&s={$S}");
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-hammer2">Processos</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Processos
        </p>
    </div>

    <div class="dashboard_header_search">
        <form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
            <select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
                <option value="">Todos</option>
                <option <?= ($O == 'outsale' ? "selected='selected'" : ''); ?> value="outsale">Indisponíveis</option>
            </select>
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>

</header>
<div class="dashboard_content">
    <?php
        $RedirectOpt = (!empty($WhereOpt) ? "&opt=outsale" : "");
        $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager("dashboard.php?wc=services/home{$RedirectOpt}&page=", "<<", ">>", 5);
        $Pager->ExePager($Page, 12);
        $Read->ExeRead(DB_SVC,
            "WHERE 1 = 1 $WhereString $WhereOpt ORDER BY svc_created DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$Read->getResult()) {
            $Pager->ReturnPage();
            echo Erro("<span class='al_center icon-notification'>Ainda não existem serviços cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro serviço!</span>",
                E_USER_NOTICE);
        } else {
            foreach ($Read->getResult() as $Services) {
                extract($Services);
                $SvcImage = ($svc_cover && file_exists("../uploads/{$svc_cover}") && !is_dir("../uploads/{$svc_cover}") ? "uploads/{$svc_cover}" : 'admin/_img/no_image.jpg');
                $SvcTitle = ($svc_title ? Check::Chars($svc_title, 45) : 'Edite este serviço para coloca-lo a venda!');
                $SvcStatus = ($svc_status != 1 ? 'inactive' : '');
                echo "<article class='box box25 single_pdt {$SvcStatus}' id='{$svc_id}'>
                    <div class='single_pdt_thumb'>
                        <img title='{$SvcTitle}' alt='{$SvcTitle}' src='../tim.php?src={$SvcImage}&w=" . THUMB_W . "&h=" . THUMB_H . "'/>
                            <header>
                                <h1><a target='_blank' href='" . BASE . "/servico/{$svc_name}' title='Ver {$SvcTitle} no site'>{$SvcTitle}</a></h1>";

                echo "</header>
                    </div>
                        <div class='single_pdt_actions'>
                            <a title='Editar serviço' href='dashboard.php?wc=services/create&id={$svc_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                            <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$svc_id}'>Excluir</span>
                            <span rel='single_pdt' callback='Services' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$svc_id}'>Remover Serviço?</span>
                        </div>
                    </article>";
            }

            $Pager->ExePaginator(DB_SVC, "WHERE 1 = 1 {$WhereString} {$WhereOpt}");
            echo $Pager->getPaginator();
        }
    ?>
</div>
