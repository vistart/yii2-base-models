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

namespace rhosocial\base\models\tests\data\ar\blameable;

use rhosocial\base\models\tests\data\ar\User;
use rhosocial\base\models\models\BaseBlameableModel;

/**
 * @author vistart <i@vistsart.me>
 */
class UserComment extends BaseBlameableModel
{
    public $idAttributeLength = 255;
    
    public $parentAttribute = 'parent_guid';
    
    public function init()
    {
        $this->userClass = User::class;
        parent::init();
        $this->setContent(\Yii::$app->security->generateRandomString());
    }
    
    public static function tableName()
    {
        return '{{%user_comment}}';
    }
    
    /**
     * Friendly to IDE.
     * @return \rhosocial\base\models\queries\BaseBlameableQuery
     */
    public static function find()
    {
        return parent::find();
    }
}