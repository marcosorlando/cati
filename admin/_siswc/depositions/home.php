<?php
$AdminLevel = LEVEL_WC_DEPOSITIONS;
if (!APP_DEPOSITIONS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_DEPOSITIONS, "WHERE depositions_profession IS NULL and depositions_text IS NULL","");

    //AUTO TRASH IMAGES
//    $Read->FullRead("SELECT image FROM " . DB_PRESENTIAL_IMAGE . " WHERE depositions_id NOT IN(SELECT depositions_id FROM " . DB_PRESENTIAL . ")");
//    if ($Read->getResult()):
//        $Delete->ExeDelete(DB_PRESENTIAL_IMAGE, "WHERE id >= :id AND depositions_id NOT IN(SELECT depositions_id FROM " . DB_PRESENTIAL . ")", "id=1");
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
    header("Location: dashboard.php?wc=depositions/home&s={$S}&cat={$SearchCat}&tag={$T}");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-pen">Depoimentos</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="Todos os Depoimentos" href="dashboard.php?wc=depositions/home">Depoimentos</a>
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
    $Paginator = new Pager("dashboard.php?wc=depositions/home&s={$S}&pg=", '<<', '>>', 5);
    $Paginator->ExePager($Page, 100);
    
    if (!empty($S)):
        $WhereString[0] = "AND ( depositions_name LIKE '%' :s '%' OR depositions_profession LIKE '%' :s '%')";
        $WhereString[1] = "&s={$S}";
    else:
        $WhereString[0] = "";
        $WhereString[1] = "";
    endif;
         
    $Read->FullRead("SELECT * FROM " . DB_DEPOSITIONS . " WHERE 1=1 "
            . "{$WhereString[0]} "
            . "ORDER BY depositions_order ASC, depositions_name ASC "
            . "LIMIT :limit OFFSET :offset", "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}{$WhereString[1]}"
    );
            
    if (!$Read->getResult()):
        $Paginator->ReturnPage();
        echo Erro("<span class='al_center icon-notification'>Ainda não existem depoimentos cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro depoimento!</span>", E_USER_NOTICE);
    else:
        foreach ($Read->getResult() as $Deposition):
            extract($Deposition);

            $DepositionCover = (file_exists("../uploads/{$depositions_image}") && !is_dir("../uploads/{$depositions_image}") ? "uploads/{$depositions_image}" : 'admin/_img/no_image.jpg');
                        
            $depositions_name = (!empty($depositions_name) ? $depositions_name : 'Edite esse rascunho para poder exibir como depoimento em seu site!');
                        
            $CourseDragAndDrop = (empty($segment_title) ? 'wc_draganddrop' : null);
  
            echo "<article class='box box25 post_single {$CourseDragAndDrop}' callback='Depositions' callback_action='depositions_order' id='{$depositions_id}'>        
                <div class='post_single_cover box_content'>
                   <img alt='{$depositions_name}' title='{$depositions_name}' src='../tim.php?src={$DepositionCover}&w=500&h=500'/></a>
                    
                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title' style='font-size:1.25em;'>{$depositions_name}</h1>                    
                </div>
                
                <div class='post_single_actions'>
                    <a title='Editar Depoimento' href='dashboard.php?wc=depositions/create&id={$depositions_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$depositions_id}'>Deletar</span>
                    <span rel='post_single' callback='Depositions' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$depositions_id}'>Deletar Depoimento?</span>
                      
                </div>
            </article>";
        endforeach;

        $Paginator->ExePaginator(DB_DEPOSITIONS, "WHERE ( depositions_name LIKE '%' :s '%' OR depositions_profession LIKE '%' :s '%')", "s={$S}"
        );
        echo $Paginator->getPaginator();
    endif;
    ?>
</div>