<?php
/**
 * Homepage Section: Team
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Section Settings from Customizer
|--------------------------------------------------------------------------
*/
$subtitle      = get_theme_mod( 'nosfir_homepage_team_subtitle', __( 'Our Team', 'nosfir' ) );
$title         = get_theme_mod( 'nosfir_homepage_team_title', __( 'Meet The Experts', 'nosfir' ) );
$description   = get_theme_mod( 'nosfir_homepage_team_description', '' );
$columns       = get_theme_mod( 'nosfir_homepage_team_columns', 4 );
$style         = get_theme_mod( 'nosfir_homepage_team_style', 'cards' ); // cards, minimal, rounded
$show_social   = get_theme_mod( 'nosfir_homepage_team_show_social', true );
$show_bio      = get_theme_mod( 'nosfir_homepage_team_show_bio', true );

/*
|--------------------------------------------------------------------------
| Team Members Data
|--------------------------------------------------------------------------
*/
$team_members = apply_filters( 'nosfir_homepage_team_members', array(
    array(
        'name'     => get_theme_mod( 'nosfir_homepage_team_1_name', __( 'John Doe', 'nosfir' ) ),
        'role'     => get_theme_mod( 'nosfir_homepage_team_1_role', __( 'CEO & Founder', 'nosfir' ) ),
        'bio'      => get_theme_mod( 'nosfir_homepage_team_1_bio', '' ),
        'image'    => get_theme_mod( 'nosfir_homepage_team_1_image', '' ),
        'facebook' => get_theme_mod( 'nosfir_homepage_team_1_facebook', '' ),
        'twitter'  => get_theme_mod( 'nosfir_homepage_team_1_twitter', '' ),
        'linkedin' => get_theme_mod( 'nosfir_homepage_team_1_linkedin', '' ),
        'email'    => get_theme_mod( 'nosfir_homepage_team_1_email', '' ),
    ),
    array(
        'name'     => get_theme_mod( 'nosfir_homepage_team_2_name', __( 'Jane Smith', 'nosfir' ) ),
        'role'     => get_theme_mod( 'nosfir_homepage_team_2_role', __( 'Creative Director', 'nosfir' ) ),
        'bio'      => get_theme_mod( 'nosfir_homepage_team_2_bio', '' ),
        'image'    => get_theme_mod( 'nosfir_homepage_team_2_image', '' ),
        'facebook' => get_theme_mod( 'nosfir_homepage_team_2_facebook', '' ),
        'twitter'  => get_theme_mod( 'nosfir_homepage_team_2_twitter', '' ),
        'linkedin' => get_theme_mod( 'nosfir_homepage_team_2_linkedin', '' ),
        'email'    => get_theme_mod( 'nosfir_homepage_team_2_email', '' ),
    ),
    array(
        'name'     => get_theme_mod( 'nosfir_homepage_team_3_name', __( 'Mike Johnson', 'nosfir' ) ),
        'role'     => get_theme_mod( 'nosfir_homepage_team_3_role', __( 'Lead Developer', 'nosfir' ) ),
        'bio'      => get_theme_mod( 'nosfir_homepage_team_3_bio', '' ),
        'image'    => get_theme_mod( 'nosfir_homepage_team_3_image', '' ),
        'facebook' => get_theme_mod( 'nosfir_homepage_team_3_facebook', '' ),
        'twitter'  => get_theme_mod( 'nosfir_homepage_team_3_twitter', '' ),
        'linkedin' => get_theme_mod( 'nosfir_homepage_team_3_linkedin', '' ),
        'email'    => get_theme_mod( 'nosfir_homepage_team_3_email', '' ),
    ),
    array(
        'name'     => get_theme_mod( 'nosfir_homepage_team_4_name', __( 'Sarah Williams', 'nosfir' ) ),
        'role'     => get_theme_mod( 'nosfir_homepage_team_4_role', __( 'Marketing Manager', 'nosfir' ) ),
        'bio'      => get_theme_mod( 'nosfir_homepage_team_4_bio', '' ),
        'image'    => get_theme_mod( 'nosfir_homepage_team_4_image', '' ),
        'facebook' => get_theme_mod( 'nosfir_homepage_team_4_facebook', '' ),
        'twitter'  => get_theme_mod( 'nosfir_homepage_team_4_twitter', '' ),
        'linkedin' => get_theme_mod( 'nosfir_homepage_team_4_linkedin', '' ),
        'email'    => get_theme_mod( 'nosfir_homepage_team_4_email', '' ),
    ),
) );

