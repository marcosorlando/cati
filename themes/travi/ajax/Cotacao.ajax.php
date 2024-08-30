<?php
//DEFINE O CALLBACK E RECUPERA O POST
    require_once '../../../_app/Config.inc.php';
    
    $jSON = null;
    $CallBack = 'Cotacao';
    $PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
    if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack) {
        //PREPARA OS DADOS
        $Case = $PostData['callback_action'];
        unset($PostData['callback'], $PostData['callback_action']);
        
        //ELIMINA CÓDIGOS
        $PostData = array_map('strip_tags', $PostData);
        
        //var_dump($PostData);
        
        //SELECIONA AÇÃO
        switch ($Case) {
            //CAPTURA DE ACORDO COM CALLBACK-ACTION
            case 'cotacao':
                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
                } else {
                    if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
                        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    } else {
                        
                        
                        
                        $Create = new Create;
                        $Create->ExeCreate(DB_LEADS, $PostData);
                        
                        $jSON['trigger'] = AjaxErro("<b>Obrigado!</b> Sua contação foi recebida com Sucesso!");
                        //$jSON['redirect'] = 'dashboard.php?wc=home';
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

