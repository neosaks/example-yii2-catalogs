<?php

namespace common\modules\catalogs\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AttributeSearch represents the model behind the search form of `Attribute`.
 */
class AttributeSearch extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                /*'id',*/
                'unique',
                'trim',
                'required',
                'catalog_id',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ], 'integer'],

            [[
                'name',
                'description',
                'type',
                // 'params',
                'placeholder',
                'hint',
                'label',
                'default_value'
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
        $query = Attribute::find()
            ->where($conditions)
            ->orderBy($orderBy)
            ->limit($limit)
            ->with('catalog', 'createdBy', 'updatedBy');

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
            'unique' => $this->unique,
            'trim' => $this->trim,
            'required' => $this->required,
            'catalog_id' => $this->catalog_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'type', $this->type])
            // ->andFilterWhere(['like', 'params', $this->params])
            ->andFilterWhere(['like', 'placeholder', $this->placeholder])
            ->andFilterWhere(['like', 'hint', $this->hint])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'default_value', $this->default_value]);

        return $dataProvider;
    }
}
