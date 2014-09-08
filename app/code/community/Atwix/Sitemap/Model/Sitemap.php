<?php
/**
 * Atwix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.

 * @category    Atwix Mod
 * @package     Atwix_Sitemap
 * @author      Atwix Core Team
 * @copyright   Copyright (c) 2014 Atwix (http://www.atwix.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * rewrite was made to add possibility of 2-level sitemap
 */

class Atwix_Sitemap_Model_Sitemap extends Mage_Sitemap_Model_Sitemap
{
    const     ITEM_LIMIT = 50000;
    protected $_io;
    protected $_subfiles = array();

    public function generateXml()
    {
        $enabled = (bool) Mage::getStoreConfig('atwix_sitemap/general/enabled');
        if(!$enabled) {
            return parent::generateXml();
        }
        $helper = Mage::helper('atwix_sitemap');
        
        $limit = (int) Mage::getStoreConfig('atwix_sitemap/general/limit');
        if ($limit == 0) {
            $limit = self::ITEM_LIMIT;
        }
        $this->fileCreate();

        $storeId = $this->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        /**
         * Generate categories sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/category/changefreq');
        $priority = (string) Mage::getStoreConfig('sitemap/category/priority');
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);

        /**
         * Delete old category files
         */
        try {
            foreach(glob($this->getPath() . substr($this->getSitemapFilename(), 0, strpos($this->getSitemapFilename(), '.xml')) . '_cat_*.xml') as $f) {
                unlink($f);
            }
        } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                $helper->__('Unable to delete old categories sitemaps') . $e->getMessage()
            );
        }

        /**
         * Brake to pages
         */
        $pages = ceil( count($collection) / $limit );
        $i = 0;
        while( $i < $pages ) {
            $name = '_cat_' . $i . '.xml';
            $this->subFileCreate($name);
            $subCollection = array_slice($collection, $i * $limit, $limit);
            foreach ($subCollection as $item) {
                $xml = sprintf(
                    '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($baseUrl . $item->getUrl()),
                    $date,
                    $changefreq,
                    $priority
                );
                $this->sitemapSubFileAddLine($xml, $name);
            }
            $this->subFileClose($name);
            /**
             * Adding link of the subfile to the main file
             */
            $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>', htmlspecialchars( $this->getSubFileUrl($name)), $date);
            $this->sitemapFileAddLine($xml);
            $i++;
        }

        unset($collection);

        /**
         * Generate products sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/product/changefreq');
        $priority = (string) Mage::getStoreConfig('sitemap/product/priority');
        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);

        /**
         * Delete old category files
         */
        try {
            foreach(glob($this->getPath() . substr($this->getSitemapFilename(), 0, strpos($this->getSitemapFilename(), '.xml')) . '_prod_*.xml') as $f) {
                unlink($f);
            }
        } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                $helper->__('Unable to delete old products sitemaps') . $e->getMessage()
            );
        }

        /**
         * Brake to pages
         */
        $pages = ceil( count($collection) / $limit );
        $i = 0;
        while( $i < $pages ) {
            $name = '_prod_' . $i . '.xml';
            $this->subFileCreate($name);
            $subCollection = array_slice($collection, $i * $limit, $limit);
            foreach ($subCollection as $item) {
                $xml = sprintf(
                    '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($baseUrl . $item->getUrl()),
                    $date,
                    $changefreq,
                    $priority
                );
                $this->sitemapSubFileAddLine($xml, $name);
            }
            $this->subFileClose($name);
            /**
             * Adding link of the subfile to the main file
             */
            $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>', htmlspecialchars($this->getSubFileUrl($name)), $date);
            $this->sitemapFileAddLine($xml);
            $i++;
        }

        unset($collection);

        /**
         * Generate cms pages sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/page/changefreq');
        $priority = (string) Mage::getStoreConfig('sitemap/page/priority');
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);

        /**
         * Delete old cms pages files
         */
        try {
            foreach(glob($this->getPath() . substr($this->getSitemapFilename(), 0, strpos($this->getSitemapFilename(), '.xml')) . '_pages_*.xml') as $f) {
                unlink($f);
            }
        } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                $helper->__('Unable to delete old products sitemaps') . $e->getMessage()
            );
        }

        /**
         * Brake to pages
         */
        $pages = ceil( count($collection) / $limit );
        $i = 0;
        while( $i < $pages ) {
            $name = '_pages_' . $i . '.xml';
            $this->subFileCreate($name);
            $subCollection = array_slice($collection, $i * $limit, $limit);
            foreach ($subCollection as $item) {
                $xml = sprintf(
                    '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($baseUrl . $item->getUrl()),
                    $date,
                    $item->getUrl() == 'home' ? 'always' : $changefreq,
                    $item->getUrl() == 'home' ? '1' : $priority
                );
                $this->sitemapSubFileAddLine($xml, $name);
            }
            $this->subFileClose($name);
            /**
             * Adding link of the subfile to the main file
             */
            $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>', htmlspecialchars($this->getSubFileUrl($name)), $date);
            $this->sitemapFileAddLine($xml);
            $i++;
        }
        unset($collection);

        $this->fileClose();

        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

    /**
     * Create sitemap subfile by name in sitemap directory
     *
     * @param $name
     */
    protected function subFileCreate($name)
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $io->streamOpen( substr($this->getSitemapFilename(), 0, strpos($this->getSitemapFilename(), '.xml')) . $name);

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
        $this->_subfiles[$name] = $io;
    }

    /**
     * Add line to sitemap subfile
     *
     * @param $xml
     * @param $name
     */
    public function sitemapSubFileAddLine($xml, $name) {
        $this->_subfiles[$name]->streamWrite($xml);
    }

    /**
     * Create main sitemap file
     */
    protected function fileCreate() {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $io->streamOpen($this->getSitemapFilename());

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
        $this->_io = $io;
    }

    /**
     * Add closing tag and close sitemap file
     */
    protected function fileClose() {
        $this->_io->streamWrite('</sitemapindex>');
        $this->_io->streamClose();
    }

    /**
     * Add closing tag and close sitemap subfile by the name
     *
     * @param $name
     */
    protected function subFileClose($name) {
        $this->_subfiles[$name]->streamWrite('</urlset>');
        $this->_subfiles[$name]->streamClose();
    }

    /**
     * Get URL of sitemap subfile by the name
     *
     * @param $name
     * @return string
     */
    public function getSubFileUrl($name)
    {
        $fileName = substr($this->getSitemapFilename(), 0, strpos($this->getSitemapFilename(), '.xml')) . $name;
        $filePath = Mage::app()->getStore($this->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $this->getSitemapPath();
        $filePath = str_replace('//','/',$filePath);
        $filePath = str_replace(':/','://',$filePath);
        return $filePath . $fileName;
    }

    /**
     * Add line to the main file
     *
     * @param $xml
     */
    public function sitemapFileAddLine($xml)
    {
        $this->_io->streamWrite($xml);
    }
}
