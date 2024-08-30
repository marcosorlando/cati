<?php

//DEFINE O CALLBACK E RECUPERA O POST
	require_once '../../../_app/Config.inc.php';

	$jSON = null;
	$CallBack = 'Ouvidoria';
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
			//CAPTURA DE ACORDO COM CALLBACK-ACTION
			case 'manager':

				if (array_search('', $PostData) &&
					array_search('', $PostData) != 'first_name' &&
					array_search('', $PostData) != 'last_name'

				) {
					$jSON['trigger'] = AjaxErro('<b class="fal fa-info-circle"> OOPS! </b> Preencha todos os campos obrigatórios e Envie novamente!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
					$jSON['trigger'] = AjaxErro('<b class="fal fa-envelope"> OOPS! </b> E-mail informado não é válido!',
						E_USER_ERROR);

				} else {
					$PostData['first_name'] = Check::getCapilalize($PostData['first_name']);
					$PostData['last_name'] = Check::getCapilalize($PostData['last_name']);
					$PostData['email'] = mb_strtolower($PostData['email']);
					$PostData['complaint'] = Check::getCapilalize($PostData['complaint']);
					$PostData['privacy'] = $PostData['privacy'] ? 1 : 0;
					$PostData['status'] = 1;

					/*REGISTRA NO BANCO DE DADOS*/
					$Create = new Create();
					$Create->ExeCreate(DB_OUVIDORIA, $PostData);

					if ($Create->getResult()) {
						$jSON['trigger'] = AjaxErro("<b class='fal fa-check'> Registro enviado com sucesso!</b>");
					}

					$PostData['full_name'] = !empty($PostData['first_name']) ? "{$PostData['first_name']} {$PostData['last_name']}" : 'ANÔNIMO(A)';

					/*ENVIA E-MAILS PARA 2 PONTAS*/
					$arrayData = $PostData;
					$arrayData['INCLUDE_PATH'] = INCLUDE_PATH;
					$arrayData['SITE_NAME'] = SITE_NAME;
					$arrayData['SITE_ADDR_NAME'] = SITE_ADDR_NAME;
					$arrayData['SITE_ADDR_PHONE_A'] = SITE_ADDR_PHONE_A;
					$arrayData['SITE_ADDR_SITE'] = SITE_ADDR_SITE;
					$arrayData['BASE'] = BASE;
					$arrayData['saudacao'] = Check::Salutation();

					if ($PostData['sector'] == 'DENÚNCIA') {
						$arrayData['SITE_ADDR_EMAIL'] = 'denuncia@travi.com.br';
					} else {
						$arrayData['SITE_ADDR_EMAIL'] = 'ouvidoria@travi.com.br';
					}

					require_once __DIR__ . './../_app_capture/class/Template.class.php';

					$MailContent = Template::setTemplate(Template::getTemplate('ouvidoria_mail.html',
						__DIR__ . '/../templates/'),
						$arrayData);

					$Email = new Email();
					//$Email->addFile($anexo);
					$Email->EnviarMontando(
						"Nova {$PostData['sector']} recebida",
						$MailContent,
						$PostData['full_name'],
						$PostData['email'],
						SITE_ADDR_NAME,
						$arrayData['SITE_ADDR_EMAIL']);

					if (!$Email->getError()) {
						$MailConfirmation = Template::setTemplate(Template::getTemplate('ouvidoria_return_mail.html',
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
						$jSON['trigger'] = AjaxErro("Desculpe, não foi possível enviar sua {$PostData['sector']}. Entre em contato via E-mail: " . $arrayData['SITE_ADDR_EMAIL'],
							E_USER_ERROR);
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
