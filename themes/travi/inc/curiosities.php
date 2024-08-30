<?php
	$Read = new Read();
	$Read->ExeRead(DB_CURIOSITIES, "WHERE cur_id = :id AND cur_status = :st","id=1&st=1");

	if ($Read->getResult()) {
		$Data = $Read->getResult()[0];

		$Data['cur_cover'] = BASE . "/uploads/{$Data['cur_cover']}";
		$Data['cur_line_one_icon'] = BASE . "/uploads/{$Data['cur_line_one_icon']}";
		$Data['cur_line_two_icon'] = BASE . "/uploads/{$Data['cur_line_two_icon']}";
		$Data['cur_line_three_icon'] = BASE . "/uploads/{$Data['cur_line_three_icon']}";
		$Data['cur_line_four_icon'] = BASE . "/uploads/{$Data['cur_line_four_icon']}";

		$view = new wcView(__DIR__ . '/_tpl');

		$View = $view->wcLoad('curiosities');
		$view->wcShow($Data, $View);
	} else {
		echo '';
	}

?>
