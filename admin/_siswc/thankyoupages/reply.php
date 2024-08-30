<?php
    /**
     * Created by PhpStorm.
     * User: marcosmoreira
     * Date: 11/04/19
     * Time: 15:17
     */
    
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

// AUTO INSTANCE OBJECT UPDATE
if (empty($Update)):
    $Update = new Update;
endif;

$PageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$Read->ExeRead(DB_THANKYOU_PAGES, "WHERE page_id = :id", "id={$PageId}");
$PageCreate = $Read->getResult()[0];

unset($PageCreate['page_id'], $PageCreate['page_pdf']);

$PageCreate['page_parent'] = $PageId;
$PageCreate['page_date'] = date('Y-m-d H:i:s');
$PageCreate['page_status'] = 0;

$Create->ExeCreate(DB_THANKYOU_PAGES, $PageCreate);

$PAGECRTUPDATE = ['page_name' => Check::Name($PageCreate['page_title']) . "-{$Create->getResult()}"];
$Update->ExeUpdate(DB_THANKYOU_PAGES, $PAGECRTUPDATE, "WHERE page_id = :id", "id={$Create->getResult()}");

header("Location: dashboard.php?wc=thankyoupages/create&id={$Create->getResult()}");
exit;
