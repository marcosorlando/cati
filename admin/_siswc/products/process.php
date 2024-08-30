<?php
    $AdminLevel = LEVEL_WC_PRODUCTS_TRAVI;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    }

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)) {
        $Read = new Read;
    }

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)) {
        $Create = new Create;
    }

    $PcsId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PcsId) {
        $Read->ExeRead(DB_PDT_PROCESS_TRAVI, "WHERE pcs_id = :id", "id={$PcsId}");
        if ($Read->getResult()) {
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Erro("<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um processo que não existe ou que foi removido recentemente!", E_USER_NOTICE);
            header('Location: dashboard.php?wc=products/processes');
            exit;
        }
    } else {
        $Date = date('Y-m-d H:i:s');
        $Title = "Novo Processo - {$Date}";
        $Name = Check::Name($Title);
        $CarCreate = ['pcs_name' => $Name, 'pcs_created' => $Date];
        $Create->ExeCreate(DB_PDT_PROCESS_TRAVI, $CarCreate);
        header('Location: dashboard.php?wc=products/process&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-price-tags"><?= $pcs_title ? $pcs_title : 'Novo Processo'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/processes">Processos</a>
            <span class="crumb">/</span>
            Gerenciar Processo
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver Processos!" href="dashboard.php?wc=products/processes" class="btn btn_blue icon-eye">Ver Processo!</a>
        <a title="Novo Processo" href="dashboard.php?wc=products/process" class="btn btn_green icon-plus">Adicionar Processo!</a>
    </div>

</header>

<div class="dashboard_content">
    <div class="box box100">

        <div class="panel_header default">
            <h2 class="icon-price-tags">Dados do Processo</h2>
        </div>

        <div class="panel">
            <form class="auto_save" name="category_add" action="" method="post" enctype="multipart/form-data">
                <div class="callback_return"></div>
                <input type="hidden" name="callback" value="ProductsTravi"/>
                <input type="hidden" name="callback_action" value="pcs_manager"/>
                <input type="hidden" name="pcs_id" value="<?= $PcsId; ?>"/>
                <label class="label">
                    <span class="legend">Nome:</span>
                    <input style="font-size: 1.5em;" type="text" name="pcs_title" value="<?= $pcs_title; ?>" placeholder="Título do Processo:" required/>
                </label>

<!--                <label class="label">-->
<!--                    <span class="legend">Setor:</span>-->
<!--                    <select name="pcs_id" class="jwc_pdtsection_selector">-->
<!--                        <option value="">Selecione um processo!</option>-->
<!--                        --><?php
//                            $Read->FullRead("SELECT pcs_id, pcs_title, pcs_sizes FROM " . DB_PDT_PROCESS_TRAVI . " ORDER BY pcs_title ASC");
//                            if ($Read->getResult()) {
//                                foreach ($Read->getResult() as $Process) {
//                                    echo "<option class='{$Process['pcs_sizes']}' value='{$Process['pcs_id']}'>&raquo;{$Sector['pcs_title']}</option>";
//                                }
//                            }
//                        ?>
<!--                    </select>-->
<!--                </label>-->

                <div class="m_top">&nbsp;</div>
                <img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                <button class="btn btn_green icon-price-tags fl_right">Atualizar Processo!</button>
                <div class="clear"></div>
            </form>
        </div>
    </div>
</div>