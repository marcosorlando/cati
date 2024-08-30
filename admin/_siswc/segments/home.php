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
        <h1 class="icon-books">Segmentos</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Segmentos
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
        $Pager = new Pager("dashboard.php?wc=segments/home{$RedirectOpt}&page=", "<<", ">>", 5);
        $Pager->ExePager($Page, 12);
        $Read->ExeRead(DB_SEG,
            "WHERE 1 = 1 $WhereString $WhereOpt ORDER BY seg_created DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$Read->getResult()) {
            $Pager->ReturnPage();
            echo Erro("<span class='al_center icon-notification'>Ainda não existem segmentos cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro segmento!</span>",
                E_USER_NOTICE);
        } else {
            foreach ($Read->getResult() as $Segments) {
                extract($Segments);
                $SegImage = ($seg_cover && file_exists("../uploads/{$seg_cover}") && !is_dir("../uploads/{$seg_cover}") ? "uploads/{$seg_cover}" : 'admin/_img/no_image.jpg');
                $SegTitle = ($seg_title ? Check::Chars($seg_title, 45) : 'Edite este segmento!');
                $SegStatus = ($seg_status != 1 ? 'inactive' : '');
                echo "<article class='box box25 single_pdt {$SegStatus}' id='{$seg_id}'>
                    <div class='single_pdt_thumb'>
                        <img title='{$SegTitle}' alt='{$SegTitle}' src='../tim.php?src={$SegImage}&w=" . THUMB_W . "&h=" . THUMB_H . "'/>
                            <header>
                                <h1><a target='_blank' href='" . BASE . "/segmento/{$seg_name}' title='Ver {$SegTitle} no site'>{$SegTitle}</a></h1>";

                echo "</header>
                    </div>
                        <div class='single_pdt_actions'>
                            <a title='Editar produto' href='dashboard.php?wc=segments/create&id={$seg_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                            <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$seg_id}'>Excluir</span>
                            <span rel='single_pdt' callback='Segments' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$seg_id}'>Remover Segmento?</span>
                        </div>
                    </article>";
            }

            $Pager->ExePaginator(DB_SEG, "WHERE 1 = 1 {$WhereString} {$WhereOpt}");
            echo $Pager->getPaginator();
        }
    ?>
</div>