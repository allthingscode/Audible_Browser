<?php


/**
 * @package Audible
 */
class Audible_Product
{
	/**
	 * All properties for this object are stored in this array.
	 * Default values are not populated,
	 *   so if properties are accessed before they are set,
	 *   a php notice is generated.  This behavior helps identify coding errors.
	 * @var array
	 */
	protected $_properties = array();
	// ------------------------------------------------------------------------


	/**
	 * This helps prevent invalid property assignments.
	 * @param string
	 * @param mixed
	 */
	public function __set( $propertyName, $propertyValue )
	{
		throw new Exception( 'Invalid property assignment: ' . $propertyName . ' => ' . $propertyValue );
	}
	/**
	 * This helps catch invalid property retreival
	 * @param string
	 */
	public function __get( $propertyName )
	{
		throw new Exception( 'Invalid property retreival: ' . $propertyName );
    }



	// ----- Setters/Getters --------------------------------------------------

    /**
     * @param string
     * @return bool
     */
    public function isKnown( $propertyName )
    {
        $isKnown = isset( $this->_properties[ $propertyName ] );
        return $isKnown;
    }



	/**
	 * @param string
	 */
	protected function _setAsin( $newValue )
	{
		$this->_properties['Asin'] = trim( $newValue );
	}
	/**
	 * @return string
	 */
	public function getAsin()
	{
		return $this->_properties['Asin'];
    }
	// ------------------------------------------------------------------------



    // ----- Public Methods ---------------------------------------------------

	// ------------------------------------------------------------------------



	// ----- Private Methods --------------------------------------------------

	/**
	 * This resets all local properties.
	 */
	protected function _reset()
	{
		$this->_properties = array();
	}
	// ------------------------------------------------------------------------
}

