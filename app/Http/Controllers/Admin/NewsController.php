<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\News;

class NewsController extends Controller
{
    public function add() {
        return view('admin.news.create');
    }
    
    public function create(Request $request) {
        
        // Varidationを行う
        $this->validate($request, News::$rules);
    
        $news = new News;
        $form = $request->all();
    
        // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
        if (isset($form['image'])) {
            $path = $request->file('image')->store('public/image');
            $news->image_path = basename($path);
        } else {
            $news->image_path = null;
        }
    
        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        // フォームから送信されてきたimageを削除する
        unset($form['image']);
    
        // データベースに保存する
        $news->fill($form);
        $news->save();
      
        return redirect('admin/news/create');
    }
    
    public function index(Request $request) {
        
      $cond_title = $request->cond_title;
        
      if ($cond_title != '') {
          
          // 検索されたら検索結果を取得する
          // newsテーブルの中のtitleカラムで$cond_title（ユーザーが入力した文字）に一致するレコードを全て取得することができる。
          $posts = News::where('title', $cond_title)->get();
      } else {
          // それ以外はすべてのニュースを取得する
          $posts = News::all();
      }
      return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]);
    }
    
    public function edit() {
        return view('admin.news.edit');
    }
    
    public function update() {
        return redirect('admin/news/edit');
    }
}
