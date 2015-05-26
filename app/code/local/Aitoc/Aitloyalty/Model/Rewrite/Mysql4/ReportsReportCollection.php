<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rewrite/Mysql4/ReportsReportCollection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ZSegBBZTWMZrRmUW('52a6e84a72b9eeb5208690300f0d572e'); ?><?php
/**
* @copyright  Copyright (c) 2011 AITOC, Inc.
*/
class Aitoc_Aitloyalty_Model_Rewrite_Mysql4_ReportsReportCollection extends Mage_Reports_Model_Mysql4_Report_Collection
{
    public function getIntervals()
    {        
        if (!$this->_intervals)
        {
            $this->_intervals = array();
            if (!$this->_from && !$this->_to){
                return $this->_intervals;
            }
            $dateStart  = new Zend_Date($this->_from);
            $dateEnd = new Zend_Date($this->_to);

            $t = array();
            $firstInterval = true;

            /** START AITOC FIX **/
            if (in_array((string) $this->_period, array('day', 'month', 'year')))
            {
            /** END AITOC FIX **/
                while ($dateStart->compare($dateEnd) <= 0) {
                    switch ($this->_period) {
                        case 'day' :
                            $t['title'] = $dateStart->toString(Mage::app()->getLocale()->getDateFormat());
                            $t['start'] = $dateStart->toString('yyyy-MM-dd HH:mm:ss');
                            $t['end'] = $dateStart->toString('yyyy-MM-dd 23:59:59');
                            $dateStart->addDay(1);
                            break;
                        case 'month':
                            $t['title'] =  $dateStart->toString('MM/yyyy');
                            $t['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
                                : $dateStart->toString('yyyy-MM-01 00:00:00');

                            $lastInterval = ($dateStart->compareMonth($dateEnd->getMonth()) == 0);

                            $t['end'] = ($lastInterval) ? $dateStart->setDay($dateEnd->getDay())
                                ->toString('yyyy-MM-dd 23:59:59')
                                : $dateStart->toString('yyyy-MM-'.date('t', $dateStart->getTimestamp()).' 23:59:59');

                            $dateStart->addMonth(1);

                            if ($dateStart->compareMonth($dateEnd->getMonth()) == 0) {
                                $dateStart->setDay(1);
                            }

                            $firstInterval = false;
                            break;
                        case 'year':
                            $t['title'] =  $dateStart->toString('yyyy');
                            $t['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
                                : $dateStart->toString('yyyy-01-01 00:00:00');

                            $lastInterval = ($dateStart->compareYear($dateEnd->getYear()) == 0);

                            $t['end'] = ($lastInterval) ? $dateStart->setMonth($dateEnd->getMonth())
                                ->setDay($dateEnd->getDay())->toString('yyyy-MM-dd 23:59:59')
                                : $dateStart->toString('yyyy-12-31 23:59:59');
                            $dateStart->addYear(1);

                            if ($dateStart->compareYear($dateEnd->getYear()) == 0) {
                                $dateStart->setMonth(1)->setDay(1);
                            }

                            $firstInterval = false;
                            break;
                    }
                    $this->_intervals[$t['title']] = $t;
                }
            /** START AITOC FIX **/            
            }
            /** END AITOC FIX **/
        }
        return  $this->_intervals;
    }
} } 