<?php

class Jextn_Testimonials_Block_Adminhtml_Testimonials_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('testimonials_form', array('legend'=>Mage::helper('testimonials')->__('Post Information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('testimonials')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
	  $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('testimonials')->__('Email'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
      ));
	$fieldset->addField('place', 'text', array(
          'label'     => Mage::helper('testimonials')->__('Place'),
		  'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'place',
      ));
      		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('testimonials')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('testimonials')->__('Approved'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('testimonials')->__('Pending'),
              ),
          ),
      ));
     $fieldset->addField('sidebar', 'select', array(
          'label'     => Mage::helper('testimonials')->__('Sidebar Display'),
          'name'      => 'sidebar',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('testimonials')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('testimonials')->__('No'),
              ),
          ),
      ));   

	 $fieldset->addField('footer', 'select', array(
          'label'     => Mage::helper('testimonials')->__('Footer Display'),
          'name'      => 'footer',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('testimonials')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('testimonials')->__('No'),
              ),
          ),
      ));
	  
	  $fieldset->addField('rating_product', 'radios', array(
          'label'     => Mage::helper('testimonials')->__('Ratings'),
          'name'      => 'rating_product',
          'onclick' => "",
          'onchange' => "",
          'values' => array(
                            array('value'=>'1','label'=>' Poor'),
                            array('value'=>'2','label'=>' Average'),
                            array('value'=>'3','label'=>' Good'),
							array('value'=>'4','label'=>' Excellent'),
                       ),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => ' <small class="accent"> [Product] </small>',
        ));
		
		$fieldset->addField('rating_service', 'radios', array(
          'name'      => 'rating_service',
          'onclick' => "",
          'onchange' => "",
          'values' => array(
                            array('value'=>'1','label'=>' Poor'),
                            array('value'=>'2','label'=>' Average'),
                            array('value'=>'3','label'=>' Good'),
							array('value'=>'4','label'=>' Excelent'),
                       ),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => ' <small class="accent"> [Customer service] </small>',
        ));
		
		$fieldset->addField('rating_brand', 'radios', array(
          'name'      => 'rating_brand',
          'onclick' => "",
          'onchange' => "",
          'values' => array(
                            array('value'=>'1','label'=>' Poor'),
                            array('value'=>'2','label'=>' Average'),
                            array('value'=>'3','label'=>' Good'),
							array('value'=>'4','label'=>' Excelent'),
                       ),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => ' <small class="accent"> [Brand experience] </small>',
        ));
	  
	   $fieldset->addField('rating_website', 'radios', array(
          'name'      => 'rating_website',
          'onclick' => "",
          'onchange' => "",
          'values' => array(
                            array('value'=>'1','label'=>' Poor'),
                            array('value'=>'2','label'=>' Average'),
                            array('value'=>'3','label'=>' Good'),
							array('value'=>'4','label'=>' Excelent'),
                       ),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => ' <small class="accent"> [Online experience] </small>',
        ));
	  
		
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('testimonials')->__('Testimonial Content'),
          'title'     => Mage::helper('testimonials')->__('Testimonial Content'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));

      if ( Mage::getSingleton('adminhtml/session')->getTestimonialsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTestimonialsData());
          Mage::getSingleton('adminhtml/session')->setTestimonialsData(null);
      } elseif ( Mage::registry('testimonials_data') ) {
          $form->setValues(Mage::registry('testimonials_data')->getData());
      }
      return parent::_prepareForm();
  }
}