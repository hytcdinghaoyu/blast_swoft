<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2019/1/16
 * Time: 上午11:16
 */

namespace App\Utils;


use Swoft\App;
use Swoft\Core\Coroutine;

class Logger extends \Swoft\Log\Logger
{
    /**
     * 记录日志
     *
     * @param int   $level   日志级别
     * @param mixed $message 信息
     * @param array $context 附加信息
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = array())
    {
        if (!$this->enable) {
            return true;
        }

        $levelName = static::getLevelName($level);

        if (!static::$timezone) {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        }

        // php7.1+ always has microseconds enabled, so we do not need this hack
        if ($this->microsecondTimestamps && PHP_VERSION_ID < 70100) {
            $ts = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone);
        } else {
            $ts = new \DateTime(null, static::$timezone);
        }

        $ts->setTimezone(static::$timezone);

        $message = $this->formatMessage($message);
//        $message = $this->getTrace($message);
        $record = $this->formateRecord($message, $context, $level, $levelName, $ts, []);

        foreach ($this->processors as $processor) {
            $record = \Swoole\Coroutine::call_user_func($processor, $record);
        }

        $this->messages[] = $record;

        if (App::$isInTest || \count($this->messages) >= $this->flushInterval) {
            $this->flushLog();
        }

        return true;
    }

    /**
     * 格式化一条日志记录
     *
     * @param string    $message   信息
     * @param array     $context   上下文信息
     * @param int       $level     级别
     * @param string    $levelName 级别名
     * @param \DateTime $ts        时间
     * @param array     $extra     附加信息
     *
     * @return array
     */
    public function formateRecord($message, $context, $level, $levelName, $ts, $extra)
    {
        $record = array(
            'messages'   => $message,
            'context'    => $context,
            'level'      => $level,
            'level_name' => $levelName,
            'channel'    => $this->name,
            'datetime'   => $ts,
            'extra'      => $extra,
            'clientIp'   => (Coroutine::tid() === -1) ? '' : Utils::getRealIp(),
            'category'   => $context[0] ?? '',
        );

        return $record;
    }

    /**
     * actionLogger调用
     *
     * @param bool $flush 是否强制刷新日志
     */
    public function actionLogFlush($flush = false)
    {
        if ($this->flushRequest || $flush) {
            $this->flushLog();
        }
    }
}