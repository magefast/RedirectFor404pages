<?php
/**
 * @author magefast@gmail.com www.magefast.com
 */

namespace Strekoza\RedirectFor404pages\Controller\Plugin;

use Magento\Cms\Controller\Noroute\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlInterface;
use Strekoza\RedirectFor404pages\Helper\UrlList;
use Strekoza\RedirectFor404pages\Logger\Logger;

class TrackNoRoutePages
{
    private Logger $logger;
    private Context $context;
    private UrlList $helper;
    private UrlInterface $urlInterface;

    /**
     * @param Context $context
     * @param Logger $logger
     * @param UrlInterface $urlInterface
     * @param UrlList $dataHelper
     */
    public function __construct(
        Context      $context,
        Logger       $logger,
        UrlInterface $urlInterface,
        UrlList      $dataHelper
    )
    {
        $this->context = $context;
        $this->logger = $logger;
        $this->urlInterface = $urlInterface;
        $this->helper = $dataHelper;
    }

    public function afterExecute(Index $subject, $return)
    {
        $arrayUrlPart = $this->helper->urlList();
        if (empty($arrayUrlPart)) {
            return $return;
        }

        $currentUrl = $this->urlInterface->getCurrentUrl();

        foreach ($arrayUrlPart as $url) {
            $checkUrl = $this->_checkUrl($url['part'], $currentUrl);

            if ($checkUrl === true) {
                $this->logger->info('>>>' . $currentUrl . '<<<');

                /**
                 * Redirect 301
                 */
                $url = $this->helper->buildUrl($url);
                $resultRedirect = $this->context->getResultRedirectFactory()->create();
                $resultRedirect->setUrl($url)->setHttpResponseCode(301);
                return $resultRedirect;
            }
        }

        return $return;
    }

    /**
     * @param $urlPart
     * @param $currentUrl
     * @return bool
     */
    private function _checkUrl($urlPart, $currentUrl): bool
    {
        if (is_string($urlPart)) {
            $pos = '';
            $pos = strpos($currentUrl, $urlPart);

            if ($pos === false) {
                return false;
            } else {
                return true;
            }
        }

        if (is_array($urlPart)) {
            $existAll = false;
            foreach ($urlPart as $part) {
                if (preg_match("#" . $part . "#i", $currentUrl)) {
                    $existAll = true;
                } else {
                    return false;
                }
            }

            if ($existAll === true) {
                return true;
            }
        }

        return false;
    }
}
