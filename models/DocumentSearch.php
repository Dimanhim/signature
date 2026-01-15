<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IndustrySearch represents the model behind the search form of `app\models\Industry`.
 */
class DocumentSearch extends Document
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appointment_id', 'template_id', 'tablet_id', 'patient_id', 'patient_name', 'patient_birthday', 'document_name','content', 'full_content', 'is_signature'], 'safe'],
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
    public function search($params, $tablet_id)
    {
        $query = Document::findModels();

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

        if($tablet_id) {
            $query->andWhere(['tablet_id' => $tablet_id]);
        }

        if(isset($this->is_signature)) {
            if($this->is_signature == '0') {
                $query->andWhere(['is_signature' => null]);
            }
            elseif($this->is_signature == '1') {
                $query->andWhere(['is_signature' => 1]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'tablet_id' => $this->tablet_id,
            'template_id' => $this->template_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
        ->andFilterWhere(['like', 'full_content', $this->full_content]);

        return $dataProvider;
    }
}

