
(function ($) {
  "use strict";

  $(function () {
    // ✅ Si no existe la tabla, no hace nada
    if (!$("#tabla_index").length) return;

    // ✅ Evitar doble init
    if ($.fn.DataTable.isDataTable("#tabla_index")) return;

const orderColumn = parseInt(
  $("#tabla_index").data("order-column") ?? 0,
  10
);

const table = $("#tabla_index").DataTable({
  responsive: true,
  dom: "rt",
  paging: true,
  pageLength: 10,
  order: [[orderColumn, "desc"]],
  language: {
    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
  },
  drawCallback: function () {
    updatePagination();
  },
  initComplete: function () {
    setTimeout(updatePagination, 60);
  },
});
    // 🔍 Buscar
    $("#searchInput").on("keyup", function () {
      table.search(this.value).draw();
    });

    // ❌ Limpiar búsqueda
    $("#clearSearch").on("click", function () {
      $("#searchInput").val("");
      table.search("").draw();
      $("#searchInput").focus();
    });

    // 📄 Por página
    $("#perPageSelect").on("change", function () {
      table.page.len(parseInt(this.value, 10)).draw();
    });

    function updatePagination() {
      const info = table.page.info();
      const filtered = info.recordsDisplay;

      $("#pagination-from").text(filtered === 0 ? 0 : info.start + 1);
      $("#pagination-to").text(filtered === 0 ? 0 : info.end);
      $("#pagination-total").text(filtered);

      $("#perPageSelect").val(info.length);

      const $pagination = $(".pagination-modern");
      $pagination.find(".page-number, .page-ellipsis").remove();

      // Máx 5 páginas visibles + ...
      let startPage = Math.max(0, info.page - 2);
      let endPage = Math.min(info.pages, startPage + 5);
      if (endPage - startPage < 5) startPage = Math.max(0, endPage - 5);

      if (startPage > 0) {
        $(
          '<li class="page-item disabled page-ellipsis"><span class="page-link">...</span></li>'
        ).insertBefore("#pagination-next");
      }

      for (let i = startPage; i < endPage; i++) {
        const $li = $('<li class="page-item page-number"></li>');
        const $a = $(`<a class="page-link" href="#">${i + 1}</a>`);

        if (i === info.page) $li.addClass("active");

        $a.on("click", function (e) {
          e.preventDefault();
          table.page(i).draw("page");
        });

        $li.append($a).insertBefore("#pagination-next");
      }

      if (endPage < info.pages) {
        $(
          '<li class="page-item disabled page-ellipsis"><span class="page-link">...</span></li>'
        ).insertBefore("#pagination-next");
      }

      $("#pagination-prev").toggleClass(
        "disabled",
        info.page === 0 || info.pages === 0
      );
      $("#pagination-next").toggleClass(
        "disabled",
        info.page >= info.pages - 1 || info.pages === 0
      );

      $("#pagination-prev .page-link")
        .off("click")
        .on("click", function (e) {
          e.preventDefault();
          if (info.page > 0) table.page(info.page - 1).draw("page");
        });

      $("#pagination-next .page-link")
        .off("click")
        .on("click", function (e) {
          e.preventDefault();
          if (info.page < info.pages - 1) table.page(info.page + 1).draw("page");
        });
    }

    // 🌙 Reaccionar al cambio de dark-theme
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (m) {
        if (m.attributeName === "class") updatePagination();
      });
    });
    observer.observe(document.body, { attributes: true });
  });
})(jQuery);
