<?php

	require_once '../../../_app/Config.inc.php';

//DEFINE O CALLBACK E RECUPERA O POST
	$jSON = null;
	$CallBack = 'Leads';
	$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
	if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack) {
		//PREPARA OS DADOS
		$Case = $PostData['callback_action'];
		unset($PostData['callback'], $PostData['callback_action']);

		//ELIMINA CÓDIGOS
		$PostData = array_map('strip_tags', $PostData);

		//SELECIONA AÇÃO
		switch ($Case) {
			//EXECURA DE ACORDO COM CALLBACK-ACTION
			case 'newsletter':
				if (in_array('', $PostData)) {
					$jSON['trigger'] = AjaxErro('<b>OOPS! </b> Informe seu e-mail por favor!', E_USER_NOTICE);
				} else {
					if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
						$jSON['trigger'] = AjaxErro('<b>OOPS! </b> E-mail informado não é válido!', E_USER_NOTICE);
					} else {
						$LeadData = [
							'lead_name' => null,
							'lead_email' => $PostData['email'],
							'lead_conversion' => $Case
						];

						$Create = new Create;
						$Create->ExeCreate(DB_LEADS, $LeadData);

						$jSON['trigger'] = AjaxErro("<b>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
						//$jSON['redirect'] = 'dashboard.php?wc=home';
					}
				}
				break;
			case 'contact':

				if (in_array('', $PostData)) {
					$jSON['trigger'] = AjaxErro('<b class="fal fa-info-circle"> OOPS! </b> Preencha todos os campos e adicione um anexo em PDF!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
					$jSON['trigger'] = AjaxErro('<b class="fal fa-envelope"> OOPS! </b> E-mail informado não é válido!',
						E_USER_ERROR);

				} elseif (empty($_FILES['cv_pdf']) || $_FILES['cv_pdf']['type'] != 'application/pdf') {
					$jSON['trigger'] = AjaxErro("<b class='fal fa-file-pdf'> ERRO AO ENVIAR ARQUIVO:</b> Selecione um arquivo em PDF para anexar com seu Currículo.",
						E_USER_ERROR);

				} else {
					$PostData['full_name'] = Check::getCapilalize($PostData['full_name']);
					$phoneMask = $PostData['phone'];
					$PostData['phone'] = Check::clearNumber($PostData['phone']);
					$PostData['email'] = mb_strtolower($PostData['email']);
					$PostData['privacy'] = $PostData['privacy'] ? 1 : 0;
					$PostData['status'] = 1;

					//UPLOAD PDF
					if (!empty($_FILES['cv_pdf'])) {
						$File = $_FILES['cv_pdf'];

						$Read = new Read();
						$Read->ExeRead(DB_CV, " WHERE email = :email ORDER BY id DESC LIMIT :limit",
							"email={$PostData['email']}&limit=1");

						if ($Read->getResult()) {
							$ThisCandidate = $Read->getResult()[0];
							if ($ThisCandidate['cv_pdf'] &&
								file_exists("../../../uploads/{$ThisCandidate['cv_pdf']}")
								&& !is_dir("../../../uploads/{$ThisCandidate['cv_pdf']}")) {
								unlink("../../../uploads/{$ThisCandidate['cv_pdf']}");
							}

							$Upload = new Upload(__DIR__ . '/../../../uploads/');
							$Upload->File($File, check::Name($PostData['full_name']) . '-cv', 'curriculos',
								'5');

							if ($Upload->getResult()) {
								$PostData['cv_pdf'] = $Upload->getResult();

							} else {
								$jSON['trigger'] = AjaxErro("<b class='fal fa-file-pdf'> ERRO AO ENVIAR ARQUIVO:</b> Olá, selecione um arquivo em PDF para anexar.",
									E_USER_WARNING);
								echo json_encode($jSON);
								return;
							}

							$Update = new Update();
							$Update->ExeUpdate(DB_CV, $PostData, "WHERE email = :email", "email={$PostData['email']}");

							if ($Update->getResult()) {
								$jSON['trigger'] = AjaxErro("<b class='fal fa-user-astronaut'> Currículo atualizado com sucesso!</b>");
							}

						} else {
							$Upload = new Upload('../../../uploads/');
							$Upload->File($File, check::Name($PostData['full_name']) . '-cv', 'curriculos',
								'5');

							if ($Upload->getResult()) {
								$PostData['cv_pdf'] = $Upload->getResult();
							} else {
								$jSON['trigger'] = AjaxErro("<b class='fal fa-file-pdf'> ERRO AO ARQUIVO PDF</b> Olá, selecione um arquivo em PDF para anexar.",
									E_USER_WARNING);
								echo json_encode($jSON);
								return;
							}

							$Create = new Create();
							$Create->ExeCreate(DB_CV, $PostData);

							if ($Create->getResult()) {
								$jSON['trigger'] = AjaxErro("<b class='fal fa-check'> Currículo cadastrado com sucesso!</b>");
							}
						}

						$anexo = __DIR__ . '/../../../uploads/' . $PostData['cv_pdf'];

						$arrayData = $PostData;
						$arrayData['INCLUDE_PATH'] = INCLUDE_PATH;
						$arrayData['SITE_NAME'] = SITE_NAME;
						$arrayData['SITE_ADDR_NAME'] = SITE_ADDR_NAME;
						$arrayData['SITE_ADDR_PHONE_A'] = SITE_ADDR_PHONE_A;
						$arrayData['SITE_ADDR_EMAIL'] = 'curriculo@travi.com.br';
						$arrayData['SITE_ADDR_SITE'] = SITE_ADDR_SITE;
						$arrayData['BASE'] = BASE;
						$arrayData['phone_mask'] = $phoneMask;
						$arrayData['saudacao'] = Check::Salutation();

						require_once __DIR__ . './../_app_capture/class/Template.class.php';

						$MailContent = Template::setTemplate(Template::getTemplate('curriculum_mail.html',
							__DIR__ . '/../templates/'),
							$arrayData);

						$Email = new Email();
						$Email->addFile($anexo);
						$Email->EnviarMontando(
							"{$PostData['area']}: Novo currículo recebido",
							$MailContent,
							$PostData['full_name'],
							$PostData['email'],
							SITE_ADDR_NAME,
							'curriculo@travi.com.br');

						if (!$Email->getError()) {
							$MailConfirmation = Template::setTemplate(Template::getTemplate('curriculum_return_mail.html',
								__DIR__ . '/../templates/'), $arrayData);

							$ResponseEmail = new Email();

							$ResponseEmail->EnviarMontando(
								'Confirmação de recebimento',
								$MailConfirmation,
								MAIL_SENDER,
								MAIL_USER,
								$PostData['full_name'],
								$PostData['email']);

						} else {
							$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
								E_USER_ERROR);
						}

					} else {
						unset($PostData['cv_pdf']);
					}


					//PHOTO UPLOAD
					/*   if (!empty($_FILES['candidate_photo'])):
						   $File = $_FILES['candidate_photo'];

						   if ($Work['candidate_photo'] && file_exists("../../uploads/{$Work['candidate_photo']}") && !is_dir("../../uploads/{$Work['candidate_photo']}")):
							   unlink("../../uploads/{$Work['candidate_photo']}");
						   endif;

						   $Upload->Image($File, "{$PostData['candidate_name']}", 400);
						   if ($Upload->getResult()):
							   $PostData['candidate_photo'] = $Upload->getResult();
						   else:
							   $jSON['trigger'] = AjaxErro(
								   "<b class='icon-image'>ERRO AO ENVIAR FOTO:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG dE 300X400 para ENVIAR!",
								   E_USER_WARNING
							   );
							   echo json_encode($jSON);
							   return;
						   endif;
					   endif;*/

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
