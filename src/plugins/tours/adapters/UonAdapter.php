<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\adapters;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use common\modules\catalogs\plugins\tours\forms\Booking;
use common\modules\catalogs\plugins\tours\CrmAdapterInterface;
use common\modules\catalogs\plugins\tours\models\Person;
use UON\Config;
use UON\API;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class UonAdapter extends Component implements CrmAdapterInterface
{
    /**
     * @param string
     */
    public $token;

    /**
     * @param array|Config
     */
    public $config = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!$this->config instanceof Config) {
            $this->config = new Config($this->config);
        }

        $this->config->set('token', $this->token);
    }

    /**
     * @param Booking $model
     * @return boolean
     */
    public function send(Booking $model)
    {
        $client = new API($this->config);
        $tourists = [];

        foreach ($model->getPersons() as $person) {
            $tourists[] = [
                'u_name' => $person->name,
                'u_surname' => $person->surname,
                'u_sname' => $person->patronymic,
                'u_birthday' => $this->asDate($person->birthday),
                'u_sex' => $this->asSex($person->sex),
                'u_note' => $person->note
            ];
        }

        $moduleId = Yii::$app->controller->module->id;
        $url = ["$moduleId/items/view", 'id' => $model->getItem()->id];

        $response = $client->requests->create([
            'r_dat_begin' => $this->asDate($model->getDate()->begin_at, true),
            'r_dat_end' => $this->asDate($model->getDate()->end_at, true),
            'r_tour_operator_link' => Url::to($url, true),
            'r_reservation_number' => $model->getModel()->id,
            'source' => Yii::$app->name,
            'price' => $model->calc(false),
            'note' => $model->note,
            'u_surname' => $model->getCustomer()->surname,
            'u_sname' => $model->getCustomer()->patronymic,
            'u_name' => $model->getCustomer()->name,
            'u_phone_mobile' => $model->getCustomer()->phone,
            'u_email' => $model->getCustomer()->email,
            'u_note' => $model->getCustomer()->note,
            'u_sex' => $this->asSex($model->getCustomer()->sex),
            'u_birthday' => $this->asDate($model->getCustomer()->birthday),
            'tourists' => $tourists
        ]);

        return $this->isSuccess($response);
    }

    /**
     * @param mixed $reponse
     * @return boolean
     */
    protected function isSuccess($response)
    {
        return is_array($response) && isset($response['code']) && $response['code'] === 200;
    }

    /**
     * @param integer $timestamp
     * @param boolean $time
     * @return string
     */
    protected function asDate($timestamp, $time = false)
    {
        return date($time ? 'Y-m-d H:i:s' : 'Y-m-d', (integer) $timestamp);
    }

    /**
     * @param integer $sex
     * @return string
     */
    protected function asSex($sex)
    {
        return $sex == Person::SEX_MALE ? 'm' : 'f';
    }
}
