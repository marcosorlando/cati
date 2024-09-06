<?php
    if (!$WorkControlDefineConf) {
        /*
         * URL DO SISTEMA
         */
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            define('BASE', 'https://localhost/cati'); //Url raiz do site no localhost
        } else {
            define('BASE', 'https://www.catianezanotto.com.br'); //Url raiz do site no servidor
        }
        define('THEME', 'unityforce'); //template do site
    }

//DINAMYC THEME
    if (!empty($_SESSION['WC_THEME'])) {
        define('THEME', $_SESSION['WC_THEME']); //template do site
    }

    /*
     * PATCH CONFIG
     */
    define('INCLUDE_PATH', BASE . '/themes/' . THEME); //Geral de inclusão (Não alterar)
    define('REQUIRE_PATH', 'themes/' . THEME); //Geral de inclusão (Não alterar)

    if (!$WorkControlDefineConf) {
        /*
         * ADMIN CONFIG
         */
        define('ADMIN_NAME', 'Zen Control');  //Nome do painel de controle (Work Control)
        define('ADMIN_DESC', 'O Zen Control é um sistema de gestão de conteúdo profissional gerido pela Zen Agência Web'); //Descrição do painel de controle (Work Control)
        define('ADMIN_MODE', 1); //1 = website / 2 = e-commerce / 3 = Imobi / 4 = EAD
        define('ADMIN_WC_CUSTOM', 1); //Habilita menu e telas customizadas
        define('ADMIN_MAINTENANCE', 0); //Manutenção
        define('ADMIN_VERSION', '3.1.4');

        /*
         * E-MAIL SERVER
         * Consulte estes dados com o serviço de hospedagem
         */
        define('MAIL_HOST', 'mail.catianezanotto.com.br'); //Servidor de e-mail
        define('MAIL_PORT', '587'); //Porta de envio
        define('MAIL_USER', 'naoresponda@catianezanotto.com.br'); //E-mail de envio
        define('MAIL_SMTP', 'naoresponda@catianezanotto.com.br'); //E-mail autenticador do envio (Geralmente igual ao
	    // MAIL_USER,
        // exceto em serviços como AmazonSES, sendgrid...)
        define('MAIL_PASS', 'Nor5pl@yC4t!4n3Z4n0tt0'); //Senha do e-mail de envio
        define('MAIL_SENDER', 'Catiane Zanotto'); //Nome do remetente de e-mail
        define('MAIL_MODE', 'tls'); //Encriptação para envio de e-mail [0 não parametrizar / tls / ssl] (Padrão = tls)
        define('MAIL_TESTER', 'marcosleaomoreira@gmail.com'); //E-mail de testes (DEV)

        /*
         * MEDIA CONFIG
         */
        define('IMAGE_W', 1200); //Tamanho da imagem (WIDTH)
        define('IMAGE_H', 628); //Tamanho da imagem (HEIGHT)
        define('THUMB_W', 800); //Tamanho da miniatura (WIDTH) PDTS
        define('THUMB_H', 800); //Tamanho da minuatura (HEIGHT) PDTS
        define('AVATAR_W', 500); //Tamanho da miniatura (WIDTH) USERS
        define('AVATAR_H', 500); //Tamanho da minuatura (HEIGHT) USERS
        define('SLIDE_W', 1920); //Tamanho da miniatura (WIDTH) SLIDE
        define('SLIDE_H', 900); //Tamanho da minuatura (HEIGHT) SLIDE
        define('VIDEO_W', 1280); //Tamanho da miniatura (WIDTH) SLIDE
        define('VIDEO_H', 720); //Tamanho da minuatura (HEIGHT) SLIDE

        /*
         * APP CONFIG
         * Habilitar ou desabilitar modos do sistema
         */
        define('APP_POSTS', 1); //Posts
        define('APP_POSTS_AMP', 0); //AMP para Posts
        define('APP_POSTS_INSTANT_ARTICLE', 0); //Instante Article FB
        define('APP_SEARCH', 1); //Relatório de Pesquisas
        define('APP_PAGES', 1); //Páginas
        define('APP_COMMENTS', 1); //Comentários
        define('APP_SLIDE', 0); //Slide Em Destaque
        define('APP_USERS', 1); //Usuários

	    /*
        * CUSTOM MODULES
        * Módulos customizados para o cliente!
        */
        define('APP_VIDEOS', 0); //Vídeos Youtube
        define('APP_MATERIALS', 0); //Materiais Ricos
        define('APP_DEPOSITIONS', 1); //Depoimentos
        define('APP_ALBUMS', 0); //Albuns de Fotos
        define('APP_PRODUCTS_TRAVI', 0); //Produtos Travi
        define('APP_SERVICES', 0); //Serviços Travi
        define('APP_SEGMENTS', 0); //Segmentos da Travi
        define('APP_CURIOSITIES', 0); //Curiosidades da Travi
        define('APP_CV', 0); //Base de Currículos da Travi
        define('APP_OUVIDORIA', 0); //Ouvidoria Travi
        define('APP_CERTIFICATIONS', 0); //Certificações da Travi
        define('APP_HELLO', 1); //Widget Hellobar!
        define('APP_LANDING_PAGES', 1); //Widget LP!
        define('APP_THANKYOU_PAGES', 1); //Widget ThankYouPage
        define('APP_LEADS', 1); //Widget Leads!
        define('APP_PARTNERS', 0); //Parceiros!
        define('APP_REPRESENTATIVES', 0); //Representantes

        /**
         * LEVEL CONFIG
         * Configura permissões do painel de controle!
         */
        define('LEVEL_WC_POSTS', 6);
        define('LEVEL_WC_COMMENTS', 6);
        define('LEVEL_WC_PAGES', 9);
        define('LEVEL_WC_SLIDES', 9);
        define('LEVEL_WC_REPORTS', 9);
        define('LEVEL_WC_USERS', 9);
        define('LEVEL_WC_VIDEOS', 9);
        define('LEVEL_WC_DEPOSITIONS', 9);
        define('LEVEL_WC_PARTNERS', 9);
        define('LEVEL_WC_ALBUMS', 9);
        define('LEVEL_WC_PRODUCTS_TRAVI', 9);
        define('LEVEL_WC_SERVICES', 9);
        define('LEVEL_WC_SEGMENTS', 9);
	    define('LEVEL_WC_CURIOSITIES', 9);
	    define('LEVEL_WC_CV', 9);
	    define('LEVEL_WC_OUVIDORIA', 9);
        define('LEVEL_WC_CERTIFICATIONS', 9);
        define('LEVEL_WC_REPRESENTATIVES', 9);
        define('LEVEL_WC_HELLO', 9);
        define('LEVEL_WC_LANDING_PAGES', 9);
        define('LEVEL_WC_THANKYOU_PAGES',9);
        define('LEVEL_WC_LEADS', 9);
        define('LEVEL_WC_CONFIG_MASTER', 10);
        define('LEVEL_WC_CONFIG_API', 10);
        define('LEVEL_WC_CONFIG_CODES', 10);

        /**
         * FB SEGMENT
         * Configura ultra segmentação de público no facebook
         * !!!! IMPORTANTE :: Para utilizar ultra segmentação de produtos e imóveis
         * é precisso antes configurar os catálogos de produtos respectivamente!
         */
        define('SEGMENT_FB_PIXEL_ID', ''); //Id do pixel de rastreamento
        define('SEGMENT_WC_USER', 1); //Enviar dados do login de usuário?
        define('SEGMENT_WC_BLOG', 1); //Ultra segmentar páginas do BLOG?
        define('SEGMENT_GL_ANALYTICS', ''); //ID do Google Analytics (GA4)
        define('SEGMENT_GL_TAGMANAGER', ''); //ID do Google Analytics (GA4)
        define('SEGMENT_FB_PAGE_ID', ''); //ID do Facebook Pages (Obrigatório para POST - Instant Article)
        define('SEGMENT_GL_ADWORDS_ID', ''); //ID do pixel do Adwords (-to do o site)
        define('SEGMENT_GL_ADWORDS_LABEL', ''); //Label do pixel do Adwords (to do o site)

        /**
         * APP LINKS
         * Habilitar ou desabilitar campos de links alternativos
         */
        define('APP_LINK_POSTS', 1); //Posts
        define('APP_LINK_PAGES', 1); //Páginas
        define('APP_LINK_PRODUCTS', 1); //Produtos
        define('APP_LINK_PROPERTIES', 1); //Imóveis
        /* ACCOUNT CONFIG */
        define('ACC_MANAGER', 1); //Conta de usuários (UI)
        define('ACC_TAG', 'Minha Conta'); //null para OLÁ {NAME} ou texto (Minha Conta, Meu Cadastro, etc)
        /* COMMENT CONFIG */
        define('COMMENT_MODERATE', 1); //Todos os NOVOS comentários ficam ocultos até serem aprovados
        define('COMMENT_ON_POSTS', 1); //Aplica comentários aos posts
        define('COMMENT_ON_PAGES', 0); //Aplica comentários as páginas
        define('COMMENT_ON_PRODUCTS',0); //Aplica comentários aos produtos
        define('COMMENT_SEND_EMAIL', 1); //Envia e-mails transicionais para usuários sobre comentários
        define('COMMENT_ORDER', 'DESC'); //Ordem de exibição dos comentários (ASC ou DESC)
        define('COMMENT_RESPONSE_ORDER', 'ASC'); //Ordem de exibição das respostas (ASC ou DESC)
    }
