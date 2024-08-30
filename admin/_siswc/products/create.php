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

    $PdtId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PdtId) {
        $Read->ExeRead(DB_PDT_TRAVI, "WHERE pdt_id = :id", "id={$PdtId}");
        if ($Read->getResult()) {
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um produto que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=products/home');
            exit;
        }
    } else {
        $Read->FullRead("SELECT count(pdt_id) as Total FROM " . DB_PDT_TRAVI . " WHERE pdt_status = :st", "st=1");

        $PdtCreate = [
            'pdt_created' => date('Y-m-d H:i:s'),
            'pdt_status' => 0
        ];
        $Create->ExeCreate(DB_PDT_TRAVI, $PdtCreate);
        header('Location: dashboard.php?wc=products/create&id=' . $Create->getResult());

    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode($Search['s']);
        header("Location: dashboard.php?wc=product/search&s={$S}");
        exit;
    }
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-new-tab"><?= $pdt_title ? $pdt_title : 'Novo Produto'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
            <span class="crumb">/</span> Gerenciar Produto
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Criar Variação Deste Produto" href="dashboard.php?wc=products/reply&id=<?= ($pdt_parent ? $pdt_parent : $PdtId); ?>" class="btn btn_blue icon-copy">Criar Variação!</a>
        <a target="_blank" title="Ver no site" href="<?= BASE; ?>/produto/<?= $pdt_name; ?>" class="wc_view btn btn_green icon-eye">Ver no Site!</a>
    </div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
    <div class="workcontrol_imageupload_content">
        <form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="callback" value="ProductsTravi"/>
            <input type="hidden" name="callback_action" value="sendimage"/>
            <input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>
            <div class="upload_progress none" style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
                0%
            </div>
            <div style="overflow: auto; max-height: 300px;">
                <img class="image image_default" alt="Nova Imagem" title="Nova Imagem" src="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>" default="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
            </div>
            <div class="workcontrol_imageupload_actions">
                <input class="wc_loadimage" type="file" name="image" required/>
                <span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control" style="margin-right: 8px;">Fechar</span>
                <button class="btn btn_green icon-image">Enviar e Inserir!</button>
                <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>

