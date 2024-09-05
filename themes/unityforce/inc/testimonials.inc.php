<?php

	$Read->ExeRead(DB_DEPOSITIONS, "ORDER BY depositions_order DESC");
	if ($Read->getResult()) {
		?>
		<div class="index2-testimonial-section w-100 float-left padding-top padding-bottom">
			<div class="container">
				<div class="index2-testimonial-inner-con" data-aos="fade-up" data-aos-duration="700">
					<div class="owl-carousel owl-theme">
						<?php
							foreach ($Read->getResult() as $Deposition) {
								extract($Deposition);
								?>
								<div class="item">
									<div class="index2-slide-content">
										<figure class="index2-quote-img">
											<img src="<?= INCLUDE_PATH ?>/assets/images/red-quote-img.png"
											     alt="red-quote-img">
										</figure>
										<p><?= $depositions_text; ?></p>
										<figure class="stars-img">
											<img src="<?= INCLUDE_PATH ?>/assets/images/stars-img.png" alt="stars-img">
										</figure>
										<div class="reviewer-details-con">
											<figure>
												<img src="<?= BASE . "/tim.php?src=uploads/{$depositions_image}&w=110&h=110"; ?>"
												     alt="Foto de <?= $deposition_name; ?>"
												     title="Foto de <?= $deposition_name; ?>">
											</figure>
											<span class="d-block"><?= $depositions_name; ?></span>
											<small class="d-block"><?= $depositions_profession; ?></small>
										</div>
									</div>
								</div>
								<?php
							}
						?>
					</div>
					<div class="btn-wrap">
						<button class="prev-btn"><span class="d-block">Próximo</span><i class="fas
						fa-long-arrow-alt-left"></i></button>
						<button class="next-btn"><span class="d-block">Anterior</span><i class="fas
						fa-long-arrow-alt-right"></i></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	} ?>
