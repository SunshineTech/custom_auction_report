<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Auction\Controller\Index;

use Magestore\Auction\Model\Auction;

/**
 *
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magestore\Auction\Model\ResourceModel\Bid\CollectionFactory
     */
    protected $_auctionFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;
    /**
     * Update constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magestore\Auction\Model\AuctionFactory $auctionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magestore\Auction\Model\AuctionFactory $auctionFactory,
        \Magento\Customer\Model\Session $seccion
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_auctionFactory = $auctionFactory;
        $this->session = $seccion;
    }

    /**
     * Execute action
     * @var \Magestore\Auction\Model\Auction $aution
     */
    public function execute(){
        $auction_Id = $this->getRequest()->getParam('id');
        $result = $this->_resultJsonFactory->create();
        $auction = $this->_auctionFactory->create()->load($auction_Id);

        if($auction->getId())
            $result->setData([
                'success' => '1',
                'current_price' => $auction->getCurrentPriceText(),
                'min_next_price' => $auction->getMinNextPrice(),
                'current_bidder_name' => $auction->getCurrentBidderName(),
                'total_bid' => __('%1 bids', $auction->getTotalBids()),
                'time_left' => $auction->getTimeLeft(),
                'end_time'  => $auction->getLocaleEndTime(),
                'suggess' => $this->getSuggess($auction),
                'logged_in' => $this->session->isLoggedIn()
            ]);
        else
            $result->setData([]);
        return $result;
    }

    /**
     * @param \Magestore\Auction\Model\Auction $auction
     * @return string
     */
    public function getSuggess($auction){
        if($auction->getTotalBids()){
            return $auction->getMaxNextPrice()?__('Your bid must be greater than %1 and less than %2',$auction->getMinNextPriceText(),$auction->getMaxNextPriceText()):__('Your bid must be greater than %1',$auction->getMinNextPriceText());
        }
        return $auction->getMaxNextPrice()?__('Your bid must be equal or greater than %1 and less than %2',$auction->getMinNextPriceText(),$auction->getMaxNextPriceText()):__('Your bid must be equal or greater than %1',$auction->getMinNextPriceText());;
    }
}