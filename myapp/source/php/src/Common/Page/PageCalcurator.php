<?php

namespace App\Common\Page;

/**
 * ページ計算。
 */
class PageCalcurator {

    /**
     * @var int 現在のページ
     */
    private $currentPage;

    /**
     * @var int 1ページ当たりの表示件数
     */
    private $showCountPerPage;

    /**
     * @var int 合計件数
     */
    private $totalCount;

    /**
     * @var int ページリンク表示件数
     */
    private $showPageLinkCount;

    /**
     * コンストラクタ。
     * @param int $currentPage 現在のページ
     * @param int $showCountPerPage 1ページ当たりの表示件数
     * @param int $totalCount 合計件数
     * @param int $showPageLinkCount ページリンク表示件数
     */
    public function __construct($currentPage, $showCountPerPage, $totalCount, $showPageLinkCount = null) {
        $this->currentPage = $currentPage;
        $this->showCountPerPage = $showCountPerPage;
        $this->totalCount = $totalCount;
        $this->showPageLinkCount = $showPageLinkCount;
    }

    /**
     * オフセット位置の計算。
     * @return int オフセット位置
     */
    public function calcOffset() {
        $currentPage = $this->getCurrentPage();
        if ($currentPage !== null && $currentPage > 0 && $this->showCountPerPage !== null && $this->showCountPerPage > 0) {
            return ($currentPage - 1) * $this->showCountPerPage;
        }

        return null;
    }

    /**
     * ページ番号の計算。
     * @param int $offset オフセット位置
     * @param int $showCountPerPage 1ページ当たりの表示件数
     * @return int ページ番号
     */
    public static function calcPageByOffset($offset, $showCountPerPage) {
        $page = (int) ($offset / $showCountPerPage) + 1;
        return $page;
    }

    /**
     * 限界位置の計算。
     * @return int 限界位置
     */
    public function calcLimit() {
        if ($this->showCountPerPage != null && $this->showCountPerPage > 0) {
            return $this->showCountPerPage;
        }

        return null;
    }

    /**
     * ページサイズの計算。
     * @return int ページサイズ
     */
    public function calcPageSize() {
        if ($this->totalCount === null || $this->showCountPerPage === null || $this->showCountPerPage <= 0) {
            return null;
        }

        return (int) ceil((float) $this->totalCount / (float) $this->showCountPerPage);
    }

    /**
     * 現在ページ。
     * @return int 現在ページ
     */
    public function getCurrentPage() {
        $currentPage = $this->currentPage;
        $pageSize = $this->calcPageSize();
        if ($this->currentPage < 1) {
            $currentPage = 1;
        } elseif ($this->currentPage > $pageSize) {
            $currentPage = $pageSize;
        }
        return $currentPage;
    }

    /**
     * 合計件数。
     * @return int 合計件数
     */
    public function getTotalCount() {
        return $this->totalCount;
    }

    /**
     * 1ページ当たりの表示件数。
     * @return int 1ページ当たりの表示件数
     */
    public function getShowCountPerPage() {
        return $this->showCountPerPage;
    }

    /**
     * ページ情報の取得。
     * @return array ページ情報
     */
    public function getData() {

        // 現在ページを取得する
        $currentPage = $this->getCurrentPage();
        // ページサイズを取得する
        $pageSize = $this->calcPageSize();

        // ページ情報リストを生成する
        $pageList = $this->getPageList($currentPage, $pageSize);

        return [
            'currentPage' => $currentPage,
            'totalCount' => $this->getTotalCount(),
            'showCountPerPage' => $this->getShowCountPerPage(),
            'pageSize' => $pageSize,
            'offset' => $this->calcOffset(),
            'limit' => $this->calcLimit(),
            'pageList' => $pageList,
        ];
    }

    /**
     * ページリストを取得する。
     * @param int $currentPage 現在ページ
     * @param int $pageSize ページサイズ
     * @return array ページリスト
     */
    private function getPageList($currentPage, $pageSize) {

        $pageList = [];

        if ($this->showPageLinkCount === null ||
                ($this->showPageLinkCount !== null && $this->showPageLinkCount >= $pageSize)) {
            // ページ番号を格納する
            for ($i = 0; $i < $pageSize; $i++) {
                $pageList[] = array('pageNum' => ($i + 1));
            }
        } else {
            // 省略表記が一つ以上表示されるパターン

            if ($currentPage < $this->showPageLinkCount) {
                // 前半部分のリンクを全て表示するパターン
                // 1 2 3 4 5 ...
                for ($i = 1; $i <= $this->showPageLinkCount; $i++) {
                    $pageList[] = array('pageNum' => ($i));
                }
                $pageList[] = array('pageNum' => 'x');
            } elseif ($currentPage + $this->showPageLinkCount > $pageSize) {
                // 後半部分のリンクを全て表示するパターン
                // ... 20 21 22 23 24 25
                $pageList[] = array('pageNum' => 'x');
                for ($i = $pageSize - $this->showPageLinkCount + 1; $i <= $pageSize; $i++) {
                    $pageList[] = array('pageNum' => ($i));
                }
            } else {
                // 前半後半の省略表記が表示されるパターン
                // ... 10 11 12 13 14 15 ...
                $pageBefore = array();
                $pageAfter = array();

                $half = (int) ceil($this->showPageLinkCount / 2);

                // 後半部分の最終インデックスを算出する
                $pageAfterLast = $currentPage + $half - 1;
                if ($pageAfterLast > $pageSize) {
                    $pageAfterLast = $pageSize;
                }

                // 後半部分のページリンクを生成する
                for ($i = $currentPage; $i <= $pageAfterLast; $i++) {
                    $pageAfter[] = array('pageNum' => ($i));
                }
                // 最終ページリンクが表示されない場合は、省略表記を追加する
                $pageAfter[] = array('pageNum' => 'x');
                // 前半部分の開始インデックスを算出する
                $pageBeforeFirst = $currentPage - count($pageAfter) + 1;
                if ($pageBeforeFirst < 1) {
                    $pageBeforeFirst = 1;
                }

                // 先頭ページリンクが表示されない場合は、省略表記を追加する
                $pageBefore[] = array('pageNum' => 'x');
                // 前半部分のページリンクを生成する
                for ($i = $pageBeforeFirst; $i < $currentPage; $i++) {
                    $pageBefore[] = array('pageNum' => ($i));
                }

                $pageList = array_merge($pageBefore, $pageAfter);
            }
        }

        return $pageList;
    }

}
