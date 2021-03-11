<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Service;

/**
 * ServiceSearch represents the model behind the search form of `app\models\Service`.
 */
class ServiceSearch extends Service
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'type', 'link_type', 'sort_order', 'is_active', 'is_deleted'], 'integer'],
            [['name', 'title', 'description', 'image', 'bg_color', 'text_color', 'secndary_text_color', 'button_bg_color', 'button_text_color', 'link', 'created_at'], 'safe'],
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
    public function search($params)
    {
        $query = Service::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['sort_order' => 'ASC']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'service_id' => $this->service_id,
            'type' => $this->type,
            'link_type' => $this->link_type,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'bg_color', $this->bg_color])
            ->andFilterWhere(['like', 'text_color', $this->text_color])
            ->andFilterWhere(['like', 'secndary_text_color', $this->secndary_text_color])
            ->andFilterWhere(['like', 'button_bg_color', $this->button_bg_color])
            ->andFilterWhere(['like', 'button_text_color', $this->button_text_color])
            ->andFilterWhere(['like', 'link', $this->link]);

        return $dataProvider;
    }
}
