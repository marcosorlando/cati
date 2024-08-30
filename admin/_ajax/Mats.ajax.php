<?php

session_start();
require '../../_app/Config.inc.php';
$NivelAcess = 6;

if (!APP_MATERIALS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;

usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Mats';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read;
    $Create = new Create;
    $Update = new Update;
    $Delete = new Delete;

    //SELECIONA AÇÃO
    switch ($Case):
        //DELETE
        case 'delete':
            $PostData['mat_id'] = $PostData['del_id'];
            $Read->FullRead("SELECT mat_cover FROM " . DB_MATERIAIS . " WHERE mat_id = :ps", "ps={$PostData['mat_id']}");
            if ($Read->getResult() && file_exists("../../uploads/{$Read->getResult()[0]['mat_cover']}") && !is_dir("../../uploads/{$Read->getResult()[0]['mat_cover']}")):
                unlink("../../uploads/{$Read->getResult()[0]['mat_cover']}");
            endif;

            $Delete->ExeDelete(DB_MATERIAIS, "WHERE mat_id = :id", "id={$PostData['mat_id']}");
         
            $jSON['success'] = true;
            break;

        case 'manager':
            $PostId = $PostData['mat_id'];
            unset($PostData['mat_id']);

            $Read->ExeRead(DB_MATERIAIS, "WHERE mat_id = :id", "id={$PostId}");
            $ThisPost = $Read->getResult()[0];

            $PostData['mat_name'] = (!empty($PostData['mat_name']) ? Check::Name($PostData['mat_name']) : Check::Name($PostData['mat_title']));
            $Read->ExeRead(DB_MATERIAIS, "WHERE mat_id != :id AND mat_name = :name", "id={$PostId}&name={$PostData['mat_name']}");
            if ($Read->getResult()):
                $PostData['mat_name'] = "{$PostData['mat_name']}-{$PostId}";
            endif;
            $jSON['name'] = $PostData['mat_name'];

            if (!empty($_FILES['mat_cover'])):
                $File = $_FILES['mat_cover'];

                if ($ThisPost['mat_cover'] && file_exists("../../uploads/{$ThisPost['mat_cover']}") && !is_dir("../../uploads/{$ThisPost['mat_cover']}")):
                    unlink("../../uploads/{$ThisPost['mat_cover']}");
                endif;

                $Upload = new Upload('../../uploads/');
                $Upload->Image($File, $PostData['mat_name'] . '-' . time(), IMAGE_W);
                if ($Upload->getResult()):
                    $PostData['mat_cover'] = $Upload->getResult();
                else:
                    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!", E_USER_WARNING);
                    echo json_encode($jSON);
                    return;
                endif;
            else:
                unset($PostData['mat_cover']);
            endif;

            $PostData['mat_status'] = (!empty($PostData['mat_status']) ? '1' : '0');
            $PostData['mat_date'] = (!empty($PostData['mat_date']) ? Check::Data($PostData['mat_date']) : date('Y-m-d H:i:s'));
            $PostData['mat_category_parent'] = (!empty($PostData['mat_category_parent']) ? implode(',', $PostData['mat_category_parent']) : null);

            $Update->ExeUpdate(DB_MATERIAIS, $PostData, "WHERE mat_id = :id", "id={$PostId}");
            $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> O post <b>{$PostData['mat_title']}</b> foi atualizado com sucesso!");
            $jSON['view'] = BASE . "/materiais#{$PostData['mat_name']}";						
            break;

//        case 'sendimage':
//            $NewImage = $_FILES['image'];
//            $Read->FullRead("SELECT mat_title, mat_name FROM " . DB_MATERIAIS . " WHERE mat_id = :id", "id={$PostData['mat_id']}");
//            if (!$Read->getResult()):
//                $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o post vinculado!", E_USER_WARNING);
//            else:
//                $Upload = new Upload('../../uploads/');
//                $Upload->Image($NewImage, $PostData['mat_id'] . '-' . time(), IMAGE_W);
//                if ($Upload->getResult()):
//                    $PostData['image'] = $Upload->getResult();
//                    $Create->ExeCreate(DB_MATERIAIS, $PostData);
//                    $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['mat_title']}' alt='{$Read->getResult()[0]['mat_title']}' src='../uploads/{$PostData['image']}'/>";
//                else:
//                    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no post!", E_USER_WARNING);
//                endif;
//            endif;
//            break;

        case 'category_add':
            $PostData = array_map('strip_tags', $PostData);
            $CatId = $PostData['category_id'];
            unset($PostData['category_id']);

            $PostData['category_name'] = Check::Name($PostData['category_title']);
            $PostData['category_parent'] = ($PostData['category_parent'] ? $PostData['category_parent'] : null);

            $Read->FullRead("SELECT category_id FROM " . DB_MATCATEGORIES . " WHERE category_name = :cn AND category_id != :ci", "cn={$PostData['category_name']}&ci={$CatId}");
            if ($Read->getResult()):
                $PostData['category_name'] = $PostData['category_name'] . '-' . $CatId;
            endif;

            $Read->FullRead("SELECT category_id FROM " . DB_MATCATEGORIES . " WHERE category_parent = :ci", "ci={$CatId}");
            if ($Read->getResult() && !empty($PostData['category_parent'])):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPPSSS: </b> {$_SESSION['userLogin']['user_name']}, uma categoria PAI (que possui subcategorias) não pode ser atribuida como subcategoria", E_USER_WARNING);
            else:
                $Update->ExeUpdate(DB_MATCATEGORIES, $PostData, "WHERE category_id = :id", "id={$CatId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> A categoria <b>{$PostData['category_title']}</b> foi atualizada com sucesso!");
            endif;
            break;

        case 'category_remove':
            $PostData['category_id'] = $PostData['del_id'];
            $Read->FullRead("SELECT category_title, category_id FROM " . DB_MATCATEGORIES . " WHERE category_parent = :cat", "cat={$PostData['category_id']}");

            if ($Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-notification'>OPPSSS: </b> Olá {$_SESSION['userLogin']['user_name']}, para deletar uma categoria certifique-se que ela não tem subcategoria cadastradas!", E_USER_WARNING);
            else:
                $Read->FullRead("SELECT mat_id FROM " . DB_MATERIAIS . " WHERE mat_category = :cat OR FIND_IN_SET(:cat, mat_category_parent)", "cat={$PostData['category_id']}");
                if ($Read->getResult()):
                    $jSON['trigger'] = AjaxErro("<b class='icon-warning'>{$Read->getRowCount()} POST(S): </b> Olá {$_SESSION['userLogin']['user_name']}, não é possível remover categorias quando existem materiais cadastrados na mesma!", E_USER_WARNING);
                else:
                    $Delete->ExeDelete(DB_MATCATEGORIES, "WHERE category_id = :cat", "cat={$PostData['category_id']}");
                    $jSON['success'] = true;
                endif;
            endif;
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
