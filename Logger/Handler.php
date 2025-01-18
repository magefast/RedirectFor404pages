<?php
/**
 * @author magefast@gmail.com www.magefast.com
 */

namespace Strekoza\RedirectFor404pages\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/404.log';
}
