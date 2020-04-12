<?php

namespace common\modules\catalogs\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DataSearch represents the model behind the search form of `Data`.
 */
class DataSearch extends Data
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                // 'id',
                'attribute_id',
                'item_id',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ], 'integer'],
            [[
                'value',
                'format',
                // 'params'
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
        $query = Data::find()
            ->where($conditions)
            ->orderBy($orderBy)
            ->limit($limit)
            ->with('attribute', 'item', 'createdBy', 'updatedBy');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
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
            'attribute_id' => $this->attribute_id,
            'item_id' => $this->item_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'format', $this->format])
            /*->andFilterWhere(['like', 'params', $this->params])*/;

        return $dataProvider;
    }
}
