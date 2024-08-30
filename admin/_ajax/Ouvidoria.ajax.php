<?php

	session_start();
	require '../../_app/Config.inc.php';
	$NivelAcess = 6;

	if (!APP_CV || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
		$jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
		echo json_encode($jSON);
		die;
	endif;

	usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
	$jSON = null;
	$CallBack = 'Ouvidoria';
	$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
	if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):
		//PREPARA OS DADOS
		$Case = $PostData['callback_action'];
		unset($PostData['callback'], $PostData['callback_action']);

		$Read = new Read();
		$Delete = new Delete();

		//SELECIONA AÇÃO
		switch ($Case):
			//DELETE
			case 'delete':
				$PostData['id'] = $PostData['del_id'];
				$Delete->ExeDelete(DB_OUVIDORIA, "WHERE id = :id", "id={$PostData['id']}");

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
