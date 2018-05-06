<?php

namespace logger;

/**
 * Class for implementing a logger.
 * 
 *
 * @author LORD AGWA
 */
final class Logger {

    const LOG_LEVEL_DEFAULT = 0, LOG_LEVEL_LOWEST = 0;
    const LOG_TYPE_ERROR = "ERROR", LOG_TYPE_DEBUG = "DEBUG", LOG_TYPE_INFO = "INFO", LOG_TYPE_WARNING = "WARNING";

    private $log_history = [];
    private $channels = [];
    private $formatter;
    private $defaultChannel = 0;
    private $useDefaultChanel = true;

    public function __construct(Formatter $formatter, $channels = []) {
        $this->formatter = $formatter;

        if (!empty($channels)) {
            $this->initChannels($channels);
        }
    }

    public function addChannel(Channel $channel) {
        if (!$channel instanceof Channel) {
            throw new \InvalidArgumentException("Channel variable must be an instance of Channel class");
        }
        if (array_search($channel, $this->channels)) {
            return false;
        }
        $this->channels[] = $channel;
    }

    private function addLogHistory(Log $newlog) {
        $this->log_history[] = $newlog;
    }

    protected function init() {
        
    }

    private function initChannels($channels) {
        if (empty($channels)) {
            return false;
        }
        if (!is_array($channels)) {
            if ($channels instanceof Channel) {
                $this->channels = [$channels];
                return true;
            } else {
                throw new \InvalidArgumentException("Provided channel must implement \logger\Channel interface");
            }
        } else {
            array_walk($channels, function ($element) {
                if (!$element instanceof Channel) {
                    throw new \InvalidArgumentException("Provided channel must implement \logger\Channel interface");
                }
            });
            $this->channels = $channels;
            return true;
        }
    }

    public function log($type, $message, $name, $channel = null, $level = Logger::LOG_LEVEL_DEFAULT) {
        $formatted = $this->formatter->format(new Log($type, $message, $name, $level));
        $result = false;
        if (empty($channel)) {
            if ($this->useDefaultChanel && array_key_exists($this->defaultChannel, $this->channels)) {
                $result = $this->logToChannel($this->channels[$this->defaultChannel], $formatted);
            } else {
                $result = $this->logToChannels($formatted);
            }
        } else {
            $result = $this->logToChannel($channel, $formatted);
        }
        $this->addLogHistory($formatted);
        return $result;
    }

    /**
     * Writes a log to all channels. 
     * 
     * 
     * @param \logger\callable $callback
     * @param array $res Result from each channel logged with the channel's index as its key.
     * @return boolean True if all channels returned true, false otherwise.
     * 
     * @throws \Exception\LoggerException If any channel is not an instance of a @see Channel
     */
    private function logToChannels(Log $log, callable $callback = null, &$res = null) {
        $res = [];
        $result = true;
        foreach ($this->channels as $index => $channel) {
            if (!$channel instanceof Channel) {
                throw new \Exception\LoggerException("{$channel} is not an instance of Channel");
            }
            $channel_result = $this->logToChannel($channel, $log);
            if ($channel_result === false) {
                $result = false;
            }

            if (!empty($callback) && is_callable($callback)) {
                $callback($log, $channel_result, $channel);
            }
            $res[$index] = $channel_result;
        }
        return $result;
    }

    private function logToChannel(Channel $channel, Log $log, callable $callback = null) {
        
    }

    public function removeChannel($channel) {
//        if(array_search($channel,  $this->channels) && ($keys = array_keys($this->channels, $channel))){
//            for
//        }
        $this->channels = array_filter($this->channels, function ($value)use (&$channel) {
            if ($channel == $value) {
                return false;
            } else {
                return true;
            }
        });
    }

    public function setDefaultChannel(Channel $channel) {
        if (!($channel_index = array_search($channel, $this->channels))) {
            return false;
        }
        $this->defaultChannel = $channel_index;
        return true;
    }

    public function useDefaultChannel($useDefault) {
        $this->useDefaultChanel = $useDefault;
    }

}
