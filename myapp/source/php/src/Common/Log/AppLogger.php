<?php

namespace App\Common\Log;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * ログオブジェクト。
 */
class AppLogger {

    /**
     * @var array ログリスト
     */
    private static $instance = null;

    /**
     * ログオブジェクトを取得する。
     * @param LoggerInterface $logger ロガー
     */
    public static function set(LoggerInterface $logger) {

        self::$instance = new AppLogger($logger);
    }

    /**
     * ログオブジェクトを取得する。
     * @return AppLogger ロガー
     */
    public static function get() {

        return self::$instance;
    }

    /**
     * @var LoggerInterface ロガー
     */
    protected $logger;

    /**
     * コンストラクタ。
     * @param \App\Common\Log\LoggerInterface $logger ロガー
     */
    protected function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * DEBUGレベルであるかの判定。
     * @return bool DEBUGレベル
     */
    public function isDebugEnabled() {
        return $this->logger->isHandling(Logger::DEBUG);
    }

    /**
     * INFOレベルであるかの判定。
     * @return bool INFOレベル
     */
    public function isInfoEnabled() {
        return $this->logger->isHandling(Logger::INFO);
    }

    /**
     * WARNレベルであるかの判定。
     * @return bool WARNレベル
     */
    public function isWarnEnabled() {
        return $this->logger->isHandling(Logger::WARN);
    }

    /**
     * ERRORレベルであるかの判定。
     * @return bool ERRORレベル
     */
    public function isErrorEnabled() {
        return $this->logger->isHandling(Logger::ERROR);
    }

    /**
     * CRITICALレベルであるかの判定。
     * @return bool CRITICALレベル
     */
    public function isCriticalEnabled() {
        return $this->logger->isHandling(Logger::CRITICAL);
    }

    /**
     * ALERTレベルであるかの判定。
     * @return bool ALERTレベル
     */
    public function isAlertEnabled() {
        return $this->logger->isHandling(Logger::ALERT);
    }

    /**
     * EMERGENCYレベルであるかの判定。
     * @return bool EMERGENCYレベル
     */
    public function isEmergencyEnabled() {
        return $this->logger->isHandling(Logger::EMERGENCY);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = array()) {
        $this->logger->debug($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = array()) {
        return $this->logger->info($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = array()) {
        return $this->logger->notice($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = array()) {
        return $this->logger->warning($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = array()) {
        return $this->logger->error($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = array()) {
        return $this->logger->critical($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = array()) {
        return $this->logger->alert($message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = array()) {
        return $this->logger->emergency($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array()) {
        $this->logger->log($level, $message, $context);
    }

}
