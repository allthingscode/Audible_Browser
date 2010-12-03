<?php
require_once 'Audible/Browser/WebPage.php';
require_once 'Audible/Browser/WebPage/MyLibrary/SearchResults.php';

/**
 * @package Audible
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Audible_Browser_WebPage_MyLibrary extends Audible_Browser_WebPage
{
    // All properties are stored in the parent class.
    // ------------------------------------------------------------------------



    // ----- Setters/Getters --------------------------------------------------

    /**
     * @param Audible_Browser_WebPage_MyLibrary_SearchResults
     */
    private function _setSearchResults( Audible_Browser_WebPage_MyLibrary_SearchResults $newValue )
    {
        $this->_properties['SearchResults'] = $newValue;
    }
    /**
     * @return Audible_Browser_MyLibrary_SearchResults
     */
    public function getSearchResults()
    {
        return $this->_properties['SearchResults'];
    }
    // ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------

    /**
     * @throws Exception
     * @param string
     * @param string
     */
    public function loadAll( $programTypeFilter = 'all', $timeFilter = 'all' )
    {
        $browser = $this->_getBrowser();

        $searchResults = new Audible_Browser_WebPage_MyLibrary_SearchResults( $browser );

        // Load page 1
        $searchResults->load( $programTypeFilter, $timeFilter, 1 );

        // ... and then, load all subsequent pages ...
        while( true === $searchResults->hasNextPageNumber() ) {
            $searchResults->load(
                $programTypeFilter,
                $timeFilter,
                $searchResults->getNextPageNumber() );
        }

        $this->_setSearchResults( $searchResults );
    }
    // ------------------------------------------------------------------------



    // ----- Private Methods --------------------------------------------------

    // ------------------------------------------------------------------------
}


