<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'deleted', 'blocked'], 'integer'],
            [['password', 'displayName', 'email', 'phone', 'created_at', 'updated_at'], 'safe'],
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
        $query = User::find()->select([
            'users.id',
            'users.deleted',
            'users.blocked',
            'users.displayName',
            'users.email',
            'users.phone',
            'users.created_at',
            'users.updated_at',
            'auth_assignment.item_name as role'
        ]);

        $query->join('LEFT OUTER JOIN','auth_assignment','auth_assignment.user_id = users.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if(isset($params['search'])){
            $search = $params['search'];
            $query->andFilterWhere(['like', 'displayName', $search])
                ->orFilterWhere(['like', 'email', $search])
                ->orFilterWhere(['like', 'phone', $search]);
        }

        return $dataProvider;
    }
}
