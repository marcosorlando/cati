<?php
$AdminLevel = LEVEL_WC_HELLO;
if (!APP_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
    $Read = new Read;
endif;

// AUTO INSTANCE OBJECT CREATE
if (empty($Create)):
    $Create = new Create;
endif;

$HelloId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($HelloId):
    $Read->ExeRead(DB_HELLO, "WHERE hello_id = :id", "id={$HelloId}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);
    else:
        $_SESSION['trigger_controll'] = Erro("<b>Oopss {$Admin['user_name']}</b>, você tentou editar uma hellobar que não existe ou que foi removida recentemente!", E_USER_NOTICE);
        header('Location: dashboard.php?wc=hello/home');
    endif;
else:
    $HelloCreate = ['hello_date' => date('Y-m-d H:i:s'), 'hello_status' => 0, "user_id" => $Admin['user_id']];
    $Create->ExeCreate(DB_HELLO, $HelloCreate);
    header('Location: dashboard.php?wc=hello/create&id=' . $Create->getResult());
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-bullhorn"><?= $hello_title ? $hello_title : 'Nova Hellobar'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=hello/home">Hellobar</a>
            <span class="crumb">/</span>
            Gerenciar Hellobar
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Voltar" href="dashboard.php?wc=hello/home" class="btn btn_blue icon-bullhorn">Voltar</a>
    </div>
</header>

<div class="dashboard_content">

    <form name="hello_add" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Custom"/>
        <input type="hidden" name="callback_action" value="hellobar_update"/>
        <input type="hidden" name="hello_id" value="<?= $HelloId; ?>"/>

        <div class="box box70">
            <div class="panel">
                <label class="label">
                    <span class="legend">Capa (Largura de <?= IMAGE_W; ?>px):</span>
                    <input type="file" class="wc_loadimage" name="hello_cover"/>
                </label>

                <label class="label">
                    <span class="legend">Headline:</span>
                    <input style="font-size: 1.4em;" type="text" name="hello_title" value="<?= $hello_title; ?>" placeholder="Título da Hellobar:" required/>
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">CTA (texto do botão):</span>
                        <input  type="text" name="hello_cta" value="<?= $hello_cta; ?>" placeholder="Texto do botão:" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Link:</span>
                        <input  type="text" name="hello_link" value="<?= $hello_link; ?>" placeholder="Link de ação:" required/>
                    </label>
                </div>

                <label class="label">
                    <span class="legend">Cor do botão?</span>
                    <select name="hello_color" required="required">
                        <option value="">Selecione a cor</option>
                        <option <?= ($hello_color == 'blue' ? 'selected="selected"' : ''); ?> value="blue">Azul</option>
                        <option <?= ($hello_color == 'green' ? 'selected="selected"' : ''); ?> value="green">Verde</option>
                        <option <?= ($hello_color == 'yellow' ? 'selected="selected"' : ''); ?> value="yellow">Amarelo</option>
                        <option <?= ($hello_color == 'red' ? 'selected="selected"' : ''); ?> value="red">Vermelho</option>
                    </select>
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Onde você quer exibir?</span>
                        <select name="hello_position" required="required">
                            <option value="">Selecione a posição</option>
                            <option <?= ($hello_position == 'center' ? 'selected="selected"' : ''); ?> value="center">Ao centro da página!</option>
                            <option <?= ($hello_position == 'right_top' ? 'selected="selected"' : ''); ?> value="right_top">Direita Acima!</option>
                            <option <?= ($hello_position == 'right_bottom' ? 'selected="selected"' : ''); ?> value="right_bottom">Direita Abaixo!</option>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Regra de exibição: <span class="icon-info icon-notext wc_tooltip"><span class="wc_tooltip_balloon">Defina uma palavra chave para disparar sua hellobar!</span></span></span>
                        <input  type="text" name="hello_rule" value="<?= $hello_rule; ?>" placeholder="Regra de exibição:"/>
                    </label>
                </div>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Exibir a partir de:</span>
                        <input class="jwc_datepicker" data-timepicker="true" readonly="readonly" type="text" name="hello_start" value="<?= (!empty($hello_start) ? date("d/m/Y H:i", strtotime($hello_start)) : date("d/m/Y H:i")); ?>" placeholder="Início da programação:" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Parar dia:</span>
                        <input class="jwc_datepicker" data-timepicker="true" readonly="readonly" type="text" name="hello_end" value="<?= (!empty($hello_end) ? date("d/m/Y H:i", strtotime($hello_end)) : date("d/m/Y H:i", strtotime("+10days"))); ?>" placeholder="Encerramento da programação:" required/>
                    </label>
                </div>


                <div class="clear"></div>
            </div>
        </div>

        <div class="box box30">
            <div class="post_create_cover">
                <?php
                $HelloImage = (!empty($hello_image) && file_exists("../uploads/{$hello_image}") && !is_dir("../uploads/{$hello_image}") ? "uploads/{$hello_image}" : 'admin/_img/no_image.jpg');
                ?>
                <img style="width: 100%;" class="hello_cover" alt="Imagem da Hellobar" title="Imagem da Hellobar"
                     src="../tim.php?src=<?= $HelloImage; ?>&w=<?= IMAGE_W / 3; ?>&h=auto" default="../tim.php?src=<?=
	                $HelloImage; ?>&w=<?= IMAGE_W / 3; ?>&h=auto"/>
            </div>
	        <div class="panel">
		        <div class="wc_actions">
			        <label class="label_check label_publish <?= ($hello_status == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="hello_status" <?= ($hello_status == 1 ? 'checked' : ''); ?>> Publicar Agora!</label>
			        <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
			        <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
		        </div>
	        </div>
        </div>
    </form>
</div>
