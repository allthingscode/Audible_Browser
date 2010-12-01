<?php
require_once 'Audible/Browser/WebPage.php';

/**
 * @package Audible
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Audible_Browser_WebPage_SignIn extends Audible_Browser_WebPage
{
    // All properties are stored in the parent class.
	// ------------------------------------------------------------------------


	/**
	 * @param Audible_Browser
	 */
	public function __construct( Audible_Browser &$browser )
	{
        $this->_setSignInPageUrl( 'https://www.audible.com/sign-in' );

        // Call the parent constructor
        parent::__construct( $browser );
	}


	// ----- Setters/Getters --------------------------------------------------

	/**
	 * @param string
	 */
	private function _setAppActionToken( $newValue )
	{
		$this->_properties['AppActionToken'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getAppActionToken()
	{
		return $this->_properties['AppActionToken'];
	}
	/**
	 * @return bool
	 */
	public function hasAppActionToken()
	{
		if ( false === $this->isKnown('AppActionToken') ) {
			return false;
		}
		if( 0 === strlen( trim( $this->getAppActionToken() ) ) ) {
			return false;
		}
		return true;
	}


	/**
	 * @param string
	 */
	private function _setAppAction( $newValue )
	{
		$this->_properties['AppAction'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getAppAction()
	{
		return $this->_properties['AppAction'];
	}
	/**
	 * @return bool
	 */
	public function hasAppAction()
	{
		if ( false === $this->isKnown('AppAction') ) {
			return false;
		}
		if( 0 === strlen( trim( $this->getAppAction() ) ) ) {
			return false;
		}
		return true;
	}


	/**
	 * @param string
	 */
	public function setEmail( $newValue )
	{
		$this->_properties['Email'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->_properties['Email'];
	}
	/**
	 * @return bool
	 */
	public function hasEmail()
	{
		if ( false === $this->isKnown('Email') ) {
			return false;
		}
		if( 0 === strlen( trim( $this->getEmail() ) ) ) {
			return false;
		}
		return true;
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
	 * @return bool
	 */
	public function hasPassword()
	{
		if ( false === $this->isKnown('Password') ) {
			return false;
		}
		if( 0 === strlen( trim( $this->getPassword() ) ) ) {
			return false;
		}
		return true;
	}


	/**
	 * @param string
	 */
	private function _setSignInPageUrl( $newValue )
	{
		$this->_properties['SignInPageUrl'] = $newValue;
	}
	/**
	 * @return string
	 */
	public function getSignInPageUrl()
	{
		return $this->_properties['SignInPageUrl'];
	}
	// ------------------------------------------------------------------------




	// ----- Public Methods ---------------------------------------------------

	/**
	 * @throws Exception
	 */
	public function load()
	{
		// Get the entire html source for the sign-in page
		$signInPageHtml = $this->_getSignInPageHtml();

		// page html sanity checks
		if ( false === strpos( $signInPageHtml, '<title>Member Login' ) ) {
			throw new Exception( 'Unrecognized sign-in page.' );
		}

		// Parse appActionToken
		$pregMatches = array();
		$matchCount = preg_match( '/<input type="hidden" name="appActionToken" value="([^"]+)"/', $signInPageHtml, $pregMatches );
		if ( 1 !== $matchCount ) {
			throw new Exception( 'Unable to locate the appActionToken form value from the sign-in page.' );
		}
		$this->_setAppActionToken( $pregMatches[1] );

		// Parse appAction
		$pregMatches = array();
		$matchCount = preg_match( '/<input type="hidden" name="appAction" value="([^"]+)"/', $signInPageHtml, $pregMatches );
		if ( 1 !== $matchCount ) {
			throw new Exception( 'Unable to locate the appAction form value from the sign-in page.' );
		}
		$this->_setAppAction( $pregMatches[1] );
	}


	/**
	 * @throws Exception
	 */
	public function postSignInForm()
	{
		// Make sure we have all required values
		if( false === $this->hasEmail() ) {
			throw new Exception( 'The Audible account username has not been set' );
		}
		if( false === $this->hasPassword() ) {
			throw new Exception( 'The Audible account password has not been set' );
		}
		if( false === $this->hasAppActionToken() ) {
			throw new Exception( 'The AppActionToken is not set' );
		}
		if( false === $this->hasAppAction() ) {
			throw new Exception( 'The AppAction is not set' );
		}

        $browser     = $this->_getBrowser();
        $curlSession = $browser->getCurlSession();

		// Prepare to post ...
		$postHeaders = array(
			'Content-Type: ' . 'application/x-www-form-urlencoded; charset=UTF-8',
			'User-Agent: '   . $browser->getUserAgent()
			);
		$postData =
			  'email='           . urlencode( $this->getEmail()          )
			. '&password='       . urlencode( $this->getPassword()       )
			. '&appActionToken=' . urlencode( $this->getAppActionToken() )
			. '&appAction='      . urlencode( $this->getAppAction()      );
		$curlOptions = array(
			CURLOPT_HTTPHEADER     => $postHeaders,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $postData,
			CURLOPT_URL            => $this->getSignInPageUrl()
			);
		if( false === curl_setopt_array( $curlSession->getHandle(), $curlOptions ) ) {
			throw new Exception( 'Unable to set curl options' );
		}

		// ... and, POST! ...
		$homePageHtml = curl_exec( $curlSession->getHandle() );
		if ( false === $homePageHtml ) {
			throw new Exception( 'Curl error while posting sign-in form:  ' . curl_error( $curlSession->getHandle() ) );
		}

		// Make sure we are actually logged in successfully.
		if ( false === strpos( $homePageHtml, '<title>Audible.com' ) ) {
			throw new Exception( 'Unrecognized audible home page.' );
		}
	}
	// ------------------------------------------------------------------------





	// ----- Private Methods --------------------------------------------------


	/**
	 * @return string
	 */
	private function _getSignInPageHtml()
	{
        $browser     = $this->_getBrowser();
        $curlSession = $browser->getCurlSession();

		$postHeaders = array(
			'User-Agent: ' . $browser->getUserAgent()
			);
		$curlOptions = array(
			CURLOPT_HTTPHEADER     => $postHeaders,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POST           => false,
			CURLOPT_HTTPGET        => true,
			CURLOPT_HEADER         => false,
			CURLOPT_URL            => $this->getSignInPageUrl()
			);
		if( false === curl_setopt_array( $curlSession->getHandle(), $curlOptions ) ) {
			throw new Exception( 'Unable to set curl options' );
		}

		$signInPageHtml = curl_exec( $curlSession->getHandle() );
		if ( false === $signInPageHtml ) {
			throw new Exception( 'Curl error while retrieving sign-in page html:  ' . curl_error( $curlSession->getHandle() ) );
		}

		return $signInPageHtml;
	}
	// ------------------------------------------------------------------------
}
