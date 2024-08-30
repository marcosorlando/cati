<?php

	$AdminLevel = LEVEL_WC_OUVIDORIA;
	if (!APP_OUVIDORIA || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
		die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
	endif;

	// AUTO INSTANCE OBJECT READ
	if (empty($Read)):
		$Read = new Read;
	endif;

	$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
	$O = filter_input(INPUT_GET, "opt", FILTER_DEFAULT);

	$WhereString = (!empty($S) ? " AND (first_name LIKE '%{$S}%' OR last_name LIKE '%{$S}%' OR email LIKE '%{$S}%') " :
		"");
	$WhereOpt = ((!empty($O)) ? " AND sector = '{$O}' " : "");

	$Search = filter_input_array(INPUT_POST);
	if ($Search):
		$S = urlencode($Search['s']);
		$O = urlencode($Search['opt']);
		header("Location: dashboard.php?wc=ouvidoria/home&opt={$O}&s={$S}");
		exit;
	endif;

	$Read->FullRead("SELECT DISTINCT(sector) FROM " . DB_OUVIDORIA . " WHERE status = :st", "st=1");
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-bullhorn">Ouvidoria</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span> Ouvidoria
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos os Setores</option>
				<?php
					$Read->FullRead("SELECT DISTINCT(sector) FROM " . DB_OUVIDORIA . " WHERE status = :st", "st=1");
					if ($Read->getResult()) {
						foreach ($Read->getResult() as $Lista):
							extract($Lista);
							$sectors[] = $Lista['sector'];
						endforeach;
						$sectors = array_unique($sectors, SORT_REGULAR);

						foreach ($sectors as $key => $value) {
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
		$Pager = new Pager("dashboard.php?wc=ouvidoria/home{$RedirectOpt}&page=", "<<", ">>", 5);
		$Pager->ExePager($Page, 12);
		$Read->ExeRead(
			DB_OUVIDORIA,
			" WHERE 1=1 $WhereString $WhereOpt ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
			"limit={$Pager->getLimit()}&offset={$Pager->getOffset()}"
		);

		if (!$Read->getResult()):
			$Pager->ReturnPage();
			echo Erro(
				"<span class='al_center icon-notification'>Não encontramos dados para sua busca. Experimente novamente com filtro de todos os setores.</span>",
				E_USER_NOTICE
			);
		else:
			foreach ($Read->getResult() as $complaint):
				extract($complaint);

			$full_name= !empty($first_name) ? "{$first_name} {$last_name}" : "ANÔNIMO";

				$PdtClass = ($status != 1 ? 'inactive' : '');

				echo "<article class='box box25 panel_header default ouvidoria cv' id='{$id}'>			            
			           <h2 class='icon-bullhorn'> {$full_name}</h2>
			     
			                <div class='info'>
					            <p class='icon-user-check'> <b>{$sector}</b></p>					            
					            <p>{$complaint}</p>					      
			                </div>
			        
				            <div class='actions'> 
				                <a class='icon-mail3 btn btn_green' target='_blank' href='mailto:{$email}?cc=testes@zen.ppg.br&subject=Ouvidoria TRAVI - Resposta de {$sector}&body=Olá!{$complaint}'> RESPONDER</a>               
				               
				                <span rel='ouvidoria' class='j_delete_action icon-cancel-circle btn btn_red' id='{$id}'>Excluir</span>
				                <span rel='ouvidoria' callback='Ouvidoria' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$id}'>Remover?</span>
				            </div>
                    </article>";
			endforeach;

			$Pager->ExePaginator(DB_OUVIDORIA, "WHERE 1 = 1 {$WhereString} {$WhereOpt}");
			echo $Pager->getPaginator();

		endif;
	?>
</div>
