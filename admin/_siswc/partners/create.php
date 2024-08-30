<?php
$AdminLevel = LEVEL_WC_PARTNERS;
if (!APP_PARTNERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
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

$PartnerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($PartnerId):
  $Read->ExeRead(DB_PARTNERS, "WHERE partner_id = :id", "id={$PartnerId}");
  if ($Read->getResult()):
    $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
    extract($FormData);
  else:
    $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um parceiro que não existe ou que foi removido recentemente!";
    header('Location: dashboard.php?wc=partners/home');
  endif;
else:
  $PartnerCreate = ['partner_name' => '', 'partner_page' => ''];
  $Create->ExeCreate(DB_PARTNERS, $PartnerCreate);
  header('Location: dashboard.php?wc=partners/create&id=' . $Create->getResult());
endif;
?>

<header class="dashboard_header">
  <div class="dashboard_header_title">
    <h1 class="icon-pen"><?= $partner_name ? $partner_name : 'Novo Parceiro'; ?></h1>
    <p class="dashboard_header_breadcrumbs">
      &raquo; <?= ADMIN_NAME; ?>
      <span class="crumb">/</span>
      <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
      <span class="crumb">/</span>
      <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=partners/home">Parceiros</a>
      <span class="crumb">/</span>
      Gerenciar Parceiro
    </p>
  </div>

  <div class="dashboard_header_search">
    <a title="Ver Parceiros" href="dashboard.php?wc=partners/home" class="btn btn_blue icon-eye">Ver Parceiro</a>
    <a title="Novo Parceiro" href="dashboard.php?wc=partners/create" class="btn btn_green icon-plus">Adicionar Parceiro</a>
  </div>
</header>

<div class="dashboard_content">
  <form name="partner_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="callback" value="Partners"/>
    <input type="hidden" name="callback_action" value="manager"/>
    <input type="hidden" name="partner_id" value="<?= $PartnerId; ?>"/>

    <div class="box box70">
      <div class="box_content">
        <label class="label">
          <span class="legend">Nome do parceiro:</span>
          <input style="font-size: 1.4em;" type="text" name="partner_name" value="<?= $partner_name; ?>" placeholder="Nome do Parceiro:" required/>
        </label>
        <label class="label">
          <span class="legend">Site do parceiro:</span>
          <input style="font-size: 1.4em;" type="text" name="partner_page" value="<?= $partner_page; ?>" placeholder="Site do Parceiro:" required/>
        </label>
        <label class="label border_top">
          <span class="legend">Foto: (JPG 300X200px):</span>
          <input type="file" class="wc_loadimage" name="partner_image" />
        </label>
        <div class="clear"></div>
      </div>
    </div>
    <div class="box box30">
      <div class="box_content">
        <?php
        $Image = (file_exists("../uploads/{$partner_image}") && !is_dir("../uploads/{$partner_image}") ? "uploads/{$partner_image}" : 'admin/_img/no_image.jpg');
        ?>
        <img class="partner_image" src="../tim.php?src=<?= $Image; ?>&w=300&h=200" default="../tim.php?src=<?= $Image; ?>&w=300&h=200">
        <div class="box_content">
          <div class="m_top">&nbsp;</div>
          <div class="wc_actions" style="text-align: center">
            <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
            <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
          </div>
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </form>
</div>