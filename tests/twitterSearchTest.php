<?php

use PHPUnit\Framework\TestCase;

/*
 * @covers twitterSearch
 */

class twitterSearchTest extends TestCase {

	/* @var twitterSearch */
	protected $twitter;

	public function setUp() {

		include_once("src/class.twitterSearch.php");
	
		$this->twitter = new twitterSearch('testConsumerKeyValue',
			'testConsumerSecretValue');
	}

	public function tearDown() {

		$this->twitter = null;
	}

	/**
	 * @param string $inputString string of space separated usernames
	 * @param string $expectedResult the expected query string
	 * @param int $numLookupUserLoops # times a user has to be validated
	 *
	 * @dataProvider providerTestSetUsernamesSetsUsernameQuery
	 */
	public function testSetUsernamesSetsUsernameQuery($inputString, $expectedResult, $numLookupUserLoops) {

		// first prepare the TwitterOAuth mock
		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();

		$twitterOAuth->expects($this->exactly($numLookupUserLoops))
			->method('get')
			->will($this->returnValue(''));

		$twitterOAuth->expects($this->exactly($numLookupUserLoops))
			->method('getLastHttpCode')
			->will($this->returnValue('200'));

		$this->twitter->setTwitter($twitterOAuth);
		$this->twitter->setUsernames($inputString);

		$this->assertEquals($expectedResult, $this->twitter->getUsernameQuery());
	}

	public function providerTestSetUsernamesSetsUsernameQuery() {

		return array(
			array('', '', 0),
			array(' ', '', 0),
			array('username1', 'from:username1', 1),
			array('username1 username2 username3',
				'from:username1 OR from:username2 OR from:username3',
				3)
			);
	}

	/**
	 * @param string $inputString string of space separated usernames
	 * @param array $expectedResult the expected query string
	 * @param int $numLookupUserLoops # times a user has to be validated
	 *
	 * @dataProvider providerTestSetUsernamesSetsUsernameArray
	 */
	public function testSetUsernamesSetsUsernameArray($inputString, $expectedResult, $numLookupUserLoops) {

		// first prepare the TwitterOAuth mock
		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();

		$twitterOAuth->expects($this->exactly($numLookupUserLoops))
			->method('get')
			->will($this->returnValue(''));

		$twitterOAuth->expects($this->exactly($numLookupUserLoops))
			->method('getLastHttpCode')
			->will($this->returnValue('200'));

		$this->twitter->setTwitter($twitterOAuth);
		$this->twitter->setUsernames($inputString);

		$this->assertEquals($expectedResult,
			$this->twitter->getUsernameArray());
	}

	public function providerTestSetUsernamesSetsUsernameArray() {

		return array(
			array('', array(), 0),
			array(' ', array(), 0),
			array('username1', array('username1'), 1),
			array('username1 username2 username3',
				array('username1', 'username2', 'username3'),
				3)
			);
	}

	/**
	 * @expectedException Exception
	 */
	public function testSetUsernamesLookupUsersFailureThrowsException() {

		// first prepare the TwitterOAuth mock
		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();

		$twitterOAuth->expects($this->exactly(3))
			->method('get')
			->will($this->returnValue(''));

		$twitterOAuth->expects($this->exactly(3))
			->method('getLastHttpCode')
			->will($this->returnValue('404'));

		$this->twitter->setTwitter($twitterOAuth);
		$this->twitter->setUsernames('username1 username2 username3');
	}

	public function testAuthenticateAppSetsOauthTokenSecret() {

		$expectedOauth2ReturnCreds = json_decode(json_encode(array('access_token' => 'value')));

		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();

		$twitterOAuth->expects($this->once())
			->method('oauth2')
			->will($this->returnValue($expectedOauth2ReturnCreds));

		$twitterOAuth->expects($this->once())
			->method('getLastHttpCode')
			->will($this->returnValue('200'));

		$authArray = $this->twitter->authenticateApp($twitterOAuth);

		$this->assertArrayHasKey('oauthTokenSecret', $authArray);
		$this->assertEquals($authArray['oauthTokenSecret'], 'value');
	}

	/**
	 * @expectedException Exception
	 */
	public function testAuthenticateAppFailureThrowsException() {

		$expectedOauth2ReturnCreds = json_decode(json_encode(array('access_token' => 'value')));

		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();

		$twitterOAuth->expects($this->once())
			->method('oauth2')
			->will($this->returnValue($expectedOauth2ReturnCreds));

		$twitterOAuth->expects($this->once())
			->method('getLastHttpCode')
			->will($this->returnValue('404'));

		$authArray = $this->twitter->authenticateApp($twitterOAuth);
	}

