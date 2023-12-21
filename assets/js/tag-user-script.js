jQuery(document).ready(function ($) {
  var dropdown = $("#tag-emails");

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
    dropdown.empty();

    $.each(userNames, function (index, userName) {
      var checkbox = $("<input>").attr({
        type: "checkbox",
        name: "tagged_users[]",
        value: userName,
      });
      var label = $("<label>").text(userName).prepend(checkbox);
      dropdown.append(label);
    });
  }

  // Event listener for button click
  $("#tag-user-button").on("click", function () {
    getUserNames();
  });
});
