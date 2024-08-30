<?php

if (empty($WcSocialRequired)):
    $WcSocialRequired = true;
    echo "<link rel='stylesheet' href='" . BASE . "/_cdn/widgets/share/share.wc.css'/>";

endif;

echo "<ul class='workcontrol_socialshare'>";
echo "<li class='workcontrol_socialshare_cta'><strong>Compartilhe</strong> </li>";

$WcShareText = (!empty($WC_TITLE_LINK) ? $WC_TITLE_LINK : null);
$WcShareLink = (!empty($WC_SHARE_LINK) ? $WC_SHARE_LINK : BASE);
$WcShareHash = (!empty($WC_SHARE_HASH) ? $WC_SHARE_HASH : Check::Name(SITE_NAME));
/*
 * FACEBOOK
 */
$ShareIconText = 'Compartilhar no Facebook';
echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_facebook'><a rel='{$WcShareLink}' target='_blank' title='{$ShareIconText}' href='https://www.facebook.com/sharer/sharer.php?u={$WcShareLink}'><img alt='{$ShareIconText}' title='{$ShareIconText}' src='" . BASE . "/_cdn/widgets/share/icons/facebook.svg'/></a></li>";
/*
 * Whatsapp +
 */
$whatsText = urlencode("*Oie!* Estou lendo este artigo no Blog da " . SITE_NAME . " e resolvi compartilhar com você! Um excelente conteúdo com certeza você vai gostar. *Clique para ler!*");
$ShareIconText = 'Compartilhar no Whatsapp';
echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_whatsapp'><a rel='{$WcShareLink}' target='_blank' title='{$ShareIconText}' href='https://api.whatsapp.com/send?text=$whatsText {$WcShareLink}'><img alt='{$ShareIconText}' title='{$ShareIconText}' src='" . BASE . "/_cdn/widgets/share/icons/whatsapp.svg'/></a></li>";
/*
 * Linkedin
 */
$ShareIconText = 'Compartilhar no Linkedin';
echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_linkedin'><a rel='{$WcShareLink}' target='_blank' title='{$ShareIconText}' href='https://www.linkedin.com/cws/share?xd_origin_host={$WcShareLink}&amp;original_referer={$WcShareLink}&amp;url={$WcShareLink}&amp;isFramed=false&amp;token=&amp;lang=pt_BR&amp;_ts=1482238060107%2E67#state=&amp;from_login=true'><img alt='{$ShareIconText}' title='{$ShareIconText}' src='" . BASE . "/_cdn/widgets/share/icons/linkedin.svg'/></a></li>";
/*
 * TWITTER
 */
$ShareIconText = 'Compartilhar no Twitter';
    $WcShareText = urlencode($WcShareText);
echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_twitter'><a rel='{$WcShareLink}' target='_blank' title='{$ShareIconText}' href='https://twitter.com/intent/tweet?url={$WcShareLink}&text={$WcShareText}&via=". SITE_SOCIAL_TWITTER . "'><img alt='{$ShareIconText}' title='{$ShareIconText}' src='" . BASE . "/_cdn/widgets/share/icons/twitter.svg'/></a></li>";
/*
 * E-MAIL
 */
$ShareIconText = 'Compartilhar por E-mail';
echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_mail'><a rel='{$WcShareLink}' target='_blank' title='{$ShareIconText}' href='mailto:?to=&amp;&subject=Leia o artigo: {$WC_TITLE_LINK}&body=Estou lendo o artigo {$WC_TITLE_LINK} no Blog da ". SITE_NAME . " e o conteúdo está excelente acho que você vai gostar, para ler acesse {$WcShareLink}'><img alt='{$ShareIconText}' title='{$ShareIconText}' src='" . BASE . "/_cdn/widgets/share/icons/envelope.svg'/></a></li>";

echo "</ul>";
