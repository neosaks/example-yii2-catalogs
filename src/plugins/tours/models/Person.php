<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\models;

use Yii;
use common\behaviors\PositionBehavior;
use common\behaviors\StatusBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Tour person
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $patronymic Optional
 * @property string $email Optional
 * @property string $phone Optional
 * @property integer $sex Optional
 * @property integer $birthday Optional
 * @property string $note Optional
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Maxim Chichkanov
 */
class Person extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const SEX_FEMALE = 0;
    const SEX_MALE = 1;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_tours_persons}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'blameable' => BlameableBehavior::class,
            'timestamp' => TimestampBehavior::class,
            'position' => PositionBehavior::class,
            'status' => StatusBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],

            ['surname', 'required'],
            ['surname', 'string', 'max' => 50],

            ['patronymic', 'string', 'max' => 50],

            ['email', 'string', 'max' => 100],
            ['phone', 'string', 'max' => 20],
            ['email', 'email'],

            ['sex', 'in', 'range' => [self::SEX_FEMALE, self::SEX_MALE]],

            ['birthday', 'date', 'timestampAttribute' => 'birthday'],

            ['note', 'string', 'max' => 500],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'email' => 'E-Mail',
            'phone' => 'Телефон',
            'sex' => 'Пол',
            'birthday' => 'День рождения',
            'note' => 'Примечание',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён'
        ];
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getCreatedBy()
    {
        $identityClass = Yii::$app->user->identityClass;

        return $this->hasOne($identityClass, [
            'id' => 'created_by'
        ]);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getUpdatedBy()
    {
        $identityClass = Yii::$app->user->identityClass;

        return $this->hasOne($identityClass, [
            'id' => 'updated_by'
        ]);
    }

    /**
     * Description
     *
     * @return array
     */
    public static function getList()
    {
        $list = self::find()->orderBy('position')->asArray()->all();
        return ArrayHelper::map($list, 'id', function ($item, $defaultValue) {
            return $item['name'] . ' ' . $item['surname'];
        });
    }
}
