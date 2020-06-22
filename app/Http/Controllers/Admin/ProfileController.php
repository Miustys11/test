<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Profile;

class ProfileController extends Controller
{
    public function add() {
        return view('admin.profile.create');
    }
    
    // Requestクラスは、ブラウザを通してユーザーから送られる情報をすべて含んでいるオブジェクトを取得することができる
    // 会員登録のフォームなら、画面でユーザーが入力したＥメール、パスワード、名前、住所だけでなく、何のブラウザを使用したか（User Agent）、どのIPから送られたか、
    // どのURLからアクセスしたかなど、また、会員ログイン後の画面なら、会員認証において保存されたクッキーもブラウザを介して、リクエストに含まれる。
    // これらの情報を$requestに代入して使用することができる。
    public function create(Request $request) {
        
        // Varidationを行う
        $this->validate($request, Profile::$rules);
    
        $profile = new Profile;
        $form = $request->all();
    
        // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
        // $pathには「public/image/ハッシュ化されたファイル名」が入っている
        // ファイル名だけ取得するメソッド、basenameを使用
        if (isset($form['image'])) {
            $path = $request->file('image')->store('public/image');
            $profile->image_path = basename($path);
        } else {
            $profile->image_path = null;
        }
    
        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        // フォームから送信されてきたimageを削除する
        unset($form['image']);
    
        // データベースに保存する
        $profile->fill($form);
        $profile->save();
        
        return redirect('admin/profile/create');
    }
    
    public function index(Request $request) {
        
        $cond_title = $request->cond_title;
        
        if ($cond_title != '') {
          
            // 検索されたら検索結果を取得する
            // newsテーブルの中のtitleカラムで$cond_title（ユーザーが入力した文字）に一致するレコードを全て取得することができる。
            $posts = Profile::where('name', $cond_title)->get();
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = Profile::all();
        }
            return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
        }
    
    public function edit(Request $request) {
        
        // Profile Modelからデータを取得する
        $profile = Profile::find($request->id);
        if (empty($profile)) {
          abort(404);    
        }
        return view('admin.profile.edit', ['profile_form' => $profile]);
    }
    
    public function update(Request $request) {
        
        // Validationをかける
        $this->validate($request, Profile::$rules);
          
          // Profile Modelからデータを取得する
          $profile = Profile::find($request->id);
          
          // 送信されてきたフォームデータを格納する
          $profile_form = $request->all();
          if (isset($profile_form['image'])) {
            $path = $request->file('image')->store('public/image');
            $profile->image_path = basename($path);
            unset($profile_form['image']);
          } elseif (isset($request->remove)) {
            $profile->image_path = null;
            unset($profile_form['remove']);
          }
          unset($profile_form['_token']);
          
          // 該当するデータを上書きして保存する
          $profile->fill($profile_form)->save();

          return redirect('admin/profile');
    }
    
    public function delete(Request $request) {
      
      // 該当するNews Modelを取得
      $profile = Profile::find($request->id);
      
      // 削除する
      $profile->delete();
      
      return redirect('admin/profile/');
    } 
}
