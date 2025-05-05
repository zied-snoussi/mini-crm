$(document).ready(() => {
  // Tab switching
  $(".tab-btn").click(function () {
    const tabId = $(this).data("tab")

    // Update active tab button
    $(".tab-btn").removeClass("active")
    $(this).addClass("active")

    // Show selected tab content
    $(".tab-pane").removeClass("active")
    $(`#${tabId}`).addClass("active")
  })

  // Add note form submission
  $("#add-note-form").submit((e) => {
    e.preventDefault()

    const prospectId = $('input[name="prospect_id"]').val()
    const content = $('textarea[name="content"]').val()

    if (!content.trim()) {
      alert("Please enter a note")
      return
    }

    $.ajax({
      url: "controllers/note_controller.php",
      type: "POST",
      data: {
        action: "add_note",
        prospect_id: prospectId,
        content: content,
      },
      dataType: "json",
      success: (response) => {
        if (response.success) {
          // Clear form
          $('textarea[name="content"]').val("")

          // Update notes container
          $("#notes-container").html(response.html)
        } else {
          alert(response.error || "Failed to add note")
        }
      },
      error: () => {
        alert("An error occurred while processing your request")
      },
    })
  })

  // Upload document form submission
  $("#upload-document-form").submit(function (e) {
    e.preventDefault()

    const formData = new FormData(this)
    formData.append("action", "upload_document")

    $.ajax({
      url: "controllers/document_controller.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: (response) => {
        if (response.success) {
          // Clear form
          $("#document").val("")

          // Update documents container
          $("#documents-container").html(response.html)
        } else {
          alert(response.error || "Failed to upload document")
        }
      },
      error: () => {
        alert("An error occurred while processing your request")
      },
    })
  })

  // Delete document
  $(document).on("click", ".delete-document", function () {
    if (confirm("Are you sure you want to delete this document?")) {
      const id = $(this).data("id")

      $.ajax({
        url: "controllers/document_controller.php",
        type: "POST",
        data: {
          action: "delete_document",
          id: id,
        },
        dataType: "json",
        success: (response) => {
          if (response.success) {
            // Reload documents
            location.reload()
          } else {
            alert(response.error || "Failed to delete document")
          }
        },
        error: () => {
          alert("An error occurred while processing your request")
        },
      })
    }
  })
})
