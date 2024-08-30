<?php
$AdminLevel = LEVEL_WC_PARTNERS;
if (!APP_PARTNERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_PARTNERS, "WHERE partner_name IS NULL and partner_page IS NULL","");

    //AUTO TRASH IMAGES
//    $Read->FullRead("SELECT image FROM " . DB_PRESENTIAL_IMAGE . " WHERE partner_id NOT IN(SELECT partner_id FROM " . DB_PRESENTIAL . ")");
//    if ($Read->getResult()):
//        $Delete->ExeDelete(DB_PRESENTIAL_IMAGE, "WHERE id >= :id AND partner_id NOT IN(SELECT partner_id FROM " . DB_PRESENTIAL . ")", "id=1");
//        foreach ($Read->getResult() as $ImageRemove):
//            if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
//                unlink("../uploads/{$ImageRemove['image']}");
//            endif;
//        endforeach;
//    endif;
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
    $Read = new Read;
endif;

$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
$Search = filter_input_array(INPUT_POST);
if ($Search && (isset($Search['s']) || isset($Search['status']))):
    $S = (isset($Search['s']) ? urlencode($Search['s']) : $S);
    $SearchCat = (!empty($Search['searchcat']) ? $Search['searchcat'] : null);
    header("Location: dashboard.php?wc=partners/home&s={$S}&cat={$SearchCat}&tag={$T}");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-pen">Parceiros</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="Todos os Parceiros" href="dashboard.php?wc=partners/home">Parceiros</a>
            <?= ($S ? "<span class='crumb'>/</span> <span class='icon-search'>{$S}</span>" : ''); ?>
        </p>
    </div>

    <div class="dashboard_header_search">

        <form style="width: 100%; display: inline-block;" name="searchCategoriesPost" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" value="<?= $S; ?>" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;">
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>
</header>

<div class="dashboard_content">
    <?php
    $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
    $Page = ($getPage ? $getPage : 1);
    $Paginator = new Pager("dashboard.php?wc=partners/home&s={$S}&pg=", '<<', '>>', 5);
    $Paginator->ExePager($Page, 100);
    
    if (!empty($S)):
        $WhereString[0] = "AND ( partner_name LIKE '%' :s '%')";
        $WhereString[1] = "&s={$S}";
    else:
        $WhereString[0] = "";
        $WhereString[1] = "";
    endif;
    
    $Read->FullRead("SELECT * FROM " . DB_PARTNERS . " WHERE 1=1 "
            . "{$WhereString[0]} "
            . "ORDER BY partner_name ASC "
            . "LIMIT :limit OFFSET :offset", "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}{$WhereString[1]}"
    );
    
    if (!$Read->getResult()):
        $Paginator->ReturnPage();
        echo Erro("<span class='al_center icon-notification'>Ainda não existem parceiros cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro parceiro!</span>", E_USER_NOTICE);
    else:
        foreach ($Read->getResult() as $Partner):
            extract($Partner);

            $PartnerCover = (file_exists("../uploads/{$partner_image}") && !is_dir("../uploads/{$partner_image}") ? "uploads/{$partner_image}" : 'admin/_img/no_image.jpg');
            
            $partner_name = (!empty($partner_name) ? $partner_name : 'Edite esse rascunho para poder exibir como parceiro em seu site!');
            
            $CourseDragAndDrop = (empty($segment_title) ? 'wc_draganddrop' : null);
  
            echo "<article class='box box25 post_single {$CourseDragAndDrop}' callback='Depositions' callback_action='partner_order' id='{$partner_id}'>
                <div class='post_single_cover box_content'>
                   <img alt='{$partner_name}' title='{$partner_name}' src='../tim.php?src={$PartnerCover}&w=300&h=200'/></a>
                   
                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title' style='font-size:1.25em;'>{$partner_name}</h1>
                </div>
                
                <div class='post_single_actions'>
                    <a title='Editar Prceiro' href='dashboard.php?wc=partners/create&id={$partner_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$partner_id}'>Deletar</span>
                    <span rel='post_single' callback='Partners' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$partner_id}'>Deletar Parceiro?</span>
                    
                </div>
            </article>";
        endforeach;

        $Paginator->ExePaginator(DB_PARTNERS, "WHERE ( partner_name LIKE '%' :s '%')", "s={$S}"
        );
        echo $Paginator->getPaginator();
    endif;
    ?>
</div>