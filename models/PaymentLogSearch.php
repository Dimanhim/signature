<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaymentLog;

class PaymentLogSearch extends PaymentLog
{
    public function rules()
    {
        return [
            [['id', 'appointment_id', 'patient_id'], 'integer'],
            [['invoice_number', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PaymentLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50, // Показываем по 50 записей, чтобы страница не висла
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Самые свежие логи — всегда сверху
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Фильтрация по точным ID и номеру счета
        $query->andFilterWhere([
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
        ]);

        $query->andFilterWhere(['like', 'invoice_number', $this->invoice_number]);

        return $dataProvider;
    }
}
