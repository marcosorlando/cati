<link rel="stylesheet" href="<?=BASE?>/_cdn/widgets/slide/slide.wc.css">
<?php

	$Read = new Read();

	$Read->FullRead("SELECT slide_image_mobile, slide_image_tablet, slide_image_desktop, slide_title, slide_headline, slide_desc, slide_category, slide_product, slide_link, slide_link_pdt, slide_link_pdt_btn, slide_link_cat, slide_link_cat_btn, show_headline, show_desc FROM " . DB_SLIDES . " WHERE slide_status = :status AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_date DESC", "status=1");

	if ($Read->getResult()):
		?>
		<section class="carousel">
			<div class="content">
				<div class="owl-carousel owl-theme">
					<?php
						foreach ($Read->getResult() as $SLIDE):
							$image_desktop = $SLIDE['slide_image_desktop'];
							$image_mobile = (!empty($SLIDE['slide_image_mobile']) ? $SLIDE['slide_image_mobile'] : $image_desktop);
							$image_tablet = (!empty($SLIDE['slide_image_tablet']) ? $SLIDE['slide_image_tablet'] : $image_desktop);
							?>
							<div>
								<a href="<?= $SLIDE['slide_link']; ?>" class="wc_goto" title="<?= $SLIDE['slide_title']; ?>">
									<picture alt="<?= $SLIDE['slide_title']; ?>">
										<source media="(min-width: 992px)"
										        srcset="<?= BASE; ?>/uploads/<?= $image_desktop; ?>"/>
										<source media="(min-width: 544px)"
										        srcset="<?= BASE; ?>/uploads/<?= $image_tablet; ?>"/>
										<source media="(min-width: 1px)"
										        srcset="<?= BASE; ?>/uploads/<?= $image_mobile; ?>"/>
										<img src="<?= BASE; ?>/uploads/<?= $image_desktop; ?>"
										     alt="<?= $SLIDE['slide_title']; ?>" title="<?= $SLIDE['slide_title']; ?>"/>
									</picture>
								</a>

								<div class="slide-text">
									<?php
										if ($SLIDE['show_headline']) {
											echo "<h1>{$SLIDE['slide_headline']}</h1>";
										}
										if ($SLIDE['show_desc']) {
											echo "<p>{$SLIDE['slide_desc']}</p>";
										}
									?>

									<div class="slide-buttons">
										<?php
											if ($SLIDE['slide_product']) {
												echo "<a class='ind_btn' href='{$SLIDE['slide_link_pdt']}' title='Acessar página do produto'><span class='fa fa-arrow-alt-right'> &nbsp;&nbsp;{$SLIDE['slide_link_pdt_btn']}&nbsp;&nbsp;</span></a>";}

											if ($SLIDE['slide_category']) {echo "<a class='ind_btn' href='{$SLIDE['slide_link_cat']}' title='Acessar página de produtos'><span class='fa fa-arrow-alt-right'>&nbsp;&nbsp;{$SLIDE['slide_link_cat_btn']}&nbsp;&nbsp;</span></a>";}
										?>

									</div>
								</div>
							</div>
						<?php
						endforeach;
					?>
				</div>

				<div class="clear"></div>
			</div>
		</section>
	<?php
	endif;
?>
