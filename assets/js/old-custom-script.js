jQuery(document).ready(function ($) {
  //select from options list

  //update values
  $(".update-btn").on("click", function () {
    var button = $(this); // Store the reference to the button element
    var formData = $("#edit-form").serialize();
    alert("formData: " + formData);
    var form_id = $(this).data("form-id");
    var id = $("label#myid").data("id");

    alert(id);
    //var id = 1;

    // Use AJAX to trigger the update function on the server
    $.ajax({
      type: "POST",
      url: custom_vars.ajax_url,
      data: {
        action: "update_form_values", // Update to the correct AJAX action
        form_id: form_id,
        formData: formData,
        id: id,
      },
      beforeSend: function () {
        // Disable the button before the AJAX request is sent
        button
          .prop("disabled", true)
          .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
      },
      success: function (response) {
        var $divToChangeBorder = $('.form-set-container[data-id="' + id + '"]');

        // Change the border style (example: solid 2px green)
        $divToChangeBorder.css("border", "9px solid green");

        console.log("response from PHP:", response.data);
        if (response.success) {
          // You can access the 'data-id' value here

          // Select the div with the matching 'data-id' value and animate it

          var fields = response.data.fields;
          console.log("fields from update: " + fields);

          console.log("Form values updated successfully.");
          button.html('<i class="fa fa-check"></i> Saved');

          // You can add any additional actions here, like refreshing the form or displaying a success message.
        } else {
          // Handle error
          console.log("Error updating form values.");
        }
      },
      error: function (xhr, status, error) {
        // Handle AJAX error
        console.log("AJAX error:", error);
      },
      complete: function () {
        // Re-enable the button after the AJAX request is completed
        button.prop("disabled", false);
      },
    });
  });

  $(".edit-btn").on("click", function () {
    var bt = $("<button>", {
      text: "Update",
      class: "update-btn",
    });
    var form_id = $(this).data("form-id");
    var id = $(this).data("id");
    console.log("Form ID:", form_id);
    // Use AJAX to fetch form fields based on form_id
    $.ajax({
      type: "POST",
      url: custom_vars.ajax_url,
      data: {
        action: "get_form_values", // Update to the correct AJAX action
        form_id: form_id,
        id: id,
      },
      success: function (response) {
        console.log(response);
        if (response.success) {
          var fields = response.data.fields;

          // Clear existing inputs
          $("#edit-form").empty();

          $("#edit-form").append(
            `<label  id='myid' data-id='${id}'>${id}</label>`
          );

          // Populate inputs with fetched fields
          $.each(fields, function (index, field) {
            var input = $("<input>", {
              type: field.type,
              name: field.name,
              value: field.value,
              class: "input-large",
              id: id,
              placeholder: field.name,
            });
            $("#edit-form").append(input);
            // bt.appendTo("#edit-form");
            $("#update-btn").html('<i class="fas fa-check"></i> Checked');
            $(this).html('<i class="fas fa-check"></i> Checked');
          });

          // Show the edit popup form
          $("#edit-popup").show();
        } else {
          // Handle error
          console.log("Error fetching form fields.");
        }
      },
    });
  });

  $(".dismiss-btn").on("click", function () {
    $("#edit-popup").hide();
  });

  $(".export-btn").on("click", function () {
    var data = {
      action: "export_form_data",
    };
    window.location.href = custom_vars.ajax_url + "?" + $.param(data);
  });

  $(".export-btn-pdf").on("click", function () {
    var data = {
      action: "export_form_data_pdf",
    };
    window.location.href = custom_vars.ajax_url + "?" + $.param(data);
  });

  $(".delete-btn").on("click", function () {
    var form_id = $(this).data("form-id");

    if (confirm("Are you sure you want to delete this?")) {
      $("#delete-button").attr("href", "query.php?ACTION=delete&ID='1'");
      var data = {
        action: "delete_form_row",
        form_id: form_id,
      };

      $.post(custom_vars.ajax_url, data, function (response) {
        // Update the page or handle the response as needed
        location.reload(); // Reload the page for demonstration
      });
    } else {
      return false;
    }

    /*  if (confirm("Are you sure you want to delete this form row?")) {
      var data = {
        action: "delete_form_row",
        form_id: form_id,
      }; $.post(custom_vars.ajax_url, data, function (response) {
      // Update the page or handle the response as needed
      location.reload(); // Reload the page for demonstration
    });*/
  });
});
