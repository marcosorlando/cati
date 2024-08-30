<?php
$AdminLevel = LEVEL_WC_ALBUMS;
if (!APP_ALBUMS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
	die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE PRODUCT TRASH
if (DB_AUTO_TRASH):
	$Delete = new Delete;
	$Delete->ExeDelete(DB_ALBUMS, "WHERE album_title IS NULL AND album_subtitle IS NULL and album_status = :st", "st=0");

	//AUTO TRASH IMAGES
	$Read->FullRead("SELECT image FROM " . DB_ALBUMS_IMAGE . " WHERE album_id NOT IN(SELECT album_id FROM " . DB_ALBUMS . ")");
	if ($Read->getResult()):
		$Delete->ExeDelete(DB_ALBUMS_IMAGE, "WHERE id >= :id AND album_id NOT IN(SELECT album_id FROM " . DB_ALBUMS . ")", "id=1");
		foreach ($Read->getResult() as $ImageRemove):
			if (file_exists("../uploads/albuns/{$ImageRemove['image']}") && !is_dir("../uploads/albuns/{$ImageRemove['image']}")):
				unlink("../uploads/albuns/{$ImageRemove['image']}");
			endif;
		endforeach;
	endif;
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
	$Read = new Read;
endif;

// AUTO INSTANCE OBJECT CREATE
if (empty($Create)):
	$Create = new Create;
endif;

$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
$O = filter_input(INPUT_GET, "opt", FILTER_DEFAULT);

$WhereString = (!empty($S) ? " AND (album_title LIKE '%{$S}%' OR album_subtitle LIKE '%{$S}%') " : "");
$WhereOpt = ((!empty($O)) ? ($O == '#') ? " AND album_status != 1 " : " AND album_subcategory = {$O} " : "");



$Search = filter_input_array(INPUT_POST);
if ($Search):
	$S = urlencode($Search['s']);
	$O = urlencode($Search['opt']);
	header("Location: dashboard.php?wc=albums/home&opt={$O}&s={$S}");
//header("Location: dashboard.php?wc=albums/home&s={$S}");
endif;
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-books">Álbuns</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Álbuns
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;" />
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos</option>

				<?php
				$Read->FullRead("SELECT presential_cat_id, presential_cat_title FROM " . DB_PRESENTIAL_CATEGORIES . " WHERE presential_cat_id > :cat", "cat=0");

				if ($Read->getResult()):
					foreach ($Read->getResult() as $Categories) :
						extract($Categories);
						echo "<option value='{$presential_cat_id}'>{$presential_cat_title}</option>";

					endforeach;
				endif;
				?>
				<option value="#">Indisponíveis</option>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
	
	<?php
	
	$Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
	$Pager = new Pager("dashboard.php?wc=albums/home&page=", "<<", ">>", 5);
	$Pager->ExePager($Page, 12);
	$Read->ExeRead(DB_ALBUMS, "WHERE 1 = 1 $WhereString $WhereOpt ORDER BY album_created DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
	if (!$Read->getResult()):
		$Pager->ReturnPage();
		echo Erro("<span class='al_center icon-notification'>Ainda não existem álbuns cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro álbum!</span>", E_USER_NOTICE);
	else:
		foreach ($Read->getResult() as $Albums):
			extract($Albums);
			$AlbImage = ($album_cover && file_exists("../uploads/albuns/{$album_cover}") && !is_dir("../uploads/albuns/{$album_cover}") ? "uploads/albuns/{$album_cover}" : 'admin/_img/no_image.jpg');
			$AlbTitle = ($album_title ? Check::Chars($album_title, 45) : 'Edite este álbum para coloca-lo a Website!');
			$AlbClass = ($album_status != 1 ? 'inactive' : '');

			echo "<article class='box box25 single_pdt {$AlbClass}' id='{$album_id}'>
            <div class='single_pdt_thumb'>
            <img title='{$AlbTitle}' alt='{$AlbTitle}' src='../tim.php?src={$AlbImage}&w=1200&h=628'/>
                <header>
                  <h1><a target='_blank' href='" . BASE . "/album/{$album_name}' title='Ver {$AlbTitle} no site'>{$AlbTitle}</a></h1>";

			$Read->FullRead("SELECT presential_cat_title FROM " . DB_PRESENTIAL_CATEGORIES . " WHERE presential_cat_id = :cat", "cat={$album_category}");
			$Category = ($Read->getResult() ? $Read->getResult()[0]['presential_cat_title'] : 'indefinida');

			$Read->FullRead("SELECT presential_cat_title FROM " . DB_PRESENTIAL_CATEGORIES . " WHERE presential_cat_id = :cat", "cat={$album_subcategory}");
			$SubCategory = ($Read->getResult() ? $Read->getResult()[0]['presential_cat_title'] : 'indefinida');

			echo "</header>
            </div>
            <div class='box_content'>
                <div class='single_pdt_info wc_normalize_height'>
                    <p>Em: <b>{$Category}</b> &raquo; <b>{$SubCategory}</b></p>
                </div>
            </div>
            <div class='single_pdt_actions'>
                <a title='Editar álbum' href='dashboard.php?wc=albums/create&id={$album_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$album_id}'>Excluir</span>
                <span rel='single_pdt' callback='Albums' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$album_id}'>Remover Álbum?</span>
            </div>
        </article>";
		endforeach;

		$Pager->ExePaginator(DB_ALBUMS);
		echo $Pager->getPaginator();

	endif;
	?>
</div>