<?php
    //DEFINE O CALLBACK E RECUPERA O POST
    require_once '../../../_app/Config.inc.php';
    $jSON = null;
    $CallBack = 'contacts';
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
            //EXECUTA DE ACORDO COM CALLBACK-ACTION
            //CITIES
            case 'state':
                $jSON['cities'] = null;
                $PostData['city'] = !empty($PostData['city']) ? $PostData['city'] : null;

                if (!$PostData['uf']) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Selecione um estado por favor!', E_USER_NOTICE);
                }
                elseif ($PostData['uf'] && !$PostData['city']) {

                    $Read->FullRead("SELECT id, name, uf FROM " . DB_CITIES . " WHERE uf = :uf ORDER BY name ASC", "uf={$PostData['uf']}");

                    $jSON['cities'] .= "<option value=''>- Selecione a cidade -</option>";
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $Cities) {
                            extract($Cities);
                            $jSON['cities'] .= "<option value='{$name}'>{$name} - {$uf}</option> ";
                        }
                    }
                }
                else {
                    $jSON['rep'] = null;

                    $Read->ExeRead(DB_REPRESENTATIVES, "WHERE rep_cities LIKE '%' :ct '%' AND rep_uf = :uf  ORDER BY rep_cities ASC", "ct={$PostData['city']}&uf={$PostData['uf']}");

                    if ($Read->getResult()) {

                        foreach ($Read->getResult() as $Reps) {
                            extract($Reps);

                            $cidades = explode(',', $rep_cities);
                            $cidades = array_map('trim', $cidades);
                            $cidades = array_flip($cidades);

                            if (array_key_exists($PostData['city'], $cidades)) {
                               // $whats = Check::WHATS($rep_cellphone);
                                $jSON['rep'] .= "<article class='box'>";
                                $jSON['rep'] .= "<h2>{$rep_company}</h2>";
                                $jSON['rep'] .= "<ul><li><h3>{$rep_city} - {$rep_uf}</h3></li>";
                                $jSON['rep'] .= "<li><h4>{$rep_name}</h4></li>";
                                $jSON['rep'] .= "<li><a class='btn btn_phone' href='tel:{$rep_phone}' title='Clique para Ligar'><i class='fa fa-phone'></i> {$rep_phone}</a>";
                                $jSON['rep'] .= "<li><a class='btn btn_whatsapp' target='_blank' href='"
                                    .Check::WhatsMessage($rep_cellphone, 'Escreva sua mensagem aqui...')."' title='Enviar mensagem pelo WhatsApp'><i class='fab fa-whatsapp'></i> {$rep_cellphone}</a></li>";
                                $jSON['rep'] .= "<li><a class='btn btn_mailto' target='_blank' href='mailto:{$rep_email}'><i class='fa fa-envelope'></i> ENVIAR E-MAIL</a></li>";
                                $jSON['rep'] .= "<li><b>Cidades Atendidas:</b> {$rep_cities}</li>";
                                $jSON['rep'] .= "</ul>";
                                $jSON['rep'] .= "</article>";
                            }
                        }
                    }
                    else {
                        $jSON['rep'] = AjaxErro('<p class="uppercase text-center"><b>Ainda não temos um representante atendendo está cidade, entre em contato com Matriz.</b></p>', E_USER_NOTICE);

                        $jSON['rep'] .= "<article class='box'>";
                        $jSON['rep'] .= "<h2>".SITE_NAME."</h2>";
                        $jSON['rep'] .= "<ul><li><h3>".SITE_ADDR_ADDR. ' '. SITE_ADDR_CITY."</h3></li>";
                        $jSON['rep'] .= "<li><h4>Equipe Comercial</h4></li>";
                        $jSON['rep'] .= "<li><a class='btn btn_phone' href='tel:" . SITE_ADDR_PHONE_A . "' title='Clique para Ligar'><i class='fa fa-phone'></i>" . SITE_ADDR_PHONE_A . "</a>";
                        $jSON['rep'] .= "<li><a class='btn btn_whatsapp' target='_blank' href='" .Check::WhatsMessage
                            (SITE_ADDR_WHATS, 'Escreva sua mensagem aqui...')."' title='Enviar mensagem pelo WhatsApp'><i 
class='fab fa-whatsapp'></i> ".SITE_ADDR_WHATS."</a></li>";
                        $jSON['rep'] .= "<li><a class='btn btn_mailto' target='_blank' href='mailto:" . SITE_ADDR_EMAIL . "'><i class='fa fa-envelope'></i> ENVIAR E-MAIL</a></li>";
                        $jSON['rep'] .= "</article>";
                    }
                }
                break;

            //REPRESENTATIVES
            case 'cities':
                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Selecione uma cidade por favor!', E_USER_NOTICE);
                }
                else {

                    $Read->FullRead("SELECT name FROM " . DB_CITIES . " WHERE id = :ct ORDER BY name", "ct={$PostData['city']}");

                    if ($Read->getResult()) {
                        $jSON['trigger'] .= "<form class='j_submit_cities' name='cities' method='post' enctype='multipart/form-data'>";
                        $jSON['trigger'] .= "<input class='callack' type='hidden' name='callback' value='representatives'>";
                        $jSON['trigger'] .= "<input class='callback-action' type='hidden' name='callback_action' value='cities'>";
                        $jSON['trigger'] .= "<label>Escolha a Cidade no {$PostData['UF']}:</label>";
                        $jSON['trigger'] .= "<select class='form-control' id='city' name='city' required > ";
                        $jSON['trigger'] .= "<option value = ''>- Selecione a cidade - </option> ";

                        foreach ($Read->getResult() as $Cities) {
                            extract($Cities);
                            $jSON['trigger'] .= "<option value = '{$id}'>{$name} - {$uf}</option> ";
                        }

                        $jSON['trigger'] .= " </select >";
                        $jSON['trigger'] .= "<figure class='form_spinner'> <img class='form_load none' src='".BASE."/admin/_img/load.gif'/> <br> <p class='form_load none'>Carregando...</p> </figure>";
                        $jSON['trigger'] .= "</form>";
                    }
                }
                break;

            case 'news2':
                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
                }
                else {
                    if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
                        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    }
                    else {
                        $LeadData = [
                            'lead_name' => $PostData['name'],
                            'lead_email' => $PostData['email'],
                            'lead_conversion' => $Case
                        ];
                        $Read->FullRead("SELECT lead_email FROM " . DB_LEADS . " WHERE lead_email = :mail", "mail ={$LeadData['lead_email']}");
                        if ($Read->getResult()) {
                            $jSON['trigger'] = AjaxErro(" < b>{$LeadData['lead_name']}</b > Seu e - mail já está registrado em nossa Newsletter!", E_USER_NOTICE);
                        }
                        else {
                            $Create = new Create;
                            $Create->ExeCreate(DB_LEADS, $LeadData);
                            $jSON['trigger'] = AjaxErro(" < b>Obrigado!</b > Seu e - mail foi registrado com Sucesso!");
                        }
                    }
                }
                break;

            case 'cotacao':
                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Para receber sua cotação, por favor preencha todos os campos!', E_USER_NOTICE);
                }
                else {
                    if (!Check::Email($PostData['lead_email']) || !filter_var($PostData['lead_email'], FILTER_VALIDATE_EMAIL)) {
                        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    }
                    else {
                        $Cotacao = implode(', ', $PostData['lead_cotacao']);
                        $LeadData = [
                            'lead_name' => $PostData['lead_name'],
                            '
                            lead_email' => $PostData['lead_email'],
                            'lead_phone' => $PostData['lead_phone'],
                            'lead_cotacao' => $Cotacao,
                            'lead_message' => $PostData['lead_message'],
                            'lead_conversion' => $Case,
                            'lead_status' => 1
                        ];
                        $Create = new Create;
                        $Create->ExeCreate(DB_LEADS, $LeadData);
                        //DISPARA OS ALERTAS POR E-MAIL
                        $MailContent = '
                            <table width="550" style="font - family: Tahoma, sans - serif">
                                <tr><td>
                                    <p>Nova cotação recebida de <b> ' . $LeadData['lead_name'] . '</b> gerado pelo formulário do Site</p>
                                    <p>Nome do Remetente: ' . $LeadData['lead_name'] . ' </p>
                                    <p>E-mail para resposta: ' . $LeadData['lead_email'] . ' </p>
                                    <p><b>Produtos de Interesse:</b> ' . $LeadData['lead_cotacao'] . '</p>
                                    <p><b>Mensagem Recebida:</b><br>' . $LeadData['lead_message'] . ' </p>
                                </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Hora = date('H');
                        $Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');
                        $Agradecimento = '
                            <table width="550" style="font - family: Tahoma, sans - serif;">
                                <tr><td>
                                    <p>' . $Saudacao . ' <b>' . $LeadData['lead_name'] . '</b></p>
                                    <p><br>Em breve estaremos respondendo sua solicitação de orçamento. <br><br> Obrigado! </p><br>
                                    <p>Mais que embalagem... Envolvimento!</p>

                                    <p style="font - size: 1em;">
                                        <img src="' . INCLUDE_PATH . '/images/logo-mail.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" />
                                        <br><br>
                                        ' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                                        Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                                    </p>
                                </td></tr>
                            </table>
                        <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Email = new Email;
                        $Email->EnviarMontando('Cotação originada pelo Site', $MailContent, $LeadData['lead_name'], $LeadData['lead_email'], SITE_ADDR_NAME, SITE_ADDR_EMAIL);
                        if (!$Email->getError()) {
                            $Email->EnviarMontando('Confirmação de recebimento', $Agradecimento, SITE_ADDR_NAME, SITE_ADDR_EMAIL, $LeadData['lead_name'], $LeadData['lead_email']);
                            $jSON['trigger'] = AjaxErro(" < b>Obrigado!</b > Sua cotação foi recebida com Sucesso!");
                            //header('Location: ' . BASE . '/cotacao#cotacao');
                        }
                        else {
                            $jSON['trigger'] = Erro("Desculpe, não foi possível enviar sua cotação . Entre em contato via por E - mail: " . SITE_ADDR_EMAIL . " . Obrigado!", E_USER_ERROR);
                        }
                    }
                }
                break;

            case 'sac':

                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Para enviar sua mensagem, por favor preencha todos os campos!', E_USER_NOTICE);
                }
                else {
                    if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
                        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    }
                    else {
                        //$Create = new Create;
                        //$Create->ExeCreate(DB_LEADS, $PostData);
                        $Hora = date('H');
                        $Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');

                        //DISPARA OS ALERTAS POR E-MAIL
                        $MailContent = "
                            <style>body, img{max-width: 600px !important;
                            height: auto !important;} p{font-size: 16px;
                            margin-botton: 15px 0 !important;}table th{background: #D1D1D1;padding:10px}</style>
                            <table width='600' style='font-family: Tahoma, sans-serif'>
                                <tr>
                                    <td>
                                        <p>Olá! Meu nome é <b> {$PostData['name']}</b> sou <b>{$PostData['profile']}</b> </p>
                                        <p><br>{$PostData['message']}<br><br></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th class='center-cell'>DADOS DO CONTATO:</th>
                                </tr>
                                <tr>
                                    <td>
                                        <p style='font-size: 1.2em'><b>Dados para Resposta:</b></p>
                                        <p><b>Nome:</b> {$PostData['name']}</p>
                                        <p><b>E-mail:</b> {$PostData['email']}</p>
                                        <p><b>Telefone:</b> {$PostData['telephone']}</p>" .

                            ($PostData['profile'] == 'Consumidor Final' ? "<p><b>CPF:</b> {$PostData['cpf']}</p> <p><b>CEP:</b> {$PostData['cep']}</p>" : "<p><b>Empresa:</b> {$PostData['company']}</p> <p><b>CNPJ:</b> {$PostData['cnpj']}</p>")

                            . " <p><b>Endereço:</b> {$PostData['address']}, {$PostData['number']} " . ($PostData['complement'] ? $PostData['complement'] : '') . "</p>
                                        <p><b>Bairro:</b> {$PostData['neigthborhood']}</p>
                                        <p><b>Cidade/UF:</b> {$PostData['city']}/{$PostData['state']}</p>
                                        <br>
                                    </td>
                                </tr>
                            </table>
                            ";

                        $Agradecimento = "
                        <style>body, img{max-width: 550px !important; height: auto !important;} p{font-size: 1.15em; margin-botton: 15px 0 !important;}</style>

                            <table width='550' style='font-family: Tahoma, sans-serif;'>
                                <tr><td>
                                    <p>{$Saudacao} <b>{$PostData['name']}</b></p>
                                    <p><br>Em breve estaremos respondendo sua mensagem. <br><br> Obrigado! </p><br>

                                    <p style='font-size: 1em;'>
                                        <img src='" . INCLUDE_PATH . "/images/logo-mail.png' alt='Atenciosamente " . SITE_NAME . "' title='Atenciosamente " . SITE_NAME . "'/>
                                        <br>
                                        " . SITE_ADDR_NAME . "<br>Telefone: " . SITE_ADDR_PHONE_A . "<br>E-mail: " . SITE_ADDR_EMAIL . "<br><br>
                                        Visite nosso site: <a title='" . SITE_NAME . "' href='" . BASE . "'>" . SITE_ADDR_SITE . "</a>
                                    </p>
                                </td></tr>
                            </table>
                        ";

                        $Email = new Email;

                        $Email->EnviarMontando('Contato realizado pelo Site', $MailContent, $PostData['name'], $PostData['email'], SITE_ADDR_NAME, $PostData['sector']);

                        if (!$Email->getError()) {
                            $Email->EnviarMontando('Loth - Confirmação de Recebimento', $Agradecimento, SITE_ADDR_NAME, SITE_ADDR_EMAIL, $PostData['name'], $PostData['email']);
                            $jSON['trigger'] = AjaxErro(" <b>Obrigado!</b> Sua mensagem foi recebida com <i class='icon icon-checkmark'></i>Sucesso!");
                        }
                        else {
                            $jSON['trigger'] = Erro("Desculpe, não foi possível enviar sua mensagem . Entre em contato via por E - mail: " . SITE_ADDR_EMAIL . " . Obrigado!", E_USER_ERROR);
                        }
                    }
                }
                break;

            case 'representative':
                if (in_array('', $PostData)) {
                    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Para enviar sua solicitação para ser nosso representante, por favor preencha todos os campos!', E_USER_NOTICE);
                }
                else {
                    if (!Check::Email($PostData['rep_email']) || !filter_var($PostData['rep_email'], FILTER_VALIDATE_EMAIL)) {
                        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    }
                    else {
                        $LeadData = [
                            'rep_name' => $PostData['rep_name'],
                            'rep_email' => $PostData['rep_email'],
                            'rep_phone' => $PostData['rep_phone'],
                            'rep_cnpj' => $PostData['rep_cnpj'],
                            'rep_cep' => $PostData['rep_cep'],
                            'rep_address' => $PostData['rep_address'],
                            'rep_address_number' => $PostData['rep_address_number'],
                            'rep_city' => $PostData['rep_city'],
                            'rep_uf' => $PostData['rep_uf'],
                            'rep_operation_field' => $PostData['rep_operation_field'],
                            'rep_message' => $PostData['rep_message'],
                            'rep_conversion' => $Case,
                            'rep_status' => 1
                        ];
                        $Create = new Create;
                        $Create->ExeCreate(DB_REPRESENTATIVES, $LeadData);
                        //DISPARA OS ALERTAS POR E-MAIL
                        $MailContent = '
                            <table width="550" style="font - family: Tahoma, sans - serif">
                                <tr><td>
                                    <p><b> ' . $LeadData['rep_name'] . '</b> deseja ser nosso representante na cidade de   ' . $LeadData['rep_city'] . '</p>
                                    <p>Nome do Remetente: ' . $LeadData['rep_name'] . ' </p>
                                    <p>E-mail para resposta: ' . $LeadData['rep_email'] . ' </p>
                                    <p>Telefone: ' . $LeadData['rep_phone'] . ' </p>
                                    <p>CNPJ: ' . $LeadData['rep_cnpj'] . ' </p>
                                    <p><b>Localidade:</b>' . $LeadData['rep_address'] . ' Nº ' . $LeadData['rep_address_number'] . ' </p>
                                    <p>' . $LeadData['rep_city'] . ' - ' . $LeadData['rep_uf'] . ' </p>
                                    <p><b>Área(s) de atuação:</b> ' . $LeadData['rep_operation_field'] . '</p>
                                    <p><b>Leia o que ele nos enviou:</b><br>' . $LeadData['rep_message'] . ' </p>
                                </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Hora = date('H');
                        $Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');
                        $Agradecimento = '
                            <table width="550" style="font - family: Tahoma, sans - serif;">
                                <tr><td>
                                    <p>' . $Saudacao . ' <b>' . $LeadData['rep_name'] . '</b></p>
                                    <p><br>Em breve estaremos respondendo sua solicitação. <br><br> Obrigado! </p><br>
                                    <p>Mais que embalagem... Envolvimento!</p>

                                    <p style="font - size: 1em;">
                                        <img src="' . INCLUDE_PATH . '/images/logo-mail.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" />
                                        <br><br>
                                        ' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                                        Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                                    </p>
                                </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Email = new Email;
                        $Email->EnviarMontando('Quero representar a Loth', $MailContent, $LeadData['rep_name'], $LeadData['rep_email'], SITE_ADDR_NAME, SITE_ADDR_EMAIL);

                        if (!$Email->getError()) {
                            $Email->EnviarMontando('Confirmação de recebimento', $Agradecimento, SITE_ADDR_NAME, SITE_ADDR_EMAIL, $LeadData['rep_name'], $LeadData['rep_email']);
                            $jSON['trigger'] = AjaxErro(" <b>Obrigado! {$LeadData['rep_name']}.</b > Sua solicitação foi recebida com Sucesso!");
                        }
                        else {
                            $jSON['trigger'] = Erro("Desculpe, não foi possível enviar sua solicitação . Entre em contato via por E - mail: " . SITE_ADDR_EMAIL . " . Obrigado!", E_USER_ERROR);
                        }
                    }
                }
                break;
        }
        //RETORNA O CALLBACK
        if ($jSON) {
            echo json_encode($jSON);
        }
        else {
            $jSON['trigger'] = AjaxErro('<b class="icon - warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
            echo json_encode($jSON);
        }
    }
    else {
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
