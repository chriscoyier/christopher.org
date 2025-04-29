jQuery(function ($) {
    tippy('.prli-tooltip', {
        content: (reference) => reference.getAttribute('data-title'),
        allowHTML: true,
        trigger: 'mouseenter click',
        interactive: true,

      });
});
