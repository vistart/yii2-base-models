<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\base\models\traits;

use rhosocial\base\helpers\Number;
use Yii;
use yii\base\ModelEvent;

/**
 * Entity features concerning ID.
 * @property-read array $idRules
 * @property mixed $ID
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait IDTrait
{
    /**
     * @var string OPTIONAL. The attribute that will receive the IDentifier No.
     * You can set this property to false if you don't use this feature.
     */
    public $idAttribute = 'id';
    public static $idTypeString = 0;
    public static $idTypeInteger = 1;
    public static $idTypeAutoIncrement = 2;

    /**
     * @var integer type of id attribute.
     */
    public $idAttributeType = 0;

    /**
     * @var boolean Determines whether its ID has been pre-assigned. It will not
     * generate or assign ID if true.
     */
    public $idPreassigned = false;

    /**
     * @var string The prefix of ID. When ID type is Auto Increment, this feature
     * is skipped.
     */
    public $idAttributePrefix = '';

    /**
     * @var integer OPTIONAL. The length of id attribute value, and max length
     * of this attribute in rules. If you set $idAttribute to false or ID type
     * to Auto Increment, this property will be ignored.
     */
    public $idAttributeLength = 4;

    /**
     * @var boolean Determine whether the ID is safe for validation.
     */
    protected $idAttributeSafe = false;

    /**
     * Get ID.
     * @return string|integer
     */
    public function getID()
    {
        $idAttribute = $this->idAttribute;
        return (is_string($idAttribute) && !empty($idAttribute)) ? $this->$idAttribute : null;
    }

    /**
     * Set id.
     * @param string|integer $identity
     * @return string|integer
     */
    public function setID($identity)
    {
        $idAttribute = $this->idAttribute;
        return (is_string($idAttribute) && !empty($idAttribute)) ? $this->$idAttribute = $identity : null;
    }

    /**
     * Attach `onInitGuidAttribute` event.
     * @param string $eventName
     */
    protected function attachInitIDEvent($eventName)
    {
        $this->on($eventName, [$this, 'onInitIDAttribute']);
    }

    /**
     * Initialize the ID attribute with new generated ID.
     * If the model's id is pre-assigned, then it will return directly.
     * If the model's id is auto-increment, the id attribute will be marked safe.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param ModelEvent $event
     */
    public function onInitIDAttribute($event)
    {
        $sender = $event->sender;
        /* @var $sender static */
        if ($sender->idPreassigned) {
            return;
        }
        if ($sender->idAttributeType === static::$idTypeAutoIncrement) {
            $sender->idAttributeSafe = true;
            return;
        }
        $idAttribute = $sender->idAttribute;
        if (is_string($idAttribute) && !empty($idAttribute) &&
            is_int($sender->idAttributeLength) &&
            $sender->idAttributeLength > 0) {
            $sender->setID($sender->generateId());
        }
    }

    /**
     * Generate the ID. You can override this method to implement your own
     * generation algorithm.
     * @return string the generated ID.
     */
    public function generateId()
    {
        if ($this->idAttributeType == static::$idTypeInteger) {
            do {
                $result = Number::randomNumber($this->idAttributePrefix, $this->idAttributeLength);
            } while ($this->checkIdExists((int) $result));
            return $result;
        }
        if ($this->idAttributeType == static::$idTypeString) {
            return $this->idAttributePrefix .
                Yii::$app->security->generateRandomString($this->idAttributeLength - strlen($this->idAttributePrefix));
        }
        if ($this->idAttributeType == static::$idTypeAutoIncrement) {
            return null;
        }
        return false;
    }
    
    /**
     * Check if $identity existed.
     * @param mixed $identity
     * @return boolean
     */
    public function checkIdExists($identity)
    {
        if ($identity == null) {
            return false;
        }
        return static::find()->where([$this->idAttribute => $identity])->exists();
    }

    /**
     * Get the rules associated with id attribute.
     * @return array
     */
    public function getIdRules()
    {
        if ($this->idAttribute == false) {
            return [];
        }
        if ($this->idAttributeSafe || $this->idAttributeType === static::$idTypeAutoIncrement) {
            return [
                [[$this->idAttribute], 'safe'],
            ];
        }
        if (is_string($this->idAttribute) && !empty($this->idAttribute) &&
            is_int($this->idAttributeLength) &&
            $this->idAttributeLength > 0) {
            $rules = [
                [[$this->idAttribute], 'required'],
                [[$this->idAttribute], 'unique'],
            ];
            if ($this->idAttributeType === static::$idTypeInteger) {
                $rules[] = [
                    [$this->idAttribute], 'number', 'integerOnly' => true
                ];
            }
            if ($this->idAttributeType === static::$idTypeString) {
                $rules[] = [[$this->idAttribute], 'string',
                    'max' => $this->idAttributeLength,];
            }
            return $rules;
        }
        return [];
    }

    /**
     * Composite IDs from models.
     * @param $models
     * @return array|int|string
     */
    public static function compositeIDs($models)
    {
        if (!is_array($models) && $models instanceof static) {
            return $models->getID();
        }
        $ids = [];
        foreach ($models as $model) {
            if ($model instanceof static) {
                $ids[] = $model->getID();
            }
        }
        return $ids;
    }
}

