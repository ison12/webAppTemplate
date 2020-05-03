<?php

namespace App\Common\Util;

use App\Common\Exception\FileException;
use Exception;

/**
 * ファイルユーティリティ。
 */
class FileUtil {

    /**
     * ファイル／ディレクトリをコピーする。
     * @param string $src コピー元
     * @param string $des コピー先
     * @return boolean true 正常、false 異常
     */
    public static function copy($src, $des) {

        if (!self::existsFile($src)) {
            throw new FileException("src={$src}, $des={$des}, FileUtil#copy実行時に、コピー元が存在しないため失敗");
        }

        if (is_dir($src)) {
            // ディレクトリのため、ディレクトリコピーを実行する
            return self::copyDir($src, $des);
        } else {

            // コピー先が存在しない場合は、コピー先ディレクトリを作成する
            if (!self::existsFile(dirname($des))) {
                if (!self::makeDirectory(dirname($des))) {
                    throw new FileException("src={$src}, $des={$des}, FileUtil#copy実行時に、ディレクトリ作成に失敗");
                }
            }

            // ファイルコピーを実行する
            $ret = copy($src, $des);
            if ($ret) {
                // 最終更新日時を設定する
                clearstatcache(true, $src);
                $ret = touch($des, filemtime($src));
                if (!$ret) {
                    throw new FileException("src={$src}, $des={$des}, FileUtil#copy実行時に、最終更新日時の設定に失敗");
                }
            } else {
                throw new FileException("src={$src}, $des={$des}, FileUtil#copy実行時に、ファイルコピーに失敗");
            }
            return $ret;
        }
    }

    /**
     * ディレクトリをコピーする。
     * @param string $src コピー元
     * @param string $des コピー先
     * @return boolean true 正常、false 異常
     */
    private static function copyDir($src, $des) {

        if (!self::existsFile($des)) {
            if (!self::makeDirectory($des)) {
                throw new FileException("src={$src}, $des={$des}, FileUtil#copyDir実行時に、ディレクトリ作成に失敗");
            }
        }

        $handle = opendir($src);
        if (!$handle) {
            throw new FileException("src={$src}, $des={$des}, FileUtil#copyDir実行時に、ディレクトリ情報読み取りに失敗");
        }

        while (false !== ( $item = readdir($handle))) {

            if ($item === "." || $item === "..") {
                continue;
            }

            $pathSrc = $src . DIRECTORY_SEPARATOR . $item;
            $pathDes = $des . DIRECTORY_SEPARATOR . $item;

            if (is_dir($pathSrc)) {
                $ret = self::copyDir($pathSrc, $pathDes);
                if (!$ret) {
                    closedir($handle);
                    return $ret;
                }
            } else {

                $ret = self::copy($pathSrc, $pathDes);
                if ($ret !== true) {
                    closedir($handle);
                    return $ret;
                }
            }
        }

        closedir($handle);

        return true;
    }

    /**
     * ファイル／ディレクトリを移動する。
     * @param string $src 移動元
     * @param string $des 移動先
     * @return boolean true 正常、false 異常
     */
    public static function move($src, $des) {

        // 移動元が存在しない
        if (!self::existsFile($src)) {
            throw new FileException("src={$src}, $des={$des}, FileUtil#move実行時に、移動元が存在しないため失敗");
        }

        // 移動先が存在しない場合は、移動先ディレクトリを作成する
        if (!self::existsFile(dirname($des))) {
            if (!self::makeDirectory(dirname($des))) {
                throw new FileException("src={$src}, $des={$des}, FileUtil#move実行時に、ディレクトリ作成に失敗");
            }
        }

        // 移動実行
        $ret = rename($src, $des);
        if (!$ret) {
            throw new FileException("src={$src}, $des={$des}, FileUtil#move実行時に、移動に失敗");
        }

        return $ret;
    }

    /**
     * ファイル／ディレクトリを移動する。
     * @param string $src 移動元
     * @param string $des 移動先
     * @return boolean true 正常、false 異常
     */
    public static function moveFileOnly($src, $des) {

        if (!self::existsFile($src)) {
            throw new FileException("src={$src}, $des={$des}, FileUtil#moveFileOnly実行時に、移動元が存在しないため失敗");
        }

        if (is_dir($src)) {
            // ディレクトリのため、ディレクトリコピーを実行する
            return self::moveDirFileOnly($src, $des);
        } else {

            // 移動先が存在しない場合は、移動先ディレクトリを作成する
            if (!self::existsFile(dirname($des))) {
                if (!self::makeDirectory(dirname($des))) {
                    throw new FileException("src={$src}, $des={$des}, FileUtil#moveFileOnly実行時に、ディレクトリ作成に失敗");
                }
            }

            // ファイル移動を実行する
            $ret = rename($src, $des);
            if (!$ret) {
                throw new FileException("src={$src}, $des={$des}, FileUtil#moveFileOnly実行時に、移動に失敗");
            }
            return $ret;
        }
    }

