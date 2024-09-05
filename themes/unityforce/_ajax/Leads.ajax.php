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

			case 'speaker':

				if (
					array_search('', $PostData) && (
						array_search('', $PostData) != 'speaker_why_lecture' &&
						array_search('', $PostData) != 'speaker_lecture_link' &&
						array_search('', $PostData) != 'speaker_instagram' &&
						array_search('', $PostData) != 'speaker_linkedin' &&
						array_search('', $PostData) != 'speaker_facebook' &&
						array_search('', $PostData) != 'speaker_youtube'
					)
				) {
					$jSON['field'] = array_search('', $PostData);
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> Preencha todos os campos obrigatórios!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['speaker_email']) || !filter_var($PostData['speaker_email'],
						FILTER_VALIDATE_EMAIL)) {
					$jSON['field'] = 'speaker_email';
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> E-mail informado não é válido!',
						E_USER_ERROR);

				} elseif (!isset($PostData['privacy'])) {
					$jSON['field'] = 'privacy';
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> Você precisa estar em consentimento com nossa Política de Privacidade.',
						E_USER_ERROR);

				} else {
					$PostData['speaker_name'] = Check::Capitalise($PostData['speaker_name']);
					$PostData['speaker_lastname'] = Check::Capitalise($PostData['speaker_lastname']);
					$phoneMask = $PostData['speaker_phone'];
					$PostData['speaker_phone'] = Check::clearNumber($PostData['speaker_phone']);
					$PostData['speaker_email'] = mb_strtolower($PostData['speaker_email']);
					$PostData['speaker_yes'] = isset($PostData['speaker_yes']) ? 1 : 0;
					$PostData['privacy'] = isset($PostData['privacy']) ? 1 : 0;
					$PostData['status'] = 0;
					$PostData['speaker_facebook'] = Check::clearSocial($PostData['speaker_facebook']);
					$PostData['speaker_linkedin'] = Check::clearSocial($PostData['speaker_linkedin']);
					$PostData['speaker_instagram'] = Check::clearSocial($PostData['speaker_instagram']);
					$PostData['speaker_youtube'] = Check::clearSocial($PostData['speaker_youtube']);

					//UPLOAD FOTO
					if (!empty($_FILES['speaker_thumb'])) {
						$File = $_FILES['speaker_thumb'];

						$Read = new Read();
						$Read->ExeRead(DB_SPEAKERS,
							" WHERE speaker_email = :email ORDER BY speaker_id DESC LIMIT :limit",
							"email={$PostData['speaker_email']}&limit=1");

						if ($Read->getResult()) {
							$ThisCandidate = $Read->getResult()[0];
							if ($ThisCandidate['speaker_thumb'] &&
								file_exists("../../../uploads/{$ThisCandidate['speaker_thumb']}")
								&& !is_dir("../../../uploads/{$ThisCandidate['speaker_thumb']}")) {
								unlink("../../../uploads/{$ThisCandidate['speaker_thumb']}");
							}

							$Upload = new Upload(__DIR__ . '/../../../uploads/');
							$Upload->Image(
								$File,
								Check::Name("{$PostData['speaker_name']}-{$PostData['speaker_lastname']}") . '-thumb',
								600,
								'speakers'
							);

							if ($Upload->getResult()) {
								$PostData['speaker_thumb'] = $Upload->getResult();

							} else {
								$jSON['trigger'] = AjaxErro("<b class='fas fa-image'> ERRO AO ENVIAR IMAGEM:</b> Olá, selecione um arquivo em .jpg ou .png para anexar.",
									E_USER_WARNING);
								echo json_encode($jSON);
								return;
							}

							$Update = new Update();
							$Update->ExeUpdate(DB_SPEAKERS, $PostData, "WHERE speaker_email = :email",
								"email={$PostData['speaker_email']}");

							if ($Update->getResult()) {
								$jSON['trigger'] = AjaxErro("<i class='fas fa-user-astronaut'></i> Registro atualizado com sucesso!");
							}

						} else {
							$Upload = new Upload('../../../uploads/');
							$Upload->Image(
								$File,
								Check::Name("{$PostData['speaker_name']}-{$PostData['speaker_lastname']}") . '-thumb',
								600,
								'speakers'
							);

							if ($Upload->getResult()) {
								$PostData['speaker_thumb'] = $Upload->getResult();
							} else {
								$jSON['trigger'] = AjaxErro("<i class='fas fa-image'></i> <b> ERRO AO ENVIAR IMAGEM:</b> Olá, selecione um arquivo em .jpg ou .png para anexar.",
									E_USER_WARNING);
								echo json_encode($jSON);
								return;
							}

							$Create = new Create();
							$Create->ExeCreate(DB_SPEAKERS, $PostData);

							if ($Create->getResult()) {
								$jSON['trigger'] = AjaxErro("<b> {$PostData['speaker_name']}, seu cadastro foi recebido com sucesso! Aguarde nosso contato.</b>");
								$jSON['redirect'] = BASE . "/obrigado-inspiradora&id={$PostData['city_id']}";

							}
						}

						$speakerCity = $Read->LinkResult(DB_CITIES, 'id', $PostData['speaker_city'], 'name');
						$eventCity = $Read->LinkResult(DB_CITIES, 'id', $PostData['city_id'], 'name');
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
						$arrayData['speaker_city'] = $speakerCity['name'];
						$arrayData['event_city'] = $eventCity['name'];
						$arrayData['logo_mail'] = BASE . "/uploads/{$logoMail['logo_mail']}";
						$arrayData['speaker_yes'] = ($arrayData['speaker_yes'] == 1 ? "<p>Já realizou alguma atividade (palestra, workshop, pitch, treinamento)? <b>Sim</b> </p>" : '');
						$arrayData['speaker_lecture_link'] = (!empty($arrayData['speaker_lecture_link']) ? "<p><b>Link para evento:</b> <a href='{$arrayData['speaker_lecture_link']}'>{$arrayData['speaker_lecture_link']}</a></p>" : '');
						$arrayData['full_name'] = "{$arrayData['speaker_name']} {$arrayData['speaker_lastname']}";

						$arrayData['speaker_social_media'] = null;

						$arrayData['speaker_social_media'] .= ((!empty($PostData['speaker_facebook'])) ? "<p><b>Facebook: </b><a href='https://fb.com/{$PostData['speaker_facebook']}' title='Visitar perfil no Facebook' target='_new'>@{$PostData['speaker_facebook']}</a></p>" : '');

						$arrayData['speaker_social_media'] .= ((!empty($PostData['speaker_linkedin'])) ? "<p><b>Linkedin: </b><a href='https://www.linkedin.com/in/{$PostData['speaker_linkedin']}' title='Visitar perfil no Linkedin' target='_new'>@{$PostData['speaker_linkedin']}</a></p>" : '');

						$arrayData['speaker_social_media'] .= ((!empty($PostData['speaker_instagram'])) ? "<p><b>Instagram: </b><a href='https://www.instagram.com//{$PostData['speaker_instagram']}' title='Visitar perfil no Instagram' target='_new'>@{$PostData['speaker_instagram']}</a></p>" : '');

						$arrayData['speaker_social_media'] .= ((!empty($PostData['speaker_youtube'])) ? "<p><b>Youtube: </b><a href='https://www.youtube.com/@{$PostData['speaker_youtube']}' title='Visitar canal no Youtube' target='_new'>@{$PostData['speaker_youtube']}</a></p>" : '');


						require_once __DIR__ . './../_app_capture/class/Template.class.php';

						$MailContent = Template::setTemplate(Template::getTemplate('speaker_mail.html',
							__DIR__ . '/../templates/'),
							$arrayData);

						$Email = new Email();
						//$Email->addFile($anexo);
						$Email->EnviarMontando(
							"Novo Inspirador(a) registrado no Ella Lidera " . date('Y'),
							$MailContent,
							$PostData['speaker_name'],
							$PostData['speaker_email'],
							SITE_ADDR_NAME,
							$arrayData['SITE_ADDR_EMAIL']
						);

						if (!$Email->getError()) {
							$MailConfirmation = Template::setTemplate(Template::getTemplate('speaker_return_mail.html',
								__DIR__ . '/../templates/'), $arrayData);

							$ResponseEmail = new Email();

							$ResponseEmail->EnviarMontando(
								'Confirmação de recebimento Ella Lidera ' . date('Y'),
								$MailConfirmation,
								MAIL_SENDER,
								MAIL_USER,
								$PostData['speaker_name'],
								$PostData['speaker_email']
							);

						} else {
							$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
								E_USER_ERROR);
						}

					} else {
						unset($PostData['speaker_thumb']);
					}
				}

				break;

			case 'voluntary':

				if (array_search('', $PostData)) {
					$jSON['field'] = array_search('', $PostData);
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> Preencha todos os campos obrigatórios!',
						E_USER_NOTICE);

				} elseif (!Check::Email($PostData['voluntary_email']) || !filter_var($PostData['voluntary_email'],
						FILTER_VALIDATE_EMAIL)) {
					$jSON['field'] = 'voluntary_email';
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> E-mail informado não é válido!',
						E_USER_ERROR);

				} elseif (!isset($PostData['privacy'])) {
					$jSON['field'] = 'privacy';
					$jSON['trigger'] = AjaxErro('<b> OOPS! </b> Você precisa estar em consentimento com nossa Política de Privacidade.',
						E_USER_ERROR);

				} else {
					var_dump($PostData);

					$PostData['voluntary_name'] = Check::Capitalise($PostData['voluntary_name']);
					$phoneMask = $PostData['voluntary_phone'];
					$PostData['voluntary_phone'] = Check::clearNumber($PostData['voluntary_phone']);
					$PostData['voluntary_email'] = mb_strtolower($PostData['voluntary_email']);
					$PostData['privacy'] = isset($PostData['privacy']) ? 1 : 0;
					$PostData['status'] = 0;

					/*$Create = new Create();
					$Create->ExeCreate(DB_VOLUNTEERS, $PostData);

					if ($Create->getResult()) {
						$jSON['trigger'] = AjaxErro("<b> Você foi cadastrado com sucesso!</b>");
						$jSON['redirect'] = BASE."/obrigado-voluntario&id={$PostData['city_id']}";
					}*/

					/*$voluntaryCity = $Read->LinkResult(DB_CITIES, 'id', $PostData['voluntary_city'], 'name');
					$eventCity = $Read->LinkResult(DB_CITIES, 'id', $PostData['city_id'], 'name');
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
					$arrayData['voluntary_city'] = $voluntaryCity['name'];
					$arrayData['event_city'] = $eventCity['name'];
					$arrayData['logo_mail'] = BASE . "/uploads/{$logoMail['logo_mail']}";
					$arrayData['full_name'] = "{$arrayData['voluntary_name']} {$arrayData['voluntary_lastname']}";

					require_once __DIR__ . './../_app_capture/class/Template.class.php';

					$MailContent = Template::setTemplate(Template::getTemplate('volunteer_mail.html',
						__DIR__ . '/../templates/'), $arrayData);

					$Email = new Email();
					//$Email->addFile($anexo);
					$Email->EnviarMontando(
						"Nova voluntária(o) registrada no Ella Lidera " . date('Y'),
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
							'Confirmação de recebimento Ella Lidera ' . date('Y'),
							$MailConfirmation,
							MAIL_SENDER,
							MAIL_USER,
							$PostData['voluntary_name'],
							$PostData['voluntary_email']
						);

					} else {
						$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
							E_USER_ERROR);
					}*/
				}
		}

		//RETORNA O CALLBACK
		if ($jSON) {
			echo json_encode($jSON);
		} else {
			$jSON['trigger'] = AjaxErro('<b class="icon-warning"> OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
				E_USER_ERROR);
			echo json_encode($jSON);
		}
	} else {
		//ACESSO DIRETO
		die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
	}
