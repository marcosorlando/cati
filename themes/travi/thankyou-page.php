<?php

	require_once '_app_capture/class/Template.class.php';

	if (!$Read) {
		$Read = new Read;
	}

	$Read->ExeRead(DB_THANKYOU_PAGES, "WHERE page_name = :nm", "nm={$URL[0]}");

	if (!$Read->getResult()) {
		require REQUIRE_PATH . '/404.php';
		return;
	} else {

		$arrayData = $Read->getResult()[0];

		$root = "<style> :root {
			--page-bg-color:{$arrayData['page_bg_color']};			
			--page-subtitle-color:{$arrayData['page_subtitle_color']};
			--page-complement-color:{$arrayData['page_complement_color']};
			--page-footer-color:{$arrayData['page_footer_color']};
			--page-btn-text-color:{$arrayData['page_btn_text_color']};
			--page-btn-bg-color:{$arrayData['page_btn_bg_color']};
			--page-btn-bg-color-hover:{$arrayData['page_btn_bg_color_hover']};
			}
			</style>";


		$arrayData['page_logo'] = $arrayData['page_logo'] ? BASE . '/uploads/thankyoupages/' .
			$arrayData['page_logo'] :
			INCLUDE_PATH . '/images/logo-travi-color.png';

		$arrayData['page_cover'] = (!empty($arrayData['page_cover']) ? "<img class='page_cover' src='".BASE . "/uploads/thankyoupages/{$arrayData['page_cover']}' alt='Fundo da Página' />" : '');

		$arrayData['page_pdf'] = $arrayData['page_pdf'] ? BASE . '/uploads/thankyoupages/' .
			$arrayData['page_pdf'] : '';

		$arrayData['page_btn_icon'] = '&#128210';
		$arrayData['copyright'] = date('Y').' - '.SITE_ADDR_NAME;
		$arrayData['page_event'] = 'Lead';
		$arrayData['alt_logo'] = 'Logotipo da '.SITE_ADDR_NAME;

		echo $arrayData['page_cover'];

		echo $root;
		echo Template::setTemplate(Template::getTemplate('tp1.html'), $arrayData);
	}




?>
