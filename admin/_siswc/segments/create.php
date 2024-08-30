<?php

    $AdminLevel = LEVEL_WC_SEGMENTS;
    if (!APP_SEGMENTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
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

    $SegId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($SegId) {
        $Read->ExeRead(DB_SEG, "WHERE seg_id = :id", "id={$SegId}");
        if ($Read->getResult()) {
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um segmento que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=segments/home');
            exit;
        }
    } else {
        $Read->FullRead("SELECT count(seg_id) as Total FROM " . DB_SEG . " WHERE seg_status = :st", "st=1");

        $SegCreate = [
            'seg_created' => date('Y-m-d H:i:s'),
            'seg_status' => 0,
        ];
        $Create->ExeCreate(DB_SEG, $SegCreate);
        header('Location: dashboard.php?wc=segments/create&id=' . $Create->getResult());
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode($Search['s']);
        header("Location: dashboard.php?wc=segment/search&s={$S}");
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-new-tab"><?= $seg_title ? $seg_title : 'Novo Segmento'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=segments/home">Segmentos</a>
            <span class="crumb">/</span>
            Gerenciar Segmento
        </p>
    </div>

    <div class="dashboard_header_search">
        <a target="_blank" title="Ver no site" href="<?= BASE . "/segmento/{$seg_name}"; ?>"
           class="wc_view btn btn_green icon-eye">Ver no Site!</a>
    </div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
    <div class="workcontrol_imageupload_content">
        <form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="callback" value="Segments"/>
            <input type="hidden" name="callback_action" value="sendimage"/>
            <input type="hidden" name="seg_id" value="<?= $SegId; ?>"/>
            <div class="upload_progress none"
                 style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
                0%
            </div>
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

<div class="dashboard_content single_seg_form">
    <form class="auto_save" name="manage_seg" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Segments"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="seg_id" value="<?= $SegId; ?>"/>

        <div class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Segmento:</span>
                    <input style="font-size: 1.4em;" type="text" name="seg_title" value="<?= $seg_title; ?>"
                           placeholder="Nome do Segmento:" required/>
                </label>

                <label class="label">
                    <span class="legend">Breve Descrição:</span>
                    <textarea style="font-size: 1.2em;" name="seg_subtitle" rows="3"
                              required><?= $seg_subtitle; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Descrição Completa:</span>
                    <textarea name="seg_description" class="work_mce" rows="10"><?= $seg_description; ?></textarea>
                </label>

                <div class="clear"></div>
            </div>
        </div>

        <div class="box box30">

            <div class='label'>
                <label class='label'>
                    <span class='legend'>Imagem principal (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
                    <input type="file" class="wc_loadimage" name="seg_cover"/>
                </label>
            </div>

            <?php
                $Image = (file_exists("../uploads/{$seg_cover}") && !is_dir("../uploads/{$seg_cover}") ? "uploads/{$seg_cover}" : 'admin/_img/no_image.jpg');
            ?>
            <img class="seg_cover" alt="Capa do Segmento" title="Capa do Segmento"
                 src="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>"
                 default="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>">

            <div class="box_content">
                <div class='label'>
                    <label class='label'>
                        <span class='legend'>ÍCONE (PNG <?= AVATAR_W; ?>x<?= AVATAR_H; ?>px):</span>
                        <input type="file" class="wc_loadimage" name="seg_icon"/>
                    </label>
                </div>
                <?php
                    $icone = (file_exists("../uploads/{$seg_icon}") && !is_dir("../uploads/{$seg_icon}") ? "uploads/{$seg_icon}" : 'admin/_img/no_image.jpg');
                ?>
                <img class="seg_icon" alt="Ícone do Segmento" title="Ícone do Segmento"
                     src="../tim.php?src=<?= $icone; ?>&w=<?= AVATAR_W; ?>&h=<?= AVATAR_H; ?>"
                     default="../tim.php?src=<?= $icone; ?>&w=<?= AVATAR_W; ?>&h=<?= AVATAR_H; ?>">


                <div class="m_top">&nbsp;</div>
                <div class="wc_actions" style="text-align: center">
                    <div class='switch'>
                        <input name='seg_status' type='checkbox' id='seg_status'
                               value='1' <?= ($seg_status == 1 ? 'checked' : ''); ?>>
                        <label for="seg_status" data-on="ON" data-off="OFF"></label>
                    </div>
                    <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
                    <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
                         title="Enviando Requisição!" src="_img/load.gif"/>
                </div>
                <div class="clear"></div>
                <?php
                    $URLSHARE = "/segmento/{$seg_name}";
                    require '_tpl/Share.wc.php';
                ?>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
