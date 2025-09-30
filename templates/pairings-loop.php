<?php

    /** Template Name: Pairings Loop */
    
    get_header();

    use ESColourPairings\Frontend\ESCP_DisplayPairings;

    $groups = ESCP_DisplayPairings::get_pairings(get_field('pairing_id'));
    //var_dump($pairings);
?>

    <div class="tile-pairing-container"><!-- this is the pairing template container -->

        <?php foreach($groups as $heading => $pairings) : ?>

            <!-- pairing groups container -->
            <div class="tile-pairing-groups pairing-groups-id-<?php echo $pairings[0]['pairing_id']; ?>">

                <div class="tile-pairing-group">
                    <div class="tile-pairing-group-heading">
                        <?php echo $heading; ?>
                    </div>
                    <div class="tile-pairing-options">

                        <?php foreach($pairings as $key => $pairing) : ?>

                            <div class="tile-pairing-option">
                                <div class="tile-pairing-option-heading"><?php echo $key + 1; ?></div>
                                <div class="tile-pairing-option-swatch-loops swatch-loops-3">
                                    <?php foreach($pairing['products'] as $product) :  ?>

                                        <div class="tile-pairing-option-swatch-loop">
                                            <a href="" data-discount-rate="<?php echo get_field('discount_percentage', $product); ?>% Off" class="tooltip">
                                                <img src=<?php echo wp_get_attachment_url( get_post_thumbnail_id( $product ) ); ?>"><span class="tooltiptext"><?php echo get_the_title( $product ); ?> @ £<?php echo get_field('discounted_price_per_sqm', $product); ?> Per M²</span>
                                            </a>
                                        </div>

                                    <?php endforeach; ?>

                                </div><!-- end tile-pairing-option-swatch-loops -->
                            </div><!-- end tile-pairing-option -->

                        <?php endforeach; ?>

                    </div><!-- end tile-pairing-options -->

                    <div class="tile-pairing-group-link"><a href="add more link from cognito" data-attribute-topic="Warm Tones">Discover More </a></div><!-- end tile-pairing-group-link -->
                </div><!-- end tile-pairing-group -->

            </div><!-- end tile-pairing-groups -->

        <?php endforeach; ?>

    </div><!-- end tile-pairing-container -->

<?php get_footer(); ?>