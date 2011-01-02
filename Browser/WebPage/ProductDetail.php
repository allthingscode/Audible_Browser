<?php
require_once 'Audible/Browser/WebPage.php';
require_once 'Audible/Browser/WebPage/ProductDetail/AudioBook.php';
require_once 'Audible/Exception/ProductUnavailable.php';

/**
 * @package Audible
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Audible_Browser_WebPage_ProductDetail extends Audible_Browser_WebPage
{
    // All properties are stored in the parent class.
    // ------------------------------------------------------------------------


    /**
     * @param Audible_Browser_CurlSession
     */
    public function __construct( Audible_Browser &$browser )
    {
        $this->_setProductDetailUrl( 'http://www.audible.com/pd'          );
        $this->setReferringUrl( 'http://www.audible.com/browseaudio' );

        // Call the parent constructor
        parent::__construct( $browser );
    }



    // ----- Setters/Getters --------------------------------------------------

    /**
     * @param string
     */
    private function _setProductDetailUrl( $newValue )
    {
        $this->_properties['ProductDetailUrl'] = $newValue;
    }
    /**
     * @return string
     */
    public function getProductDetailUrl()
    {
        return $this->_properties['ProductDetailUrl'];
    }


    /**
     * @param string
     */
    public function setReferringUrl( $newValue )
    {
        $this->_properties['ReferringUrl'] = $newValue;
    }
    /**
     * @return string
     */
    public function getReferringUrl()
    {
        return $this->_properties['ReferringUrl'];
    }


    /**
     * @param Audible_Browser_WebPage_ProductDetail_AudioBook
     */
    private function _setAudioBook( Audible_Browser_WebPage_ProductDetail_AudioBook $newValue )
    {
        $this->_properties['AudioBook'] = $newValue;
    }
    /**
     * @return Audible_Browser_WebPage_ProductDetail_AudioBook
     */
    public function getAudioBook()
    {
        return $this->_properties['AudioBook'];
    }

    // ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------

    /**
     * @param string
     */
    public function loadFromAsin( $asin )
    {
        $productDetailHtml = $this->_getProductDetailHtmlFromAsinWithRetries( $asin );

        $audioBook = new Audible_Browser_WebPage_ProductDetail_AudioBook();
        $audioBook->load( $productDetailHtml );

        $this->_setAudioBook( $audioBook );
    }
    // ------------------------------------------------------------------------



    // ----- Private Methods --------------------------------------------------

    /**
     * This is needed to increase stability.
     * For some reason, random product detail loads fail.
     * I assume this behavior would be no different in a browser.
     * Perhaps it happens more with this script because it hammers the audible web server.
     * @param string
     */
    private function _getProductDetailHtmlFromAsinWithRetries( $asin, $maxTryCount = 3 )
    {
        $currentTryCount = 0;

        while ( $currentTryCount < $maxTryCount ) {

            $exceptionOccured = false;
            try {
                $currentTryCount++;
                $productDetailHtml = $this->_getProductDetailHtmlFromAsin( $asin );

            } catch ( Exception_ProductUnavailable $exception ) {

                throw $exception;

            } catch ( Exception $exception ) {

                // Swallow the exception.  We'll loop back and try again.
                $exceptionOccured = true;
            }

            // If we successfully loaded the details, then no need to keep trying
            if ( false === $exceptionOccured ) {
                break;
            }
        }

        // If we tried the max number of times and never loaded the html,
        //   then throw the last exception.
        if ( $currentTryCount === $maxTryCount ) {
            if ( true === $exceptionOccured ) {
                throw $exception;
            }
        }

        return $productDetailHtml;
    }

    /**
     * @param string
     */
    private function _getProductDetailHtmlFromAsin( $asin )
    {
        $browser     = $this->_getBrowser();
        $curlSession = $browser->getCurlSession();

        $postHeaders = array(
            'Referer: '    . $this->getReferringUrl(),
            'User-Agent: ' . $browser->getUserAgent()
            );
        $curlOptions = array(
            CURLOPT_HTTPHEADER     => $postHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST           => false,
            CURLOPT_HTTPGET        => true,
            CURLOPT_HEADER         => false,
            CURLOPT_URL            => $this->getProductDetailUrl() . '?asin=' . $asin
            );
        if( false === curl_setopt_array( $curlSession->getHandle(), $curlOptions ) ) {
            throw new Exception( 'Unable to set curl options' );
        }

        $productDetailHtml = curl_exec( $curlSession->getHandle() );
        if ( false === $productDetailHtml ) {
            throw new Exception( 'Curl error while retrieving product detail page html:  ' . curl_error( $curlSession->getHandle() ) );
        }

        // Make sure we are actually on a valid product detail page
        if ( true === $this->_isProductNotAvailablePage( $productDetailHtml ) ) {
            throw new Exception_ProductUnavailable();
        }
        if ( false === strpos( $productDetailHtml, '<div class="adbl-prod-detail-cont">' ) ) {
            throw new Exception( 'Unrecognized audible product detail page.' );
        }

        return $productDetailHtml;
    }

    /**
     * @param string
     */
    private function _isProductNotAvailablePage( $productDetailHtml )
    {
        if ( false === strpos( $productDetailHtml, '<h1>Product Not Available</h1>' ) ) {
            return false;
        }
        return true;
    }
    // ------------------------------------------------------------------------
}

