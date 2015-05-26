<?php
 class AmericanSwan_Sorting_Model_Sorting extends Mage_Core_Model_Abstract
 {
    protected $categories_to_run_for = array();
    protected $categories_priority = array();
    protected $weightage = array('category_weightage' => 0.5, 'weightage_time' => 0.3, 'weightage_simples' => 0.15, 'weightage_orders' => 0.05);
    protected $log_file;
    private $csv_data;
    public function _construct()
    { 
        parent::_construct();
        $this->log_file = 'product_sorting.log';
        $this->_init('sorting/sorting');
    }
    
    public function setLogFile($log_file) {
        if ($log_file != "") {
            $this->log_file = $log_file;
        }
    }
    
    public function setCategory($categoryId) {
        $categories_to_run_for = $this->categories_to_run_for;
        foreach($categories_to_run_for as $i => $val) {
            if ((int)$val == (int)$categoryId) {
                unset($categories_to_run_for[$i]);
            }
        }
        $categories_to_run_for[] = (int)$categoryId;
        $this->categories_to_run_for = $categories_to_run_for;
    }
    
    public function setCategoryPriority($categoryId) {
        $categories_priority = $this->categories_priority;
        foreach($categories_priority as $i => $val) {
            if ((int)$val == (int)$categoryId) {
                unset($categories_priority[$i]);
            }
        }
        $categories_priority[] = (int)$categoryId;
        $this->categories_priority = $categories_priority;
    }
    
    public function setWeightageCategory($weight) {
        $weightage = $this->weightage;
        $weightage['category_weightage'] = $weight;
        $this->weightage = $weightage;
    }
    
    public function setWeightageTime($weight) {
        $weightage = $this->weightage;
        $weightage['weightage_time'] = $weight;
        $this->weightage = $weightage;
    }
    
    public function setWeightageSimple($weight) {
        $weightage = $this->weightage;
        $weightage['weightage_simples'] = $weight;
        $this->weightage = $weightage;
    }
    
    public function setWeightageOrder($weight) {
        $weightage = $this->weightage;
        $weightage['weightage_orders'] = $weight;
        $this->weightage = $weightage;
    }
    
    public function productSorting() {
        $weightage = $this->weightage;
        Mage::log("Started script product sorting.", null, $this->log_file);
        Mage::log("", null, $this->log_file);
        //calculating total orders in past 24 hours
        $time = Mage::getModel('core/date')->timestamp(time());
        $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - 86400; // 60*60*24
        $from = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp($lastTime));
        $skuList = array();
        $csv_data = array();
        $csv_data_row_index = 0;
        
        if ($weightage['weightage_orders'] > 0) {
            $order_items = Mage::getResourceModel('sales/order_item_collection')
                ->addAttributeToSelect('product_id')
                ->addAttributeToSelect('product_type')
                ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
                ->addAttributeToSort('created_at', 'DESC')
                ->load();
            
            Mage::log("Fetching orders for 24 hrs.", null, $this->log_file);
            foreach($order_items as $order) {
                    $productType = $order->getData('product_type');
                    if($productType == "configurable") {
                            $sku = Mage::getModel('catalog/product')->load($order->getProductId())->getSku();
                            if(array_key_exists($sku,$skuList)) $skuList[$sku] = $skuList[$sku] + 1;
                            else $skuList[$sku] = 1;
                    }
            }
            Mage::log("Found ".count($skuList)." products being ordered in last 24 hrs.", null, $this->log_file);
            Mage::log("", null, $this->log_file);
        } else {
            Mage::log("Weightage order is zero, not fetchine 24 hrs orders.", null, $this->log_file);
        }

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $arrayCategoryIds = $this->categories_to_run_for;
        $categoryPriorities = $this->categories_priority;
        $r = $write->query("SELECT * FROM `eav_attribute` WHERE `attribute_code` = 'launch_date';");
        $attribute = $r->fetch();
        $attribute_id = $attribute['attribute_id'];
        
        foreach($arrayCategoryIds as $carPosition => $catId) {
            Mage::log("Re-setting positions for all products in category id #" . $catId, null, $this->log_file);
            $sql = "UPDATE catalog_category_product SET position = '0' WHERE category_id = '$catId'";
            $write->query($sql);

            Mage::log("", null, $this->log_file);
            Mage::log("Quering products for category id: #" . $catId, null, $this->log_file);

            $query = "SELECT e.sku, e.entity_id, c.category_id, e.created_at, cpe.value AS launch_date, IF((cpe.value IS NULL OR cpe.value <= 0), UNIX_TIMESTAMP(e.created_at), UNIX_TIMESTAMP(cpe.value)) AS launch_time, count( distinct pr.child_id ) AS totSimpleProd
                            FROM catalog_product_entity AS e
                            INNER JOIN catalog_category_product as c ON (e.entity_id = c.product_id)	
                            INNER JOIN catalog_product_relation AS pr ON ( e.entity_id = pr.parent_id )
                            INNER JOIN cataloginventory_stock_item AS st ON ( pr.child_id = st.product_id AND st.is_in_stock = '1' AND st.qty > 0 )
                            LEFT JOIN catalog_product_entity_varchar AS cpe ON ( cpe.entity_id = e.entity_id )
                            AND c.category_id = '$catId'
                            AND cpe.attribute_id =  '".$attribute_id."'
                            AND e.type_id = 'configurable'
                            GROUP BY e.sku
                            ORDER BY launch_time DESC";
            $result = $write->query($query); //eav_attribute, catalog_product_entity
            Mage::log("Found ".count($result)." products for category id #" . $catId, null, $this->log_file);

            $array_score = array();
            $array_formula_values = array();
            
            $max_time = 0;
            $min_time = time();
            $weightage_time = $weightage['weightage_time'];

            $max_simples = 0;
            $min_simples = 50;
            $weightage_simples = $weightage['weightage_simples'];

            $max_orders = 0;
            $min_orders = 1000;
            $weightage_orders = $weightage['weightage_orders'];

            $category_weightage = $weightage['category_weightage'];

            Mage::log("", null, $this->log_file);
            Mage::log("Formula to calculate score: ((Maximum - Actual) / Maximum) X 1000 X Weightage", null, $this->log_file);
            Mage::log("Weightage alloted: Category: " . $category_weightage . " (".($category_weightage * 1000)." per 1000)", null, $this->log_file);
            Mage::log("Weightage alloted: Days: " . $weightage_time . " (".($weightage_time * 1000)." per 1000)", null, $this->log_file);
            Mage::log("Weightage alloted: Sizes: " . $weightage_simples . " (".($weightage_simples * 1000)." per 1000)", null, $this->log_file);
            Mage::log("Weightage alloted: Orders: " . $weightage_orders . " (".($weightage_orders * 1000)." per 1000)", null, $this->log_file);
            Mage::log("", null, $this->log_file);

            while($data = $result->fetch()) {
                $clean_sku = str_replace("'","",$data['sku']);
                $pId = isset($data['entity_id']) ? $data['entity_id'] : 0;
                $ProdCatId = isset($data['category_id']) ? $data['category_id'] : 0;
                $time= isset($data['launch_time']) ? $data['launch_time'] : 0;
                $simple = isset($data['totSimpleProd']) ? $data['totSimpleProd'] : 0;
                $orderCount = isset($skuList['sku']) ? $skuList['sku'] : 0;

                if(!empty($clean_sku) && $pId > 0) {
                    if ($min_time > $time) {
                        $min_time = $time;
                    }
                    if ($max_time < $time) {
                        $max_time = $time;
                    }
                    if ($min_simples > $simple) {
                        $min_simples = $simple;
                    }
                    if ($max_simples < $simple) {
                        $max_simples = $simple;
                    }
                    if ($min_orders > $orderCount) {
                        $min_orders = $orderCount;
                    }
                    if ($max_orders < $orderCount) {
                        $max_orders = $orderCount;
                    }
                    $array_formula_values[$pId] = array(
                        'actual_time' => $time,
                        'actual_simples' => $simple,
                        'actual_orders' => $orderCount,
                        'sku' => $clean_sku
                    );
                }
            }
            
            foreach ($array_formula_values as $pId => $values) {
                Mage::log("", null, $this->log_file);

                $category_score = 0;
                $query = "SELECT * FROM catalog_category_product WHERE product_id = '".$pId."' AND category_id != '".$catId."';";
                $result_categories = $write->query($query);
                while($other_categories = $result_categories->fetch()) {
                    if (isset($other_categories['category_id'])) {
                        $category_position = -1;
                        foreach($categoryPriorities as $indx => $val) {
                            if ((int)$other_categories['category_id'] == (int)$val) {
                                $category_position = $indx;
                                break;
                            }
                        }
                        #$category_position = array_search($other_categories['category_id'], $categoryPriorities);
                        if ($category_position >= 0) {
                            $category_score = ((count($categoryPriorities) - $category_position) / count($categoryPriorities)) * 1000 * $category_weightage;
                            break;
                        } else {
                            Mage::log("Category #" . $other_categories['category_id'] ." is not a part of sortable categories.", null, $this->log_file);
                        }
                    } 
                }

                $score_time = (($max_time - $min_time) / (($max_time - $min_time) - $values['actual_time'])) * 1000 * $weightage_time;

                $score_simples = (($max_simples - $min_simples) / (($max_simples - $min_simples) - $values['actual_simples'])) * 1000 * $weightage_simples;

                $score_orders = (($max_orders - $min_orders) / (($max_orders - $min_orders) - $values['actual_orders'])) * 1000 * $weightage_simples;

                $score = $score_time + $score_simples + $score_orders + $category_score;

                $array_score[$pId] = round($score, 5);

                $csv_data[$csv_data_row_index] = array(
                    'Category Id' => $ProdCatId, 
                    'Other Category' => $categoryPriorities[$category_position],
                    'Priority Position' => isset($category_score) && $category_score != 0 && isset($category_position) ? $category_position : '',
                    'Priority Weightage' => $category_weightage,
                    'Priority Length' => count($categoryPriorities),
                    'Category Score' => $category_score,

                    'Product Id' => $pId,
                    'Product SKU' => $values['sku'],

                    'Launch Time' => date('Y-m-d H:i:s', $values['actual_time']),
                    'Launch Time Stamp' => $values['actual_time'],
                    'Launch Time Difference' => $values['actual_time'],
                    'Launch Time Minimum' => $min_time,
                    'Launch Time Maximum' => $max_time,
                    'Launch Time Difference' => $max_time - $min_time,
                    'Launch Time Weightage' => $weightage_time,
                    'Launch Score' => $score_time,

                    'Simple Count' => $values['actual_simples'],
                    'Simple Minimum' => $min_simples,
                    'Simple Maximum' => $max_simples,
                    'Simple Difference' => $max_simples - $min_simples,
                    'Simple Weightage' => $weightage_simples,
                    'Simple Score' => $score_simples,

                    'Order Count' => $values['actual_orders'],
                    'Order Minimum' => $min_orders,
                    'Order Maximum' => $max_orders,
                    'Order Difference' => $max_orders - $min_orders,
                    'Order Weightage' => $weightage_orders,
                    'Order Score' => $score_orders,

                    'Product Score' => $score,
                );
                $csv_data_row_index++;

                Mage::log("", null, $this->log_file);
            }
            $decimal_count = 0;
            foreach($array_score as $pId => $score) {
                $decimal = strlen(substr(strrchr($score, "."), 1));
                if ($decimal > $decimal_count) {
                    $decimal_count = $decimal;
                }
            }
            Mage::log("Maximum decimal counts: " . $decimal_count, null, $this->log_file);
            $m = 1;
            for($i = 1; $i <= $decimal_count; $i++) {
                    $m *= 10;
            }
            foreach($array_score as $pId => $score) {
                $score_int = $score * $m;
                Mage::log("Removing decimal for product id #" . $pId . ": " . $score_int ." = " . $score . " X " . $m . "", null, $this->log_file);
                $sql = "UPDATE catalog_category_product SET position = '".$score."' WHERE product_id ='".$pId."' AND category_id = '$catId'";
                Mage::log($sql, null, $this->log_file);
                $write->query($sql);
            }
            Mage::log("Total ".count($array_score)." products updated for category id #".$catId, null, $this->log_file);
            Mage::log("", null, $this->log_file);
            Mage::log("", null, $this->log_file);
        }

        //indexing
        Mage::log("Re-indexing started", null, $this->log_file);
        $process = Mage::getModel('index/process')->load('6');
        $process->reindexAll();
        Mage::log("Re-indexing end", null, $this->log_file);
        $this->csv_data = $csv_data;
    }
    
    public function getCSV() {
        $csv_data = $this->csv_data;
        if (count($csv_data) > 0) {
            $csv_file_path = Mage::getBaseDir('log') . "/product_sorting" . date('Y-m-d-H-i-s') . ".csv";
            Mage::log("Opening CSV file " . $csv_file_path, null, $this->log_file);

            $f = fopen($csv_file_path, "w") or die("Unable to open file!");

            $header = "";
            foreach($csv_data[0] as $head => $data) {
                $header .= ($header == "" ? "" : ",") . "\"" . $head . "\"";
            }
            fwrite($f, $header . "\n");

            foreach($csv_data as $row) {
                $line = "";
                foreach($row as $value) {
                    $line .= ($line == "" ? "" : ",") . "\"" . $value . "\"";
                }
                fwrite($f, $line . "\n");
            }
            fclose($f);
            Mage::log("Closed CSV file " . $csv_file_path, null, $this->log_file);
            return $csv_file_path;
        }
    }
 }
