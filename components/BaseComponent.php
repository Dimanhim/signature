<?php

namespace app\components;

use yii\base\Component;

class BaseComponent extends Component
{
    public $_errors = [];
    public $_data = [
        'error' => false,
        'message' => null,
        'data' => [],
    ];

    public function getData()
    {
        $this->_data['error'] = $this->_hasErrors();
        $this->_data['message'] = $this->_errorSummary();
        return $this->_data;
    }

    public function _hasErrors()
    {
        return !empty($this->_errors);
    }

    public function _addError($message)
    {
        if($message) {
            $this->_errors[] = $message;
        }
    }

    public function _errorSummary()
    {
        if($this->_errors) return implode(' ', $this->_errors);
        return false;
    }
}
