<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\forms;

use Yii;
use yii\base\Model;
use common\modules\catalogs\DataManager;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\plugins\tours\models\Date;
use common\modules\catalogs\plugins\tours\models\Person;
use common\modules\catalogs\plugins\tours\models\Booking as BookingModel;
use NXP\MathExecutor;

/**
 * Tour booking form
 *
 * @author Maxim Chichkanov
 */
class Booking extends Model
{
    /**
     * @var boolean
     */
    public $agreement;

    /**
     * @var integer
     */
    public $date_id;

    /**
     * @var string
     */
    public $note;

    /**
     * @var BookingModel|null
     */
    protected $_model;

    /**
     * @var Date|null
     */
    protected $_date;

    /**
     * @var Item
     */
    protected $_item;

    /**
     * @var Person
     */
    protected $_customer;

    /**
     * @var Person[]
     */
    protected $_persons = [];

    /**
     * Constructor.
     *
     * @param Item $item
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct(Item $item, $config = [])
    {
        parent::__construct($config);
        $this->_item = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['date_id', 'required'],
            ['date_id', 'exist', 'targetClass' => Date::class, 'targetAttribute' => 'id'],

            ['note', 'trim'],
            ['note', 'string', 'max' => 500],

            ['agreement', 'required', 'requiredValue' => true,
                'message' => 'Мы не можем обрабатывать данные без Вашего согласия'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date_id' => 'Дата',
            'note' => 'Примечание'
        ];
    }

    /**
     * @param Person $person
     * @return void
     */
    public function setCustomer(Person $person)
    {
        $this->_customer = $person;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * @param BookingModel $model
     * @return void
     */
    public function setModel(BookingModel $model)
    {
        $this->_model = $model;
    }

    /**
     * @return BookingModel|null
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param Date $date
     * @return void
     */
    public function setDate(Date $date)
    {
        $this->_date = $date;
    }

    /**
     * @return Date|null
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * @param Person[] $person
     * @return void
     */
    public function addPerson(Person $person)
    {
        $this->_persons[] = $person;
    }

    /**
     * @return Person[]
     */
    public function getPersons()
    {
        return $this->_persons;
    }

    /**
     * @return boolean
     */
    public function loadAll($data)
    {
        return $this->load($data) && $this->loadCustomer($data) && $this->loadPersons($data);
    }

    /**
     * @return boolean
     */
    public function loadCustomer($data)
    {
        $model = new Customer();
        $loaded = $model->load($data);

        $this->setCustomer($model);

        return $loaded;
    }

    /**
     * @return boolean
     */
    public function loadPersons($data)
    {
        if (!isset($data['Person']) || !is_array($data['Person'])) {
            return true;
        }

        $loaded = true;

        foreach ($data['Person'] as $person) {
            $model = new Person();
            if ($model->load($person, '')) {
                $this->addPerson($model);
            } else {
                $loaded = false;
            }
        }

        return $loaded;
    }

    /**
     * @return boolean
     */
    public function validateAll()
    {
        return $this->validate() && $this->validateCustomer() && $this->validatePersons();
    }

    /**
     * @return boolean
     */
    public function validateCustomer()
    {
        return $this->getCustomer()->validate();
    }

    /**
     * @return boolean
     */
    public function validatePersons()
    {
        $validated = true;

        foreach ($this->getPersons() as $person) {
            if (!$person->validate()) {
                $validated = false;
            }
        }

        return $validated;
    }

    /**
     * @return boolean
     */
    public function hasErrorsAll()
    {
        return $this->hasErrors() || $this->hasErrorsCustomer() || $this->hasErrorsPersons();
    }

    /**
     * @return boolean
     */
    public function hasErrorsCustomer()
    {
        return $this->getCustomer()->hasErrors();
    }

    /**
     * @return boolean
     */
    public function hasErrorsPersons()
    {
        foreach ($this->getPersons() as $person) {
            if ($person->hasErrors()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param boolean $formatted
     * @return string|integer
     */
    public function calc($formatted = true)
    {
        $dataManager = $this->getItem()->getDataManager();
        $formatter = Yii::$app->getFormatter();

        $nightCount = $dataManager->getValue('night_count');
        $fixPrice = $dataManager->getValue('fix_price');
        $minPrice = $dataManager->getValue('min_price');
        $dayPrice = $dataManager->getValue('day_price');

        if ($this->hasValueStrict($dataManager, 'formula')) {
            $formula = $dataManager->getValue('formula');

            $math = new MathExecutor();

            $math->setVars([
                'nightCount' => (int) ($nightCount ? $nightCount->value : 0),
                'fixPrice' => (int) ($fixPrice ? $fixPrice->value : 0),
                'minPrice' => (int) ($minPrice ? $minPrice->value : 0),
                'dayPrice' => (int) ($dayPrice ? $dayPrice->value : 0),
                'tourists' => count($this->_persons) + 1
            ]);

            $price = $math->execute($formula->value);
            return $formatted ? $formatter->asCurrency($price) : $price;
        } elseif ($this->hasValueStrict($dataManager, 'fix_price')) {
            $fixPrice = (integer) ($fixPrice ? $fixPrice->value : 0);
            return $formatted ? $formatter->asCurrency($fixPrice) : $fixPrice;
        } elseif ($this->hasValueStrict($dataManager, 'min_price')) {
            $minPrice = (integer) ($minPrice ? $minPrice->value : 0);
            return $formatted ? 'от ' . $formatter->asCurrency($minPrice) : $minPrice;
        }

        return $formatted ? '-' : 0;
    }

    /**
     * @param string $email
     * @return boolean
     */
    public function send($email)
    {
        if (!$this->validateAll()) {
            return false;
        }

        $model = new BookingModel();

        $this->setModel($model);

        $date = Date::find()
            ->where(['id' => $this->date_id])
            ->with('bookings')
            ->one();

        $this->setDate($date);

        if (count($date->bookings) > count($this->_persons) + 1) {
            $message = 'На выбранную дату недостаточно мест. Приносим извинения за доставленные неудобства.';
            $this->addError('date_id', $message);
        } else {
            $transaction = Yii::$app->getDb()->beginTransaction();

            $customer = $this->getCustomer();
            if (!$customer->save()) {
                return false;
            }

            $model->token = Yii::$app->security->generateRandomString() . '_' . time();

            $model->item_id = $this->getItem()->id;
            $model->date_id = $date->id;
            $model->customer_id = $customer->id;
            $model->price = $this->calc(false);
            $model->note = $this->note;

            if (!$model->save()) {
                $this->addErrors($model->getErrors());
            } else {
                foreach ($this->getPersons() as $person) {
                    if ($person->save()) {
                        $model->link('persons', $person);
                    }
                }

                $transaction->commit();

                // @todo create template for email message
                // @todo get email address from config ?
                return true;
                // return Yii::$app->mailer->compose('booking')
                //     ->setTo($email)
                //     ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                //     ->setSubject($this->subject)
                //     ->send();
            }
        }

        return !$this->hasErrorsAll();
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @param DataManager $dataManager
     * @param string $value
     * @return boolean
     */
    protected function hasValueStrict(DataManager $dataManager, $value)
    {
        return $dataManager->hasValue($value) && (boolean) $dataManager->getValue($value)->value;
    }
}
