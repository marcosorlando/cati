<?php
    $AdminLevel = LEVEL_WC_CERTIFICATIONS;
    if (!APP_CERTIFICATIONS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    }

    //AUTO DELETE CERTIFICATION TRASH
    if (DB_AUTO_TRASH) {
        $Delete = new Delete;
        $Delete->ExeDelete(DB_CERT, "WHERE cert_title IS NULL AND cert_description IS NULL and cert_status = :st", "st=0");
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

    $WhereString = (!empty($S) ? " AND (cert_title LIKE '%{$S}%' OR cert_description LIKE '%{$S}%'" : "");
    $WhereOpt = ((!empty($O)) ? " AND (cert_status != 1) " : "");

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode($Search['s']);
        $O = urlencode($Search['opt']);
        header("Location: dashboard.php?wc=certifications/home&opt={$O}&s={$S}");
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-books">Certificações</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Certificações
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
        $Pager = new Pager("dashboard.php?wc=certifications/home{$RedirectOpt}&page=", "<<", ">>", 5);
        $Pager->ExePager($Page, 12);
        $Read->ExeRead(DB_CERT,
            "WHERE 1 = 1 $WhereString $WhereOpt ORDER BY cert_created DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$Read->getResult()) {
            $Pager->ReturnPage();
            echo Erro("<span class='al_center icon-notification'>Ainda não existem setificações cadastradas {$Admin['user_name']}. Comece agora mesmo criando sua primeira certificação!</span>",
                E_USER_NOTICE);
        } else {
            foreach ($Read->getResult() as $Certificações) {
                extract($Certificações);
                $CertImage = ($cert_cover && file_exists("../uploads/{$cert_cover}") && !is_dir("../uploads/{$cert_cover}") ? "uploads/{$cert_cover}" : 'admin/_img/no_image.jpg');
                $CertTitle = ($cert_title ? Check::Chars($cert_title, 45) : 'Edite esta certificação!');
                $CertStatus = ($cert_status != 1 ? 'inactive' : '');
                echo "<article class='box box25 single_pdt {$CertStatus}' id='{$cert_id}'>
                    <div class='single_pdt_thumb'>
                        <img title='{$CertTitle}' alt='{$CertTitle}' src='../tim.php?src={$CertImage}&w=" . THUMB_W . "&h=" . THUMB_H . "'/>
                            <header>
                                <h1><a target='_blank' href='" . BASE . "/certificacao/{$cert_name}' title='Ver {$CertTitle} no site'>{$CertTitle}</a></h1>";

                echo "</header>
                    </div>
                        <div class='single_pdt_actions'>
                            <a title='Editar certificação' href='dashboard.php?wc=certifications/create&id={$cert_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                            <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$cert_id}'>Excluir</span>
                            <span rel='single_pdt' callback='Certifications' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$cert_id}'>Remover Certificação?</span>
                        </div>
                    </article>";
            }

            $Pager->ExePaginator(DB_CERT, "WHERE 1 = 1 {$WhereString} {$WhereOpt}");
            echo $Pager->getPaginator();
        }
    ?>
</div>