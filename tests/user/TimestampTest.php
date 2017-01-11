<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\base\models\tests\user;

use rhosocial\base\models\tests\data\ar\User;
use rhosocial\base\models\tests\data\ar\ExpiredUser;
use rhosocial\base\models\tests\user\UserTestCase;

/**
 * @author vistart <i@vistart.me>
 */
class TimestampTest extends UserTestCase
{
    protected function setUp() {
        parent::setUp();
        $this->user = new ExpiredUser();
    }
    /**
     * @group user
     * @group registration
     * @group timestamp
     * @param integer $severalTimes
     * @dataProvider timestampProvider
     */
    public function testAfterRegister($severalTimes)
    {
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertTrue($this->user->register());
        $this->assertNotNull($this->user->getCreatedAt());
        $this->assertNotNull($this->user->getUpdatedAt());
        $this->assertEquals($this->user->getCreatedAt(), $this->user->getUpdatedAt());
        $this->assertTrue($this->user->deregister());
    }
    
    /**
     * @group user
     * @group timestamp
     * @param integer $severalTimes
     * @dataProvider timestampProvider
     */
    public function testNoCreatedAt($severalTimes)
    {
        $this->user = new ExpiredUser(['createdAtAttribute' => false]);
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertTrue($this->user->register());
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNotNull($this->user->getUpdatedAt());
        $this->assertNotEquals($this->user->getCreatedAt(), $this->user->getUpdatedAt());
        $this->assertTrue($this->user->deregister());
    }
    
    /**
     * @group user
     * @group timestamp
     * @param integer $severalTimes
     * @dataProvider timestampProvider
     */
    public function testNoUpdatedAt($severalTimes)
    {
        $this->user = new ExpiredUser(['updatedAtAttribute' => false]);
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertTrue($this->user->register());
        $this->assertNotNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertNotEquals($this->user->getCreatedAt(), $this->user->getUpdatedAt());
        $this->assertTrue($this->user->deregister());
    }
    
    /**
     * @group user
     * @group timestamp
     * @param integer $severalTimes
     * @dataProvider timestampProvider
     */
    public function testNoTimestamp($severalTimes)
    {
        $this->user = new ExpiredUser(['createdAtAttribute' => false, 'updatedAtAttribute' => false]);
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertTrue($this->user->register());
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertEquals($this->user->getCreatedAt(), $this->user->getUpdatedAt());
        $this->assertTrue($this->user->deregister());
    }

    /**
     * @group user
     * @group timestamp
     * @param integer $severalTimes
     * @dataProvider timestampProvider
     */
    public function testRemoveIfExpired($severalTimes)
    {
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getUpdatedAt());
        $this->assertFalse($this->user->getIsExpired());
        $this->assertTrue($this->user->register());
        $this->assertNotNull($this->user->getCreatedAt());
        $this->assertNotNull($this->user->getUpdatedAt());
        $this->assertEquals($this->user->getCreatedAt(), $this->user->getUpdatedAt());
        $this->user->setExpiredAfter(1);
        sleep(2);
        $this->assertFalse($this->user->getIsNewRecord());
        $this->assertTrue($this->user->getIsExpired());
        $this->assertTrue($this->user->refresh());
        $this->assertTrue($this->user->getIsNewRecord());
        $this->assertFalse($this->user->deregister());
    }
    
    /**
     * @group user
     * @group timestamp
     */
    public function testEnabledFields()
    {
        $this->assertNotEmpty($this->user->enabledTimestampFields());
    }
    
    /**
     * @group user
     * @group timestamp
     */
    public function testNoExpiration()
    {
        $this->user = new User(['password' => '123456']);
        $this->user->setExpiredAfter(1);
    }
    
    public function timestampProvider()
    {
        for ($i = 0; $i < 3; $i++)
        {
            yield [$i];
        }
    }
}