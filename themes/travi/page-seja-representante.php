<!-- PAGE TITLE -->
<section class="page-title text-center">
    <div class="container relative clearfix">
        <div class="title-holder">
            <div class="title-text">
                <h1 class="uppercase">Seja um representante comercial</h1>
            </div>
        </div>
    </div>
</section>
<!-- END PAGE TITLE -->

<!-- CONTACT -->
<section class="section-wrap-lg contact pt-10" id="contact">
    <div class="container">

        <div class="row contact-container">

            <div class="contact-info style-big mt-20">
                <h5 class="uppercase">Seja um representante Global</h5>
                <p>Venha trabalhar conosco.</p>
            </div>

            <div class="contact-message style-big mt-20">
                <form action="" name="lead_capture" class="j_formsubmit" method="post" enctype="multipart/form-data">
                    <div id="success-project-contact-form" class="no-margin-lr"></div>

                    <input type="hidden" name="callback" value="Cotacao"/>
                    <input type="hidden" name="callback_action" value="cotacao"/>

                    <div class="row row-16">
                        <div class="col-md-12">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="name" placeholder="Nome Completo..." required tabindex="1"/>
                        </div>
                        <div class="col-md-12">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" placeholder="Seu E-mail..." required tabindex="2"/>
                        </div>
                        <div class="col-md-12">
                            <label for="fone">Telefone</label>
                            <input type="text" id="fone" name="fone" placeholder="Telefone..." required tabindex="3"/>
                        </div>
                        <div class="col-md-12">
                            <label for="social">Razão Social</label>
                            <input type="text" id="social" name="social" placeholder="Razão Social..." required tabindex="4"/>
                        </div>
                        <div class="col-md-12">
                            <label for="fantasia">Nome Fantasia</label>
                            <input type="text" id="fantasia" name="fantasia" placeholder="Nome Fantasia..." required tabindex="5"/>
                        </div>
                        <div class="col-md-12">
                            <label for="cnpj">CNPJ</label>
                            <input type="text" id="cnpj" name="name" placeholder="CNPJ..." required tabindex="6"/>
                        </div>
                    </div>

                    <label for="atuacao">Areas de Atuação</label>
                    <textarea id="atuacao" name="atuacao" placeholder="Areas de atuação..." rows="9" required tabindex="7"></textarea>

                    <label for="mensagem">Menssagem</label>
                    <textarea id="mensagem" name="mensagem" placeholder="Sua menssagem..." rows="9" required tabindex="8"></textarea>

                    <button name="public" type="submit" class="btn btn-lg btn-submit" tabindex="9">Enviar Menssagem
                        <img class="form_load none" alt="Enviando Requisição!" title="Enviando Requisição!"
                             src="<?= INCLUDE_PATH; ?>/images/icons/load.gif"/>
                    </button>
                </form>
            </div> <!-- end col -->

        </div>
    </div>

</section>
<!-- END CONTACT -->


<!-- CALL TO ACTION -->
<?php require REQUIRE_PATH . "/inc/banner-cta.php"; ?>
<!-- END CALL TO ACTION -->
