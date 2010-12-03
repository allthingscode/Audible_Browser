<?php

/**
 * @package Audible
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
abstract class Audible_Browser_WebPage
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
	 * @param Audible_Browser
	 */
	public function __construct( Audible_Browser &$browser )
	{
		$this->_setBrowser( $browser );
	}


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
	 * @param Audible_Browser
	 */
	protected function _setBrowser( &$newValue )
	{
		$this->_properties['Browser'] = $newValue;
	}
	/**
	 * @return Audible_Browser
	 */
	protected function _getBrowser()
	{
		return $this->_properties['Browser'];
	}
	// ------------------------------------------------------------------------




	// ----- Public Methods ---------------------------------------------------

	// ------------------------------------------------------------------------





	// ----- Private Methods --------------------------------------------------

	// ------------------------------------------------------------------------
}
