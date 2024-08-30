<?php
    session_start();
    require '../../_app/Config.inc.php';
    $NivelAcess = LEVEL_WC_CERTIFICATIONS;

    if (!APP_CERTIFICATIONS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
            E_USER_ERROR);
        echo json_encode($jSON);
        die;
    }

    usleep(50000);

    //DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Certifications';
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

                $CertId = $PostData['cert_id'];
                $PostData['cert_status'] = (!empty($PostData['cert_status']) ? '1' : '0');

                $Read->ExeRead(DB_CERT, "WHERE cert_id = :id", "id={$CertId}");

                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar a certificação. Experimente atualizar a página!",
                        E_USER_WARNING);
                } else {
                    $Certification = $Read->getResult()[0];

                    unset($PostData['cert_id'], $PostData['cert_cover']);

                    $PostData['cert_name'] = (!empty($PostData['cert_name']) ? Check::Name($PostData['cert_name']) : Check::Name($PostData['cert_title']));

                    //COVER UPLOAD
                    if (!empty($_FILES['cert_cover'])) {
                        $File = $_FILES['cert_cover'];

                        if ($Certification['cert_cover'] && file_exists("../../uploads/{$Certification['cert_cover']}") && !is_dir("../../uploads/{$Certification['cert_cover']}")) {
                            unlink("../../uploads/{$Certification['cert_cover']}");
                        }

                        $Upload->Image($File, $PostData['cert_name'].time(), 800);
                        if ($Upload->getResult()) {
                            $PostData['cert_cover'] = $Upload->getResult();
                        } else {
                            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 800px de largura para a capa!",
                                E_USER_WARNING);
                            echo json_encode($jSON);
                            return;
                        }
                    }

                    $Read->FullRead("SELECT cert_id FROM " . DB_CERT . " WHERE cert_name = :nm AND cert_id != :id",
                        "nm={$PostData['cert_name']}&id={$CertId}");
                    if ($Read->getResult()) {
                        $PostData['cert_name'] = $PostData['cert_name'].time();
                    }

                    $jSON['name'] = $PostData['cert_name'];
                    $jSON['trigger'] = AjaxErro("<span class='icon-checkmark'><b>CERTIFICAÇAO ATUALIZADA:</b> Olá {$_SESSION['userLogin']['user_name']}. A certificação {$PostData['cert_title']} foi atualizada com sucesso!<span>");

                    $Update->ExeUpdate(DB_CERT, $PostData, "WHERE cert_id = :id", "id={$CertId}");
                    $jSON['view'] = BASE . '/certificacoes/' . $PostData['cert_name'];
                }
                break;

            case 'delete':
                $CertId = $PostData['del_id'];

                $Read->ExeRead(DB_CERT, "WHERE cert_id = :id", "id={$CertId}");
                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois a certificação não existe ou foi removido recentemente!",
                        E_USER_WARNING);
                } else {
                    $Certification = $Read->getResult()[0];
                    $CertCover = "../../uploads/{$Certification['cert_cover']}";

                    if (file_exists($CertCover) && !is_dir($CertCover)) {
                        unlink($CertCover);
                    }

                    $Delete->ExeDelete(DB_CERT, "WHERE cert_id = :id", "id={$Certification['cert_id']}");
                    $jSON['success'] = true;
                }
                break;

            case 'sendimage':
                $NewImage = $_FILES['image'];
                $Read->FullRead('SELECT cert_title, cert_name FROM ' . DB_CERT . ' WHERE cert_id = :id',
                    "id={$PostData['cert_id']}");

                if (!$Read->getResult()) {
                    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar a certificação vinculada!",
                        E_USER_WARNING);

                } else {
                    $Upload = new Upload('../../uploads/');
                    $Upload->Image($NewImage, $Read->getResult()[0]['cert_title'] . '-' . time(), IMAGE_W);

                    if ($Upload->getResult()) {
                        $PostData['certification_id'] = $PostData['cert_id'];
                        $PostData['image'] = $Upload->getResult();
                        unset($PostData['cert_id']);

                        $Create->ExeCreate(DB_CERT_IMAGE, $PostData);
                        $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['cert_title']}' alt='{$Read->getResult()[0]['cert_title']}' src='../uploads/{$PostData['image']}'/>";
                    } else {
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir na Certificação!",
                            E_USER_WARNING);
                    }
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
