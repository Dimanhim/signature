<?php

namespace app\models;

use Yii;

class User extends BaseModel implements \yii\web\IdentityInterface
{
    public $accessToken;
    public $role;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'user';
    }

    /**
     * @return int
     */
    public static function typeId()
    {
        return Gallery::TYPE_USER;
    }

    public static function modelName()
    {
        return 'Пользователь';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['username'], 'required'],
            [['password'], 'required', 'on' => 'create'],
            [['username', 'password', 'password_hash', 'email', 'role'], 'string', 'max' => 255],
            [['clinic_id', 'default_tablet_id'], 'safe'],
            [['status'], 'integer'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'username' => 'Логин',
            'password_hash' => 'Пароль',
            'password' => 'Пароль',
            'role' => 'Роль',
            'email' => 'E-mail',
            'status' => 'Статус',
            'clinic_id' => 'Филиал',
            'default_tablet_id' => 'Планшет по умолчанию',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
        //return $this->password === $password;
    }

    public function beforeSave($insert)
    {
        if ($this->password) {
            $this->password_hash = \Yii::$app->security->generatePasswordHash($this->password);
        }
        if (!$this->auth_key) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $auth = Yii::$app->authManager;
        $userRoles = $auth->getRolesByUser($this->id);
        $this->role = $userRoles[array_key_first($userRoles)]->name ?? null;
        return parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        if (!$roles) {
            $this->createRole();
        }
        else {
            $this->updateRole();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function createRole()
    {
        $auth = Yii::$app->authManager;
        $roleName = $this->role ?? 'manager';
        $role = $auth->getRole($roleName);
        $auth->assign($role, $this->id);
    }

    public function updateRole()
    {
        $auth = Yii::$app->authManager;
        $roleName = $this->role ?? 'manager';
        $auth->revokeAll($this->id);
        $role = $auth->getRole($roleName);
        $auth->assign($role, $this->id);
    }

    public function getRoleName()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        if ($roles) {
            foreach ($roles as $role) {
                return $role->description;
            }
        }
        return false;
    }

    public function getClinicName()
    {
        $clinics = Api::getClinicsList();
        if ($this->clinic_id and isset($clinics[$this->clinic_id])) {
            return $clinics[$this->clinic_id];
        }
        return false;
    }

    public function getDefaultTabletName()
    {
        $tablet = Tablet::findOne($this->default_tablet_id);
        return $tablet ? $tablet->name : '';
    }

    public static function isAdmin()
    {
        return Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
    }

    public static function isManager()
    {
        return Yii::$app->authManager->getAssignment('manager', Yii::$app->user->id);
    }

    public static function isTablet()
    {
        return Yii::$app->authManager->getAssignment('tablet', Yii::$app->user->id);
    }

    public static function getRoleList()
    {
        $roleList = [];
        if($roles = \Yii::$app->authManager->getRoles()) {
            foreach($roles as $roleValues) {
                $roleList[$roleValues->name] = $roleValues->description;
            }
        }
        if(!isset($roleList['tablet'])) {
            $auth = Yii::$app->authManager;
            $tablet = $auth->createRole('tablet');
            $tablet->description = 'Планшет';
            $auth->add($tablet);
            return self::getRoleList();
        }
        return $roleList;
    }

    public static function getTemplateLink()
    {
        if($user = self::findIdentity(Yii::$app->user->identity->id)) {
            return '/tablet/' . $user->default_tablet_id;
        }

        return '/tablet/';
    }
}
