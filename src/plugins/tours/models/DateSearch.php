<?php

namespace common\modules\catalogs\plugins\tours\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DateSearch represents the model behind the search form of `Date`.
 */
class DateSearch extends Date
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                /*'id',*/
                'begin_at',
                'end_at',
                'capacity',
                'price',
                'item_id',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ], 'integer'],

            [[
                'name',
                'surname',
                'patronymic',
                'email',
                'phone',
                'note'
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $conditions = [], $orderBy = 'position', $limit = null)
    {
        $query = Date::find()
            ->where($conditions)
            ->orderBy($orderBy)
            ->limit($limit)
            ->with('item', 'bookings', 'createdBy', 'updatedBy');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'begin_at' => $this->begin_at,
            'end_at' => $this->end_at,
            'capacity' => $this->capacity,
            'price' => $this->price,
            'item_id' => $this->item_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
