<?php

    if (!$Read) {
        $Read = new Read;
    }
    $Email = new Email;
    $Read->ExeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';

        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

<?php require REQUIRE_PATH .'/inc/blog_banner.inc.php'; ?>

<!-- BANNER SECTION END HERE -->
<!-- FORM SECTION START HERE -->
<section class="unity-form-section index2-form-section contact-form-section w-100 float-left padding-top light-bg">
	<div class="container">
		<div class="index2-form-outer-con">
			<div class="generic-title text-center">
				<span class="small-txt d-block" data-aos="fade-up" data-aos-duration="700">FALE CONOSCO</span>
				<h2 data-aos="fade-up" data-aos-duration="700">TEM ALGO EM MENTE, ENTRE EM CONTATO CONOSCO</h2>
			</div>
			<div class="form-con index2-form-inner-section">
				<div class="index2-form-img-con" data-aos="fade-up" data-aos-duration="700">
					<figure class="mb-0">
						<img src="<?= INCLUDE_PATH ?>/assets/images/index2-form-left-img.jpg" alt="index2-form-left-img">
					</figure>
				</div>

					<form class="form-box j_formsubmit" method="post" id="contactpage" method="post"
					      enctype="multipart/form-data">
						<input type="hidden" class="callback" name="callback" value="Leads">
						<input type="hidden" class="callback-action" name="callback_action" value="contato">
						<input type="hidden" name="origin" value="contactpage">

						<div class="callback_return trigger_ajax"></div>

					<div class="form-inputs-con">
						<ul class="list-unstyled w-100 float-left mb-0">
							<li data-aos="fade-up" data-aos-duration="700">
								<label>Nome:</label>
								<input type="text" name="name" id="name" placeholder="Seu nome completo:">
							</li>
							<li data-aos="fade-up" data-aos-duration="700">
								<label>Email:</label>
								<input type="email" placeholder="Seu melhor e-mail" name="email" id="email">
							</li>
							<li data-aos="fade-up" data-aos-duration="700">
								<label>Telefone:</label>
								<input type="tel" class="formPhone" placeholder="Número de telefone:" name="tel"
								       id="tel">
							</li>
						</ul>
						<ul class="list-unstyled w-100 float-left mb-0">
							<li data-aos="fade-up" data-aos-duration="700">
								<label>Código Postal (CEP):</label>
								<input type="text" class="formCep" placeholder="Digite seu CEP:" name="cep"
								       id="cep">
							</li>
							<li data-aos="fade-up" data-aos-duration="700">
								<label>Mensagem:</label>
								<textarea placeholder="Sua mensagem" name="subject" id="subject"></textarea>
							</li>
						</ul>
					</div>
					<div class="submit-btn" data-aos="fade-up" data-aos-duration="700">
						<button type="submit" id="submit"><i class="far fa-paper-plane"></i> ENVIAR MENSAGEM </button>
					</div>
				</form>
			</div>
		</div>
	<!--	<div class="contact-outer-con">
			<div class="contact-details-box" data-aos="fade-up" data-aos-duration="700">
				<div class="contact-icon">
					<i class="fas fa-phone-alt"></i>
				</div>
				<div class="contact-info-con">
					<span class="d-block">PHONE:</span>
					<a href="tel:+18002345">+1 (800) 234 5</a>
					<a href="tel:+58001237">+5 (800) 123 7</a>
				</div>
			</div>
			<div class="contact-details-box" data-aos="fade-up" data-aos-duration="700">
				<div class="contact-icon">
					<i class="far fa-envelope"></i>
				</div>
				<div class="contact-info-con">
					<span class="d-block">EMAIL:</span>
					<a href="mailto:info@unityforce.com">info@unityforce.com</a>
					<a href="mailto:donate@info@unityforce">donate@info@unityforce</a>
				</div>
			</div>
			<div class="contact-details-box" data-aos="fade-up" data-aos-duration="700">
				<div class="contact-icon">
					<i class="fas fa-map-marker-alt"></i>
				</div>
				<div class="contact-info-con">
					<span class="d-block">LOCATION:</span>
					<p class="mb-0">King Street Melbourne,<br>
						3000, Australia</p>
				</div>
			</div>
		</div>
		<div class="map-section w-100 float-left" data-aos="fade-up" data-aos-duration="700">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8387096759334!2d144.9532000767644!3d-37.817246734238644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4dd5a05d97%3A0x3e64f855a564844d!2s121%20King%20St%2C%20Melbourne%20VIC%203000%2C%20Australia!5e0!3m2!1sen!2s!4v1692879195247!5m2!1sen!2s" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		</div>-->
	</div>
</section>