// Remove empty team members
$team_members = array_filter( $team_members, function( $member ) {
    return ! empty( $member['name'] );
});

if ( empty( $team_members ) ) {
    return;
}

/*
|--------------------------------------------------------------------------
| Section Classes
|--------------------------------------------------------------------------
*/
$section_classes = array(
    'homepage-section',
    'homepage-team',
    'team-style-' . sanitize_html_class( $style ),
    'team-columns-' . absint( $columns ),
);

/**
 * Filter section classes
 */
$section_classes = apply_filters( 'nosfir_homepage_team_classes', $section_classes );

/*
|--------------------------------------------------------------------------
| Social Icons SVG
|--------------------------------------------------------------------------
*/
$social_icons = array(
    'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
    'twitter'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>',
    'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
    'email'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
);

?>

<section id="homepage-team" class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="container">
        
        <header class="section-header">
            <?php if ( $subtitle ) : ?>
                <span class="section-subtitle"><?php echo esc_html( $subtitle ); ?></span>
            <?php endif; ?>
            
            <?php if ( $title ) : ?>
                <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            
            <?php if ( $description ) : ?>
                <p class="section-description"><?php echo wp_kses_post( $description ); ?></p>
            <?php endif; ?>
        </header>
        
        <div class="team-grid" style="--columns: <?php echo absint( $columns ); ?>">
            <?php foreach ( $team_members as $member ) : ?>
                <div class="team-member">
                    <div class="team-member-inner">
                        
                        <div class="team-member-image">
                            <?php if ( ! empty( $member['image'] ) ) : ?>
                                <img src="<?php echo esc_url( $member['image'] ); ?>" 
                                     alt="<?php echo esc_attr( $member['name'] ); ?>" 
                                     loading="lazy">
                            <?php else : ?>
                                <div class="team-member-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ( $show_social ) : ?>
                                <?php
                                $has_social = ! empty( $member['facebook'] ) || 
                                              ! empty( $member['twitter'] ) || 
                                              ! empty( $member['linkedin'] ) || 
                                              ! empty( $member['email'] );
                                              
                                if ( $has_social ) :
                                ?>
                                    <div class="team-member-social">
                                        <?php if ( ! empty( $member['facebook'] ) ) : ?>
                                            <a href="<?php echo esc_url( $member['facebook'] ); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               aria-label="<?php printf( esc_attr__( '%s on Facebook', 'nosfir' ), $member['name'] ); ?>">
                                                <?php echo $social_icons['facebook']; ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ( ! empty( $member['twitter'] ) ) : ?>
                                            <a href="<?php echo esc_url( $member['twitter'] ); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               aria-label="<?php printf( esc_attr__( '%s on Twitter', 'nosfir' ), $member['name'] ); ?>">
                                                <?php echo $social_icons['twitter']; ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ( ! empty( $member['linkedin'] ) ) : ?>
                                            <a href="<?php echo esc_url( $member['linkedin'] ); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               aria-label="<?php printf( esc_attr__( '%s on LinkedIn', 'nosfir' ), $member['name'] ); ?>">
                                                <?php echo $social_icons['linkedin']; ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ( ! empty( $member['email'] ) ) : ?>
                                            <a href="mailto:<?php echo esc_attr( $member['email'] ); ?>"
                                               aria-label="<?php printf( esc_attr__( 'Email %s', 'nosfir' ), $member['name'] ); ?>">
                                                <?php echo $social_icons['email']; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="team-member-info">
                            <?php if ( ! empty( $member['name'] ) ) : ?>
                                <h3 class="team-member-name"><?php echo esc_html( $member['name'] ); ?></h3>
                            <?php endif; ?>
                            
                            <?php if ( ! empty( $member['role'] ) ) : ?>
                                <span class="team-member-role"><?php echo esc_html( $member['role'] ); ?></span>
                            <?php endif; ?>
                            
                            <?php if ( $show_bio && ! empty( $member['bio'] ) ) : ?>
                                <p class="team-member-bio"><?php echo wp_kses_post( $member['bio'] ); ?></p>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php
        /**
         * Hook: nosfir_homepage_team_after
         */
        do_action( 'nosfir_homepage_team_after' );
        ?>
        
    </div>
</section>