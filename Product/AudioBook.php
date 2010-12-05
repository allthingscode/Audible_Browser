<?php
require_once 'Audible/Product.php';

/**
 * @package Audible
 */
abstract class Audible_Product_AudioBook extends Audible_Product
{
    // All properties are stored in the parent Audible_Product class.
    // ------------------------------------------------------------------------


    // ----- Setters/Getters --------------------------------------------------

    /**
     * @param string
     */
    protected function _setProgramType( $newValue )
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
    protected function _setTitle( $newValue )
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
    protected function _appendAuthor( $newValue )
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
    protected function _appendNarrator( $newValue )
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
    protected function _setAverageCustomerRating( $newValue )
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
    protected function _setDescription( $newValue )
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
    protected function _setLength( $newValue )
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
     * @param string
     */
    protected function _appendAudioFormat( $newValue )
    {
        $newValue = trim( strip_tags( $newValue ) );
        if ( $this->getAudioFormatCount() > 0 ) {
            if ( true === in_array( $newValue, $this->getAudioFormats() ) ) {
                return;
            }
        }
        $this->_properties['AudioFormats'][] = $newValue;
    }
    /**
     * @return array
     */
    public function getAudioFormats()
    {
        return $this->_properties['AudioFormats'];
    }
    /**
     * @return int
     */
    public function getAudioFormatCount()
    {
        if ( false === $this->isKnown( 'AudioFormats' ) ) {
            return 0;
        }
        $count = count( $this->getAudioFormats('AudioFormats') );
        return $count;
    }


    /**
     * @param bool
     */
    protected function _setIsInMyLibrary( $newValue )
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



    // ------------------------------------------------------------------------



    // ----- Private Methods --------------------------------------------------

    // ------------------------------------------------------------------------
}

