<?php
    $AdminLevel = LEVEL_WC_SERVICES;
    if (!APP_SERVICES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
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

    $SvcId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($SvcId) {
        $Read->ExeRead(DB_SVC, "WHERE svc_id = :id", "id={$SvcId}");
        if ($Read->getResult()) {
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um serviço que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=services/home');
            exit;
        }
    } else {
        $Read->FullRead("SELECT count(svc_id) as Total FROM " . DB_SVC . " WHERE svc_status = :st", "st=1");

        $SvcCreate = [
            'svc_created' => date('Y-m-d H:i:s'),
            'svc_status' => 0,
        ];
        $Create->ExeCreate(DB_SVC, $SvcCreate);
        header('Location: dashboard.php?wc=services/create&id=' . $Create->getResult());

    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode($Search['s']);
        header("Location: dashboard.php?wc=service/search&s={$S}");
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-hammer"><?= $svc_title ? $svc_title : 'Novo Processo'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=services/home">Processos</a>
            <span class="crumb">/</span>
            Gerenciar Processo
        </p>
    </div>

    <div class="dashboard_header_search">
        <a target="_blank" title="Ver no site" href="<?= BASE. "/servico/{$svc_name}"; ?>"
           class="wc_view btn btn_green icon-eye">Ver no Site!</a>
    </div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
    <div class="workcontrol_imageupload_content">
        <form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="callback" value="Services"/>
            <input type="hidden" name="callback_action" value="sendimage"/>
            <input type="hidden" name="svc_id" value="<?= $SvcId; ?>"/>
            <div class="upload_progress none" style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">0%</div>
            <div style="overflow: auto; max-height: 300px;">
                <img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
                     src="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"
                     default="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
            </div>
            <div class="workcontrol_imageupload_actions">
                <input class="wc_loadimage" type="file" name="image" required/>
                <span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control"
                      style="margin-right: 8px;">Fechar</span>
                <button class="btn btn_green icon-image">Enviar e Inserir!</button>
                <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
                     title="Enviando Requisição!" src="_img/load.gif"/>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>

<div class="dashboard_content single_svc_form">
    <form class="auto_save" name="manage_svc" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Services"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="svc_id" value="<?= $SvcId; ?>"/>

        <div class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Processo:</span>
                    <input style="font-size: 1.4em;" type="text" name="svc_title" value="<?= $svc_title; ?>"
                           placeholder="Nome do Processo:" required/>
                </label>

                <label class="label">
                    <span class="legend">Breve Descrição:</span>
                    <textarea style="font-size: 1.2em;" name="svc_subtitle" rows="3"
                              required><?= $svc_subtitle; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Descrição Completa:</span>
                    <textarea name="svc_description" class="work_mce" rows="10"><?= $svc_description; ?></textarea>
                </label>

                <div class="clear"></div>
            </div>
        </div>

        <div class="box box30">
            <div class="panel_header default">
                <h2 class="icon-file-picture">Imagem Principal do Processo:</h2>
                <label class='label'>
                    <span class='legend'>Tamanho (JPG <?= IMAGE_W; ?>x<?= IMAGE_H; ?>px):</span>
                    <input type="file" class="wc_loadimage" name="svc_cover"/>
                </label>
            <?php
                $Image = (file_exists("../uploads/{$svc_cover}") && !is_dir("../uploads/{$svc_cover}") ? "uploads/{$svc_cover}" : 'admin/_img/no_image.jpg');
            ?>
            <img class="svc_cover" alt="Capa do Processo" title="Capa do Processo"
                 src="../tim.php?src=<?= $Image; ?>&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"
                 default="../tim.php?src=<?= $Image; ?>&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>">
            <?php
                $Read->ExeRead(DB_SVC_GALLERY, "WHERE svc_id = :id", "id={$svc_id}");
                if ($Read->getResult()) {
                    echo '<div class="pdt_images gallery pdt_single_image">';
                    foreach ($Read->getResult() as $Image) {
                        $ImageUrl = ($Image['image'] && file_exists("../uploads/{$Image['image']}") && !is_dir("../uploads/{$Image['image']}") ? "../uploads/{$Image['image']}" : '_img/no_image.jpg');
                        echo "<img rel='Services' id='{$Image['id']}' alt='Imagem em {$svc_title}' title='Imagem em {$svc_title}' src='{$ImageUrl}'/>";
                    }
                    echo '</div>';
                } else {
                    echo '<div class="pdt_images gallery pdt_single_image"></div>';
                }
            ?>
            </div>

            <div class="box_content">
                <label class="label">
                    <span class="legend">Fotos Adicionais (JPG <?= IMAGE_W; ?>x<?= IMAGE_H; ?>px):</span>
                    <input type="file" name="image[]" multiple/>
                </label>

                <div class='label'>
                    <label class='label'>
                        <span class='legend'>ÍCONE (PNG <?= AVATAR_W; ?>x<?= AVATAR_H; ?>px):</span>
                        <input type="file" class="wc_loadimage" name="svc_icon"/>
                    </label>
                </div>
                <?php
                    $icone = (file_exists("../uploads/{$svc_icon}") && !is_dir("../uploads/{$svc_icon}") ? "uploads/{$svc_icon}" : 'admin/_img/no_image.jpg');
                ?>
                <img class="svc_icon" alt="Ícone do Segmento" title="Ícone do Segmento"
                     src="../tim.php?src=<?= $icone; ?>&w=<?= AVATAR_W; ?>&h=<?= AVATAR_H; ?>"
                     default="../tim.php?src=<?= $icone; ?>&w=<?= AVATAR_W; ?>&h=<?= AVATAR_H; ?>">

                <div class="m_top">&nbsp;</div>

                <div class="wc_actions">
                    <div class='switch'>
                        <input name='svc_status' type='checkbox' id='svc_status'
                               value='1' <?= ($svc_status == 1 ? 'checked' : ''); ?>>
                        <label for="svc_status" data-on="ON" data-off="OFF"></label>
                    </div>

                    <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
                    <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
                         title="Enviando Requisição!" src="_img/load.gif"/>
                </div>
                <div class="clear"></div>
                <?php
                    $URLSHARE = "/servico/{$svc_name}";
                    $pdt_title = $svc_title;
                    $pdt_subtitle = $svc_subtitle;
                    require '_tpl/Share.wc.php';
                ?>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
