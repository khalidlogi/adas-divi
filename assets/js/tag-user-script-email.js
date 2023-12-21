jQuery(document).ready(function ($) {
  $('input[type="checkbox"]').change(function () {
    if ($(this).is(":checked")) {
      // Checkbox is checked
      alert("Checkbox is checked");
    } else {
      // Checkbox is unchecked
      alert("Checkbox is unchecked");
    }
  });
  z;

  $("#send-email-button").click(function () {
    var email = $('input[name="tagged_users[]"]:checked').val();
    var message = $("#email-textarea").val();

    // Send AJAX request to send emailu
    $.ajax({
      url: "send_email.php", // Replace with the actual URL to your server-side script
      method: "POST",
      data: { email: email, message: message },
      success: function (response) {
        // Display success message
        $("#message").text("Message sent");
      },
      error: function (xhr, status, error) {
        // Display error message
        $("#message").text("Error sending message");
      },
    });
  });
});
