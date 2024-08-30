<?php

    $AdminLevel = 6;
    if (!APP_MATERIALS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    endif;

    $Read = new Read;
    $Create = new Create;

    $MatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($MatId):
        $Read->ExeRead(DB_MATERIAIS, "WHERE mat_id = :id", "id={$MatId}");
        if ($Read->getResult()):
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        else:
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um material que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=materiais/home');
        endif;
    else:
        $PostCreate = [
            'mat_date' => date('Y-m-d H:i:s'),
            'mat_type' => 0,
            'mat_status' => 0,
            'mat_author' => $Admin['user_id']
        ];
        $Create->ExeCreate(DB_MATERIAIS, $PostCreate);
        header('Location: dashboard.php?wc=materiais/create&id=' . $Create->getResult());
    endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-new-tab">Cadastrar Novo Material</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=materiais/home">Materiais</a>
            <span class="crumb">/</span>
            Novo Material
        </p>
    </div>

    <div class="dashboard_header_search">
        <a target="_blank" title="Ver no site" href="<?= BASE; ?>/materiais/#<?= $mat_name; ?>"
           class="wc_view btn btn_green icon-eye">Ver material no site!</a>
    </div>
</header>

<div class="workcontrol_imageupload none" id="mat_control">
    <div class="workcontrol_imageupload_content">
        <form name="workcontrol_mat_upload" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="callback" value="Mats"/>
            <input type="hidden" name="callback_action" value="sendimage"/>
            <input type="hidden" name="mat_id" value="<?= $MatId; ?>"/>
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

<div class="dashboard_content">
    <form class="auto_save" name="mat_create" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Mats"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="mat_id" value="<?= $MatId; ?>"/>

        <article class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Título:</span>
                    <input style="font-size: 1.4em;" type="text" name="mat_title" value="<?= $mat_title; ?>" required/>
                </label>

                <label class="label">
                    <span class="legend">Subtítulo:</span>
                    <textarea style="font-size: 1.2em;" name="mat_subtitle" rows="3"
                              required><?= $mat_subtitle; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Link para Landing page:</span>
                    <input type="text" name="mat_link" value="<?= $mat_link; ?>" placeholder="Link do Material - LP:"
                           required/>
                </label>


                <div class="label_50">
                    <label class="label">
                        <span class="legend">Formato do Material:</span>
                        <!--            <select name="mat_type" required>
                                      <option value="" disabled="disabled" selected="selected">Selecione o formato do material:</option>
                                      <php
                                      foreach (getWcMatFormato() as $TypeId => $TypeValue):
                                        echo "<option " . ($mat_type == $TypeId ? "selected='selected'" : null) . " value='{$TypeId}'>{$TypeValue}</option>";
                                      endforeach;
                                      ?>
                                    </select>-->


                        <select name="mat_category" required>
                            <option value="" disabled="disabled" selected="selected">Selecione uma seção:</option>
                            <?php
                                $Read->FullRead("SELECT category_id, category_title FROM " . DB_MATCATEGORIES . " WHERE category_parent IS NULL ORDER BY category_title");
                                if (!$Read->getResult()):
                                    echo '<option value="" disabled="disabled">Não existem sessões cadastradas!</option>';
                                else:
                                    foreach ($Read->getResult() as $CatPai):
                                        echo "<option";
                                        if ($mat_category == $CatPai['category_id']):
                                            echo " selected='selected'";
                                        endif;
                                        echo " value='{$CatPai['category_id']}'>{$CatPai['category_title']}</option>";
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Nível de conhecimento:</span>
                        <select name="mat_level" required>
                            <option value="" disabled="disabled" selected="selected">Selecione o nível:</option>
                            <?php
                                foreach (getWcMatLevels() as $LevelId => $LevelValue):
                                    echo "<option " . ($mat_level == $LevelId ? "selected='selected'" : null) . " value='{$LevelId}'>{$LevelValue}</option>";
                                endforeach;
                            ?>
                        </select>
                    </label>
                </div>
                <div class="label_50">
                    <label class="label">
                        <span class="legend">DIA:</span>
                        <input type="text" class="formTime" name="mat_date"
                               value="<?= $mat_date ? date('d/m/Y H:i', strtotime($mat_date)) : date('d/m/Y H:i'); ?>"
                               required/>
                    </label>

                    <label class="label">
                        <span class="legend">AUTOR:</span>
                        <select name="mat_author" required>
                            <option value="<?= $Admin['user_id']; ?>"><?= $Admin['user_name']; ?> <?= $Admin['user_lastname']; ?></option>
                            <?php
                                $Read->FullRead("SELECT user_id, user_name, user_lastname FROM " . DB_USERS . " WHERE user_level >= :lv AND user_id != :uid",
                                    "lv=6&uid={$Admin['user_id']}");
                                if ($Read->getResult()):
                                    foreach ($Read->getResult() as $PostAuthors):
                                        echo "<option";
                                        if ($PostAuthors['user_id'] == $mat_author):
                                            echo " selected='selected'";
                                        endif;
                                        echo " value='{$PostAuthors['user_id']}'>{$PostAuthors['user_name']} {$PostAuthors['user_lastname']}</option>";
                                    endforeach;
                                endif;
                            ?>
                        </select>
                    </label>
                </div>
                <div class="clear"></div>
            </div>
        </article>

        <article class="box box30">
            <label class="label">
                <span class="legend">Capa: (.jpg 400X250 px)</span>
                <input type="file" class="wc_loadimage" id="jmat_cover" name="mat_cover"/>
            </label>
            <div class="post_create_cover">
                <div class="upload_progress none">0%</div>
                <?php
                    $PostCover = (!empty($mat_cover) && file_exists("../uploads/{$mat_cover}") && !is_dir("../uploads/{$mat_cover}") ? "uploads/{$mat_cover}" : 'admin/_img/no_image.jpg');
                ?>
                <img class="post_thumb mat_cover" alt="Capa" id="mat_cover" title="Capa"
                     src="../tim.php?src=<?= $PostCover; ?>&w=400&h=250"
                     default="../tim.php?src=<?= $PostCover; ?>&w=400&h=250"/>
            </div>
            <div class="post_create_categories">
                <?php
                    $Read->FullRead("SELECT category_id, category_title FROM " . DB_MATCATEGORIES . " WHERE category_parent IS NULL ORDER BY category_title");

					if (!$Read->getResult()):
                        echo "<br><br>";
                        echo Erro('<span class="al_center icon-price-tags">Não existem categorias cadastradas!</span>',
                            E_USER_WARNING);
                    else:
                        foreach ($Read->getResult() as $Categories):
                            $Read->FullRead("SELECT category_id, category_title FROM " . DB_MATCATEGORIES . " WHERE category_parent = :parent ORDER BY category_title",
                                "parent={$Categories['category_id']}");
                            if ($Read->getResult()):
                                echo "<p class='mat_create_ses'>{$Categories['category_title']}</p>";
                                foreach ($Read->getResult() as $SubCategories):
                                    echo "<p class='mat_create_cat'><label class='label_check'><input type='checkbox' name='mat_category_parent[]' value='{$SubCategories['category_id']}'";
                                    if (in_array($SubCategories['category_id'], explode(',', $mat_category_parent))):
                                        echo " checked";
                                    endif;
                                    echo "> {$SubCategories['category_title']}</label></p>";
                                endforeach;
                            endif;
                        endforeach;
                    endif;
                ?>
            </div>
            <header>
                <h1>Publicar:</h1>
            </header>
            <div class="box_content">
                <div class="m_top">&nbsp;</div>
                <div class="wc_actions">

                    <div class='switch'>
                        <input name='mat_status' type='checkbox' id='mat_status'
                               value='1' <?= ($mat_status == 1 ? 'checked' : ''); ?>>
                        <label for="mat_status" data-on="ON" data-off="OFF"></label>
                    </div>
                    <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
                    <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
                         title="Enviando Requisição!" src="_img/load.gif"/>
                </div>
                <div class="clear"></div>
                <?php
                    $URLSHARE = "/artigo/{$mat_name}";
                    require '_tpl/Share.wc.php';
                ?>
            </div>
        </article>
    </form>
</div>