	public function testSearchReturnsArray() {

		$numTweets = 3;
		$oembedResponse = json_decode(json_encode(array(
			'html' => 'tweetBlock')));
		$getResponse = json_decode(json_encode(array(
			'statuses' => array(
				array(
					'id' => 'idval',
					'text' => 'textval',
					'user' => array('name' => 'nameval',
						'screen_name' => 'snval'),
					'created_at' => 'createval'),
				array(  
                                        'id' => 'idval2',
                                        'text' => 'textval2',
                                        'user' => array('name' => 'nameval2',
                                                'screen_name' => 'snval2'),
                                        'created_at' => 'createval2'),
				array(  
                                        'id' => 'idval3',
                                        'text' => 'textval3',
                                        'user' => array('name' => 'nameval3',
                                                'screen_name' => 'snval3'),
                                        'created_at' => 'createval3')))));

		// first prepare the TwitterOAuth mock
                $twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
                        ->setConstructorArgs(array($this->twitter->getConsumerKey(),
                                $this->twitter->getConsumerSecret()))
                        ->getMock();

                $twitterOAuth->expects($this->once())
                        ->method('get')
                        ->will($this->returnValue($getResponse));

                $this->twitter->setTwitter($twitterOAuth);

		// then prepare the curl mock
		$curl = $this->getMockBuilder('curl')
			->getMock();

		$curl->expects($this->exactly($numTweets))
			->method('initiate');
		$curl->expects($this->exactly($numTweets))
			->method('setOptArray');
		$curl->expects($this->exactly($numTweets))
                        ->method('execute')
			->will($this->returnValue($oembedResponse));
		$curl->expects($this->exactly($numTweets))
                        ->method('getErrorNumber')
			->will($this->returnValue(false));
		$curl->expects($this->exactly($numTweets))
                        ->method('close');

		$this->twitter->setCurl($curl);

		$searchResults = $this->twitter->search('', 3, 'term', '', false);

		$this->assertEquals(3, sizeof($searchResults));
		$this->assertEquals('array', typeof($searchResults));
	}

	public function testSearchSearchArray() {

		$numTweets = 3;
                $oembedResponse = json_decode(json_encode(array(
                        'html' => 'tweetBlock')));
                $getResponse = json_decode(json_encode(array(
                        'statuses' => array(
                                array(
                                        'id' => 'idval',
                                        'text' => 'textval',
                                        'user' => array('name' => 'nameval',
                                                'screen_name' => 'snval'),
                                        'created_at' => 'createval'),
                                array(
                                        'id' => 'idval2',
                                        'text' => 'textval2',
                                        'user' => array('name' => 'nameval2',
                                                'screen_name' => 'snval2'),
                                        'created_at' => 'createval2'),
                                array(
                                        'id' => 'idval3',
                                        'text' => 'textval3',
                                        'user' => array('name' => 'nameval3',
                                                'screen_name' => 'snval3'),
                                        'created_at' => 'createval3')))));

                // first prepare the TwitterOAuth mock
                $twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
                        ->setConstructorArgs(array($this->twitter->getConsumerKey(),
                                $this->twitter->getConsumerSecret()))
                        ->getMock();

                $twitterOAuth->expects($this->once())
                        ->method('get')
                        ->will($this->returnValue($getResponse));

		$this->twitter->setTwitter($twitterOAuth);

                // then prepare the curl mock
                $curl = $this->getMockBuilder('curl')
                        ->getMock();

                $curl->expects($this->exactly($numTweets))
                        ->method('initiate');
                $curl->expects($this->exactly($numTweets))
                        ->method('setOptArray');
                $curl->expects($this->exactly($numTweets))
                        ->method('execute')
                        ->will($this->returnValue($oembedResponse));
                $curl->expects($this->exactly($numTweets))
                        ->method('getErrorNumber')
                        ->will($this->returnValue(false));
                $curl->expects($this->exactly($numTweets))
                        ->method('close');

                $this->twitter->setCurl($curl);

                // test with no retweets filter off and geocode on
                $searchResults = $this->twitter->search('username', 3, 'term to search', '36.778259,-119.417931', false);

		$expectedResult = array('q' => 'from:username term to search',
			'count' => 3,
			'geocode' => '36.778259,-119.417931,5mi');
		$this->assertEquals($expectedResult, $this->twitter->getSearchArray());

		// test with no retweets filter on and geocode off
		$searchResults = $this->twitter->search('username', 3, 'term to search', '', true);

                $expectedResult = array('q' => 'from:username term to search -filter:retweets',
                        'count' => 3);
                $this->assertEquals($expectedResult, $this->twitter->getSearchArray());
		
	}

