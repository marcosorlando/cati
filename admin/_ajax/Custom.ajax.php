<?php
session_start();
require '../../_app/Config.inc.php';

if (empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < 6):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;

//ADMIN
$Admin = $_SESSION['userLogin'];

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Custom';
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
        //HELLO BAR CREATE / UPDATE
        case 'hellobar_update':

            //NIVEL DE ACESSO
            if ($Admin['user_level'] < LEVEL_WC_HELLO):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
                break;
            endif;

            $HelloId = $PostData['hello_id'];
            $HelloRule = $PostData['hello_rule'];
            unset($PostData['hello_id'], $PostData['hello_rule'], $PostData['hello_cover']);

            $Read->ExeRead(DB_HELLO, "WHERE hello_id = :id", "id={$HelloId}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPPSSS:</b> Desculpe {$Admin['user_name']}, mas você tentou atualizar um Pop-Up que não existe!", E_USER_ERROR);
                break;
            endif;

            extract($Read->getResult()[0]);

            $HelloUpload = (!empty($_FILES['hello_cover']) ? $_FILES['hello_cover'] : null);
            if (empty($hello_image) && empty($HelloUpload)):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Olá {$Admin['user_name']}, você precisa enviar a imagem do hello bar!", E_USER_WARNING);
                break;
            endif;

            if (!empty($HelloUpload)):
                if (!empty($hello_image) && file_exists("../../uploads/{$hello_image}") && !is_dir("../../uploads/{$hello_image}")):
                    unlink("../../uploads/{$hello_image}");
                endif;

                $Upload = new Upload("../../uploads/");
                $Upload->Image($HelloUpload, Check::Name($PostData['hello_title']), IMAGE_W);
                if (!$Upload->getResult()):
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Não foi possível enviar a imagem:</b> Selecione imagens JPG, ou PNG para a sua hello!", E_USER_WARNING);
                    break;
                else:
                    $PostData['hello_image'] = $Upload->getResult();
                endif;
            endif;

            //CAMPOS EM BRANCO
            if (in_array("", $PostData)):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">Erro ao cadastrar:</b> Para criar uma hellobar, preencha todos os campos do formulário!', E_USER_WARNING);
                break;
            endif;

            //RETORNAR O RULE PARA O WC_VIEW QUANDO TIVER
            $PostData['hello_status'] = (!empty($PostData['hello_status']) ? 1 : 0);
            $PostData['hello_rule'] = (!empty($HelloRule) ? $HelloRule : null);
            $PostData['hello_start'] = Check::Data($PostData['hello_start']);
            $PostData['hello_end'] = Check::Data($PostData['hello_end']);
            $PostData['hello_date'] = date('Y-m-d H:i:s');

            $Update->ExeUpdate(DB_HELLO, $PostData, "WHERE hello_id = :id", "id={$HelloId}");
            $jSON['trigger'] = AjaxErro('<b class="icon-checkmark">Hellobar atualizada:</b> Sua hellobar foi atualizada e já pode ser exibida em seu site!');
            break;

        //HELLO BAR DELETE
        case 'hellobar_delete':
            $HelloId = $PostData['del_id'];

            $Read->FullRead("SELECT hello_image FROM " . DB_HELLO . " WHERE hello_id = :hello", "hello={$HelloId}");
            if ($Read->getResult()):
                $hello_image = $Read->getResult()[0]['hello_image'];
                if (file_exists("../../uploads/{$hello_image}") && !is_dir("../../uploads/{$hello_image}")):
                    unlink("../../uploads/{$hello_image}");
                endif;
            endif;

            $Delete->ExeDelete(DB_HELLO, "WHERE hello_id = :id", "id={$HelloId}");
            $jSON['success'] = true;
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
