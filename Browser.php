<?php
require_once 'Audible/Browser/CurlSession.php';
require_once 'Audible/Product.php';
require_once 'Audible/Browser/WebPage/SignIn.php';
require_once 'Audible/Browser/WebPage/MyLibrary.php';
require_once 'Audible/Browser/WebPage/ProductDetail.php';

/**
 * @package Audible
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Audible_Browser
{
	/**
	 * All properties for this object are stored in this array.
	 * Default values are not populated,
	 *   so if properties are accessed before they are set,
	 *   a php notice is generated.  This behavior helps identify coding errors.
	 * @var array
	 */
	private $_properties = array();
	// ------------------------------------------------------------------------


	/**
	 * constructor
	 */
	public function __construct()
	{
		// Default the IsSignedInFlag value
		$this->_setIsSignedInFlag( false );

		// This is the user agent we will be using in all requests
		$this->setUserAgent( 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.7 (KHTML, like Gecko) Chrome/7.0.517.44 Safari/534.7' );
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
	 * @return Audible_Browser_CurlSession
	 */
	public function getCurlSession()
	{
		$curlSession = Audible_Browser_CurlSession::getInstance();
		return $curlSession;
    }
    
    
    /**
	 * @param string
	 */
	public function setUserAgent( $newValue )
	{
		$this->_properties['UserAgent'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->_properties['UserAgent'];
	}


	/**
	 * @param string
	 */
	public function setUsername( $newValue )
	{
		$this->_properties['Username'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->_properties['Username'];
	}


	/**
	 * @param string
	 */
	public function setPassword( $newValue )
	{
		$this->_properties['Password'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->_properties['Password'];
	}


	/**
	 * @param bool
	 */
	private function _setIsSignedInFlag( $newValue )
	{
		$this->_properties['IsSignedInFlag'] = $newValue;
	}
	/**
	 * @return bool
	 */
	public function getIsSignedInFlag()
	{
		return $this->_properties['IsSignedInFlag'];
	}
	/**
	 * @return bool
	 */
	public function isSignedIn()
	{
		return $this->getIsSignedInFlag();
	}
	// ------------------------------------------------------------------------




	// ----- Public Methods ---------------------------------------------------

	/**
	 * Most methods in this class
	 *   will automatically call this method if we are not already signed in.
	 * @throws Exception
	 */
	public function signIn()
    {
        // Only sign in once
		if( true === $this->isSignedIn() ) {
		    return;
		}

		// Submit the sign-in web form
		$signInPage = new Audible_Browser_WebPage_SignIn( $this );
		$signInPage->setEmail(    $this->getUsername() );
		$signInPage->setPassword( $this->getPassword() );
		$signInPage->load();
		$signInPage->postSignInForm();

		// Ladies and gentlemen, we are now signed into Audible.com.
		$this->_setIsSignedInFlag( true );
	}



	/**
	 * @param string
	 * @param string
	 * @return array of Audible_Product_AudioBook objects
	 */
	public function getMyLibrary( $programTypeFilter = 'all', $timeFilter = 'all' )
	{
		// We can't get any audible information without signing in first.
		if( false === $this->isSignedIn() ) {
			$this->signIn();
		}

		$myLibraryPage = new Audible_Browser_WebPage_MyLibrary( $this );
		$myLibraryPage->loadAll();
		$audioBooks = $myLibraryPage->getSearchResults()->getAudioBooks();

		return $audioBooks;
	}


	/**
	 * @return array of Audible_Product objects
	 */
	public function getWishList()
	{
		// We can't get any audible information without signing in first.
		if( false === $this->isSignedIn() ) {
			$this->signIn();
		}

		// @TODO
	}


	/**
	 * @return array of Audible_Product objects
	 */
	public function getMyNextListen()
	{
		// We can't get any audible information without signing in first.
		if( false === $this->isSignedIn() ) {
			$this->signIn();
		}

		// @TODO
	}


	/**
     * @param string
     * @return Audible_Product
	 */
	public function getProductDetails( $asin )
    {
		$productDetailPage = new Audible_Browser_WebPages_ProductDetail( $this );
		$productDetailPage->loadFromAsin( $asin );
		$product = $productDetailPage->getProduct();

        return $product;
	}
	// ------------------------------------------------------------------------




	// ----- Private Methods --------------------------------------------------


	// ------------------------------------------------------------------------
}
