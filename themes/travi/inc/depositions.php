<?php

	$Read->ExeRead(DB_DEPOSITIONS, "ORDER BY depositions_order DESC");
	if ($Read->getResult()) {
		?>
		<section class="commonSection testimonialSection">
			<div class="container">
				<div class="row">
					<div class="col-xl-4 col-lg-4 noPaddingRight">
						<h6 class="sub_title ">Depoimentos</h6>
						<h2 class="sec_title">
							<span>O que dizem nossos clientes?</span>
						</h2>
						<p class="ind_lead">Soluções em Plásticos Industriais</p>
						<!-- <p>É nosso cliente? <a href="" title="Enviar meu depoimento!"> Deixe seu depoimento!</a></p>-->

					</div>
					<div class="col-xl-8 col-lg-8 pdl40">
						<div class="testimonialSliderHolder tw-stretch-element-inside-column">
							<div class="testimonialSlider">
								<?php
									foreach ($Read->getResult() as $Deposition) {
										extract($Deposition);
										?>
										<div class="ts_item">
											<div class="testimonial_item">
                                            <span class="ratings"><i class="fas fa-star"></i><i class="fas fa-star"></i><i
			                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
			                                            class="fas fa-star"></i></span>

												<p><?= $depositions_text; ?></p>
												<div class="ti_author clearfix">
													<img src="<?= BASE . "/tim.php?src=uploads/{$depositions_image}&w=70&h=70"; ?>"
													     alt="Foto de <?= $deposition_name; ?>"
													     title="Foto de <?= $depositions_name; ?>"/>
													<h4><?= $depositions_name; ?></h4>
													<span><?= $depositions_profession; ?></span>
												</div>
											</div>
										</div>
										<?php
									}

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	} ?>
