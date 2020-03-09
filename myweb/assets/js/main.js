/**
 * 任意の要素にスクロールする。
 * @param  {[type]} $selector セレクタ
 * @param  {Number} adjustSize 調整サイズ
 */
function scrollElement($selector, adjustSize) {

  $("html,body").animate({
    scrollTop: $selector.offset().top + adjustSize
  });
}

(function() {

  /**
   * ページのスクロールイベントの適用
   */
  var applyOnScrollPage = function() {

    var $headerArea = $("#header-area");
    var $menuArea = $("#menu-area");

    var adjustSize = ($headerArea.height() + $menuArea.height());

    // 対象の要素を取得する
    var $targetList = $("a[name]");

    var scrollWithMenuItemActive = function() {

      var scrollTop = $(window).scrollTop();

      var lastElement = null;

      // windowがスクロールされた時に実行する処理
      $targetList.each(function(index, element){

        // カレント要素の位置を取得する
        var elementTop = $(element).offset().top;

        if (scrollTop+adjustSize >= elementTop) {
          // カレント要素よりも下部に位置する
          lastElement = $(element);
        }
      });

      if (lastElement !== null) {
        // スクロール位置からアクティブなメニューを取得する

        // メニューIDを取得する
        var menuItemId = $(lastElement).attr("name");
        // メニューIDからメニューのaタグを取得する
        var $menuItemAnchor = $("a[href=\\#" + menuItemId + "]");
        // メニューのaタグからメニュー項目（親の要素）を取得する
        var $menuItem = $menuItemAnchor.closest(".menu-item");

        // アクティブを切り替える
        $(".menu-item").removeClass("active");
        $menuItem.addClass("active");
      }

    };

    var pageTopLinkActive = function() {

        var scrollTop = $(window).scrollTop();

        var $pageTop = $("#page-top");
        if (scrollTop >= 100) {
          $pageTop.slideDown();
        } else {
          $pageTop.slideUp();
        }

    };

    $(window).scroll(function() {
      scrollWithMenuItemActive();
      pageTopLinkActive();
    });
    scrollWithMenuItemActive();
    pageTopLinkActive();

  };

  /**
   * メニューのクリックイベントの適用
   */
  var applyOnClickMenuItem = function() {

    var $headerArea = $("#header-area");
    var $menuArea = $("#menu-area");

    var adjustSize = ($headerArea.height() + $menuArea.height());

    $(document).on("click", ".menu-item-anchor, #page-top > a", function() {
      // #xxxx の "#" を除いた文字列を取得する
      var anchorName = $(this).attr("href").substring(1);
      // 対象の要素を取得する
      var $target = $("a[name=" + anchorName + "]");
      // 対象要素にスクロールする
      scrollElement($target, -adjustSize);

      return false;
    });
  };

  /**
   * ヘッダボタンのクリックイベントの適用
   */
  var applyHeaderButton = function() {

    var $headerArea = $("#header-area");
    var $menuArea = $("#menu-area");

    var adjustSize = ($headerArea.height() + $menuArea.height());

    $(document).on("click", "#header-nav .top-button", function() {
      // 対象の要素を取得する
      var $target = $("a[name=" + "about_site" + "]");
      // 対象要素にスクロールする
      scrollElement($target, -adjustSize);

      return false;
    });

    $(document).on("click", "#header-nav .contact-button", function() {
      // 対象の要素を取得する
      var $target = $("a[name=" + "contact" + "]");
      // 対象要素にスクロールする
      scrollElement($target, -adjustSize);

      return false;
    });

  };

  // ページロード時の処理
  $(function() {

    applyHeaderButton();
    applyOnScrollPage();
    applyOnClickMenuItem();

  });

})();
