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
$jSON = NULL;
$CallBack = 'Leads';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):
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

  //ELIMINA CÓDIGOS
  $PostData = array_map('strip_tags', $PostData);

  //SELECIONA AÇÃO
  switch ($Case):
    //DELETE
    case 'delete':
      $Read->FullRead("SELECT lead_thumb FROM ". DB_LEADS . " WHERE lead_id = :id" , "id={$PostData['key']}");
      
      if($Read->getResult()):
        unlink("../../uploads/leads/{$Read->getResult()[0]['lead_thumb']}");        
      endif;
      
      $Delete->ExeDelete(DB_LEADS, "WHERE lead_id = :id", "id={$PostData['key']}");
      $jSON['redirect'] = 'dashboard.php?wc=leads/home';
      $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Lead Removido:</b>Esse LEAD foi removido com sucesso!");

      break;

    //CAPTURA DE ACORDO COM CALLBACK-ACTION
    case 'manage':
      if (in_array('', $PostData) && $PostData['lead_thumb']):
        $jSON['trigger'] = AjaxErro('<strong>OPPSSS:</strong> Favor preencha todos os campos!', E_USER_NOTICE);

      elseif (
              !Check::Email($PostData['lead_email']) || !filter_var($PostData['lead_email'], FILTER_VALIDATE_EMAIL)):
        $jSON['trigger'] = AjaxErro('<strong>OPPSSS:  </strong>' . $PostData['lead_name'] . ' o e-mail informado não é válido!', E_USER_NOTICE);

      else:       

        $LeadId = $PostData['lead_id'];
        unset($PostData['lead_id']);

        $PostData['lead_status'] = (!empty($PostData['lead_status']) ? '1' : '0');
        //$PostData['lead_name'] = (!empty($PostData['lead_name']) ? Check::Name($PostData['lead_name']) : Check::Name($PostData['lead_title']));

        $Read->ExeRead(DB_LEADS, "WHERE lead_id= :id", "id={$LeadId}");
        $ThisPage = $Read->getResult()[0];

        //UPLOAD AVATAR-IMAGE
        if (!empty($_FILES['lead_thumb'])):
          $File = $_FILES['lead_thumb'];

          if ($ThisPage['lead_thumb'] && file_exists("../../uploads/leads/{$ThisPage['lead_thumb']}") && !is_dir("../../uploads/leads/{$ThisPage['lead_thumb']}")):
            unlink("../../uploads/leads/{$ThisPage['lead_thumb']}");
          endif;

          $Upload = new Upload('../../uploads/leads/');
          $Upload->Image($File, $PostData['lead_name'], AVATAR_W);

          if ($Upload->getResult()):
            $PostData['lead_thumb'] = $Upload->getResult();
          else:
            $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR FOTO:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como foto!", E_USER_WARNING);
            echo json_encode($jSON);
            return;
          endif;
        else:
          unset($PostData['lead_thumb']);
        endif;

        $Update->ExeUpdate(DB_LEADS, $PostData, "WHERE lead_id = :id", "id={$LeadId}");
        $jSON['trigger'] = AjaxErro("<strong>OK!</strong> Lead atualizado com sucesso!");
      //$jSON['redirect'] = $Link;
      //endif;
      endif;
      break;
  endswitch;

  //RETORNA O CALLBACK
  if ($jSON):
    echo json_encode($jSON);
  else:
    $jSON['trigger'] = AjaxErro('<strong class="icon-warning">OPSS:</strong> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
    echo json_encode($jSON);
  endif;
else:
  //ACESSO DIRETO
  die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    endif;
