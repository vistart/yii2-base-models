<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\base\models\tests\user\relation;

use rhosocial\base\models\tests\data\ar\User;
use rhosocial\base\models\tests\data\ar\relation\UserSingleRelation;
use rhosocial\base\models\tests\user\UserTestCase;

/**
 * @author vistart <i@vistart.me>
 */
class SingleRelationTestCase extends UserTestCase
{
    /**
     * @var User 
     */
    protected $other = null;
    
    /**
     * @var UserSingleRelation 
     */
    protected $relation = null;
    
    protected function setUp()
    {
        parent::setUp();
        $this->other = new User(['password' => '123456']);
        $this->relation = $this->prepareSingleRelation($this->user, $this->other);
    }
    
    /**
     * Prepare single relation.
     * @param User $user
     * @param User $other
     */
    protected function prepareSingleRelation($user, $other)
    {
        return UserSingleRelation::buildNormalRelation($user, $other);
    }
    
    protected function tearDown()
    {
        if ($this->relation instanceof UserSingleRelation) {
            $this->relation->remove();
        }
        $this->relation = null;
        if ($this->other instanceof User) {
            try {
                $this->other->deregister();
            } catch (\Exception $ex) {

            } finally {
                $this->other = null;
            }
        }
        parent::tearDown();
    }
}