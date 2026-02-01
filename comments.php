<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nosfir
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Post Comments', 'nosfir' ); ?>">
	<div class="comments-wrapper">

		<?php
		// You can start editing here -- including this comment!
		if ( have_comments() ) :
			?>
			<div class="comments-header">
				<h2 class="comments-title">
					<?php
					$nosfir_comment_count = get_comments_number();
					if ( '1' === $nosfir_comment_count ) {
						printf(
							/* translators: 1: title. */
							esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'nosfir' ),
							'<span>' . wp_kses_post( get_the_title() ) . '</span>'
						);
					} else {
						printf( 
							/* translators: 1: comment count number, 2: title. */
							esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $nosfir_comment_count, 'comments title', 'nosfir' ) ),
							number_format_i18n( $nosfir_comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'<span>' . wp_kses_post( get_the_title() ) . '</span>'
						);
					}
					?>
				</h2><!-- .comments-title -->

				<?php
				// Comments statistics
				if ( get_theme_mod( 'nosfir_show_comment_stats', true ) ) :
					$comment_authors = get_comments( array(
						'post_id' => get_the_ID(),
						'count'   => false,
						'fields'  => 'comment_author',
					) );
					$unique_authors = count( array_unique( $comment_authors ) );
					?>
					<div class="comments-stats">
						<span class="stat-item">
							<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
							</svg>
							<?php
							printf(
								/* translators: %s: number of comments */
								esc_html( _n( '%s Comment', '%s Comments', $nosfir_comment_count, 'nosfir' ) ),
								number_format_i18n( $nosfir_comment_count )
							);
							?>
						</span>
						<span class="stat-item">
							<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
							</svg>
							<?php
							printf(
								/* translators: %s: number of participants */
								esc_html( _n( '%s Participant', '%s Participants', $unique_authors, 'nosfir' ) ),
								number_format_i18n( $unique_authors )
							);
							?>
						</span>
					</div>
				<?php endif; ?>

				<?php
				// Comment sorting
				if ( get_theme_mod( 'nosfir_comment_sorting', true ) ) : ?>
					<div class="comments-sorting">
						<label for="comment-sort" class="screen-reader-text"><?php esc_html_e( 'Sort comments', 'nosfir' ); ?></label>
						<select id="comment-sort" class="comment-sort-select">
							<option value="newest"><?php esc_html_e( 'Newest First', 'nosfir' ); ?></option>
							<option value="oldest"><?php esc_html_e( 'Oldest First', 'nosfir' ); ?></option>
							<option value="popular"><?php esc_html_e( 'Most Popular', 'nosfir' ); ?></option>
						</select>
					</div>
				<?php endif; ?>
			</div><!-- .comments-header -->

			<?php the_comments_navigation(); ?>

			<ol class="comment-list">
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 60,
						'callback'    => 'nosfir_comment',
						'type'        => 'comment',
					)
				);
				?>
			</ol><!-- .comment-list -->

			<?php
			// Display pingbacks/trackbacks separately
			$pingbacks = get_comments(
				array(
					'post_id' => get_the_ID(),
					'type'    => 'pings',
					'status'  => 'approve',
				)
			);

			if ( ! empty( $pingbacks ) ) : ?>
				<div class="pingbacks-area">
					<h3 class="pingbacks-title">
						<?php
						printf(
							/* translators: %s: number of pingbacks */
							esc_html( _n( '%s Pingback', '%s Pingbacks', count( $pingbacks ), 'nosfir' ) ),
							number_format_i18n( count( $pingbacks ) )
						);
						?>
					</h3>
					<ol class="pingback-list">
						<?php
						wp_list_comments(
							array(
								'style'       => 'ol',
								'short_ping'  => true,
								'type'        => 'pings',
							)
						);
						?>
					</ol>
				</div>
			<?php endif; ?>

			<?php
			the_comments_navigation();

			// If comments are closed and there are comments, let's leave a little note, shall we?
			if ( ! comments_open() ) :
				?>
				<div class="comments-closed-notice">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
					</svg>
					<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'nosfir' ); ?></p>
				</div>
				<?php
			endif;

		endif; // Check for have_comments().

		// Comment Form
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		$aria_req  = ( $req ? " aria-required='true'" : '' );
		$html_req  = ( $req ? " required='required'" : '' );
		$consent   = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

		// Custom fields for the comment form
		$fields = array(
			'author' => '<div class="comment-form-fields"><div class="comment-form-field comment-form-author">' .
						'<label for="author">' . esc_html__( 'Name', 'nosfir' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' .
						'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' placeholder="' . esc_attr__( 'Your Name', 'nosfir' ) . '" />' .
						'</div>',
			
			'email'  => '<div class="comment-form-field comment-form-email">' .
						'<label for="email">' . esc_html__( 'Email', 'nosfir' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' .
						'<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req . ' placeholder="' . esc_attr__( 'your@email.com', 'nosfir' ) . '" />' .
						'</div>',
			
			'url'    => '<div class="comment-form-field comment-form-url">' .
						'<label for="url">' . esc_html__( 'Website', 'nosfir' ) . '</label>' .
						'<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" placeholder="' . esc_attr__( 'https://yourwebsite.com', 'nosfir' ) . '" />' .
						'</div></div>',
			
			'cookies' => '<p class="comment-form-cookies-consent">' .
						 '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' .
						 '<label for="wp-comment-cookies-consent">' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'nosfir' ) . '</label>' .
						 '</p>',
		);

		// Comment form arguments
		$args = array(
			'id_form'              => 'commentform',
			'class_form'           => 'comment-form',
			'id_submit'            => 'submit',
			'class_submit'         => 'submit-button',
			'name_submit'          => 'submit',
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			'title_reply'          => esc_html__( 'Leave a Reply', 'nosfir' ),
			'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'nosfir' ),
			'cancel_reply_link'    => esc_html__( 'Cancel Reply', 'nosfir' ),
			'label_submit'         => esc_html__( 'Post Comment', 'nosfir' ),
			'format'               => 'xhtml',
			'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
			
			'comment_field'        => '<div class="comment-form-comment">' .
									   '<label for="comment">' . esc_html__( 'Comment', 'nosfir' ) . ' <span class="required">*</span></label>' .
									   '<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required" placeholder="' . esc_attr__( 'Share your thoughts...', 'nosfir' ) . '"></textarea>' .
									   '<div class="comment-form-toolbar">' .
									   '<span class="character-count" data-max="65525">0 / 65525</span>' .
									   '</div>' .
									   '</div>',
			
			'comment_notes_before' => '<p class="comment-notes">' .
									   '<span id="email-notes">' . esc_html__( 'Your email address will not be published.', 'nosfir' ) . '</span> ' .
									   ( $req ? '<span class="required-field-message">' . sprintf( /* translators: %s: required field symbol */ esc_html__( 'Required fields are marked %s', 'nosfir' ), '<span class="required">*</span>' ) . '</span>' : '' ) .
									   '</p>',
			
			'comment_notes_after'  => '',
			
			'logged_in_as'         => '<p class="logged-in-as">' .
									   sprintf(
										   /* translators: 1: edit user link, 2: accessibility text, 3: user name, 4: logout URL */
										   __( 'Logged in as <a href="%1$s" aria-label="%2$s">%3$s</a>. <a href="%4$s">Log out?</a>', 'nosfir' ),
										   get_edit_user_link(),
										   /* translators: %s: user name */
										   esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.', 'nosfir' ), $user_identity ) ),
										   $user_identity,
										   wp_logout_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ) ) )
									   ) . '</p>',
		);

		// Add a wrapper div around the comment form
		echo '<div class="comment-respond-wrapper">';
		
		// Add comment policy if set
		if ( get_theme_mod( 'nosfir_comment_policy_text' ) ) : ?>
			<div class="comment-policy">
				<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
				</svg>
				<p><?php echo wp_kses_post( get_theme_mod( 'nosfir_comment_policy_text' ) ); ?></p>
			</div>
		<?php endif;
		
		// Display the comment form
		comment_form( apply_filters( 'nosfir_comment_form_args', $args ) );
		
		echo '</div><!-- .comment-respond-wrapper -->';

		// Add comment guidelines if enabled
		if ( get_theme_mod( 'nosfir_show_comment_guidelines', false ) ) : ?>
			<div class="comment-guidelines">
				<h4><?php esc_html_e( 'Comment Guidelines', 'nosfir' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'Be respectful and courteous to others.', 'nosfir' ); ?></li>
					<li><?php esc_html_e( 'Stay on topic and contribute to the discussion.', 'nosfir' ); ?></li>
					<li><?php esc_html_e( 'No spam, self-promotion, or offensive content.', 'nosfir' ); ?></li>
					<li><?php esc_html_e( 'Use clear and concise language.', 'nosfir' ); ?></li>
				</ul>
			</div>
		<?php endif; ?>

	</div><!-- .comments-wrapper -->
</section><!-- #comments -->