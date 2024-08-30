<?php
$AdminLevel = 6;
if (!APP_VIDEOS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_YOUTUBE, "WHERE video_image IS NULL AND video_title IS NULL AND video_id >= :st", "st=1");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-youtube">Vídeos em Destaque</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=end">Dashboard</a>
            <span class="crumb">/</span>
            Vídeos
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Novo Vídeo" href="dashboard.php?wc=videos/create" class="btn btn_green icon-plus">Adicionar Vídeo!</a>
    </div>
</header>
<div class="dashboard_content">
    <?php
    $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $Page = ($getPage ? $getPage : 0);
    $Pager = new Pager('dashboard.php?wc=videos/end&page=', "<<", ">>", 3);
    $Pager->ExePager($Page, 4);
    $Read->ExeRead(DB_YOUTUBE, "WHERE video_start <= NOW() AND video_end < NOW() ORDER BY video_date DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
    if (!$Read->getResult()):
        $Pager->ReturnPage();
        Erro("<span class='al_center icon-notification'>Ainda não existe vídeos inativos em seu site.</span>", E_USER_NOTICE);
   else:
		?>
		<div class="testimony">
			<div class="testimony_content">
				<span class="testimony_close">X</span>
				<h1>Assistir Vídeo:</h1>
				<div class="embed-container"></div>
				<p style='font-size: 0.8em; margin: 10px 0 20px 20px;'>Descrição:</p>
				<div class="content_like">
					<div class="box_like">

					</div>
				</div>
			</div>
		</div>
		<?php
        foreach ($Read->getResult() as $Video):
            extract($Video);
		$video_title = Check::Chars($video_title, 72);
		$Capa = $video_image == null ? "http://i1.ytimg.com/vi/{$video_link}/0.jpg" : BASE . "/tim.php?src=uploads/{$video_image}&w=".VIDEO_W."&h=".VIDEO_H;
            echo "<article class='box box25 video_single' id='{$video_id}'>							
					<article id='{$video_link}' class='lead_take testimony_start'>
						<header>
							<h1>{$video_title}</h1>
						</header>
						<div class='thumb'>
							<img src='{$Capa}' title='{$video_title}}' alt='{$video_title}'/>
							<div class='false_bg take_play'></div>
						</div>                
						<p style='font-size: 0.8em; margin: 10px 0 20px 0;'><b>De " . date('d/m/Y H\hi', strtotime($video_start)) . " - " . ($video_end ? date('d/m/Y H\hi', strtotime($video_end)) : 'Sempre') . "</b><br> {$video_desc}</p>						
					</article>	   
			
                    <a title='Editar Vídeo' href='dashboard.php?wc=videos/create&id={$video_id}' class='icon-notext icon-pencil btn btn_blue'></a>
                    <span rel='video_single' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$video_id}'></span>
                    <span rel='video_single' callback='Videos' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$video_id}'>Deletar Vídeo?</span>           
				</article>";
        endforeach;

        $Pager->ExePaginator(DB_YOUTUBE, "WHERE video_start <= NOW() AND (video_end >= NOW() OR video_end IS NULL)");
        echo $Pager->getPaginator();

    endif;
    ?>
</div>
<script src="<?= BASE; ?>/themes/wc_conversion/scripts.js"></script>