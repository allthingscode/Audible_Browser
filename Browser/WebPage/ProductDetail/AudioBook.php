<?php
require_once 'Audible/Product.php';

/**
 * @package Audible
 */
class Audible_Browser_WebPage_ProductDetail_AudioBook extends Audible_Product_AudioBook
{
    // All properties are stored in the parent Audible_Product_AudioBook class.
    // ------------------------------------------------------------------------


    // ----- Setters/Getters --------------------------------------------------
    // Most of the property setters/getters
    //   are located in the Audible_Product_AudioBook class.
    // These properties are just the ones that
    //   only come from the product detail page.



    /**
     * @param string
     */
    private function _setReleaseDate( $newValue )
    {
        if ( 0 === preg_match( '/\d{2}-\d{2}-\d{2}/', $newValue ) ) {
            throw new Exception( 'Invalid release date value: ' . $newValue );
        }
        $newValue = str_replace( '-', '/', $newValue );
        $newValue = date( 'Y-m-d', strtotime( $newValue ) );
        $this->_properties['ReleaseDate'] = $newValue;
    }
    /**
     * @return string
     */
    public function getReleaseDate()
    {
        return $this->_properties['ReleaseDate'];
    }


    /**
     * @param string
     */
    private function _setVersion( $newValue )
    {
        $this->_properties['Version'] = trim( $newValue );
    }
    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_properties['Version'];
    }


    /**
     * @param float
     */
    private function _setRegularPrice( $newValue )
    {
        $this->_properties['RegularPrice'] = trim( $newValue );
    }
    /**
     * @return float
     */
    public function getRegularPrice()
    {
        return $this->_properties['RegularPrice'];
    }


    /**
     * @param string
     */
    private function _setPublisher( $newValue )
    {
        $this->_properties['Publisher'] = trim( $newValue );
    }
    /**
     * @return string
     */
    public function getPublisher()
    {
        return $this->_properties['Publisher'];
    }


    /**
     * @param int
     */
    private function _setCustomerRatingsCount( $newValue )
    {
        $this->_properties['CustomerRatingsCount'] = trim( $newValue );
    }
    /**
     * @return int
     */
    public function getCustomerRatingsCount()
    {
        return $this->_properties['CustomerRatingsCount'];
    }
    // ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------

    /**
     * @param string
     */
    public function load( $productDetailHtml )
    {
        // Make sure we are starting with a blank slate.
        $this->_reset();



        //echo htmlspecialchars( $productDetailHtml );
        //return;



        // Getting rid of new-line chars makes parsing easier
        $productDetailHtml = str_replace( array( "\n", "\r" ), '', $productDetailHtml );

        // See if this product is in My Library
        // @TODO Determine if we are logged in so we can set this property definitively
        if ( false !== strpos( $productDetailHtml, 'This audio is available in <a class="adbl-link" href="/lib">My Library' ) ) {
            $this->_setIsInMyLibrary( true );
        }

        // Parse the asin
        $pregMatches = array();
        $matchCount = preg_match( '/asin=([0-9a-z]+)/i', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the asin from the product view html.' );
        }
        $this->_setAsin( $pregMatches[1] );


        // Parse the title
        $pregMatches = array();
        $matchCount = preg_match( '/<li class="adbl-prod-title adbl-label">([^<]+)<\/li>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product title from the product view html.' );
        }
        $this->_setTitle( $pregMatches[1] );

        // Parse the version
        $pregMatches = array();

        $matchCount = preg_match( '/<li class="adbl-prod-version">([^<]+)<\/li>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product version from the product view html.' );
        }
        $this->_setVersion( $pregMatches[1] );

        // Parse the author/s
        $pregMatches = array();
        $matchCount = preg_match_all( '/<a class="adbl-link" href="\/search\?searchAuthor=[^"]+">[^<]*<span>([^<]+)<\/span>[^<]*<\/a>/', $productDetailHtml, $pregMatches );
        if ( $matchCount < 1 ) {
            throw new Exception( 'Unable to locate any product author/s from the product view html.' );
        }
        foreach( $pregMatches[1] as $author ) {
            $this->_appendAuthor( $author );
        }

        // Parse the Narrated-By/s
        $pregMatches = array();
        $matchCount = preg_match_all( '/<a class="adbl-link" href="\/search\?searchNarrator=[^"]+">[^<]*<span>([^<]+)<\/span>[^<]*<\/a>/', $productDetailHtml, $pregMatches );
        if ( $matchCount < 1 ) {
            throw new Exception( 'Unable to locate any product narrator/s from the product view html.' );
        }
        foreach( $pregMatches[1] as $narrator ) {
            $this->_appendNarrator( $narrator );
        }

        // Parse the regular price
        $pregMatches = array();
        $matchCount = preg_match( '/<li class="adbl-reg-price-cont"><div class="adbl-price-item"><span class="adbl-label">Regular Price:<\/span><span class="adbl-price-content"><span class="adbl-reg-price">\$([^<]+)<\/span><\/span><\/div>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product regular price from the product view html.' );
        }
        $this->_setRegularPrice( $pregMatches[1] );

        // Parse the program type
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-prod-type">([^<]+)<\/div>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product program type from the product view html.' );
        }
        $this->_setProgramType( $pregMatches[1] );

        // Parse the publisher
        $pregMatches = array();
        $matchCount = preg_match( '/<a href="\/search\?searchProvider=[^"]+" class="adbl-link">([^<]+)<\/a>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product publisher from the product view html.' );
        }
        $this->_setPublisher( $pregMatches[1] );

        // Parse the length
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-run-time">([^<]+)<\/div>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product length from the product view html.' );
        }
        $this->_setLength( $pregMatches[1] );

        // Parse the release date
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-date">([^<]+)<\/div>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product release date from the product view html.' );
        }
        $this->_setReleaseDate( $pregMatches[1] );

        // Parse the available audible audio formats
        $pregMatches = array();
        $matchCount = preg_match_all( '/<span class="adbl-audio-format"><img src="[^"]+" height="[^"]+" alt="([^"]+)" width="[^"]+" border="[^"]+"\/><\/span>/', $productDetailHtml, $pregMatches );
        if ( $matchCount < 1 ) {
            throw new Exception( 'Unable to locate any audio formats from the product view html.' );
        }
        foreach ( $pregMatches[1] as $audioFormat ) {
            $this->_appendAudioFormat( $audioFormat );
        }

        // Parse the average customer rating
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-rating-text">([0-9.]+) based on ([0-9]+) ratings <\/div>/', $productDetailHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the average customer rating from the product view html.' );
        }
        $this->_setAverageCustomerRating( $pregMatches[1] );
        $this->_setCustomerRatingsCount(  $pregMatches[2] );

    }


    /**
     * @return string
     */
    public function toArray()
    {
        // @TODO
    }


    /**
     * @return string
     */
    public function toJson()
    {
        // @TODO
    }


    /**
     * @return string
     */
    public function toXml()
    {
        // @TODO
    }
    // ------------------------------------------------------------------------



    // ----- Private Methods --------------------------------------------------

    // ------------------------------------------------------------------------
}