    /**
     * ディレクトリを移動する。
     * @param string $src 移動元
     * @param string $des 移動先
     * @return boolean true 正常、false 異常
     */
    private static function moveDirFileOnly($src, $des) {

        if (!self::existsFile($des)) {
            if (!self::makeDirectory($des)) {
                return false;
            }
        }

        $handle = opendir($src);
        if ($handle === false) {
            throw new FileException("src={$src}, $des={$des}, FileUtil#moveDirFileOnly実行時に、ディレクトリ情報読み取りに失敗");
        }

        while (false !== ( $item = readdir($handle))) {

            if ($item === "." || $item === "..") {
                continue;
            }

            $pathSrc = $src . DIRECTORY_SEPARATOR . $item;
            $pathDes = $des . DIRECTORY_SEPARATOR . $item;

            if (is_dir($pathSrc)) {
                $ret = self::moveDirFileOnly($pathSrc, $pathDes);
                if (!$ret) {
                    closedir($handle);
                    return false;
                }
            } else {

                $ret = self::moveFileOnly($pathSrc, $pathDes);
                if (!$ret) {
                    closedir($handle);
                    return false;
                }
            }
        }

        closedir($handle);

        return true;
    }

    /**
     * ファイル／ディレクトリを削除する。
     * @param string $path ファイルパス
     * @return boolean true 正常、false 異常
     */
    public static function delete($path) {

        if ($path === DIRECTORY_SEPARATOR) {
            // root directoryの削除は禁止
            throw new FileException("path={$path}, FileUtil#delete実行時に、ルートディレクトリが指定");
        }

        if (!self::existsFile($path)) {
            // ファイルが存在しない場合、削除の必要がないので正常とする
            return true;
        }

        if (is_dir($path)) {
            // ディレクトリを削除
            return self::deleteDir($path);
        } else {
            // ファイルを削除
            $ret = unlink($path);
            if (!$ret) {
                throw new FileException("path={$path}, FileUtil#delete実行時に、削除に失敗");
            }
            return $ret;
        }
    }

