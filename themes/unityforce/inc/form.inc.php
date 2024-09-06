<!-- FORM SECTION START HERE -->
<section id="voluntary" class="unity-form-section w-100 float-left padding-top padding-bottom light-bg">
	<div class="container">
		<div class="generic-title text-center">
			<span class="small-txt d-block" data-aos="fade-up" data-aos-duration="700">SEJA UM VOLUNTÁRIO NA
				CAMPANHA</span>
			<h2 data-aos="fade-up" data-aos-duration="700">JUNTOS, TRANSFORMAMOS ASPIRAÇÕES EM REALIDADE.</h2>
		</div>
		<div class="form-con">
			<form class="form-box j_formsubmit" method="post" id="contact_index" method="post"
			      enctype="multipart/form-data">
				<input type="hidden" class="callback" name="callback" value="Leads">
				<input type="hidden" class="callback-action" name="callback_action" value="voluntary">

				<input type="hidden" class="wc_logradouro" name="voluntary_street" value="">
				<input type="hidden" class="wc_bairro" name="voluntary_district" value="">
				<input type="hidden" class="wc_localidade" name="voluntary_city" value="">
				<input type="hidden" class="wc_uf" name="voluntary_uf" value="">
				<input type="hidden" class="wc_ibge" name="voluntary_ibge" value="">

				<div class="callback_return"></div>
				<div class="form-inputs-con">
					<ul class="list-unstyled w-100 float-left mb-0">
						<li data-aos="fade-up" data-aos-duration="700">
							<label>Nome Completo:</label>
							<input type="text" name="voluntary_name" id="voluntary_name" placeholder="Seu nome:"
							       required
							       tabindex="1">
						</li>
						<li data-aos="fade-up" data-aos-duration="700">
							<label>Email:</label>
							<input type="email" placeholder="Seu melhor e-mail:" name="voluntary_email"
							       id="voluntary_email" required  tabindex="2">
						</li>
						<li data-aos="fade-up" data-aos-duration="700">
							<label>Whatsapp:</label>
							<input type="tel" class="formPhone" placeholder="Seu número de telefone:"
							       name="voluntary_phone"
							       id="voluntary_phone" required  tabindex="3">
						</li>
					</ul>
					<ul class="list-unstyled w-100 float-left mb-0">
						<li data-aos="fade-up" data-aos-duration="700">
							<label>Código Postal (CEP):</label>
							<input type="text" class="wc_getCep formCep" placeholder="Seu CEP:" name="voluntary_cep"
							       id="voluntary_cep" required  tabindex="4">
						</li>
						<li data-aos="fade-up" data-aos-duration="700">
							<label>Deixe sua mensagem:</label>
							<textarea placeholder="Escreva sua mensagem" name="voluntary_message"
							          id="voluntary_message" required  tabindex="5"></textarea>
						</li>
					</ul>
				</div>
				<label for="privacy" class="label_check mb-3 text-size-14">
					<input type="checkbox" id="privacy" name="privacy" required tabindex="6">
					<span>
								<b class="font_red">*</b> Consinto com a
								<i class="font_red" data-toggle="modal" data-target="#myModal">Política de
									Privacidade.</i>
								Ao enviar meus dados, eu autorizo o uso dos mesmos para receber a resposta e comunicações sobre a campanha.
							</span>

				</label>

				<div class="submit-btn" data-aos="fade-up" data-aos-duration="700">
					<button type="submit" id="submit" tabindex="7"><i class="fa fa-users"></i><img class="form_load" src="<?= INCLUDE_PATH ?>/assets/images/load_w.gif" alt="Enviando..."> JUNTE-SE AGORA</button>
				</div>
			</form>
		</div>
	</div>
</section>
<!-- FORM SECTION START HERE -->

<div class="modal" id="myModal" style="align-items: center; justify-content: center;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<?php
				$Read = new Read();
				$page = $Read->LinkResult(DB_PAGES, 'page_name', 'politica-de-protecao-de-dados', 'page_title, page_content');
			?>
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?=$page['page_title']?></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<div class="modal-body">
				<?=$page['page_content']?>
			</div>
			<!-- Modal footer -->
			<div class="modal-footer">
				<label for="privacy" class="btn btn-danger" data-dismiss="modal">
					<i class="fa
				fa-window-close"></i> Fechar</label>
			</div>
		</div>
	</div>
</div>
