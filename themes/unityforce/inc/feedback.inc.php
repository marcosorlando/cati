<?php

	$Read->ExeRead(DB_DEPOSITIONS);

	if ($Read->getResult()) {
		?>
		<section class="feedback-section w-100 float-left padding-top light-bg">
			<div class="container">
				<div class="generic-title text-center">
					<span class="d-block small-txt" data-aos="fade-up"
					      data-aos-duration="700">FEEDBACK DE QUEM CONFIA</span>
					<h2 data-aos="fade-up" data-aos-duration="700">OUÇA O QUE NOSSOS ELEITORES</h2>
				</div>
				<div class="feedback-testimonial" data-aos="fade-up" data-aos-duration="700">
					<div class="owl-carousel owl-theme">

							<?php
								foreach ($Read->getResult() as $Dep) {
									echo "<div class='item'>
											<div class='client-box'>
												<div class='client-img'>
													<figure class='mb-0'>
														<img src='" . BASE . "/uploads/{$Dep['depositions_image']}' alt='client-img'>
													</figure>
													<figure class='quote-img'>
														<img src='" . INCLUDE_PATH . "/assets/images/quote-img.png' alt='quote-img'>
													</figure>
												</div>
												<div class='feedback-right-box'>
													<p>{$Dep['depositions_text']}</p>
													<h4 class='text-uppercase'>{$Dep['depositions_name']}</h4>
													<span class='d-block'>{$Dep['depositions_profession']}</span>
												</div>
											</div>
											</div>						
									";

								}
							?>
					</div>
					<div class="btn-wrap">
						<button class="prev-btn"><i class="fas fa-arrow-left"></i></button>
						<button class="next-btn"><i class="fas fa-arrow-right"></i></button>
					</div>
				</div>

			<!--	<div class="donate-suggestion w-100 float-left" data-aos="fade-up" data-aos-duration="700">
					<h3 class="mb-0">VOCÊ GOSTARIA DE SE TORNAR UM DOADOR?</h3>
					<div class="generic-btn">
						<a href="javascript:alert('Inserir link para Vaquinha')"><i class="fas fa-hand-holding-usd"></i> DOE AGORA</a>
					</div>
				</div>-->
			</div>
		</section>
		<?php
	}
?>
