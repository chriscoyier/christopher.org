<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  if( isset($errors) and count($errors) > 0 )
  {
    ?>
<div class="error">
  <ul>
  <?php
    foreach( $errors as $error )
    {
      ?>
      <li><strong><?php esc_html_e('ERROR', 'pretty-link'); ?></strong>: <?php echo esc_html($error); ?></li>
      <?php
    }
  ?>
  </ul>
</div>
    <?php
  }
