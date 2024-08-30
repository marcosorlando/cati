<?php

session_start();
require '../../_app/Config.inc.php';
$NivelAcess = LEVEL_WC_ALBUMS;

if (!APP_ALBUMS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
	$jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
	echo json_encode($jSON);
	die;
endif;

usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Albums';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] == $CallBack):
	//PREPARA OS DADOS
	$Case = $PostData['callback_action'];
	unset($PostData['callback'], $PostData['callback_action']);

	// AUTO INSTANCE OBJECT READ
	if (empty($Read)):
		$Read = new Read;
	endif;

	// AUTO INSTANCE OBJECT CREATE
	if (empty($Create)):
		$Create = new Create;
	endif;

	// AUTO INSTANCE OBJECT UPDATE
	if (empty($Update)):
		$Update = new Update;
	endif;

	// AUTO INSTANCE OBJECT DELETE
	if (empty($Delete)):
		$Delete = new Delete;
	endif;
	$Upload = new Upload('../../uploads/albuns/');

	//var_dump($PostData);
	//SELECIONA AÇÃO
	switch ($Case):
		case 'manager':
			$AlbId = $PostData['album_id'];
			$PostData['album_status'] = (!empty($PostData['album_status']) ? $PostData['album_status'] : '0');
			$Read->ExeRead(DB_ALBUMS, "WHERE album_id = :id", "id={$AlbId}");
			if (!$Read->getResult()):
				$jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar o album. Experimente atualizar a página!", E_USER_WARNING);
			elseif (!empty($PostData['album_start']) && (!Check::Data($PostData['album_start']) || !Check::Data($PostData['album_end']))):
				$jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas a(s) data(s) de divulgação foi informada com erro de calendário. Veja isso!", E_USER_WARNING);
			else:
				$Album = $Read->getResult()[0];
				//var_dump($Album);
				unset($PostData['album_id'], $PostData['album_cover'], $PostData['image']);

				$PostData['album_name'] = (!empty($PostData['album_name']) ? Check::Name($PostData['album_name']) : Check::Name($PostData['album_title']));

				//ENVIO DA CAPA DO ALBUM
				if (!empty($_FILES['album_cover'])):
					$File = $_FILES['album_cover'];

					if ($Album['album_cover'] && file_exists("../../uploads/albuns/{$Album['album_cover']}") && !is_dir("../../uploads/albuns/{$Album['album_cover']}")):
						unlink("../../uploads/albuns/{$Album['album_cover']}");
					endif;

					$Upload->Image($File, "{$AlbId}-{$PostData['album_name']}-" . time(), 1000);
					if ($Upload->getResult()):
						$PostData['album_cover'] = $Upload->getResult();
					else:
						$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 1200x628px para a capa!", E_USER_WARNING);
						echo json_encode($jSON);
						return;
					endif;
				endif;

				//ENVIO DAS IMAGENS DO ALBUM
				if (!empty($_FILES['image'])):
					$File = $_FILES['image'];
					$gbFile = array();
					$gbCount = count($File['type']);
					$gbKeys = array_keys($File);
					$gbLoop = 0;

					for ($gb = 0; $gb < $gbCount; $gb++):
						foreach ($gbKeys as $Keys):
							$gbFiles[$gb][$Keys] = $File[$Keys][$gb];
						endforeach;
					endfor;

					$jSON['gallery'] = null;
					foreach ($gbFiles as $UploadFile):
						$gbLoop ++;
						$Upload->Image($UploadFile, "{$AlbId}-{$gbLoop}-" . time() . base64_encode(time()), 1000);
						if ($Upload->getResult()):
							$gbCreate = ['album_id' => $AlbId, "image" => $Upload->getResult()];
							$Create->ExeCreate(DB_ALBUMS_IMAGE, $gbCreate);
							$jSON['gallery'] .= "<img rel='Albums' id='{$Create->getResult()}' alt='Imagem em {$PostData['album_title']}' title='Imagem em {$PostData['album_title']}' src='../tim.php?src=uploads/albuns/{$Upload->getResult()}&w=1200&h=628'/>";
//								../tim.php?src=uploads/albuns/{$Upload->getResult()}&w=1200&h=628	
//							../uploads/albuns/{$Upload->getResult()}
						endif;
					endforeach;
				endif;

				if (isset($PostData['album_subcategory'])):
					$Read->FullRead("SELECT presential_cat_parent FROM " . DB_PRESENTIAL_CATEGORIES . " WHERE presential_cat_id = :id", "id={$PostData['album_subcategory']}");
					$PostData['album_category'] = ($Read->getResult() ? $Read->getResult()[0]['presential_cat_parent'] : null);
				endif;

				$Read->FullRead("SELECT album_id FROM " . DB_ALBUMS . " WHERE album_name = :nm AND album_id != :id", "nm={$PostData['album_name']}&id={$AlbId}");
				if ($Read->getResult()):
					$PostData['album_name'] = "{$PostData['album_name']}-{$AlbId}";
				endif;

				$PostData['album_start'] = (!empty($PostData['album_start']) && Check::Data($PostData['album_start']) ? Check::Data($PostData['album_start']) : null);
				$PostData['album_end'] = (!empty($PostData['album_end']) && Check::Data($PostData['album_end']) ? Check::Data($PostData['album_end']) : null);

				$PostData['album_status'] = (!empty($PostData['album_status']) ? '1' : '0');

				$Update->ExeUpdate(DB_ALBUMS, $PostData, "WHERE album_id = :id", "id={$AlbId}");

				$jSON['name'] = $PostData['album_name'];
				$jSON['trigger'] = AjaxErro("<span class='icon-checkmark'><b>ÁLBUM ATUALIZADO:</b> Olá {$_SESSION['userLogin']['user_name']}. O álbum {$PostData['album_title']} foi atualizado com sucesso!<span>");

			endif;
			break;

		case 'sendimage':
			$NewImage = $_FILES['image'];
			$Read->FullRead("SELECT album_title, album_name FROM " . DB_ALBUMS . " WHERE album_id = :id", "id={$PostData['album_id']}");
			if (!$Read->getResult()):
				$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o álbum vinculado!", E_USER_WARNING);
			else:
				$Upload = new Upload('../../uploads/albuns/');
				$Upload->Image($NewImage, $PostData['album_id'] . '-' . time(), IMAGE_W);
				if ($Upload->getResult()):
					$PostData['album_id'] = $PostData['album_id'];
					$PostData['image'] = $Upload->getResult();
					unset($PostData['album_id']);

					$Create->ExeCreate(DB_ALBUMS_IMAGE, $PostData);
					$jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['album_title']}' alt='{$Read->getResult()[0]['album_title']}' src='../uploads/albuns/{$PostData['image']}'/>";
				else:
					$jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no álbum!", E_USER_WARNING);
				endif;
			endif;
			break;

		case 'removeimage':
			$Read->FullRead("SELECT image FROM " . DB_PDT_GALLERY . " WHERE id = :id", "id={$PostData['img']}");
			if ($Read->getResult()):
				$ImageRemove = "../../uploads/albuns/{$Read->getResult()[0]['image']}";
				if (file_exists($ImageRemove) && !is_dir($ImageRemove)):
					unlink($ImageRemove);
				endif;
				$Delete->ExeDelete(DB_ALBUMS_IMAGE, "WHERE id = :id", "id={$PostData['img']}");
				$jSON['success'] = true;
			endif;
			break;

		case 'delete':
			$AlbId = $PostData['del_id'];
