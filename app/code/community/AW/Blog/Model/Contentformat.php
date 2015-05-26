<?php

class AW_Blog_Model_Contentformat {
    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
			
			$this->_options[] = array(
				'value' => 'blog_1_column',
				'label' => 'One Column',
			);
			
			$this->_options[] = array(
				'value' => 'blog_2_column',
				'label' => 'Two Column',
			);
			
			$this->_options[] = array(
				'value' => 'blog_3_column',
				'label' => 'Three Column',
			);
        }
		
        return $this->_options;
    }
}
