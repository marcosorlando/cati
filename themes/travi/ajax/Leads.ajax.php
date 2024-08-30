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

		//SELECIONA AÇÃO
		switch ($Case) {
			//CAPTURA DE ACORDO COM CALLBACK-ACTION
			case 'news1':
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

			case 'contato':
				if (in_array('', $PostData)) {
					$jSON['trigger'] = AjaxErro('<b>OOPS! </b> Preencha todos os campos!', E_USER_WARNING);
				} else {
					if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
						$jSON['trigger'] = AjaxErro('<b>OOPS! </b> E-mail informado não é válido!', E_USER_ERROR);
					} else {
						$MailContent = '
                            <table width="550" style="font-family: Tahoma, sans-serif">
                                <tr><td>
                                <p>Novo contato de <b> ' . $PostData['nome'] . '</b> gerado pelo formulário do Site</p>
                                <p>Nome do Remetente: ' . $PostData['nome'] . ' </p>
                                <p>Telefone: ' . $PostData['fone'] . '</p>
                                <p>E-mail para resposta: ' . $PostData['email'] . ' </p>
                                <p><b>MENSAGEM:</b><br>' . $PostData['mensagem'] . ' </p>

                                <p style="font-size: 1em">
                                    <img src="' . INCLUDE_PATH . '/images/logo-travi-color.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" /><br><br>' . SITE_ADDR_NAME . '<br>Telefone:<b> ' . SITE_ADDR_PHONE_A . '</b><br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br> Visite nosso site: <a title=" ' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a><br>
                                    </p>
                                    </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';

						$Hora = date('H');
						$Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');

						$Agradecimento = '
                            <table width="550" style="font-family: "Tahoma, sans-serif;">
                                <tr><td>
                                <p>' . $Saudacao . ' <b>' . $PostData['nome'] . '</b></p>
                                <p><br>Em breve estaremos respondendo seu e-mail. <br> Obrigado! </p><br><br>

                                <p style="font-size: 1em;">
                                    <img src="' . INCLUDE_PATH . '/images/logo-travi-color.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" />
            	    				<br><br>
            	    				' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                                    Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                                </p>
                                    </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';

						$PostData['assunto'] = 'Contato do Site';
						$jSON['trigger'] = AjaxErro("{$Saudacao} {$PostData['nome']}, sua mensagem foi recebida com sucesso. Obrigado");

						$Email = new Email;
						$Email->EnviarMontando($PostData['assunto'], $MailContent, $PostData['nome'],
							$PostData['email'], SITE_ADDR_NAME, SITE_ADDR_EMAIL);

						if (!$Email->getError()) {
							$jSON['trigger'] = AjaxErro("{$Saudacao} {$PostData['nome']}, sua mensagem foi recebida com sucesso. Obrigado");
							$Email->EnviarMontando('Confirmação de recebimento', $Agradecimento, SITE_ADDR_NAME,
								SITE_ADDR_EMAIL, $PostData['nome'], $PostData['email']);
							//header('Location: ' . BASE . '/contato#contactForm');
						} else {
							$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
								E_USER_ERROR);
						}
					}
				}
				break;

			case 'curriculum':

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
							$MailConfirmation = Template::setTemplate(Template::getTemplate('curriculum_return_mail.html', __DIR__ . '/../templates/'), $arrayData);

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

			case 'cotacao':
				if (in_array('', $PostData)) {
					$jSON['trigger'] = AjaxErro('<b>OOPS! </b> Informe seu e-mail por favor!', E_USER_NOTICE);

				} else {
					if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
						$jSON['trigger'] = AjaxErro('<b>OOPS! </b> E-mail informado não é válido!', E_USER_NOTICE);
					} else {
						$MailContent = '
                            <table width="550" style="font-family: Tahoma, sans-serif">
                                <tr><td>
                                <p>Novo contato de <b> ' . $PostData['nome'] . '</b> gerado pelo formulário do Site</p>
                                <p>Nome do Remetente: ' . $PostData['nome'] . ' </p>
                                <p>Telefone: ' . $PostData['fone'] . '</p>
                                <p>E-mail para resposta: ' . $PostData['email'] . ' </p>
                                <p>Razão Social: ' . $PostData['fantasia'] . '</p>
                                <p>Nome Fantasia: ' . $PostData['cnpj'] . '</p>
                                <p>Produto(s): ' . $PostData[''] . '</p>
                                <p><b>MENSAGEM:</b><br>' . $PostData['mensagem'] . ' </p>

                                <p style="font-size: 1em">
                                    <img src="' . INCLUDE_PATH . '/images/logo-travi-color.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" /><br><br>' . SITE_ADDR_NAME . '<br>Telefone:<b> ' . SITE_ADDR_PHONE_A . '</b><br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br> Visite nosso site: <a title=" ' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a><br>
                                    </p>
                                    </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';

						$Hora = date('H');
						$Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');

						$Agradecimento = '
                            <table width="550" style="font-family: "Tahoma, sans-serif;">
                                <tr><td>
                                <p>' . $Saudacao . ' <b>' . $PostData['nome'] . '</b></p>
                                <p><br>Em breve estaremos respondendo seu e-mail. <br> Obrigado! </p><br><br>

                                <p style="font-size: 1em;">
                                    <img src="' . INCLUDE_PATH . '/images/logo-travi-color.png" alt="Atenciosamente '
							. SITE_NAME . '" title="Atenciosamente, ' . SITE_NAME . '" />
            	    				<br><br>
            	    				' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                                    Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                                </p>
                                    </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';

						$PostData['assunto'] = 'Contato do Site';

						$Email = new Email;
						$Email->EnviarMontando($PostData['assunto'], $MailContent, $PostData['nome'],
							$PostData['email'], SITE_ADDR_NAME, SITE_ADDR_EMAIL);

						if (!$Email->getError()) {
							$jSON['trigger'] = AjaxErro("{$Saudacao} {$PostData['nome']}, sua mensagem foi recebida com sucesso. Obrigado");
							$Email->EnviarMontando('Confirmação de recebimento', $Agradecimento, 'Global Suprimentos',
								'contato@suprimentosglobal.com.br', $PostData['nome'], $PostData['email']);
							header('Location: ' . BASE . '/contato#formulario');
						} else {
							$jSON['trigger'] = AjaxErro('Desculpe, não foi possível enviar sua mensagem. Entre em contato via E-mail: ' . SITE_ADDR_EMAIL . ' Obrigado!',
								E_USER_ERROR);
						}
						$jSON['redirect'] = 'dashboard.php?wc=home';
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
