<?php

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Exception\LocalizedException;

class ImageUploader extends Template
{
    protected Factory $elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array   $data = []
    )
    {
        parent::__construct($context, $data);

        $this->elementFactory = $elementFactory;
    }

    /**
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $config = $this->_getData('config');

        $sourceUrl = $this->getUrl('cms/wysiwyg_images/index', [
            'target_element_id' => $element->getId(),
            'type' => 'file'
        ]);

        /** @var Button $chooser */
        $chooser = $this->getLayout()->createBlock(Button::class);
        $chooser->setData([
            'type' => 'button',
            'class' => 'btn-chooser',
            'label' => $config['button']['open'],
            'on_click' => "MediabrowserUtility.openDialog('$sourceUrl', 0, 0, 'MediaBrowser', {})",
            'disabled' => $element->getReadonly()
        ]);

        /** @var Text $input */
        $input = $this->elementFactory->create('text', ['data' => $element->getData()])
            ->setId($element->getId())
            ->setForm($element->getForm())
            ->setData('class', 'widget-option input-text admin__control-text');

        if ($element->getData('required'))
            $input->addClass('required-entry');

        $element->setData(
            'after_element_html',
            $input->getElementHtml() . $chooser->toHtml() . "<script>require(['mage/adminhtml/browser']);</script>"
        );

        return $element;
    }
}
