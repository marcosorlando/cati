<?php
    $AdminLevel = LEVEL_WC_LANDING_PAGES;
    if (!APP_LANDING_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    endif;

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

    //AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH):
        $Delete = new Delete;

        //AUTO TRASH IMAGES
        $Read->FullRead("SELECT page_cover, page_mockup FROM " . DB_LANDING_PAGES . " WHERE page_title IS NULL AND page_status = :st", "st=0");

        if ($Read->getResult()):
            foreach ($Read->getResult() as $PageImage):
                $CoverRemove = "../../uploads/landingpages/{$PageImage['page_cover']}";
                $LogoRemove = "../../uploads/landingpages/{$PageImage['page_mockup']}";

                if (file_exists($CoverRemove) && !is_dir($CoverRemove)):
                    unlink($CoverRemove);
                endif;
                if (file_exists($LogoRemove) && !is_dir($LogoRemove)):
                    unlink($LogoRemove);
                endif;

            endforeach;
        endif;
        $Delete->ExeDelete(DB_LANDING_PAGES, "WHERE page_title IS NULL AND page_status = :st", "st=0");


    endif;
?>

<header class="dashboard_header">
  <div class="dashboard_header_title">
    <h1 class="icon-pagebreak">Páginas</h1>
    <p class="dashboard_header_breadcrumbs">
      &raquo; <?= ADMIN_NAME; ?>
      <span class="crumb">/</span>
      <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
      <span class="crumb">/</span>
      Landing Page
    </p>
  </div>

  <div class="dashboard_header_search">
    <a title="Nova Página de Cliente" href="dashboard.php?wc=landingpages/create" class="btn btn_green icon-plus">Adicionar Nova Landing Page</a>
  </div>

</header>
<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = ($getPage ? $getPage : 1);
        $Paginator = new Pager('dashboard.php?wc=landingpages/home&pg=', '<<', '>>', 10);
        $Paginator->ExePager($Page, 12);

        $Read->ExeRead(DB_LANDING_PAGES, "ORDER BY page_title ASC, page_date DESC LIMIT :limit OFFSET :offset",
            "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}");
        if (!$Read->getResult()):
            $Paginator->ReturnPage();
            echo Erro("<span class='al_center icon-notification'>Ainda não existem páginas cadastradas {$Admin['user_name']}. Comece agora mesmo criando sua primeira página de cliente!</span>",
                E_USER_NOTICE);
        else:
            foreach ($Read->getResult() as $PAGE):
                extract($PAGE);
                $page_status = ($page_status == 1 ? '<span class="icon-checkmark font_green">Publicada</span>' : '<span class="icon-warning font_yellow">Rascunho</span>');
                $page_cover = (!empty($page_cover) ? BASE . "/tim.php?src=uploads/landingpages/{$page_social_media}&w=" . IMAGE_W / 2 . "&h=" . IMAGE_H / 2 . "" : "");

                echo "<article class='box box25 page_single wc_draganddrop' callback='Customers' id='{$page_id}'>

                <a title='Ver página no site' target='_blank' href='" . BASE . "/{$page_name}'><img alt='{$page_title}' src='{$page_cover}'/></a>
                <div class='box_content wc_normalize_height'>
                    <h1 class='title'><a title='Ver página no site' target='_blank' href='" . BASE . "/{$page_name}'>/{$page_title}</a></h1>
                    <p>{$page_status}</p>
                </div>
                <div class='page_single_action'>
                    <a title='Editar Página' href='dashboard.php?wc=landingpages/create&id={$page_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='page_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$page_id}'>Excluir</span>
                    <span rel='page_single' callback='Landingpages' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$page_id}'>Deletar Página?</span>
                </div>
            </article>";
            endforeach;

            $Paginator->ExePaginator(DB_LANDING_PAGES);
            echo $Paginator->getPaginator();
        endif;
    ?>
</div>
