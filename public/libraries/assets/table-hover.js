//table row hovar
function get_hover() {
  $("#myTable tr").hover(
    function () {
      $(this).addClass("current-row");
    },
    function () {
      $(this).removeClass("current-row");
    }
  );
  $(".table-row").hover(
    function () {
      $(this).addClass("current-row");
    },
    function () {
      $(this).removeClass("current-row");
    }
  );

  $("th").hover(
    function () {
      var index = $(this).index();
      $("th.table-header, td")
        .filter(":nth-child(" + (index + 1) + ")")
        .addClass("current-col");
      $("th.table-header")
        .filter(":nth-child(" + (index + 1) + ")")
        .css("background-color", "#ddd", "cursor", "pointer");
    },
    function () {
      var index = $(this).index();
      $("th.table-header, td").removeClass("current-col");
      $("th.table-header")
        .filter(":nth-child(" + (index + 1) + ")")
        .css("background-color", "#F5F5F5", "cursor", "pointer");
    }
  );
  $("th").on(
    "click",
    function () {
      var index = $(this).index();
      $("th.table-header, td")
        .filter(":nth-child(" + (index + 1) + ")")
        .addClass("current-col");
      $("th.table-header")
        .filter(":nth-child(" + (index + 1) + ")")
        .css("background-color", "#ddd", "cursor", "pointer");
    },
    function () {
      var index = $(this).index();
      $("th.table-header, td").removeClass("current-col");
      $("th.table-header")
        .filter(":nth-child(" + (index + 1) + ")")
        .css("background-color", "#F5F5F5", "cursor", "pointer");
    }
  );
}
