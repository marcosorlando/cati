<?php
    $AdminLevel = LEVEL_WC_PRODUCTS_TRAVI;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    endif;

    //AUTO DELETE PRODUCT TRASH
    if (DB_AUTO_TRASH):
        $Delete = new Delete;
        $Delete->ExeDelete(DB_PDT_TRAVI, "WHERE pdt_title IS NULL AND pdt_content IS NULL and pdt_status = :st", "st=0");

        //AUTO TRASH IMAGES
        $Read->FullRead("SELECT image FROM " . DB_PDT_IMAGE_TRAVI . " WHERE product_id NOT IN(SELECT pdt_id FROM " . DB_PDT_TRAVI . ")");
        if ($Read->getResult()):
            $Delete->ExeDelete(
                DB_PDT_IMAGE_TRAVI,
                "WHERE id >= :id AND product_id NOT IN(SELECT pdt_id FROM " . DB_PDT_TRAVI . ")", "id=1"
            );
            foreach ($Read->getResult() as $ImageRemove):
                if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
                    unlink("../uploads/{$ImageRemove['image']}");
                endif;
            endforeach;
        endif;
    endif;

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

    $S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
    $O = filter_input(INPUT_GET, "opt", FILTER_DEFAULT);

    $WhereString = (!empty($S) ? " AND (pdt_title LIKE '%{$S}%' OR pdt_content LIKE '%{$S}%') " : "");
    $WhereOpt = ((!empty($O)) ? " AND (pdt_status != 1) " : "");

    $Search = filter_input_array(INPUT_POST);
    if ($Search):
        $S = urlencode($Search['s']);
        $O = urlencode($Search['opt']);
        header("Location: dashboard.php?wc=products/home&opt={$O}&s={$S}");
        exit;
    endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-books">Produtos</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span> Produtos
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
        $Pager = new Pager("dashboard.php?wc=products/home{$RedirectOpt}&page=", "<<", ">>", 5);
        $Pager->ExePager($Page, 12);
        $Read->ExeRead(
            DB_PDT_TRAVI,
            " WHERE 1=1 $WhereString $WhereOpt ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}"
        );

        if (!$Read->getResult()):
            $Pager->ReturnPage();
            echo Erro(
                "<span class='al_center icon-notification'>Ainda não existem produtos cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro produto!</span>",
                E_USER_NOTICE
            );
        else:
            foreach ($Read->getResult() as $Products):
                extract($Products);
                $PdtImage = ($pdt_cover && file_exists("../uploads/{$pdt_cover}") && !is_dir("../uploads/{$pdt_cover}") ? "uploads/{$pdt_cover}" : 'admin/_img/no_image.jpg');
                $PdtTitle = ($pdt_title ? Check::Chars($pdt_title, 45) : 'Edite este produto para coloca-lo a venda!');
                $PdtClass = ($pdt_status != 1 ? 'inactive' : '');

                echo "<article class='box box25 single_pdt {$PdtClass}' id='{$pdt_id}'>
            <div class='single_pdt_thumb'>
            <img title='{$PdtTitle}' alt='{$PdtTitle}' src='../tim.php?src={$PdtImage}&w=" . THUMB_W . "&h=" . THUMB_H . "'/>
                <header>
                    <h1><a target='_blank' href='" . BASE . "/produto/{$pdt_name}' title='Ver {$PdtTitle} no site'>{$PdtTitle}</a></h1>";

                $Read->FullRead(
                    "SELECT cat_title FROM " . DB_PDT_CATS_TRAVI . " WHERE cat_id = :cat",
                    "cat={$pdt_category}"
                );
                $Category = ($Read->getResult() ? $Read->getResult()[0]['cat_title'] : 'indefinida');

                $Read->FullRead(
                    "SELECT cat_title FROM " . DB_PDT_CATS_TRAVI . " WHERE cat_id = :cat",
                    "cat={$pdt_subcategory}"
                );
                $SubCategory = ($Read->getResult() ? $Read->getResult()[0]['cat_title'] : 'indefinida');

                echo "</header>
            </div>
            <div class='box_content'>
                <div class='single_pdt_info wc_normalize_height'>
             <p>Cor/Padrão: <b>{$pdt_color}</b></p>
                    <p>Em: <b>{$Category}</b> &raquo; <b>{$SubCategory}</b></p>
                </div>
            </div>
            <div class='single_pdt_actions'>
                <a title='Editar produto' href='dashboard.php?wc=products/create&id={$pdt_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$pdt_id}'>Excluir</span>
                <span rel='single_pdt' callback='ProductsTravi' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$pdt_id}'>Remover Produto?</span>
            </div>
        </article>";
            endforeach;

            $Pager->ExePaginator(DB_PDT_TRAVI, "WHERE 1 = 1 {$WhereString} {$WhereOpt}");
            echo $Pager->getPaginator();

        endif;
    ?>
</div>
