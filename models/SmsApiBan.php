<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sms_not_to_send}}".
 *
 * @property integer $id
 * @property string $phone
 * @property integer $user_id
 */
class SmsApiBan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $session = Yii::$app->session;

        return $session->get('city_module')."_sms_not_to_send";

    }
public function Is_in_ban_list ($phone) {

    $session = Yii::$app->session;
    return SmsApiBan::find()->where(['user_id' => $session->get('user_id')])
        ->andFilterWhere(['phone' => $phone])
        ->exists();


}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'user_id' => 'User ID',
        ];
    }
}
