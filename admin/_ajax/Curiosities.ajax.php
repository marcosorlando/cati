<?php

	session_start();
	require '../../_app/Config.inc.php';
	$NivelAcess = LEVEL_WC_CURIOSITIES;

	if (!APP_CURIOSITIES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
		$jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
			E_USER_ERROR);
		echo json_encode($jSON);
		die;
	}

	usleep(50000);

	//DEFINE O CALLBACK E RECUPERA O POST
	$jSON = null;
	$CallBack = 'Curiosities';
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
		$Upload = new Upload('../../uploads/');

		//SELECIONA AÇÃO
		switch ($Case) {
			case 'manager':

				$CurId = $PostData['cur_id'];
				$PostData['cur_status'] = (!empty($PostData['cur_status']) ? '1' : '0');

				$Read->ExeRead(DB_CURIOSITIES, "WHERE cur_id = :id", "id={$CurId}");

				if (!$Read->getResult()) {
					$jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar as curiosidades. Experimente atualizar a página!",
						E_USER_WARNING);
				} else {
					$Cur = $Read->getResult()[0];
					unset(
						$PostData['cur_id'],
						$PostData['cur_cover'],
						$PostData['cur_line_one_icon'],
						$PostData['cur_line_two_icon'],
						$PostData['cur_line_three_icon'],
						$PostData['cur_line_four_icon']
					);

					//COVER UPLOAD
					if (!empty($_FILES['cur_cover'])) {
						$File = $_FILES['cur_cover'];

						if ($Cur['cur_cover'] && file_exists("../../uploads/{$Cur['cur_cover']}") && !is_dir("../../uploads/{$Cur['cur_cover']}")) {
							unlink("../../uploads/{$Cur['cur_cover']}");
						}

						$Upload->Image($File, Check::Name($PostData['cur_title']) . time(), 950);
						if ($Upload->getResult()) {
							$PostData['cur_cover'] = $Upload->getResult();
						} else {
							$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 950x1033px para a capa!",
								E_USER_WARNING);
							echo json_encode($jSON);
							return;
						}
					}

					//ICONE 1 UPLOAD
					if (!empty($_FILES['cur_line_one_icon'])) {
						$File = $_FILES['cur_line_one_icon'];

						if ($Cur['cur_line_one_icon'] && file_exists("../../uploads/{$Cur['cur_line_one_icon']}") && !is_dir("../../uploads/{$Cur['cur_line_one_icon']}")) {
							unlink("../../uploads/{$Cur['cur_line_one_icon']}");
						}

						$Upload->Image($File, Check::Name($PostData['cur_line_one_label']), 100);
						if ($Upload->getResult()) {
							$PostData['cur_line_one_icon'] = $Upload->getResult();
						} else {
							$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR ÍCONE 1:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem PNG de 100x100px.",
								E_USER_WARNING);
							echo json_encode($jSON);
							return;
						}
					}

					//ICONE 2 UPLOAD
					if (!empty($_FILES['cur_line_two_icon'])) {
						$File = $_FILES['cur_line_two_icon'];

						if ($Cur['cur_line_two_icon'] && file_exists("../../uploads/{$Cur['cur_line_two_icon']}") && !is_dir("../../uploads/{$Cur['cur_line_two_icon']}")) {
							unlink("../../uploads/{$Cur['cur_line_two_icon']}");
						}

						$Upload->Image($File, Check::Name($PostData['cur_line_two_label']), 100);
						if ($Upload->getResult()) {
							$PostData['cur_line_two_icon'] = $Upload->getResult();
						} else {
							$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR ÍCONE 2:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem PNG de 100x100px.",
								E_USER_WARNING);
							echo json_encode($jSON);
							return;
						}
					}

					//ICONE 3 UPLOAD
					if (!empty($_FILES['cur_line_three_icon'])) {
						$File = $_FILES['cur_line_three_icon'];

						if ($Cur['cur_line_three_icon'] && file_exists("../../uploads/{$Cur['cur_line_three_icon']}") && !is_dir("../../uploads/{$Cur['cur_line_three_icon']}")) {
							unlink("../../uploads/{$Cur['cur_line_three_icon']}");
						}

						$Upload->Image($File, Check::Name($PostData['cur_line_three_label']), 100);
						if ($Upload->getResult()) {
							$PostData['cur_line_three_icon'] = $Upload->getResult();
						} else {
							$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR ÍCONE 3:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem PNG de 100x100px.",
								E_USER_WARNING);
							echo json_encode($jSON);
							return;
						}
					}

					//ICONE 4 UPLOAD
					if (!empty($_FILES['cur_line_four_icon'])) {
						$File = $_FILES['cur_line_four_icon'];

						if ($Cur['cur_line_four_icon'] && file_exists("../../uploads/{$Cur['cur_line_four_icon']}") && !is_dir("../../uploads/{$Cur['cur_line_four_icon']}")) {
							unlink("../../uploads/{$Cur['cur_line_four_icon']}");
						}

						$Upload->Image($File, Check::Name($PostData['cur_line_four_label']), 100);
						if ($Upload->getResult()) {
							$PostData['cur_line_four_icon'] = $Upload->getResult();
						} else {
							$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR ÍCONE 4:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem PNG de 100x100px.",
								E_USER_WARNING);
							echo json_encode($jSON);
							return;
						}
					}

					$Update->ExeUpdate(DB_CURIOSITIES, $PostData, "WHERE cur_id = :id", "id={$CurId}");

					$jSON['trigger'] = AjaxErro("<span class='icon-checkmark'><b>CURIOSIDADES ATUALIZADAS:</b> Olá {$_SESSION['userLogin']['user_name']}. Os dados de {$PostData['cur_title']} foi atualizado com sucesso!<span>");
				}
				break;

			case 'delete':
				$CurId = $PostData['del_id'];

				$Read->ExeRead(DB_CURIOSITIES, "WHERE cur_id = :id", "id={$CurId}");

				if (!$Read->getResult()) {
					$jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar, pois o esse dado não existe ou foi removido recentemente!",
						E_USER_WARNING);
				} else {
					$Cur = $Read->getResult()[0];
					$CurCover = "../../uploads/{$Cur['cur_cover']}";
					$iconOne = "../../uploads/{$Cur['cur_line_one_icon']}";
					$iconTwo = "../../uploads/{$Cur['cur_line_two_icon']}";
					$iconThree = "../../uploads/{$Cur['cur_line_three_icon']}";
					$iconFour = "../../uploads/{$Cur['cur_line_four_icon']}";

					if (file_exists($CurCover) && !is_dir($CurCover)) unlink($CurCover);
					if (file_exists($iconOne) && !is_dir($iconOne)) unlink($iconOne);
					if (file_exists($iconTwo) && !is_dir($iconTwo)) unlink($iconTwo);
					if (file_exists($iconThree) && !is_dir($iconThree)) unlink($iconThree);
					if (file_exists($iconFour) && !is_dir($iconFour)) unlink($iconFour);

					$Delete->ExeDelete(DB_CURIOSITIES, "WHERE cur_id = :id", "id={$Cur['cur_id']}");
					$jSON['success'] = true;
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
