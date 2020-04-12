<?php

namespace common\modules\catalogs\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CatalogSearch represents the model behind the search form of `Catalog`.
 */
class CatalogSearch extends Catalog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                /*'id',*/
                'hits',
                'image_id',
                'category_id',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ], 'integer'],

            [[
                'name',
                'alias',
                'description',
                'keywords'
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
        $query = Catalog::find()
            ->where($conditions)
            ->orderBy($orderBy)
            ->limit($limit)
            ->with('image', 'createdBy', 'updatedBy');

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
            'hits' => $this->hits,
            'category_id' => $this->category_id,
            'image_id' => $this->image_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'keywords', $this->keywords]);

        return $dataProvider;
    }
}
