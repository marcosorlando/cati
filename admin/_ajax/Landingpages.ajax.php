<?php

    session_start();
    require '../../_app/Config.inc.php';
    $NivelAcess = LEVEL_WC_LANDING_PAGES;

    if (!APP_LANDING_PAGES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
        echo json_encode($jSON);
        die;
    endif;

    usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Landingpages';
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
                $Read->FullRead("SELECT page_logo, page_social_media, page_cover, page_mockup, page_mockup FROM " . DB_LANDING_PAGES . " WHERE page_id = :pi", "pi={$PostData['del_id']}");

                if ($Read->getResult()):

                        $LogoRemove = "../../uploads/landingpages/{$Read->getResult()[0]['page_logo']}";
                        $MockupRemove = "../../uploads/landingpages/{$Read->getResult()[0]['page_mockup']}";
                        $SocialRemove = "../../uploads/landingpages/{$Read->getResult()[0]['page_social_media']}";
                        $CoverRemove = "../../uploads/landingpages/{$Read->getResult()[0]['page_cover']}";

                        if (file_exists($LogoRemove) && !is_dir($LogoRemove)):
                            unlink($LogoRemove);
                        endif;
                        if (file_exists($MockupRemove) && !is_dir($MockupRemove)):
                            unlink($MockupRemove);
                        endif;
                        if (file_exists($SocialRemove) && !is_dir($SocialRemove)):
                            unlink($SocialRemove);
                        endif;
                        if (file_exists($CoverRemove) && !is_dir($CoverRemove)):
                            unlink($CoverRemove);
                        endif;

                endif;

                $Delete->ExeDelete(DB_LANDING_PAGES, "WHERE page_id = :id", "id={$PostData['del_id']}");

                $jSON['success'] = true;
                break;

            //MANAGER
            case 'manage':
                $PageId = $PostData['page_id'];
                unset($PostData['page_id']);

                $PostData['page_status'] = (!empty($PostData['page_status']) ? '1' : '0');
                $PostData['page_name'] = (!empty($PostData['page_name']) ? Check::Name($PostData['page_name']) : Check::Name($PostData['page_title']));

                $Read->ExeRead(DB_LANDING_PAGES, "WHERE page_id= :id", "id={$PageId}");
                $ThisPage = $Read->getResult()[0];

                //UPLOAD LOGOTIPO
                if (!empty($_FILES['page_logo'])):
                    $File = $_FILES['page_logo'];

                    if ($ThisPage['page_logo'] && file_exists("../../uploads/landingpages/{$ThisPage['page_logo']}") && !is_dir("../../uploads/landingpages/{$ThisPage['page_logo']}")):
                        unlink("../../uploads/landingpages/{$ThisPage['page_logo']}");
                    endif;

                    $Upload = new Upload('../../uploads/landingpages/');
                    $Upload->Image($File, $PostData['page_name'] . '-logo', IMAGE_W);

                    if ($Upload->getResult()):
                        $PostData['page_logo'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR LOGO:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como Logotipo!", E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_logo']);
                endif;

                //ENVIAR MOCKUP
                if (!empty($_FILES['page_mockup'])):
                    $File = $_FILES['page_mockup'];

                    if ($ThisPage['page_mockup'] && file_exists("../../uploads/landingpages/{$ThisPage['page_mockup']}") && !is_dir("../../uploads/landingpages/{$ThisPage['page_mockup']}")):
                        unlink("../../uploads/landingpages/{$ThisPage['page_mockup']}");
                    endif;

                    $Upload = new Upload('../../uploads/landingpages/');
                    $Upload->Image($File, $PostData['page_name'] . '-mkp', AVATAR_W);

                    if ($Upload->getResult()):
                        $PostData['page_mockup'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR Mockup:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como mockup!", E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_mockup']);
                endif;

                     //UPLOAD BACKGROUND-IMAGE - BOX
                if (!empty($_FILES['page_cover'])):
                    $File = $_FILES['page_cover'];

                    if ($ThisPage['page_cover'] && file_exists("../../uploads/landingpages/{$ThisPage['page_cover']}") && !is_dir("../../uploads/landingpages/{$ThisPage['page_cover']}")):
                        unlink("../../uploads/landingpages/{$ThisPage['page_cover']}");
                    endif;

                    $Upload = new Upload('../../uploads/landingpages/');
                    $Upload->Image($File, $PostData['page_name'] . '-bg', IMAGE_W);

                    if ($Upload->getResult()):
                        $PostData['page_cover'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!", E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_cover']);
                endif;

                     //UPLOAD SOCIAL-MEDIA-IMAGE - BOX
                if (!empty($_FILES['page_social_media'])):
                    $File = $_FILES['page_social_media'];

                    if ($ThisPage['page_social_media'] && file_exists("../../uploads/landingpages/{$ThisPage['page_social_media']}") && !is_dir("../../uploads/landingpages/{$ThisPage['page_social_media']}")):
                        unlink("../../uploads/landingpages/{$ThisPage['page_social_media']}");
                    endif;

                    $Upload = new Upload('../../uploads/landingpages/');
                    $Upload->Image($File, $PostData['page_name'] . '-social', IMAGE_W);

                    if ($Upload->getResult()):
                        $PostData['page_social_media'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como Thumb para Rede Social!", E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                else:
                    unset($PostData['page_social_media']);
                endif;

                $Read->FullRead("SELECT page_name FROM " . DB_LANDING_PAGES . " WHERE page_name = :nm AND page_id != :id", "nm={$PostData['page_name']}&id={$PageId}");

                if ($Read->getResult()):
                    $PostData['page_name'] = "{$PostData['page_name']}-{$PageId}";
                endif;

                $jSON['name'] = $PostData['page_name'];
                $jSON['view'] = BASE . "/{$PostData['page_name']}";

                $Update->ExeUpdate(DB_LANDING_PAGES, $PostData, "WHERE page_id = :id", "id={$PageId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Página atualizada com sucesso!</b>");
                break;

        endswitch;

        //RETORNA O CALLBACK
        if ($jSON):
            echo json_encode($jSON);
        else:
            $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
            echo json_encode($jSON);
        endif;
    else:
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    endif;
