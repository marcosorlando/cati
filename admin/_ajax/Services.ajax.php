<?php
    session_start();
    require '../../_app/Config.inc.php';
    $NivelAcess = LEVEL_WC_SERVICES;

    if (!APP_SERVICES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
        echo json_encode($jSON);
        die;
    }

    usleep(50000);

    //DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Services';
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
                $SvcId = $PostData['svc_id'];
                $PostData['svc_status'] = (!empty($PostData['svc_status']) ? $PostData['svc_status'] : '0');

                $Read->ExeRead(DB_SVC, "WHERE svc_id = :id", "id={$SvcId}");

                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar o serviço. Experimente atualizar a página!", E_USER_WARNING);

                } else {
                    $Service = $Read->getResult()[0];

                    //var_dump($PostData);
                    unset($PostData['svc_id'], $PostData['svc_cover'], $PostData['image'], $PostData['svc_icon']);

                    $PostData['svc_name'] = Check::Name($PostData['svc_title']);

                    if (!empty($_FILES['svc_cover'])) {
                        $File = $_FILES['svc_cover'];

                        if ($Service['svc_cover'] && file_exists("../../uploads/{$Service['svc_cover']}") && !is_dir("../../uploads/{$Service['svc_cover']}")) {
                            unlink("../../uploads/{$Service['svc_cover']}");
                        }

                        $Upload->Image($File, "{$SvcId}-{$PostData['svc_name']}-" . time(), 1200);
                        if ($Upload->getResult()) {
                            $PostData['svc_cover'] = $Upload->getResult();
                        } else {
                            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 1200X628px para a capa!", E_USER_WARNING);
                            echo json_encode($jSON);
                            return;
                        }
                    }

                    if (!empty($_FILES['svc_icon'])) {
                        $File = $_FILES['svc_icon'];

                        if ($Service['svc_icon'] && file_exists("../../uploads/{$Service['svc_icon']}") && !is_dir("../../uploads/{$Service['svc_icon']}")) {
                            unlink("../../uploads/{$Service['svc_icon']}");
                        }

                        $Upload->Image($File, "{$SvcId}-{$PostData['svc_name']}-" . time(), 1200);
                        if ($Upload->getResult()) {
                            $PostData['svc_icon'] = $Upload->getResult();
                        } else {
                            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 1200X628px para a capa!", E_USER_WARNING);
                            echo json_encode($jSON);
                            return;
                        }
                    }

                    if (!empty($_FILES['image'])) {
                        $File = $_FILES['image'];
                        $gbFile = array();
                        $gbCount = count($File['type']);
                        $gbKeys = array_keys($File);
                        $gbLoop = 0;

                        for ($gb = 0; $gb < $gbCount; $gb++) {
                            foreach ($gbKeys as $Keys) {
                                $gbFiles[$gb][$Keys] = $File[$Keys][$gb];
                            }
                        }

                        $jSON['gallery'] = null;
                        foreach ($gbFiles as $UploadFile) {
                            $gbLoop ++;
                            $Upload->Image($UploadFile, "{$SvcId}-{$gbLoop}-" . time() . base64_encode(time()), 1000);
                            if ($Upload->getResult()) {
                                $gbCreate = ['svc_id' => $SvcId, "image" => $Upload->getResult()];
                                $Create->ExeCreate(DB_SVC_GALLERY, $gbCreate);
                                $jSON['gallery'] .= "<img rel='Services' id='{$Create->getResult()}' alt='Imagem em {$PostData['svc_title']}' title='Imagem em {$PostData['svc_title']}' src='../uploads/{$Upload->getResult()}'/>";
                            }
                        }
                    }

                    $Read->FullRead("SELECT svc_id FROM " . DB_SVC . " WHERE svc_name = :nm AND svc_id != :id", "nm={$PostData['svc_name']}&id={$SvcId}");
                    if ($Read->getResult()) {
                        $PostData['svc_name'] = "{$PostData['svc_name']}-{$SvcId}";
                    }

                    $jSON['name'] = $PostData['svc_name'];
                    $jSON['trigger'] = AjaxErro("<span class='icon-checkmark'><b>SERVIÇO ATUALIZADO:</b> Olá {$_SESSION['userLogin']['user_name']}. O serviço {$PostData['svc_title']} foi atualizado com sucesso!<span>");

                    $PostData['svc_status'] = (!empty($PostData['svc_status']) ? '1' : '0');

                    $Update->ExeUpdate(DB_SVC, $PostData, "WHERE svc_id = :id", "id={$SvcId}");
                    $jSON['view'] = BASE . '/servico/' . $PostData['svc_name'];
                }
                break;

            case 'sendimage':
                $NewImage = $_FILES['image'];
                $Read->FullRead("SELECT svc_title, svc_name FROM " . DB_SVC . " WHERE svc_id = :id", "id={$PostData['svc_id']}");
                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o serviço vinculado!", E_USER_WARNING);
                } else {
                    $Upload = new Upload('../../uploads/');
                    $Upload->Image($NewImage, $PostData['svc_id'] . '-' . time(), IMAGE_W);
                    if ($Upload->getResult()) {
                        $PostData['svc_id'] = $PostData['svc_id'];
                        $PostData['image'] = $Upload->getResult();
                        unset($PostData['svc_id']);

                        $Create->ExeCreate(DB_SVC_IMAGE, $PostData);
                        $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['svc_title']}' alt='{$Read->getResult()[0]['svc_title']}' src='../uploads/{$PostData['image']}'/>";
                    } else {
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no serviço!", E_USER_WARNING);
                    }
                }
                break;

            case 'delete':
                $SvcId = $PostData['del_id'];
                $Read->ExeRead(DB_SVC, "WHERE svc_id = :id", "id={$SvcId}");
                $Service = $Read->getResult()[0];

                if (!$Service) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois o serviço não existe ou foi removido recentemente!", E_USER_WARNING);
                } else {
                    $SvcCover = "../../uploads/{$Service['svc_cover']}";

                    if (file_exists($SvcCover) && !is_dir($SvcCover)) {
                        unlink($SvcCover);
                    }

                    $Read->ExeRead(DB_SVC_IMAGE, "WHERE svc_id = :id", "id={$Service['svc_id']}");
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SvcImage) {
                            $SvcImageIs = "../../uploads/{$SvcImage['image']}";
                            if (file_exists($SvcImageIs) && !is_dir($SvcImageIs)) {
                                unlink($SvcImageIs);
                            }
                        }
                        $Delete->ExeDelete(DB_SVC_IMAGE, "WHERE svc_id = :id", "id={$Service['svc_id']}");
                    }

                    $Read->ExeRead(DB_SVC_GALLERY, "WHERE svc_id = :id", "id={$Service['svc_id']}");
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SvcGallery) {
                            $SvcGalleryImage = "../../uploads/{$SvcGallery['image']}";
                            if (file_exists($SvcGalleryImage) && !is_dir($SvcGalleryImage)) {
                                unlink($SvcGalleryImage);
                            }
                        }
                        $Delete->ExeDelete(DB_SVC_GALLERY, "WHERE svc_id = :id", "id={$Service['svc_id']}");
                    }

                    $Delete->ExeDelete(DB_SVC, "WHERE svc_id = :id", "id={$Service['svc_id']}");
                    $jSON['success'] = true;
                }
                break;

            case 'gbremove':
                $Read->FullRead("SELECT image FROM " . DB_SVC_GALLERY . " WHERE id = :id", "id={$PostData['img']}");
                if ($Read->getResult()) {
                    $ImageRemove = "../../uploads/{$Read->getResult()[0]['image']}";
                    if (file_exists($ImageRemove) && !is_dir($ImageRemove)) {
                        unlink($ImageRemove);
                    }
                    $Delete->ExeDelete(DB_SVC_GALLERY, "WHERE id = :id", "id={$PostData['img']}");
                    $jSON['success'] = true;
                }
                break;
        }

        //RETORNA O CALLBACK
        if ($jSON) {
            echo json_encode($jSON);
        } else {
            $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
            echo json_encode($jSON);
        }
    } else {
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
