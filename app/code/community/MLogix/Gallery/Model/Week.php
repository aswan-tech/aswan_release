<?php

/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

/**
 * SortHelperWeek is just a quick hack
 * to help with the "after_id"
 * prototype js tree ordering
 */
class SortHelperWeek {

    public $object;
    public $afters;

    function __construct($mainobj = 0) {
        $this->object = $mainobj;
        $this->afters = array();

        return $this;
    }

    public function getArray() {
        $arr = array();

        if ($this->object) {
            $arr[] = $this->object;

            foreach ($this->afters as &$obj) {
                $arr = array_merge($arr, $obj->getArray());
            }
        } else {
            foreach ($this->afters as &$obj) {
                // put parent=0's at the top
                if (!$obj->object->getAfterId())
                    $arr = array_merge($arr, $obj->getArray());
            }

            foreach ($this->afters as &$obj) {
                if ($obj->object->getAfterId())
                    $arr = array_merge($arr, $obj->getArray());
            }
        }

        return $arr;
    }

    public function addAfter($sorthelper) {

        if ($sorthelper->object) {
            if ($this->object && $sorthelper->object->getAfterId() == $this->object->getId() || (!$this->object && !$sorthelper->object->getAfterId())) {
                $this->afters[] = $sorthelper;

                return true;
            } else {
                foreach ($this->afters as &$after) {
                    if ($after->addAfter($sorthelper))
                        return true;
                }
            }
        }
        return false;
    }

}

class MLogix_Gallery_Model_Week extends Mage_Core_Model_Abstract {

	const STATUS_ENABLED = 1;
		 
    public function _construct() {
        parent::_construct();
        $this->_init('gallery/week');

        $path = Mage::getBaseDir('media') . DS . 'gallery';
        $thumbpath = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'thumbs';

        if (!file_exists($path))
            mkdir($path);
        if (!file_exists($thumbpath))
            mkdir($thumbpath);
    }

