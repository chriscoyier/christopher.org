<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

$links_count = count($links);
?>

<div class="prli-footer-promotion">
  <p><?php echo esc_html($title); ?>

  <ul class="prli-footer-promotion-links">
    <?php foreach($links as $key => $item): ?>
      <li>
        <?php
        $attributes = array(
          'href' => esc_url($item['url']),
          'target' => isset($item['target']) ? $item['target'] : false,
          'rel' => isset($item['target']) ? 'noopener noreferrer' : false
        );

        $attribute_str = '';

        foreach($attributes as $attr_key => $attr_item) {
          if($attr_item) {
            $attribute_str .= sprintf('%s="%s"', $attr_key, esc_attr($attr_item));
          }
        }

        printf(
          '<a %1$s>%2$s</a>%3$s',
          $attribute_str,
          esc_html($item['text']),
          $links_count === $key + 1 ? '' : '<span>/</span>'
        );
        ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <ul class="prli-footer-promotion-social">
    <li>
      <a href="https://www.facebook.com/prettylink" target="_blank" rel="noopener noreferrer">
        <svg width="16" height="16" aria-hidden="true">
          <path fill="#A7AAAD" d="M16 8.05A8.02 8.02 0 0 0 8 0C3.58 0 0 3.6 0 8.05A8 8 0 0 0 6.74 16v-5.61H4.71V8.05h2.03V6.3c0-2.02 1.2-3.15 3-3.15.9 0 1.8.16 1.8.16v1.98h-1c-1 0-1.31.62-1.31 1.27v1.49h2.22l-.35 2.34H9.23V16A8.02 8.02 0 0 0 16 8.05Z"/>
        </svg>
        <span class="screen-reader-text"><?php echo esc_html('Facebook'); ?></span>
      </a>
    </li>
    <li>
      <a href="https://www.linkedin.com/showcase/pretty-links/" target="_blank" rel="noopener noreferrer">
        <svg width="16" height="16" aria-hidden="true">
          <path fill="#A7AAAD" d="M14 1H1.97C1.44 1 1 1.47 1 2.03V14c0 .56.44 1 .97 1H14a1 1 0 0 0 1-1V2.03C15 1.47 14.53 1 14 1ZM5.22 13H3.16V6.34h2.06V13ZM4.19 5.4a1.2 1.2 0 0 1-1.22-1.18C2.97 3.56 3.5 3 4.19 3c.65 0 1.18.56 1.18 1.22 0 .66-.53 1.19-1.18 1.19ZM13 13h-2.1V9.75C10.9 9 10.9 8 9.85 8c-1.1 0-1.25.84-1.25 1.72V13H6.53V6.34H8.5v.91h.03a2.2 2.2 0 0 1 1.97-1.1c2.1 0 2.5 1.41 2.5 3.2V13Z"/>
        </svg>
        <span class="screen-reader-text"><?php echo esc_html('LinkedIn'); ?></span>
      </a>
    </li>
    <li>
      <a href="https://twitter.com/prettylink" target="_blank" rel="noopener noreferrer">
        <svg width="17" height="16" aria-hidden="true">
          <path fill="#A7AAAD" d="M15.27 4.43A7.4 7.4 0 0 0 17 2.63c-.6.27-1.3.47-2 .53a3.41 3.41 0 0 0 1.53-1.93c-.66.4-1.43.7-2.2.87a3.5 3.5 0 0 0-5.96 3.2 10.14 10.14 0 0 1-7.2-3.67C.86 2.13.7 2.73.7 3.4c0 1.2.6 2.26 1.56 2.89a3.68 3.68 0 0 1-1.6-.43v.03c0 1.7 1.2 3.1 2.8 3.43-.27.06-.6.13-.9.13a3.7 3.7 0 0 1-.66-.07 3.48 3.48 0 0 0 3.26 2.43A7.05 7.05 0 0 1 0 13.24a9.73 9.73 0 0 0 5.36 1.57c6.42 0 9.91-5.3 9.91-9.92v-.46Z"/>
        </svg>
        <span class="screen-reader-text"><?php echo esc_html('Twitter'); ?></span>
      </a>
    </li>
  </ul>
</div>