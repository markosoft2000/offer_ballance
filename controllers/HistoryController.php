<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 17.03.16
 * Time: 14:13
 */

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;


class HistoryController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\BalanceHistory';

    /**
     * Get balance history for user or all users with date filter
     * @return array|\yii\db\ActiveRecord[]
     * @throws ForbiddenHttpException
     */
    public function actionBalanceHistory()
    {
        $access = $this->checkAccess('BalaceHistory', $this->modelClass);
        $uid = (!empty($_GET['uid']) && $_GET['uid'] > 0) ? (int) $_GET['uid'] : 0;
        $dateStart= (!empty($_GET['dateStart']) && $_GET['dateStart'] != '') ? date('Y-m-d 00:00:00',strtotime($_GET['dateStart'])) : '01.01.1970';
        $dateEnd= (!empty($_GET['dateEnd']) && $_GET['dateEnd'] != '') ? date('Y-m-d 23:59:59',strtotime($_GET['dateEnd'])) : date('d.m.Y');

        if ($uid != $access['userId'] && !$access['admin']) {
            throw(new ForbiddenHttpException('User not found'));
        }

        $model = new $this->modelClass();
        $subQuery = $model->find();

        if ($uid)
        {
            $subQuery->andWhere(['=', 'receiver_id', $uid]);
        }

        if ($dateStart)
        {
            $subQuery
                ->andWhere(['>=', 'date', $dateStart])
                ->andWhere(['<=', 'date', $dateEnd]);
        }

        return $subQuery->all();
    }
}