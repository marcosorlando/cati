<?php

    session_start();
    require '../../_app/Config.inc.php';
    $NivelAcess = LEVEL_WC_THANKYOU_PAGES;

    if (!APP_THANKYOU_PAGES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
            E_USER_ERROR);
        echo json_encode($jSON);
        die;
    endif;

    usleep(50000);

    //DEFINE O CALLBACK E RECUPERA O POST
    $jSON = NULL;
    $CallBack = 'Thankyoupages';
    $PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //VALIDA AÇÃO
    if ($PostData && $PostData['callback_action'] && $PostData['callback'] == $CallBack):
        //PREPARA OS DADOS
        $Case = $PostData['callback_action'];
        unset($PostData['callback'], $PostData['callback_action']);

        // AUTO INSTANCE OBJECT READ
        if (empty($Read)):
            $Read = new Read;
        endif;

        // AUTO INSTANCE OBJECT CREATE
        if (empty($Create)):
            $Create = new Create;
        endif;

        // AUTO INSTANCE OBJECT UPDATE
        if (empty($Update)):
            $Update = new Update;
        endif;

        // AUTO INSTANCE OBJECT DELETE
        if (empty($Delete)):
            $Delete = new Delete;
        endif;

        //SELECIONA AÇÃO
        switch ($Case):
            //DELETE
            case 'delete':
                $Read->FullRead("SELECT page_cover, page_logo, page_pdf FROM " . DB_THANKYOU_PAGES . " WHERE page_id = :ps", "ps={$PostData['del_id']}");

                if ($Read->getResult()):

                        $CoverRemove = "../../uploads/thankyoupages/{$Read->getResult()[0]['page_cover']}";
                        $LogoRemove = "../../uploads/thankyoupages/{$Read->getResult()[0]['page_logo']}";
                        $MaterialRemove = "../../uploads/thankyoupages/{$Read->getResult()[0]['page_pdf']}";

                        if (file_exists($CoverRemove) && !is_dir($CoverRemove)):
                            unlink($CoverRemove);
                        endif;
                        if (file_exists($LogoRemove) && !is_dir($LogoRemove)):
                            unlink($LogoRemove);
                        endif;
                        if (file_exists($MaterialRemove) && !is_dir($MaterialRemove)):
                            unlink($MaterialRemove);
                        endif;

                endif;

                $Delete->ExeDelete(DB_THANKYOU_PAGES, "WHERE page_id = :id", "id={$PostData['del_id']}");

                $jSON['success'] = TRUE;
                break;

            //MANAGER
            case 'manage':
                $PageId = $PostData['page_id'];
                unset($PostData['page_id']);

                $PostData['page_status'] = (!empty($PostData['page_status']) ? '1' : '0');
                $PostData['page_name'] = (!empty($PostData['page_name']) ? Check::Name($PostData['page_name']) : Check::Name($PostData['page_title']) . '-download');

                $Read->ExeRead(DB_THANKYOU_PAGES, "WHERE page_id= :id", "id={$PageId}");

                if ($Read->getResult()) {
                    $ThisPage = $Read->getResult()[0];
                }

                //UPLOAD PDF
                if (!empty($_FILES['page_pdf'])):
                    $File = $_FILES['page_pdf'];

                    if ($ThisPage['page_pdf'] && file_exists("../../uploads/thankyoupages/{$ThisPage['page_pdf']}") && !is_dir("../../uploads/thankyoupages/{$ThisPage['page_pdf']}")):
                        unlink("../../uploads/thankyoupages/{$ThisPage['page_pdf']}");
                    endif;

                    $Upload = new Upload('../../uploads/thankyoupages/');
                    $Upload->File($File, $PostData['page_name'] . '-pdf');

                    if ($Upload->getResult()):
                        $PostData['page_pdf'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-file-pdf'>ERRO AO ARQUIVO PDF</b> Olá {$_SESSION['userLogin']['user_name']}, selecione um arquivo em PDF para inserir no produto!",
                            E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_pdf']);
                endif;

                //UPLOAD BACKGROUND-IMAGE
                if (!empty($_FILES['page_cover'])):
                    $File = $_FILES['page_cover'];

                    if ($ThisPage['page_cover'] && file_exists("../../uploads/thankyoupages/{$ThisPage['page_cover']}") && !is_dir("../../uploads/thankyoupages/{$ThisPage['page_cover']}")):
                        unlink("../../uploads/thankyoupages/{$ThisPage['page_cover']}");
                    endif;

                    $Upload = new Upload('../../uploads/thankyoupages/');
                    $Upload->Image($File, $PostData['page_name'] . '-bg');

                    if ($Upload->getResult()):
                        $PostData['page_cover'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!",
                            E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_cover']);
                endif;

                //ENVIAR LOGO
                if (!empty($_FILES['page_logo'])):
                    $File = $_FILES['page_logo'];

                    if ($ThisPage['page_logo'] && file_exists("../../uploads/thankyoupages/{$ThisPage['page_logo']}") && !is_dir("../../uploads/thankyoupages/{$ThisPage['page_logo']}")):
                        unlink("../../uploads/thankyoupages/{$ThisPage['page_logo']}");
                    endif;

                    $Upload = new Upload('../../uploads/thankyoupages/');
                    $Upload->Image($File, $PostData['page_name'] . '-logo');

                    if ($Upload->getResult()):
                        $PostData['page_logo'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR LOGO:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como logotipo!",
                            E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_logo']);
                endif;

                $Read->FullRead("SELECT page_name FROM " . DB_THANKYOU_PAGES . " WHERE page_name = :nm AND page_id != :id",
                    "nm={$PostData['page_name']}&id={$PageId}");

                if ($Read->getResult()):
                    $PostData['page_name'] = "{$PostData['page_name']}-{$PageId}";
                endif;

                $jSON['name'] = $PostData['page_name'];
                $jSON['view'] = BASE . "/{$PostData['page_name']}";

                $Update->ExeUpdate(DB_THANKYOU_PAGES, $PostData, "WHERE page_id = :id", "id={$PageId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Página atualizada com sucesso!</b>");
                break;

        endswitch;

        //RETORNA O CALLBACK
        if ($jSON):
            echo json_encode($jSON);
        else:
            $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
                E_USER_ERROR);
            echo json_encode($jSON);
        endif;
    else:
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    endif;