    /**
     * ファイル／ディレクトリを削除する。
     * @param string $dir ディレクトリパス
     * @return boolean true 正常、false 異常
     */
    public static function deleteDir($dir) {

        if ($dir === DIRECTORY_SEPARATOR) {
            // root directoryの削除は禁止
            throw new FileException("dir={$dir}, FileUtil#deleteDir実行時に、ルートディレクトリが指定");
        }

        if (!self::existsFile($dir)) {
            // ファイルが存在しない場合、削除の必要がないので正常とする
            return true;
        }

        $handle = opendir($dir);
        if (!$handle) {
            throw new FileException("dir={$dir}, FileUtil#deleteDir実行時に、ディレクトリ情報読み取りに失敗");
        }

        $ret = true;
        while (false !== ($item = readdir($handle))) {

            if ($item === "." || $item === "..") {
                continue;
            }

            // パス文字列を生成
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path) === true) {
                // 再帰的に削除
                if (!self::deleteDir($path)) {
                    $ret = false;
                }
            } else {
                // ファイルを削除
                if (!self::delete($path)) {
                    $ret = false;
                }
            }
        }

        closedir($handle);

        // ディレクトリを削除
        if (!rmdir($dir)) {
            throw new FileException("dir={$dir}, FileUtil#deleteDir実行時に、削除に失敗");
        }

        return $ret;
    }

    /**
     * ファイル／ディレクトリを削除する。
     * @param string $path ファイルパス
     * @return boolean true 正常、false 異常
     */
    public static function deleteFileOnly($path) {

        if ($path === DIRECTORY_SEPARATOR) {
            // root directoryの削除は禁止
            throw new FileException("path={$path}, FileUtil#deleteFileOnly実行時に、ルートディレクトリが指定");
        }

        if (self::existsFile($path) !== true) {
            // ファイルが存在しない場合、削除の必要がないので正常とする
            return true;
        }

        if (is_dir($path)) {
            // ディレクトリを削除
            return self::deleteDirFileOnly($path);
        } else {
            // ファイルを削除
            $ret = unlink($path);
            if (!$ret) {
                throw new FileException("path={$path}, FileUtil#deleteFileOnly実行時に、削除に失敗");
            }
            return $ret;
        }
    }

    /**
     * ファイル／ディレクトリを削除する。
     * @param string $dir ディレクトリパス
     * @return boolean true 正常、false 異常
     */
    public static function deleteDirFileOnly($dir) {

        if ($dir === DIRECTORY_SEPARATOR) {
            // root directoryの削除は禁止
            throw new FileException("dir={$dir}, FileUtil#deleteDirFileOnly実行時に、ルートディレクトリが指定");
        }

        if (!self::existsFile($dir)) {
            // ファイルが存在しない場合、削除の必要がないので正常とする
            return true;
        }

        $handle = opendir($dir);
        if (!$handle) {
            throw new FileException("dir={$dir}, FileUtil#deleteDirFileOnly実行時に、ディレクトリ情報読み取りに失敗");
        }

        $ret = true;
        while (false !== ($item = readdir($handle))) {

            if ($item === "." || $item === "..") {
                continue;
            }

            // パス文字列を生成
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path) === true) {
                // 再帰的に削除
                if (self::deleteDirFileOnly($path) === false) {
                    $ret = false;
                }
            } else {
                // ファイルを削除
                if (self::deleteFileOnly($path) === false) {
                    $ret = false;
                }
            }
        }

        closedir($handle);

        return $ret;
    }

    /**
     * 指定ディレクトリを削除する。
     * @param string $dir ディレクトリパス
     * @return boolean true 正常、false 異常
     */
    public static function deleteDirOnly($dir) {

        if ($dir === DIRECTORY_SEPARATOR) {
            // root directoryの削除は禁止
            throw new FileException("dir={$dir}, FileUtil#deleteDirOnly実行時に、ルートディレクトリが指定");
        }

        if (!self::existsFile($dir)) {
            // ファイルが存在しない場合、削除の必要がないので正常とする
            return true;
        }

        // ディレクトリを削除
        $ret = rmdir($dir);
        if (!$ret) {
            throw new FileException("dir={$dir}, FileUtil#deleteDirOnly実行時に、削除に失敗");
        }

        return $ret;
    }

    /**
     * ファイルコンテンツを読み込む。
     * @param string $filePath ファイルパス
     * @return string ファイルコンテンツ
     * @throws FileException ファイル関連の例外
     */
    public static function readFile(string $filePath) {

        if (self::existsFile($filePath) !== true) {
            return '';
        }

        // ファイルに出力する
        $ret = file_get_contents($filePath);
        if ($ret === false) {
            throw new FileException("filePath={$filePath}, FileUtil#readFile実行時に、ファイル読み込みに失敗");
        }

        return $ret;
    }

    /**
     * ディレクトリを作成する。
     * @param string $filePath ファイルパス
     * @param int $mode モード
     * @param boolean $recursive 再帰フラグ
     * @return boolean true 正常、false 異常
     */
    public static function makeDirectory($filePath, $mode = 0777, $recursive = true) {

        $ret = true;

        if (!self::existsFile($filePath)) {

            $ret = mkdir($filePath, $mode, $recursive);
            if (!$ret) {

                // ディレクトリ作成に失敗した場合は再度ディレクトリ作成を試みる
                // PHP標準のmkdirsだと、異なるプロセスやスレッドから新規に複数階層のディレクトリを同時に作成しようとすると後続のmkdirsがエラーになることがあるため
                $ret = RetryUtil::retryProcess(null, function () use ($filePath, $mode, $recursive) {

                            if (self::existsFile($filePath) !== true) {
                                // ファイルが存在していないのでディレクトリを作成する
                                $retMkdir = mkdir($filePath, $mode, $recursive);
                            } else {
                                // ファイルが存在しているのでスキップ
                                $retMkdir = true;
                            }
                            return $retMkdir;
                        });

                if (!$ret) {
                    throw new FileException("filePath={$filePath}, FileUtil#makeDirectory実行時に、ディレクトリ作成に失敗");
                }
            }
        }

        chmod($filePath, $mode);
        if (!$ret) {
            throw new FileException("filePath={$filePath}, FileUtil#makeDirectory実行時に、権限設定に失敗");
        }

        return $ret;
    }

    /**
     * ファイルコンテンツを書き込む。
     * @param string $filePath ファイルパス
     * @param string $contents ファイル内容
     * @param int $flags フラグ
     * @return boolean true 正常、false 異常
     */
    public static function writeFile($filePath, $contents, $flags = 0) {

        if (self::existsFile(dirname($filePath)) !== true) {
            if (self::makeDirectory(dirname($filePath)) !== true) {
                return false;
            }
        }

        // ファイルに出力する
        $ret = file_put_contents($filePath, $contents, $flags);
        if (!$ret) {
            throw new FileException("filePath={$filePath}, FileUtil#writeFileSimply実行時に、ファイル書き込みに失敗");
        }

        return $ret;
    }

    /**
     * ファイルコンテンツを安全に書き込む。
     * 一時ファイルに書き込み後、ファイルを移動する。ファイル移動はアトミックな単位で実施されるため、より安全にファイル操作できる。
     * @param string $filePath ファイルパス
     * @param string $contents ファイル内容
     * @param int $flags フラグ
     * @return boolean true 正常、false 異常
     */
    public static function writeFileSafely($filePath, $contents, $flags = 0) {

        $dirPath = dirname($filePath);
        self::makeDirectory($dirPath);

        $tempFile = tempnam($dirPath, 'tmp');

        $ret = self::writeFile($tempFile, $contents, $flags);
        if (!$ret) {
            return $ret;
        }

        $ret = self::move($tempFile, $filePath);
        if (!$ret) {
            // 移動に失敗した場合はコピーしたファイルを削除する
            self::delete($tempFile);
            throw new FileException("filePath={$filePath}, FileUtil#writeFile実行時に、ファイル移動に失敗");
        }

        return $ret;
    }

    /**
     * ファイルの存在チェック。
     * @param string $filePath ファイルパス
     * @return boolean true ファイルが存在する、false ファイルが存在しない
     */
    public static function existsFile($filePath) {
        clearstatcache(true, $filePath);
        return file_exists($filePath);
    }

    /**
     * 空ファイルの作成。
     * @param string $filePath ファイルパス
     * @param int $time 設定する時刻
     * @param int $atime 最終アクセス時刻
     * @return boolean true 正常、false 異常
     */
    public static function touch($filePath, $time = null, $atime = null) {

        if (self::existsFile(dirname($filePath)) !== true) {
            if (self::makeDirectory(dirname($filePath)) !== true) {
                throw new FileException("filePath={$filePath}, FileUtil#touch実行時に、ディレクトリ作成に失敗");
            }
        }

        $ret = true;
        if ($time !== null && $atime !== null) {
            $ret = touch($filePath, $time, $atime);
        } elseif ($time !== null && $atime === null) {
            $ret = touch($filePath, $time);
        } else {
            $ret = touch($filePath);
        }

        if (!$ret) {
            throw new FileException("filePath={$filePath}, FileUtil#touch実行時に、touchそのものに失敗");
        }

        return $ret;
    }

    /**
     * ファイルリストを取得する。
     * @param string $filePath ファイルパス
     * @param string $ext ファイル拡張子
     * @param bool $recursive 再起呼び出し
     * @param array $exclude 除外フォルダ
     * @param callable $onFetch ファイル取得時の処理
     * @return array ファイルリスト
     */
    public static function getFiles($filePath, $ext = null, $recursive = false, $exclude = null) {

        $filePath = rtrim($filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $ret = array();

        if (!self::existsFile($filePath)) {
            return $ret;
        }

        // 除外フォルダチェック
        if (is_array($exclude)) {
            if (in_array(basename($filePath), $exclude)) {
                return $ret;
            }
        }

        $handle = opendir($filePath);
        if (!$handle) {
            throw new FileException("filePath={$filePath}, FileUtil#getFiles実行時に、ディレクトリ情報読み取りに失敗");
        }

        while (false !== ($file = readdir($handle))) {

            if ($file === "." || $file === "..") {
                continue;
            }

            if (is_file($filePath . $file) === true) {

                if ($ext === null || $ext === pathinfo($file, PATHINFO_EXTENSION)) {
                    $ret[] = $filePath . $file;
                }
            } else if ($recursive === true &&
                    is_dir($filePath . $file) === true) {

                // 再帰呼び出しありの場合は、本メソッドを再呼び出しする
                $retSub = self::getFiles($filePath . $file, $ext, $recursive, $exclude);
                if (is_array($retSub)) {
                    $ret = array_merge($ret, $retSub);
                } else {
                    $ret = false;
                    break;
                }
            }
        }

        closedir($handle);

        return $ret;
    }

    /**
     * ファイルリストを取得する。
     * @param string $filePath ファイルパス
     * @param callable $onFetch ファイル取得時の処理
     * @param bool $recursive 再起呼び出し
     * @return array ファイルリスト
     */
    public static function getFilesFetch($filePath, $onFetch, $recursive = false) {

        $filePath = rtrim($filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!self::existsFile($filePath)) {
            return;
        }

        $handle = opendir($filePath);
        if (!$handle) {
            throw new FileException("filePath={$filePath}, FileUtil#getFilesFetch実行時に、ディレクトリ情報読み取りに失敗");
        }

        while (false !== ($file = readdir($handle))) {

            if ($file === "." || $file === "..") {
                continue;
            }

            if (is_file($filePath . $file) === true) {

                $onFetch($filePath . $file);
            } else if ($recursive === true &&
                    is_dir($filePath . $file) === true) {

                // 再帰呼び出しありの場合は、本メソッドを再呼び出しする
                self::getFilesFetch($filePath . $file, $onFetch, $recursive);
            }
        }

        closedir($handle);
    }

    /**
     * ディレクトリリストを取得する。
     * @param string $filePath ファイルパス
     * @param bool $recursive 再起呼び出し
     * @return array ディレクトリリスト
     */
    public static function getDirs($filePath, $recursive = false) {

        $filePath = rtrim($filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $ret = array();

        if (!self::existsFile($filePath)) {

            return $ret;
        }

        $handle = opendir($filePath);
        if (!$handle) {
            throw new FileException("filePath={$filePath}, FileUtil#getDirs実行時に、ディレクトリ情報読み取りに失敗");
        }

        while (false !== ($file = readdir($handle))) {

            if ($file === "." || $file === "..") {
                continue;
            }

            if (is_dir($filePath . $file) === true) {

                $ret[] = $filePath . $file;
            } else if ($recursive === true &&
                    is_dir($filePath . $file) === true) {

                // 再帰呼び出しありの場合は、本メソッドを再呼び出しする
                $retSub = self::getDirs($filePath . $file, $recursive);
                if (is_array($retSub)) {
                    $ret = array_merge($ret, $retSub);
                } else {
                    $ret = false;
                    break;
                }
            }
        }

        closedir($handle);

        return $ret;
    }

    /**
     * 表示用のファイルパスに変換する。
     * @param string $filePath ファイルパス
     * @return string 表示用のファイルパス
     */
    public static function convertToDisplayFilePath($filePath) {

        if (PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT') {

            // for UNIX or LINUX.
        } else {
            // for Windows.
            // ファイルシステムからファイル名を取得することで
            // CP932で取得されるため、UTF8に変換する
            $encoding = mb_check_encoding($filePath, 'CP932');
            if ($encoding !== false) {
                // CP932であるため、UTF-8に変換する
                $filePath = mb_convert_encoding($filePath, 'UTF-8', 'CP932');
            }
        }

        return $filePath;
    }

    /**
     * 拡張子を取得する。
     * @param string $filename ファイル名
     */
    public static function getExtension($filename) {

        return substr($filename, strrpos($filename, '.') + 1);
    }

    /**
     * 文字列の最後のスラッシュまたはバックスラッシュを除去し、ファイル名を連結する。
     * @param string $dir ディレクトリ
     * @param string $file ファイル
     * @return string ファイルパス
     */
    public static function combinePath($dir, $file) {

        return rtrim(self::convertToOsDirSeparator($dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(self::convertToOsDirSeparator($file), DIRECTORY_SEPARATOR);
    }

    /**
     * ファイルパスのスラッシュをOS固有の区切り文字に変換する。
     * @param string $file ファイルパス
     * @return string ファイルパス
     */
    public static function convertToOsDirSeparator($file) {

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
    }

    /**
     * ファイル／ディレクトリのパスを正式なパスに変換する。
     * @param string $path ファイルパス
     * @return boolean true 正常、false 異常
     */
    public static function realpath($path) {

        $temp = realpath($path);
        if ($temp === false) {
            throw new Exception('');
        }

        return $temp;
    }

    /**
     * ファイル／ディレクトリのパスを正式なパスに変換する。
     * あわせてディレクトリも作成する。
     * @param string $path ファイルパス
     * @return boolean true 正常、false 異常
     */
    public static function realpathWithMakeDir($path) {

        $temp = realpath($path);
        if ($temp === false) {
            FileUtil::makeDirectory($path);
            $temp = realpath($path);
            if ($temp === false) {
                throw new Exception('');
            }
        }

        return $temp;
    }

    /**
     * ファイルパスを比較する。
     * @param string $path1 ファイルパス1
     * @param string $path2 ファイルパス2
     * @return boolean true 一致、false 不一致
     */
    public static function equalsPath($path1, $path2) {

        if (\Common\Util\CommonUtil::isWindows() === true) {
            $path1 = rtrim(self::convertToOsDirSeparator(strtolower($path1)), DIRECTORY_SEPARATOR);
            $path2 = rtrim(self::convertToOsDirSeparator(strtolower($path2)), DIRECTORY_SEPARATOR);
        } else {
            $path1 = rtrim(self::convertToOsDirSeparator($path1), DIRECTORY_SEPARATOR);
            $path2 = rtrim(self::convertToOsDirSeparator($path2), DIRECTORY_SEPARATOR);
        }

        return ($path1 === $path2);
    }

    /**
     * パスがベースパスと一致しているかおよび階層関係が有るかを確認する。
     * @param string $basePath ベースパス
     * @param string $path パス
     * @return boolean true 一致、false 不一致
     */
    public static function inPath($basePath, $path) {

        if (ValUtil::_empty($basePath) || ValUtil::_empty($path)) {
            return false;
        }

        if (\Common\Util\CommonUtil::isWindows() === true) {
            $basePath = rtrim(self::convertToOsDirSeparator(strtolower($basePath)), DIRECTORY_SEPARATOR);
            $path = rtrim(self::convertToOsDirSeparator(strtolower($path)), DIRECTORY_SEPARATOR);
        } else {
            $basePath = rtrim(self::convertToOsDirSeparator($basePath), DIRECTORY_SEPARATOR);
            $path = rtrim(self::convertToOsDirSeparator($path), DIRECTORY_SEPARATOR);
        }

        $basePathArr = explode(DIRECTORY_SEPARATOR, $basePath);
        $pathArr = explode(DIRECTORY_SEPARATOR, $path);

        $ret = false;

        if (count($basePathArr) <= count($pathArr)) {
            $equalsCount = 0;
            for ($i = 0; $i < count($basePathArr); $i++) {
                if ($basePathArr[$i] === $pathArr[$i]) {
                    $equalsCount++;
                }
            }

            if ($equalsCount === count($basePathArr)) {
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     * UNCパスであるかをチェックする。
     * 例）\\host\share
     * @param string $path パス
     * @return boolean true UNCパス、false UNCパスではない
     */
    public static function isUncPath($path) {

        if (mb_strpos($path, '\\\\') === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * バイト数をバイト文字列に変換する。
     * @param int $byteNum バイト数
     * @return string バイト文字列
     */
    public static function convertByteNumberToByteWithUint($byteNum, $isLongUnit = false) {

        if ($isLongUnit === true) {
            $si_prefix = array('Byte', 'KByte', 'MByte', 'GByte', 'TByte', 'EByte', 'ZByte', 'YByte');
        } else {
            $si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
        }

        if ((int) $byteNum === 0) {
            return sprintf('%d', $byteNum) . $si_prefix[0];
        }

        $base = 1024;

        $class = min((int) log($byteNum, $base), count($si_prefix) - 1);
        return sprintf('%1.2f', $byteNum / pow($base, $class)) . $si_prefix[$class];
    }

    /**
     * ドライブ名を取得する。
     * @param string $path ファイルパス
     * @return string ドライブ名
     */
    public static function getDriveName($path) {

        $realpath = realpath($path);
        if ($realpath === false) {
            return null;
        }

        $realpathArray = explode(DIRECTORY_SEPARATOR, trim($realpath, DIRECTORY_SEPARATOR));

        if (is_array($realpathArray) && count($realpathArray) > 0) {
            if (\Common\Util\CommonUtil::isWindows() === true) {
                return $realpathArray[0];
            } else {
                return DIRECTORY_SEPARATOR . $realpathArray[0];
            }
        }

        return null;
    }

    /**
     * ファイルサイズを取得する。
     * @param string $path ファイルパス
     * @return int ファイルサイズ
     */
    public static function getFileSize($path) {

        $ret = 0;

        if (is_dir($path) === true) {
            $files = self::getFiles($path, null, true);
        } else {
            $files = array($path);
        }

        foreach ($files as $file) {
            if (self::existsFile($file) === true) {
                $ret += filesize($file);
            }
        }

        return $ret;
    }

    /**
     * 一時ファイルを作成する。
     * @param string $path ファイルパス
     * @param string $prefix 作成されるテンポラリファイルのプレフィックス
     * @return string 一時ファイルのフルパス（作成に失敗した場合はfalse）
     */
    public static function createTempFile($path, $prefix = 'tmp') {

        self::makeDirectory($path);
        $ret = tempnam($path, $prefix);

        return $ret;
    }

    /**
     * ファイル更新日時を取得する。
     * @param string $filePath ファイルパス
     * @return int ファイル更新日時
     */
    public static function getFilemtime($filePath) {

        clearstatcache(true, $filePath);
        if (file_exists($filePath) === true) {
            return filemtime($filePath);
        } else {
            return null;
        }
    }

    /**
     * 圧縮ファイルを解凍する。
     * @param type $zipPath 圧縮ファイルパス
     * @param type $toPath 解凍先パス
     * @return bool 処理結果(true:正常|false:異常)
     */
    public static function extractArchive($zipPath, $toPath): bool {

        // ファイルパスチェック
        if (!self::existsFile($zipPath)) {
            return false;
        }
        // 解凍先のパスがなければ作成
        if (!self::existsFile($toPath)) {
            if (!FileUtil::makeDirectory($toPath)) {
                return false;
            }
        }

        $zip = new \ZipArchive();
        if (true !== $zip->open($zipPath)) {
            return false;
        }

        // 圧縮ファイルの解凍開始
        for ($i = 0; $i < $zip->numFiles; $i++) {

            $stat = $zip->statIndex($i);

            $entryName = $stat['name'];
            $entryNameRaw = $zip->getNameIndex($i, \ZipArchive::FL_ENC_RAW);

            if (mb_strrpos($entryName, '/') === mb_strlen($entryName) - 1) {
                // ディレクトリエントリの場合
                continue;
            }

            // 文字コード変換(マルチバイト対応)
            // Win環境の圧縮だとファイル名がラテン文字で解凍されるため変換
            $encoded = mb_detect_encoding($entryNameRaw, 'UTF-8, CP932, SJIS-win', true);

            $destName = mb_convert_encoding($entryNameRaw, 'UTF-8', $encoded);

            $destFilePath = $toPath . DIRECTORY_SEPARATOR . FileUtil::convertToOsDirSeparator($destName);

            FileUtil::makeDirectory(dirname($destFilePath));

            $fw = fopen($destFilePath, 'w');
            $fr = $zip->getStream($entryName);

            while (!feof($fr)) {

                $contents = fread($fr, 1024);

                if ($contents === false) {
                    return false;
                }

                if (fwrite($fw, $contents) === false) {
                    return false;
                }
            }

            fclose($fr);
            fclose($fw);
        }

        return $zip->close();
    }

    /**
     * JPEG画像の撮影日時を取得する。
     * @param type $filePath 画像ファイルパス
     * @return string 撮影日時(JPEG画像以外は空文字)
     */
    public static function getPhotoDateTime($filePath): string {

        $result = '';

        if (ValUtil::isEmpty($filePath)) {
            return $result;
        }
        // 画像情報を取得
        $fileData = exif_read_data($filePath, null, true);
        if (isset($fileData['EXIF']['DateTimeOriginal'])) {

            $matches = [];

            $datePattern = '/\A(?<year>\d{4}):(?<month>\d{1,2}):(?<day>\d{1,2}) (?<hour>\d{2}):(?<minute>\d{2}):(?<second>\d{2})\z/';
            if (preg_match($datePattern, $fileData['EXIF']['DateTimeOriginal'], $matches)) {
                $result = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $matches['year'], $matches['month'], $matches['day'], $matches['hour'], $matches['minute'], $matches['second']);
            }
        }
        return $result;
    }

    /**
     * 画像をリサイズする。
     * @param type $srcPath リサイズ元ファイルパス
     * @param type $destPath リサイズ先ファイルパス
     * @param type $containerSize リサイズ値
     * @return bool 処理結果(true:正常|false:異常)
     */
    public static function makeResizeImage($srcPath, $destPath, $containerSize, $containerRatio): bool {

        // 作成元ファイル存在チェック
        if (!self::existsFile($srcPath)) {
            return false;
        }
        // 画像サイズを取得(画像ファイル以外は処理しない)
        list($srcWidth, $srcHeight, $imageType) = getimagesize($srcPath);
        if (!self::isImageType($imageType)) {
            return false;
        }
        // リサイズ先が存在しない場合は、リサイズ先ディレクトリを作成する
        if (!self::existsFile(dirname($destPath))) {
            if (!self::makeDirectory(dirname($destPath))) {
                return false;
            }
        }
        // 縦横比を算出
        $ratio = $srcWidth / $srcHeight;
        if (!ValUtil::isEmpty($containerSize)) {
            // 縦 ＞ 横 の場合
            if ($ratio < 1) {
                // 画像サイズをセット
                $canvasWidth = intval($containerSize * $ratio);
                $canvasHeight = $containerSize;
            } else {
                $canvasHeight = intval($containerSize / $ratio);
                $canvasWidth = $containerSize;
            }
        } else if (!ValUtil::isEmpty($containerRatio)) {
            // 縦 ＞ 横 の場合
            $resizeRatio = 0.8;
            if ($ratio < 1) {
                if ($srcHeight > 2000) {
                    $resizeRatio = 0.4;
                } else if ($srcHeight > 1000) {
                    $resizeRatio = 0.6;
                }
            } else {
                if ($srcWidth > 5000) {
                    $resizeRatio = 0.2;
                } else if ($srcWidth > 3000) {
                    $resizeRatio = 0.4;
                } else if ($srcHeight > 1000) {
                    $resizeRatio = 0.6;
                }
            }
            $canvasHeight = intval($srcHeight * $resizeRatio);
            $canvasWidth = intval($srcWidth * $resizeRatio);
        }
        // 元画像がリサイズ値より小さければリサイズしない
        if (($canvasWidth > $srcWidth) && ($canvasHeight > $srcHeight)) {
            $canvasWidth = $srcWidth;
            $canvasHeight = $srcHeight;
        }
        // リサイズ画像作成
        $source = null;
        if (IMAGETYPE_JPEG === $imageType) {
            $source = \imagecreatefromjpeg($srcPath);
        } else if (IMAGETYPE_GIF === $imageType) {
            $source = \imagecreatefromgif($srcPath);
        } else if (IMAGETYPE_PNG === $imageType) {
            $source = \imagecreatefrompng($srcPath);
        }

        try {
            // サンプリング実施
            $canvas = \imagecreatetruecolor($canvasWidth, $canvasHeight);

            if (
                    IMAGETYPE_GIF === $imageType ||
                    IMAGETYPE_PNG === $imageType) {

                // 透過色を取得
                $trnprt_indx = \imagecolortransparent($source);

                // 透過色を保持するかの判定
                if ($trnprt_indx >= 0) {
                    /*
                     * 透過色設定時
                     */

                    // カラーインデックスからカラーを取得する（RGB）
                    $trnprt_color = \imagecolorsforindex($source, $trnprt_indx);
                    if ($trnprt_color !== false) {
                        // 画像で使用する色を作成する
                        if (($trnprt_indx = \imagecolorallocate($canvas, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue'])) === false) {
                            return false;
                        }
                        // 塗り潰す
                        if (\imagefill($canvas, 0, 0, $trnprt_indx) === false) {
                            return false;
                        }
                        // 透明色を定義する
                        if (\imagecolortransparent($canvas, $trnprt_indx) !== $trnprt_indx) {
                            return false;
                        }
                    }
                } else if (IMAGETYPE_PNG === $imageType) {
                    /*
                     * PNG 透過情報（alpha）
                     */

                    // イメージのブレンドモードをオフにする
                    if (\imagealphablending($canvas, false) === false) {
                        return false;
                    }
                    // 画像で使用する色を透過度を指定して作成する（127=アルファ値を完全な透明な情報にする）
                    if (($color = \imagecolorallocatealpha($canvas, 0, 0, 0, 127)) === false) {
                        return false;
                    }
                    // 塗り潰す
                    if (\imagefill($canvas, 0, 0, $color) === false) {
                        return false;
                    }
                    // イメージのブレンドモードをオンにする
                    if (\imagesavealpha($canvas, true) === false) {
                        return false;
                    }
                }
            }

            if (!\imagecopyresampled($canvas, $source, 0, 0, 0, 0, $canvasWidth, $canvasHeight, $srcWidth, $srcHeight)) {
                return false;
            }

            // 画像の方向をセット
            $fileData = \exif_read_data($srcPath, null, true);
            if (isset($fileData['IFD0']['Orientation'])) {

                $orientation = $fileData['IFD0']['Orientation'];
                if (3 == $orientation) {
                    // 180度回転
                    $canvas = \imagerotate($source, 180, 0, 0);
                } else if ((5 == $orientation) || (8 == $orientation)) {
                    // 反時計回りに90度回転
                    $canvas = \imagerotate($source, 90, 0, 0);
                } else if ((6 == $orientation) || (7 == $orientation)) {
                    // 時計回りに90度回転
                    $canvas = \imagerotate($canvas, 270, 0, 0);
                }
            }

            // 画像の出力
            if (IMAGETYPE_JPEG === $imageType) {
                return \imagejpeg($canvas, $destPath);
            } else if (IMAGETYPE_GIF === $imageType) {
                return \imagegif($canvas, $destPath);
            } else if (IMAGETYPE_PNG === $imageType) {
                return \imagepng($canvas, $destPath);
            }
        } finally {
            // リソース開放
            \imagedestroy($source);
            \imagedestroy($canvas);
        }
    }

}
