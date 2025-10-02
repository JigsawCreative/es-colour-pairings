<?php

    /** Template Name: Pairings Loop */
    
    get_header(); 
    
    $url =  "https://emporiosurfaces.com/?p=" . get_the_id();
  $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
  $post_data = get_post($post->post_parent);
  $parent_slug = $post_data->post_name;
  $parent_name = str_replace("-", " ",  $parent_slug);
    
    ?>
    
     <!-- page content -->
<div class="page-content">
    
    <div class="intro-content">
        <div class="collection-banner">
            <div class="panel panel-left">
                <div class="panel-head banner-headline">Room solutions ready to order</div>
                <div class="panel-content"><div class="term-description"><p><?php the_title(); ?></p></div></div>
            </div>
            <div class="panel panel-right"><img class="banner-spacer" src="/wp-content/themes/emporio/images/emporio-surfaces-catagory-banner.png"><?php if ( has_post_thumbnail() ) { the_post_thumbnail('homelandscape', array('class' => 'banner-image')); } ?></div>
        </div>
    </div>
  
  <article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?>">
    <?php echo '<nav class="breadcrumbs"><a href="../">Home</a> <i class="fa-solid fa-chevron-right"></i> <a href="/'. $parent_slug .'">' . ucwords($parent_name) . '</a> <i class="fa-solid fa-chevron-right"></i> ' . get_the_title() . '</nav>';?>

<div class="entry-content">
<?php the_content(); ?>
<span style="position:absolute"><?php edit_post_link(__('Edit')); ?></span>


<?php

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
                                            <a href="/perfect-pairings/purchase/?ids=<?php echo implode(",", $products); ?>" data-batch-id="<?php echo $product; ?>" data-discount-rate="<?php echo get_field('discount_percentage', $product); ?>% Off" class="tooltip">
                                                <img src="<?php echo wp_get_attachment_image_url( get_post_thumbnail_id( $product ), 'samples' ); ?>"><span class="tooltiptext">ID:<?php echo $product; ?> <br><?php echo get_the_title( $product ); ?> <br><?php echo get_field('finish', $product); ?> <?php echo get_field('dimensions', $product); ?> <br>@ £<?php echo get_field('discounted_price_per_sqm', $product); ?>&nbsp;m²</span>
                                            </a>
                                        </div>

                                    <?php endforeach; ?>

                                </div><!-- end tile-pairing-option-swatch-loops -->
                                
                            </div><!-- end tile-pairing-option -->

                        <?php endforeach; ?>

                    </div><!-- end tile-pairing-options -->

                    <div class="tile-pairing-group-link"><a href="<?php echo $pairings['more_link']; ?>" data-attribute-topic="<?php echo $pairings['heading']; ?>">Discover More </a></div><!-- end tile-pairing-group-link -->
                </div><!-- end tile-pairing-group -->

            </div><!-- end tile-pairing-groups -->

        <?php endforeach; ?>

    </div><!-- end tile-pairing-container -->
    
    </div>
</article>
</div>


<?php get_footer(); ?>
