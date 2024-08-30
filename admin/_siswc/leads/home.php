<?php
$AdminLevel = LEVEL_WC_LANDING_PAGES;
if (!APP_LANDING_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
  die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
  $Delete = new Delete;
  $Delete->ExeDelete(DB_LEADS, "WHERE lead_name IS NULL AND lead_email IS NULL and lead_status = :st", "st=0");

endif;
?>
<div class="box box100">
<header class="header_pink">
    <h1 class="icon-books">BASE DE LEADS:
        <a class="icon-download btn btn_yellow fl_right" style="margin-top: -5px;" href="<?= INCLUDE_PATH ?>/lp/excell-export.php?a=<?= $Admin['user_level']; ?>" title="Clique para Salvar em Excel">Download</a>
        <a class="icon-user-plus btn btn_green fl_right" style="margin-top: -5px; margin-right: 5px" href="dashboard.php?wc=leads/create" title="Novo Lead">Inserir Lead</a>
    </h1>
</header>
<div class="panel dashboard_search">

    <article class="lead_header">
        <p>NOME</p>
        <p>DATA</p>
        <p>E-MAIL</p>
        <p>ID CONVERSÃO</p>
        <p>

        </p>
    </article>
    <?php
        $Read->ExeRead(DB_LEADS, "ORDER BY lead_date DESC");
        if (!$Read->getResult()):
            echo Erro("<span class='icon-info al_center'>Ainda não existem Leads cadastrados!</span>", E_USER_NOTICE);
            echo "<div class='clear'></div>";
        else:
            foreach ($Read->getResult() as $Lead):
                extract($Lead);
                echo " <article>
                    <h1 class='icon-eye'><a href='#' title='Ver detalhes'>{$lead_name}</a></h1>
                      <p>DIA " . date('d/m/Y H\hi', strtotime($lead_date)) . "</p>
                        <p><a href='mailto:{$lead_email}' title='Enviar e-mail para esse lead'>{$lead_email}</a></p>
                               <p>{$lead_conversion}</p>
                               <p>                            
                                    <a href='dashboard.php?wc=leads/create&id={$lead_id}' class='btn btn_blue icon-notext icon-pen wc_tooltip'>
                                      <span class='wc_tooltip_balloon'>Editar</span></button>
                                      </a>
                                    <button class='btn btn_red icon-notext icon-cross wc_tooltip j_wc_action' data-callback='Leads' data-callback-action='delete' data-value='{$lead_id}'><span class='wc_tooltip_balloon'>Deletar</span></button>
                                </p>
                            </article>
                        ";
            endforeach;
        endif;
    ?>
    <!--<a class="dashboard_searchnowlink" href="dashboard.php?wc=searchnow" title="Ver Mais">MAIS PESQUISAS!</a>-->
    <div class="clear"></div>
</div>
</div>