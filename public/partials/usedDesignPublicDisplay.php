<?php

/**
 * Public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.used-design.com
 * @since      0.0.1
 *
 * @package    UsedDesign
 * @subpackage UsedDesign/public/partials
 */

function used_design_offers_grid_func($atts)
{
    $attributes = shortcode_atts( array(
        'col-max' => 4,
    ), $atts );

    $offers = UsedDesignPublic::getOffers($atts);
    ob_start();

    echo '<div class="usedDesignBootstrap">';
        if (array_key_exists('error', $offers))
        {
            echo $offers['error'];
        }
        else
        {
            // print '<pre>';
            // var_dump($offers['data']);
            // print '</pre>';
            if (count($offers['data']))
            {
                echo '<div class="row offersGrid">';
                    foreach ($offers['data'] as $offer) { ?>
                        <div class="col-xs-12 <?php print ($attributes['col-max'] >= 2) ? 'col-sm-6' : ''; ?> <?php print ($attributes['col-max'] >= 3) ? 'col-md-4' : ''; ?> <?php print ($attributes['col-max'] >= 4) ? 'col-lg-3' : ''; ?> offerGridItem">
                            <a href="<?php echo $offer['public_link']; ?>" target="_blank" class="plain">
                                <div class="wrapper-outer">
                                    <div class="wrapper-inner">
                                        <div class="wrapper-img">
                                            <img class="img-responsive lazy" src="<?php echo $offer['image_1_grid_url']; ?>" alt="">
                                        </div>
                                        <div class="wrapper-footer">
                                            <div class="clearfix">
                                                <h4><?php echo (strlen($offer['headline']) > 20) ? substr($offer['headline'], 0, 20) . '...' : $offer['headline']; ?></h4>
                                                <span class="price">€ <?php echo number_format($offer['price'], 0, ',', '.'); ?>,-</span>
                                            </div>
                                            <div class="clearfix">
                                                <span class="manufacturer"><?php echo $offer['manufacturer_name']; ?></span>
                                                <span class="discount"><?php echo ($offer['discount']) ? $offer['discount'] . '% Nachlass' : ''; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                echo '</div>'; // END .row
            }
            else { ?>
                <div class="well">Es wurde kein Angebot mit den von Ihnen angegebenen Suchparametern gefunden</div>
            <?php }
        }
    echo '</div>'; // END .usedDesignBootstrap

    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
?>
