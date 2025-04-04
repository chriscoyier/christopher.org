<?php

class PrliLinksHelper {

  public static function redirect_type_dropdown($fieldname, $value='', $extra_options=array(), $classes='') {
    $idname = preg_match('#^.*\[(.*?)\]$#',$fieldname,$matches)?$matches[1]:$fieldname;
    ?>
    <select id="<?php echo esc_attr($idname); ?>" name="<?php echo esc_attr($fieldname); ?>" class="<?php echo esc_attr($classes); ?>">
      <?php if( !empty($extra_options) ): ?>
        <?php foreach( $extra_options as $exoptkey => $exoptval ): ?>
          <option value="<?php echo esc_attr($exoptval); ?>"><?php echo esc_html($exoptkey); ?>&nbsp;</option>
        <?php endforeach; ?>
      <?php endif; ?>
      <option value="307" <?php selected((int)$value,(int)307); ?>><?php esc_html_e('307 (Temporary)', 'pretty-link') ?>&nbsp;</option>
      <option value="302" <?php selected((int)$value,(int)302); ?>><?php esc_html_e('302 (Temporary)', 'pretty-link') ?>&nbsp;</option>
      <option value="301" <?php selected((int)$value,(int)301); ?>><?php esc_html_e('301 (Permanent)', 'pretty-link') ?>&nbsp;</option>
      <?php do_action('prli_redirection_types', array(), $value); ?>
    </select>
    <?php
  }

  public static function link_list_icons($link) {
    do_action('prli_list_icon',$link->id);

    switch( $link->redirect_type ):
      case 'prettybar': ?>
        <i title="<?php esc_attr_e('PrettyBar Redirection', 'pretty-link'); ?>" class="pl-icon-star pl-list-icon"></i><?php
        break;
      case 'cloak': ?>
        <i title="<?php esc_attr_e('Cloaked Redirection', 'pretty-link'); ?>" class="pl-icon-cloak pl-list-icon"></i><?php
        break;
      case 'pixel': ?>
        <i title="<?php esc_attr_e('Pixel Tracking Redirection', 'pretty-link'); ?>" class="pl-icon-eye-off pl-list-icon"></i><?php
        break;
      case 'metarefresh': ?>
        <i title="<?php esc_attr_e('Meta Refresh Redirection', 'pretty-link'); ?>" class="pl-icon-cw pl-list-icon"></i><?php
        break;
      case 'javascript': ?>
        <i title="<?php esc_attr_e('Javascript Redirection', 'pretty-link'); ?>" class="pl-icon-code pl-list-icon"></i><?php
        break;
      case '307': ?>
        <i title="<?php esc_attr_e('Temporary (307) Redirection', 'pretty-link'); ?>" class="pl-icon-307 pl-list-icon"></i><?php
        break;
      case '302': /* Using 307 Icon for now */ ?>
        <i title="<?php esc_attr_e('Temporary (302) Redirection', 'pretty-link'); ?>" class="pl-icon-307 pl-list-icon"></i><?php
        break;
      case '301': ?>
        <i title="<?php esc_attr_e('Permanent (301) Redirection', 'pretty-link'); ?>" class="pl-icon-301 pl-list-icon"></i><?php
    endswitch;

    if( $link->nofollow ): ?>
      <i title="<?php esc_attr_e('Nofollow Enabled', 'pretty-link'); ?>" class="pl-icon-cancel-circled pl-list-icon"></i><?php
    endif;

    if( $link->sponsored ): ?>
      <i title="<?php esc_attr_e('Sponsored Enabled', 'pretty-link'); ?>" class="pl-icon-ok-circle pl-list-icon"></i><?php
    endif;

    if(!empty($link->param_forwarding) && $link->param_forwarding != 'off'): ?>
      <i title="<?php esc_attr_e('Parameter Forwarding Enabled', 'pretty-link'); ?>" class="pl-icon-forward pl-list-icon"></i><?php
    endif;

    do_action('prli_list_end_icon',$link);
  }

  public static function link_list_actions($link, $pretty_link_url) {
    global $prli_options;

    $link_nonce = wp_create_nonce( 'link-actions' );
    ?>
    <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link&action=edit&id=' . $link->id)); ?>" title="<?php echo esc_html(sprintf(__('Edit %s', 'pretty-link'), $link->slug)); ?>"><i class="pl-list-icon pl-icon-edit"></i></a>
    <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link&action=destroy&id={$link->id}&_wpnonce={$link_nonce}")); ?>" onclick="return confirm('<?php echo esc_attr(sprintf(__('Are you sure you want to delete your %s Pretty Link? This will delete the Pretty Link and all of the statistical data about it in your database.', 'pretty-link'), $link->name)); ?>');" title="<?php echo esc_attr(sprintf(__('Delete %s', 'pretty-link'), $link->slug)); ?>"><i class="pl-list-icon pl-icon-cancel"></i></a>
    <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link&action=reset&id={$link->id}&_wpnonce={$link_nonce}")); ?>" onclick="return confirm('<?php echo esc_attr(sprintf(__('Are you sure you want to reset your %s Pretty Link? This will delete all of the statistical data about this Pretty Link in your database.', 'pretty-link'), $link->name)); ?>');" title="<?php echo esc_attr(sprintf(__('Reset %s', 'pretty-link'), $link->name)); ?>"><i class="pl-list-icon pl-icon-reply"></i></a>
    <?php
      if( $link->track_me and $prli_options->extended_tracking!='count' ):
        ?><a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-clicks&l={$link->id}")); ?>" title="<?php echo esc_attr(sprintf(__('View clicks for %s', 'pretty-link'), $link->slug)); ?>"><i class="pl-list-icon pl-icon-chart-line"></i></a><?php
        do_action('prli-link-action',$link->id);
      endif;

      if( $link->redirect_type != 'pixel' ):
        ?><a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . urlencode($pretty_link_url)); ?>" target="_blank" title="<?php echo esc_attr(sprintf(__('Post %s to Twitter', 'pretty-link'), $pretty_link_url)); ?>"><i class="pl-list-icon pl-icon-twitter"></i></a>
        <a href="<?php echo esc_url('mailto:?subject=Pretty Link&body=' . urlencode($pretty_link_url)); ?>" target="_blank" title="<?php echo esc_attr(sprintf(__('Send %s in an Email', 'pretty-link'), $pretty_link_url)); ?>"><i class="pl-list-icon pl-icon-mail"></i></a><?php
      endif;
    ?>

