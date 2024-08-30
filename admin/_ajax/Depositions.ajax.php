<?php
session_start();
require '../../_app/Config.inc.php';
$NivelAcess = LEVEL_WC_DEPOSITIONS;

if (!APP_DEPOSITIONS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;
usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Depositions';
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
        //GERENCIA
        case 'manager':
            $DepositionId = $PostData['depositions_id'];
            
            $Image = (!empty($_FILES['depositions_image']) ? $_FILES['depositions_image'] : null);
            unset($PostData['depositions_id'], $PostData['depositions_image']);
            
            $Read->FullRead("SELECT depositions_image FROM " . DB_DEPOSITIONS . " WHERE depositions_id = :id", "id={$DepositionId}");

            if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['depositions_image'])):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Favor envie uma FOTO nas medidas de 300X300px!', E_USER_ERROR);
            elseif (in_array('', $PostData)):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o destaque, favor preencha todos os campos!', E_USER_ERROR);
                $jSON['error'] = true;
            else:
                $PostData['depositions_date'] = date('Y-m-d H:i:s');
                
                if (!empty($Image)):
                    if ($Read->getResult() && !empty($Read->getResult()[0]['depositions_image']) && file_exists("../../uploads/depositions/{$Read->getResult()[0]['depositions_image']}") && !is_dir("../../uploads/depositions/{$Read->getResult()[0]['depositions_image']}")):
                        unlink("../../uploads/depositions/{$Read->getResult()[0]['depositions_image']}");
                    endif;
                    $Upload = new Upload('../../uploads/');
                    $Upload->Image($Image, Check::Name($PostData['depositions_name']), SLIDE_W, 'depositions');
                    $PostData['depositions_image'] = $Upload->getResult();
                endif;
               
                $Update->ExeUpdate(DB_DEPOSITIONS, $PostData, "WHERE depositions_id = :id", "id={$DepositionId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O depoimento foi atualizado com sucesso. E será exibido no intervalo de datas cadastradas!");
            endif;
            break;

        //DELETA
        case 'delete':         
          
            $DepositionId = $PostData['del_id'];
            $Read->FullRead("SELECT depositions_image FROM " . DB_DEPOSITIONS . " WHERE depositions_id = :id", "id={$DepositionId}");
            if ($Read->getResult()):
                $DepositionImage = (!empty($Read->getResult()[0]['depositions_image']) ? $Read->getResult()[0]['depositions_image'] : null);
                if ($DepositionImage && file_exists("../../uploads/depositions/{$DepositionImage}") && !is_dir("../../uploads/depositions/{$DepositionImage}")):
                    unlink("../../uploads/depositions/{$DepositionImage}");
                endif;
            endif;

            $Delete->ExeDelete(DB_DEPOSITIONS, "WHERE depositions_id = :id", "id={$DepositionId}");
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
