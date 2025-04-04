<?php

/**
 * Admin Flyout Menu.
 */
class PrliFlyoutMenuController {

  public function load_hooks() {
    add_action( 'admin_footer', array( $this, 'output' ) );
  }

  /**
   * Output menu.
   *
   * @since 1.5.7
   */
  public function output() {

    if ( empty( $_GET['post_type'] ) || ( 'pretty-link' !== $_GET['post_type'] && 'pretty-link-groups' !== $_GET['post_type'] ) ) {
      return;
    }

    global $prli_notifications;
    $notifications_count = $prli_notifications->get_count();

    printf(
      '<div id="caseproof-flyout">
        <div id="caseproof-flyout-items">
          %1$s
        </div>
        <a href="#" id="caseproofFlyoutButton" class="caseproof-flyout-button caseproof-flyout-head">
          %5$s
          <div class="caseproof-flyout-label">%2$s</div>
          <img src="%3$s" alt="%2$s" data-active="%4$s" />
        </a>
      </div>',
      $this->get_items_html(),
      esc_attr__( 'See Quick Links', 'pretty-link' ),
      esc_url( PRLI_IMAGES_URL . '/admin-flyout-default.svg' ),
      esc_url( PRLI_IMAGES_URL . '/admin-flyout-active.svg' ),
      $notifications_count > 0 ? '<span id="prliAdminNotificationsBadge" class="prli-notifications-count">' . $notifications_count . '</span>' : ''
    );
  }

  /**
   * Generate menu items HTML.
   *
   * @since 1.5.7
   *
   * @return string Menu items HTML.
   */
  private function get_items_html() {

    $items      = array_reverse( $this->menu_items() );
    $items_html = '';

    foreach ( $items as $item_key => $item ) {
      $items_html .= sprintf(
        '<a id="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" class="caseproof-flyout-button caseproof-flyout-item caseproof-flyout-item-%3$d"%6$s%7$s>
          <div class="caseproof-flyout-label">%4$s</div>
          %5$s
        </a>',
        ! empty( $item['id'] ) ? esc_attr( $item['id'] ) : '',
        esc_url( $item['url'] ),
        (int) $item_key,
        wp_kses_post( $item['title'] ),
        $item['icon'],
        ! empty( $item['bgcolor'] ) ? ' style="background-color: ' . esc_attr( $item['bgcolor'] ) . '"' : '',
        ! empty( $item['hover_bgcolor'] ) ? ' onMouseOver="this.style.backgroundColor=\'' . esc_attr( $item['hover_bgcolor'] ) . '\'" onMouseOut="this.style.backgroundColor=\'' . esc_attr( $item['bgcolor'] ) . '\'"' : ''
      );
    }

    return $items_html;
  }

