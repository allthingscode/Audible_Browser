<?php
require_once 'Audible/Product.php';

/**
 * @package Audible
 */
final class Audible_Product_AudioBook extends Audible_Product
{
    // All properties are stored in the parent Audible_Product class.
	// ------------------------------------------------------------------------


	// ----- Setters/Getters --------------------------------------------------

	/**
	 * @param string
	 */
	private function _setProgramType( $newValue )
	{
		$this->_properties['ProgramType'] = trim( $newValue );
	}
	/**
	 * @return string
	 */
	public function getProgramType()
	{
		return $this->_properties['ProgramType'];
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
	 * @param string
	 */
	private function _setTitle( $newValue )
	{
		$this->_properties['Title'] = trim( $newValue );
	}
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_properties['Title'];
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
	 * @param string
	 */
	private function _appendNarrator( $newValue )
    {
        $newValue = trim( strip_tags( $newValue ) );
        if ( $this->getNarratorCount() > 0 ) {
            if ( true === in_array( $newValue, $this->_properties['Narrators'] ) ) {
                return;
            }
        }
		$this->_properties['Narrators'][] = $newValue;
    }
	/**
	 * @return array
	 */
	public function getNarrators()
	{
		return $this->_properties['Narrators'];
    }
    /**
     * @return int
     */
    public function getNarratorCount()
    {
        if ( false === $this->isKnown( 'Narrators' ) ) {
            return 0;
        }
        $count = count( $this->_properties['Narrators'] );
        return $count;
    }


	/**
	 * @param float
	 */
	private function _setAverageCustomerRating( $newValue )
	{
		$this->_properties['AverageCustomerRating'] = trim( $newValue );
	}
	/**
	 * @return float
	 */
	public function getAverageCustomerRating()
	{
		return $this->_properties['AverageCustomerRating'];
	}


	/**
	 * @param string
	 */
	private function _setDescription( $newValue )
	{
		$this->_properties['Description'] = trim( strip_tags( $newValue ) );
	}
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_properties['Description'];
	}


	/**
	 * @param string
	 */
	private function _appendAuthor( $newValue )
    {
        $newValue = trim( strip_tags( $newValue ) );
        if ( $this->getAuthorCount() > 0 ) {
            if ( true === in_array( $newValue, $this->_properties['Authors'] ) ) {
                return;
            }
        }
		$this->_properties['Authors'][] = $newValue;
    }
	/**
	 * @return array
	 */
	public function getAuthors()
	{
		return $this->_properties['Authors'];
    }
    /**
     * @return int
     */
    public function getAuthorCount()
    {
        if ( false === $this->isKnown('Authors') ) {
            return 0;
        }
        $count = count( $this->_properties['Authors'] );
        return $count;
    }


	/**
	 * @param string
	 */
	private function _setLength( $newValue )
	{
		$this->_properties['Length'] = trim( $newValue );
	}
	/**
	 * @return string
	 */
	public function getLength()
	{
		return $this->_properties['Length'];
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
	 * @param bool
	 */
	private function _setIsInMyLibrary( $newValue )
	{
		$this->_properties['IsInMyLibrary'] = $newValue;
	}
	/**
	 * @return bool
	 */
	public function getIsInMyLibrary()
	{
		return $this->_properties['IsInMyLibrary'];
    }
	// ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------



	/**
	 * @param string
	 */
	public function loadFromMyLibrarySearchResultsHtml( $searchResultsRowHtml )
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
		$matchCount = preg_match( '/<a href="\/pd\?asin=([^"]+)" name="tdTitle" class="adbl-link adbl-prod-title">([^<]+)<\/a>/', $searchResultsRowHtml, $pregMatches );
		if ( 1 !== $matchCount ) {
			throw new Exception( 'Unable to locate the product asin and title value from the search results row.' );
		}
		$this->_setAsin(  $pregMatches[1] );
		$this->_setTitle( $pregMatches[2] );

		// Parse the narrated-by
		$pregMatches = array();
		$matchCount = preg_match( '/<div class="adbl-overlay-narrated-by">([^<]+)<\/div>/', $searchResultsRowHtml, $pregMatches );
		if ( 1 !== $matchCount ) {
			$pregMatches[1] = '';
			//throw new Exception( 'Unable to locate the narrated-by value from the search results row.' );
		}
		$this->_appendNarrator( $pregMatches[1] );

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
		$this->_appendAuthor( $pregMatches[1] );

		// Parse the my rating
		$pregMatches = array();
		$matchCount = preg_match( '/<div class="adbl-rating-act-cont" rating="([^"]*)" asin="[^"]+"><a /', $searchResultsRowHtml, $pregMatches );
		if ( 1 !== $matchCount ) {
			throw new Exception( 'Unable to locate the rating value from the search results row.' );
		}
		$this->_setMyRating( $pregMatches[1] );

		// Parse the downloaded flag
		$pregMatches = array();
		$matchCount = preg_match( '/<td><span class="adbl-check-off[^"]*">([^<]*)<\/span><\/td>/', $searchResultsRowHtml, $pregMatches );
		if ( 1 !== $matchCount ) {
			throw new Exception( 'Unable to locate the downloaded flag from the search results row.' );
		}
		$this->_setDownloaded( strlen( trim( $pregMatches[1] ) ) > 0 );
	}


    /**
     * @param string
     */
    public function loadFromProductDetailHtml( $productDetailHtml )
    {
		// Make sure we are starting with a blank slate.
        $this->_reset();

        // Getting rid of new-line chars makes parsing easier
        $productDetailHtml = str_replace( array( "\n", "\r" ), '', $productDetailHtml );

        // See if this product is in My Library
        // @TODO Determine if we are logged in so we can set this property definitively
        if ( false !== strpos( $productDetailHtml, 'This audio is available in <a class="adbl-link" href="/lib">My Library' ) ) {
            $this->_setIsInMyLibrary( true );
        }

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
		$this->_setProgramType( $pregMatches[1] );

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
    }
	// ------------------------------------------------------------------------



	// ----- Private Methods --------------------------------------------------

	// ------------------------------------------------------------------------
}

