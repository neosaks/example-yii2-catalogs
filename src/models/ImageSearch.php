<?php

namespace common\modules\catalogs\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ImageSearch represents the model behind the search form of `Image`.
 */
class ImageSearch extends Image
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                /*'id',*/
                'size',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ], 'integer'],

            [[
                'name',
                'description',
                'sub_directory',
                'resolution',
                'filename',
                'basename',
                'extension'
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
        $query = Image::find()
            ->where($conditions)
            ->orderBy($orderBy)
            ->limit($limit)
            ->with('createdBy', 'updatedBy');

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
            'size' => $this->size,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'sub_directory', $this->sub_directory])
            ->andFilterWhere(['like', 'resolution', $this->resolution])
            ->andFilterWhere(['like', 'filename', $this->filename])
            ->andFilterWhere(['like', 'basename', $this->basename])
            ->andFilterWhere(['like', 'extension', $this->extension]);

        return $dataProvider;
    }
}
