<?php
$AdminLevel = LEVEL_WC_THANKYOU_PAGES;
if (!APP_THANKYOU_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
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

$LeadId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($LeadId):
  $Read->ExeRead(DB_LEADS, "WHERE lead_id = :id", "id={$LeadId}");
  if ($Read->getResult()):
    $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
    extract($FormData);
  else:
    $_SESSION['trigger_controll'] = Erro("<b>OPPSS {$Admin['user_name']}</b>, você tentou editar uma Lead que não existe ou que foi removido recentemente!", E_USER_NOTICE);
    header('Location: dashboard.php?wc=leads/home');
    exit;
  endif;
else:
  $LeadCreate = ['lead_date' => date('Y-m-d H:i:s'), 'lead_conversion' => 'Cadastro Manual'];
  $Create->ExeCreate(DB_LEADS, $LeadCreate);
  header('Location: dashboard.php?wc=leads/create&id=' . $Create->getResult());
  exit;
endif;
?>

<header class="dashboard_header">
  <div class="dashboard_header_title">
    <h1 class="icon-page-break"><?= $lead_name ? $lead_name : 'Novo Lead '; ?></h1>
    <p class="dashboard_header_breadcrumbs">
      &raquo; <?= ADMIN_NAME; ?>
      <span class="crumb">/</span>
      <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
      <span class="crumb">/</span>
      <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=leads/home">Leads</a>
      <span class="crumb">/</span>
      Gerenciar Leads
    </p>
  </div>

  <div class="dashboard_header_search">
    <a title="Nova Página de Cliente" href="dashboard.php?wc=leads/create" class="btn btn_green icon-plus">Novo</a>
    <!--<a target="_blank" title="Ver no site" href="dashboard.php?wc=leads/lead" class="wc_view btn btn_blue icon-eye float-right">Ver Lead</a>-->
  </div>
</header>

<div class="dashboard_content">

  <form class="auto_save" name="lead_add" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="callback" value="Leads"/>
    <input type="hidden" name="callback_action" value="manage"/>                               
    <input type="hidden" name="lead_conversion" value="CADASTRO MANUAL"/>
    <input type="hidden" name="lead_id" value="<?= $LeadId; ?>"/>


    <div class="box box70">

      <div class="panel_header default">
        <h2 class="icon-page-break">Insira os dados do Lead</h2>
      </div>

      <div class="panel">              

        <label class="label">
          <span class="legend">Nome:</span>
          <input style="font-size: 1.4em;" type="text" name="lead_name" value="<?= $lead_name; ?>" placeholder="Nome do lead:" required/>
        </label>
        <label class="label">
          <span class="legend">Email:</span>
          <input style="font-size: 1.4em;" type="text" name="lead_email" value="<?= $lead_email; ?>" placeholder="E-mail do lead:" required/>
        </label>
        <label class="label">
          <span class="legend">Cidade:</span>
          <input style="font-size: 1.4em;" type="text" name="lead_city" value="<?= $lead_city; ?>" placeholder="Nome da Cidade:" required/>
        </label>
        <label class="label">
          <span class="legend">Profissão:</span>
          <input style="font-size: 1.4em;" type="text" name="lead_job_title" value="<?= $lead_job_title; ?>" placeholder="Cargo:" required/>
        </label>               

        <div class="clear"></div>
      </div>
    </div>

    <div class="box box30">

      <div class="panel_header default">
        <h2>Foto (3X4)</h2>
      </div>

      <div class="panel">
        <label class="label">
          <input type="file" class="wc_loadimage" name="lead_thumb"/>
        </label>
        <div class="post_create_cover m_botton">
          <div class="upload_progress none">0%</div>
          <?php
          $LeadThumb = (!empty($lead_thumb) && file_exists("../uploads/leads/{$lead_thumb}") && !is_dir("../uploads/leads/{$lead_thumb}") ? "uploads/leads/{$lead_thumb}" : 'admin/_img/no_image.jpg');
          ?>
          <img class="post_thumb lead_thumb" style="display: block; margin: 0 auto !important;" src="../tim.php?src=<?= $LeadThumb; ?>&w=500&h=auto" default="../tim.php?src=<?= $LeadThumb; ?>&w=500&h=auto"/>
        </div>

        <div class="m_top">&nbsp;</div>
        <div class="wc_actions" style="text-align: center; margin-bottom: 10px;">
          <label class="label_check label_publish <?= ($lead_status == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="lead_status" <?= ($lead_status == 1 ? 'checked' : ''); ?>>
            Cadastrar Lead!</label>
          <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
          <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
        </div>
      </div>
    </div>
  </form>
</div>