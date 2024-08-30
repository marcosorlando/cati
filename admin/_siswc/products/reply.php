<?php

    $AdminLevel = LEVEL_WC_PRODUCTS_TRAVI;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
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

    $PdtId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $Read->ExeRead(DB_PDT_TRAVI, "WHERE pdt_id = :id", "id={$PdtId}");
    $ProductCreate = $Read->getResult()[0];

//var_dump($ProductCreate);

    unset($ProductCreate['pdt_id'], $ProductCreate['pdt_cover'], $ProductCreate['pdt_scene']);

    $ProductCreate['pdt_parent'] = $PdtId;
    $ProductCreate['pdt_created'] = date('Y-m-d H:i:s');
    $ProductCreate['pdt_status'] = 0;
    $Create->ExeCreate(DB_PDT_TRAVI, $ProductCreate);

    $PDTCRTUPDATE = [
        'pdt_name' => Check::Name($ProductCreate['pdt_title']) . "-{$Create->getResult()}",
           ];
    $Update->ExeUpdate(DB_PDT_TRAVI, $PDTCRTUPDATE, "WHERE pdt_id = :id", "id={$Create->getResult()}");

    header("Location: dashboard.php?wc=products/create&id={$Create->getResult()}");
    exit;
