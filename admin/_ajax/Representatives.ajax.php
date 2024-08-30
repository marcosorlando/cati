<?php

    session_start();
    require '../../_app/Config.inc.php';
    $NivelAcess = LEVEL_WC_REPRESENTATIVES;

    if (!APP_REPRESENTATIVES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
        echo json_encode($jSON);
        die;
    }
    usleep(50000);

    //DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Representatives';
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

        //SELECIONA AÇÃO
        switch ($Case) {
            //GERENCIA
            case 'manager':
                $RepresentativeId = $PostData['rep_id'];

                $Image = (!empty($_FILES['rep_image']) ? $_FILES['rep_image'] : null);
                unset($PostData['rep_id'], $PostData['rep_image']);

                $Read->FullRead("SELECT rep_image, rep_city FROM " . DB_REPRESENTATIVES . " WHERE rep_id = :id", "id={$RepresentativeId}");

                if ($PostData['rep_uf']) {
                    $jSON['city'] = null;

                    $Read->FullRead("SELECT id, name, uf FROM " . DB_CITIES . " WHERE uf = :uf", "uf={$PostData['rep_uf']}");

                    if ($Read->getResult()) {

                        $jSON['city'] .= "<option value = '0'>- Selecione a cidade -</option> ";

                        foreach ($Read->getResult() as $Cities) {
                            extract($Cities);
                            $selected = (!empty($PostData['rep_city']) && $name == $PostData['rep_city'] ? 'selected' : '');
                            $jSON['city'] .= "<option value='{$name}' {$selected}>{$name}</option>";

                        }

                    }
                }

                $PostData['rep_all_cities'] = (!empty($PostData['rep_all_cities']) ? '1' : '0');

                if ($PostData['rep_all_cities'] == 1) {
                    $jSON['allcities'] = null;

                    $Read->FullRead("SELECT name FROM " . DB_CITIES . " WHERE uf = :uf", "uf={$PostData['rep_uf']}");

                    if ($Read->getResult()) {
                         foreach ($Read->getResult() as $Cities) {
                            extract($Cities);
                             $jSON['allcities'] .= "{$name}, ";
                        }
                    }
                }

                $PostData["rep_cities"] = $PostData["rep_cities"] ? $PostData["rep_cities"] : "Não esquecer de inserir as cidades separadas por virgula AQUI!";

                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o representante, favor preencha todos os campos!', E_USER_ERROR);
                    $jSON['error'] = true;
                } else {
                    $PostData['rep_created'] = date('Y-m-d H:i:s');

                    if (!empty($Image)) {
                        if ($Read->getResult() && !empty($Read->getResult()[0]['rep_image']) && file_exists("../../uploads/representatives/{$Read->getResult()[0]['rep_image']}") && !is_dir("../../uploads/representatives/{$Read->getResult()[0]['rep_image']}")) {
                            unlink("../../uploads/representatives/{$Read->getResult()[0]['rep_image']}");
                        }
                        $Upload = new Upload('../../uploads/');
                        $Upload->Image($Image, Check::Name($PostData['rep_name']), SLIDE_W, 'representatives');
                        $PostData['rep_image'] = $Upload->getResult();
                    }
                    $Update->ExeUpdate(DB_REPRESENTATIVES, $PostData, "WHERE rep_id = :id", "id={$RepresentativeId}");

                    $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O representante foi atualizado com sucesso.");
                }
                break;

            //DELETA
            case 'delete':
                $RepresentativeId = $PostData['del_id'];
                $Read->FullRead("SELECT rep_image FROM " . DB_REPRESENTATIVES . " WHERE rep_id = :id", "id={$RepresentativeId}");
                if ($Read->getResult()) {
                    $RepresentativeImage = (!empty($Read->getResult()[0]['rep_image']) ? $Read->getResult()[0]['rep_image'] : null);
                    if ($RepresentativeImage && file_exists("../../uploads/representatives/{$RepresentativeImage}") && !is_dir("../../uploads/representatives/{$RepresentativeImage}")) {
                        unlink("../../uploads/representatives/{$RepresentativeImage}");
                    }
                }

                $Delete->ExeDelete(DB_REPRESENTATIVES, "WHERE rep_id = :id", "id={$RepresentativeId}");
                $jSON['success'] = true;
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