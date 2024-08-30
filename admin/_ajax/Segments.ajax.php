<?php

    session_start();
    require '../../_app/Config.inc.php';
    $NivelAcess = LEVEL_WC_SEGMENTS;

    if (!APP_SEGMENTS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
            E_USER_ERROR);
        echo json_encode($jSON);
        die;
    }

    usleep(50000);

    //DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Segments';
    $PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //VALIDA AÇÃO
    if ($PostData && $PostData['callback_action'] && $PostData['callback'] == $CallBack) {
        //PREPARA OS DADOS
        $Case = $PostData['callback_action'];
        unset($PostData['callback'], $PostData['callback_action']);

        // AUTO INSTANCE OBJECT READ
        if (empty($Read)) {
            $Read = new Read;
        }

        // AUTO INSTANCE OBJECT CREATE
        if (empty($Create)) {
            $Create = new Create;
        }

        // AUTO INSTANCE OBJECT UPDATE
        if (empty($Update)) {
            $Update = new Update;
        }

        // AUTO INSTANCE OBJECT DELETE
        if (empty($Delete)) {
            $Delete = new Delete;
        }
        $Upload = new Upload('../../uploads/');

        //SELECIONA AÇÃO
        switch ($Case) {
            case 'manager':

                $SegId = $PostData['seg_id'];
                $PostData['seg_status'] = (!empty($PostData['seg_status']) ? '1' : '0');

                $Read->ExeRead(DB_SEG, "WHERE seg_id = :id", "id={$SegId}");

                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar o segmento. Experimente atualizar a página!",
                        E_USER_WARNING);
                } else {
                    $Segment = $Read->getResult()[0];
                    unset($PostData['seg_id'], $PostData['seg_cover'], $PostData['seg_icon']);

                    $PostData['seg_name'] = (!empty($PostData['seg_name']) ? Check::Name($PostData['seg_name']) : Check::Name($PostData['seg_title']));

                    //COVER UPLOAD
                    if (!empty($_FILES['seg_cover'])) {
                        $File = $_FILES['seg_cover'];

                        if ($Segment['seg_cover'] && file_exists("../../uploads/{$Segment['seg_cover']}") && !is_dir("../../uploads/{$Segment['seg_cover']}")) {
                            unlink("../../uploads/{$Segment['seg_cover']}");
                        }

                        $Upload->Image($File, "{$PostData['seg_name']}".time(), 800);
                        if ($Upload->getResult()) {
                            $PostData['seg_cover'] = $Upload->getResult();
                        } else {
                            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 800x800px para a capa!",
                                E_USER_WARNING);
                            echo json_encode($jSON);
                            return;
                        }
                    }
                    //ICONE UPLOAD
                    if (!empty($_FILES['seg_icon'])) {
                        $File = $_FILES['seg_icon'];

                        if ($Segment['seg_icon'] && file_exists("../../uploads/{$Segment['seg_icon']}") && !is_dir("../../uploads/{$Segment['seg_icon']}")) {
                            unlink("../../uploads/{$Segment['seg_icon']}");
                        }

                        $Upload->Image($File, "{$PostData['seg_name']}", 600);
                        if ($Upload->getResult()) {
                            $PostData['seg_icon'] = $Upload->getResult();
                        } else {
                            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem PNG de 600x600px para a capa!",
                                E_USER_WARNING);
                            echo json_encode($jSON);
                            return;
                        }
                    }

                    $Read->FullRead("SELECT seg_id FROM " . DB_SEG . " WHERE seg_name = :nm AND seg_id != :id",
                        "nm={$PostData['seg_name']}&id={$SegId}");
                    if ($Read->getResult()) {
                        $PostData['seg_name'] = "{$PostData['seg_name']}";
                    }

                    $Update->ExeUpdate(DB_SEG, $PostData, "WHERE seg_id = :id", "id={$SegId}");

                    $jSON['name'] = $PostData['seg_name'];
                    $jSON['trigger'] = AjaxErro("<span class='icon-checkmark'><b>SEGMENTO ATUALIZADO:</b> Olá {$_SESSION['userLogin']['user_name']}. O segmento {$PostData['seg_title']} foi atualizado com sucesso!<span>");
                    $jSON['view'] = BASE . '/segmento/' . $PostData['seg_name'];
                }
                break;

            case 'sendimage':
                $NewImage = $_FILES['image'];
                $Read->FullRead("SELECT seg_title, seg_name FROM " . DB_SEG . " WHERE seg_id = :id",
                    "id={$PostData['seg_id']}");
                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o segmento vinculado!",
                        E_USER_WARNING);
                } else {
                    $Upload = new Upload('../../uploads/');
                    $Upload->Image($NewImage, $Read->getResult()[0]['seg_title'] . '-' . time(), IMAGE_W);
                    if ($Upload->getResult()) {
                        $PostData['segment_id'] = $PostData['seg_id'];
                        $PostData['image'] = $Upload->getResult();
                        unset($PostData['seg_id']);

                        $Create->ExeCreate(DB_SEG_IMAGE, $PostData);
                        $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['seg_title']}' alt='{$Read->getResult()[0]['seg_title']}' src='../uploads/{$PostData['image']}'/>";
                    } else {
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no segmento!",
                            E_USER_WARNING);
                    }
                }
                break;

            case 'delete':
                $SegId = $PostData['del_id'];

                $Read->ExeRead(DB_SEG, "WHERE seg_id = :id", "id={$SegId}");
                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois o segmento não existe ou foi removido recentemente!",
                        E_USER_WARNING);
                } else {
                    $Segment = $Read->getResult()[0];
                    $SegCover = "../../uploads/{$Segment['seg_cover']}";

                    if (file_exists($SegCover) && !is_dir($SegCover)) {
                        unlink($SegCover);
                    }

                    $Read->ExeRead(DB_SEG_IMAGE, "WHERE segment_id = :id", "id={$Segment['seg_id']}");
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SegImage) {
                            $SegImageIs = "../../uploads/{$SegImage['image']}";
                            if (file_exists($SegImageIs) && !is_dir($SegImageIs)) {
                                unlink($SegImageIs);
                            }
                        }
                        $Delete->ExeDelete(DB_SEG_IMAGE, "WHERE segment_id = :id", "id={$Segment['seg_id']}");
                    }

                    $Read->ExeRead(DB_SEG_GALLERY, "WHERE segment_id = :id", "id={$Segment['seg_id']}");
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SegGB) {
                            $SegGBImage = "../../uploads/{$SegGB['image']}";
                            if (file_exists($SegGBImage) && !is_dir($SegGBImage)) {
                                unlink($SegGBImage);
                            }
                        }
                        $Delete->ExeDelete(DB_SEG_GALLERY, "WHERE segment_id = :id", "id={$Segment['seg_id']}");
                    }

                    $Delete->ExeDelete(DB_SEG, "WHERE seg_id = :id", "id={$Segment['seg_id']}");
                    $jSON['success'] = true;
                }
                break;

            case 'gbremove':
                echo "";
                $Read->FullRead("SELECT image FROM " . DB_SEG_GALLERY . " WHERE id = :id",
                    "id={$PostData['img']}");
                if ($Read->getResult()) {
                    $ImageRemove = "../../uploads/{$Read->getResult()[0]['image']}";
                    if (file_exists($ImageRemove) && !is_dir($ImageRemove)) {
                        unlink($ImageRemove);
                    }
                    $Delete->ExeDelete(DB_SEG_GALLERY, "WHERE id = :id", "id={$PostData['img']}");
                    $jSON['success'] = true;
                }
                break;
        }

        //RETORNA O CALLBACK
        if ($jSON) {
            echo json_encode($jSON);
        } else {
            $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
                E_USER_ERROR);
            echo json_encode($jSON);
        }
    } else {
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