	public function testSearchOembedFailureReturnsTextTweet() {

		$numTweets = 1;
		$oembedResponse = json_decode(json_encode(array(
			'html' => 'tweetBlock')));
		$getResponse = json_decode(json_encode(array(
			'statuses' => array(
				array(
					'id' => 'idval',
					'text' => 'textval',
					'user' => array('name' => 'nameval',
						'screen_name' => 'snval'),
					'created_at' => 'createval')
				))));
		
		// first prepare the TwitterOAuth mock
		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();
		
		$twitterOAuth->expects($this->once())
			->method('get')
			->will($this->returnValue($getResponse));
		
		$this->twitter->setTwitter($twitterOAuth);
		
		// then prepare the curl mock
		$curl = $this->getMockBuilder('curl')
			->getMock();
		
		$curl->expects($this->exactly($numTweets))
			->method('initiate');
		$curl->expects($this->exactly($numTweets))
			->method('setOptArray');
		$curl->expects($this->exactly($numTweets))
			->method('execute')
			->will($this->returnValue($oembedResponse));
		$curl->expects($this->exactly($numTweets))
			->method('getErrorNumber')
			->will($this->returnValue('1'));
		$curl->expects($this->exactly($numTweets))
			->method('close');
		
		$this->twitter->setCurl($curl);
		
		$searchResults = $this->twitter->search('username', 3, 'term to search', '36.778259,-119.417931', false);
		
		$expectedResultUrl = 'https://twitter.com/snval/status/idval';
		$expectedResult = '<blockquote class="twitter-tweet" data-lang="en">';
		$expectedResult .= '<p lang="en" dir="ltr">textval</p>';
		$expectedResult .= ' &mdash; nameval (@snval)';
		$expectedResult .= ' <a href="' . $expectedResultUrl . '">createval</a>';
		$expectedResult .= '</blockquote>';
			
		$this->assertEquals(1, sizeof($searchResults));
		$this->assertEquals($expectedResult, $searchResults[0]);
	}

	/**
	 * @expectedException Exception
	 */
	public function testSearchThrowsExceptionOnNoResults() {

		$numTweets = 1;
		$getResponse = json_decode(json_encode(array(
				'statuses' => array())));
		
		// first prepare the TwitterOAuth mock
		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();
		
		$twitterOAuth->expects($this->once())
			->method('get')
			->will($this->returnValue($getResponse));
		
		$this->twitter->setTwitter($twitterOAuth);
		
		$searchResults = $this->twitter->search('', 3, 'term to search', '36.778259,-119.417931', false);
	}
	
	public function testSearchReturnsOembedBlock() {
		
		$numTweets = 1;
		$oembedResponse = json_decode(json_encode(array(
			'html' => 'tweetBlock')));
		$getResponse = json_decode(json_encode(array(
			'statuses' => array(
				array(
					'id' => 'idval',
					'text' => 'textval',
					'user' => array('name' => 'nameval',
						'screen_name' => 'snval'),
					'created_at' => 'createval')
				))));
		
		// first prepare the TwitterOAuth mock
		$twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
			->setConstructorArgs(array($this->twitter->getConsumerKey(),
				$this->twitter->getConsumerSecret()))
			->getMock();
		
		$twitterOAuth->expects($this->once())
			->method('get')
			->will($this->returnValue($getResponse));
		
		$this->twitter->setTwitter($twitterOAuth);
		
		// then prepare the curl mock
		$curl = $this->getMockBuilder('curl')
			->getMock();
		
		$curl->expects($this->exactly($numTweets))
			->method('initiate');
		$curl->expects($this->exactly($numTweets))
			->method('setOptArray');
		$curl->expects($this->exactly($numTweets))
			->method('execute')
			->will($this->returnValue($oembedResponse));
		$curl->expects($this->exactly($numTweets))
			->method('getErrorNumber')
			->will($this->returnValue(false));
		$curl->expects($this->exactly($numTweets))
			->method('close');
		
		$this->twitter->setCurl($curl);
		
		$searchResults = $this->twitter->search('username', 3, 'term to search', '36.778259,-119.417931', false);
			
		$this->assertEquals(1, sizeof($searchResults));
		$this->assertEquals('tweetBlock', $searchResults[0]);
	}
}
?>
