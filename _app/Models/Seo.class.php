<?php

    /**
     * Seo [ MODEL ]
     * Classe de apoio para o modelo LINK. Pode ser utilizada para gerar SEO para as páginas do sistema!
     *
     * @copyright (c) 2014, Marcos Orlando - ZEN AGÊNCIA WEB
     */
    class Seo
    {

        private $Pach;
        private $File;
        private $Link;
        private $Key;
        private $Schema;
        private $Title;
        private $Description;
        private $Image;
        private $Data;

        public function __construct($Pach)
        {
            $this->Pach = explode('/', strip_tags(trim($Pach)));
            $this->File = (!empty($this->Pach[0]) ? $this->Pach[0] : null);
            $this->Link = (!empty($this->Pach[1]) ? $this->Pach[1] : null);
            $this->Key = (!empty($this->Pach[2]) ? $this->Pach[2] : null);

            $this->setPach();
        }

        public function getSchema()
        {
            return $this->Schema;
        }

        public function getTitle()
        {
            return $this->Title;
        }

        public function getDescription()
        {
            return $this->Description;
        }

        public function getImage()
        {
            return $this->Image;
        }

        public function getData()
        {
            return $this->Data;
        }

        /*
         * ***************************************
         * **********  PRIVATE METHODS  **********
         * ***************************************
         */

        private function setPach()
        {
            if (empty($Read)):
                $Read = new Read;
            endif;

            $Pages = [];
            $Read->FullRead("SELECT page_name FROM " . DB_PAGES);
            if ($Read->getResult()):
                foreach ($Read->getResult() as $SinglePage):
                    $Pages[] = $SinglePage['page_name'];
                endforeach;
            endif;

            //LANDING_PAGES
            $LandingPages = [];
            $Read->FullRead('SELECT page_name FROM ' . DB_LANDING_PAGES);
            if ($Read->getResult()):
                foreach ($Read->getResult() as $SinglePage):
                    $LandingPages[] = $SinglePage['page_name'];
                endforeach;
            endif;

            //THANKYOU_PAGES
            $ThankYouPages = [];
            $Read->FullRead('SELECT page_name FROM ' . DB_THANKYOU_PAGES);
            if ($Read->getResult()):
                foreach ($Read->getResult() as $SinglePage):
                    $ThankYouPages[] = $SinglePage['page_name'];
                endforeach;
            endif;

            if (in_array($this->File, $Pages)):
                //PÁGINAS
                $Read->FullRead("SELECT page_title, page_subtitle, page_cover FROM " . DB_PAGES . " WHERE page_name = :nm",
                    "nm={$this->File}");
                if ($Read->getResult()):
                    $Page = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Page['page_title'] . " - " . SITE_NAME;
                    $this->Description = $Page['page_subtitle'];
                    $this->Image = (!empty($Page['page_cover']) ? BASE . "/uploads/{$Page['page_cover']}" : INCLUDE_PATH . '/images/default.jpg');
                else:
                    $this->set404();
                endif;

            elseif (in_array($this->File, $LandingPages)):
                //LANDING PAGES
                $Read->FullRead('SELECT page_title, page_subtitle, page_cover FROM ' . DB_LANDING_PAGES . ' WHERE page_name = :nm',
                    "nm={$this->File}");
                if ($Read->getResult()):
                    $Page = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Page['page_title'] . ' - ' . SITE_NAME;
                    $this->Description = $Page['page_subtitle'];
                    $this->Image = (!empty($Page['page_cover']) ? BASE . "/uploads/landingpages/{$Page['page_cover']}" : INCLUDE_PATH . '/images/default.jpg');
                else:
                    $this->set404();
                endif;

            elseif (in_array($this->File, $ThankYouPages)):
                //THANKYOU PAGES
                $Read->FullRead('SELECT page_title, page_subtitle, page_cover FROM ' . DB_THANKYOU_PAGES . ' WHERE page_name = :nm',
                    "nm={$this->File}");
                if ($Read->getResult()):
                    $Page = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Page['page_title'];
                    $this->Description = 'Download do conteúdo:' . $Page['page_title'];
                    $this->Image = (!empty($Page['page_cover']) ? BASE . "/uploads/thankyoupages/{$Page['page_cover']}" : INCLUDE_PATH . '/images/default.jpg');
                else:
                    $this->set404();
                endif;

            elseif ($this->File == 'index'):
                //INDEX
                $this->Schema = 'WebSite';
                $this->Title = SITE_NAME . " - " . SITE_SUBNAME;
                $this->Description = SITE_DESC;
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            elseif ($this->File == 'artigo' && $this->Key == 'amp'):
                //ARTIGO AMP
                $Read->FullRead("SELECT post_title, post_subtitle, post_cover, post_date FROM " . DB_POSTS . " WHERE post_name = :nm AND post_date <= NOW()",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Post = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Post['post_title'] . " - " . SITE_NAME;
                    $this->Description = $Post['post_subtitle'];
                    $this->Image = BASE . "/uploads/{$Post['post_cover']}";
                    $this->Data = $Post['post_date'];
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'artigo'):
                //ARTIGO
                $Read->FullRead("SELECT post_title, post_subtitle, post_cover FROM " . DB_POSTS . " WHERE post_name = :nm AND post_date <= NOW()",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Post = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Post['post_title'] . " - " . SITE_NAME;
                    $this->Description = $Post['post_subtitle'];
                    $this->Image = BASE . "/uploads/{$Post['post_cover']}";
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'artigos'):
                //ARTIGOS
                $Read->FullRead("SELECT category_title, category_content FROM " . DB_CATEGORIES . " WHERE category_name = :nm",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Category = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Category['category_title'] . " - " . SITE_NAME;
                    $this->Description = $Category['category_content'];
                    $this->Image = INCLUDE_PATH . '/images/default.jpg';
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'pesquisa'):
                //PESQUISA
                $this->Schema = 'WebSite';
                $this->Title = "Pesquisa por {$this->Link} em " . SITE_NAME;
                $this->Description = SITE_DESC;
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            elseif ($this->File == 'artigo'):
                //CUSTOM
            elseif ($this->File == 'produto'):
                //PDT TRAVI
                $Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_cover FROM " . DB_PDT_TRAVI . " WHERE pdt_name = :nm AND pdt_created <= NOW()",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Product = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Product['pdt_title'] . " - " . SITE_NAME;
                    $this->Description = $Product['pdt_subtitle'];
                    $this->Image = BASE . "/uploads/{$Product['pdt_cover']}";
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'produtos'):
                //Produtos
                $Read->FullRead("SELECT cat_title, cat_description FROM " . DB_PDT_CATS_TRAVI . " WHERE cat_name = :nm",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Category = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Category['cat_title'] . " - " . SITE_NAME;
                    $this->Description = $Category['cat_description'];
                    $this->Image = INCLUDE_PATH . '/images/default.jpg';
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'pesquisa-produtos'):
                //PESQUISA PRODUTOS
                $this->Schema = 'WebSite';
                $this->Title = "Pesquisa por {$this->Link} em " . SITE_NAME;
                $this->Description = SITE_DESC;
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            elseif ($this->File == 'servico'):
                //SERVIÇO
                $Read->FullRead("SELECT svc_title, svc_subtitle, svc_cover FROM " . DB_SVC . " WHERE svc_name = :nm AND svc_created <= NOW()",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Product = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Product['svc_title'] . " - " . SITE_NAME;
                    $this->Description = $Product['svc_subtitle'];
                    $this->Image = BASE . "/uploads/{$Product['svc_cover']}";
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'servicos'):
                //SERVIÇOS
                $this->Schema = 'WebSite';
                $this->Title = "Serviços - " . SITE_NAME;
                $this->Description = "Serviços prestados pela Travi.";
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            elseif ($this->File == 'segmento'):
                //SEGMENTO
                $Read->FullRead("SELECT seg_title, seg_subtitle, seg_cover FROM " . DB_SEG . " WHERE seg_name = :nm AND seg_created <= NOW()",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Segment = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Segment['seg_title'] . " - " . SITE_NAME;
                    $this->Description = $Segment['seg_subtitle'];
                    $this->Image = BASE . "/uploads/{$Segment['seg_cover']}";
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'segmentos'):
                //SEGMENTOS
                $this->Schema = 'WebSite';
                $this->Title = "Segmentos - " . SITE_NAME;
                $this->Description = "Segmentos que a Travi antende.";
                $this->Image = INCLUDE_PATH . '/images/default.jpg';

            elseif ($this->File == 'certificacao'):
                //certificados
                $Read->FullRead("SELECT cert_title, cert_subtitle, cert_cover FROM " . DB_CERT . " WHERE cert_name = :nm AND cert_created <= NOW()",
                    "nm={$this->Link}");
                if ($Read->getResult()):
                    $Product = $Read->getResult()[0];
                    $this->Schema = 'WebSite';
                    $this->Title = $Product['cert_title'] . " - " . SITE_NAME;
                    $this->Description = $Product['cert_subtitle'];
                    $this->Image = BASE . "/uploads/{$Product['cert_cover']}";
                else:
                    $this->set404();
                endif;
            elseif ($this->File == 'certificacoes'):
                //CERTIFICAÇÔES
                $this->Schema = 'WebSite';
                $this->Title = "Certificações  - " . SITE_NAME;
                $this->Description = "Certificações que a Travi possui.";
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            //CUSTOM
            elseif ($this->File == 'conta'):
                //CONTA
                if (ACC_MANAGER):
                    $ArrAccountApp = [
                        '' => 'Entrar!',
                        'login' => 'Entrar!',
                        'cadastro' => 'Criar Conta!',
                        'recuperar' => 'Recuperar Senha!',
                        'nova-senha' => 'Criar Nova Senha!',
                        'sair' => 'Sair!',
                        'home' => 'Minha Conta!',
                        'restrito' => 'Acesso Restrito!',
                        'enderecos' => 'Meus Endereços!',
                        'pedidos' => 'Meus Pedidos!',
                        'dados' => 'Atualizar Dados!',
                        'pedido' => 'Pedido #' . str_pad($this->Key, 7, 0, STR_PAD_LEFT)
                    ];

                    $this->Schema = 'WebSite';
                    $this->Title = (!empty($ArrAccountApp[$this->Link]) ? SITE_NAME . " - " . $ArrAccountApp[$this->Link] : 'OPPPSSS!');
                    $this->Description = SITE_DESC;
                    $this->Image = INCLUDE_PATH . '/images/default.jpg';
                else:
                    $this->set404();
                endif;
            else:
                //404
                $this->set404();
            endif;
        }

        private function set404()
        {
            $this->Schema = 'WebSite';
            $this->Title = "Oppsss, nada encontrado! - " . SITE_NAME;
            $this->Description = SITE_DESC;
            $this->Image = INCLUDE_PATH . '/images/default.jpg';
        }

    }
