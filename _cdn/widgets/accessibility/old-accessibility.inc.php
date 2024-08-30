<?php

	echo "
    <link rel='stylesheet' href='" . BASE . "/_cdn/widgets/accessibility/css/buttons.css'/>
     <link rel='stylesheet' href='" . BASE . "/_cdn/widgets/accessibility/css/highcontrast.css'/>
      <link rel='stylesheet' href='" . BASE . "/_cdn/widgets/accessibility/css/daltonism.css'/> ";
?>
<ul class="accessibility-buttons">
	<!--ZOOM IN / OUT / RESET-->
	<li class="access-font-size-a-plus no-zoomm">
		<button class="btn-lat-rigth" id="In" accesskey="A" title="Aumenta 10%">
			<span class="fa fa-search-plus"></span> AUMENTAR ZOOM
		</button>
	</li>
	<li class="access-font-size-a-minus">
		<button class="btn-lat-rigth" id="Out" accesskey="D" title="Diminui 10%">
			<span class="fa fa-search-minus"></span> DIMINUIR ZOOM
		</button>
	</li>
	<li class="access-font-size-a-reset">
		<button class="btn-lat-rigth" id="Rst" accesskey="R" title="Restaurar tamanho normal">
			<span class="fa fa-search-location"></span> RESTAURAR ZOOM
		</button>
	</li>

	<!--CONTRAST: HIGTHCONTRAST / DALTONISM / RESET-->
	<li class="higth-contrast">
		<a title="ALTO CONTRASTE" class="link-alto-contraste btn-lat-rigth" data-class="contrast">
			<span class="fa fa-adjust"></span> CONTRASTE
		</a>
	</li>
	<li class="daltonism-contrast">
		<a title="Adaptar site para Daltonicos" class="link-alto-contraste btn-lat-rigth" data-class="daltonism">
			<span class="fa fa-low-vision"></span> DALTÔNISMO
		</a>
	</li>
	<li class="contrast-default">
		<a title="Resetar - Voltar as cores padrão" class="link-alto-contraste btn-lat-rigth" data-class="">
			<span class="fa fa-recycle"></span> RESTAURAR CORES
		</a>
	</li>
	<!-- /.higth-contrast -->
</ul>

<!--VLIBRAS - LINGUA BRASILEIRA DE SINAIS-->
<div vw class="enabled">
	<div vw-access-button class="active"></div>
	<div vw-plugin-wrapper>
		<div class="vw-plugin-top-wrapper"></div>
	</div>
</div>

<script src="<?= BASE ?>/_cdn/widgets/accessibility/js-cookie-latest/src/js.cookie.js"></script>
<script src="<?= BASE ?>/_cdn/widgets/accessibility/js/scripts.js"></script>
<script src="<?= BASE ?>/_cdn/widgets/accessibility/js/textzoom.js"></script>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
