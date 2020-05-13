<?php

class MwddSpecial extends SpecialPage {

	public function __construct() {
		parent::__construct( 'Mwdd' );
	}

	/**
	 * @see SpecialPage::execute
	 *
	 * @param string|null $subPage
	 */
	public function execute( $subPage ) {
		parent::execute( $subPage );
		global $mwddServices;

		$this->getOutput()->addHTML( "Which services are running?" );
		$this->getOutput()->addHTML( "</br>" );
		$this->getOutput()->addHTML( json_encode( $mwddServices ) );
	}

}
