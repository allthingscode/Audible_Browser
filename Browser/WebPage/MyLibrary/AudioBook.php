<?php
require_once 'Audible/Product/AudioBook.php';

/**
 * @package Audible
 */
final class Audible_Browser_WebPage_MyLibrary_AudioBook extends Audible_Product_AudioBook
{
    // All properties are stored in the parent Audible_Product_AudioBook class.
    // ------------------------------------------------------------------------


    // ----- Setters/Getters --------------------------------------------------

    /**
     * @return string
     */
    public function getTopAsin()
    {
        if ( true === $this->hasParentAsin() ) {
            return $this->getParentAsin();
        }
        return $this->getAsin();
    }


    /**
     * @param string
     */
    private function _setOrdNumber( $newValue )
    {
        $this->_properties['OrdNumber'] = trim( $newValue );
    }
    /**
     * @return string
     */
    public function getOrdNumber()
    {
        return $this->_properties['OrdNumber'];
    }


    /**
     * @param string
     */
    private function _setProductId( $newValue )
    {
        $newValue = trim( $newValue );
        $this->_properties['ProductId'] = $newValue;
        if ( strlen( $newValue ) > 0 ) {
            $programType = $this->_resolveProgramTypeFromProductId($newValue);
            if ( false === is_null( $programType ) ) {
                $this->_setProgramType( $programType );
            }
        }
    }
    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->_properties['ProductId'];
    }


    /**
     * @param string
     */
    private function _setParentProductId( $newValue )
    {
        $this->_properties['ParentProductId'] = trim( $newValue );
    }
    /**
     * @return string
     */
    public function getParentProductId()
    {
        return $this->_properties['ParentProductId'];
    }


    /**
     * @param string
     */
    private function _setParentAsin( $newValue )
    {
        $this->_properties['ParentAsin'] = trim( $newValue );
    }
    /**
     * @return string
     */
    public function getParentAsin()
    {
        return $this->_properties['ParentAsin'];
    }
    /**
     * @return bool
     */
    public function hasParentAsin()
    {
        if ( false === $this->isKnown( 'ParentAsin' ) ) {
            return false;
        }
        if ( strlen( $this->getParentAsin() ) > 0 ) {
            return true;
        }
        return false;
    }


    /**
     * @param string
     */
    private function _setItemDeliveryType( $newValue )
    {
        $this->_properties['ItemDeliveryType'] = trim( $newValue );
    }
    /**
     * @return string
     */
    public function getItemDeliveryType()
    {
        return $this->_properties['ItemDeliveryType'];
    }


    /**
     * @param string
     */
    private function _setPurchaseDate( $newValue )
    {
        if ( 0 === preg_match( '/\d{2}-\d{2}-\d{2}/', $newValue ) ) {
            throw new Exception( 'Invalid purchase date value: ' . $newValue );
        }
        $newValue = str_replace( '-', '/', $newValue );
        $newValue = date( 'Y-m-d', strtotime( $newValue ) );
        $this->_properties['PurchaseDate'] = $newValue;
    }
    /**
     * @return string
     */
    public function getPurchaseDate()
    {
        return $this->_properties['PurchaseDate'];
    }


    /**
     * This is a wrapper for _appendAuthor()
     * @param string
     */
    private function _setAuthors( $newValue )
    {
        $authors = explode( ',', $newValue );
        foreach ( $authors as $author ) {
            $this->_appendAuthor( $author );
        }
    }


    /**
     * @param float
     */
    private function _setMyRating( $newValue )
    {
        $this->_properties['MyRating'] = trim( $newValue );
    }
    /**
     * @return float
     */
    public function getMyRating()
    {
        return $this->_properties['MyRating'];
    }


    /**
     * @param bool
     */
    private function _setDownloaded( $newValue )
    {
        $this->_properties['Downloaded'] = $newValue;
    }
    /**
     * @return bool
     */
    public function getDownloaded()
    {
        return $this->_properties['Downloaded'];
    }
    // ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------



    /**
     * @param string
     */
    public function load( $searchResultsRowHtml )
    {
        // Make sure we are starting with a blank slate.
        $this->_reset();

        $this->_setIsInMyLibrary( true );

        // Parse the Purchased Date and Length
        $pregMatches = array();
        $matchCount = preg_match_all( '/<td><span class="adbl-label">([^<]+)<\/span><\/td>/', $searchResultsRowHtml, $pregMatches );
        if ( 2 !== $matchCount ) {
            throw new Exception( 'Unable to locate the purchase date and/or length from the search results row.' );
        }
        $this->_setPurchaseDate( $pregMatches[1][0] );
        $this->_setLength(       $pregMatches[1][1] );

        // Parse the asin and title
        $pregMatches = array();
        $matchCount = preg_match( '/<a href="\/pd\?asin=[^"]+" name="tdTitle" class="adbl-link adbl-prod-title">([^<]+)<\/a>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product asin and title value from the search results row.' );
        }
        $this->_setTitle( $pregMatches[1] );

        // Parse the narrated-by
        // NOTE:  Some audio books really don't have a narrator
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-overlay-narrated-by">Narrated by ([^<]+)<\/div>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 == $matchCount ) {
            $this->_appendNarrator( $pregMatches[1] );
        }

        // Parse the average customer rating
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-ratingnumdisp-libflyout">\s+<span>([^<]+)<\/span>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the average customer rating value from the search results row.' );
        }
        $this->_setAverageCustomerRating( $pregMatches[1] );

        // Parse the description
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-overlay-desc">(.*?)<\/div>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the description value from the search results row.' );
        }
        $this->_setDescription( $pregMatches[1] );

        // Parse the author
        $pregMatches = array();
        $matchCount = preg_match( '/<td><a href="\/search\?searchAuthor=[^"]+" class="adbl-link">(.*?)<\/a><\/td>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the author value from the search results row.' );
        }
        $this->_setAuthors( $pregMatches[1] );

        // Parse the my rating
        $pregMatches = array();
        $matchCount = preg_match( '/<div class="adbl-rating-act-cont" rating="([^"]*)" asin="[^"]+"><a /', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the rating value from the search results row.' );
        }
        $this->_setMyRating( $pregMatches[1] );

        // Parse the ord number
        $pregMatches = array();
        $matchCount = preg_match( '/<input type=hidden name="ordNumber" value="([^"]+)" \/>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the ord number from the search results row.' );
        }
        $this->_setOrdNumber( $pregMatches[1] );

        // Parse the product id
        $pregMatches = array();
        $matchCount = preg_match( '/<input type=hidden name="productId" value="([^"]+)" \/>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the product id from the search results row.' );
        }
        $this->_setProductId( $pregMatches[1] );

        // Parse the parent product id
        $pregMatches = array();
        $matchCount = preg_match( '/<input type=hidden name="parentProductId" value="([^"]*)" \/>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the parent product id from the search results row.' );
        }
        $this->_setParentProductId( $pregMatches[1] );

        // Parse the asin
        $pregMatches = array();
        $matchCount = preg_match( '/<input type=hidden name="asin" value="([^"]+)" \/>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the asin from the search results row.' );
        }
        $this->_setAsin( $pregMatches[1] );

        // Parse the parent asin
        $pregMatches = array();
        $matchCount = preg_match( '/<input type=hidden name="parentAsin" value="([^"]*)" \/>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the parent asin from the search results row.' );
        }
        $this->_setParentAsin( $pregMatches[1] );

        // Parse the item delivery type
        $pregMatches = array();
        $matchCount = preg_match( '/<input type=hidden name="itemDeliveryType" value="([^"]+)" \/>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the item delivery type from the search results row.' );
        }
        $this->_setItemDeliveryType( $pregMatches[1] );

        // Parse the audible formats
        $pregMatches = array();
        $matchCount = preg_match_all( '/<option [^>]+>([^<]+)<\/option>/', $searchResultsRowHtml, $pregMatches );
        if ( $matchCount < 1 ) {
            throw new Exception( 'Unable to locate any audible formats from the search results row.' );
        }
        foreach ( $pregMatches[1] as $audioFormat ) {
            $this->_appendAudioFormat( $audioFormat );
        }

        // Parse the downloaded flag
        $pregMatches = array();
        $matchCount = preg_match( '/<td><span class="adbl-check-off[^"]*">([^<]*)<\/span><\/td>/', $searchResultsRowHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the downloaded flag from the search results row.' );
        }
        $this->_setDownloaded( strlen( trim( $pregMatches[1] ) ) > 0 );
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

    /**
     * @param string
     */
    private function _resolveProgramTypeFromProductId( $productId )
    {
        $programType = null;

        // The first 2 chars of the product id is a code that sometimes translates to program types
        $programTypeCode = strtolower( substr( $productId, 0, 2 ) );

        switch ( $programTypeCode ) {
            case 'bk':
                $programType = 'Audiobook';
                break;
            case 'lc':
                $programType = 'Lecture';
                break;
            case 'nb':
                $programType = 'Newspaper';
                break;
            case 'pe':
                $programType = 'Periodical';
                break;
            case 'pf':
                $programType = 'Performance';
                break;
            case 'rt':
                $programType = 'Radio/TV';
                break;
            case 'sp':
                $programType = 'Speech';
                break;
            case 'wc':
                $programType = 'Wordcast';
                break;
            case 'fr':
                // This was a free item, so the product id does not indicate the program type
                break;
            default:
                print_r( $this );
                throw new Exception( 'Unable to resolve program type from product id: ' . $productId );
                break;
        }

        return $programType;
    }
    // ------------------------------------------------------------------------
}

