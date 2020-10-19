<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event; // added.
use Exception; // added.

class AuctionController extends AuctionBaseController
{
	// デフォルトテーブルを使わない
	public $useTable = false;

	// 初期化処理
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Paginator');
		// 必要なモデルをすべてロード
		$this->loadModel('Users');
		$this->loadModel('Biditems');
		$this->loadModel('Bidrequests');
		$this->loadModel('Bidinfo');
		$this->loadModel('Bidmessages');
		$this->loadModel('Buyerinfo');
		$this->loadModel('Ratings');
		// ログインしているユーザー情報をauthuserに設定
		$this->set('authuser', $this->Auth->user());
		// レイアウトをauctionに変更
		$this->viewBuilder()->setLayout('auction');
	}

	// トップページ
	public function index()
	{
		// ページネーションでBiditemsを取得
		$auction = $this->paginate('Biditems', [
			'contain' => ['Bidinfo'],
			'order' => ['endtime' => 'desc'],
			'limit' => 10
		]);
		$ratings = $this->Ratings->find()->select('biditem_id')->where(['reviewer_id' => $this->Auth->user('id')])->toArray();
		if (!empty($ratings)) {
			foreach ($ratings as $rate) {
				$endOfTransaction[] = $rate->biditem_id;
			}
		} else {
			$endOfTransaction = array(0);
		}

		$this->set(compact('auction', 'endOfTransaction'));
	}

	// 商品情報の表示
	public function view($id = null)
	{
		// $idのBiditemを取得
		$biditem = $this->Biditems->get($id, [
			'contain' => ['Users', 'Bidinfo', 'Bidinfo.Users']
		]);
		// オークション終了時の処理
		if ($biditem->endtime < new \DateTime('now') and $biditem->finished == 0) {
			// finishedを1に変更して保存
			$biditem->finished = 1;
			$this->Biditems->save($biditem);
			// Bidinfoを作成する
			$bidinfo = $this->Bidinfo->newEntity();
			// Bidinfoのbiditem_idに$idを設定
			$bidinfo->biditem_id = $id;
			// 最高金額のBidrequestを検索
			$bidrequest = $this->Bidrequests->find('all', [
				'conditions' => ['biditem_id' => $id],
				'contain' => ['Users'],
				'order' => ['price' => 'desc']
			])->first();
			// Bidrequestが得られた時の処理
			if (!empty($bidrequest)) {
				// Bidinfoの各種プロパティを設定して保存する
				$bidinfo->user_id = $bidrequest->user->id;
				$bidinfo->user = $bidrequest->user;
				$bidinfo->price = $bidrequest->price;
				$this->Bidinfo->save($bidinfo);
			}
			// Biditemのbidinfoに$bidinfoを設定
			$biditem->bidinfo = $bidinfo;
		}
		// Bidrequestsからbiditem_idが$idのものを取得
		$bidrequests = $this->Bidrequests->find('all', [
			'conditions' => ['biditem_id' => $id],
			'contain' => ['Users'],
			'order' => ['price' => 'desc']
		])->toArray();
		// オブジェクト類をテンプレート用に設定
		$this->set(compact('biditem', 'bidrequests'));
	}

	// 出品する処理
	public function add()
	{
		// Biditemインスタンスを用意
		$biditem = $this->Biditems->newEntity();
		// POST送信時の処理
		if ($this->request->is('post')) {
			// $biditemにフォームの送信内容を反映
			$biditem = $this->Biditems->patchEntity($biditem, $this->request->getData());
			// $biditemを保存する
			if ($this->Biditems->save($biditem)) {
				// 成功時のメッセージ
				$this->Flash->success(__('保存しました。'));
				// トップページ（index）に移動
				return $this->redirect(['action' => 'index']);
			}
			// 失敗時のメッセージ
			$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
		}
		// 値を保管
		$this->set(compact('biditem'));
	}

	// 入札の処理
	public function bid($biditem_id = null)
	{
		// 入札用のBidrequestインスタンスを用意
		$bidrequest = $this->Bidrequests->newEntity();
		// $bidrequestにbiditem_idとuser_idを設定
		$bidrequest->biditem_id = $biditem_id;
		$bidrequest->user_id = $this->Auth->user('id');
		// POST送信時の処理
		if ($this->request->is('post')) {
			// $bidrequestに送信フォームの内容を反映する
			$bidrequest = $this->Bidrequests->patchEntity($bidrequest, $this->request->getData());
			// Bidrequestを保存
			if ($this->Bidrequests->save($bidrequest)) {
				// 成功時のメッセージ
				$this->Flash->success(__('入札を送信しました。'));
				// トップページにリダイレクト
				return $this->redirect(['action' => 'view', $biditem_id]);
			}
			// 失敗時のメッセージ
			$this->Flash->error(__('入札に失敗しました。もう一度入力下さい。'));
		}
		// $biditem_idの$biditemを取得する
		$biditem = $this->Biditems->get($biditem_id);
		$this->set(compact('bidrequest', 'biditem'));
	}

	// 落札者とのメッセージ
	public function msg($bidinfo_id = null)
	{
		// Bidmessageを新たに用意
		$bidmsg = $this->Bidmessages->newEntity();
		// POST送信時の処理
		if ($this->request->is('post')) {
			// 送信されたフォームで$bidmsgを更新
			$bidmsg = $this->Bidmessages->patchEntity($bidmsg, $this->request->getData());
			// Bidmessageを保存
			if ($this->Bidmessages->save($bidmsg)) {
				$this->Flash->success(__('保存しました。'));
			} else {
				$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
			}
		}
		try { // $bidinfo_idからBidinfoを取得する
			$bidinfo = $this->Bidinfo->get($bidinfo_id, ['contain' => ['Biditems']]);
		} catch (Exception $e) {
			$bidinfo = null;
		}
		// Bidmessageをbidinfo_idとuser_idで検索
		$bidmsgs = $this->Bidmessages->find('all', [
			'conditions' => ['bidinfo_id' => $bidinfo_id],
			'contain' => ['Users'],
			'order' => ['created' => 'desc']
		]);
		$this->set(compact('bidmsgs', 'bidinfo', 'bidmsg'));
	}

	// 落札情報の表示
	public function home()
	{
		// 自分が落札したBidinfoをページネーションで取得
		$bidinfo = $this->paginate('Bidinfo', [
			'conditions' => ['Bidinfo.user_id' => $this->Auth->user('id')],
			'contain' => ['Users', 'Biditems'],
			'order' => ['created' => 'desc'],
			'limit' => 10
		])->toArray();
		$ratings = $this->Ratings->find()->select('biditem_id')->where(['reviewer_id' => $this->Auth->user('id')])->toArray();
		if (!empty($ratings)) {
			foreach ($ratings as $rate) {
				$endOfTransaction[] = $rate->biditem_id;
			}
		} else {
			$endOfTransaction = array(0);
		}
		$this->set(compact('bidinfo', 'endOfTransaction'));
	}

	// 出品情報の表示
	public function home2()
	{
		// 自分が出品したBiditemをページネーションで取得
		$biditems = $this->paginate('Biditems', [
			'conditions' => ['Biditems.user_id' => $this->Auth->user('id')],
			'contain' => ['Users', 'Bidinfo'],
			'order' => ['created' => 'desc'],
			'limit' => 10
		])->toArray();
		$ratings = $this->Ratings->find()->select('biditem_id')->where(['reviewer_id' => $this->Auth->user('id')])->toArray();
		if (!empty($ratings)) {
			foreach ($ratings as $rate) {
				$endOfTransaction[] = $rate->biditem_id;
			}
		} else {
			$endOfTransaction = array(0);
		}
		$this->set(compact('biditems', 'endOfTransaction'));
	}

	// 取引終了後のページ
	public function interact($id = null)
	{
		// bidinfoテーブルに商品idのデータが入っているか検索
		$bidinfo = $this->Bidinfo->find('all')->where(['biditem_id' => $id])->first();

		// ログインしているユーザーを変数に挿入
		$loginUserId = $this->Auth->user('id');
		// 出品者と落札者のuser_idを検索
		$sellerId = $this->Biditems->find()->select(['user_id'])->where(['id' => $id])->first()->user_id;
		$buyerId = $this->Bidinfo->find()->select(['user_id'])->where(['biditem_id' => $id])->first()->user_id;

		// bidinfoテーブルに商品idのデータが入っていない時
		if (is_null($bidinfo)) {
			$this->Flash->success(__('権限がありません'));
			// トップページ（index）に移動
			return $this->redirect(['action' => 'index']);
		}
		// ログインユーザーが出品者・落札者であるか
		switch (true) {
				// 出品者
			case ($loginUserId === $sellerId):
				$user = 'seller';
				$this->set('user', $user);
				break;
				// 落札者
			case ($loginUserId === $buyerId):
				$user = 'buyer';
				$this->set('user', $user);
				break;
				// 出品者でも落札者でもない
			default:
				$this->Flash->success(__('権限がありません'));
				// トップページ（index）に移動
				return $this->redirect(['action' => 'index']);
				break;
		}

		// buyerinfoテーブルに商品idのデータが入っているか検索
		$formed = $this->Buyerinfo->find('all')->where(['biditem_id' => $id])->first();
		// 発送完了フラグを検索
		$shipped = $this->Biditems->find()->select('shipped')->where(['id' => $id])->first()->shipped;

		// buyerinfoテーブルに値が入っていないとき
		if (is_null($formed)) {
			$status = 'form';
		} elseif ($shipped === false) {
			$this->set('form', $formed);
			$status = 'ship';
		} elseif ($formed->received === false) {
			// 受け取り未完了の時
			$status = 'receive';
		} else {
			return $this->redirect(['controller' => 'ratings', 'action' => 'add', $id]);
		}
		$this->set('status', $status);

		$entity = $this->Buyerinfo->newEntity();
		$this->set('entity', $entity);
		$this->set('id', $id);
	}

	// 落札者の発送情報フォーム
	public function form($id = null)
	{
		// ログインしているユーザーを変数に挿入
		$loginUserId = $this->Auth->user('id');
		// 落札者のuser_idを検索
		$buyerId = $this->Bidinfo->find()->select(['user_id'])->where(['biditem_id' => $id])->first()->user_id;

		// buyerinfoテーブルに商品idのデータが入っているか検索
		$formed = $this->Buyerinfo->find('all')->where(['biditem_id' => $id])->first();

		if (!empty($formed) || $buyerId !== $loginUserId) {
			$this->Flash->error(__('権限がありません'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->isPost()) {
			$form = $this->request->data['Form'];

			$form['user_id'] = $this->Auth->user('id');
			$form['biditem_id'] = $id;

			// フォームの内容をDBに挿入
			$entity = $this->Buyerinfo->newEntity($form);
			if ($this->Buyerinfo->save($entity)) {
				$this->Flash->success(__('保存しました。'));
				// /auction/interact/商品id にリダイレクト
				return $this->redirect(['action' => 'interact', $id]);
			}
			$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
			$this->setAction('interact', $id);
		}
		$this->set(compact('entity'));
	}

	public function ship($id = null)
	{
		$loginUserId = $this->Auth->user('id');
		$sellerId = $this->Biditems->find()->select(['user_id'])->where(['id' => $id])->first()->user_id;

		// 発送情報フォームが送られたか検索
		$formed = $this->Buyerinfo->find('all')->where(['biditem_id' => $id])->first();
		// 発送完了ボタンが押されたか検索
		$biditem = $this->Biditems->get($id);
		$shipped = $biditem->shipped;

		// ・発送情報のデータがない・ユーザーが出品者でない・発送完了済み
		if (empty($formed) || $sellerId !== $loginUserId || $shipped === true) {
			$this->Flash->error(__('権限がありません'));
			return $this->redirect(['action' => 'index']);
		}

		$biditem->shipped = 1;
		if ($this->Biditems->save($biditem)) {
			return $this->redirect(['action' => 'interact', $id]);
		}
	}
	public function receive($id = null)
	{
		// ログインしているユーザーを変数に挿入
		$loginUserId = $this->Auth->user('id');
		// 落札者のuser_idを検索
		$buyerId = $this->Bidinfo->find()->select(['user_id'])->where(['biditem_id' => $id])->first()->user_id;

		// 発送完了ボタンが押されたか検索
		$biditem = $this->Biditems->get($id);
		$shipped = $biditem->shipped;

		// 受取完了ボタンが押されたか検索
		$buyerinfo = $this->Buyerinfo->get($id);
		$received = $buyerinfo->received;

		// ・発送未完了・落札者でない・受取完了済み
		if ($shipped === false || $loginUserId !== $buyerId || $received === true) {
			$this->Flash->error(__('権限がありません'));
			return $this->redirect(['action' => 'index']);
		}

		$buyerinfo->received = 1;
		if ($this->Buyerinfo->save($buyerinfo)) {
			return $this->redirect(['action' => 'interact', $id]);
		}
	}
}
