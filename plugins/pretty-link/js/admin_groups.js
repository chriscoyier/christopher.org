jQuery(document).ready(function($) {
  $('.group_actions').hide();
  $('.edit_group').hover(
    function() {
      $(this).children(".group_actions").show();
    },
    function() {
      $(this).children(".group_actions").hide();
    }
  );
});

