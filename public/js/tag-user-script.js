jQuery(document).ready(function ($) {
  jQuery(document).ready(function ($) {
    // AJAX request to fetch user names
    function getUserNames() {
      $.ajax({
        url: custom_vars.ajax_url,
        method: "POST",
        dataType: "json",
        data: {
          action: "tag_user_get_user_names",
        },
        success: function (response) {
          console.log("response from PHP:", response);
          if (response.success) {
            populateDropdown(response.data);
          }
        },
        error: function () {
          console.log("Error fetching user names.");
        },
      });
    }

    // Populate the dropdown with user names
    function populateDropdown(userNames) {
      var dropdown = $("#tag-user-dropdown");

      dropdown.empty();

      $.each(userNames, function (index, userName) {
        dropdown.append($("<option>").val(userName).text(userName));
      });
    }

    // Event listener for button click
    $("#tag-user-button").on("click", function () {
      getUserNames();
    });
  });
});