    public function getMediaUrl() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'gallery/';
    }

    public function isActive() {
        return ($this->getStatus() == 2 ? 0 : 1);
    }

    public function getTrendsChildren() {
        $id = ($this->getId() ? $this->getId() : 0);


        if (is_array($this->children))
            return $this->children;

        $this->children = array();

        $collection = Mage::getModel('gallery/week')->getCollection();
        //$collection->addFieldToFilter('status',1);

        $collection->addFieldToFilter('parent_id', $id);

        //Show recently added item at the top
        //$collection->setOrder('position_no', 'ASC');
        $collection->setOrder('created_time', 'DESC');

        $sq = $collection->load();


        foreach ($collection as &$child) {
            $child->parent = $this;
            $child->load($child->getId(), null, true);
        }

        $this->children = $collection;

        $this->sortChildren();


        return $this->children;
    }

    public function getPath() {
        if (!$this->parent)
            return '0';
        else
            return $this->parent->getPath() . '/' . $this->getId();
    }

    public function getJsonArray() {
        $me = array();

        $me['text'] = ($this->getId() ? $this->getTitle() : 'Root');
        $me['id'] = ($this->getId() ? $this->getId() : 2);
        $me['store'] = 0;
        $me['path'] = $this->getPath();

        $me['cls'] = ($this->hasChildren() ? 'folder' : 'leaf');
        $me['allowDrop'] = false;
        $me['allowDrag'] = false;

        $me['category_id'] = ($this->getCategoryId() ? $this->getCategoryId() : 0);
        $me['after_id'] = ($this->getAfterId() ? $this->getAfterId() : 0);
        $me['expanded'] = 1;

        $me['children'] = array();

        if (count($this->children))
            foreach ($this->children as $child)
                $me['children'][] = $child->getJsonArray();

        if ($this->getId())
            return $me;

        else
            return $me['children'];
    }

    public function getSimpleArray() {

        $me = array();
        $me['id'] = $this->getId();
        $me['after'] = ($this->after && $this->after->getId() ? $this->after->getId() : 0);
        $me['parent'] = ($this->parent && $this->parent->getId() ? $this->parent->getId() : 0);
        $me['title'] = $this->getTitle();
        $me['tier'] = $this->getTier();

        if ($this->children && count($this->children)) {
            $me['children'] = array();

            foreach ($this->children as $child) {
                $newchild = $child->getSimpleArray();
                if ($newchild)
                    $me['children'][] = $newchild;
            }
        }

        if ($this->getId())
            return $me;
        else
            return $me['children'];
    }

    public function array_flatten($array, $return) {
        if (is_array($array))
            foreach ($array as $item) {
                if (isset($item['children']))
                    $children = $item['children'];
                else
                    $children = array();

                unset($item['children']);

                $return[] = $item;

                foreach ($children as $child) {
                    $return = $this->array_flatten(array($child), $return);
                }
            }
        return $return;
    }

    public function getTier() {
        if (!$this->parent)
            return 0;

        return ($this->parent->getTier() + 1);
    }

    /**
     * Shifts the array right, starting at position $position
     * and inserts $value at $position
     *
     * @param unknown_type $array
     * @param unknown_type $position
     * @param unknown_type $value
     */
    public function arrayInsert(&$array, $position, $value) {
        if ($position == count($array)) {
            $array[] = $value;
            return;
        }

        $firsthalf = array_slice($array, 0, $position);
        $secondhalf = array_slice($array, $position, count($array) - count($firsthalf));

        $array = array_merge($firsthalf, array($value), $secondhalf);
    }

    public function arrayDelete(&$array, $position) {
        if ($position >= count($array))
            return -1;

        $deletedItem = $array[$position];

        $firsthalf = array_slice($array, 0, $position);
        if ($position < count($array) - 1) {
            $secondhalf = array_slice($array, $position + 1, count($array) - count($firsthalf) - 1);
            $array = array_merge($firsthalf, $secondhalf);
        }
        else
            $array = $firsthalf;

        return $deletedItem;
    }

    public function arrayFindId($array, $id) {
        foreach ($array as $key => $item)
            if ($item->getId() == $id)
                return $key;
        return -1;
    }

    public function insertAfter(&$array, $after_id, $value) {
        $keypos = $this->arrayFindId($array, $after_id);

        if ($keypos < 0) {
            return 0;
        }

        $this->arrayInsert($array, $keypos + 1, $value);

        return 1;
    }

    public function validateChildrenAfterIds() {
        // Validate+fix after_id's.. just in case the id's get screwed up somehow
        foreach ($this->children as $child)
            if ($child->getAfterId() && !$child->getSibling($child->getAfterId())) {
                $child->setAfterId(0);
                $child->save();
            }
    }

    public function sortChildren() {
        $this->validateChildrenAfterIds();

        foreach ($this->children as &$child)
            $child->after = $child->getSibling($child->getAfterId());

        $sorthelpers = array();
        foreach ($this->children as &$child)
            $sorthelpers[] = new SortHelperWeek($child);

        $limit = count($this->children);

        $z = 0;
        while ($z++ < $limit && count($this->children)) {
            $x = array_pop($sorthelpers);
            $go = 1;
            foreach ($sorthelpers as &$sorthelper) {

                if ($go) {
                    if ($sorthelper->addAfter($x))
                        $go = 0;
                }
            }
            if ($go) {
                $this->arrayInsert($sorthelpers, 0, $x);
            }
        }

        $sortedarray = array();

        foreach ($sorthelpers as &$helper) {
            $sortedarray = array_merge($sortedarray, $helper->getArray());
        }

        $this->children = $sortedarray;
    }

    public function getSibling($id) {
        if (!$this->getId())
            return 0;
        if (!$this->parent)
            return 0;

        return $this->parent->getChild($id);
    }

    public function getChild($id) {
        foreach ($this->children as &$child) {
            if ($child->getId() == $id)
                return $child;
        }

        return 0;
    }

    public function initTree() {
        //unused
    }

    public function load($id, $field=null, $tree=false) {
        parent::load($id, $field);

        if ($this->getId() && $this->getId() == $this->getParentId()) {
            $this->setParentId(0);
            $this->parent = 0;
            $this->save();
        }

        if ($this->getId() && $this->getId() == $this->getAfterId()) {
            $this->setAfterId(0);
            $this->save();
        }

        if (!$this->getId() || $tree)
            $this->getTrendsChildren();




        return $this;
    }

    /**
     * Returns an array of active categories
     * Default behavior is to nest the array by parent->children relationship
     * @return array
     */
    public function getCategories($node = 0, $json = 1) {
        $root = Mage::getModel('gallery/week')->load(0);

        if ($json) {
            $ar = $root->getJsonArray();

            return $ar;
        }

        $rawcategories = $this->getCollection()->addFieldToFilter('status', '1')->toArray();

        $rawcategories = $rawcategories['items'];

        $categories = array();

        if ($node)
            return $categories;

        foreach ($rawcategories as $category) {
            $id = $category['gallery_id'];
            $categories[$id] = $category;
        }



        return $categories;
    }

    /**
     * Moves this category to a new parent and/or repositions it     
     *
     * @param int $newParentId the parent's gallery_id
     * @param int $afterId the Id of the category this appears after (for sorting)
     * @param int $categoryId Used if the category isn't loaded yet (with ->load($id))
     * @return string Response message (for json)
     */
    public function move($newParentId, $afterId, $categoryId=0) {
        if ($categoryId)
            $this->load($categoryId);
        else
            $categoryId = $this->getCategoryId();

        try {
            $newParentId = (int) $newParentId;
            $categoryId = (int) $categoryId;
            $afterId = (int) $afterId;
            $prevAfterId = (int) $this->getAfterId();

            $tableName = $this->getResource()->getTable('gallery/galleryweek');

            $write = Mage::getSingleton('core/resource')->getConnection('core_write');

            $write->raw_query("update $tableName set after_id = $prevAfterId where after_id = $categoryId");
            $write->raw_query("update $tableName set after_id = $categoryId where after_id = $afterId and parent_id = $newParentId and gallery_id != $categoryId");

            $this->setParentId($newParentId);
            $this->setAfterId($afterId);
            $this->save();

            return "SUCCESS";
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
            //return Mage::helper('catalog')->__('Category move error');
        }
    }

    public function createthumb($name,$filename,$new_w,$new_h,$forcepng=false){
		$system=explode('.',$name);
		$ext = $system[count($system)-1];
		
		if (preg_match('/jpg|jpeg/i',$ext)){
			$src_img=imagecreatefromjpeg($name);
			$fileType = "jpg";
		}
		if (preg_match('/png/i',$ext)){
			$src_img=imagecreatefrompng($name);
			$fileType = "png";
		}
		
		if (preg_match('/gif/i',$ext)){
			$src_img=imagecreatefromgif($name);
			$fileType = "gif";
		}
		
		// define coordinates of image inside new frame
        $srcX = 0;
        $srcY = 0;
        $dstX = 0;
        $dstY = 0;
		
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
				
		$thumb_w=$new_w;
		$thumb_h=$new_h;
		
		//**************************************************************//
		// do not make picture bigger, than it is, if required
		if (($new_w >= $old_x) && ($new_h >= $old_y)) {
			$thumb_w  = $old_x;
			$thumb_h = $old_y;
		}
		
		// keep aspect ratio
		if ($old_x / $old_y >= $new_w / $new_w) {
			$thumb_h = round(($thumb_w / $old_x) * $old_y);
		} else {
			$thumb_w = round(($thumb_h / $old_y) * $old_x);
		}
			
		// define position in center (TODO: add positions option)
        $dstY = round(($new_h - $thumb_h) / 2);
        $dstX = round(($new_w - $thumb_w) / 2);
		
		// create new image
        $isAlpha     = false;
        $isTrueColor = false;

		//*************************************************************//
		$transparentIndex = $this->_getTransparency($this->_imageHandler, $fileType, $isAlpha, $isTrueColor);
		
		if ($isTrueColor) {
			$dst_img = imagecreatetruecolor($new_w,$new_h);
		} else {
			$dst_img = imagecreate($new_w,$new_h);
		}
		
		try {
			// fill truecolor png with alpha transparency
			if ($isAlpha) {

				if (!imagealphablending($dst_img, false)) {
					throw new Exception('Failed to set alpha blending for PNG image.');
				}
				$transparentAlphaColor = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
				if (false === $transparentAlphaColor) {
					throw new Exception('Failed to allocate alpha transparency for PNG image.');
				}
				if (!imagefill($dst_img, 0, 0, $transparentAlphaColor)) {
					throw new Exception('Failed to fill PNG image with alpha transparency.');
				}
				if (!imagesavealpha($dst_img, true)) {
					throw new Exception('Failed to save alpha transparency into PNG image.');
				}
			}
			// fill image with indexed non-alpha transparency
			elseif (false !== $transparentIndex) {
							
				$transparentColor = false;
				if ($transparentIndex >=0 && $transparentIndex <= imagecolorstotal($this->_imageHandler)) {
					list($r, $g, $b)  = array_values(imagecolorsforindex($this->_imageHandler, $transparentIndex));
					$transparentColor = imagecolorallocate($dst_img, $r, $g, $b);
				}
				if (false === $transparentColor) {
					throw new Exception('Failed to allocate transparent color for image.');
				}
				if (!imagefill($dst_img, 0, 0, $transparentColor)) {
					throw new Exception('Failed to fill image with transparency.');
				}
				imagecolortransparent($dst_img, $transparentColor);
			} else {
				$color = imagecolorallocate($dst_img, 157, 156, 156);
				if (!imagefill($dst_img, 0, 0, $color)) {
					throw new Exception("Failed to fill image background with color {157} {156} {156}.");
				}
			}
		}
		catch (Exception $e) {
			// fallback to default background color
		}
		
		imagecopyresampled($dst_img,$src_img,$dstX,$dstY,$srcX,$srcY,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		if ($forcepng||preg_match("/png/i",$system[1]))
		{
			imagepng($dst_img,$filename); 
		} else {
			imagejpeg($dst_img,$filename); 
		}
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	} 
	
	/**
     * Gives true for a PNG with alpha, false otherwise
     *
     * @param string $fileName
     * @return boolean
     */

    public function checkAlpha($fileName)
    {
        return ((ord(file_get_contents($fileName, false, null, 25, 1)) & 6) & 4) == 4;
    }

    private function _getTransparency($imageResource, $fileType, &$isAlpha = false, &$isTrueColor = false)
    {
        $isAlpha     = false;
        $isTrueColor = false;
        // assume that transparency is supported by gif/png only
        if (("gif" === $fileType) || ("png" === $fileType)) {
            // check for specific transparent color
            $transparentIndex = imagecolortransparent($imageResource);
            if ($transparentIndex >= 0) {
                return $transparentIndex;
            }
            // assume that truecolor PNG has transparency
            elseif ("png" === $fileType) {
                $isAlpha     = $this->checkAlpha($this->_fileName);
                $isTrueColor = true;
                return $transparentIndex; // -1
            }
        }
        if ("jpg" === $fileType) {
            $isTrueColor = true;
        }
        return false;
    }

    public function getThumbnail($width=144, $height=193) {
        $thumbname = $this->makeThumbnail($width, $height);

        return $this->getMediaUrl() . 'thumbs/' . $thumbname;
    }

    public function makeThumbnail($width, $height, $refresh=false) {
        $forcepng = true;

        $filename = $this->getFilename();
        $thumbname = $width . '_' . $height . '_' . $filename;
        //$thumbname = $filename;

        if ($forcepng)
            $thumbname = preg_replace("/\.[^\.]+$/", ".png", $thumbname);

        if (!$this->getId() || !$filename)
            return '';

        $path = Mage::getBaseDir('media') . DS . 'gallery' . DS;
        $thumbpath = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'thumbs' . DS;

        $file = $path . $filename;
        $thumb = $thumbpath . $thumbname;



        //return '';
        if (!$refresh && file_exists($thumb))
            return $thumbname;

        $this->createthumb($file, $thumb, $width, $height, $forcepng);

        return $thumbname;
    }

    public function getGallery($parent=0) {
        $items = $this->getCollection()
                        ->addFieldToFilter('status', '1')
                        ->addFieldToFilter('parent_id', $parent);
        return $items;
    }

    public function getLookByItemTitle($title) {
        $items = $this->getCollection()
                        ->addFieldToFilter('item_title', array('eq' => $title))
                        ->addFieldToFilter('status', '1')
                        ->setOrder('position_no', 'ASC');
        $items->getSelect();
        return $items->getFirstItem();
    }

    public function getImageUrl() {
        return $this->getMediaUrl() . $this->getFilename();
    }

    public function getThumbUrl() {
        $path = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'thumbs' . DS;
        $thumbname = preg_replace("/\.[^\.]+$/", ".png", $this->getFilename());

        if (file_exists($path . $thumbname)) {
            return $this->getMediaUrl() . 'thumbs/' . $thumbname;
        }

        return $this->getMediaUrl() . $this->getFilename();
    }

    public function getArchiveThumbUrl() {
        Mage::getStoreConfig('gallery/lookoftheweek/archivethumbwidth') ? $archiveWidth = Mage::getStoreConfig('gallery/lookoftheweek/archivethumbwidth') : $archiveWidth = 144;
        Mage::getStoreConfig('gallery/lookoftheweek/archivethumbheight') ? $archiveHeight = Mage::getStoreConfig('gallery/lookoftheweek/archivethumbheight') : $archiveHeight = 193;

        $path = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'thumbs' . DS;
        $thumbname = preg_replace("/\.[^\.]+$/", ".png", $archiveWidth . '_' . $archiveHeight . '_' . $this->getFilename());

        if (file_exists($path . $thumbname)) {
            return $this->getMediaUrl() . 'thumbs/' . $thumbname;
        }

        return $this->getMediaUrl() . $this->getFilename();
    }

    public function getLookImageUrl() {
        Mage::getStoreConfig('gallery/lookoftheweek/imagewidth') ? $width = Mage::getStoreConfig('gallery/lookoftheweek/imagewidth') : $width = 659;
        Mage::getStoreConfig('gallery/lookoftheweek/imageheight') ? $height = Mage::getStoreConfig('gallery/lookoftheweek/imageheight') : $height = 731;

        $path = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'thumbs' . DS;
        $thumbname = preg_replace("/\.[^\.]+$/", ".png", $width . '_' . $height . '_' . $this->getFilename());

        if (file_exists($path . $thumbname)) {
            return $this->getMediaUrl() . 'thumbs/' . $thumbname;
        }

        return $this->getMediaUrl() . $this->getFilename();
    }

    public function getBreadcrumbPath() {
        $resource = $this->getResource();
        $read = $resource->getReadConnection();
        $table = $resource->getMainTable();

        $path = array();

        //if($this->getId() > 0)
        //	$path[] = $this;

        $parent = $this->getParentId();

        $count = 0;
        while ($parent != 0) {
            // An infinite loop shouldn't happen, but just in case
            if ($count++ > 100) {
                echo 'Error in Model/Gallery';
                die();
            }

            $sql = "SELECT gallery_id, parent_id FROM " . $table . " where gallery_id = ?";
            //echo $sql;
            //die();
            $stmt = $read->query($sql, $parent);

            if ($row = $stmt->fetch()) {
                $path[] = Mage::getModel('gallery/gallery')->load($row['gallery_id']);
                $parent = $row['parent_id'];
            }
        }

        return array_reverse($path);
    }

    public function hasChildren() {
        $resource = $this->getResource();
        $read = $resource->getReadConnection();
        $table = $resource->getMainTable();

        $sql = "SELECT gallery_id, parent_id FROM " . $table . " where parent_id = ?";
        //echo $sql;
        //die();
        $stmt = $read->query($sql, $this->getId());

        if ($row = $stmt->fetch()) {
            return true;
        }

        return false;
    }

    public function getDescription() {
        //return str_replace("\"","%22",parent::getDescription());
        return parent::getDescription();
    }

    public function getTitle() {
        return str_replace("\"", "%22", parent::getData('item_title'));
    }

    public function getAlt() {
        return str_replace("\"", "%22", parent::getAlt());
    }

    public function getCategoryId() {
        return $this->getGalleryId();
    }

    public function loadByItemTitle($v) {
        return $this->load($v, 'item_title');
    }
	
	public function getDefaultSearchedResult($term){
		$collection = $this->getCollection();
				
		$collection->addFieldToSelect(array("gallery_id","parent_id"));
		
		$collection->addPresentFilter();
		
		$collection->addEnableFilter(array('in' => $this->getEnabledStatusIds()));
		
		$collection->addContentFilter($term);
				
		return $collection;
	}
	
	public function getFinalSearchedResult($currentPage,$looksIds){
		$collection = $this->getCollection();
		
		$ids_string = implode(',',$looksIds);
		
		$collection->addFieldToFilter('`main_table`.`gallery_id`',array('in'=>$looksIds));
				
		$collection->addFieldToSelect(array("gallery_id","parent_id"));
		
		$collection->getSelect()->order(new Zend_Db_Expr("FIELD(`main_table`.`gallery_id`,$ids_string)"));
		
		$collection->addPresentFilter();
		
		$collection->addEnableFilter(array('in' => $this->getEnabledStatusIds()));
			
		$collection->setCurPage($currentPage);
		
		$looks_tobe_displayed = (int) Mage::getStoreConfig('gallery/lookoftheweek/looksperpage');
		$collection->setPageSize($looks_tobe_displayed);
				
		return $collection;
	}
	
	public function getSearchedResultPager($term,$currentPage,$looksIds){
		$collection = $this->getCollection();
		
		$collection->addFieldToFilter('`main_table`.`gallery_id`',array('in'=>$looksIds));
					
		$collection->addFieldToSelect(array("gallery_id","parent_id"));
		
		$collection->addPresentFilter();
		
		$collection->addEnableFilter(array('in' => $this->getEnabledStatusIds()));
		
		$collection->addContentFilter($term);
		
		$collection->setCurPage($currentPage);
		
		return $collection;
	}
	
	public function getSearchedResultCount($term){
		$collection = $this->getCollection();
				
		$collection->addFieldToSelect(array("gallery_id","parent_id"));
		
		$collection->addPresentFilter();
		
		$collection->addEnableFilter(array('in' => $this->getEnabledStatusIds()));
		
		$collection->addContentFilter($term);
		
		return $collection;
	}
	
	 public function getEnabledStatusIds() {
        return array(self::STATUS_ENABLED);
    }

}