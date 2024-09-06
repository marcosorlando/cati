<?php

    /* BANCO DE DADOS */
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('SIS_DB_HOST', 'localhost'); //Link do banco de dados no localhost
        define('SIS_DB_USER', 'root'); //Usuário do banco de dados no localhost
        define('SIS_DB_PASS', 'root'); //Senha  do banco de dados no localhost
        define('SIS_DB_DBSA', 'cati'); //Nome  do banco de dados no localhost
    } else {
        define('SIS_DB_HOST', 'localhost'); //Link do banco de dados no servidor
        define('SIS_DB_USER', 'catianezanotto31_root'); //Usuário do banco de dados no servidor
        define('SIS_DB_PASS', 'e*,bYGlj9MiP'); //Senha  do banco de dados no servidor
        define('SIS_DB_DBSA', 'catianezanotto31_db'); //Nome  do banco de dados no servidor
    }
    /*
     * CACHE E CONFIG
     */
    define('SIS_CACHE_TIME', 10); //Tempo em minutos de sessão
    define('SIS_CONFIG_WC', 1); //Registrar configurações no banco para gerenciar pelo painel!
    /*
     * AUTO MANAGER
     */
    define('DB_AUTO_TRASH', 1); //Remove todos os itens não gerenciados do banco!
    define('DB_AUTO_PING', 0); //Tenta enviar 1x por dia o sitemap e o RSS para o Google/Bing
    /*
     * TABELAS
     */
    define('DB_CONF', 'ws_config'); //Tabela de Configurações
    define('DB_USERS', 'ws_users'); //Tabela de usuários
    define('DB_USERS_ADDR', 'ws_users_address'); //Tabela de endereço de usuários
    define('DB_USERS_NOTES', 'ws_users_notes'); //Tabela de notas do usuário
    define('DB_POSTS', 'ws_posts'); //Tabela de posts
    define('DB_POSTS_IMAGE', 'ws_posts_images'); //Tabela de imagens de post
    define('DB_CATEGORIES', 'ws_categories'); //Tabela de categorias de posts
    define('DB_SEARCH', 'ws_search'); //Tabela de pesquisas
    define('DB_PAGES', 'ws_pages'); //Tabela de páginas
    define('DB_PAGES_IMAGE', 'ws_pages_images'); //Tabela de imagens da página
    define('DB_COMMENTS', 'ws_comments'); //Tabela de Comentários
    define('DB_COMMENTS_LIKES', 'ws_comments_likes'); //Tabela GOSTEI dos Comentários
    define('DB_HELLO', 'ws_hellobar'); //Tabela da wc hello bar
    define('DB_SLIDES', 'ws_slides'); //Tabela de conteúdo em destaque
    define('DB_VIEWS_VIEWS', 'ws_siteviews_views'); //Controle de acesso ao site
    define('DB_VIEWS_ONLINE', 'ws_siteviews_online'); //Controle de usuários online
    define('DB_WC_API', 'workcontrol_api'); //Controle de api do WC
    define('DB_WC_CODE', 'workcontrol_code'); //Controle de code de WC
    /*
     * CUSTOM DBSA
     */
    define('DB_YOUTUBE', 'ws_youtube'); //Tabela de vídeos Youtube
    define('DB_MATERIAIS', 'ws_materiais'); //Tabela de Materiais Ricos
    define('DB_MATCATEGORIES', 'ws_matcategories'); //Tabela de Categorias Materiais Ricos
    define('DB_LEADS', 'ws_leads'); //Tabela de Leads Capturados pelos Forms - Newsletter do Site
    define('DB_ALBUMS', 'ws_albums'); //Tabela de Albuns de Fotos
    define('DB_ALBUMS_IMAGE', 'ws_albums_images'); //Tabela de Fotos dos Albuns
    define('DB_DEPOSITIONS', 'ws_depositions'); //Tabela de Depoimentos de Clientes
    define('DB_PARTNERS', 'ws_partners'); //Tabela de Parceiros
    define('DB_LANDING_PAGES', 'ws_landingpages'); //Tabela de Landing Pages
    define('DB_LANDING_PAGES_IMAGES', 'ws_landingpages_images'); //Tabela de Images no Corpo da LandingPages
    define('DB_THANKYOU_PAGES', 'ws_thankyoupages'); //Tabela de ThankYouPages
    //PRODUTOS TRAVI
    define('DB_PDT_TRAVI', 'ws_products_travi'); //Tabela de produtos
    define('DB_PDT_IMAGE_TRAVI', 'ws_products_images_travi'); //Tabela de imagem de produtos
    define('DB_PDT_IMAGE_CAT_TRAVI', 'ws_products_images_cat_travi'); //Tabela de imagem de Categorias
    define('DB_PDT_GALLERY_TRAVI', 'ws_products_gallery_travi'); //Tabela de galeria de produtos
    define('DB_PDT_CATS_TRAVI', 'ws_products_categories_travi'); //Tabela de categorias de produtos
    define('DB_PDT_PROCESS_TRAVI', 'ws_products_processes_travi'); //Tabela de processos de produtos
    define('DB_PDT_FORMAT_TRAVI', 'ws_products_formats_travi'); //Tabela de formatos de produtos
    //SERVICES TRAVI
    define('DB_SVC', 'ws_services'); //Tabela de serviços
    define('DB_SVC_IMAGE', 'ws_services_images'); //Tabela de imagem de serviços
    define('DB_SVC_GALLERY', 'ws_services_gallery'); //Tabela de galeria de serviços
    //SEGMENTS TRAVI
    define('DB_SEG', 'ws_segments'); //Tabela de segmentos
    define('DB_SEG_IMAGE', 'ws_segments_images'); //Tabela de imagens de segmentos
    define('DB_SEG_GALLERY', 'ws_segments_gallery'); //Tabela de galeria de segmentos
    //CERTIFICATIONS TRAVI
    define('DB_CERT', 'ws_certifications'); //Tabela de certificações
    define('DB_CERT_IMAGE', 'ws_certifications_images'); //Tabela de images
    //REPRESENTANTES TRAVI
    define('DB_REPRESENTATIVES', 'ws_representatives'); //Tabela de representantes
    define('DB_STATES', 'states'); //Tabela de estados - UF
    define('DB_CITIES', 'cities'); //Tabela de cidades
	define('DB_CURIOSITIES', 'ws_curiosities'); //Tabela Curiosidades da Travi
	define('DB_CV', 'ws_job_candidates'); //Tabela Currículos
	define('DB_OUVIDORIA', 'ws_ouvidoria'); //Tabela Ouvidoria
	define('DB_VOLUNTEERS', 'ws_volunteers'); //Tabela Valuntários
	define('DB_CONTACTS', 'ws_contacts'); //Tabela Valuntários
    /*
      AUTO LOAD DE CLASSES
     */
    function MyAutoLoad($Class)
    {
        $cDir = ['Conn', 'Helpers', 'Models', 'WorkControl'];
        $iDir = null;

        foreach ($cDir as $dirName) {
            if (!$iDir && file_exists(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php') && !is_dir(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php')) {
                include_once(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php');
                $iDir = true;
            }
        }
    }

    spl_autoload_register("MyAutoLoad");

    /*
     * Define todas as constantes do banco dando sua devida preferência!
     */
    $WorkControlDefineConf = null;
    if (SIS_CONFIG_WC) {
        $Read = new Read;
        $Read->FullRead("SELECT conf_key, conf_value FROM " . DB_CONF);
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $WorkControlDefineConf) {
                if ($WorkControlDefineConf['conf_key'] != 'THEME' || empty($_SESSION['WC_THEME'])) {
                    define("{$WorkControlDefineConf['conf_key']}", "{$WorkControlDefineConf['conf_value']}");
                }
            }
            $WorkControlDefineConf = true;
        }
    }

    require 'Config/Config.inc.php';
    require 'Config/Agency.inc.php';
    require 'Config/Client.inc.php';

    /*
     * Exibe erros lançados
     */

    function Erro($ErrMsg, $ErrNo = null)
    {
        $CssClass = ($ErrNo == E_USER_NOTICE ? 'trigger_info' : ($ErrNo == E_USER_WARNING ? 'trigger_alert' : ($ErrNo == E_USER_ERROR ? 'trigger_error' : 'trigger_success')));
        echo "<div class='trigger {$CssClass}'>{$ErrMsg}<span class='ajax_close'></span></div>";
    }

    /*
     * Exibe erros lançados por ajax
     */
    function AjaxErro($ErrMsg, $ErrNo = null)
    {
        $CssClass = ($ErrNo == E_USER_NOTICE ? 'trigger_info' : ($ErrNo == E_USER_WARNING ? 'trigger_alert' : ($ErrNo == E_USER_ERROR ? 'trigger_error' : 'trigger_success')));
        return "<div class='trigger trigger_ajax {$CssClass}'>{$ErrMsg}<span class='ajax_close'></span></div>";
    }

    /*
     * personaliza o gatilho do PHP
     */
    function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine)
    {
        echo "<div class='trigger trigger_error'>";
        echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
        echo "<small>{$ErrFile}</small>";
        echo "<span class='ajax_close'></span></div>";

        if ($ErrNo == E_USER_ERROR) {
            die;
        }
    }

    set_error_handler('PHPErro');

    /*
     * Descreve nivel de usuário
     */
    function getWcLevel($Level = null)
    {
        $UserLevel = [
            1 => 'Cliente (user)',
            2 => 'Assinante (user)',
            6 => 'Marketing - Blog (adm)',
            7 => 'Suporte Geral (adm)',
            8 => 'Gerente Geral (adm)',
            9 => 'Administrador (adm)',
            10 => 'Super Admin (adm)'
        ];

        if (!empty($Level)) {
            return $UserLevel[$Level];
        } else {
            return $UserLevel;
        }
    }

//CUSTOM FUNCTIONS
    function getWcMatLevels($Levels = null)
    {
        $MatLevels = [
            1 => 'Introdutório',
            2 => 'Intermediário',
            3 => 'Avançado'
        ];
        if (!empty($Levels)) {
            return $MatLevels[$Levels];
        } else {
            return $MatLevels;
        }
    }

    function getWcMonths($Months = null)
    {
        $PostMonths = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];
        if (!empty($Months)) {
            return $PostMonths[$Months];
        } else {
            return $PostMonths;
        }
    }

    function getWcInches($Length)
    {
        $Inch = $Length / 25.4;

        if (!empty($Inch)) {
            //var_dump ($Length);
            return $Inch;
        } else {
            return null;
        }
    }
