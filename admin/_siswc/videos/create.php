<?php
$AdminLevel = 6;
if (!APP_VIDEOS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
$Create = new Create;

$VideoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($VideoId):
    $Read->ExeRead(DB_YOUTUBE, "WHERE video_id = :id", "id={$VideoId}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);
    else:
        $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um video que não existe ou que foi removido recentemente!";
        header('Location: dashboard.php?wc=videos/home');
    endif;
else:
    $VideoCreate = ['video_date' => date('Y-m-d H:i:s'), 'video_start' => date('Y-m-d H:i:s')];
    $Create->ExeCreate(DB_YOUTUBE, $VideoCreate);
    header('Location: dashboard.php?wc=videos/create&id=' . $Create->getResult());
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-youtube2">Cadastrar Vídeo</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=videos/home">Vídeos</a>
            <span class="crumb">/</span>
            Novo Vídeo
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver Vídeos!" href="dashboard.php?wc=videos/home" class="btn btn_blue icon-eye">Ver Videos</a>
        <a title="Novo Vídeo!" href="dashboard.php?wc=videos/create" class="btn btn_green icon-plus">Adicionar Vídeos!</a>
    </div>
</header>

<div class="dashboard_content">
    <form name="post_create" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Videos"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="video_id" value="<?= $VideoId; ?>"/>

        <article class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Título:</span>
                    <input style="font-size: 1.5em;" type="text" name="video_title" value="<?= $video_title; ?>" required/>
                </label>

                <label class="label">
                    <span class="legend">Descrição:</span>
                    <textarea style="font-size: 1.2em;" name="video_desc" rows="3" required><?= $video_desc; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Link: (<?= BASE; ?>/<b>destino</b>)</span>
                    <input style="font-size: 1.2em;" type="text" name="video_link" value="<?= $video_link; ?>" required/>
                </label>

                <fieldset class="label_50">
                    <legend>Veiculação</legend>
                    <label class="label">
                        <span class="legend">A partir de:</span>
                        <input style="font-size: 1.2em;" type="text" class="formTime" name="video_start" value="<?= (!empty($video_start) ? date('d/m/Y H:i:s', strtotime($video_start)) : date('d/m/Y H:i:s')); ?>" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Até dia: (opcional)</span>
                        <input style="font-size: 1.2em;" type="text" class="formTime" name="video_end" value="<?= (!empty($video_end) ? date('d/m/Y H:i:s', strtotime($video_end)) : date('d/m/Y H:i:s', strtotime("+1month"))); ?>"/>
                    </label>
                </fieldset>

                <div class="clear"></div>
            </div>
        </article>
        <aside class="box box30">

           <div class="panel_header default">
               <div class='video_create_cover'>
                   <div class='upload_progress none'>0%</div>
                   <?php
                       $VideoImage = (!empty($video_image) && file_exists("../uploads/{$video_image}") && !is_dir("../uploads/{$video_image}") ? "uploads/{$video_image}" : 'admin/_img/no_image.jpg');
                   ?>
                   <img class="video_image post_cover" alt="Capa" title="Capa"
                        src="../tim.php?src=<?= $VideoImage; ?>&w=<?= VIDEO_W; ?>&h=<?= VIDEO_H; ?>"
                        default="../tim.php?src=<?= $VideoImage; ?>&w=<?= VIDEO_W; ?>&h=<?= VIDEO_H; ?>"/>
               </div>
               <label class='label m_top'>
                   <span class='legend'>Capa: (JPG <?= VIDEO_W; ?>x<?= VIDEO_H; ?>px)</span>
                   <input type="file" class="wc_loadimage" name="video_image"/>
               </label>
           </div>

           <div class="wc_actions panel_header default">

               <button name='public' value='1' class='btn btn_green fl_right icon-share'>Atualizar Vídeo!
                   <img class='form_load' alt='Enviando Requisição!'
                        title='Enviando Requisição!' src='_img/load_w.gif'/>
               </button>
           </div>
        </aside>
    </form>
</div>
