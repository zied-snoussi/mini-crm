$(document).ready(() => {
  // Status filter buttons
  $(".btn-filter").click(function () {
    const status = $(this).data("status")

    // Update active class
    $(".btn-filter").removeClass("active")
    $(this).addClass("active")

    // Load prospects with filter
    loadProspects(status, 1)
  })

  // Delete prospect
  $(document).on("click", ".delete-prospect", function () {
    if (confirm("Are you sure you want to delete this prospect?")) {
      const id = $(this).data("id")

      $.ajax({
        url: "controllers/prospect_controller.php",
        type: "POST",
        data: {
          action: "delete_prospect",
          id: id,
        },
        dataType: "json",
        success: (response) => {
          if (response.success) {
            // Reload prospects
            const status = $(".btn-filter.active").data("status")
            loadProspects(status, 1)
          } else {
            alert(response.error || "Failed to delete prospect")
          }
        },
        error: () => {
          alert("An error occurred while processing your request")
        },
      })
    }
  })

  // Pagination
  $(document).on("click", ".pagination a", function (e) {
    e.preventDefault()

    const url = new URL($(this).attr("href"), window.location.origin)
    const page = url.searchParams.get("page") || 1
    const status = url.searchParams.get("status") || ""

    loadProspects(status, page)
  })

  // Function to load prospects with AJAX
  function loadProspects(status, page) {
    $.ajax({
      url: "controllers/prospect_controller.php",
      type: "GET",
      data: {
        action: "get_prospects",
        status: status,
        page: page,
      },
      dataType: "json",
      success: (response) => {
        if (response.html) {
          // Update prospects table
          $("#prospects-container").html(response.html)

          // Update pagination
          updatePagination(response.total_pages, response.current_page, status)
        } else {
          alert(response.error || "Failed to load prospects")
        }
      },
      error: () => {
        alert("An error occurred while processing your request")
      },
    })
  }

  // Function to update pagination
  function updatePagination(totalPages, currentPage, status) {
    if (totalPages <= 1) {
      $(".pagination").hide()
      return
    }

    let html = ""
    for (let i = 1; i <= totalPages; i++) {
      const activeClass = i === Number.parseInt(currentPage) ? "active" : ""
      const statusParam = status ? `&status=${status}` : ""
      html += `<a href="?page=${i}${statusParam}" class="${activeClass}">${i}</a>`
    }

    $(".pagination").html(html).show()
  }
})