    <?php if( $link->redirect_type != 'pixel' ): ?>
      <a href="<?php echo esc_url($link->url); ?>" target="_blank" title="<?php echo esc_attr(sprintf(__('Visit Target URL: %s in a New Window', 'pretty-link'), $link->url)); ?>"><i class="pl-icon-link-ext pl-list-icon"></i></a>
      <a href="<?php echo esc_url($pretty_link_url); ?>" target="_blank" title="<?php echo esc_attr(sprintf( __('Visit Short URL: %s in a New Window', 'pretty-link'), $pretty_link_url)); ?>"><i class="pl-icon-link-ext pl-list-icon"></i></a><?php
    endif;

    do_action('prli-special-link-action',$link->id);
  }

  public static function link_list_clicks($link) {
    global $prli_options;

    if($link->track_me) {
      $clicks = ( empty($link->clicks) ? 0 : $link->clicks );
      $uniques = ( empty($link->uniques) ? 0 : $link->uniques );
      $click_str = "{$clicks}/{$uniques}";

      if($prli_options->extended_tracking !== 'count') {
        ?>
          <a href="<?php echo esc_url(admin_url( "admin.php?page=pretty-link-clicks&l={$link->id}" )); ?>"
             id="link_clicks_<?php echo esc_attr($link->id); ?>"
             title="<?php echo esc_attr(sprintf(__('%d Clicks / %d Uniques', 'pretty-link'), $clicks, $uniques)); ?>"><?php echo esc_html($click_str); ?></a>
        <?php
      }
      else {
        echo esc_html($click_str);
      }
    }
    else {
      ?>
      <img src="<?php echo PRLI_IMAGES_URL.'/not_tracking.png'; ?>" title="<?php esc_attr_e('This link isn\'t being tracked', 'pretty-link'); ?>" />
      <?php
    }
  }

  public static function link_list_url_clipboard($link) {
    global $prli_link;

    $pretty_link_url = $prli_link->get_pretty_link_url($link->slug, true);

    ?>
      <input type='text'
             readonly="true"
             style="width: 65%;"
             onclick='this.select();'
             onfocus='this.select();'
             value="/<?php echo esc_attr( $link->slug ); ?>" />
        <span class="list-clipboard prli-clipboard">
          <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs"
             data-clipboard-text="<?php echo esc_url($pretty_link_url); ?>"></i>
        </span>
        <?php if( 0 ): // $link->redirect_type !== 'pixel' ): ?>
          <div style="font-size: 8px;"
               title="<?php echo esc_url($link->url); ?>">
            <strong><?php esc_html_e('Target URL:', 'pretty-link'); ?></strong>
            <?php echo esc_html(substr($link->url,0,47) . ((strlen($link->url) >= 47)?'...':'')); ?>
          </div>
        <?php endif;
  }

  public static function link_action_reset($link, $title) {
    ob_start();
    ?>
      <a href=""
         data-id="<?php echo esc_attr($link->id); ?>"
         class="prli_reset_pretty_link"
         title="<?php echo esc_attr(sprintf( __('Reset %s', 'pretty-link'), $link->name )); ?>"><?php echo esc_html($title); ?></a>
    <?php

    return ob_get_clean();
  }

  public static function link_action_tweet($link, $title) {
    ob_start();
    ?>
      <a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . urlencode($link->pretty_url)); ?>"
         target="_blank"
         title="<?php echo esc_attr(sprintf( __('Post %s to Twitter', 'pretty-link'), $link->pretty_url )); ?>"><?php echo esc_html($title); ?></a>
    <?php

    return ob_get_clean();
  }

  public static function link_action_email($link, $title) {
    ob_start();
    ?>
      <a href="<?php echo esc_url('mailto:?subject=' . rawurlencode(__('Pretty Link', 'pretty-link')) . '&body=' . rawurlencode($link->pretty_url)); ?>"
         target="_blank"
         title="<?php echo esc_attr(sprintf( __('Send %s in an Email', 'pretty-link'), $link->pretty_url )); ?>"><?php echo esc_html($title); ?></a>
    <?php

    return ob_get_clean();
  }

  public static function link_action_visit_target($link, $title) {
    ob_start();
    ?>
      <a href="<?php echo esc_url($link->url); ?>"
         target="_blank"
         title="<?php echo esc_attr(sprintf(__('Visit Target URL: %s in a New Window', 'pretty-link'), $link->url)); ?>"><?php echo esc_html($title); ?></a>
    <?php

    return ob_get_clean();
  }

  public static function link_action_visit_pretty_link($link, $title) {
    ob_start();
    ?>
      <a href="<?php echo esc_url($link->pretty_url); ?>"
         target="_blank"
         title="<?php echo esc_attr(sprintf(__('Visit Short URL: %s in a New Window', 'pretty-link'), $link->pretty_url)); ?>"><?php echo esc_html($title); ?></a>
    <?php

    return ob_get_clean();
  }
}

