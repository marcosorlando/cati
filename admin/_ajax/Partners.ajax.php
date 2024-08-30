<?php
session_start();
require '../../_app/Config.inc.php';
$NivelAcess = LEVEL_WC_PARTNERS;

if (!APP_PARTNERS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;
usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Partners';
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
            $PartnerID = $PostData['partner_id'];
            
            $Image = (!empty($_FILES['partner_image']) ? $_FILES['partner_image'] : null);
            unset($PostData['partner_id'], $PostData['partner_image']);
            
            $Read->FullRead("SELECT partner_image FROM " . DB_PARTNERS . " WHERE partner_id = :id", "id={$PartnerID}");

            if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['partner_image'])):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Favor envie uma FOTO nas medidas de 300X200px!', E_USER_ERROR);
            elseif (in_array('', $PostData)):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o destaque, favor preencha todos os campos!', E_USER_ERROR);
                $jSON['error'] = true;
            else:
                $PostData['partner_date'] = date('Y-m-d H:i:s');
                
                if (!empty($Image)):
                    if ($Read->getResult() && !empty($Read->getResult()[0]['partner_image']) && file_exists("../../uploads/partners/{$Read->getResult()[0]['partner_image']}") && !is_dir("../../uploads/partners/{$Read->getResult()[0]['partner_image']}")):
                        unlink("../../uploads/partners/{$Read->getResult()[0]['partner_image']}");
                    endif;
                    $Upload = new Upload('../../uploads/');
                    $Upload->Image($Image, Check::Name($PostData['partner_name']), SLIDE_W, 'partners');
                    $PostData['partner_image'] = $Upload->getResult();
                endif;
               
                $Update->ExeUpdate(DB_PARTNERS, $PostData, "WHERE partner_id = :id", "id={$PartnerID}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O parceiro foi atualizado com sucesso. E será exibido no intervalo de datas cadastradas!");
            endif;
            break;

        //DELETA
        case 'delete':
          
            $PartnerID = $PostData['del_id'];
            $Read->FullRead("SELECT partner_image FROM " . DB_PARTNERS . " WHERE partner_id = :id", "id={$PartnerID}");
            if ($Read->getResult()):
                $PartnerImage = (!empty($Read->getResult()[0]['partner_image']) ? $Read->getResult()[0]['partner_image'] : null);
                if ($PartnerImage && file_exists("../../uploads/partners/{$PartnerImage}") && !is_dir("../../uploads/partners/{$PartnerImage}")):
                    unlink("../../uploads/partners/{$PartnerImage}");
                endif;
            endif;

            $Delete->ExeDelete(DB_PARTNERS, "WHERE partner_id = :id", "id={$PartnerID}");
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
