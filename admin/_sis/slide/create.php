<?php

	$AdminLevel = LEVEL_WC_SLIDES;
	if (!APP_SLIDE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
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

	$SlideId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if ($SlideId):
		$Read->ExeRead(DB_SLIDES, "WHERE slide_id = :id", "id={$SlideId}");
		if ($Read->getResult()):
			$FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
			extract($FormData);
		else:
			$_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um slide que não existe ou que foi removido recentemente!";
			header('Location: dashboard.php?wc=slide/home');
			exit;
		endif;
	else:
		$SlideCreate = ['slide_date' => date('Y-m-d H:i:s'), 'slide_start' => date('Y-m-d H:i:s')];
		$Create->ExeCreate(DB_SLIDES, $SlideCreate);
		header('Location: dashboard.php?wc=slide/create&id=' . $Create->getResult());
		exit;
	endif;
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-camera"><?= $slide_title ? $slide_title : 'Novo Slide'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=slide/home">Slides</a>
			<span class="crumb">/</span>
			Gerenciar Destaque
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Slides!" href="dashboard.php?wc=slide/home" class="btn btn_blue icon-eye">Ver Destaques!</a>
		<a title="Novo Slide!" href="dashboard.php?wc=slide/create" class="btn btn_green icon-plus">Adicionar
			Destaque!</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="post_create" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Slides"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="slide_id" value="<?= $SlideId; ?>"/>

		<div class="slide_tab_image">
			<a class="box box33 icon-mobile wc_tab wc_active" href="#mobile" title="Mobile">Mobile</a>
			<a class="box box33 icon-tablet wc_tab" href="#tablet" title="Tablet">Tablet</a>
			<a class="box box33 icon-display wc_tab" href="#desktop" title="Desktop">Desktop</a>
		</div>

		<article class="box box100">
			<div class="panel">
				<div class="wc_tab_target wc_active" id="mobile">
					<div class="slide_create_cover al_center">
						<div class="upload_progress none">0%</div>
						<?php
							$imageMobile = (!empty($slide_image_mobile) && file_exists("../uploads/{$slide_image_mobile}") && !is_dir("../uploads/{$slide_image_mobile}") ? "uploads/{$slide_image_mobile}" : 'admin/_img/no_image.jpg');
						?>
						<img class="slide_image_mobile post_cover" alt="Capa" title="Capa" src="../tim.php?src=<?=
							$imageMobile; ?>&w=430&h=660" default="../tim.php?src=<?= $imageMobile; ?>&w=430&h=660"/>
					</div>

					<label class="label m_top">
						<span class="legend">Capa: (JPG 430x660px)</span>
						<input type="file" class="wc_loadimage" name="slide_image_mobile"/>
					</label>
				</div>

				<div class="wc_tab_target ds_none" id="tablet">
					<div class="slide_create_cover al_center">
						<div class="upload_progress none">0%</div>
						<?php
							$imageTablet = (!empty($slide_image_tablet) && file_exists("../uploads/{$slide_image_tablet}") && !is_dir("../uploads/{$slide_image_tablet}") ? "uploads/{$slide_image_tablet}" : 'admin/_img/no_image.jpg');
						?>
						<img class="slide_image_tablet post_cover" alt="Capa" title="Capa"
						     src="../tim.php?src=<?= $imageTablet; ?>&w=1500&h=460"
						     default="../tim.php?src=<?= $imageTablet; ?>&w=1500&h=460"/>
					</div>

					<label class="label m_top">
						<span class="legend">Capa: (JPG 1500x460px)</span>
						<input type="file" class="wc_loadimage" name="slide_image_tablet"/>
					</label>
				</div>

				<div class="wc_tab_target ds_none" id="desktop">
					<div class="slide_create_cover al_center">
						<div class="upload_progress none">0%</div>
						<?php
							$imageDesktop = (!empty($slide_image_desktop) && file_exists("../uploads/{$slide_image_desktop}") && !is_dir("../uploads/{$slide_image_desktop}") ? "uploads/{$slide_image_desktop}" : 'admin/_img/no_image.jpg');
						?>
						<img class="slide_image_desktop post_cover" alt="Capa" title="Capa"
						     src="../tim.php?src=<?= $imageDesktop; ?>&w=<?= SLIDE_W; ?>&h=<?= SLIDE_H; ?>"
						     default="../tim.php?src=<?= $imageDesktop; ?>&w=<?= SLIDE_W; ?>&h=<?= SLIDE_H; ?>"/>
					</div>

					<label class="label m_top">
						<span class="legend">Capa: (JPG <?= SLIDE_W; ?>x<?= SLIDE_H; ?>px)</span>
						<input type="file" class="wc_loadimage" name="slide_image_desktop"/>
					</label>
				</div>

				<label class="label">
					<span class="legend">Título do Banner (SEO):</span>
					<input class="font_medium" type="text" name="slide_title" value="<?= $slide_title; ?>" required/>
				</label>
				<label class="label">
					<span class="legend">Link na imagem:</span>
					<input type="text" placeholder="Link de destino ao clicar no banner" name="slide_link" value="<?=
						$slide_link; ?>"/>
				</label>

				<label class="label al_center"><b>ESCOLHA QUAIS ITENS APARECERAM POR CIMA DA IMAGEM (VERDE = MOSTRA | CINZA = OCULTA)</b></label>

				<div class="wc_actions" style="background:#2E4051; text-align: center; margin: 5px 0 10px 0">
					<div class="box box100">
						<label class="label_check label_publish <?= ($show_headline == 1 ? 'active' : ''); ?>">
							<input style="margin-top: -1px;" type="checkbox" value="1" name="show_headline" <?=
								($show_headline == 1 ? 'checked' : ''); ?>>
							HEADLINE
						</label>
						<label class="label_check label_publish <?= ($show_desc == 1 ? 'active' : ''); ?>">
							<input style="margin-top: -1px;" type="checkbox" value="1"
							       name="show_desc" <?= ($show_desc == 1 ? 'checked' : ''); ?>>
							TEXTO COMPLEMENTAR
						</label>

						<label class="label_check label_publish <?= ($slide_product == 1 ? 'active' : ''); ?>"><input
									style="margin-top: -1px;" type="checkbox" value="1"
									name="slide_product" <?= ($slide_product == 1 ? 'checked' : ''); ?>>
							PRODUTO
						</label>
						<label class="label_check label_publish <?= ($slide_category == 1 ? 'active' : ''); ?>"><input
									style="margin-top: -1px;" type="checkbox" value="1"
									name="slide_category" <?= ($slide_category == 1 ? 'checked' : ''); ?>>
							CATEGORIA</label>
					</div>
				</div>

				<label class="label">
					<span class="legend">Texto da Chamada:</span>
					<input class="font_medium" type="text" name="slide_headline" value="<?= $slide_headline; ?>"
					       required/>
				</label>

				<label class="label">
					<span class="legend">Texto Complementar:</span>
					<textarea style="font-size: 1.2em;" name="slide_desc" rows="3"
					          required><?= $slide_desc; ?></textarea>
				</label>


				<div class="label_50">
					<label class="label">
						<span class="legend">Link para <b>PRODUTO</b>)</span>
						<input type="text" name="slide_link_pdt" value="<?= $slide_link_pdt; ?>"/>
					</label>
					<label class="label">
						<span class="legend">Texto do <b>Botão</b>)</span>
						<input type="text" name="slide_link_pdt_btn" value="<?= $slide_link_pdt_btn; ?>"/>
					</label>

				</div>
				<div class="label_50">
					<label class="label">
						<span class="legend">Link para  <b>Categoria</b>)</span>
						<input style="font-size: 1.2em;" type="text" name="slide_link_cat"
						       value="<?= $slide_link_cat; ?>"/>
					</label>
					<label class="label">
						<span class="legend">Texto do <b>Botão</b>)</span>
						<input style="font-size: 1.2em;" type="text" name="slide_link_cat_btn"
						       value="<?= $slide_link_cat_btn; ?>"/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Mostrar a partir de:</span>


						<input type="text" class="jwc_datepicker" data-timepicker="true" readonly="readonly"
						       name="slide_start"
						       value="<?= $slide_start ? date('d/m/Y H:i',
							       strtotime($slide_start)) : date('d/m/Y H:i:s'); ?>"
						       required/>
					</label>

					<label class="label">
						<span class="legend">Até dia: (opcional)</span>

						<input type="text" class="jwc_datepicker" data-timepicker="true"
						       name="slide_end"
						       value="<?= $slide_end ? date('d/m/Y H:i', strtotime($slide_end)) : ''; ?>"
						/>

					</label>
				</div>

				<!--    <div class="wc_actions" style="text-align: right">
                    <label class="label_check label_publish <?
					/*= ($slide_status == 1 ? 'active' : ''); */ ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="slide_status" <?
					/*= ($slide_status == 1 ? 'checked' : ''); */ ?>> Publicar Agora!</label>
                    <button name="public" value="1" class="btn btn_green icon-share" style="margin-left: 5px;">Atualizar Destaque!</button>
                    <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                </div>-->
				<div class="clear"></div>

				<div class="wc_actions">
						<?= Check::switchOnOff('slide_status', $slide_status, 'Publicar
							Agora!', 'SIM', 'NÃO');?>
						<button name="public" value="1" class="btn btn_green icon-share"
">Atualizar Destaque! <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
						   title="Enviando Requisição!" src="_img/load_w.gif"/></button>
				</div>

			</div>
		</article>
	</form>
</div>
