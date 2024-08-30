<?php

	$AdminLevel = LEVEL_WC_CV;
	if (!APP_CV || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
		die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
	endif;

	// AUTO INSTANCE OBJECT READ
	if (empty($Read)):
		$Read = new Read;
	endif;

	$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
	$O = filter_input(INPUT_GET, "opt", FILTER_DEFAULT);

	$WhereString = (!empty($S) ? " AND (full_name LIKE '%{$S}%' OR email LIKE '%{$S}%') " : "");
	$WhereOpt = ((!empty($O)) ? " AND area = '{$O}' " : "");

	$Search = filter_input_array(INPUT_POST);
	if ($Search):
		$S = urlencode($Search['s']);
		$O = urlencode($Search['opt']);
		header("Location: dashboard.php?wc=curriculos/home&opt={$O}&s={$S}");
		exit;
	endif;

	$Read->FullRead("SELECT DISTINCT(area) FROM " . DB_CV . " WHERE status = :st", "st=1");
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-books">Currículos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span> Currículos
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todas as áreas</option>
				<?php
					$Read->FullRead("SELECT DISTINCT(area) FROM " . DB_CV . " WHERE status = :st", "st=1");
					if ($Read->getResult()) {
						foreach ($Read->getResult() as $Lista):
							extract($Lista);
							$areas[] = $Lista['area'];
						endforeach;
						$areas = array_unique($areas, SORT_REGULAR);

						foreach ($areas as $key => $value) {
							echo "<option value='{$value}'>{$value}</option>";
						}
					}
				?>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
	<?php
		$RedirectOpt = (!empty($WhereOpt) ? "&opt=outsale" : "");
		$Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
		$Pager = new Pager("dashboard.php?wc=curriculos/home{$RedirectOpt}&page=", "<<", ">>", 5);
		$Pager->ExePager($Page, 12);
		$Read->ExeRead(
			DB_CV,
			" WHERE 1=1 $WhereString $WhereOpt ORDER BY date_created DESC LIMIT :limit OFFSET :offset",
			"limit={$Pager->getLimit()}&offset={$Pager->getOffset()}"
		);

		if (!$Read->getResult()):
			$Pager->ReturnPage();
			echo Erro(
				"<span class='al_center icon-notification'>Não encontramos nenhum currículo para sua busca. Experimente novamente com filtro de todas as áreas.</span>",
				E_USER_NOTICE
			);
		else:
			foreach ($Read->getResult() as $Candidates):
				extract($Candidates);
				$pdfFile = ($cv_pdf && file_exists("../uploads/{$cv_pdf}") && !is_dir(BASE . "/uploads/{$cv_pdf}") ?
					BASE . "/uploads/{$cv_pdf}" : '');

				$PdtClass = ($status != 1 ? 'inactive' : '');

				echo "<article class='box box25 panel_header cv' id='{$id}'>			            
			           <h2 class='icon-user-tie'> {$full_name}</h2>
			     
			                <div class='info'>
					            <p class='icon-user-check'> <b>{$area}</b></p>
					            
					            <p><b><a class='icon-mail3' target='_blank' href='mailto:{$email}'>{$email}</a></b></b></p>
					            
					            <a target='_blank' class='icon-file-pdf btn btn_blue' href='$pdfFile'> VER CURRÍCULO PDF</a>
			                </div>
			        
				            <div class='actions'> 
				                <a class='icon-whatsapp btn btn_green' target='_blank' href='https://wa.me/{$phone}'> CHAMAR NO WHATS</a>               
				               
				                <span rel='cv' class='j_delete_action icon-cancel-circle btn btn_red' id='{$id}'>Excluir</span>
				                <span rel='cv' callback='Curriculos' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$id}'>Remover CV?</span>
				            </div>
                    </article>";
			endforeach;

			$Pager->ExePaginator(DB_CV, "WHERE 1 = 1 {$WhereString} {$WhereOpt}");
			echo $Pager->getPaginator();

		endif;
	?>
</div>
