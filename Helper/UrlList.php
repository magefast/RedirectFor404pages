<?php
/**
 * @author magefast@gmail.com www.magefast.com
 */

namespace Strekoza\RedirectFor404pages\Helper;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\Helper\Data;

class UrlList extends Data
{
    public const REDIRECT_DIR_NAME = 'redirect404';
    public const REDIRECT_FILE_NAME = '404.csv';
    public const CSV_DELIMITER = ';';

    private array $_urlListArray = [];
    private Filesystem $filesystem;
    private Csv $csv;

    /**
     * @param Filesystem $filesystem
     * @param Csv $csv
     */
    public function __construct(Filesystem $filesystem, Csv $csv)
    {
        $this->filesystem = $filesystem;
        $this->csv = $csv;
    }

    /**
     * @param $value
     * @return string
     */
    public function buildUrl($value): string
    {
        return $value['domain'] . $value['url'];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function urlList(): ?array
    {
        $data = $this->getCsvContent();

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    private function getCsvContent(): ?array
    {
        if (!file_exists($this->getFilePath())) {
            return null;
        }

        $mageCsv = $this->csv;
        $mageCsv->setEnclosure('"');
        $mageCsv->setDelimiter(self::CSV_DELIMITER);

        $data = $mageCsv->getData($this->getFilePath());

        if (count($data) == 0) {
            return null;
        }

        $dataPrepared = $this->prepareData($data);
        unset($data);

        return $dataPrepared;
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    private function getFilePath(): string
    {
        return $this->getDirPath() . '/' . self::REDIRECT_FILE_NAME;
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    private function getDirPath(): string
    {
        $varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

        return $varDirectory->getAbsolutePath(self::REDIRECT_DIR_NAME);
    }

    /**
     * @param $data
     * @return array
     */
    private function prepareData($data): array
    {
        $columnsKey = [];
        $dataNew = [];
        foreach ($data[0] as $k => $v) {
            $columnsKey[$k] = $v;
        }
        unset($data[0]);
        foreach ($data as $m) {
            $md = [];
            foreach ($columnsKey as $bb => $lf) {
                $md[$lf] = $m[$bb];
            }
            $dataNew[] = $md;
        }

        return $dataNew;
    }

}
