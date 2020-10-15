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
        // 受け取り完了フラグの検索
        $received = $this->Buyerinfo->find()->select(['received'])->where(['biditem_id' => $id])->first();

        // ログインしているユーザーを変数に挿入
        $loginUserId = $this->Auth->user('id');
        // 出品者と落札者のuser_idを検索
        $seller = $this->Biditems->find()->select(['user_id'])->where(['id' => $id])->first();
        $buyer = $this->Bidinfo->find()->select(['user_id'])->where(['biditem_id' => $id])->first();

        //受け取り完了していない時
        if (empty($received) || ($received['received'] === false)) {
            $this->Flash->success(__('権限がありません'));
            // トップページ（index）に移動
            return $this->redirect(['controller' => 'auction', 'action' => 'index']);
        }
        // ログインユーザーが出品者・落札者であるか
        switch (true) {
                // 出品者
            case ($loginUserId === $seller['user_id']):
                $appraiseeId = $buyer['user_id'];
                break;
                // 落札者
            case ($loginUserId === $buyer['user_id']):
                $appraiseeId = $seller['user_id'];
                break;
                // 出品者でも落札者でもない
            default:
                $this->Flash->success(__('権限がありません'));
                // トップページ（index）に移動
                return $this->redirect(['action' => 'index']);
                break;
        }

        $rating = $this->Ratings->newEntity();
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
        $this->set(compact('rating'));
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
}
