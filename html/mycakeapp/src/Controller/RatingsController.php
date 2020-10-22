<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Ratings Controller
 *
 * @property \App\Model\Table\RatingsTable $Ratings
 *
 * @method \App\Model\Entity\Rating[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RatingsController extends AuctionBaseController
{
    // 初期化処理
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        // 必要なモデルをすべてロード
        $this->loadModel('Users');
        $this->loadModel('Biditems');
        $this->loadModel('Bidinfo');
        $this->loadModel('Buyerinfo');
        // ログインしているユーザー情報をauthuserに設定
        $this->set('authuser', $this->Auth->user());
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Biditems', 'Users'],
        ];
        $ratings = $this->paginate($this->Ratings);

        $this->set(compact('ratings'));
    }

    /**
     * View method
     *
     * @param string|null $id Rating id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $rating = $this->Ratings->get($id, [
            'contain' => ['Biditems', 'Users'],
        ]);

        $this->set('rating', $rating);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        // 受け取り完了しているかどうか
        // 受け取り完了フラグを検索
        $received = $this->Buyerinfo->find()->select(['received'])->where(['biditem_id' => $id])->first();
        //受け取り完了していない時/auction/indexにリダイレクト
        if (empty($received) || ($received['received'] === false)) {
            $this->Flash->success(__('権限がありません'));
            // トップページ（index）に移動
            return $this->redirect(['controller' => 'auction', 'action' => 'index']);
        }
        // ログインユーザーが出品者・落札者であるか
        // ログインしているユーザーを変数に挿入
        $loginUserId = $this->Auth->user('id');
        // 出品者と落札者のuser_idを検索
        $seller = $this->Biditems->find()->select(['user_id'])->where(['id' => $id])->first();
        $buyer = $this->Bidinfo->find()->select(['user_id'])->where(['biditem_id' => $id])->first();
        switch (true) {
            case ($loginUserId === $seller['user_id']): // 出品者
                $appraiseeId = $buyer['user_id'];
                break;
            case ($loginUserId === $buyer['user_id']): // 落札者
                $appraiseeId = $seller['user_id'];
                break;
            default: // 出品者でも落札者でもない
                $this->Flash->success(__('権限がありません'));
                // トップページ（index）に移動
                return $this->redirect(['controller' => 'auction', 'action' => 'index']);
                break;
        }
        // 前に評価したことがあるか
        $rated = $this->Ratings->find()->where(['biditem_id' => $id, 'reviewer_id' => $loginUserId])->first();
        if (!empty($rated)) {
            $this->Flash->success(__('すでに評価しています'));
            // トップページ（index）に移動
            return $this->redirect(['controller' => 'auction', 'action' => 'index']);
        }
        // インスタンスを用意
        $rating = $this->Ratings->newEntity();
        // 取引相手の名前を変数に挿入
        $appraiseeInfo = $this->Users->get($appraiseeId);
        $appraiseeName = $appraiseeInfo['username'];
        // フォームが送信されたとき
        if ($this->request->is('post')) {
            $rating = $this->Ratings->patchEntity($rating, $this->request->getData());

            $rating->biditem_id = $id;
            $rating->reviewer_id = $loginUserId;
            $rating->appraisee_id = $appraiseeId;

            if ($this->Ratings->save($rating)) {
                $this->Flash->success(__('取引は終了です。ありがとうございました。'));
                return $this->redirect(['controller' => 'auction', 'action' => 'index']);
            }
            $this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
        }
        $this->set(compact('rating', 'appraiseeName'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rating id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rating = $this->Ratings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rating = $this->Ratings->patchEntity($rating, $this->request->getData());
            if ($this->Ratings->save($rating)) {
                $this->Flash->success(__('The rating has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rating could not be saved. Please, try again.'));
        }
        $biditems = $this->Ratings->Biditems->find('list', ['limit' => 200]);
        $appraisees = $this->Ratings->Appraisees->find('list', ['limit' => 200]);
        $reviewers = $this->Ratings->Reviewers->find('list', ['limit' => 200]);
        $this->set(compact('rating', 'biditems', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rating id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $rating = $this->Ratings->get($id);
        if ($this->Ratings->delete($rating)) {
            $this->Flash->success(__('The rating has been deleted.'));
        } else {
            $this->Flash->error(__('The rating could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // 各ユーザーの評価ページ
    public function userrating($id = null)
    {
        // 該当ユーザーの名前
        $userName = $this->Users->find()->select('username')->where(['id' => $id])->first()->username;
        if (empty($userName)) {
            $this->Flash->success(__('ユーザーが存在していません'));
            // トップページ（index）に移動
            return $this->redirect(['controller' => 'auction', 'action' => 'index']);
        }
        // ユーザーの評価コメント
        $ratingComments = $this->Ratings->find()->select(['rating_comment'])->where(['appraisee_id' => $id]);
        // ユーザーの平均評価
        $avgRating = $this->Ratings->find()->where(['appraisee_id' => $id])->avg('rating_scale');
        // 一人も評価されていない時
        if (empty($avgRating)) {
            $avgRating = '-';
        } else {
            // 小数第2位を切り捨て
            $avgRating = (floor($avgRating * 10) / 10);
        }
        $this->set(compact('userName', 'avgRating', 'ratingComments'));
    }
}
