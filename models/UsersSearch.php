<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'is_phone_verified', 'is_email_verified', 'is_social_register', 'is_active', 'is_deleted'], 'integer'],
            [['name', 'email', 'phone', 'password', 'social_register_type', 'gender', 'dob', 'otp_code', 'created_at'], 'safe'],
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
        $query = Users::find();

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
            'user_id' => $this->user_id,
            'is_phone_verified' => $this->is_phone_verified,
            'is_email_verified' => $this->is_email_verified,
            'is_social_register' => $this->is_social_register,
            'dob' => $this->dob,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'social_register_type', $this->social_register_type])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'otp_code', $this->otp_code]);

        return $dataProvider;
    }
}
