<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// 以下を追記することでNews Modelが扱えるようになる
use App\Profiles;
use App\ProfileHistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }
    
    public function create(Request $request)
    {
        // 以下を追記
        // Varidationを行う
        $this->validate($request, Profiles::$rules);
    
        $profiles = new Profiles;
        $form = $request->all();
    
        /*
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
        */
    
        // データベースに保存する
        $profiles->fill($form);
        $profiles->save();
    
        return redirect('admin/profile/create');
    }
    
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
            // 検索されたら検索結果を取得する
            $posts = Profiles::where('title', $cond_title)->get();
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = Profiles::all();
        }
        return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
    }
    
    public function edit(Request $request)
    {
        // News Modelからデータを取得する
        $profiles = Profiles::find($request->id);
        if (empty($profiles)) {
            abort(404);
        }
        return view('admin.profile.edit', ['profiles_form' => $profiles]);
    }
    
    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profiles::$rules);
        // News Modelからデータを取得する
        $profiles = Profiles::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profiles_form = $request->all();
        unset($profiles_form['_token']);

        // 該当するデータを上書きして保存する
        $profiles->fill($profiles_form)->save();

        $profilehistory = new ProfileHistory;
        $profilehistory->profiles_id = $profiles->id;
        $profilehistory->edited_at = Carbon::now();
        $profilehistory->save();
        
        return redirect('admin/profile/');
    }
    
    public function delete(Request $request)
    {
        // 該当するNews Modelを取得
        $profiles = Profiles::find($request->id);
        // 削除する
        $profiles->delete();
        return redirect('admin/profile/');
    }
}
