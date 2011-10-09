<?php

/**
 * ShopMotorcycle
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    www.motoweby.cz
 * @subpackage model
 * @author     Michal Palma
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ShopMotorcycle extends BaseShopMotorcycle
{
    public function  __get($name) {
        switch (strtolower($name)) {
            case 'add_photo':
                return NULL;
            default:
                return parent::__get($name);
        }

    }

    /**
     * pretizeno o update apostrophe search indexu
     * @param Doctrine_Connection $conn
     */
    public function save(Doctrine_Connection $conn = null) {
        $conn = $conn ? $conn : $this->getTable()->getConnection();
        $conn->beginTransaction();
        try {
            $ret = aZendSearch::saveInDoctrineAndLucene($this, null, $conn);
            $conn->commit();
            return $ret;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * pretizeno o mazani relevantnich photos
     * @param Doctrine_Connection $conn
     */
    public function delete(Doctrine_Connection $conn = null) {
        $conn = $conn ? $conn : $this->getTable()->getConnection();
        $conn->beginTransaction();
        try {
            $photos = $this->getPhotos();
            foreach ($photos as $photo) {
                $photo->delete($conn);
            }
            $ret = aZendSearch::deleteFromDoctrineAndLucene($this, null, $conn);
            $conn->commit();
            return $ret;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @return int
     */
    public function getPriceWithTax()
    {
        return $this->getPrice() * (1 + sfConfig::get('app_tax_percent')/100);
    }

    /**
     * returns title photo image
     * @return Photo
     */
    public function getPhotoTitle()
    {
        return $this->getPhotosBounds()->getFirst()->getPhoto();
    }

    /**
     * @return Doctrine_Collection
     */
    public function getPhotosBounds()
    {
        return $this->getPhotosShopMotorcycle();
    }



    ### Lucene index required API:

    /**
     * updates Lucene indexes
     */
    public function updateLuceneIndex()
    {
        aZendSearch::updateLuceneIndex(array('object' => $this, 'culture' => aTools::getUserCulture(),
                                                'indexed' => $i = array(
                                                   'title' => $this->getName(),
                                                   'summary' => htmlspecialchars_decode($this->getDescription()),
                                                   'vendor' => $this->getVendor()->getName(),
                                                ),
                                       ));
/*$founds = aZendSearch::searchLuceneWithValues($this->getTable(), $q = 'enduro', aTools::getUserCulture());
var_dump($this->getTable()->getTableName());
var_dump($q);
var_dump(aTools::getUserCulture());
var_dump($founds);die(__FILE__ .'::'. __LINE__);*/
    }

    /**
     * DOCUMENT ME
     * @param mixed $conn
     * @return mixed
     */
    public function doctrineSave($conn)
    {
      return parent::save($conn);
    }

    /**
     * DOCUMENT ME
     * @param mixed $conn
     * @return mixed
     */
    public function doctrineDelete($conn)
    {
      return parent::delete($conn);
    }

    /**
     * returns global search string
     * @return string
     */
    public function getFullSearchText()
    {
      return implode(' ', array(
        $this->getName(),
        $this->getDescription(),
        $this->getVendor()->getName(),
      ));
    }
}
