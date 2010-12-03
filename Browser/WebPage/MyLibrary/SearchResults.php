<?php
require_once 'Audible/Browser/WebPage.php';
require_once 'Audible/Product/AudioBook.php';

/**
 * @package Audible
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Audible_Browser_WebPage_MyLibrary_SearchResults extends Audible_Browser_WebPage
{
    // All properties are stored in the parent class.
    // ------------------------------------------------------------------------


    /**
     * Audible_Browser
     */
    public function __construct( Audible_Browser &$browser )
    {
        $this->_setMyLibraryUrl( 'http://www.audible.com/lib' );
        $this->setItemsPerPage( '200' );

        $this->_properties['Products'] = array();

        // Call the parent constructor
        parent::__construct( $browser );
    }


    // ----- Setters/Getters --------------------------------------------------

    /**
     * @param string
     */
    private function _setMyLibraryUrl( $newValue )
    {
        $this->_properties['MyLibraryUrl'] = $newValue;
    }
    /**
     * @return string
     */
    public function getMyLibraryUrl()
    {
        return $this->_properties['MyLibraryUrl'];
    }


    /**
     * This settings can affect performance.
     * @param string
     */
    public function setItemsPerPage( $newValue )
    {
        $this->_properties['ItemsPerPage'] = $newValue;
    }
    /**
     * @return string
     */
    public function getItemsPerPage()
    {
        return $this->_properties['ItemsPerPage'];
    }


    /**
     * @param int
     */
    private function _setFrom( $newValue )
    {
        $this->_properties['From'] = $newValue;
    }
    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->_properties['From'];
    }


    /**
     * @param int
     */
    private function _setTo( $newValue )
    {
        $this->_properties['To'] = $newValue;
    }
    /**
     * @return int
     */
    public function getTo()
    {
        return $this->_properties['To'];
    }


    /**
     * @param int
     */
    private function _setTotal( $newValue )
    {
        $this->_properties['Total'] = $newValue;
    }
    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->_properties['Total'];
    }


    /**
     * @param int
     */
    private function _setNextPageNumber( $newValue )
    {
        $this->_properties['NextPageNumber'] = $newValue;
    }
    /**
     * @return int
     */
    public function getNextPageNumber()
    {
        return $this->_properties['NextPageNumber'];
    }
    /**
     * @return bool
     */
    public function hasNextPageNumber()
    {
        if ( false === $this->isKnown('NextPageNumber') ) {
            return false;
        }
        if ( strlen( $this->getNextPageNumber() ) > 0 ) {
            return true;
        }
        return false;
    }


    /**
     * @param Audible_Product_AudioBook
     */
    private function _appendAudioBook( Audible_Product_AudioBook $audioBook )
    {
        $this->_properties['AudioBooks'][ $audioBook->getAsin() ] = $audioBook;
    }
    /**
     * @return bool
     */
    public function hasAudioBooks()
    {
        $hasAudioBooks = ( $this->getAudioBookCount() > 0 );
        return $hasProducts;
    }
    /**
     * @return int
     */
    public function getAudioBookCount()
    {
        $audioBookCount = count( $this->getAudioBooks() );
        return $audioBookCount;
    }
    /**
     * @return array Audible_Product_AudioBook
     */
    public function getAudioBooks()
    {
        return $this->_properties['AudioBooks'];
    }
    // ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------

    /**
     * @throws Exception
     * @param string
     * @param string
     */
    public function load( $programTypeFilter, $timeFilter, $pageNumber )
    {
        // Validate the passed params
        $programTypeFilter = $this->_normalizeProgramTypeFilter( $programTypeFilter );
        $timeFilter        = $this->_normalizeTimeFilter(        $timeFilter        );

        $searchResultsHtml = $this->_loadSearchResults( $programTypeFilter, $timeFilter, $pageNumber );

        $this->_parseSearchResults( $searchResultsHtml );
    }
    // ------------------------------------------------------------------------



    // ----- Private Methods --------------------------------------------------

    /**
     * @param string
     * @param string
     */
    private function _loadSearchResults( $programTypeFilter, $timeFilter, $pageNumber )
    {
        $progreamTypeFilter = $this->_normalizeProgramTypeFilter( $programTypeFilter );
        $timeFilter         = $this->_normalizeTimeFilter(        $timeFilter        );

        $browser     = $this->_getBrowser();
        $curlSession = $browser->getCurlSession();

        // Prepare to post ...
        $postHeaders = array(
            'Content-Type: '     . 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With: ' . 'XMLHttpRequest',
            'Referer: '          . $this->getMyLibraryUrl(),
            'User-Agent: '       . $browser->getUserAgent()
            );
        $postData =
              'progType='      . urlencode( $programTypeFilter       )
            . '&timeFilter='   . urlencode( $timeFilter              )
            . '&itemsPerPage=' . urlencode( $this->getItemsPerPage() )
            . '&page='         . urlencode( $pageNumber              )
            . '&mode='         . urlencode( 'normal'                 )
            . '&sortColumn='   . urlencode( 'PURCHASE_DATE'          )
            . '&sortType='     . urlencode( 'down'                   )
            ;
        $curlOptions = array(
            CURLOPT_HTTPHEADER     => $postHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_URL            => $this->getMyLibraryUrl() . '-ajax',
            );
        if( false === curl_setopt_array( $curlSession->getHandle(), $curlOptions ) ) {
            throw new Exception( 'Unable to set curl options' );
        }

        // ... and POST! ...
        $searchResultsHtml = curl_exec( $curlSession->getHandle() );
        if ( false === $searchResultsHtml ) {
            throw new Exception( 'Curl error while posting search form in "My Library":  ' . curl_error( $curlSession->getHandle() ) );
        }

        // Make sure we are actually on a search results page
        if ( false === strpos( $searchResultsHtml, '<span class="adbl-results-from">' ) ) {
            throw new Exception( 'Unrecognized audible search results page.' );
        }

        return $searchResultsHtml;
    }


    /**
     * @param string
     */
    private function _parseSearchResults( $searchResultsHtml )
    {
        // Reduce the html to a single line to make regex parsing easier
        $searchResultsHtml = str_replace( array( "\n", "\r" ), '', $searchResultsHtml );

        // Parse pagination
        $pregMatches = array();
        $matchCount = preg_match( '/(<div class="adbl-pagination">.*?<\/div>)/', $searchResultsHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the pagination html from the search results page.' );
        }
        $this->_parsePagination( $pregMatches[1] );

        // Parse audio books
        $pregMatches = array();
        $matchCount = preg_match_all( '/<tr[^>]+>(.*?)<\/tr>/', $searchResultsHtml, $pregMatches );
        if ( $matchCount < 1 ) {
            throw new Exception( 'Unable to locate audio books from the search results page.' );
        }
        // Load each audio book
        foreach( $pregMatches[1] as $audioBookHtml ) {

            $audioBook = new Audible_Product_AudioBook();
            $audioBook->loadFromMyLibrarySearchResultsHtml( $audioBookHtml );

            $this->_appendAudioBook( $audioBook );

            unset( $audioBook );
        }
    }


    /**
     * @param string
     */
    private function _parsePagination( $paginationHtml )
    {
        // Parse "from"
        $pregMatches = array();
        $matchCount = preg_match( '/<span class="adbl-results-from">([^<]+)<\/span>/', $paginationHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the "from" value from the search results page.' );
        }
        $this->_setFrom( $pregMatches[1] );

        // Parse "to"
        $pregMatches = array();
        $matchCount = preg_match( '/<span class="adbl-results-to">([^<]+)<\/span>/', $paginationHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the "to" value from the search results page.' );
        }
        $this->_setTo( $pregMatches[1] );

        // Parse "total"
        $pregMatches = array();
        $matchCount = preg_match( '/<span class="adbl-results-total">([^<]+)<\/span>/', $paginationHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            throw new Exception( 'Unable to locate the "total" value from the search results page.' );
        }
        $this->_setTotal( $pregMatches[1] );

        // Parse "next" page
        $pregMatches = array();
        $matchCount = preg_match( '/<a href="\/lib\?page=([^"]*)" class="adbl-link">NEXT<\/a>/', $paginationHtml, $pregMatches );
        if ( 1 !== $matchCount ) {
            $pregMatches[1] = '';
            //throw new Exception( 'Unable to locate the "next page" value from the search results page.' );
        }
        $this->_setNextPageNumber( $pregMatches[1] );
    }


    /**
     * @param string
     * @return string
     */
    private function _normalizeProgramTypeFilter( $unnormalizedProgramTypeFilter )
    {
        $programTypeFilter = strtolower( trim( $unnormalizedProgramTypeFilter ) );

        switch ( $programTypeFilter ) {
            case 'all':
            case 'all program types':
                $programTypeFilter = 'all';
                break;
            case 'bk':
            case 'audiobooks':
                $programTypeFilter = 'bk';
                break;
            case 'lc':
            case 'lectures':
                $programTypeFilter = 'lc';
                break;
            case 'nb':
            case 'newspaper':
                $programTypeFilter = 'nb';
                break;
            case 'pe':
            case 'periodicals':
                $programTypeFilter = 'pe';
                break;
            case 'pf':
            case 'performances':
                $programTypeFilter = 'pf';
                break;
            case 'rt':
            case 'radio/tv':
            case 'radio':
            case 'tv':
                $programTypeFilter = 'rt';
                break;
            case 'sp':
            case 'speeches':
                $programTypeFilter = 'sp';
                break;
            case 'wc':
            case 'wordcast':
                $programTypeFilter = 'wc';
                break;
            default:
                throw new Exception( 'Unrecognized program-type filter: ' . $unnormalizedProgramTypeFilter );
                break;
        }

        return $programTypeFilter;
    }

    /**
     * @param string
     * @return string
     */
    private function _normalizeTimeFilter( $unnormalizedTimeFilter )
    {
        $timeFilter = strtolower( trim( $unnormalizedTimeFilter ) );

        switch ( $timeFilter ) {
            case '30':
            case 'past 30 days':
                $timeFilter = '30';
                break;
            case '60':
            case 'past 60 days':
                $timeFilter = '60';
                break;
            case '90':
            case 'past 90 days':
                $timeFilter = '90';
                break;
            case '183':
            case 'past 6 months':
                $timeFilter = '183';
                break;
            case '365':
            case 'past year':
                $timeFilter = '365';
                break;
            case 'all':
                $timeFilter = 'all';
                break;
            default:
                throw new Exception( 'Unrecognized time filter: ' . $unnormalizedTimeFilter );
                break;
        }

        return $timeFilter;
    }
    // ------------------------------------------------------------------------
}


