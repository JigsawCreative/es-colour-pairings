<?php

    /** Template Name: Pairings Loop */
    
    get_header();

    use ESColourPairings\Frontend\ESCP_DisplayPairings;

    // Fetch groups from database
    $groups = ESCP_DisplayPairings::get_pairings(get_field('pairing_id'));

?>

    <div class="tile-pairing-container"><!-- this is the pairing template container -->

        <?php foreach($groups as $pairings) : ?>

            <!-- pairing groups container -->
            <div class="tile-pairing-groups pairing-groups-id-<?php echo $pairings['pairing_id']; ?>">

                <div class="tile-pairing-group">

                    <div class="tile-pairing-group-heading">
                        <?php echo $pairings['heading']; ?>
                    </div>

                    <div class="tile-pairing-options">

                        <?php foreach($pairings['products'] as $key => $products) : ?>

                            <div class="tile-pairing-option">

                                <div class="tile-pairing-option-heading"><?php echo $key + 1; ?></div>
                                
                                <div class="tile-pairing-option-swatch-loops swatch-loops-3">

                                    <?php foreach($products as $product) :

                                        $sqm   = (int) get_field( 'discounted_price_per_sqm', $product );
                                        $class = '';

                                        if ( $sqm === 0 ) {
                                            $class = 'out-of-stock';
                                        } elseif ( $sqm >= 1 && $sqm <= 10 ) {
                                            $class = 'low-stock';
                                        }

                                    ?>

                                        <div class="tile-pairing-option-swatch-loop <?php echo $class; ?>">
                                            <a href="https://emporiosurfaces.local/product-recommendations/product-comparison/?ids=<?php echo implode(",", $products); ?>" data-discount-rate="<?php echo get_field('discount_percentage', $product); ?>% Off" class="tooltip">
                                                <img src=<?php echo wp_get_attachment_image_url( get_post_thumbnail_id( $product ), 'samples' ); ?>" data-batch-id="<?php echo $product; ?>"><span class="tooltiptext"><?php echo get_the_title( $product ); ?> @ £<?php echo get_field('discounted_price_per_sqm', $product); ?> Per M²</span>
                                            </a>
                                        </div>

                                    <?php endforeach; ?>

                                </div><!-- end tile-pairing-option-swatch-loops -->
                                
                            </div><!-- end tile-pairing-option -->

                        <?php endforeach; ?>

                    </div><!-- end tile-pairing-options -->

                    <div class="tile-pairing-group-link"><a href="<?php echo $pairings['more_link']; ?>" data-attribute-topic="<?php echo $pairings['pairing_id']; ?>">Discover More </a></div><!-- end tile-pairing-group-link -->
                </div><!-- end tile-pairing-group -->

            </div><!-- end tile-pairing-groups -->

        <?php endforeach; ?>

    </div><!-- end tile-pairing-container -->

<?php get_footer(); ?>
