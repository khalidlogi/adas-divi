jQuery(document).ready(function ($) {
  var buttonAdded = false; // Initialize a variable to track if the button is added.

  $(".update-btn").on("click", function () {
    var button = $(this);
    var form_id = $(this).data("form-id");
    var id = $("label#myid").data("id");
    var nonceupdate = $(this).data("nonceupdate");

    if (!buttonAdded) {
      var refreshButton =
        '<button style="width: 100%;color:blue; border-radius: 4px;background-color: #EDF9FF;border: 1px solid #0497E1;" id="refresh-page-button">Refresh Page</button>';
    } else {
      refreshButton = "";
    }

    var formData = $("#edit-form").serialize();
    var formData = decodeURIComponent(formData); //decode data

    $.ajax({
      type: "POST",
      url: custom_vars.ajax_url,
      data: {
        action: "update_form_values",
        contact_form_id: form_id,
        formData: formData,
        id: id,
        nonceupdate: nonceupdate,
      },
      beforeSend: function () {
        // Disable the button before the AJAX request is sent
        button
          .prop("disabled", true)
          .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
      },
      success: function (response) {

        if (response.success) {
          // Select the div with the matching 'data-id' value and animate it
          var fields = response.data.fields;
                   button.html('<i class="fa fa-check"></i> Saved');

          $("#edit-popup").append(refreshButton);
          buttonAdded = true; // Set the flag to indicate the button is added.

          $("#refresh-page-button").click(function () {
            window.location.href = window.location.href;
          });
        } 
      },
      error: function (xhr, status, error) {
      },
      complete: function () {
        // Re-enable the button after the AJAX request is completed
        button.prop("disabled", false);
      },
    });
  });

  $(".edit-btn, .editbtn").on("click", function () {
    var form_id = $(this).data("form-id");
    var id = $(this).data("id");

    $.ajax({
      type: "POST",
      url: custom_vars.ajax_url,
      data: {
        action: "get_form_values", // Update to the correct AJAX action
        form_id: form_id,
        id: id,
      },

      success: function (response) {
        if (response.success) {
          var fields = response.data.fields;
          // Clear existing inputs
          $("#edit-form").empty();
          //Add the relevant id label to the edit form
          $("#edit-form").append(
            `<label  id='myid' data-id='${id}'>Form id: ${id}</label>`
          );

          // Populate inputs with fetched fields
          var isAminField = false;
          $.each(fields, function (index, field) {
            if (field.name === "Admin_note") {
              isAminField = true;
            }
            var input = $("<input>", {
              type: field.type,
              name: field.name,

              value: Array.isArray(field.value)
                ? field.value.join(" ") // Join array with spaces
                : field.value,
              class: "input-large",
              id: id,
              placeholder: field.name,
            });

            $("#edit-form").append(input); // apend all to edit-form
            $(this).html('<i class="fas fa-check"></i> Checked');
          });

          var inputAdmin = $("<input>", {
            type: "text",
            name: "Admin note",
            class: "input-large",
            id: "admintext",
            placeholder: "Admin note",
            style: "color: red;",
          });
          if (!isAminField) {
            $("#edit-form").append(inputAdmin);
          }

          // Show the edit popup form
          $("#edit-popup").show();
          $("#edit-popup").draggable();
        }
      },
    });
  });

  $(".dismiss-btn").on("click", function () {
    $("#edit-popup").hide();
  });

  //Export to csv data
  $(".export-btn").on("click", function () {
    var data = {
      action: "export_form_data",
    };

    var url = custom_vars.ajax_url + "?" + $.param(data);
    window.location.href = url;
  });

  $(".export-btn-pdf").on("click", function () {
    var nonce = $(".export-btn-pdf").data("nonce");
    var data = {
      action: "export_form_data_pdf",
      nonce: nonce,
    };
    window.location.href = custom_vars.ajax_url + "?" + $.param(data);
  });

  $(".delete-btn").on("click", function () {

    var form_id = $(this).data("form-id");
    var id = $(this).data("form-id");
    var nonce = $(this).data("nonce");

    if (confirm("Are you sure you want to delete this?")) {
      var data = {
        action: "delete_form_row",
        form_id: form_id,
        id: id,
        nonce: nonce,
      };

      $.post(custom_vars.ajax_url, data, function (response) {
        location.reload(); // Reload the page for demonstration
      });
    } else {
      return false;
    }
  });
});
