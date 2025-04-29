<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="prli-onboarding">
  <div class="prli-onboarding-logo">
    <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/pl-logo-horiz-RGB.svg'); ?>" alt="">
  </div>

  <h1><?php esc_html_e('Welcome to Pretty Links', 'pretty-link'); ?></h1>
  <p class="prli-onboarding-intro">
    <?php esc_html_e('Welcome aboard the most powerful link management platform on the planet! We\'re thrilled to have you join our community of link-loving enthusiasts.', 'pretty-link'); ?>
  </p>
  <p class="prli-onboarding-intro">
    <?php esc_html_e('Here at Pretty Links, we believe every link deserves to be pretty, sleek, and oh-so-effective. Whether you\'re a seasoned affiliate marketer, a passionate blogger, or simply someone who wants their links to pop, we\'ve got the perfect tools to make it happen.', 'pretty-link'); ?>
  </p>
  <p class="prli-onboarding-intro">
    <?php esc_html_e('With just a few clicks, you can create customized, trackable, and shareable links that will leave a lasting impression. But that\'s not all! We also offer a range of advanced features, including comprehensive analytics, automated redirects, and so much more.', 'pretty-link'); ?>
  </p>
  <p class="prli-onboarding-intro">
    <?php _e('<i>What are you waiting for?</i> Let\'s make some <strong>link magic</strong> happen!', 'pretty-link'); ?>
  </p>
  <div class="prli-onboarding-get-started">
    <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-onboarding&step=1')); ?>"><?php esc_html_e('Get Started', 'pretty-link'); ?><img src="<?php echo esc_url(PRLI_IMAGES_URL . '/long-arrow-right.svg'); ?>" alt=""></a>
  </div>

  <div class="prli-welcome-steps">
    <div class="prli-welcome-step">
      <span class="welcome-step-number">1</span>
      <img src="<?php echo PRLI_IMAGES_URL . '/welcome-add-new.png'; ?>" alt="<?php esc_attr_e('Click "Add New Link"', 'pretty-link'); ?>">
      <?php esc_html_e('Click "Add New Link"', 'pretty-link'); ?>
    </div>
    <div class="prli-welcome-step">
      <span class="welcome-step-number">2</span>
      <img src="<?php echo PRLI_IMAGES_URL . '/welcome-enter-url.png'; ?>" alt="<?php esc_attr_e('Enter the URL of your Affiliate Link', 'pretty-link'); ?>">
      <?php esc_html_e('Enter the URL of your Affiliate Link', 'pretty-link'); ?>
    </div>
    <div class="prli-welcome-step">
      <span class="welcome-step-number">3</span>
      <img src="<?php echo PRLI_IMAGES_URL . '/welcome-customize-slug.png'; ?>" alt="<?php esc_attr_e('Customize your Pretty Link Slug', 'pretty-link'); ?>">
      <?php esc_html_e('Customize your Pretty Link Slug', 'pretty-link'); ?>
    </div>
    <div class="prli-welcome-step">
      <span class="welcome-step-number">4</span>
      <img src="<?php echo PRLI_IMAGES_URL . '/welcome-click-update.png'; ?>" alt="<?php esc_attr_e('Click "Update"', 'pretty-link'); ?>">
      <?php esc_html_e('Click "Update"', 'pretty-link'); ?>
    </div>
    <div class="prli-welcome-step">
      <span class="welcome-step-number">5</span>
      <img class="prli-welcome-step" src="<?php echo PRLI_IMAGES_URL . '/welcome-copy-url.png'; ?>" alt="<?php esc_attr_e('Copy the Pretty Link URL', 'pretty-link'); ?>">
      <?php esc_html_e('Copy the Pretty Link URL', 'pretty-link'); ?>
    </div>
  </div>

  <p><?php esc_html_e('Wasn\'t that easy? Now, you can use this link wherever you want!', 'pretty-link'); ?></p>

  <?php if(!in_array(PRLI_EDITION, array('pretty-link-executive'))): ?>
    <div class="pre-badge"><?php esc_html_e('Unlock the The Power of', 'pretty-link'); ?></div>

    <div class="prli-welcome-badge">
      <img src="<?php echo PRLI_IMAGES_URL . '/plp-dialog-logo.svg'; ?>" alt="<?php esc_attr_e('The Power of Pretty Links Pro', 'pretty-link'); ?>">
    </div>

    <p><?php _e('There are many reasons that premium users of Pretty Links <br> are able to take their business to the next level:', 'pretty-link'); ?></p>

    <div class="prlip-reasons">
      <?php if(!in_array(PRLI_EDITION, array('pretty-link-beginner', 'pretty-link-marketer', 'pretty-link-pro-blogger', 'pretty-link-pro-developer'))): ?>
        <div class="prlip-reason">
          <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/Automatic_Link_Placement.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Automatic Link Placement ', 'pretty-link'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('Put an end to the mind-numbing task of manually inserting links into your content. Let Pretty Links do the heavy lifting, effortlessly sneaking in your cash-loaded affiliate links while you sit back and chillax.', 'pretty-link'); ?></p>
              <p><?php _e('Here\'s how it works: You create a pretty link and let us know which keywords you wanna cash in on. Then, like a ninja, Pretty Links scans your entire website, spots those juicy keywords, and BAM – swaps \'em out with just one click. <i>Easy peasy.</i>', 'pretty-link'); ?></p>
            </div>
          </div>
        </div>
        <div class="prlip-reason">
          <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/Customized_Checkout_Links.jpg'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Customized Checkout Links', 'pretty-link'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('PrettyPay™ is here for when you\'re ready to start making money from your own creative projects. From doubling up on your content\'s profits to marketing those quirky crafts you love making, PrettyPay™ is ready to take your side hussle’s side hussle to the marketplace.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('Create personalized payment links powered by Stripe\'s reliable payment processing platform. Share them with your network, and watch as followers become your paying customers.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('*Upgrade to a Pretty Links Pro plan to avoid the 3% transaction fee on all sales.', 'pretty-link'); ?></p>
            </div>
          </div>
        </div>
        <div class="prlip-reason">
          <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/Streamlined_Categories_Tags.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Streamlined Categories & Tags', 'pretty-link'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('As a content creator constantly on the move, juggling links across multiple platforms can be a chaotic experience. But fear not, because Pretty Links is here to bring order to the madness.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('Take link organization to the next level with custom link categories. Effortlessly sort and group your links based on affiliate programs, platforms, campaigns, or any criteria that help you stay in control and on top of your marketing game.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('No more scrambling to find the right link. Everything is at your fingertips, neatly organized in one convenient location.', 'pretty-link'); ?></p>
            </div>
          </div>
        </div>
        <div class="prlip-reason">
          <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/Advanced_Redirect_Types.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Advanced Redirect Types', 'pretty-link'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('When it comes to boosting your SEO, enhancing user experience, or streamlining your website, the right redirect type can be a game-changer.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('Start with the traditional server-side URL redirects, such as 301 redirects for permanent redirection and 302/307 redirects for temporary redirection.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('Then, Pretty Links takes it a step further by offering advanced forms of redirection. Explore JavaScript-based redirects, Meta-Refresh redirection, Framed Cloak Redirection, and even Pixel Tracking to optimize every aspect of your website.', 'pretty-link'); ?></p>
            </div>
          </div>
        </div>
        <div class="prlip-reason">
          <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/Dynamic_Redirect_Types.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Dynamic Redirect Types', 'pretty-link'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('In addition to traditional redirects that send users to a single destination, dynamic redirects offer an exceptional advantage by allowing you to determine the user\'s destination based on specific conditions.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('With dynamic redirects, you have the power to create rules that consider a wide range of factors, including user location, device type, and even the time of day.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('This opens up a world of possibilities to deliver a personalized experience for each user, directing them to the most relevant content.', 'pretty-link'); ?></p>
            </div>
          </div>
        </div>
        <div class="prlip-reason">
          <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/Effortlessly_Import_Export_Links.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Effortlessly Import & Export Links', 'pretty-link'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('Easily migrate links between platforms, back up your valuable link data, or collaborate with others by sharing links effortlessly with our 1-click import and export feature.', 'pretty-link'); ?></p>
              <p><?php esc_html_e('Whether you\'re transitioning to a new website or making the smart move of switching from a different plugin, our intuitive import and export functionality ensures a smooth, secure, and stress-free process.', 'pretty-link'); ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="prlip-reason">
        <div class="reason-image"><img src="<?php echo PRLI_IMAGES_URL . '/product-displays-add-on.png'; ?>" alt=""></div>
        <div class="reason-content">
          <div class="reason-title"><h3><?php esc_html_e('Scroll-Stopping Product Displays', 'pretty-link'); ?></h3></div>
          <div class="reason-desc">
            <p><?php esc_html_e('Add a touch of pizzazz to your blog posts with product displays that demand attention and drive commissions.', 'pretty-link'); ?></p>
            <p><?php esc_html_e('Transform ordinary text links into captivating showcases featuring vibrant images, enticing descriptions, and irresistible CTAs.', 'pretty-link'); ?></p>
            <p><?php esc_html_e('Leverage the persuasive power of visually appealing content to captivate your audience at the precise moment they’re ready to buy.', 'pretty-link'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="prli-onboarding-pricing">
      <h2><i>See a Feature You Need?</i> <strong>Upgrade Now!</strong></h2>
      <div class="prli-onboarding-pricing-guarantee">
        <img src="<?php echo PRLI_IMAGES_URL . '/onboarding/14days-badge.svg'; ?>" alt="">
        <p>Don't settle for <i>ordinary</i> when you can embrace <strong>extraordinary</strong> with Pretty Links! Upgrade today and discover a world of limitless possibilities for your website.</p>
      </div>
    </div>

    <div class="prli-onboarding-pricing-table">
      <div class="prli-onboarding-pricing-executive">
        <div class="prli-onboarding-pricing-content">
          <div class="prli-onboarding-price-title">Super Affiliate</div>
          <div class="prli-onboarding-pricing-wrap">
            <div class="prli-onboarding-price-normally-wrap">
              <div class="prli-onboarding-price-normally">normally $399</div>
            </div>
            <div class="prli-onboarding-price-cost">
              <span class="prli-onb-price-currency">$</span>
              <span class="prli-onb-price-amount">199.50</span>
              <span class="prli-onb-price-term">/ year</span>
            </div>
            <div class="prli-onboarding-price-savings">$199.50 savings*</div>
          </div>
          <p class="prli-onboarding-price-desc">"Perfect for Super affiliates and other leaders who want big results."</p>
          <div class="prli-onboarding-price-cta">
            <a href="https://prettylinks.com/register/executive/" class="prli-onboarding-price-get-started">Get Started</a>
          </div>
          <ul class="prli-onboarding-price-features">
            <li class="prli-onboarding-price-feature"><strong>Use on up to 5 WordPress sites</strong></li>
            <li class="prli-onboarding-price-feature">Advanced redirect types</li>
            <li class="prli-onboarding-price-feature">Auto-create pretty links</li>
            <li class="prli-onboarding-price-feature">Auto-link keywords</li>
            <li class="prli-onboarding-price-feature">Product Displays Add-on</li>
            <li class="prli-onboarding-price-feature">1 year of support and updates</li>
            <li class="prli-onboarding-price-feature">Priority support</li>
          </ul>
          <div class="prli-onboarding-price-all-features"><a href="https://prettylinks.com/features/">See all features</a></div>
        </div>
      </div>

      <?php if(!in_array(PRLI_EDITION, array('pretty-link-marketer'))): ?>
        <div class="prli-onboarding-pricing-marketer">
          <div class="prli-onboarding-pricing-content">
            <div class="prli-onboarding-price-popular">Most Popular</div>
            <div class="prli-onboarding-price-title">Marketer</div>
            <div class="prli-onboarding-pricing-wrap">
              <div class="prli-onboarding-price-normally-wrap">
                <div class="prli-onboarding-price-normally">normally $299</div>
              </div>
              <div class="prli-onboarding-price-cost">
                <span class="prli-onb-price-currency">$</span>
                <span class="prli-onb-price-amount">149.50</span>
                <span class="prli-onb-price-term">/ year</span>
              </div>
              <div class="prli-onboarding-price-savings">$149.50 savings*</div>
            </div>
            <p class="prli-onboarding-price-desc">"Best for marketers who are serious about their link strategy."</p>
            <div class="prli-onboarding-price-cta">
              <a href="https://prettylinks.com/register/marketer/" class="prli-onboarding-price-get-started">Get Started</a>
            </div>
            <ul class="prli-onboarding-price-features">
              <li class="prli-onboarding-price-feature"><strong>Use on up to 2 WordPress sites</strong></li>
              <li class="prli-onboarding-price-feature">Advanced redirect types</li>
              <li class="prli-onboarding-price-feature">Auto-create pretty link</li>
              <li class="prli-onboarding-price-feature">Auto-link keywords</li>
              <li class="prli-onboarding-price-feature">Advanced add-ons (coming soon)</li>
              <li class="prli-onboarding-price-feature">1 year of support and updates</li>
            </ul>
            <div class="prli-onboarding-price-all-features"><a href="https://prettylinks.com/features/">See all features</a></div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!in_array(PRLI_EDITION, array('pretty-link-beginner', 'pretty-link-marketer', 'pretty-link-pro-blogger', 'pretty-link-pro-developer'))): ?>
        <div class="prli-onboarding-pricing-beginner">
          <div class="prli-onboarding-pricing-content">
            <div class="prli-onboarding-price-title">Beginner</div>
            <div class="prli-onboarding-pricing-wrap">
              <div class="prli-onboarding-price-normally-wrap">
                <div class="prli-onboarding-price-normally">normally $199</div>
              </div>
              <div class="prli-onboarding-price-cost">
                <span class="prli-onb-price-currency">$</span>
                <span class="prli-onb-price-amount">99.50</span>
                <span class="prli-onb-price-term">/ year</span>
              </div>
              <div class="prli-onboarding-price-savings">$99.50 savings*</div>
            </div>
            <p class="prli-onboarding-price-desc">"Essential pro features for those just getting started."</p>
            <div class="prli-onboarding-price-cta">
              <a href="https://prettylinks.com/register/beginner/" class="prli-onboarding-price-get-started">Get Started</a>
            </div>
            <ul class="prli-onboarding-price-features">
              <li class="prli-onboarding-price-feature">Use on 1 WordPress site</li>
              <li class="prli-onboarding-price-feature">Advanced redirect types</li>
              <li class="prli-onboarding-price-feature">Auto-create pretty links</li>
              <li class="prli-onboarding-price-feature">Auto-link keywords</li>
              <li class="prli-onboarding-price-feature">1 year of support and updates</li>
            </ul>
            <div class="prli-onboarding-price-all-features"><a href="https://prettylinks.com/features/">See all features</a></div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
