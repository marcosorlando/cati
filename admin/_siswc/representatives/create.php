<?php

    $rep_all_cities = 1;

    $AdminLevel = LEVEL_WC_REPRESENTATIVES;
    if (!APP_REPRESENTATIVES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    endif;
    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;
    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

    $RepresentativeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($RepresentativeId):
        $Read->ExeRead(DB_REPRESENTATIVES, "WHERE rep_id = :id", "id={$RepresentativeId}");
        if ($Read->getResult()):
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        else:
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um representante que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=representatives/home');
        endif;
    else:
        $RepresentativeCreate = ['rep_created' => date('Y-m-d H:i:s')];
        $Create->ExeCreate(DB_REPRESENTATIVES, $RepresentativeCreate);
        header('Location: dashboard.php?wc=representatives/create&id=' . $Create->getResult());
    endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-pen"><?= $rep_name ? $rep_name : 'Novo Representante'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=representatives/home">Representantes</a>
            <span class="crumb">/</span> Gerenciar Represenante
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver Depoimentos" href="dashboard.php?wc=representatives/home" class="btn btn_blue icon-eye">Ver</a>
        <a title="Novo Depoimento" href="dashboard.php?wc=representatives/create" class="btn btn_green icon-plus">Adicionar</a>
    </div>
</header>

<div class="dashboard_content">
    <form name="deposition_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Representatives"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="rep_id" value="<?= $RepresentativeId; ?>"/>

        <div class="box box100">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Nome da Empresa:</span>
                    <input style="font-size: 1.4em;" type="text" name="rep_company" value="<?= $rep_company; ?>" placeholder="Nome da Empresa:" required/>
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Estado</span>
                        <select name="rep_uf" id="UF" required="required">
                            <option value="0"> - Selecione -</option>

                            <?php

                                $Read->ExeRead(DB_STATES);
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $States) {
                                        extract($States);
                                        echo "<option value='{$uf}' " . ($uf == $rep_uf ? 'selected' : '') . ">{$uf} - {$name}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Cidade:</span>
                        <select id='rep_city' name='rep_city' class='cities_return' required="required">

                            <?php
                                $Read->ExeRead(DB_CITIES, "WHERE uf = :uf ORDER BY name", "uf={$rep_uf}");
                                if ($Read->getResult()) {
                                    echo "<option value='0'>- Selecione a cidade -</option>";
                                    foreach ($Read->getResult() as $Cities) {
                                        extract($Cities);
                                        echo "<option value='{$name}' " . ($name == $rep_city ? 'selected' : '') . " >{$name}</option>";
                                    }

                                }
                            ?>
                        </select>

                    </label>
                </div>
                <div class="label_50">
                    <label class="label">
                        <span class="legend">Nome do Contato:</span>
                        <input type="text" name="rep_name" value="<?= $rep_name; ?>" placeholder="Nome da contato:" required/>
                    </label>
                    <label class="label">
                        <span class="legend">E-mail:</span>
                        <input type="email" name="rep_email" value="<?= $rep_email; ?>" placeholder="E-mail:" required/>
                    </label>
                </div>
                <div class="label_50">
                    <label class="label">
                        <span class="legend">Telefone:</span>
                        <input type="text" name="rep_phone" class="formPhone" value="<?= $rep_phone; ?>" placeholder="Telefone:" required/>
                    </label>
                    <label class="label">
                        <span class="legend">Celular/WhatsApp:</span>
                        <input type="text" name="rep_cellphone" class="formPhone" value="<?= $rep_cellphone; ?>" placeholder="Celular/WhatsApp:" required/>
                    </label>
                </div>
                <label class="label">
                    <span class="legend">Cidades Atendidas (separadas por vírgula ( , ):  <label class="label_check label_publish <?= ($rep_all_cities == 1 ? 'active' : ''); ?>">
                    <input style="margin-top: -1px;" type="checkbox" value="1" name="rep_all_cities" <?= ($rep_all_cities == 1 ? 'checked' : ''); ?> >
                    <i class="icon-checkmark"></i> ATENDE TODO ESTADO - <b><?= $rep_uf ? $rep_uf : ''; ?></b>
                </label></span>
                    <textarea class="allcities" name="rep_cities" rows="6" placeholder="Inserir cidades atendidas
                    separadas por (, ) vírgula."><?= $rep_cities ?></textarea>
                </label>

                <div class="clear"></div>
                <div class="m_top">&nbsp;</div>
                <div class="wc_actions" style="text-align: center">
                    <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
                    <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                </div>
            </div>
        </div>

    </form>
</div>
