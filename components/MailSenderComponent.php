<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Setting;

class MailSenderComponent extends Component
{
    public function sendWithAttachment($to, $subject, $viewData, $fileName)
    {
        $filePath = Yii::getAlias('@pdf/') . $fileName;

        if (!is_file($filePath)) {
            Yii::error("Файл не найден для отправки: $filePath");
            return false;
        }

        try {
            $message = Yii::$app->mailer->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => Setting::findOne(['key' => 'app_name'])->value])
                ->setTo($to)
                ->setSubject($subject)
                ->setTextBody($viewData['text'] ?? '')
                ->attach($filePath, ['fileName' => $fileName]);

            return $message->send();
        } catch (\Exception $e) {
            Yii::error("Ошибка MailSender: " . $e->getMessage());
            return false;
        }
    }
}