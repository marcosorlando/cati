<?php
    $AdminLevel = LEVEL_WC_PRODUCTS_TRAVI;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    }

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)) {
        $Read = new Read;
    }

    //AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH) {
        $Delete = new Delete;
        $Delete->ExeDelete(DB_PDT_PROCESS_TRAVI, "WHERE pcs_title IS NULL AND pcs_id >= :st", "st=1");
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-price-tags">Processos</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
            <span class="crumb">/</span>
            Processos
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Novo Processo" href="dashboard.php?wc=products/process" class="btn btn_green icon-plus">Adicionar Processo!</a>
    </div>
</header>

<div class="dashboard_content">
    <?php
        $Read->ExeRead(DB_PDT_PROCESS_TRAVI, " ORDER BY pcs_title ASC");
        if (!$Read->getResult()) {
            echo Erro("<span class='al_center icon-notification'>Ainda não existem processos de produtos cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro processo!</span>", E_USER_NOTICE);
        } else {
            foreach ($Read->getResult() as $Process) {
                echo "<article class='product_category box box100' id='{$Process['pcs_id']}'>
                        <header>
                            <h1 class='icon-price-tags'>{$Process['pcs_title']} <span>[{$Process['pcs_sizes']}] produto(s) cadastrado(s)</span></h1>
                            <a target='_blank' title='Ver Processo!' href='" . BASE . "/servicos/{$Process['pcs_name']}' class='btn btn_green icon-eye icon-notext'></a>
                            <a title='Editar Processo!' href='dashboard.php?wc=products/process&id={$Process['pcs_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                            <span rel='product_category' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Process['pcs_id']}'></span>
                            <span rel='product_category' callback='ProductsTravi' callback_action='pcs_delete' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Process['pcs_id']}'>Deletar Processo?</span>
                        </header>
                    </article>";
            }
        }
    ?>
</div>