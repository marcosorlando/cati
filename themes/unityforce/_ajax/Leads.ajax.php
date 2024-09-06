<?php

//DEFINE O CALLBACK E RECUPERA O POST
	require_once '../../../_app/Config.inc.php';
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

		$Read = new Read();

		//SELECIONA AÇÃO
		switch ($Case) {
			//CAPTURA DE ACORDO COM CALLBACK-ACTION
			case 'Newsletter':
				if (in_array('', $PostData)) {
					$jSON['trigger'] = AjaxErro('<b class="icon-mail"> OPPSSS:</b> Informe seu e-mail por favor!',
						E_USER_NOTICE);
				} else {
					if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
						$jSON['trigger'] = AjaxErro('<b class="icon-warning"> OPPSSS:</b> E-mail informado não é válido!',
							E_USER_ERROR);
					} else {
						$LeadData = [
							'lead_name' => ' ',
							'lead_email' => $PostData['email'],
							'lead_conversion' => $Case
						];
						$Read->FullRead("SELECT lead_email FROM " . DB_LEADS . " WHERE lead_email = :mail",
							"mail={$LeadData['lead_email']}");
						if ($Read->getResult()) {
							$jSON['trigger'] = AjaxErro("<b class='icon-notification'> {$LeadData['lead_name']}.</b> Seu e-mail já está registrado em nossa Newsletter!",
								E_USER_NOTICE);
						} else {
							$Create = new Create;
							$LeadData = array_map('strip_tags', $LeadData);
							$Create->ExeCreate(DB_LEADS, $LeadData);
							$jSON['trigger'] = AjaxErro("<b class='icon-happy'>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
						}
					}
				}
				break;

			case 'news2':
				if (in_array('', $PostData)) {
					$jSON['trigger'] = AjaxErro('<b>OOPS! </b> Informe seu e-mail por favor!', E_USER_NOTICE);
				} else {
					if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
						$jSON['trigger'] = AjaxErro('<b>OOPS! </b> E-mail informado não é válido!', E_USER_NOTICE);
					} else {
						$LeadData = [
							'lead_name' => $PostData['name'],
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

				if (array_search('', $PostData)) {
					$jSON['field'] = array_search('', $PostData);
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> Preencha todos os campos obrigatórios!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['email']) || !filter_var($PostData['email'],
						FILTER_VALIDATE_EMAIL)) {
					$jSON['field'] = 'email';
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> E-mail informado não é válido!',
						E_USER_ERROR);

				} elseif (!isset($PostData['privacy'])) {
					$jSON['field'] = 'privacy';
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> Você precisa estar em consentimento com nossa Política de Privacidade.',
						E_USER_ERROR);

				} else {
					$PostData['name'] = Check::Capitalise($PostData['name']);
					$phoneMask = $PostData['phone'];
					$PostData['phone'] = Check::clearNumber($PostData['phone']);
					$PostData['email'] = mb_strtolower($PostData['email']);
					$PostData['privacy'] = isset($PostData['privacy']) ? 1 : 0;

					/*$Create = new Create();
					$Create->ExeCreate(DB_VOLUNTEERS, $PostData);

					if ($Create->getResult()) {
						$jSON['trigger'] = AjaxErro("<b> Recebemos sua mensagem com sucesso!</b>");
						$jSON['redirect'] = BASE."/obrigado-contato";
					}*/

					//$voluntaryCity = $Read->LinkResult(DB_CITIES, 'id', $PostData['voluntary_city'], 'name');
					//$eventCity = $Read->LinkResult(DB_CITIES, 'id', $PostData['city_id'], 'name');
					$logoMail = $Read->LinkResult(DB_TEMPLATE_LOGOS, 'logo_id', 1, 'logo_mail');

					$arrayData = $PostData;
					$arrayData['INCLUDE_PATH'] = INCLUDE_PATH;
					$arrayData['SITE_NAME'] = SITE_NAME;
					$arrayData['SITE_ADDR_NAME'] = SITE_ADDR_NAME;
					$arrayData['SITE_ADDR_PHONE_A'] = SITE_ADDR_PHONE_A;
					$arrayData['SITE_ADDR_EMAIL'] = 'contato@ellalidera2024.com.br';
					$arrayData['SITE_ADDR_SITE'] = SITE_ADDR_SITE;
					$arrayData['BASE'] = BASE;
					$arrayData['phone_mask'] = $phoneMask;
					//$arrayData['voluntary_city'] = $voluntaryCity['name'];
					//$arrayData['event_city'] = $eventCity['name'];
					$arrayData['logo_mail'] = BASE . "/uploads/{$logoMail['logo_mail']}";

					require_once __DIR__ . './../_app_capture/class/Template.class.php';

					$MailContent = Template::setTemplate(Template::getTemplate('contact_mail.html',
						__DIR__ . '/../templates/'), $arrayData);

					$Email = new Email();
					$Email->EnviarMontando(
						"{$PostData['subject']}",
						$MailContent,
						$PostData['name'],
						$PostData['email'],
						SITE_ADDR_NAME,
						$arrayData['SITE_ADDR_EMAIL']
					);

					if (!$Email->getError()) {
						$MailConfirmation = Template::setTemplate(Template::getTemplate('contact_return_mail.html',
							__DIR__ . '/../templates/'), $arrayData);

						$ResponseEmail = new Email();
						$ResponseEmail->EnviarMontando(
							'Confirmação de recebimento Ella Lidera ' . date('Y'),
							$MailConfirmation,
							MAIL_SENDER,
							MAIL_USER,
							$PostData['name'],
							$PostData['email']
						);

						$jSON['trigger'] = AjaxErro("<b> Recebemos sua mensagem com sucesso!</b>");
						$jSON['redirect'] = BASE . "/obrigado-contato";

					} else {
						$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
							E_USER_ERROR);
					}
				}

				break;

			case 'redirectcity':
				$jSON['redirect'] = BASE . '/cidade/' . $PostData['key'];
				break;

			case 'loadcities':
				$Read->FullRead("SELECT id, name, uf FROM " . DB_CITIES . " WHERE uf = :uf ORDER BY name ASC",
					"uf={$PostData['key']}");

				$jSON['content'] = "<option value='' disabled selected>- Selecione a cidade:</option>";
				if ($Read->getResult()) {
					$jSON['target'] = "#city_id";
					foreach ($Read->getResult() as $Cities) {
						extract($Cities);
						$jSON['content'] .= "<option value='{$id}'>{$name} - {$uf}</option>";
					}
				}
				break;

			case 'contato':

				if (array_search('', $PostData)) {
					$jSON['field'] = array_search('', $PostData);
					$jSON['trigger'] = AjaxErro('<b>Oops!</b> Preencha todos os campos obrigatórios!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['contact_email']) || !filter_var($PostData['contact_email'],
						FILTER_VALIDATE_EMAIL)) {
					$jSON['field'] = 'contact_email';
					$jSON['trigger'] = AjaxErro('<b>Oops!</b> E-mail informado não é válido!',
						E_USER_ERROR);

				} elseif (!isset($PostData['privacy'])) {
					$jSON['field'] = 'privacy';
					$jSON['trigger'] = AjaxErro('<b>Oops!</b> Você precisa estar em consentimento com nossa Política de Privacidade.',
						E_USER_ERROR);

				} else {

					$PostData['contact_name'] = Check::getCapilalize($PostData['contact_name']);
					$phoneMask = $PostData['contact_phone'];
					$PostData['contact_phone'] = Check::clearNumber($PostData['contact_phone']);
					$PostData['contact_cep'] = preg_replace('/[\D]/', '', $PostData['contact_cep']);
					$PostData['contact_email'] = mb_strtolower($PostData['contact_email']);
					$PostData['privacy'] = isset($PostData['privacy']) ? 1 : 0;
					$PostData['status'] = 0;

					$Create = new Create();
					$Create->ExeCreate(DB_CONTACTS, $PostData);

					if ($Create->getResult()) {
						$jSON['trigger'] = AjaxErro("<p>Obrigado! <b>{$PostData['contact_name']}</b>, seu registro foi recebido com sucesso!</p>");
						$jSON['clear'] = true;
					}

					$arrayData = $PostData;
					$arrayData['INCLUDE_PATH'] = INCLUDE_PATH;
					$arrayData['SITE_NAME'] = SITE_NAME;
					$arrayData['SITE_ADDR_NAME'] = SITE_ADDR_NAME;
					$arrayData['SITE_ADDR_PHONE_A'] = SITE_ADDR_PHONE_A;
					$arrayData['SITE_ADDR_EMAIL'] = SITE_ADDR_EMAIL;
					$arrayData['SITE_ADDR_SITE'] = SITE_ADDR_SITE;
					$arrayData['BASE'] = BASE;
					$arrayData['phone_mask'] = $phoneMask;
					$arrayData['logo_mail'] = BASE .'/uploads/mail/logo.png';;

					require_once __DIR__ . './../_app_capture/class/Template.class.php';

					$MailContent = Template::setTemplate(Template::getTemplate('contact_mail.html',
						__DIR__ . '/../templates/'), $arrayData);

					$Email = new Email();
					$Email->EnviarMontando(
						"Novo voluntária(o) registrada(o) site",
						$MailContent,
						$PostData['contact_name'],
						$PostData['contact_email'],
						SITE_ADDR_NAME,
						$arrayData['SITE_ADDR_EMAIL']
					);

					if (!$Email->getError()) {
						$MailConfirmation = Template::setTemplate(Template::getTemplate('contact_return_mail.html',
							__DIR__ . '/../templates/'), $arrayData);

						$ResponseEmail = new Email();

						$ResponseEmail->EnviarMontando(
							'Confirmação de recebimento Catiane Zanotto - 11112 ',
							$MailConfirmation,
							MAIL_SENDER,
							MAIL_USER,
							$PostData['contact_name'],
							$PostData['contact_email']
						);

						$jSON['trigger'] = AjaxErro("<p>Você recebeu um email. Aguarde em breve entraremos em contato.</p>");

					} else {
						$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
							E_USER_ERROR);
					}
				}

			case 'voluntary':

				if (array_search('', $PostData)) {
					$jSON['field'] = array_search('', $PostData);
					$jSON['trigger'] = AjaxErro('<b>Oops!</b> Preencha todos os campos obrigatórios!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['voluntary_email']) || !filter_var($PostData['voluntary_email'],
						FILTER_VALIDATE_EMAIL)) {
					$jSON['field'] = 'voluntary_email';
					$jSON['trigger'] = AjaxErro('<b>Oops!</b> E-mail informado não é válido!',
						E_USER_ERROR);

				} elseif (!isset($PostData['privacy'])) {
					$jSON['field'] = 'privacy';
					$jSON['trigger'] = AjaxErro('<b>Oops!</b> Você precisa estar em consentimento com nossa Política de Privacidade.',
						E_USER_ERROR);

				} else {

					$PostData['voluntary_name'] = Check::getCapilalize($PostData['voluntary_name']);
					$phoneMask = $PostData['voluntary_phone'];
					$PostData['voluntary_phone'] = Check::clearNumber($PostData['voluntary_phone']);
					$PostData['voluntary_cep'] = preg_replace('/[\D]/', '', $PostData['voluntary_cep']);
					$PostData['voluntary_email'] = mb_strtolower($PostData['voluntary_email']);
					$PostData['privacy'] = isset($PostData['privacy']) ? 1 : 0;
					$PostData['status'] = 0;

					$Create = new Create();
					$Create->ExeCreate(DB_VOLUNTEERS, $PostData);

					if ($Create->getResult()) {
						$jSON['trigger'] = AjaxErro("<p>Obrigado! <b>{$PostData['voluntary_name']}</b>, seu registro foi recebido com sucesso!</p>");
						$jSON['clear'] = true;
					}

					$arrayData = $PostData;
					$arrayData['INCLUDE_PATH'] = INCLUDE_PATH;
					$arrayData['SITE_NAME'] = SITE_NAME;
					$arrayData['SITE_ADDR_NAME'] = SITE_ADDR_NAME;
					$arrayData['SITE_ADDR_PHONE_A'] = SITE_ADDR_PHONE_A;
					$arrayData['SITE_ADDR_EMAIL'] = SITE_ADDR_EMAIL;
					$arrayData['SITE_ADDR_SITE'] = SITE_ADDR_SITE;
					$arrayData['BASE'] = BASE;
					$arrayData['phone_mask'] = $phoneMask;
					$arrayData['logo_mail'] = BASE .'/uploads/mail/logo.png';;

					require_once __DIR__ . './../_app_capture/class/Template.class.php';

					$MailContent = Template::setTemplate(Template::getTemplate('volunteer_mail.html',
						__DIR__ . '/../templates/'), $arrayData);

					$Email = new Email();
					$Email->EnviarMontando(
						"Novo voluntária(o) registrada(o) site",
						$MailContent,
						$PostData['voluntary_name'],
						$PostData['voluntary_email'],
						SITE_ADDR_NAME,
						$arrayData['SITE_ADDR_EMAIL']
					);

					if (!$Email->getError()) {
						$MailConfirmation = Template::setTemplate(Template::getTemplate('volunteer_return_mail.html',
							__DIR__ . '/../templates/'), $arrayData);

						$ResponseEmail = new Email();

						$ResponseEmail->EnviarMontando(
							'Confirmação de recebimento Catiane Zanotto - 11112 ',
							$MailConfirmation,
							MAIL_SENDER,
							MAIL_USER,
							$PostData['voluntary_name'],
							$PostData['voluntary_email']
						);

						$jSON['trigger'] = AjaxErro("<p>Você recebeu um email. Aguarde em breve entraremos em contato.</p>");

					} else {
						$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
							E_USER_ERROR);
					}
				}
		}

		//RETORNA O CALLBACK
		if ($jSON) {
			echo json_encode($jSON);
		} else {
			$jSON['trigger'] = AjaxErro('<b>Oops!</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
				E_USER_ERROR);
			echo json_encode($jSON);
		}
	} else {
		//ACESSO DIRETO
		die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
	}
