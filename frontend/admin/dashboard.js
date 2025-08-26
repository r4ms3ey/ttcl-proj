$(document).ready(function () {
    $(".tabs .tab").click(function () {
        let page = $(this).data("page");

        // Add active class to clicked tab and remove from others
        $(".tabs .tab").removeClass("active");
        $(this).addClass("active");

        // Redirect with kurasa parameter
        window.location.href = "dashboard.php?kurasa=" + page;
    });

    // On page load, keep the correct tab active based on URL
    let urlParams = new URLSearchParams(window.location.search);
    let currentPage = urlParams.get("kurasa") || "attendance.php";

    $(".tabs .tab").each(function () {
        if ($(this).data("page") === currentPage) {
            $(this).addClass("active");
        }
    });
});