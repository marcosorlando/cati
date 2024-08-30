<?php

session_start();
require '../../_app/Config.inc.php';
$NivelAcess = 6;

if (!APP_VIDEOS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
	$jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
	echo json_encode($jSON);
	die;
endif;

usleep(50000);

//DEFINE O CALLBACK E RECUPERA O VÍDEO
$jSON = null;
$CallBack = 'Videos';
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
		//GERENCIA
		case 'manager':
			$VideoId = $PostData['video_id'];
			$VideoLink = $PostData['video_link'];	
			$VideoEnd = (!empty($PostData['video_end']) ? $PostData['video_end'] : null);
			$Image = (!empty($_FILES['video_image']) ? $_FILES['video_image'] : null);
			unset($PostData['video_id'], $PostData['video_end'], $PostData['video_image']);

			//$Read->FullRead("SELECT video_image FROM " . DB_YOUTUBE . " WHERE video_id = :id", "id={$VideoId}");

//			if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['video_image'])):
//				$jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Favor envie uma capa de vídeo nas medidas de ' . SLIDE_W . 'x' . SLIDE_H . 'px!', E_USER_ERROR);
//			else
				if (in_array('', $PostData)):
				$jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o vídeo, favor preencha todos os campos!', E_USER_ERROR);
				$jSON['error'] = true;
			else:
				$PostData['video_date'] = date('Y-m-d H:i:s');
				$PostData['video_start'] = Check::Data($PostData['video_start']);
				$PostData['video_end'] = (!empty($VideoEnd) ? Check::Data($VideoEnd) : null);

				if (!empty($Image)):
					if ($Read->getResult() && !empty($Read->getResult()[0]['video_image']) && file_exists("../../uploads/{$Read->getResult()[0]['video_image']}") && !is_dir("../../uploads/{$Read->getResult()[0]['video_image']}")):
						unlink("../../uploads/{$Read->getResult()[0]['video_image']}");
					endif;
					$Upload = new Upload('../../uploads/');
					$Upload->Image($Image, Check::Name($PostData['video_title']), SLIDE_W, 'videos');
					$PostData['video_image'] = $Upload->getResult();
				endif;

				$Update->ExeUpdate(DB_YOUTUBE, $PostData, "WHERE video_id = :id", "id={$VideoId}");
				$jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O vídeo foi atualizado com sucesso. E sera exibido nas datas cadastradas!");
			endif;
			break;

		//DELETA
		case 'delete':
			$VideoId = $PostData['del_id'];
			$Read->FullRead("SELECT video_image FROM " . DB_YOUTUBE . " WHERE video_id = :id", "id={$VideoId}");
			if ($Read->getResult()):
				$VideoImage = (!empty($Read->getResult()[0]['video_image']) ? $Read->getResult()[0]['video_image'] : null);
				if ($VideoImage && file_exists("../../uploads/{$VideoImage}") && !is_dir("../../uploads/{$VideoImage}")):
					unlink("../../uploads/{$VideoImage}");
				endif;
			endif;

			$Delete->ExeDelete(DB_YOUTUBE, "WHERE video_id = :id", "id={$VideoId}");
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