  /**
   * Menu items data.
   *
   * @since 1.5.7
   */
  private function menu_items() {

    global $prli_notifications;
    $notifications_count = $prli_notifications->get_count();
    $notifications_count_text = $notifications_count > 0 ? sprintf( '(%s)', $notifications_count ) : '';

    $items = array(
      array(
        'title'         => esc_html__( 'Upgrade to Pretty Links Pro', 'pretty-link' ),
        'url'           => 'https://prettylinks.com/pricing/plans/?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=quick_links_widget',
        'icon'          => '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 51 45" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" fill-rule="nonzero" transform="translate(-15.5823 -17.1195)"><path d="m52.6 36.6c-.3-.8-1-1.3-1.8-1.3l-11.5.1-3.3-10.1c-.2-.8-1-1.3-1.8-1.3s-1.5.5-1.9 1.3l-3.3 9.9-11.5.1c-.8 0-1.5.5-1.8 1.2s0 1.6.6 2l8.9 7.5-4.2 12.8s0 .1-.1.1c0 0 0 .1-.1.1v.9s0 .1.1.1c0 .1 0 .1.1.2 0 0 0 .1.1.1l.3.3c.1.1.2.1.2.2h.1c.1 0 .1.1.2.1h.1c.1 0 .1 0 .2.1h.9c.1 0 .2 0 .2-.1h.1c.1 0 .2-.1.3-.1l10.2-8.1 9.6 8.2c.3.3.8.4 1.2.5.4-.1.7-.1 1.1-.3.6-.5 1-1.3.7-2.1l-4.1-12.6 9.3-7.6c.9-.6 1.2-1.4.9-2.2"/><path d="m40.1 25.9 5.1 2.1 2.1 5.1c.2.3.6.5.9.3.1-.1.2-.2.3-.3l2.1-5.1 5.1-2.1c.3-.2.5-.6.3-.9-.1-.1-.2-.2-.3-.3l-5.1-2.1-2.1-5.1c-.2-.3-.6-.5-.9-.3-.1.1-.2.2-.3.3l-2.1 5.1-5.1 2.1c-.3.2-.5.6-.3.9.1.1.2.2.3.3"/><path d="m66 39.7-4.2-1.7-1.7-4.1c-.1-.3-.5-.4-.7-.2-.1.1-.2.1-.2.2l-1.8 4.1-4.1 1.7c-.3.1-.4.5-.2.7.1.1.1.2.2.2l4.1 1.7 1.7 4.1c.1.3.5.4.7.2.1-.1.2-.1.2-.2l1.7-4.1 4.1-1.7c.3-.1.4-.5.2-.7.2 0 .1-.1 0-.2z"/></g></svg>',
        'bgcolor'       => '#E1772F',
        'hover_bgcolor' => '#ff8931',
      ),
      array(
        'title' => esc_html__( 'Support & Docs', 'pretty-link' ),
        'url'   => 'https://prettylinks.com/support/?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=quick_links_widget',
        'icon'  => '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 42 37" xmlns="http://www.w3.org/2000/svg"><path d="m47.9 39.3v2.6c0 .5-.4 1-1 1h-4.6v4.6c0 .5-.4 1-1 1h-2.6c-.5 0-1-.4-1-1v-4.6h-4.6c-.5 0-1-.4-1-1v-2.6c0-.5.4-1 1-1h4.6v-4.6c0-.5.4-1 1-1h2.6c.5 0 1 .4 1 1v4.6h4.6c.5 0 1 .5 1 1m-24.4 13.1c-.3 0-.5-.2-.5-.5v-22.6c0-.3.2-.5.5-.5h33c.3 0 .5.2.5.5v22.6c0 .3-.2.5-.5.5zm12.6-28.8h7.9v1.3h-7.9zm20.9 1.3h-9.2v-1.3c0-2.2-1.8-3.9-3.9-3.9h-7.9c-2.2 0-3.9 1.8-3.9 3.9v1.3h-9.1c-2.2 0-3.9 1.8-3.9 3.9v23.6c0 2.2 1.8 3.9 3.9 3.9h34c2.2 0 3.9-1.8 3.9-3.9v-23.6c0-2.1-1.7-3.9-3.9-3.9" fill="#fff" fill-rule="nonzero" transform="translate(-19.1 -19.7)"/></svg>',
      ),
      array(
        'title' => esc_html__( 'Suggest a Feature', 'pretty-link' ),
        'url'   => 'https://prettylinks.com/contact/?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=quick_links_widget',
        'icon'  => '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 53 43" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" fill-rule="nonzero" transform="translate(-13.5 -19.9)"><path d="m22.8 35.7c0-1.1-.9-2-2-2h-5.3c-1.1 0-2 .9-2 2s.9 2 2 2h5.3c1.1 0 2-.9 2-2"/><path d="m64.5 33.7h-5.3c-1.1 0-2 .9-2 2s.9 2 2 2h5.3c1.1 0 2-.9 2-2s-1-2-2-2"/><path d="m22.4 43.6-4.6 2.6c-1 .5-1.3 1.7-.8 2.7s1.7 1.3 2.7.8h.1l4.6-2.6c.9-.6 1.2-1.8.7-2.7-.6-1-1.8-1.3-2.7-.8"/><path d="m56.6 28.1c.3 0 .7-.1 1-.3l4.6-2.6c.9-.6 1.2-1.8.7-2.7s-1.7-1.2-2.7-.7l-4.6 2.6c-.9.5-1.3 1.8-.7 2.7.3.6 1 1 1.7 1"/><path d="m24.5 24.5-4.6-2.7c-.9-.6-2.2-.3-2.7.7-.6.9-.3 2.2.7 2.7h.1l4.6 2.6c.9.6 2.2.3 2.7-.7s.1-2.1-.8-2.6"/><path d="m49.6 23.5c-2.7-2.3-6.1-3.6-9.6-3.6-8.4 0-14.5 6.9-14.5 14.5 0 3.5 1.3 6.9 3.6 9.6 1.4 1.6 3.5 4.9 4.3 7.6h4c0-.4-.1-.8-.2-1.2-.5-1.5-1.9-5.3-5.1-9.1-1.7-1.9-2.6-4.4-2.6-7 0-6.1 4.9-10.6 10.6-10.6 5.8 0 10.6 4.7 10.6 10.6 0 2.6-.9 5-2.6 7-3.2 3.7-4.7 7.6-5.1 9-.1.4-.2.8-.2 1.2h4c.8-2.8 3-6 4.3-7.6 5.1-6 4.5-15.2-1.5-20.4"/><path d="m33.4 57.8c0 .3.1.5.2.7l2 3c.2.4.7.6 1.1.6h6.5c.4 0 .9-.2 1.1-.6l2-3c.1-.2.2-.5.2-.7v-3.6h-13.1z"/><path d="m62.2 46.2-4.6-2.6c-.9-.6-2.2-.3-2.7.7-.6.9-.3 2.2.7 2.7h.1l4.6 2.6c1 .5 2.2.2 2.7-.8.4-.9.1-2-.8-2.6"/><path d="m40 26.5c-4.4 0-7.9 3.6-7.9 7.9 0 .7.6 1.3 1.3 1.3s1.3-.6 1.3-1.3c0-2.9 2.4-5.3 5.3-5.3.7 0 1.3-.6 1.3-1.3s-.6-1.3-1.3-1.3"/></g></svg>',
      ),
      array(
        'id' => 'prliAdminNotifications',
        'title' => esc_html__( 'Notifications ', 'pretty-link' ) . '<span id="prliNotificationsCount"> ' . $notifications_count_text . ' </span>',
        'url'   => '#',
        'icon'  => '<svg width="22" height="14" viewBox="0 0 22 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.6944 6.5625C21.8981 6.85417 22 7.18229 22 7.54687V12.25C22 12.7361 21.8218 13.1493 21.4653 13.4896C21.1088 13.8299 20.6759 14 20.1667 14H1.83333C1.32407 14 0.891204 13.8299 0.534722 13.4896C0.178241 13.1493 0 12.7361 0 12.25V7.54687C0 7.18229 0.101852 6.85417 0.305556 6.5625L4.35417 0.765625C4.45602 0.644097 4.58333 0.522569 4.73611 0.401042C4.91435 0.279514 5.10532 0.182292 5.30903 0.109375C5.51273 0.0364583 5.7037 0 5.88194 0H16.1181C16.3981 0 16.6782 0.0850694 16.9583 0.255208C17.2639 0.401042 17.4931 0.571181 17.6458 0.765625L21.6944 6.5625ZM6.1875 2.33333L2.94097 7H7.63889L8.86111 9.33333H13.1389L14.3611 7H19.059L15.8125 2.33333H6.1875Z" fill="#ffffff"></path></svg>',
      )
    );


    return $items;
  }
}