//            $Read->FullRead("SELECT album_id FROM " . DB_ORDERS_ITEMS . " WHERE album_id = :id", "id={$AlbId}");
//            $AlbOrder = $Read->getResult();

			$Read->ExeRead(DB_ALBUMS, "WHERE album_id = :id", "id={$AlbId}");

			if (!$Read->getResult()):
				$jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois o produto não existe ou foi removido recentemente!", E_USER_WARNING);
			else:
				$Album = $Read->getResult()[0];
				$AlbCover = "../../uploads/{$Album['album_cover']}";

				if (file_exists($AlbCover) && !is_dir($AlbCover)):
					unlink($AlbCover);
				endif;

				$Read->ExeRead(DB_ALBUMS_IMAGE, "WHERE album_id = :id", "id={$Album['album_id']}");
				if ($Read->getResult()):
					foreach ($Read->getResult() as $AlbImage):
						$AlbImageIs = "../../uploads/albuns/{$AlbImage['image']}";
						if (file_exists($AlbImageIs) && !is_dir($AlbImageIs)):
							unlink($AlbImageIs);
						endif;
					endforeach;
					$Delete->ExeDelete(DB_ALBUMS_IMAGE, "WHERE album_id = :id", "id={$Album['album_id']}");
				endif;

//                $Read->ExeRead(DB_PDT_GALLERY, "WHERE product_id = :id", "id={$Album['album_id']}");
//                if ($Read->getResult()):
//                    foreach ($Read->getResult() as $AlbGB):
//                        $AlbGBImage = "../../uploads/{$AlbGB['image']}";
//                        if (file_exists($AlbGBImage) && !is_dir($AlbGBImage)):
//                            unlink($AlbGBImage);
//                        endif;
//                    endforeach;
//                    $Delete->ExeDelete(DB_PDT_GALLERY, "WHERE product_id = :id", "id={$Album['album_id']}");
//                endif;

				$Delete->ExeDelete(DB_ALBUMS, "WHERE album_id = :id", "id={$Album['album_id']}");

				$jSON['success'] = true;
			endif;
			break;
	endswitch;

	//RETORNA O CALLBACK
	if ($jSON):
		echo json_encode($jSON);
	else:
		$jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
		echo json_encode($jSON);
	endif;
else:
	//ACESSO DIRETO
	die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;