<div class="dashboard_content single_pdt_form">
    <form class="auto_save" name="manage_pdt" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="ProductsTravi"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>

        <div class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Produto:</span>
                    <input style="font-size: 1.4em;" type="text" name="pdt_title" value="<?= $pdt_title; ?>" placeholder="Nome do Produto:" required/>
                </label>

                <label class="label">
                    <span class="legend">Breve Descrição:</span>
                    <textarea style="font-size: 1.2em;" name="pdt_subtitle" rows="3" required><?= $pdt_subtitle; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">TAGS:</span>
                    <input style="font-size: 1.2em;" type="text" name="pdt_tags" value="<?= $pdt_tags; ?>" list="tags"/>

                    <datalist id="tags">
                        <?php
                            $Read->FullRead("SELECT DISTINCT upper(pdt_tags) as pdt_tags FROM " . DB_PDT_TRAVI . " WHERE pdt_tags IS NOT NULL AND pdt_tags != ''");
                            foreach ($Read->getResult() as $tags):
                                echo '<option value="' . $tags['pdt_tags'] . '">';
                            endforeach;
                        ?>
                    </datalist>
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Imagem do Topo (JPG 1920x370px):</span>
                        <input type="file" class="wc_loadimage" name="pdt_scene"/>
                    </label>
                    <label class="label">
                        <span class="legend">Imagem principal do Produto (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
                        <input type="file" class="wc_loadimage" name="pdt_cover"/>
                    </label>
                </div>

                <label class="label">
                    <span class="legend">Cor ou Padrão: (se mais de 1 separe com vírgula):</span>
                    <input type="text" name="pdt_color" value="<?= $pdt_color; ?>">
                </label>

                <div class="label_33">
                    <label class="label">
                        <span class="legend">Categoria:</span>
                        <?php
                            $Read->ExeRead(DB_PDT_CATS_TRAVI, "WHERE cat_parent IS NULL ORDER BY cat_title ASC");
                            if (!$Read->getResult()) {
                                echo Erro("<span class='icon-warning'>Cadastre algumas categorias de produtos antes de começar!</span>", E_USER_WARNING);
                            } else {
                                echo "<select name='pdt_subcategory' class='jwc_product_stock' required>";
                                echo "<option value=''>Selecione uma Categoria</option>";
                                foreach ($Read->getResult() as $Cat) {
                                    echo "<option disabled='disabled' value='{$Cat['cat_id']}'>{$Cat['cat_title']}</option>";
                                    $Read->ExeRead(DB_PDT_CATS_TRAVI, "WHERE cat_parent = :id", "id={$Cat['cat_id']}");
                                    if (!$Read->getResult()) {
                                        echo "<option disabled='disabled' value=''>&raquo;&raquo; Cadastre uma categoria nessa sessão!</option>";
                                    } else {
                                        foreach ($Read->getResult() as $SubCat) {
                                            echo "<option";
                                            if ($pdt_subcategory == $SubCat['cat_id']) {
                                                echo " selected='selected'";
                                            }
                                            echo " value='{$SubCat['cat_id']}'>&raquo;&raquo; {$SubCat['cat_title']}</option>";
                                        }
                                    }
                                }
                                echo "</select>";
                            }
                        ?>
                    </label>
                    <label class="label">
                        <span class="legend">Processo:</span>
                        <?php
                            $Read->ExeRead(DB_SVC, " ORDER BY svc_title ASC");
                            if (!$Read->getResult()) {
                                echo Erro("<span class='icon-warning'>Cadastre alguns processos de antes de começar!</span>", E_USER_WARNING);
                            } else {
                                echo "<select name='pdt_process' required>";
                                echo "<option value='' selected disabled>Selecione um Processo</option>";

                                foreach ($Read->getResult() as $Process) {
                                    extract($Process);
                                    echo "<option value='{$svc_id}' " . ($svc_id == $pdt_process ? 'selected' : '') . " >&raquo; {$svc_title}</option>";
                                }
                                echo "</select>";
                            }
                        ?>
                    </label>
                    <label class="label">
                        <span class="legend">Formato:</span>
                        <?php
                            $Read->ExeRead(DB_PDT_FORMAT_TRAVI, " ORDER BY fmt_title ASC");
                            if (!$Read->getResult()) {
                                echo Erro("<span class='icon-warning'>Cadastre algums formatos de produtos antes de começar!</span>", E_USER_WARNING);
                            } else {
                                echo "<select name='pdt_format' required>";
                                echo "<option value='' selected disabled>Selecione um Formato</option>";

                                foreach ($Read->getResult() as $Format) {
                                    extract($Format);
                                    echo "<option value='{$fmt_id}'" . ($fmt_id == $pdt_format ? 'selected' : '') . ">&raquo; {$fmt_title}</option>";
                                }
                                echo "</select>";
                            }
                        ?>
                    </label>
                </div>

                <label class="label">
                    <span class="legend">Descrição Completa:</span>
                    <textarea name="pdt_content" class="work_mce" rows="10"><?= $pdt_content; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Informações Adicionais:</span>
                    <textarea name="pdt_infos" class="work_mce" rows="10"><?= $pdt_infos; ?></textarea>
                </label>

                <div class="clear"></div>
            </div>
        </div>

        <div class="box box30">
            <div class="panel_header default">
                <h2 class="icon-file-picture">Imagem do Topo da Página:</h2>
                <?php
                    $Cena = (file_exists("../uploads/{$pdt_scene}") && !is_dir("../uploads/{$pdt_scene}") ? "uploads/{$pdt_scene}" : 'admin/_img/no_image.jpg');
                ?>
                <img class="pdt_scene" alt="Cena do Produto" title="Cena do Produto" src="../tim.php?src=<?= $Cena; ?>&w=1920/2&h=368/2" default="../tim.php?src=<?= $Cena; ?>&w=1920/2&h=368/2"/>
            </div>


            <div class="panel_header default">
                <h2 class="icon-file-picture">Imagem Principal do Produto:</h2>
                <?php
                    $Image = (file_exists("../uploads/{$pdt_cover}") && !is_dir("../uploads/{$pdt_cover}") ? "uploads/{$pdt_cover}" : 'admin/_img/no_image.jpg');
                ?>
                <img class="pdt_cover" alt="Capa do Produto" title="Capa do Produto" src="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>" default="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>">
                <?php
                    $Read->ExeRead(DB_PDT_GALLERY_TRAVI, "WHERE product_id = :id", "id={$pdt_id}");
                    if ($Read->getResult()) {
                        echo '<div class="pdt_images gallery pdt_single_image">';
                        foreach ($Read->getResult() as $Image) {
                            $ImageUrl = ($Image['image'] && file_exists("../uploads/{$Image['image']}") && !is_dir("../uploads/{$Image['image']}") ? "../uploads/{$Image['image']}" : '_img/no_image.jpg');
                            echo "<img rel='ProductsTravi' id='{$Image['id']}' alt='Imagem em {$pdt_title}' title='Imagem em {$pdt_title}' src='{$ImageUrl}'/>";
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="pdt_images gallery pdt_single_image"></div>';
                    }
                ?>
            </div>

            <div class="box_content">
                <label class="label">
                    <span class="legend">Fotos Adicionais (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
                    <input type="file" name="image[]" multiple/>
                </label>

                <label class="label">
                    <span class="legend">Hotsite (opcional):</span>
                    <input type="url" name="pdt_hotlink" value="<?= $pdt_hotlink; ?>" placeholder="https://"/>
                </label>

                <div class="m_top">&nbsp;</div>
                <div class="wc_actions">
                    <div class='switch'>
                        <input name='pdt_status' type='checkbox' id='pdt_status'
                               value='1' <?= ($pdt_status == 1 ? 'checked' : ''); ?>>
                        <label for="pdt_status" data-on="ON" data-off="OFF"></label>
                    </div>
                    <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
                    <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                </div>
                <div class="clear"></div>

                <?php
                    $WC_TITLE_LINK = $pdt_title;
                    $WC_SHARE_HASH = "#TraviPlasticos";
                    $WC_SHARE_LINK = BASE . "/produto/{$pdt_name}";

                    $URLSHARE = "/produto/{$pdt_name}";
                    require '_tpl/Share.wc.php';
                ?>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
